<?php
namespace Service;

use \Odhen\API\Remote\Printer\Command;

class Impressao {

	protected $entityManager;
	protected $util;
	protected $impressaoUtilAPI;
	protected $tableService;
	protected $billService;
	protected $date;
	protected $instanceManager;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Util\Util $util,
		\Odhen\API\Lib\ImpressaoUtil $impressaoUtilAPI,
		\Service\Table $tableService,
		\Service\Bill $billService,
		\Util\Date $date,
		\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager
	){
		$this->entityManager       = $entityManager;
		$this->util                = $util;
		$this->impressaoUtilAPI    = $impressaoUtilAPI;
		$this->tableService        = $tableService;
		$this->billService         = $billService;
		$this->date                = $date;
		$this->instanceManager 	   = $instanceManager;
	}

	public function buscaMensagemImpressa($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);
		$results = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_MENSAGEM_IMPRESSA", array($session['CDFILIAL'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST'], $session['CDLOJA']));
		if (Empty($results['TXMOTIVCANCE'])) $mensagemImpressa = '';
		else $mensagemImpressa = $results['TXMOTIVCANCE'];
		$result = array('funcao' => '1', 'retorno' => $mensagemImpressa);
		return $result;
	}

	public function enviaMensagemProducao($dataset){

		$retornoFinal = array('error'=> false);
		foreach ($dataset['nrimpressora'] as $impressora) {

			$session = $this->util->getSessionVars($dataset['chave']);

			if ($dataset['NRVENDAREST'] !== "waiterless"){
				if ($dataset['modoHabilitado'] == 'M') {
					$valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST']);
					$stNrVendaRest = $valMesa['NRVENDAREST'];
					$stNrComanda = $valMesa['NRCOMANDA'];
					$descMesaComanda = $valMesa['NMMESA'];
				}
				else if ($dataset['modoHabilitado'] == 'C'){
					// valida e busca dados da comanda
					$valComanda = $this->billService->dadosComanda($session['CDFILIAL'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST'], $session['CDLOJA']);
					$stNrVendaRest = $valComanda['NRVENDAREST'];
					$stNrComanda = $valComanda['NRCOMANDA'];
					$descMesaComanda = 'Comanda ' . $valComanda['DSCOMANDA'];
				}
			}
			else {
				$descMesaComanda = 'MENSAGEM GERAL';
			}

			$params = array($session['CDFILIAL'], $session['CDLOJA'], $impressora);
			$r_valImprLoja = $this->entityManager->getConnection()->fetchAssoc("SQL_VAL_IMPR_LOJA", $params);

			$printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($r_valImprLoja['IDMODEIMPRES']);
			$text = '';

			$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
			$text .= 'Filial   : ' . $session['CDFILIAL'] . $printerParams['comandoEnter'];
			$text .= 'Loja     : ' . $session['CDLOJA'] . $printerParams['comandoEnter'];
			$text .= 'Caixa    : ' . $session['CDCAIXA'] . $printerParams['comandoEnter'];
			$text .= 'Data     : ' . date('d/m/Y H:i:s') . $printerParams['comandoEnter'];
			$text .= 'Operador : ' . $session['CDOPERADOR'] . ' - ' . $session['NMFANVEN'] . $printerParams['comandoEnter'];
            $text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
			$text .= $printerParams['comandoEnter'] . $this->impressaoUtilAPI->centraliza($printerParams, $descMesaComanda, $printerParams['largura']) . $printerParams['comandoEnter'];
			$text .= $printerParams['comandoEnter'];
			$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
			$text .= 'MENSAGEM' . $printerParams['comandoEnter'];
			$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
			$text .= $dataset['mensagem'] . $printerParams['comandoEnter'];
			$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
			$text .= 'FIM' . $printerParams['comandoEnter'];
			$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
			$text .= $printerParams['comandoEnter'];
			$text .= $printerParams['comandoEnter'];
			$text .= $printerParams['comandoEnter'];
			$yesterday = date("d/m/Y", time() - 60 * 60 * 24);
			$this->util->newCode('VENDADIA' . $session['CDFILIAL'] . $yesterday);
			$nrPedido = $this->util->getNewCode('VENDADIA' . $session['CDFILIAL'] . $yesterday, 5);
			$retornoImpressao = $this->impressaoUtilAPI->imprimeNaoFiscal($text, $r_valImprLoja, $nrPedido . 'E'); // E de mensagem
			if($retornoImpressao['error']){
				$retornoFinal['error'] = true;
				if(!isset($retornoFinal['message'])){
					$retornoFinal['message'] = 'Não foi prossivel imprimir nas seguintes impressoras: <br><br>';
				}
				$retornoFinal['message'] .= '- ' . $retornoImpressao['message'] . '<br>';
			}
			if(isset($retornoImpressao['saas']) && $retornoImpressao['saas']){
				array_push($retornoFinal, $retornoImpressao);
			}
		}

		if ($dataset['NRVENDAREST'] !== "waiterless"){
			$params = array($dataset['mensagem'] . $dataset['historico'], $session['CDFILIAL'], $stNrVendaRest, $stNrComanda);
			$this->entityManager->getConnection()->executeQuery("SQL_UPDATA_MENS", $params);
		}
		return $retornoFinal;
	}

	public function printRelease($chave, $NRCOMANDA, $NRVENDAREST, $products, $printerCode){

		$session = $this->util->getSessionVars($chave);

		$valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST);
		$stNrVendaRest = $valMesa['NRVENDAREST'];
		$stNrComanda = $valMesa['NRCOMANDA'];
		$descMesaComanda = $valMesa['NMMESA'];

		$productNames = array();
		foreach ($products as $product){
			$params = array(
				$product['NRPEDIDOFOS'],
				$product['NRITPEDIDOFOS']
			);
			$name = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_PRODUCT_NAME", $params);
			$params = array(
				$product['CDFILIAL'],
				$product['NRVENDAREST'],
				$product['NRCOMANDA'],
				$product['NRPRODCOMVEN']
			);
			$TXPRODCOMVEN = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_TXPRODCOMVEN", $params);
			if (!empty($TXPRODCOMVEN['TXPRODCOMVEN'])) $observations = explode(';', $TXPRODCOMVEN['TXPRODCOMVEN']);
			else $observations = array();
			array_push($productNames, array($name, $observations));
		}
		if(!isset($printerCode) || $printerCode == '' ){
			$printerCode = $session['NRSEQIMPRLOJA1'];
		}
		$params = array($session['CDFILIAL'], $session['CDLOJA'], $printerCode);
		$r_valImprLoja = $this->entityManager->getConnection()->fetchAssoc("SQL_VAL_IMPR_LOJA", $params);

		$printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($r_valImprLoja['IDMODEIMPRES']);
		$text = '';

		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams['largura']);
		$text .= 'Filial   : ' . $session['CDFILIAL'] . $printerParams['comandoEnter'];
		$text .= 'Loja     : ' . $session['CDLOJA'] . $printerParams['comandoEnter'];
		$text .= 'Caixa    : ' . $session['CDCAIXA'] . $printerParams['comandoEnter'];
		$text .= 'Data     : ' . date('d/m/Y H:i:s') . $printerParams['comandoEnter'];
		$text .= 'Operador : ' . $session['CDOPERADOR'] . ' - ' . $session['NMFANVEN'] . $printerParams['comandoEnter'];
		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams['largura']);
		$text .= $printerParams['comandoEnter'] . $this->impressaoUtilAPI->centraliza($printerParams, $descMesaComanda, $printerParams['largura']) . $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams['largura']);
		$text .= '--> PODE MANDAR ' . $printerParams['comandoEnter'];

		foreach ($productNames as $product){
			$text .= '* ' . $product[0]['NMPRODUTO'] . $printerParams['comandoEnter'];
			if (!empty($product[1])){
				foreach ($product[1] as $obs){
					if (strlen(trim($obs)) > 0) $text .= '   - '.trim($obs).$printerParams['comandoEnter'];
				}
			}
		}

		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams['largura']);
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];

		$yesterday = date("d/m/Y", time() - 60 * 60 * 24);
		$this->util->newCode('VENDADIA' . $session['CDFILIAL'] . $yesterday);
		$nrPedido = $this->util->getNewCode('VENDADIA' . $session['CDFILIAL'] . $yesterday, 5);
		$this->impressaoUtilAPI->imprimeNaoFiscal($text, $r_valImprLoja, $nrPedido . 'E'); // E de mensagem
	}

	public function imprimeCancelamento($chave, $dadosOld, $modo, $dsComanda){
		$session = $this->util->getSessionVars($chave);
		$dados = array();

		for ($i = 0; $i < count($dadosOld); $i++) {
			if (isset($dadosOld[$i]['NRSEQIMPRLOJA'])) {
				$dados[] = $dadosOld[$i];
			}
		}

		if (Empty($dados)) return array('error' => true, 'message' => 'Nenhuma impressora vinculado ao produto.');

		$impressora = $dados[0]['NRSEQIMPRLOJA'];
		$text = '';
		$yesterday = date("d/m/Y", time() - 60 * 60 * 24);
		$this->util->newCode('VENDADIA' . $session['CDFILIAL'] . $yesterday);
		$nrPedido = $this->util->getNewCode('VENDADIA' . $session['CDFILIAL'] . $yesterday, 5);

		$oldItem = null;
		// imprime cancelamento
		$numMesa = $dados[0]['NRMESA'];
		// monta o cabeçalho do cancelamento
		//********************************************************************************
		$printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($dados[0]['IDMODEIMPRES']);
		$text .= $printerParams['comandoEnter'];
		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
		$text .= $this->impressaoUtilAPI->centraliza($printerParams, 'CANCELAMENTO DE PEDIDO', $printerParams['largura']) . $printerParams['comandoEnter'];
		$data = new \DateTime('NOW');
		$text .= $this->impressaoUtilAPI->centraliza($printerParams, 'DATA: ' . $data->format('Y-m-d H:i:s'), $printerParams['largura']) . $printerParams['comandoEnter'];
		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
		if ($modo == 'C')
			$text .= 'Comanda : ' . $dsComanda . $printerParams['comandoEnter'];
		$text .= 'Mesa    : ' . $numMesa . $printerParams['comandoEnter'];
		$text .= 'Garcom  : ' . $session['CDVENDEDOR'] . ' - ' . $session['NMFANVEN'] . $printerParams['comandoEnter'];
		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
		//********************************************************************************


		for ($i = 0; $i < count($dados); $i++) {

			if ($impressora != $dados[$i]['NRSEQIMPRLOJA']) {
				// quando a porta do produto trocar, deve-se imprimir e cortar o papel
				// monta rodapé
				//********************************************************************************
				if (!Empty($dados[0]['MOTIVO']))
					$text .= 'MOT: ' . $dados[0]['MOTIVO'] . $printerParams['comandoEnter'];
				$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
				//********************************************************************************

				$this->impressaoUtilAPI->imprimeNaoFiscal($text, $oldItem, $nrPedido . 'C'); // C de cancelamento
				$impressora = $dados[$i]['NRSEQIMPRLOJA'];

				$text = '';
				// monta o cabeçalho do cancelamento
				//********************************************************************************
				$printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($dados[$i]['IDMODEIMPRES']);
				$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
				$text .= $this->impressaoUtilAPI->centraliza($printerParams, 'CANCELAMENTO DE PEDIDO', $printerParams['largura']) . $printerParams['comandoEnter'];
				$data = new \DateTime('NOW');
				$text .= $this->impressaoUtilAPI->centraliza($printerParams, 'DATA: ' . $data->format('Y-m-d H:i:s'), $printerParams['largura']) . $printerParams['comandoEnter'];
				$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
				if ($modo == 'C')
					$text .= 'Comanda : ' . $dsComanda . $printerParams['comandoEnter'];
				$text .= 'Mesa    : ' . $numMesa . $printerParams['comandoEnter'];
				$text .= 'Garcom  : ' . $session['CDVENDEDOR'] . ' - ' . $session['NMFANVEN'] . $printerParams['comandoEnter'];
				$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
				//********************************************************************************
			}

			$text .= ' ---> ' . $dados[$i]['QTPRODCOMVEN'] . ' ' . $dados[$i]['NMPRODUTO'] . $printerParams['comandoEnter'];
			$oldItem = $dados[$i];
		}
		// acrescenta o motivo
		if (!Empty($dados[0]['MOTIVO']))
			$text .= 'MOT: ' . $dados[0]['MOTIVO'] . $printerParams['comandoEnter'];

		// monta rodapé
		//********************************************************************************
		$text .= $this->impressaoUtilAPI->imprimeLinha($printerParams);
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		$text .= $printerParams['comandoEnter'];
		//********************************************************************************

		$result = $this->impressaoUtilAPI->imprimeNaoFiscal($text, $oldItem, $nrPedido . 'C'); // C de cancelamento
		return $result;
	}

	public function imprimeParcial ($produtosParcial ,$chave, $cdfilial, $stVendaRests, $stComandaVens, $cdprodcouver, $modo,
		$nrvendarest, $nrcomanda, $cdloja, $nrmesa, $impPosicao, $nrpessoas, $totalSemDesconto, $totalDesconto, $taxaDeServico,
        $total,	$totalPorPessoa, $horas, $minutos, $segundos, $dscomanda, &$dadosImpressao, $fidelityValue, $couvert){
		$session = $this->util->getSessionVars($chave);
        // PRODUCT AGRUPATOR
        $params = array(
			'CDFILIAL' => $cdfilial,
			'NRVENDAREST' => $nrvendarest,
			'NRCOMANDA' => $nrcomanda
		);
    	$dadosComissao = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_COMISSAO_VENDA", $params);

		$produtosAgrupados = array();
        foreach ($produtosParcial as $produto){
			$quant = floatval($produto['QTPRODCOMVEN']);
            $key = $produto['NRLUGARMESA'].$produto['CDPRODUTO'].$produto['VRPRECCOMVEN'].$produto['VRDESCCOMVEN'].$produto['VRACRCOMVEN'];
            if ($produto['IDPESAPROD'] != 'S' && array_key_exists($key, $produtosAgrupados)){
				$produtosAgrupados[$key]['QTPRODCOMVEN'] = floatval($produtosAgrupados[$key]['QTPRODCOMVEN']) + $quant;
				$produtosAgrupados[$key]['VRDESCCOMVEN'] = round($produtosAgrupados[$key]['VRDESCCOMVEN'] + $produto['VRDESCCOMVEN'], 2);
				$produtosAgrupados[$key]['VRACRCOMVEN'] = round($produtosAgrupados[$key]['VRACRCOMVEN'] + $produto['VRACRCOMVEN'], 2);
            }
            else {
            	if ($produto['IDPESAPROD'] == 'S') $key .= $produto['NRPRODCOMVEN'];
            	$produtosAgrupados[$key] = $produto;
			}
        }
        $produtosParcial = $produtosAgrupados;

		$arrPosicoes = array_filter(array_unique(array_column($produtosParcial,'NRLUGARMESA'))); 
		$arrNmPosicao = $this->buscaNomePorPosicao($cdfilial, $nrvendarest, $arrPosicoes);


		$valImpreLoja = $this->entityManager->getConnection()->fetchAssoc("SQL_VAL_IMPRE_LOJA", array($cdfilial, $cdloja, $session["NRSEQIMPRLOJA1"]));
		// Define parametros da impressora.
		$printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($valImpreLoja["IDMODEIMPRES"]);
		$comandos = new Command();
		$printerParams['letterType'] = $printerParams['tipoLetra'];
		$printerParams['bold'] = false;
		$strImprimir = '';
		// Monta string de impressao.
		$strImprimir .= $this->impressaoUtilAPI->centraliza($printerParams, "ACOMPANHAMENTO DO PEDIDO", $printerParams['largura']) . $printerParams['comandoEnter'];
		$strImprimir .= $this->impressaoUtilAPI->preencheLinha($printerParams, '', ' ') . $printerParams['comandoEnter'];
		if ($modo === "C") $strImprimir .= "Comanda: $dscomanda". $printerParams['comandoEnter'];

		$strImprimir .= "Mesa   : $nrmesa". $printerParams['comandoEnter'];
				
		$strImprimir .= "Garcom : ".$this->util->removeAcentos($session["NMFANVEN"]). $printerParams['comandoEnter'];//nome do Garcom
		$strImprimir .= 'Emissao: '.$this->date->getDataAtual()->format(\Util\Date::FORMATO_BRASILEIRO_DATAHORA). $printerParams['comandoEnter'];

		$firstBlank   = true;
		$posicaoAtual = null;
		$prodCobTax = 0;
		foreach ($produtosParcial as $produtoParcial){
			$posicaoMesa = $produtoParcial["NRLUGARMESA"];
			if (($session["IDLUGARMESA"] === "S") && ($modo === "M")){
				if ($posicaoAtual != $posicaoMesa){
					$strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
					if ($firstBlank) $firstBlank = false;
					else $strImprimir .=  $printerParams['comandoEnter'];
					$strImprimir .= "Posicao ".$posicaoMesa;
					if (array_key_exists($posicaoMesa, $arrNmPosicao)){
						$nomeConsumidor = $arrNmPosicao[$posicaoMesa]['DSCONSUMIDOR'];
						$strImprimir .= ' - ' . $nomeConsumidor;
					}
					$strImprimir .= $printerParams['comandoEnter'];
					$posicaoAtual = $posicaoMesa;
				}
			}
			if ($firstBlank){
				$strImprimir .= "Posicao ".$posicaoMesa;
				if (array_key_exists($posicaoMesa, $arrNmPosicao)){
					$nomeConsumidor = $arrNmPosicao[$posicaoMesa]['DSCONSUMIDOR'];
					$strImprimir .=  ' - ' . $nomeConsumidor;
				}
				$strImprimir .= $printerParams['comandoEnter'];
				$firstBlank = false;
			}

            $strImprimir .= $produtoParcial["NMPRODUTO"]. $printerParams['comandoEnter'];

            if ($session['IDMOSTRADESPARC'] === "S" && $produtoParcial["VRDESCCOMVEN"] > 0){
                $totProd = $produtoParcial["QTPRODCOMVEN"] * ($produtoParcial["VRPRECCOMVEN"] + $produtoParcial["VRPRECCLCOMVEN"]);
                $totProd = $this->util->formataPreco($totProd);
                $textoPreco = $this->util->toRight(str_replace(".",",",number_format($produtoParcial["QTPRODCOMVEN"], 3)) . " X " . $this->util->formataPreco($produtoParcial["VRPRECCOMVEN"] + $produtoParcial["VRPRECCLCOMVEN"]), $totProd, $printerParams['largura']);

                $discount = str_replace(".",",",number_format($produtoParcial["VRDESCCOMVEN"], 2));
                $discountedPrice = str_replace(".",",",number_format(($produtoParcial["QTPRODCOMVEN"] * ($produtoParcial["VRPRECCOMVEN"] + $produtoParcial["VRPRECCLCOMVEN"])) - $produtoParcial["VRDESCCOMVEN"], 2));
                $discountString = $this->util->toRight('Desconto            -'.$discount, $discountedPrice, $printerParams['largura']);
                $textoDesconto = $discountString . $printerParams['comandoEnter'];

                $texto = $textoPreco . $printerParams['comandoEnter'] . $textoDesconto;
            }
            else {
                $vrProd = round($produtoParcial["VRPRECCOMVEN"] + $produtoParcial["VRPRECCLCOMVEN"] - ($produtoParcial["VRDESCCOMVEN"]/$produtoParcial["QTPRODCOMVEN"]), 2);
                $totProd = $this->util->formataPreco($produtoParcial["QTPRODCOMVEN"] * $vrProd);
                $vrProd = $this->util->formataPreco($vrProd);
                $texto = $this->util->toRight(str_replace(".",",",number_format($produtoParcial["QTPRODCOMVEN"], 3)) . " X " . $vrProd, $totProd, $printerParams['largura']);
            }

            $strImprimir .= $texto. $printerParams['comandoEnter'];

			if ($produtoParcial['IDCOBTXSERV'] === 'S' && $cdprodcouver !== $produtoParcial['CDPRODUTO']){
                $vrProd = round($produtoParcial["VRPRECCOMVEN"] + $produtoParcial["VRPRECCLCOMVEN"], 2);
                $totProd = $this->util->formataPreco($produtoParcial["QTPRODCOMVEN"] * $vrProd - $produtoParcial["VRDESCCOMVEN"]);
				$prodCobTax += floatval(str_replace(",",".",$totProd));
            }
		}
		$vrcomisvenda1 = floatval($session["VRCOMISVENDA"]);
		if ($impPosicao == '' && $nrpessoas > 1 && $session["IDLUGARMESA"] == "S" && $modo == "M") {
			$porcentServico = isset($dadosComissao['VRCOMISPOR']) ? $dadosComissao['VRCOMISPOR']: $vrcomisvenda1;
			$strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
			$params = array(
				'CDFILIAL' => $cdfilial,
				'NRVENDAREST' => $stVendaRests,
				'NRCOMANDA' => $stComandaVens,
				'CDPRODUTO' => $cdprodcouver
			);
			$posicaoParcial = $this->entityManager->getConnection()->fetchAll("SQL_POSICAO_PARCIAL", $params);
			foreach ($posicaoParcial as $pos){
				// TOTAL PRODUTOS POSIÇÃO
				$vrTotPos = floatval($pos["PRECOTX"]) + floatval($pos["PRECONTX"]);
				$texto = "Total P" . $pos["NRLUGARMESA"] . ": " . $this->util->formataPreco($vrTotPos) . " ";
				// TAXA POSIÇÃO
				if($taxaDeServico > 0) {
					$vrTxPos = (floatval($pos["PRECOTX"]) / 100) * $porcentServico;
					$texto .= "TX ". $this->util->formataPreco($vrTxPos) . " ";
				} else {
					$vrTxPos = 0;
					$texto .= "TX ". $this->util->formataPreco($vrTxPos) . " ";
				}
				// TOTAL GERAL POR POSIÇÃO
				$texto .= $this->util->formataPreco($vrTotPos + $vrTxPos);
				$strImprimir .= $texto . $printerParams['comandoEnter'];
			}
		}

		// lista produtos
		$strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
		$texto        = "Total Produtos";

        if ($session['IDMOSTRADESPARC'] === "S" && $totalDesconto > 0) {
            $strImprimir .= $this->util->toRight($texto, $this->util->formataPreco($totalSemDesconto), $printerParams['largura']) . $printerParams['comandoEnter'];
            $texto        = "Desconto";
            $strImprimir .= $this->util->toRight($texto, $this->util->formataPreco($totalDesconto), $printerParams['largura']) . $printerParams['comandoEnter'];
        }
        else {
            $strImprimir .= $this->util->toRight($texto, $this->util->formataPreco(round($totalSemDesconto - $totalDesconto, 2)), $printerParams['largura']) . $printerParams['comandoEnter'];
        }

		if ($fidelityValue > 0){
			$texto        = "Desconto Fidelidade";
			$strImprimir .= $this->util->toRight($texto, $this->util->formataPreco($fidelityValue), $printerParams['largura']) . $printerParams['comandoEnter'];
		}
		$totalPagar = round($totalSemDesconto - $totalDesconto - $fidelityValue, 2);
		$texto = "Subtotal";
		$strImprimir .= $this->util->toRight($texto, $this->util->formataPreco($totalPagar), $printerParams['largura']) . $printerParams['comandoEnter'];

		if ($taxaDeServico > 0) {
			$texto        = "Gorjeta Sugerida";
			$strImprimir .= $this->util->toRight($texto, $this->util->formataPreco($taxaDeServico), $printerParams['largura']) . $printerParams['comandoEnter'];
		}

		// coloca total de couvert
		if ($couvert > 0) {
			$texto = "Couvert ";
			$strImprimir .= $this->util->toRight($texto, $this->util->formataPreco($couvert), $printerParams['largura']) . $printerParams['comandoEnter'];
		}
		$strImprimir = $this->impressaoUtilAPI->formataMp20($valImpreLoja['IDMODEIMPRES'], $strImprimir);
		$comandos->text($strImprimir, $printerParams);
		

		$printerParams['bold'] = true;
		$strImprimir = !$printerParams['impressaoFront'] ? '' : $strImprimir;
		$temp = "Total Geral";
		$strImprimir .= $this->util->toRight($temp, $this->util->formataPreco($total), $printerParams['largura']) . $printerParams['comandoEnter'];
		$strImprimir = $this->impressaoUtilAPI->formataMp20($valImpreLoja['IDMODEIMPRES'], $strImprimir);
		$comandos->text($strImprimir, $printerParams);

        
		$printerParams['bold'] = false;
		$strImprimir = !$printerParams['impressaoFront'] ? '' : $strImprimir;
		if ($session['IDCOMISVENDA'] === 'S' && $prodCobTax > 0) {
	        $vrcomisvenda1 = !empty($vrcomisvenda1) ? $vrcomisvenda1 : null;
	        $vrcomisvenda2 = !empty(floatval($session["VRCOMISVENDA2"])) ? floatval($session["VRCOMISVENDA2"]) : null;
	        $vrcomisvenda3 = !empty(floatval($session["VRCOMISVENDA3"])) ? floatval($session["VRCOMISVENDA3"]) : null;

			if (($vrcomisvenda1 !== null && $vrcomisvenda2 !== null)  || ($vrcomisvenda1 !== null && $vrcomisvenda3 !== null) || ($vrcomisvenda2 !== null && $vrcomisvenda3 !== null)){
	            $strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
				$texto = "Gorjeta Sugerida";

	            if ($vrcomisvenda1 !== null) {
		            $vrTaxa = ($prodCobTax / 100) * $vrcomisvenda1;
		            $strImprimir .= $this->util->toRight($texto . ' ' . $this->util->formataPreco($vrcomisvenda1) . '%', $this->util->formataPreco($vrTaxa + $totalPagar), $printerParams['largura']) . $printerParams['comandoEnter'];
		        }
	            if ($vrcomisvenda2 !== null) {
	                $vrTaxa = ($prodCobTax / 100) * $vrcomisvenda2;
	                $strImprimir .= $this->util->toRight($texto . ' ' . $this->util->formataPreco($vrcomisvenda2) . '%', $this->util->formataPreco($vrTaxa + $totalPagar), $printerParams['largura']) . $printerParams['comandoEnter'];
	            }
	            if ($vrcomisvenda3 !== null) {
	                $vrTaxa = ($prodCobTax / 100) * $vrcomisvenda3;
	                $strImprimir .= $this->util->toRight($texto . ' ' . $this->util->formataPreco($vrcomisvenda3) . '%', $this->util->formataPreco($vrTaxa + $totalPagar), $printerParams['largura']) . $printerParams['comandoEnter'];
	            }
	        }
	    }

		if ($nrpessoas > 1) {
			$strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
			if ($impPosicao == ''){
				$strImprimir .= "$nrpessoas PESSOA(S) - " . $this->util->formataPreco($totalPorPessoa) . " POR PESSOA".$printerParams['comandoEnter'];
			}
		} else {
			$strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
		}

		$strImprimir .= "Tempo de permanencia - $horas:$minutos:$segundos". $printerParams['comandoEnter'];
		$strImprimir .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $strImprimir);
        $strImprimir .= $this->impressaoUtilAPI->centraliza($printerParams, 'Teknisa Software - www.teknisa.com') . $printerParams['comandoEnter'];
		$strImprimir .= $printerParams['comandoEnter'];
		$strImprimir = $this->impressaoUtilAPI->formataMp20($valImpreLoja['IDMODEIMPRES'], $strImprimir);
		$comandos->text($strImprimir, $printerParams);
		$comandos->cutPaper();

		if (!$printerParams['impressaoFront']) {
			try {
				$issaas = $this->instanceManager->getParameter('ISSAAS');
			} catch (\Throwable $th) {
				$issaas = false;
			}
			if($issaas){
				$dadosImpressao = [];
				$dadosImpressao['paramsImpressora'] = array(
					'saas'      => true,
					'impressora'=> $valImpreLoja,
					'comandos'  => $comandos->getCommands(),
					'error'     => false);
			} else{
				$result = $this->impressaoUtilAPI->requisicaoPonte($valImpreLoja, $comandos);
			}
		} else {
			$dadosImpressao = $strImprimir;
		}

	}

    public function printPersonalCreditVoucher($CDFILIAL, $CDCAIXA, $NRDEPOSICONS, $creditDetails, $TIPORECE, $TROCO){
        $result = array();
        $dadosImpressora = $this->impressaoUtilAPI->getDadosImpressora($CDFILIAL, $CDCAIXA);
        if (!$dadosImpressora['error']){
            $dadosImpressora = $dadosImpressora['dadosImpressora'];
            $printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
            $printerParams['largura'] = $printerParams['larguraCupom'];

            $receipt = "";

            $filialDetails = $this->getFilialDetails($CDFILIAL);
            if (!empty($filialDetails)){
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, strtoupper($filialDetails['NMRAZSOCFILI'])) . $printerParams['comandoEnter'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, strtoupper($filialDetails['DSENDEFILI'])) . $printerParams['comandoEnter'];
                $location = $filialDetails['NMBAIRFILI'] . ' - ' . $filialDetails['NMMUNICIPIO'] . ' - ' . $filialDetails['SGESTADO'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, strtoupper($location)) . $printerParams['comandoEnter'];
                $CNPJ = substr($filialDetails['NRINSJURFILI'], 0, 2) . '.' . substr($filialDetails['NRINSJURFILI'], 2, 3) . '.' . substr($filialDetails['NRINSJURFILI'], 5, 3) . '/' . substr($filialDetails['NRINSJURFILI'], 8, 4) . '-' .substr($filialDetails['NRINSJURFILI'], 12, 2);
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'CNPJ: ' . $CNPJ) . $printerParams['comandoEnter'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'IE: ' . $filialDetails['CDINSCESTA']) . $printerParams['comandoEnter'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'IM: ' . $filialDetails['CDINSCMUNI']) . $printerParams['comandoEnter'];
            }

            $receipt .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $receipt);
            $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'COMPRA DE CREDITO') . $printerParams['comandoEnter'];
            $data = new \DateTime('NOW');
            $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'DATA: ' . $data->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
            $receipt .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $receipt);

            $receipt .= 'Nr. Deposito: ' . $NRDEPOSICONS . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
            $receipt .= 'Consumidor..: ' . $creditDetails['NMCONSUMIDOR'] . $printerParams['comandoEnter'];
            $receipt .= 'Familia.....: ' . $creditDetails['NMFAMILISALD'] . $printerParams['comandoEnter'];

            foreach ($TIPORECE as $recebimento){
                if (sizeof($TIPORECE) > 1) $receipt .= $printerParams['comandoEnter'];
                $receipt .= $recebimento['DSBUTTON'] . $printerParams['comandoEnter'];
                $receipt .= 'Valor.......: R$ ' . $this->impressaoUtilAPI->formataNumero(floatval($recebimento['VRMOVIVEND']), 2) . $printerParams['comandoEnter'];
                if ($recebimento['IDTIPORECE'] == '4' && $TROCO > 0){
                    $receipt .= 'Troco.......: R$ ' . $this->impressaoUtilAPI->formataNumero(floatval($TROCO), 2) . $printerParams['comandoEnter'];
                    if (sizeof($TIPORECE) > 1) $receipt .= $printerParams['comandoEnter'];
                }
            }

            if (sizeof($TIPORECE) > 1){
                $receipt .= 'TOTAL.......: R$ ' . $this->impressaoUtilAPI->formataNumero(floatval($creditDetails['VRSALDCONFAM']), 2) . $printerParams['comandoEnter'];
            }
            $receipt .= 'Saldo Final : R$ ' . $this->impressaoUtilAPI->formataNumero(floatval($creditDetails['VRSALDCONEXT']), 2) . $printerParams['comandoEnter'];

            $receipt .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $receipt);

            if (!$printerParams['impressaoFront']){
                $comandos = new Command();
                $comandos->text($receipt);
                $comandos->cutPaper();
                $comandos->text($receipt);
                $comandos->cutPaper();

                $respostaPonte = $this->impressaoUtilAPI->requisicaoPonte($dadosImpressora, $comandos);
                if ($respostaPonte['error']){
                    $result['error'] = true;
                    $result['message'] = $respostaPonte['message'];
                }
                else {
                    $result['error'] = false;
                    $result['message'] = null;
                }
            }
            else {
                $result['error'] = false;
                $result['message'] = array('RECEIPT' => $receipt);
            }
        }
        else {
            $result['error'] = true;
            $result['message'] = "Caixa sem impressora cadastrada.";
        }

        return $result;
    }

    public function printCancelCreditVoucher($CDFILIAL, $CDCAIXA, $NRDEPOSICONS, $cancelDetails, $VRMOVEXTCONS, $NMTIPORECE){
        $result = array();
        $dadosImpressora = $this->impressaoUtilAPI->getDadosImpressora($CDFILIAL, $CDCAIXA);
        if (!$dadosImpressora['error']){
            $dadosImpressora = $dadosImpressora['dadosImpressora'];
            $printerParams = $this->impressaoUtilAPI->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
            $printerParams['largura'] = $printerParams['larguraCupom'];

            $receipt = "";

            $filialDetails = $this->getFilialDetails($CDFILIAL);
            if (!empty($filialDetails)){
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, strtoupper($filialDetails['NMRAZSOCFILI'])) . $printerParams['comandoEnter'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, strtoupper($filialDetails['DSENDEFILI'])) . $printerParams['comandoEnter'];
                $location = $filialDetails['NMBAIRFILI'] . ' - ' . $filialDetails['NMMUNICIPIO'] . ' - ' . $filialDetails['SGESTADO'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, strtoupper($location)) . $printerParams['comandoEnter'];
                $CNPJ = substr($filialDetails['NRINSJURFILI'], 0, 2) . '.' . substr($filialDetails['NRINSJURFILI'], 2, 3) . '.' . substr($filialDetails['NRINSJURFILI'], 5, 3) . '/' . substr($filialDetails['NRINSJURFILI'], 8, 4) . '-' .substr($filialDetails['NRINSJURFILI'], 12, 2);
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'CNPJ: ' . $CNPJ) . $printerParams['comandoEnter'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'IE: ' . $filialDetails['CDINSCESTA']) . $printerParams['comandoEnter'];
                $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'IM: ' . $filialDetails['CDINSCMUNI']) . $printerParams['comandoEnter'];
            }

            $receipt .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $receipt);
            $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'CANCELAMENTO DE CREDITO') . $printerParams['comandoEnter'];
            $data = new \DateTime('NOW');
            $receipt .= $this->impressaoUtilAPI->centraliza($printerParams, 'DATA: ' . $data->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
            $receipt .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $receipt);

            $receipt .= 'Nr. Deposito: ' . $NRDEPOSICONS . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
            $receipt .= 'Consumidor..: ' . $cancelDetails['NMCONSUMIDOR'] . $printerParams['comandoEnter'];
            $receipt .= 'Familia.....: ' . $cancelDetails['NMFAMILISALD'] . $printerParams['comandoEnter'];
            $receipt .= 'Valor.......: R$ ' . $this->impressaoUtilAPI->formataNumero(floatval($VRMOVEXTCONS), 2) . $printerParams['comandoEnter'];
            $receipt .= 'Saldo Final : R$ ' . $this->impressaoUtilAPI->formataNumero(floatval($cancelDetails['VRSALDCONEXT']), 2) . $printerParams['comandoEnter'];

            $receipt .= $this->impressaoUtilAPI->imprimeLinha($printerParams, $receipt);

            if (!$printerParams['impressaoFront']){
                $comandos = new Command();
                $comandos->text($receipt);
                $comandos->cutPaper();
                $comandos->text($receipt);
                $comandos->cutPaper();

                $respostaPonte = $this->impressaoUtilAPI->requisicaoPonte($dadosImpressora, $comandos);
                if ($respostaPonte['error']){
                    $result['error'] = true;
                    $result['message'] = $respostaPonte['message'];
                }
                else {
                    $result['error'] = false;
                    $result['message'] = null;
                }
            }
            else {
                $result['error'] = false;
                $result['message'] = array('RECEIPT' => $receipt);
            }
        }
        else {
            $result['error'] = true;
            $result['message'] = "Caixa sem impressora cadastrada.";
        }

        return $result;
    }

    private function getFilialDetails($CDFILIAL){
        return $this->entityManager->getConnection()->fetchAssoc("SQL_GET_FILIAL_DETAILS", array('CDFILIAL' => $CDFILIAL));
    }

	private function buscaNomePorPosicao($cdfilial, $nrvendarest, $posicoes){
        $params = array(
            'CDFILIAL' => $cdfilial,
            'NRVENDAREST' => $nrvendarest,
            'NRLUGARMESA' => $posicoes
        );
        $type = array(
            'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
        );
        $nmPosicao = $this->entityManager->getConnection()->fetchAll("BUSCA_NOME_POR_POSICAO", $params, $type);
        return array_column($nmPosicao, null, 'NRLUGARMESA');
	}

}