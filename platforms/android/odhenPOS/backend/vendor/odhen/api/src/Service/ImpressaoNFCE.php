<?php

namespace Odhen\API\Service;

use Odhen\API\Remote\Printer\Command;
use Odhen\API\Util\Exception;

class ImpressaoNFCE {

	protected $entityManager;
	protected $impressaoUtil;
	protected $util;
	protected $instanceManager;
	protected $painelSenha;

	public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Lib\ImpressaoUtil $impressaoUtil,
		\Odhen\API\Util\Util $util,
		\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager,
		\Odhen\API\Service\PainelSenha $painelSenha
	) {
        $this->entityManager = $entityManager;
        $this->impressaoUtil = $impressaoUtil;
		$this->util 	     = $util;
		$this->instanceManager = $instanceManager;
		$this->painelSenha   = $painelSenha;
	}

    private function fillNfceInfo($nfceData, &$nfceInfo){
    	$nfceInfo['NRNOTAFISCALCE'] = $nfceData['NRNOTAFISCALCE'];
    	$nfceInfo['CDSERIENFCE'] = $nfceData['CDSERIENFCE'];
    	$nfceInfo['DTEMISSAONFCE'] = $nfceData['DTEMISSAONFCE'];
    	$nfceInfo['NRACESSONFCE'] = $nfceData['NRACESSONFCE'];
    	$nfceInfo['CDSENHAPED'] = $nfceData['CDSENHAPED'];
    	$nfceInfo['DSQRCODENFCE'] = $nfceData['DSQRCODENFCE'];
    	$nfceInfo['WSLINKQRCODENFC'] = $nfceData['WSLINKQRCODENFC'];
    	$nfceInfo['NRPROTOCOLONFCE'] = $nfceData['NRPROTOCOLONFCE'];
    	$nfceInfo['IDIMPLOGONF'] = $nfceData['IDIMPLOGONF'];
    	$nfceInfo['IDIMPTXSERV'] = $nfceData['IDIMPTXSERV'];
    	$nfceInfo['IDIMPCNPJCLIE'] = $nfceData['IDIMPCNPJCLIE'];
    	$nfceInfo['DSMENSCPFCUP'] = $nfceData['DSMENSCPFCUP'];
    	$nfceInfo['DSOBSCUPFIS'] = $nfceData['DSOBSCUPFIS'];
    	$nfceInfo['nmloja'] = $nfceData['NMLOJA'];
    	$nfceInfo['nmcaixa'] = $nfceData['NMCAIXA'];
    	$nfceInfo['IDUMAVIANFCE'] = $nfceData['IDUMAVIANFCE'];
    }

    public function imprimeDanfeNFCE($nfceInfo, $filialInfo, $productsNFCE, $infoConsumer, $impContingencia, $FIDELITYVALUE){

        // PRODUCT AGRUPATOR
        $produtosAgrupados = array();
        $NRSEQUITVEND = 1;
        foreach ($productsNFCE as $produto){
            $key = $produto['CDPRODUTO'].$produto['VRUNITVEND'].$produto['VRTOTITEM'].$produto['VRTOTACRE'].$produto['VRTOTDESC'];
            if ($produto['IDPESAPROD'] !== 'S' && array_key_exists($key, $produtosAgrupados)){
                $produtosAgrupados[$key]['QTPRODVEND'] = floatval($produtosAgrupados[$key]['QTPRODVEND']) + floatval($produto['QTPRODVEND']);
                $produtosAgrupados[$key]['VRTOTITEM'] = floatval($produtosAgrupados[$key]['VRTOTITEM']) + floatval($produto['VRTOTITEM']);
            }
            else {
                if ($produto['IDPESAPROD'] === 'S') $key .= $NRSEQUITVEND;
                $produtosAgrupados[$key] = $produto;
                $produtosAgrupados[$key]['NRSEQUITVEND'] = str_pad((string)$NRSEQUITVEND++, 3, '0', STR_PAD_LEFT);
            }
        }
        $productsNFCE = $produtosAgrupados;

		$nfceData = self::getNFCEDataToPrint($nfceInfo['nrAcessoNFCE'], $filialInfo);
        if (!$nfceData['error']) {
			self::fillNfceInfo($nfceData['nfceData'], $nfceInfo);
            $paravendData = self::getParavendData($filialInfo['CDFILIAL']);
            if (!$paravendData['error']) {
                $dadosImpressora = $this->impressaoUtil->getDadosImpressora($filialInfo['CDFILIAL'], $filialInfo['CDCAIXA']);
                if (!$dadosImpressora['error']) {
					$params = array(
						'CDFILIAL' => $filialInfo['CDFILIAL'],
						'CDCAIXA' => $filialInfo['CDCAIXA'],
						'NRORG' => $filialInfo['NRORG']
					);
					$dadosCaixa = $this->entityManager->getConnection()->fetchAll("BUSCA_DADOS_CAIXA", $params);
					$IDHABCAIXAVENDA = $dadosCaixa[0]['IDHABCAIXAVENDA'];
                	$dadosImpressora = $dadosImpressora['dadosImpressora'];
                	$retornoMontaString = $this->montaStringCupom($nfceData['nfceData'], $filialInfo, $nfceInfo, $dadosImpressora, $productsNFCE, $infoConsumer, $FIDELITYVALUE);
                    $result = array(
	                    'error' => false,
	                    'dadosImpressao' => array(),
	                    'errPainelSenha' => $retornoMontaString['ERRMSGPAINELSENHA']
	                );
	                $printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
                    if ($printerParams['impressaoFront']) {
                        $result['dadosImpressao'] = array(
                        	'TEXTOPAINELSENHA' => !empty($retornoMontaString['TEXTOPAINELSENHA']) ? $retornoMontaString['TEXTOPAINELSENHA'] : array(),
                            'TEXTOCUPOM1VIA' => $retornoMontaString['TEXTOCUPOM1VIA'],
                            'TEXTOCUPOM2VIA' => $retornoMontaString['TEXTOCUPOM2VIA'],
                            'TEXTOCODIGOBARRAS' => '',
							'TEXTOQRCODE' => $retornoMontaString['TEXTOQRCODE'],
							'TEXTORODAPE' => $retornoMontaString['TEXTORODAPE'] . $retornoMontaString['TEXTOSENHAMESA'] . $retornoMontaString['TEXTOPRODCAN']
                        );

                    } else {
						$comandos = new Command();

						$comandos->text($retornoMontaString['TEXTOCUPOM1VIA']);
						$comandos->qrCode($retornoMontaString['TEXTOQRCODE'], array(
							'height' => 60,
							'width' => 2,
							'position' => 2,
							'font' => 0,
							'margin' => 174
						));
						$comandos->text($retornoMontaString['TEXTORODAPE']);
						if (!empty($retornoMontaString['TEXTOSENHAMESA'])){
							$printerParams['letterType'] = $printerParams['tipoLetra'];
							$printerParams['bold'] = true;
                            $printerParams['italic'] = true;
							$comandos->text($retornoMontaString['TEXTOSENHAMESA'], $printerParams);
						}
						if (!empty($retornoMontaString['TEXTOPRODCAN'])){
							$comandos->text($retornoMontaString['TEXTOPRODCAN']);
						}

						if (!empty($retornoMontaString['TEXTOPAINELSENHA'])) {
							$comandos->text($retornoMontaString['TEXTOPAINELSENHA']['inicio']);
							$comandos->qrCode($retornoMontaString['TEXTOPAINELSENHA']['qrCode'], array(
								'height' => 30,
								'width' => 1,
								'position' => 2,
								'font' => 0,
								'margin' => 174
							));
							$comandos->text($retornoMontaString['TEXTOPAINELSENHA']['final']);
						}

						if($dadosImpressora['IDMODEIMPRES'] != '23' && $IDHABCAIXAVENDA != 'TAA' && $IDHABCAIXAVENDA != 'APC')  {
							$comandos->cutPaper();
						}

						if (!empty($retornoMontaString['TEXTOCUPOM2VIA']) && $impContingencia) {
							$comandos->text($retornoMontaString['TEXTOCUPOM2VIA']);
							$comandos->qrCode($retornoMontaString['TEXTOQRCODE'], array(
								'height' => 60,
								'width' => 2,
								'position' => 2,
								'font' => 0,
								'margin' => 174
							));
							$comandos->text($retornoMontaString['TEXTORODAPE']);
							if (!empty($retornoMontaString['TEXTOSENHAMESA'])){
								$printerParams['letterType'] = $printerParams['tipoLetra'];
								$comandos->text($retornoMontaString['TEXTOSENHAMESA'], $printerParams);
							}
							if (!empty($retornoMontaString['TEXTOPRODCAN'])){
								$comandos->text($retornoMontaString['TEXTOPRODCAN']);
							}
							if($dadosImpressora['IDMODEIMPRES'] != '23' && $IDHABCAIXAVENDA != 'TAA' && $IDHABCAIXAVENDA != 'APC') {
								$comandos->cutPaper();
							}
						}
						$issaas = $this->util->isSaas();
						if($issaas){
							$result['paramsImpressora'] = array(
								'saas'      => true,
								'impressora'=> $dadosImpressora,
								'comandos'  => $comandos->getCommands(),
								'error'     => false);
							$respostaPonte['error'] = false;
						}else{
							$respostaPonte = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
						}
                		if ($respostaPonte['error']) {
                			$result['error'] = true;
                			$result['message'] = $respostaPonte['message'];
                		}
                    }
                    return $result;
				} else {
					$result = $dadosImpressora;
				}
			} else {
				$result = $paravendData;
			}
		} else {
			$result = $nfceData;
		}
		return $result;
	}

	private function montaStringCupom($nfceData, $filialInfo, $nfceInfo, $dadosImpressora, $productsNFCE, $infoConsumer, $FIDELITYVALUE){
		$printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
		$largura = $printerParams['largura'];
		$printerParams['largura'] = $printerParams['larguraCupom'];


		// Realiza a montagem dos dados na nota fiscal da API de painel de senhas do Madero.
		$dadosPainelSenha = $this->painelSenha->buscaDados($filialInfo['CDFILIAL'], $filialInfo['CDLOJA'], $nfceInfo['CDSENHAPED'], $nfceInfo['nrseqvenda']);
		$rodapePainelSenha = array();
		if (!$dadosPainelSenha['error']) {
			$rodapePainelSenha = $this->montaRodapePainelSenha($printerParams, $dadosPainelSenha, $nfceInfo['CDSENHAPED']);
		}

		$texto = '';
		$texto = $this->montaCabecalhoCupom($printerParams, $filialInfo);
		$texto = $this->montaTituloCupom($texto, $printerParams);
		$texto = $this->montaInfoStatusCupom($texto, $nfceInfo, $printerParams, false);
		$texto = $this->montaInfoProdutosCupom($texto, $printerParams, $productsNFCE, $nfceInfo);
		$texto = $this->montaInfoPagamentoCupom($texto, $filialInfo, $nfceInfo, $printerParams, count($productsNFCE));
		$texto = $this->montaInfoAcessoNota($texto, $nfceInfo, $printerParams);
		$texto = $this->montaInfoConsumidorCupom($texto, $infoConsumer, $printerParams, $nfceData);
		$arrTexto = $this->montaInfoDadosCupom($texto, $nfceInfo, $printerParams);

		$textoRodape = '';
		$textoRodape = $this->montaInfoTributosCupom($textoRodape, $nfceInfo, $printerParams);
        $textoRodape = $this->montaRodapeCupom($textoRodape, $nfceInfo, $filialInfo, $printerParams, $FIDELITYVALUE, $infoConsumer);
        $textoSenhaMesa = $this->montaTextoSenhaMesa($nfceInfo, $printerParams, $largura, $filialInfo, $dadosImpressora, $rodapePainelSenha);
		$textoProdutosCancelados = $this->montaProdutosCancelados($nfceInfo, $filialInfo, $printerParams);

		$retorno = array(
			'TEXTOPAINELSENHA' => $rodapePainelSenha,
			'TEXTOCUPOM1VIA' => $arrTexto['VIACONSUMIDOR'],
			'TEXTOCUPOM2VIA' => $arrTexto['VIAESTABELECIMENTO'],
			'TEXTOQRCODE' => $nfceInfo['DSQRCODENFCE'],
			'TEXTORODAPE' => $textoRodape,
			'TEXTOSENHAMESA' => $textoSenhaMesa,
			'TEXTOPRODCAN' => $textoProdutosCancelados,
			'ERRMSGPAINELSENHA' => !empty($dadosPainelSenha['errmsg']) ? $dadosPainelSenha['errmsg'] : ''
		);

		return $retorno;
	}

	private function montaRodapePainelSenha($printerParams, $dadosPainelSenha, $CDSENHAPED) {
		$rodPainelSenhas['inicio'] = $this->impressaoUtil->imprimeLinha($printerParams);
		$rodPainelSenhas['inicio'] .= $this->impressaoUtil->centraliza($printerParams, 'ACOMPANHE SEU PEDIDO PELO PAINEL DE SENHAS!') . $printerParams['comandoEnter'];

		$rodPainelSenhas['qrCode'] = $this->impressaoUtil->centraliza($printerParams, $dadosPainelSenha['URL_QRCODE']) . $printerParams['comandoEnter'];

		$rodPainelSenhas['final'] = $this->impressaoUtil->centraliza($printerParams, 'Leia o QR Code ou acesse pelo endereço abaixo para') . $printerParams['comandoEnter'];
		$rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, 'acompanhar seu pedido no painel de senhas pelo celular.') . $printerParams['comandoEnter'];

		$rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, 'Endereço: ') . $printerParams['comandoEnter'];
		$rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, $dadosPainelSenha['URL']) . $printerParams['comandoEnter'];
		$rodPainelSenhas['final'] .= $this->impressaoUtil->centraliza($printerParams, 'Código de acesso: ' . $dadosPainelSenha['ACCESS_CODE']) . $printerParams['comandoEnter'];

		return $rodPainelSenhas;
    }

	private function montaCabecalhoCupom($printerParams, $filialInfo){
		$texto = '';
		$texto .= trim(substr($filialInfo['NMRAZSOCFILI'], 0, $printerParams['largura']-26));
		$texto .= ' ' . 'CNPJ: ' . $this->util->aplicaMascaraCpfCnpj($filialInfo['NRINSJURFILI']);
		$texto = $this->impressaoUtil->centraliza($printerParams, $texto) . $printerParams['comandoEnter'];
		$endereco = $filialInfo['DSENDEFILI'].', '.$filialInfo['NMBAIRFILI'].', '.$filialInfo['NMMUNICIPIO'].' - '.$filialInfo['SGESTADO'];
		$texto .= $this->impressaoUtil->centraliza($printerParams, $endereco) . $printerParams['comandoEnter'];
		$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
		return $texto;
	}

	private function montaTituloCupom($texto, $printerParams){
		$texto .= $this->impressaoUtil->centraliza($printerParams, 'DOCUMENTO AUXILIAR DA NOTA FISCAL DE CONSUMIDOR ELETRONICA') . $printerParams['comandoEnter'];
		$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
		return $texto;
	}

	private function montaInfoProdutosCupom($texto, $printerParams, $productsNFCE, $nfceInfo) {
		if ($printerParams['largura'] <= 38) {
			return $this->montaInfoProdutosCupomReduzido($texto, $printerParams, $productsNFCE, $nfceInfo);
		} else {
			return $this->montaInfoProdutosCupomNormal($texto, $printerParams, $productsNFCE, $nfceInfo);
		}
	}

    private function montaInfoProdutosCupomNormal($texto, $printerParams, $productsNFCE, $nfceInfo){
        $colunasInfo = 'Item | Codigo | Descricao | Qtde | UN | Vl.Unit | Vl.Tot ';
        $texto .= $this->impressaoUtil->centraliza($printerParams, $colunasInfo). $printerParams['comandoEnter'];
        foreach($productsNFCE as $product) {
            $itemTaxaServico = $this->verificaSeItemTaxaServico($product);
            $itemTaxaEntrega = $this->verificaSeItemTaxaEntrega($product);
            $tituloProduto = $product['NRSEQUITVEND'].' '.$product['CDARVPROD'].' '.substr($product['NMPRODUTO'], 0, 29).' ';
            $quantProduto = $this->impressaoUtil->formataNumero(floatval($product['QTPRODVEND']), 2);
            if (!$itemTaxaServico && !$itemTaxaEntrega){
                $vrProduto = $this->impressaoUtil->formataNumero(floatval($product['VRUNITVEND']) + floatval($product['VRUNITVENDCL']), 2);
                $totalProduto = $this->impressaoUtil->formataNumero(floatval($product['VRTOTITEM']), 2);
            }
            else {
                $vrProduto = $this->impressaoUtil->formataNumero(floatval($product['VRTOTACRE']), 2);
                $totalProduto = $this->impressaoUtil->formataNumero(floatval($product['VRTOTACRE']), 2);
            }
            $infoProduto = $quantProduto . ' ' . $product['SGUNIDADE'] . ' ' . $vrProduto . '  ' . $totalProduto;
            $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $tituloProduto, $infoProduto) . $printerParams['comandoEnter'];
            // if (!$itemTaxaServico && $product['VRTOTACRE'] > 0) {
            //     $vrAcrescimo = $this->impressaoUtil->formataNumero(floatval($product['VRTOTACRE']), 2);
            //     $texto .= $this->impressaoUtil->preencheLinha($printerParams, 'Acrescimo no item ' . $product['NRSEQUITVEND'] . ' - '. $vrAcrescimo, ' ');
            // }
            // if ($product['VRTOTDESC'] > 0){
            //     $vrDesconto =  $this->impressaoUtil->formataNumero(floatval($product['VRTOTDESC']), 2);
            //     $texto .= $this->impressaoUtil->preencheLinha($printerParams, 'Desconto no item ' .  $product['NRSEQUITVEND'] . ' - '. $vrDesconto, ' ');
            // }
        }
        $texto .= $this->impressaoUtil->imprimeLinha($printerParams);

        return $texto;
    }

	private function montaInfoProdutosCupomReduzido($texto, $printerParams, $productsNFCE, $nfceInfo) {
        $colunasInfo = 'Item | Descricao | Codigo | Qtde | UN | Vl.Unit | Vl.Tot ';
        $texto .= $this->impressaoUtil->centraliza($printerParams, $colunasInfo). $printerParams['comandoEnter'];
        foreach($productsNFCE as $product) {
            $itemTaxaServico = $this->verificaSeItemTaxaServico($product);
            $itemTaxaEntrega = $this->verificaSeItemTaxaEntrega($product);
            $texto .= substr($product['NRSEQUITVEND'].' '.$product['NMPRODUTO'], 0, $printerParams['largura']).$printerParams['comandoEnter'];
            $quantProduto = $this->impressaoUtil->formataNumero(floatval($product['QTPRODVEND']), 2);
            if (!$itemTaxaServico && !$itemTaxaEntrega){
                $vrProduto = $this->impressaoUtil->formataNumero(floatval($product['VRUNITVEND']), 2);
                $totalProduto = $this->impressaoUtil->formataNumero(floatval($product['VRTOTITEM']), 2);
            }
            else {
                $vrProduto = $this->impressaoUtil->formataNumero(floatval($product['VRTOTACRE']), 2);
                $totalProduto = $this->impressaoUtil->formataNumero(floatval($product['VRTOTACRE']), 2);
            }
            $infoProduto = $quantProduto . ' ' . $product['SGUNIDADE'] . ' ' . $vrProduto . '  ' . $totalProduto;
            $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $product['CDARVPROD'].' ', $infoProduto) . $printerParams['comandoEnter'];
            // if (!$itemTaxaServico && $product['VRTOTACRE'] > 0) {
            //     $vrAcrescimo = $this->impressaoUtil->formataNumero(floatval($product['VRTOTACRE']), 2);
            //     $texto .= $this->impressaoUtil->preencheLinha($printerParams, 'Acrescimo no item ' . $product['NRSEQUITVEND'] . ' - '. $vrAcrescimo, ' ');
            // }
            // if ($product['VRTOTDESC'] > 0){
            //     $vrDesconto =  $this->impressaoUtil->formataNumero(floatval($product['VRTOTDESC']), 2);
            //     $texto .= $this->impressaoUtil->preencheLinha($printerParams, 'Desconto no item ' .  $product['NRSEQUITVEND'] . ' - '. $vrDesconto, ' ');
            // }
        }
        $texto .= $this->impressaoUtil->imprimeLinha($printerParams);

        return $texto;
	}

    private function verificaSeItemTaxaServico($item) {
        return $item['CDPRODUTO'] === $item['CDPRODTAXASERV'];
    }

    private function verificaSeItemTaxaEntrega($item) {
        return $item['CDPRODUTO'] === $item['CDPRODTAXAENTR'];
    }

	private function montaInfoPagamentoCupom($texto, $filialInfo, $nfceInfo, $printerParams, $quantidadeProdutos) {
        $infoPaymentNFCE = $this->getInfoPayment($filialInfo, $nfceInfo);
        $vrVenda = floatval($infoPaymentNFCE[0]['VRTOTITEM']);
        $totalAcrescimo  = floatval($infoPaymentNFCE[0]['VRTOTACRE']);
        $totalDescontos  = floatval($infoPaymentNFCE[0]['VRTOTDESC']);
        $totalVenda = $vrVenda + round($totalAcrescimo - $totalDescontos, 2);
		$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'QTDE. TOTAL DE ITENS', $quantidadeProdutos) . $printerParams['comandoEnter'];
        $vrVenda = $this->impressaoUtil->formataNumero(floatval($vrVenda), 2);

        $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'VALOR TOTAL R$', $vrVenda) . $printerParams['comandoEnter'];
        if ($totalAcrescimo > 0.00 ){
			$textoAcrescimo = $this->impressaoUtil->formataNumero(floatval($totalAcrescimo), 2);
            $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,'ACRESCIMOS R$',$textoAcrescimo).$printerParams['comandoEnter'];
        }
        if (floatval($totalDescontos)> 0.00 ){
			$textoDesconto = $this->impressaoUtil->formataNumero(floatval($totalDescontos), 2);
            $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,'DESCONTOS R$',$textoDesconto).$printerParams['comandoEnter'];
        }

        $totalVenda = $this->impressaoUtil->formataNumero(floatval($totalVenda), 2);
        $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,'VALOR A PAGAR R$',$totalVenda).$printerParams['comandoEnter'];
        $texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,'FORMA PAGAMENTO','VALOR PAGO R$').$printerParams['comandoEnter'];

        $vrTroco = 0;
        foreach($infoPaymentNFCE as $payment){
        	if ($payment['IDTIPOMOVIVE'] == 'S'){
        		$vrTroco += floatval($payment['VRMOVIVEND']);
        	} else {
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,$payment['NMTIPORECE'],$this->impressaoUtil->formataNumero(floatval($payment['VRMOVIVEND']), 2)).$printerParams['comandoEnter'];
        	}
        }

        if ($vrTroco > 0){
        	$vrTroco = $this->impressaoUtil->formataNumero($vrTroco, 2);
        	$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'TROCO R$', $vrTroco) . $printerParams['comandoEnter'];
        }

        $texto .= $this->impressaoUtil->imprimeLinha($printerParams);

        return $texto;
	}

	private function getInfoPayment($filialInfo, $nfceInfo){
		$paramsToVerifyQuery = array(
            'CDFILIAL'   => $filialInfo['CDFILIAL'],
            'CDCAIXA'    => $filialInfo['CDCAIXA'],
            'NRSEQVENDA' => $nfceInfo['nrseqvenda']
        );

        return $this->entityManager->getConnection()->fetchAll("GET_INFO_PAYMENT_NFCE", $paramsToVerifyQuery);

	}

	private function montaInfoStatusCupom($texto, $nfceInfo, $printerParams, $end){
		if ($nfceInfo['tpAmb'] == '2' && $end){
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL') . $printerParams['comandoEnter'];
		}

		if ($nfceInfo['IDSTATUSNFCE'] != 'A') {
			$texto .= $this->impressaoUtil->centraliza($printerParams,'EMITIDA EM CONTINGENCIA') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams,'Pendente de Autorizacao') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
		}

		return $texto;
	}

	private function montaInfoAcessoNota($texto, $nfceInfo, $printerParams){
		$texto .= $this->impressaoUtil->centraliza($printerParams, 'Consulte pela Chave de Acesso em') . $printerParams['comandoEnter'];
        $texto .= $this->impressaoUtil->centraliza($printerParams, $nfceInfo['WSLINKQRCODENFC']) . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
        $texto .= $this->impressaoUtil->centraliza($printerParams,
            substr($nfceInfo['NRACESSONFCE'],0,4).' '.substr($nfceInfo['NRACESSONFCE'],4,4).' '.substr($nfceInfo['NRACESSONFCE'],8,4).' '.substr($nfceInfo['NRACESSONFCE'],12,4).' '.
            substr($nfceInfo['NRACESSONFCE'],16,4).' '.substr($nfceInfo['NRACESSONFCE'],20,4).' '.substr($nfceInfo['NRACESSONFCE'],24,4).' '.substr($nfceInfo['NRACESSONFCE'],28,4).' '.
            substr($nfceInfo['NRACESSONFCE'],32,4).' '.substr($nfceInfo['NRACESSONFCE'],36,4).' '.substr($nfceInfo['NRACESSONFCE'],40,4)) . $printerParams['comandoEnter'];

		$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
		return $texto;
	}


	private function montaInfoDadosCupom($texto, $nfceInfo, $printerParams){
        $data = new \DateTime($nfceInfo['DTEMISSAONFCE']);
        $data = $data->format('d/m/Y H:i:s');
		$texto .= $this->impressaoUtil->centraliza($printerParams, 'Numero '.$nfceInfo['NRNOTAFISCALCE'].' Serie '.$nfceInfo['CDSERIENFCE']) . $printerParams['comandoEnter'];

        $viaConsumidor = $texto . $this->impressaoUtil->centraliza($printerParams, 'Emissao '. $data.' - Via Consumidor') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
        $viaEstabelecimento = '';

        $infoStatusCupom = $this->montaInfoStatusCupom('', $nfceInfo, $printerParams, true);
        if ($nfceInfo['IDSTATUSNFCE'] == 'A') {
        	$data = $nfceInfo['DTHRPROTOCONFCE']->format('d/m/Y H:i:s');
            $infoStatusCupom .= $this->impressaoUtil->centraliza($printerParams, 'Protocolo de Autorizacao: ' . $nfceInfo['NRPROTOCOLONFCE']) . $printerParams['comandoEnter'];
            $infoStatusCupom .= $this->impressaoUtil->centraliza($printerParams, 'Data de Autorizacao: ' . $data) . $printerParams['comandoEnter'];
			$infoStatusCupom .= $this->impressaoUtil->imprimeLinha($printerParams);
		} else {
			if ($nfceInfo['IDUMAVIANFCE'] != 'S'){ // valida impressão da segunda via
				$viaEstabelecimento = $texto . $this->impressaoUtil->centraliza($printerParams, 'Emissao '. $data.' - Via do Estabelecimento') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
				$viaEstabelecimento .= $infoStatusCupom;
			}
		}

		return array(
			'VIACONSUMIDOR' => $viaConsumidor . $infoStatusCupom,
			'VIAESTABELECIMENTO' => $viaEstabelecimento
		);
	}

	private function montaInfoConsumidorCupom($texto, $infoConsumer, $printerParams, $nfceData){
        if (!empty($infoConsumer['NRINSCRCONS'])){
			if ($nfceData['IDIMPCNPJCLIE'] == 'S'){
				if ($infoConsumer['IDTPIJURCLIE'] == 'J' || $infoConsumer['IDTPIJURCLIE'] == 'I') {
					if (!empty($infoConsumer['NRINSJURCLIE'])) {
						$texto .= $this->impressaoUtil->centraliza($printerParams, 'CONSUMIDOR CPF/CNPJ: ' . $this->util->aplicaMascaraCpfCnpj($infoConsumer['NRINSJURCLIE'])) . $printerParams['comandoEnter'];
						$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper($infoConsumer['NMFANTCLIE'])) . $printerParams['comandoEnter'];
					}
			  	} else {
					$texto .= $this->impressaoUtil->centraliza($printerParams, 'CONSUMIDOR CPF/CNPJ: ' . $this->util->aplicaMascaraCpfCnpj($infoConsumer['NRINSCRCONS'])) . $printerParams['comandoEnter'];
	                if (!empty($infoConsumer['NMCONSVEND'])){
						$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper($infoConsumer['NMCONSVEND'])) . $printerParams['comandoEnter'];
					} else if (!empty($infoConsumer['NMCONSUMIDOR'])){
						$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper($infoConsumer['NMCONSUMIDOR'])) . $printerParams['comandoEnter'];
					} else {
						$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper('CONSUMIDOR FINAL')) . $printerParams['comandoEnter'];
					}
			  	}
		   	} else {
            	$texto .= $this->impressaoUtil->centraliza($printerParams, 'CONSUMIDOR CPF/CNPJ: ' . $this->util->aplicaMascaraCpfCnpj($infoConsumer['NRINSCRCONS'])) . $printerParams['comandoEnter'];
                if (!empty($infoConsumer['NMCONSVEND'])){
					$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper($infoConsumer['NMCONSVEND'])) . $printerParams['comandoEnter'];
			  	} else if (!empty($infoConsumer['NMCONSUMIDOR'])){
					$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper($infoConsumer['NMCONSUMIDOR'])) . $printerParams['comandoEnter'];
			  	} else {
					$texto .= $this->impressaoUtil->centraliza($printerParams, strtoupper('CONSUMIDOR FINAL')) . $printerParams['comandoEnter'];
			  	}
		   	}
		} else {
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'CONSUMIDOR NAO IDENTIFICADO') . $printerParams['comandoEnter'];
		}
        $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
        return $texto;
	}

	private function montaInfoTributosCupom($texto, $nfceInfo, $printerParams){
		$this->impressaoUtil->checaEnter($texto, $printerParams);
		$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
        $texto .= $this->impressaoUtil->centraliza($printerParams, 'Informacao dos Tributos Totais Incidentes') . $printerParams['comandoEnter'];
		$vrTributo = $this->impressaoUtil->formataNumero(floatval($nfceInfo['VRTOTTRIBIBPT']), 2);
        $texto .= $this->impressaoUtil->centraliza($printerParams, '(Lei Federal 12.741/2012)      R$ ' . $vrTributo) . $printerParams['comandoEnter'];
        $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
        return $texto;
	}

	private function getCanceledProducts($nfceInfo, $filialInfo){
		$canceledProductsParams = array(
			':CDFILIAL'  => $filialInfo['CDFILIAL'],
			':CDCAIXA'   => $filialInfo['CDCAIXA'],
			':NRSEQVENDA'=> $nfceInfo['nrseqvenda']
		);
		return $this->entityManager->getConnection()->fetchAll("GET_CANCELED_PRODUCTS", $canceledProductsParams);
	}

	private function montaProdutosCancelados($nfceInfo, $filialInfo, $printerParams){
		$texto = '';
        $canceledProducts = $this->getCanceledProducts($nfceInfo, $filialInfo);
        if (!empty($canceledProducts)){
           $this->impressaoUtil->checaEnter($texto, $printerParams);
           $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
           $texto .= 'Itens Cancelados: '.$printerParams['comandoEnter'];
		   foreach($canceledProducts as $canceled){
				$texto .= $canceled['NRSEQITVENDC'] . ' ' . $canceled['CDPRODUTO'] . ' ' . $canceled['NMPRODUTO'] . ' - ' . $canceled['QTPRODVENDC'] . ' ' . $canceled['SGUNIDADE'] . $printerParams['comandoEnter'];
		   }
		}

		return $texto;
	}

	private function montaRodapeCupom($texto, $nfceInfo, $filialInfo, $printerParams, $FIDELITYVALUE, $infoConsumer){
        $this->impressaoUtil->checaEnter($texto, $printerParams);
        $texto .= $this->impressaoUtil->centraliza($printerParams, 'Teknisa Software - www.teknisa.com') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
        $texto .= 'Filial: '.$filialInfo['CDFILIAL'].' '.$filialInfo['NMFILIAL'] . $printerParams['comandoEnter'];
        $texto .= '  Loja: '.$filialInfo['CDLOJA'  ].' '.$nfceInfo['nmloja'] . $printerParams['comandoEnter'];
        $texto .= ' Caixa: '.$filialInfo['CDCAIXA' ].' '.$nfceInfo['nmcaixa'] . $printerParams['comandoEnter'].$printerParams['comandoEnter'];

        if($FIDELITYVALUE > 0) {
            $texto .= $this->impressaoUtil->centraliza($printerParams, 'Voce recebeu um desconto de R$' . number_format($FIDELITYVALUE, 2, ',', '.') . ' pelo Fidelidade')
                . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
        }

        if (!empty($nfceInfo['DSOBSCUPFIS'])){
            $texto .= $nfceInfo['DSOBSCUPFIS'] . $printerParams['comandoEnter'];
		}
		if (empty($nfceInfo['CDSENHAPED']) && empty($filialInfo['NRMESA'])){
			$texto .= $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
		}
        $infoPaymentNFCE = $this->getInfoPayment($filialInfo, $nfceInfo);
        $vendaDebCreCons = false;

        foreach ($infoPaymentNFCE as $payment) {
            if ($payment['IDTIPORECE'] == '9') {
                $vendaDebCreCons = true;
            }
        }

        if ($vendaDebCreCons) {
            $linhaCons = 'Cliente:    ' . $infoConsumer['CDCLIENTE'] . ' - ' . $infoConsumer['NMFANTCLIE'] . $printerParams['comandoEnter'];
            $texto .= $this->impressaoUtil->quebraLinha($linhaCons, $printerParams, true);
            $linhaCons = 'Consumidor: ' . $infoConsumer['CDCONSUMIDOR'] . $printerParams['comandoEnter'];
            $texto .= $this->impressaoUtil->quebraLinha($linhaCons, $printerParams, true);
            $linhaCons = 'Nome:       ' . $infoConsumer['NMCONSUMIDOR'] . $printerParams['comandoEnter'];
            $texto .= $this->impressaoUtil->quebraLinha($linhaCons, $printerParams, true);
            $valor = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_BALANCE_API", array($infoConsumer['CDCLIENTE'], $infoConsumer['CDCONSUMIDOR']));
            $valor = !empty($valor) ? $valor['SALDO'] : 0;
            $valor = $this->impressaoUtil->formataNumero(floatval($valor), 2);
            $texto .= 'Saldo Total: R$' . $valor . $printerParams['comandoEnter'];
        }

		return $texto;
	}

	private function montaTextoSenhaMesa($nfceInfo, $printerParams, $largura, $filialInfo, $dadosImpressora, $rodapePainelSenha){
		$texto = '';
		$printerParams['largura'] = $largura;

		if (!empty($nfceInfo['CDSENHAPED'])){
			$texto .= 'SENHA: ' . $nfceInfo['CDSENHAPED'] . '  ';
		}
        if(!empty($filialInfo['NRMESA'])){
            $texto .= 'MESA: ' . $filialInfo['NRMESA'] . $printerParams['comandoEnter'];
        }
        $texto = !empty($texto) ? $printerParams['comandoEnter'] . $texto : $texto;
        $texto .= $printerParams['comandoEnter'];

    	$espacoFinal = '';
    	if($dadosImpressora['IDMODEIMPRES']!='27' ){
        	$espacoFinal .= $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
    	}
    	$espacoFinal .= $printerParams['comandoEnter'];

        if (!empty($rodapePainelSenha)){
        	$rodapePainelSenha['final'] .= $espacoFinal;
        } else {
        	$texto .= $espacoFinal;
        }

		return $texto;
	}

    private function getNFCEDataToPrint($nrAcessoNFCE, $filialInfo){
		$paramsToNFCeData = array(
			'NRACESSONFCE' => $nrAcessoNFCE,
			'CDFILIAL' => $filialInfo['CDFILIAL'],
			'CDCAIXA' => $filialInfo['CDCAIXA']
		);
		$nfceData = $this->entityManager->getConnection()->fetchAssoc("GET_NFCE_TO_PRINT", $paramsToNFCeData);
		return array(
			'error'=> false,
			'nfceData' => $nfceData
		);
	}

	private function getParavendData($CDFILIAL){
		try{
			$paramsToParavendData = array(
				':CDFILIAL' => $CDFILIAL
			);
			$paravendData = $this->entityManager->getConnection()->fetchAssoc("GET_PARAVEND_DATA_TO_PRINT", $paramsToParavendData);
			$result = array(
				'error'=> false,
				'paravendData' => $paravendData
			);
		} catch(\Exception $e){
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			$result = array(
				'error'=> true,
				'message' => $e->getMessage()
			);
		}
		return $result;
	}

	private function getDadosImpressora($CDFILIAL, $CDCAIXA, $NRORG) {
		$paramsToPrinterData = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA
		);
		$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $paramsToPrinterData);
		$result = array(
			'error' => false,
			'dadosImpressora' => $dadosImpressora
		);
		return $result;
	}

}