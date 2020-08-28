<?php
namespace Odhen\API\Service;

use Odhen\API\Util\Exception;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Standardize;
use NFePHP\Common\Certificate;
use NFePHP\Common\Strings;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\NFe\Complements;

class NotaFiscal {

    const AMB_EMISSAO_ONLINE = '1';
    const AMB_EMISSAO_OFFLINE = '9';
    const MODELO_DOC = '65'; // 55 - NFe; 65 - NFC-e
    const VERSAO_LAYOUT = '4.00';

    const APROVADA = 'O';
    const ENTRADA = 'E';
    const SAIDA = 'S';
    const PRODUTO_NACIONAL = 'N';

	protected $entityManager;
	protected $util;
	protected $databaseUtil;
	protected $impressaoNFCE;
	protected $instanceManager;
	protected $systemPath;

    public function __construct(
    	\Doctrine\ORM\EntityManager $entityManager,
		\Odhen\API\Util\Util $util,
		\Odhen\API\Util\Database $databaseUtil,
		\Odhen\API\Service\ImpressaoNFCE $impressaoNFCE,
		\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager) {

	   	$this->entityManager   = $entityManager;
	   	$this->util 		   = $util;
	   	$this->databaseUtil    = $databaseUtil;
		$this->impressaoNFCE   = $impressaoNFCE;
		$this->instanceManager = $instanceManager;
		$this->systemPath      = $this->instanceManager->getParameter('SYSTEM_PATH');
		$this->nfcePath        = $this->instanceManager->getParameter('NFCE_PATH');
		$this->PROXY_IP        = $this->instanceManager->getParameter('PROXY_IP');
		$this->PROXY_PORT      = $this->instanceManager->getParameter('PROXY_PORT');
		$this->PROXY_USER      = $this->instanceManager->getParameter('PROXY_USER');
		$this->PROXY_PASSWORD  = $this->instanceManager->getParameter('PROXY_PASSWORD');
	}

	private function validaDadosEmpresa($cdfilial, $nrorg){
	    $getEmpresaParams = array(
	        'CDFILIAL' => $cdfilial,
	        'NRORG'    => $nrorg
    	);
		$result = $this->entityManager->getConnection()->fetchAll("GET_EMPRESAFILIAL",$getEmpresaParams);
		$arrayResult = array();
		if(isset($result[0])){
			$arrayResult['error'] = false;
			$arrayResult['dadosEmpresa'] = $result[0];
		} else {
			$arrayResult['error'] = true;
			$arrayResult['message'] = "Não há dados para a filial informada";
		}
		return $arrayResult;
	}

    public function validaEnderecoFilial($cdfilial, $nrorg){

 		$getEmpresaParams = array(
	        'CDFILIAL' => $cdfilial,
	        'NRORG'    => $nrorg
    	);
    	$result = $this->entityManager->getConnection()->fetchAll("GET_ENDEFILI",$getEmpresaParams);
    	$arrayResult = array();
		if(isset($result[0])){
			$arrayResult['error'] = false;
			$arrayResult['dadosEndereco'] = $result[0];
		} else {
			$arrayResult['error'] = true;
			$arrayResult['message'] = "Não há endereco para a filial logada";
		}
		return $arrayResult;
    }

    public function validaDadosEmitenteXML($cdfilial, $nrorg){
		$dadosEmitenteParams = array(
	        'CDFILIAL' => $cdfilial,
			'NRORG'    => $nrorg
	  	);
    	$result = $this->entityManager->getConnection()->fetchAssoc("GET_DADOSEMITENTE_IDE_XML",$dadosEmitenteParams);
    	$arrayResult = array();
		if(!empty($result)){
			$arrayResult['error'] = false;
			$arrayResult['dadosEmitente'] = $result;
		} else {
			$arrayResult['error'] = true;
			$arrayResult['message'] = "Não há dados de emitente da filial.";
		}
		return $arrayResult;
    }

    private function validaSerieNFCE($filialInfo){

    	$serieNfceParams = array(
	        'CDFILIAL' => $filialInfo['CDFILIAL'],
			'CDCAIXA'  => $filialInfo['CDCAIXA'],
			'NRORG'    => $filialInfo['NRORG']
		);
		$result = $this->entityManager->getConnection()->fetchAll("GET_SERIE_NFCE",$serieNfceParams);

		$arrayResult = array();
		if(isset($result[0])){
			$arrayResult['error'] = false;
			$arrayResult['dadosSerie'] = $result[0];
		} else {
			$arrayResult['error'] = true;
			$arrayResult['message'] = "Não há serie NFCE no caixa logado.";
		}
		return $arrayResult;
    }

    private function montaArrayFilial($dadosEmitente, $dadosEmpresaResult, $endeFiliResult, $nrorg, $cdcaixa, $cdfilial, $cdloja, $cdoperador, $nrmesa){
    	$filialInfo = array(
            'CDPRODTAXSER' 		=> $dadosEmitente['CDPRODTAXSER'],
    		'NMRAZSOCFILI' 		=> $dadosEmitente['NMRAZSOCFILI'],
		    'NMFILIAL' 			=> $dadosEmitente['NMFILIAL'],
		    'NRINSJURFILI' 		=> preg_replace('/[^0-9]/', '', $dadosEmitente['NRINSJURFILI']),
		    'CDESTADOIBGE' 		=> $dadosEmitente['CDESTADOIBGE'],
		    'CDMUNICIBGE' 		=> strlen($dadosEmitente['CDMUNICIBGE']) != 5 ? $dadosEmitente['CDMUNICIBGE'] : $dadosEmitente['CDESTADOIBGE'] . $dadosEmitente['CDMUNICIBGE'],
		    'CDINSCESTAFILI' 	=> $dadosEmitente['CDINSCESTA'],
		    'CDINSCMUNIFILI' 	=> $dadosEmitente['CDINSCMUNI'],
		    'DSENDEFILI' 		=> $dadosEmitente['DSENDEFILI'],
		    'NRENDEFILISEP'		=> $dadosEmitente['NRENDEFILISEP'] ? $dadosEmitente['NRENDEFILISEP'] : '.',
		    'NMBAIRFILI' 		=> $dadosEmitente['NMBAIRFILI'],
		    'NMMUNICIPIO' 		=> $dadosEmitente['NMMUNICIPIO'],
		    'SGESTADOFILI' 		=> $dadosEmitente['SGESTADO'],
		    'NRCEPFILI' 		=> str_replace('-','',$dadosEmitente['NRCEPFILI']),
		    'CDPAISBACEN' 		=> $dadosEmitente['CDPAISBACEN'],
		    'NMPAIS' 			=> $dadosEmitente['NMPAIS'],
		    'CDCNAE' 			=> $dadosEmitente['CDCNAE'],
		    'CDSITUCRT'  		=> $dadosEmitente['CDSITUCRT'],
		    'CDEMPRESA' 		=> $dadosEmpresaResult['CDEMPRESA'],
		    'CDINSCESTA' 		=> $dadosEmpresaResult['CDINSCESTA'],
		    'CDPAIS' 			=> $endeFiliResult['CDPAIS'],
		    'SGESTADO' 			=> $endeFiliResult['SGESTADO'],
			'CDMUNICIPIO' 		=> $endeFiliResult['CDMUNICIPIO'],
			'CDFILIAL' 			=> $cdfilial,
			'CDCAIXA' 			=> $cdcaixa,
			'NRORG' 			=> $nrorg,
			'CDLOJA' 			=> $cdloja,
			'CDOPERADOR' 		=> $cdoperador,
			'CDURLWSNFC' 		=> $dadosEmitente['CDURLWSNFC'],
			'NRMESA' 			=> $nrmesa,
			'CDPRODTAXAENTR' 	=> $dadosEmitente['CDPRODTAXAENTR']
    	);
    	return $filialInfo;
    }

    private function montaArrayInfoNFCE($dadosEmitente, $nrseqvenda, $serieNFE, $filialInfo, $connection, $VRTOTTRIBIBPT, $TOTALVENDA, $IDAMBTRABNFCE){
		// define informações do certificado para produção/homologação
		if ($dadosEmitente['IDAMBTRABNFCE'] == '1'){
			$CSC =  $dadosEmitente['CDCODSCONSPROD'];
			$CSCid = $dadosEmitente['CDIDTOKENPROD'];
		} else {
			$CSC = $dadosEmitente['CDCODSCONSHOMO'];
			$CSCid = $dadosEmitente['CDIDTOKENHOMO'];
		}
		
	    $serieCH = str_pad($serieNFE,3,"0",STR_PAD_LEFT);
        $randomNrNFE = str_pad(mt_rand(1, 99999999), 8, "9", STR_PAD_LEFT);
	    $nrNFE = self::gerarNumeroNota($filialInfo, $serieCH, $connection, $IDAMBTRABNFCE);
		$nrAcessoNFCE = self::gerarChaveAcessoNFCe($filialInfo, $serieCH, $nrNFE, self::AMB_EMISSAO_ONLINE, $randomNrNFE);
		$nfceInfo = array(
			'CSC' => $CSC,
			'CSCid' => $CSCid,
		    'NMARQCERTNFCE' => $dadosEmitente['NMARQCERTNFCE'],
		    'DSSENHACERTNFCE' => $dadosEmitente['DSSENHACERTNFCE'],
			'tpEmis' => self::AMB_EMISSAO_ONLINE,
			'nrNFE' => $nrNFE,
			'nrNFEInt' => intval($nrNFE),
			'randomNrNFE' => $randomNrNFE,
			'serieCH' => $serieCH,
			'cDV' => substr($nrAcessoNFCE, -1),
			'nrAcessoNFCE' => $nrAcessoNFCE,
			'VRTOTTRIBIBPT' => $VRTOTTRIBIBPT,
			'nrseqvenda' => $nrseqvenda,
			'totalVenda' => $TOTALVENDA,
			'tpAmb' => $IDAMBTRABNFCE
		);

		return $nfceInfo;
	}

	private function gerarNumeroNota($filialInfo, $serieCH, $connection, $IDAMBTRABNFCE){
		$cdContadorNFE = 'NFCE_' . $filialInfo['CDFILIAL'] . $serieCH . '_AMB' . $IDAMBTRABNFCE;

	    return $this->util->geraCodigo($connection, $cdContadorNFE, $filialInfo['NRORG'], 1, 9);
	}

	private function gerarChaveAcessoNFCe($filialInfo, $serieCH, $nrNFE, $AMBEMISSAO, $randomNrNFE){
		$nrAcessoNFCE = $filialInfo['CDESTADOIBGE'] . date('ym') . $filialInfo['NRINSJURFILI'] .
			self::MODELO_DOC . $serieCH . $nrNFE . $AMBEMISSAO . $randomNrNFE;

		return $nrAcessoNFCE . self::calculaDigitoVerificadorNFe($nrAcessoNFCE);
	}

	public function validateDataNfce($nfceData){
		$result = array(
			'error' => false,
			'message' => ''
		);

		$nfceParams = self::associateCodeToMeaning();
		$missingParameters = array();
		foreach ($nfceParams as $param => $message) {
			if (empty($nfceData[$param])){
				array_push($missingParameters, $message);
			}
		}

		if (!empty($missingParameters)){
			$result['error'] = true;
			$result['message'] = 'Não foi possível utilizar o NFCE. ' . $this->nfceMissingParameterMessage($missingParameters);
		}

		return $result;
	}

	private function getSerieNFE ($serieNFCE){
		if(!$serieNFCE['dadosSerie']['CDSERIECX']){
	    	$serieNFE = intval($serieNFCE['dadosSerie']['CDCAIXA']);
	    } else {
	    	$serieNFE = intval($serieNFCE['dadosSerie']['CDSERIECX']);
	    }
	    return $serieNFE;
	}

	private function montaArraySale($ideArray, $emitArray, $totalArray, $transpArray, $infAdicArray, $paymentArray, $IDAMBTRABNFCE, $infoConsumer, $infRespTec) {
		$saleArray = array();
		$saleArray['ide'] = $ideArray;
		$saleArray['emit'] = $emitArray;
		if (!empty($infoConsumer['NRINSCRCONS'])){
	        $destArray = self::montaArrayDest($IDAMBTRABNFCE, $infoConsumer);
	        $saleArray['dest'] = $destArray;
		}
		$indexItemAprov = 0;

		foreach ($totalArray['toSaleArray'] as $item) {
			$indexItemAprov++;
			$nItem = "nItem+".$indexItemAprov;
			$saleArray['det+'.$nItem] = $item;
		}
		unset($totalArray['toSaleArray']);
		$saleArray['total'] = $totalArray;
		$saleArray['transp'] = $transpArray;
		$saleArray['pag'] = $paymentArray;
		$saleArray['infAdic'] = $infAdicArray;
		$saleArray['infRespTec'] = $infRespTec;
		return $saleArray;
	}

	private function montaArrayPayments($filialInfo, $dtabercaix, $nrseqvenda){
		$paymentArray = array();
		$vTroco = null;

		$params = array(
			'CDFILIAL' => $filialInfo['CDFILIAL'],
			'CDCAIXA' => $filialInfo['CDCAIXA'],
			'NRSEQVENDA' => $nrseqvenda
		);
		$paymentModes = $this->entityManager->getConnection()->fetchAll("GET_PAYMENTS", $params);
		$idpag = 1;
		foreach ($paymentModes as $paymentMode) {
			if ($paymentMode['IDTIPOMOVIVE'] != 'S'){
				$paymentArray['detPag+nItem+' . $idpag] = self::getPayment($paymentMode, $filialInfo['CDFILIAL'], $filialInfo['CDCAIXA'], $dtabercaix, $nrseqvenda, $filialInfo['NRORG'], $filialInfo['CDOPERADOR']);
				$idpag++;
			} else {
				$vTroco = $paymentMode;
			}
		}
		$paymentArray['vTroco'] = !empty($vTroco) ? $this->numberFormat($vTroco['VRMOVIVEND'], 2, '.', '') : '0.00';

		return $paymentArray;
	}

	private function iniciaTransmissao($saleArray, $nfceInfo, $nfeTools){
		$XMLASSINADO = self::gerarXMLValidado($saleArray, $nfeTools, $nfceInfo['nrAcessoNFCE'], false);
		$respostaEnvio = self::transmitirXML($nfeTools,$XMLASSINADO);
		return self::validarTransmissao($respostaEnvio);
	}

	private function validarTransmissao($respostaEnvio){
		$resultadoTransmissao = array(
			'XMLENVIO' => $respostaEnvio['XMLENVIO'],
			'XMLRETORNO' => $respostaEnvio['XMLRETORNO'],
			'IDSTATUSNFCE' => '',
			'DTHRPROTOCONFCE' => null,
			'NRPROTOCOLONFCE' => null,
			'message' => ''
		);
		if (!$respostaEnvio['error']){
			$respostaEnvio = $respostaEnvio['data'];

			if ($respostaEnvio['cStat'] == '104'){
				$respostaEnvio = $respostaEnvio['protNFe']['infProt'];

				$resultadoTransmissao['IDSTATUSNFCE'] = self::getIDSTATUSNFCE($respostaEnvio['cStat']);
				$resultadoTransmissao['DTHRPROTOCONFCE'] = new \DateTime($respostaEnvio['dhRecbto']);
				$resultadoTransmissao['NRPROTOCOLONFCE'] = !empty($respostaEnvio['nProt']) ? $respostaEnvio['nProt'] : null;
			} else {
				$resultadoTransmissao['IDSTATUSNFCE'] = 'R';
			}
			$resultadoTransmissao['message'] = $respostaEnvio['xMotivo'];
		} else {
			$resultadoTransmissao['IDSTATUSNFCE'] = 'P';
            $respostaEnvio['cStat'] = 'P';
			$resultadoTransmissao['message'] = $respostaEnvio['message'];
		}
		$resultadoTransmissao['message'] = $respostaEnvio['cStat'] . ' - ' . $resultadoTransmissao['message'];

		return $resultadoTransmissao;
	}

	private function saveAuthorizeXML($resultadoTransmissao, $nrAcessoNFCE, $authorizePath){
		$result = array(
			'error' => false,
			'message' => ''
		);

        // try {
        //     // junta XML assinado com retorno da SEFAZ
        //     $authorizeXML = Complements::toAuthorize($resultadoTransmissao['XMLENVIO'], $resultadoTransmissao['XMLRETORNO']);
        //     // salva no destino previamente definido
        //     $fpXML = fopen($authorizePath . $nrAcessoNFCE . ".xml", "w");
        //     fwrite($fpXML, $authorizeXML);
        //     fclose($fpXML);
        // } catch (\Exception $e) {
        //     Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
        //     $result['error'] = true;
        //     $result['message'] = '<br> Erro ao salvar XML: ' . $e->getMessage();
        // }

		return $result;
	}

	private function salvaXML($text) {
        //testa se a pasta existe e cria caso não exista
        $folder = $this->systemPath . "NFCE/LOGS/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        //--grava o log
        $line = date("d-m-Y H:i:s") . " - " . $text . "\n";
        $date = date("dmY");
        file_put_contents($this->systemPath . "NFCE/LOGS/error" . $date . ".txt", $line, FILE_APPEND);
    }

    private function getPermiteContingencia($filialInfo) {
        return $filialInfo['SGESTADOFILI'] != 'SP';
	}

	public function transmitirNFCe($nrorg, $cdcaixa, $cdfilial, $dtabercaix, $cdloja, $cdoperador, $idtpemisvend, $nrseqvenda, $VRTOTTRIBIBPT, $TOTALVENDA, $simulaImpressao, $FIDELITYVALUE, $nrmesa) {
		$connection		= $this->entityManager->getConnection();
	    $idimpvenda 	= 'I';
	    // 1-> Producao, 2 -> Homologação
		$cdtipooper = '66';
		$dadosEmpresaResult = self::validaDadosEmpresa($cdfilial, $nrorg);
        if($dadosEmpresaResult['error'] == false){
            $endeFiliResult = self::validaEnderecoFilial($cdfilial, $nrorg);
            if($endeFiliResult['error'] == false){
                $dadosEmitente = self::validaDadosEmitenteXML($cdfilial, $nrorg);
                if($dadosEmitente['error'] == false){
                    $dadosEmitente = $dadosEmitente['dadosEmitente'];
            		$IDAMBTRABNFCE = $dadosEmitente['IDAMBTRABNFCE'];
				    $filialInfo = self::montaArrayFilial($dadosEmitente, $dadosEmpresaResult['dadosEmpresa'], $endeFiliResult['dadosEndereco'], $nrorg, $cdcaixa, $cdfilial, $cdloja, $cdoperador, $nrmesa);
					$emitArray = self::montaArrayEmit($filialInfo);
				    $serieNFCE = self::validaSerieNFCE($filialInfo);
  					if($serieNFCE['error'] == false){
					    $serieNFE = self::getSerieNFE($serieNFCE);
				    	$nfceInfo = self::montaArrayInfoNFCE($dadosEmitente, $nrseqvenda, $serieNFE, $filialInfo, $connection, $VRTOTTRIBIBPT, $TOTALVENDA, $IDAMBTRABNFCE);
				    	$validateNfce = self::validateDataNfce(array_merge($nfceInfo, $filialInfo));
				    	if (!$validateNfce['error']){
							$productsNFCE = $this->getInfoProducts($filialInfo, $nfceInfo);
							$infoConsumer = $this->getInfoConsumer($filialInfo, $nfceInfo);
							$DTEMISSAO = new \DateTime();
							self::createPath($IDAMBTRABNFCE, $filialInfo, $DTEMISSAO);
					        $ideArray = self::montaArrayIde($filialInfo, $nfceInfo, $serieNFE, $DTEMISSAO->format('c'), $IDAMBTRABNFCE);
							$totalArray = self::montaArrayTotal($productsNFCE, $filialInfo, $nrseqvenda, $cdtipooper, $IDAMBTRABNFCE, $VRTOTTRIBIBPT);
							$transpArray = self::montaArrayTransp();
							$infAdicArray = self::montaArrayInfAdic($nrseqvenda);
							$infRespTec = self::montaArrayInfRespTec();
							$paymentArray = self::montaArrayPayments($filialInfo, $dtabercaix, $nrseqvenda);
							$saleArray = self::montaArraySale($ideArray, $emitArray, $totalArray, $transpArray, $infAdicArray, $paymentArray, $IDAMBTRABNFCE, $infoConsumer, $infRespTec);
	                        $nfeTools = self::getNfeTools(array_merge($nfceInfo, $filialInfo));
	                        $resultadoTransmissao = self::iniciaTransmissao($saleArray, $nfceInfo, $nfeTools);
	                        $nfceInfo['IDSTATUSNFCE'] = $resultadoTransmissao['IDSTATUSNFCE'];
	                        $nfceInfo['DTHRPROTOCONFCE'] = $resultadoTransmissao['DTHRPROTOCONFCE'];

	                        $deveCancelarVenda = false;
	                        if ($resultadoTransmissao['IDSTATUSNFCE'] != 'A') {
	                        	if ($this->getPermiteContingencia($filialInfo)) {
	                        		self::alteraDadosParaContingencia($saleArray, $nfceInfo, $filialInfo);
	                        		$XMLASSINADO = self::gerarXMLValidado($saleArray, $nfeTools, $nfceInfo['nrAcessoNFCE'], true);
	                            	$resultadoTransmissao['XMLENVIO'] = $XMLASSINADO;
	                        	} else {
	                        		$deveCancelarVenda = true;
	                        	}
	                        } else {
	                        	// salva XML de acordo com a legislação
	                        	$saveResult = self::saveAuthorizeXML($resultadoTransmissao, $nfceInfo['nrAcessoNFCE'], ENVIADAS_XML_PATH);
	                        	if ($saveResult['error']){
	                        		$resultadoTransmissao['message'] .= $saveResult['message'];
	                        	}
	                        }

	                        $resultadoTransmissao['QRCODENFCE'] = self::getQrCode($resultadoTransmissao['XMLENVIO']);
							self::updateVendaNFCE($nfceInfo, $filialInfo, $nrseqvenda, $resultadoTransmissao, $DTEMISSAO, $deveCancelarVenda);
	                        if ($deveCancelarVenda) {
	                        	$result = array(
	                                'error' => true,
	                                'message' => 'Nota rejeitada pela SEFAZ.',
	                                'IDSTATUSNFCE' => $resultadoTransmissao['IDSTATUSNFCE'],
	                                'dadosImpressao' => array()
	                            );
	                        } else {
	                            if ($simulaImpressao) {
									$resultadoImpressao = array(
										'error' => false,
										'dadosImpressao' => array()
									);
								} else {
									$resultadoImpressao = $this->impressaoNFCE->imprimeDanfeNFCE($nfceInfo, $filialInfo, $productsNFCE, $infoConsumer, true, $FIDELITYVALUE);
								}

								$result = array(
									'error'=> false,
									'DSQRCODE' => $resultadoTransmissao['QRCODENFCE'],
									'dadosImpressao' => array(),
									'IDSTATUSNFCE' => $resultadoTransmissao['IDSTATUSNFCE'],
									'mensagemNfce' => $resultadoTransmissao['message'],
									'mensagemImpressao' => ''
								);

								if ($resultadoImpressao['error']) {
									$result['mensagemImpressao'] = $resultadoImpressao['message'];
								} else {
									$result['dadosImpressao'] = $resultadoImpressao['dadosImpressao'];
                                }
								$result['paramsImpressora'] = isset($resultadoImpressao['paramsImpressora']) ? $resultadoImpressao['paramsImpressora'] : array();
								$result['errPainelSenha'] = !empty($resultadoImpressao['errPainelSenha']) ? $resultadoImpressao['errPainelSenha'] : '';
							}
							return $result;
				    	} else {
				    		return $validateNfce;
				    	}
  					} else {
						return $serieNFCE;
					}
  				} else {
					return $dadosEmitente;
				}
	    	} else {
				return $endeFiliResult;
			}
		} else {
			return $dadosEmpresaResult;
		}
	}

    private static function calculaDigitoVerificadorNFe($chave){

        $nAlgarismo = 42;
        $multiplicadores = array(2,3,4,5,6,7,8,9);
        $nMultiplicador = 0;
        $soma = 0;
        $DV = 0;

        while($nAlgarismo >= 0){
            $algarismo = substr($chave, $nAlgarismo , 1);
            $soma += $algarismo*$multiplicadores[$nMultiplicador];
            if($nMultiplicador == 7){
                $nMultiplicador = 0;
            }else{
                $nMultiplicador++;
            }
            $nAlgarismo--;
        }
        $restoDivisao = $soma % 11;
        if($restoDivisao > 1){
            $DV = 11 - $restoDivisao;
        }

        return $DV;

    }

	private function gerarXMLValidado($saleArray, $nfeTools, $nrAcessoNFCE, $contingencia){
        $unsignedXML = self::gerarXML($saleArray,$nrAcessoNFCE, $contingencia);
        $XMLASSINADO = self::assinarXML($unsignedXML,$nrAcessoNFCE,$nfeTools);

   		return $XMLASSINADO;
	}

	private function alteraDadosParaContingencia(&$saleArray, &$nfceInfo, $filialInfo){
		$nrAcessoNFCE = self::gerarChaveAcessoNFCe($filialInfo, $nfceInfo['serieCH'], $nfceInfo['nrNFE'], self::AMB_EMISSAO_OFFLINE, $nfceInfo['randomNrNFE']);

		$nfceInfo['nrAcessoNFCE'] = $nrAcessoNFCE;
		$nfceInfo['randomNrNFE'] = date('d') . $nfceInfo['randomNrNFE']; // VND necessita dos 10 digitos para retransmissão
		$saleArray['ide']['cDV'] = substr($nrAcessoNFCE, -1);
		$saleArray['ide']['tpEmis'] = self::AMB_EMISSAO_OFFLINE;
	}

	private function getQrCode($signedXML){
		$begin = strpos($signedXML, '<qrCode>');
		if ($begin) {
			$beginStr = substr($signedXML, $begin + 8);
			$end = strpos($beginStr, '</qrCode>');
			$qrCode = substr($beginStr, 0, $end);
		} else {
			$qrCode = "QRCode inexistente.";
		}
		return $qrCode;
	}

	private function trataMascaraImposto($valor) {
		$valor = str_replace(' ', '', $valor);
		$valor = str_replace(',', '.', $valor);
		return floatval($valor);
	}

	private function getSaleItem($cdfilial, $cdloja, $cdcaixa, $nrorg, $nrseqvenda, $nrsequitvend, $cdempresa, $cdpais, $sgestado, $cdmunicipio, $cdinscesta, $item, $cdtipooper, $CDPRODTAXSER, $VRTOTTRIBIBPTporItem, $CDPRODTAXAENTR) {

		$item_array =  self::montaArray_item();
		$cdproduto = $item['CDPRODUTO'];

		$qtprodvend = str_replace(',', '.', $item['QTPRODVEND']);
		$vrunitvend = (string) (floatval(str_replace(',', '.', $item['VRUNITVEND'])) + floatval(str_replace(',', '.', $item['VRUNITVENDCL'])));
		$nmproduto =  $item['NMPRODUTO'];

		//---- TEMPORARIO
   		$vracritvend = isset($item['VRTOTACRE']) ? $item['VRTOTACRE'] : 0;
		$vracritvend = floatval(str_replace(',', '.', $vracritvend));
   		//----
		$vrdesitvend = isset($item['VRTOTDESC']) ? $item['VRTOTDESC'] : 0;
		$vrdesitvend = floatval(str_replace(',', '.', $vrdesitvend));

        if ($cdproduto === $CDPRODTAXSER) {
            $aux = $vrunitvend;
            $vrunitvend = $vracritvend;
            $vracritvend = $aux;
        }

        if($cdproduto === $CDPRODTAXAENTR){
        	$aux = $vrunitvend;
            $vrunitvend = $vracritvend;
            $vracritvend = $aux;
        }

	  	$dadosProdImpParams = self::buildParamsProdImp($nrseqvenda,$nrsequitvend, $cdfilial, $cdcaixa, $cdproduto, $nrorg);
	  	$dadosProd = $this->entityManager->getConnection()->fetchAll("GET_DADOSPRODIMP_XML",$dadosProdImpParams);

	  	if (count($dadosProd) != 0) {
	  		$dadosProd[0]['VRALIQPIS'] = $this->trataMascaraImposto($dadosProd[0]['VRALIQPIS']);

		  	$sgunidade = $dadosProd[0]['SGUNIDADE'];
		  	$cdclasfisc = $dadosProd[0]['CDCLASFISC'];
			$cdarvprod = $dadosProd[0]['CDARVPROD'];
			// --- Dados de impostos
			$cdimposto = $dadosProd[0]['CDIMPOSTO'];
			$cstImp = $dadosProd[0]['CDCSTPRODI'];
			$cstPisCof = $dadosProd[0]['CDCSTPRODPC'];
			$vraliquotaImp = $dadosProd[0]['VRPEALPRODIT'];
			$vraliquotaPis = $dadosProd[0]['VRALIQPIS'];
			$vraliquotaCof = $dadosProd[0]['VRPERCOFINS'];
			$vrbasecalcicms = $dadosProd[0]['VRBASECALCICMS'];
			$vrbasecalcreduz = $dadosProd[0]['VRBASECALCREDUZ'];
			$cdCest = str_replace('.','',$dadosProd[0]['CDCEST']);
			$pRed = $dadosProd[0]['VRPERCREDUCAO'];
			$vraliqFcp = $dadosProd[0]['VRALIQFCP'];
			$vrpercReducaoef = $dadosProd[0]['VRPERCREDUCAOEF'];
			$vrpealImpfisef = $dadosProd[0]['VRPEALIMPFISEF'];
			$idtpImposfis = $dadosProd[0]['IDTPIMPOSFIS'];
			$cfopImp = $dadosProd[0]['CDCFOPPFIS'];
			$CDCBENEF = $dadosProd[0]['CDCBENEF'];

		  	$vUnCom = $this->numberFormat($vrunitvend, 10, '.', '');

		  	$qCom = $this->numberFormat(floatval($qtprodvend),4);

		  	$item_array['prod']['cProd'] = $cdarvprod;
		  	$item_array['prod']['cEAN'] = 'SEM GTIN';
		  	$item_array['prod']['xProd'] = trim(preg_replace('/[^A-Za-z0-9 \-]/', '', $nmproduto));
		  	$item_array['prod']['NCM'] = $cdclasfisc;
		  	$item_array['prod']['uCom'] = $sgunidade;
		  	$item_array['prod']['vUnCom'] = $vUnCom;

		  	$item_array['prod']['vProd'] = $this->numberFormat(($vUnCom * $qtprodvend),2 , '.', '');
		  	$item_array['prod']['cEANTrib'] = 'SEM GTIN';
		  	$item_array['prod']['qCom'] = $qCom;
			if($vrdesitvend > 0){
		  		$item_array['prod']['vDesc'] = $this->numberFormat($vrdesitvend, 2, '.', '');
		  	}else{
		  		unset($item_array['prod']['vDesc']);
		  	}
		  	if($vracritvend > 0){
		  		$item_array['prod']['vOutro'] = $this->numberFormat($vracritvend, 2, '.', '');
		  	}else{
		  		unset($item_array['prod']['vOutro']);
		  	}
		  	$item_array['prod']['uTrib'] = $sgunidade;
	     	$item_array['prod']['qTrib'] = $qCom;
	     	$item_array['prod']['vUnTrib'] = $vUnCom;
	     	$item_array['prod']['indTot'] = 1;//-- Compõe o total da nota

	     	$baseCalcItVend = (floatval($qtprodvend) * floatval($vrunitvend)) - $vrdesitvend + $vracritvend;

			$regraImpostos = array();
			//---- TRATAMENTO DOS IMPOSTOS
			if(isset($item['IMPOSTOS'])){
				//--- impostos vindos do front
			}else{
				$orig = '0';
			   	if($cdimposto != null){
				   	$nrseqitimpos      = '001';
				   	$vrimpoprodit      = $vraliquotaImp * $baseCalcItVend;
				   	$modBC             = '3';

				   	$icms_array        = self::getICMSbyCST($cstImp, $orig, $modBC, $vraliquotaImp, $vrbasecalcicms, $pRed, $vraliqFcp, $sgestado, $baseCalcItVend, $vrpercReducaoef, $vrpealImpfisef, $idtpImposfis, floatval($vrbasecalcreduz));
				 	$pis_array 		   = self::getPISbyCST($cstPisCof, $orig, $modBC, $vraliquotaPis, $vrbasecalcicms);
				 	$cofins_array 	   = self::getCOFINSbyCST($cstPisCof, $orig, $modBC, $vraliquotaCof, $vrbasecalcicms);

				 	$tagICMS = $icms_array['TAG'];
				 	unset($icms_array['TAG']);

				 	$tagPIS = $pis_array['TAG'];
				 	unset($pis_array['TAG']);

				 	$tagCOFINS = $cofins_array['TAG'];
				 	unset($cofins_array['TAG']);

				 	$item_array['imposto']['vTotTrib'] = $VRTOTTRIBIBPTporItem;
				 	$item_array['imposto']['ICMS'][$tagICMS] = $icms_array;
				 	$item_array['imposto']['PIS'][$tagPIS] = $pis_array;
				 	$item_array['imposto']['COFINS'][$tagCOFINS] = $cofins_array;

					if(($cstImp == '10' || $cstImp == '30' || $cstImp == '60' || $cstImp == '70' || $cstImp == '90'
						|| $cstImp == '201' || $cstImp == '202' || $cstImp == '203' || $cstImp == '500' || $cstImp == '900')
						&& !empty($cdCest)){
				 		$item_array['prod']['CEST'] = $cdCest;
				 	}else{
				 		unset($item_array['prod']['CEST']);
				 	}
				 	// Código de Benefício Fiscal utilizado pela UF, aplicado ao item.
       				// Obs.: Deve ser utilizado o mesmo código adotado na EFD
       				// e outras declarações, nas UF que o exigem.
       				if (self::validaEstadoICMS($sgestado, $cstImp)){
		                if(isset($CDCBENEF) && $CDCBENEF != null){
	                		$item_array['prod']['cBenef'] = $CDCBENEF;
	                	} else {
	                		unset($item_array['prod']['cBenef']);
	                	}
       				}

				 	$item_array['prod']['CFOP'] = $cfopImp;

				 	if(self::isComb($cfopImp)){
				 		$comb_array = self::montaArray_comb();
				 		if(isset($cProdANP)){
				 			$comb_array['cProdANP'] = $cProdANP;
				 			$comb_array['UFCons'] = $sgestado;
				 			$comb_array['encerrante']['nBico']   =  intval($cdbico);
   							$comb_array['encerrante']['nBomba']  =  intval($cdbomba);
   							$comb_array['encerrante']['nTanque'] =  intval($cdalmoxarifado);
   							$comb_array['encerrante']['vEncIni'] =  $this->numberFormat($vrencerranteinicial,3,'.','');
   							$comb_array['encerrante']['vEncFin'] =  $this->numberFormat($vrencerrantefinal,3,'.','');
   							$item_array['prod']['comb'] = $comb_array;
   						}
				 	}
				}
			}
	  	}
		return $item_array;
	}

	private function buildParamsProdImp($nrseqvenda,$nrsequitvend, $cdfilial, $cdcaixa, $cdproduto, $nrorg){
		$prodImpParams = array(
			'NRSEQVENDA'   => $nrseqvenda,
			'NRSEQUITVEND' => $nrsequitvend,
			'CDFILIAL'     => $cdfilial,
			'CDCAIXA'      => $cdcaixa,
			'CDPRODUTO'    => $cdproduto,
			'NRORG'        => $nrorg
   		);
   		return $prodImpParams;
	}

	private function getPayment($payment) {
        $payment_array = self::montaArray_pag();
		$idtiporeceSEFAZ = self::getSEFAZIdtiporece($payment['IDTIPORECE']);

		$payment_array['tPag'] = $idtiporeceSEFAZ;
		$payment_array['vPag'] = $this->numberFormat($payment['VRMOVIVEND'], 2, '.', '');

		if($idtiporeceSEFAZ == '03' || $idtiporeceSEFAZ == '04'){
            $payment_array['card'] = $this->montaArray_card($payment['IDDESABTEF'], $payment['CDADMINCART'], $payment['CDBANCARTSEFAZ'], $payment['CDNSUHOSTTEF']);
        }

		return $payment_array;
	}

   	private function montaArrayIde($filialInfo, $nfceInfo, $serieNFE, $dtvendaTZ, $IDAMBTRABNFCE){
   		// --- IDENTIFICAÇÃO DA NOTA
		$ide =  array(
					 'cUF'    	=>  $filialInfo['CDESTADOIBGE'], // CDESTADOIBGE
			         'cNF'    	=>  $nfceInfo['randomNrNFE'], // NUMERO ALEATÓRIO - 8 DIG
			         'natOp'  	=>  'Descrição da natureza de operação : Venda', // DESCRIÇÃO DA NATUREZA DE OPERAÇÃO
			         'mod'   	=>  self::MODELO_DOC,
			         'serie'  	=>  $serieNFE, // CDSERIENFE
			         'nNF'	 	=>  $nfceInfo['nrNFEInt'], // NÚMERO DA NOTA (NOVOCODIGO NFCE_FILIAL_AMBIENTE)
			         'dhEmi'  	=>  $dtvendaTZ, // FULL DATETIME, MOMENTO DE EMISSÃO
			         'tpNF'   	=>  1, // TIPO DA NOTA FISCAL : 0 - ENTRADA, 1 - SAIDA
			         'idDest' 	=>  1, // ID DESTINO: 1 - INTERNO, 2 - INTERESTADUAL , 3 - EXTERIOR
			         'cMunFG' 	=>  $filialInfo['CDMUNICIBGE'], // CDMUNICIBGE
			         'tpImp'  	=>	4, // TIPO DE IMPRESSAO : 4 - DANFE NFCe
			         'tpEmis' 	=>  $nfceInfo['tpEmis'], // TIPO DE EMISSÃO : 1 - NORMAL
			         'cDV'    	=>  $nfceInfo['cDV'], // DÍGITO VERIFICADOR nrAcessoNFCE
			         'tpAmb'  	=>  $IDAMBTRABNFCE, // TIPO DE AMBIENTE : 1 - PRODUÇÃO, 2 - HOMOLOGAÇÃO
			         'finNFe' 	=>  1, // FINALIDADE DA NOTA : 1 - NORMAL
			         'indFinal' =>	1, // CONSUMIDOR FINAL : 1 - SIM
			         'indPres'  =>	1, // OPERACAO PRESENCIAL : 1 - SIM
			         'procEmi'  =>	0, // PROCESSO DE EMISSÃO : 0 - POR APP DO CONTRIBUINTE
			         'verProc'	=>	1  // VERSÃO DO PROCESSO EMISSOR
	    );
		return $ide;
   	}

   	private function montaArrayEmit($filialInfo){
   		// --- IDENTIFICAÇÃO DO EMITENTE
		// - identificação do endereço do emitente
		$enderEmit = array(
						'xLgr' 	  =>	$filialInfo['DSENDEFILI'], // DSENDEFILI
						'nro'	  =>	$filialInfo['NRENDEFILISEP'], // NRENDEFILISEP
						'xBairro' =>	$filialInfo['NMBAIRFILI'], // NMBAIRFILI
						'cMun'	  =>	$filialInfo['CDMUNICIBGE'], // CDMUNICIBGE
						'xMun'	  =>	$filialInfo['NMMUNICIPIO'], // NMMUNICIPIO
						'UF'	  =>	$filialInfo['SGESTADOFILI'], // SGESTADO
						'CEP'	  =>	str_replace(array('.', '-'), '', $filialInfo['NRCEPFILI']), // CEP
						'cPais'	  =>	$filialInfo['CDPAISBACEN'], // CDPAISBACEN
						'xPais'	  =>	$filialInfo['NMPAIS']  // NMPAIS
					 );
		// - identificação do emitente
		$emit = array(
					'CNPJ'		 =>	$filialInfo['NRINSJURFILI'], // NRINSJURFILI
					'xNome'	     =>	$filialInfo['NMRAZSOCFILI'], // NMRAZSOCFILI
					'xFant'	     =>	$filialInfo['NMFILIAL'], // NMFILIAL
					'enderEmit'  => $enderEmit, // ENDERECO EMITENTE
					'IE'		 => $filialInfo['CDINSCESTAFILI'], // CDINSCESTA
					'IM'		 => $filialInfo['CDINSCMUNIFILI'], // CDINSCMUNI
					'CNAE'		 => $filialInfo['CDCNAE'], // CDCNAE
					'CRT'		 => $filialInfo['CDSITUCRT']  // CDSITUCRT
				);

		return $emit;
   	}

   	private function montaArrayDest($IDAMBTRABNFCE, $infoConsumer){
   		// --- IDENTIFICAÇÃO DO DESTINATARIO
   		$dest_array = array();

   		$cpfCNPJ = preg_replace('/[^0-9]/', '', $infoConsumer['NRINSCRCONS']);
   		if (strlen($cpfCNPJ) == 11) {
   			$dest_array['CPF'] = $cpfCNPJ;
   		} else {
   			$dest_array['CNPJ'] = $cpfCNPJ;
   		}

   		$xNome = '';
   		if ($IDAMBTRABNFCE == 1){
        	if (!empty($infoConsumer['NMCONSVEND'])){
        		$xNome = preg_replace("/&([a-z])[a-z]+;/i", "$1", trim($infoConsumer['NMCONSVEND']));
        	} else if (!empty($infoConsumer['NMCONSUMIDOR'])){
        		$xNome = preg_replace("/&([a-z])[a-z]+;/i", "$1", trim($infoConsumer['NMCONSUMIDOR']));
        	} else {
        		$xNome = 'CONSUMIDOR FINAL';
        	}
   		} else {
   			$xNome = 'NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
   		}
   		$dest_array['xNome'] = $xNome;
   		if (!empty($infoConsumer['DSENDECONSVENDA']) && strlen($infoConsumer['DSENDECONSVENDA']) > 3){
   			$dest_array['enderDest'] = array(
   				'xLgr' => preg_replace("/&([a-z])[a-z]+;/i", "$1", trim($infoConsumer['DSENDECONSVENDA'])),
				'nro' => '000',
				'xBairro' => $infoConsumer['NMBAIRIEST'],
				'cMun' => strlen($infoConsumer['CDMUNICIBGE']) == 7 ? $infoConsumer['CDMUNICIBGE'] : $infoConsumer['CDESTADOIBGE'] . $infoConsumer['CDMUNICIBGE'],
				'xMun' => $infoConsumer['NMMUNICIPIO'],
				'UF' => $infoConsumer['SGESTADO'],
				'CEP' => '00000000',
				'cPais' => '1058',
				'xPais' => 'BRASIL'
   			);
   		}

   		$dest_array['indIEDest'] = '9';

		return $dest_array;
   	}

   	private function montaArray_item(){
   		// --- IDENTIFICAÇÃO DE UM ITEM DA VENDA
		// - identificação do produto que compõe o item
		$prod = array(
			'cProd'		 => null, // CDARVPROD
			'cEAN'		 => NULL, // ?????????
			'xProd'		 => null, // NMPRODUTO
			'NCM'		 => null, // CDCLASFISC
			'CEST'       => null, // CDCEST
			'cBenef'	 => null,
			'CFOP'		 => null, // Consulta regra fiscal
			'uCom'		 => null, // SGUNIDADE
			'qCom'		 => null, // QTPRODVEND - 4 casas decimais
			'vUnCom'	 => null, // VRUNITPROD - 10 casas decimais
			'vProd'		 => null, // Valor Bruto
			'cEANTrib'	 => null, // ?????????
			'uTrib'		 => null, // SGUNIDADE
			'qTrib'		 => null, // qCom
			'vUnTrib'	 => null, // vUnCom
			'vDesc'	     => null, // VRDESITVEND
			'vOutro'	 => null,
			'indTot'	 => null  // COMPÕE O TOTAL DA NOTA: 0 - NÃO; 1 - SIM
		);

		$imposto = array(
			'vTotTrib' => null,
			'ICMS'     => array(),
			'PIS'      => array(),
			'COFINS'   => array()
		);

		// - identificação do imposto de um item
		$item = array(
			'prod' 	  => $prod,
			'imposto' => $imposto
		);

		return $item;
   	}

   	private function montaArray_comb(){
   		$encerrante  = array(
   			'nBico'   =>  null,
   			'nBomba'  =>  null,
   			'nTanque' =>  null,
   			'vEncIni' =>  null,
   			'vEncFin' =>  null
   		);
   		$comb = array(
   			'cProdANP'	=> null,
   			'UFCons'	=> null,
   			'encerrante' => $encerrante
   		);
   	}

   	private function montaArray_pag(){
   		// --- IDENTIFICAÇÃO DE UM PAGAMENTO DA VENDA
		return array(
			'tPag' => null, // TIPO DE PAGAMENTO - idtiporeceSEFAZ
			'vPag' => null, // VALOR DO PAGAMENTO - VRMOVIVEND
		);
   	}

   	private function montaArray_card($IDDESABTEF, $CDADMINCART, $CDBANCARTSEFAZ, $CDNSUHOST){
   		if ($IDDESABTEF == 'N'){
	   		return array(
				'tpIntegra' => 1,
				'CNPJ' => !empty($CDADMINCART) ? preg_replace('/[^0-9]/', '', $CDADMINCART) : '00000000000000',
				'tBand' => !empty($CDBANCARTSEFAZ) ? $CDBANCARTSEFAZ : '99',
				'cAut' => !empty($CDNSUHOST) ? substr($CDNSUHOST, 0, 20) :'000000000'
			);
   		} else {
   			return array(
   				'tpIntegra' => 2
   			);
   		}
   	}

   	private function montaArray_ICMS00(){
   		return array(
   			'orig' => null,
   			'CST' => '00',
   			'modBC' => null,
   			'vBC' => null,
   			'pICMS' => null,
   			'vICMS' => null
   		);
   	}

   	private function montaArray_ICMS10(){
   		return array(
   			'orig' => null,
   			'CST' => '10',
   			'modBC' => null,
   			'vBC' => null,
   			'pICMS' => null,
   			'vICMS' => null
   		);
   	}

   	private function montaArray_ICMS20(){
   		return array(
   			'orig' => null,
   			'CST' => '20',
   			'modBC' => null,
   			'pRedBC' => null,
   			'vBC' => null,
   			'pICMS' => null,
   			'vICMS' => null
   		);
   	}

   	private function montaArray_ICMS30(){
   		return array(
   			'orig' => null,
   			'CST' => '30'
   		);
   	}

   	private function montaArray_ICMS40(){
		return array(
   			'orig' => null,
   			'CST' => '40'
   		);
   	}

   	private function montaArray_ICMS41(){
		return array(
   			'orig' => null,
   			'CST' => '41'
   		);
   	}

   	private function montaArray_ICMS50(){
		return array(
   			'orig' => null,
   			'CST' => '50'
   		);
   	}

   	private function montaArray_ICMS51(){
		return array(
   			'orig' => null,
   			'CST' => '51',
   			'modBC' => null,
   			'pRedBC' => null,
   			'vBC' => null,
   			'pICMS' => null,
   			'vICMS' => null
   		);
   	}

   	private function montaArray_ICMS60(){
		return array(
   			'orig' => null,
   			'CST' => '60'
   		);
   	}

   	private function montaArray_ICMS70(){
		return array(
   			'orig'    => null,
   			'CST'     => '70',
   			'modBC'   => null,
   			'pRedBC'  => null,
   			'vBC'     => null,
   			'pICMS'   => null,
   			'vICMS'   => null,
   			'modBCST' => '3',
            'pMVAST'  => '0.00',
            'pRedBCST'=> '0.00',
            'vBCST'   => '0.00',
            'pICMSST' => '0.00',
            'vICMSST' => '0.00'
   		);
   	}

   	private function montaArray_ICMS90(){
   		return array(
   			'orig'    => null,
   			'CST'     => '90',
   			'modBC'   => null,
   			'vBC'     => null,
   			'pRedBC'  => null,
   			'pICMS'   => null,
   			'vICMS'   => null
   		);
   	}

   	private function montaArray_PISAliq(){
		return array(
   			'CST'  => null,
   			'vBC'  => null,
			'pPIS' => null,
			'vPIS' => null
   		);
   	}

   	private function montaArray_PISNT(){
		return array(
   			'CST'  => null
   		);
   	}

   	private function montaArray_PISQtde(){
		return array(
   			'CST'  => null,
   			'vBC'  => null,
			'pPIS' => null,
			'vPIS' => null
   		);
   	}

   	private function montaArray_PISOutr(){
		return array(
   			'CST'  	  => null,
   			'vBC'  	  => null,
			'pPIS' => null,
			'vPIS' => null
   		);
   	}

   	private function montaArray_COFINSAliq() {
   		return array(
   			'CST'  	  => null,
   			'vBC'  	  => null,
			'pCOFINS' => null,
			'vCOFINS' => null
   		);
   	}

   	private function montaArray_COFINSQtde() {
		return array(
   			'CST'  	  => null,
   			'vBC'  	  => null,
			'pCOFINS' => null,
			'vCOFINS' => null
   		);
   	}

   	private function montaArray_COFINSNT() {
   		return array(
   			'CST' => null
   		);
   	}

   	private function montaArray_COFINSOutr() {
   		return array(
   			'CST'  	  => null,
   			'vBC'  	  => null,
			'pCOFINS' => null,
			'vCOFINS' => null
   		);
   	}

	private function montaArrayTotal($productsNFCE, $filialInfo, $nrseqvenda, $cdtipooper, $IDAMBTRABNFCE, $VRTOTTRIBIBPT) {
   		$icmsArray = self::montaArrayICMS();
		$totalArray = array();
		$totalArray['ICMSTot'] = $icmsArray;
		$totalArray['toSaleArray'] = array();
	    $indexItemAprov = 0;
		$impProd = self::calcImpProd($VRTOTTRIBIBPT, count($productsNFCE));
	    foreach ($productsNFCE as $item) {
			$itemXml = self::getSaleItem(
				$filialInfo['CDFILIAL'],
				$filialInfo['CDLOJA'],
				$filialInfo['CDCAIXA'],
				$filialInfo['NRORG'],
				$nrseqvenda,
				$item['NRSEQUITVEND'],
				$filialInfo['CDEMPRESA'],
				$filialInfo['CDPAIS'],
				$filialInfo['SGESTADO'],
				$filialInfo['CDMUNICIPIO'],
				$filialInfo['CDINSCESTA'],
				$item,
				$cdtipooper,
				$filialInfo['CDPRODTAXSER'],
				$impProd['perProd'],
				$filialInfo['CDPRODTAXAENTR']
			);
			if (!empty($itemXml['prod']['cProd'])){
				$indexItemAprov++;
				$nItem = "nItem+".$indexItemAprov;
				if($IDAMBTRABNFCE == 2 && $indexItemAprov == 1){
					$itemXml['prod']['xProd'] = 'NOTA FISCAL EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
				}
				// $saleArray['det+'.$nItem] = $itemXml;
				$totalArray['toSaleArray']['det+'.$nItem] = $itemXml;

				if($itemXml['prod']['indTot'] == '1'){
					$icms = $itemXml['imposto']['ICMS'];
					$totalArray = self::updateTotal($itemXml,$totalArray, $icms);
				}
			}
	    }

		$totalArray['ICMSTot']['vNF'] = self::calcTotalNF($totalArray);
		// evitar diferenças no total do imposto no caso de valor ímpar
		$totalArray['ICMSTot']['vTotTrib'] = $impProd['totTrib'];
	    return $totalArray;
   	}

   	private function calcImpProd($VRTOTTRIBIBPT, $nrProd){
   		$perProd = floatval(bcdiv(str_replace(',','.',strval($VRTOTTRIBIBPT / $nrProd)), '1', '2'));

   		return array(
   			'totTrib' => $this->numberFormat($perProd * $nrProd, 2, '.', ''),
   			'perProd' => $this->numberFormat($perProd, 2, '.', '')
   		);
   	}

   	private function calcTotalNF($totalArray){
   		return $this->numberFormat(round($totalArray['ICMSTot']['vProd'] - $totalArray['ICMSTot']['vDesc'] + $totalArray['ICMSTot']['vOutro'], 2), 2, '.', '');
   	}

   	private function montaArrayICMS(){
   		$icmsArray = array(
         	'vBC'		 =>	0,
         	'vICMS' 	 =>	0,
         	'vICMSDeson' =>	0,
         	'vFCP'		 => 0,
         	'vBCST' 	 =>	0,
         	'vST' 		 =>	0,
         	'vFCPST' 	 => 0,
         	'vFCPSTRet'  => 0,
    		'vProd'      =>	0,
    		'vFrete'	 =>	0,
    		'vSeg'		 =>	0,
    		'vDesc'		 =>	0,
    		'vII'		 =>	0,
    		'vIPI'		 =>	0,
    		'vIPIDevol'	 => 0,
    		'vPIS' 		 =>	0,
    		'vCOFINS'	 =>	0,
    		'vOutro'	 =>	0,
    		'vNF'		 =>	0
	    );
	    return $icmsArray;
   	}

   	private function montaArrayTransp(){
   		$transp = array(
         	'modFrete'	 =>	9
        );
        return $transp;
   	}

   	private function montaArrayInfAdic($nrseqvenda){
   		$infAdic = array(
         	'obsCont+xCampo+NrSeqVen-IdOrigemVen' => array(
         		'xTexto' => $nrseqvenda.'-BAL_FNC'
         	)
        );
        return $infAdic;
   	}

   	private function getSEFAZIdtiporece($idtiporeceSAAS){
   		$idtiporeceSEFAZ = '99';
   		switch($idtiporeceSAAS){
   			case '1':
   				$idtiporeceSEFAZ = '03';
   				break;
   			case '2':
   				$idtiporeceSEFAZ = '04';
   				break;
   			case '3':
   				$idtiporeceSEFAZ = '02';
   				break;
   			case '4':
   				$idtiporeceSEFAZ = '01';
   				break;
   			case '5':
   				$idtiporeceSEFAZ = '11';
   				break;
   			case 'A':
   				$idtiporeceSEFAZ = '99';
   				break;
   			case '9':
   			case '8':
   				$idtiporeceSEFAZ = '05';
   				break;
   			default:
   				break;
	   	}
	   	return $idtiporeceSEFAZ;
	}

	private function getICMSbyCST($cstImp, $orig, $modBC, $vraliquotaImp, $vrbasecalcicms, $pRed, $vraliqFcp, $sgestado, $baseCalcItVend, $vrpercReducaoef, $vrpealImpfisef, $idtpImposfis, $vrbasecalcreduz){
		$icms_array = array();
		$tagXML = '';
		$vraliquotaImp = $vraliquotaImp/100;
		$vrbasecalcreduz = $vrbasecalcreduz ? $vrbasecalcreduz : $vrbasecalcicms;
		if($cstImp == '00'){
			$tagXML = 'ICMS00';
			$icms_array = self::montaArray_ICMS00();
			$icms_array['orig']  = 	$orig;
			$icms_array['modBC'] = 	$modBC;
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);

			$vICMS = (double) $vraliquotaImp * (double) $this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2);
			$vICMS = $this->numberFormat($this->util->roundABNT($vICMS, 2),2, '.', '');
			$icms_array['vICMS'] = $vICMS;

			//Fundo de Combate à Pobreza (FCP)
			if ($vraliqFcp > 0){
                $icms_array['pFCP'] = $this->numberFormat(str_replace(',', '.',$vraliqFcp), 2, '.', '');
                $icms_array['vFCP'] = $this->numberFormat((double) $vraliqFcp * (double) $this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2));
            }
		}else if($cstImp == '10'){
			$tagXML = 'ICMS10';
			$icms_array = self::montaArray_ICMS10();
			$icms_array['orig']  = 	$orig;
			$icms_array['modBC'] = 	$modBC;
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);
			$vICMS = $vraliquotaImp * $this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2);
			$icms_array['vICMS'] = $this->numberFormat($this->util->roundABNT($vICMS, 2),2, '.', '');
		}else if($cstImp == '20'){
			$tagXML = 'ICMS20';
			$icms_array = self::montaArray_ICMS20();
			$icms_array['orig']  = $orig;
			$icms_array['modBC'] = 	$modBC;
			$icms_array['pRedBC'] = $this->numberFormat(str_replace(',', '.',$pRed),2, '.', '');
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);
			$vICMS = $vraliquotaImp * $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2);
			$icms_array['vICMS'] = $this->numberFormat($this->util->roundABNT($vICMS, 2),2, '.', '');

			//Fundo de Combate à Pobreza (FCP)
			if ($vraliqFcp > 0){
				$icms_array['vBCFCP'] = $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2, '.', '');
                $icms_array['pFCP'] = $this->numberFormat(str_replace(',', '.',$vraliqFcp), 2, '.', '');
                $icms_array['vFCP'] = $this->numberFormat((double) $vraliqFcp * (double) $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2));
            }
			//ICMS DESONERADO
			if ($sgestado == 'RJ' || $sgestado == 'PR' || $sgestado == 'RS'){
	            $vrIcmsDes = $baseCalcItVend*(1-($vraliquotaImp*(1-($pRed/100))))/(1-$vraliquotaImp)-$baseCalcItVend;
	            $icms_array['vICMSDeson'] = $this->numberFormat(str_replace(',', '.', $vrIcmsDes),2, '.', '');
	            $icms_array['motDesICMS'] = '9';
         	}
		}else if($cstImp == '30'){
			$tagXML = 'ICMS30';
			$icms_array = self::montaArray_ICMS30();
			$icms_array['orig']  = $orig;
		}else if($cstImp == '40'){
			$tagXML = 'ICMS40';
			$icms_array = self::montaArray_ICMS40();
			$icms_array['orig']  = $orig;
			//ICMS DESONERADO
			if ($sgestado == 'RJ' || $sgestado == 'PR' || $sgestado == 'RS'){
	            $vrIcmsDes = $baseCalcItVend*(1-($vraliquotaImp*(1-($pRed/100))))/(1-$vraliquotaImp)-$baseCalcItVend;
	            $icms_array['vICMSDeson'] = $this->numberFormat(str_replace(',', '.', $vrIcmsDes),2, '.', '');
	            $icms_array['motDesICMS'] = '9';
         	}
		}else if($cstImp == '41'){
			$tagXML = 'ICMS40';
			$icms_array = self::montaArray_ICMS41();
			$icms_array['orig']  = $orig;
		}else if($cstImp == '50'){
			$tagXML = 'ICMS50';
			$icms_array = self::montaArray_ICMS50();
			$icms_array['orig']  = $orig;
		}else if($cstImp == '51'){
			$tagXML = 'ICMS51';
			$icms_array = self::montaArray_ICMS51();
			$icms_array['orig']  = $orig;
			$icms_array['modBC'] = 	$modBC;
			$icms_array['pRedBC'] = $this->numberFormat(str_replace(',', '.',$pRed),2, '.', '');
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);
			// o código abaixo não está atribuindo valor ao XML. é necessário validar se a conta abaixo deve ser feita
			// $vICMSStr = (string)($vraliquotaImp * $this->numberFormat(str_replace(',', '.',$vrbasecalcicms),2));
			// $dotPos = strpos($vICMSStr,'.');
			// $vICMS = $this->numberFormat($this->util->roundABNT(substr($vICMSStr,0,$dotPos+3), 2));
		}else if($cstImp == '60'){
			$tagXML = 'ICMS60';
			$icms_array = self::montaArray_ICMS60();
			$icms_array['orig']  = $orig;

			//ICMS EFETIVO
			if (($vrpercReducaoef != null) and ($vrpealImpfisef != null)){
                $icms_array['pRedBCEfet'] = $this->numberFormat(str_replace(',', '.', $vrpercReducaoef), 2, '.', '');
                $vrBaseCalc	= $baseCalcItVend * (1 - $vrpercReducaoef/100);
                $icms_array['vBCEfet'] = $this->numberFormat(str_replace(',', '.', $vrBaseCalc), 2, '.', '');
                if($idtpImposfis == 'T'){
                	$porcIcmsEfet = $vrpealImpfisef;
                }else{
                	$porcIcmsEfet = 0.00;
                }
                $icms_array['pICMSEfet'] = $this->numberFormat(str_replace(',', '.', $porcIcmsEfet), 2, '.', '');
                $vrIcmsProd = $vrBaseCalc * ($porcIcmsEfet/100);
                $icms_array['vICMSEfet'] = $this->numberFormat(str_replace(',', '.', $vrIcmsProd), 2, '.', '');
             }
		}else if($cstImp == '70'){
			$tagXML = 'ICMS70';
			$icms_array = self::montaArray_ICMS70();
			$icms_array['orig']  = $orig;
			$icms_array['modBC'] = 	$modBC;
			$icms_array['pRedBC'] = $this->numberFormat(str_replace(',', '.',$pRed),2, '.', '');
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);
			$vICMS = $vraliquotaImp * $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2);
			$icms_array['vICMS'] = $this->numberFormat($this->util->roundABNT($vICMS, 2),2, '.', '');
		}else if($cstImp == '90'){
			$tagXML = 'ICMS90';
			$icms_array = self::montaArray_ICMS90();
			$icms_array['orig']  = $orig;
			$icms_array['modBC'] = 	$modBC;
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2, '.', '');
			$icms_array['pRedBC'] = $this->numberFormat(str_replace(',', '.',$pRed),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);
			$vICMS = $vraliquotaImp * $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2);
			$icms_array['vICMS'] = $this->numberFormat($this->util->roundABNT($vICMS, 2),2, '.', '');

			//Fundo de Combate à Pobreza (FCP)
			if ($vraliqFcp > 0){
				$icms_array['vBCFCP'] = $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2, '.', '');
                $icms_array['pFCP'] = $this->numberFormat(str_replace(',', '.',$vraliqFcp), 2, '.', '');
                $icms_array['vFCP'] = $this->numberFormat((double) $vraliqFcp * (double) $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2));
            }
			//ICMS DESONERADO
			if ($sgestado == 'RJ' || $sgestado == 'PR' || $sgestado == 'RS'){
	            $vrIcmsDes = $baseCalcItVend*(1-($vraliquotaImp*(1-($pRed/100))))/(1-$vraliquotaImp)-$baseCalcItVend;
	            $icms_array['vICMSDeson'] = $this->numberFormat(str_replace(',', '.', $vrIcmsDes),2, '.', '');
	            $icms_array['motDesICMS'] = '9';
         	}

		}else if($cstImp == '102' || $cstImp == '103' || $cstImp == '300' || $cstImp == '400'){
            $tagXML = 'ICMSSN102';
            $icms_array = array();
            $icms_array['orig'] = '0';
            $icms_array['CSOSN'] = '102';
        }else if($cstImp == '500'){
            $tagXML = 'ICMSSN500';
            $icms_array = array();
            $icms_array['orig'] = '0';
			$icms_array['CSOSN'] = '500';
			//ICMS EFETIVO
			if (($vrpercReducaoef != null) and ($vrpealImpfisef != null)){
                $icms_array['pRedBCEfet'] = $this->numberFormat(str_replace(',', '.', $vrpercReducaoef), 2, '.', '');
                $vrBaseCalc	= $baseCalcItVend * (1 - $vrpercReducaoef/100);
                $icms_array['vBCEfet'] = $this->numberFormat(str_replace(',', '.', $vrBaseCalc), 2, '.', '');
                if($idtpImposfis == 'T'){
                	$porcIcmsEfet = $vrpealImpfisef;
                }else{
                	$porcIcmsEfet = 0.00;
                }
                $icms_array['pICMSEfet'] = $this->numberFormat(str_replace(',', '.', $porcIcmsEfet), 2, '.', '');
                $vrIcmsProd = $vrBaseCalc * ($porcIcmsEfet/100);
                $icms_array['vICMSEfet'] = $this->numberFormat(str_replace(',', '.', $vrIcmsProd), 2, '.', '');
             }
		}else if($cstImp == '900'){
            $tagXML = 'ICMSSN900';
            $icms_array = array();
            $icms_array['orig'] = '0';
			$icms_array['CSOSN'] = '900';
			$icms_array['modBC'] = '3';
			$icms_array['vBC']   = 	$this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2, '.', '');
			$icms_array['pRedBC'] = $this->numberFormat(str_replace(',', '.',$pRed),2, '.', '');
			$icms_array['pICMS'] =  $this->numberFormat($vraliquotaImp*100,2);
			$vICMS = $vraliquotaImp * $this->numberFormat(str_replace(',', '.',$vrbasecalcreduz),2);
			$icms_array['vICMS'] = $this->numberFormat($this->util->roundABNT($vICMS, 2),2, '.', '');
        }
		$icms_array['TAG'] = $tagXML;
		return $icms_array;
	}

	private function numberFormat($number, $decimals = 2, $dec_point = '.', $thousands_sep = '') {
		return number_format($this->trunc($number, $decimals), $decimals, $dec_point, $thousands_sep);
	}

	private function trunc($val, $f = "2") {
	    if (($p = strpos($val, '.')) !== false) {
	        $val = floatval(substr($val, 0, $p + 1 + $f));
	    }
	    return floatval($val);
	}

	private function getPISbyCST($cstPis, $orig, $modBC, $vraliquotaPis, $vrbasecalcicms){
		$pis_array = array();
		$vraliquotaPis = $vraliquotaPis / 100;
		$vrbasecalcicms = str_replace(',', '.', $vrbasecalcicms);
		$tagXML = '';
		if ($cstPis == '01' || $cstPis == '02') {
			$pis_array = self::montaArray_PISAliq();
			$tagXML = 'PISAliq';
			$pis_array['pPIS'] = $this->numberFormat($vraliquotaPis * 100, 2);
			$pis_array['vBC']  = $this->numberFormat($vrbasecalcicms, 2, '.', '');
			$pis_array['vPIS'] = $this->numberFormat($vrbasecalcicms * $vraliquotaPis, 2);
		} else if ($cstPis == '03') {
			$tagXML = 'PISQtde';
			$pis_array = self::montaArray_PISQtde();
			$pis_array['pPIS'] = $this->numberFormat($vraliquotaPis * 100, 2);
			$pis_array['vBC']  = $this->numberFormat($vrbasecalcicms, 2, '.', '');
			$pis_array['vPIS'] = $this->numberFormat($vrbasecalcicms * $vraliquotaPis, 2);
		} else if ($cstPis == '04' || $cstPis == '05' || $cstPis == '06' || $cstPis == '07' || $cstPis == '08' || $cstPis == '09'){
			$tagXML = 'PISNT';
			$pis_array = self::montaArray_PISNT();
		} else if ($cstPis == '49' || $cstPis == '50' || $cstPis == '51' ||
			       $cstPis == '52' || $cstPis == '53' || $cstPis == '54' || $cstPis == '55' ||
			       $cstPis == '56' || $cstPis == '60' || $cstPis == '61' || $cstPis == '62' ||
			       $cstPis == '63' || $cstPis == '64' || $cstPis == '65' || $cstPis == '66' ||
			       $cstPis == '67' || $cstPis == '70' || $cstPis == '71' || $cstPis == '72' ||
			       $cstPis == '73' || $cstPis == '74' || $cstPis == '75' || $cstPis == '98' ||
			       $cstPis == '99') {
			$tagXML = 'PISOutr';
			$pis_array = self::montaArray_PISOutr();
			$pis_array['pPIS'] = $this->numberFormat($vraliquotaPis * 100, 2);
			$pis_array['vBC']  = $this->numberFormat($vrbasecalcicms, 2, '.', '');
			$pis_array['vPIS'] = $this->numberFormat($vrbasecalcicms * $vraliquotaPis, 2);
		}
		$pis_array['CST'] = $cstPis;
		$pis_array['TAG'] = $tagXML;
		return $pis_array;
	}

	private function getCOFINSbyCST($cstCof, $orig, $modBC, $vraliquotaCof, $vrbasecalcicms){
		$cof_array = array();
		$tagXML = '';
		$vraliquotaCof  = str_replace(',','.',$vraliquotaCof);
		$vrbasecalcicms = str_replace(',','.',$vrbasecalcicms);
		$vraliquotaCof = $vraliquotaCof/100;
		if($cstCof == '01' || $cstCof == '02'){
			$tagXML = 'COFINSAliq';
			$cof_array = self::montaArray_COFINSAliq();
			$cof_array['pCOFINS'] = $this->numberFormat($vraliquotaCof*100,2);
			$cof_array['vBC']     = $this->numberFormat($vrbasecalcicms,2, '.', '');
			$cof_array['vCOFINS'] = $this->numberFormat($vrbasecalcicms * $vraliquotaCof,2);
		}else if($cstCof == '03'){
			$tagXML = 'COFINSQtde';
			$cof_array = self::montaArray_COFINSQtde();
			$cof_array['pCOFINS'] = $this->numberFormat($vraliquotaCof*100,2);
			$cof_array['vBC']     = $this->numberFormat($vrbasecalcicms,2, '.', '');
			$cof_array['vCOFINS'] = $this->numberFormat($vrbasecalcicms * $vraliquotaCof,2);
		}else if($cstCof == '04'  || $cstCof == '05' || $cstCof == '06' || $cstCof == '07' || $cstCof == '08' || $cstCof == '09'){
			$tagXML = 'COFINSNT';
			$cof_array = self::montaArray_COFINSNT();
		}else if($cstCof == '49' || $cstCof == '50' || $cstCof == '51' ||
			     $cstCof == '52' || $cstCof == '53' || $cstCof == '54' || $cstCof == '55' ||
			     $cstCof == '56' || $cstCof == '60' || $cstCof == '61' || $cstCof == '62' ||
			     $cstCof == '63' || $cstCof == '64' || $cstCof == '65' || $cstCof == '66' ||
			     $cstCof == '67' || $cstCof == '70' || $cstCof == '71' || $cstCof == '72' ||
			     $cstCof == '73' || $cstCof == '74' || $cstCof == '75' || $cstCof == '98' ||
			     $cstCof == '99'){
			$tagXML = 'COFINSOutr';
			$cof_array = self::montaArray_COFINSOutr();
			$cof_array['pCOFINS'] = $this->numberFormat($vraliquotaCof*100,2);
			$cof_array['vBC']     = $this->numberFormat($vrbasecalcicms,2, '.', '');
			$cof_array['vCOFINS'] = $this->numberFormat($vrbasecalcicms * $vraliquotaCof,2);
		}

		$cof_array['CST'] = $cstCof;
		$cof_array['TAG'] = $tagXML;

		return $cof_array;
	}

	private function gerarXML($saleArray, $nrAcessoNFCE, $contingencia){
        $dom = self::generatesStringXMLByIndexedArray($saleArray, $nrAcessoNFCE, $contingencia);
        // trata xml
	    $XML = Strings::clearXmlString($dom->saveXML());
	    // salva xml
        // $XMLName = ENTRADAS_XML_PATH . $nrAcessoNFCE.".xml";
        // $handle = fopen($XMLName, "w");
        // fwrite($handle, $XML);
        // fclose($handle);
		return $XML;
	}

	public function createPath($IDAMBTRABNFCE, $filialInfo, $DTEMISSAO){
		$typePath = $IDAMBTRABNFCE == '2' ? 'homologacao' : 'producao';
		$XMLPath = realpath($this->systemPath) . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'NOTAS' . DIRECTORY_SEPARATOR . $typePath . DIRECTORY_SEPARATOR;
		$nfceMoment = $filialInfo['NRORG'] . '-' . $filialInfo['CDFILIAL'] . '-'
			. $filialInfo['CDCAIXA'] . '_' . $DTEMISSAO->format('Y-m-d') . DIRECTORY_SEPARATOR;

		$entradas = $XMLPath . "ENTRADAS" . DIRECTORY_SEPARATOR . $nfceMoment;
		$assinadas = $XMLPath . "ASSINADAS" . DIRECTORY_SEPARATOR . $nfceMoment;
		$enviadas = $XMLPath . "ENVIADAS" . DIRECTORY_SEPARATOR . $nfceMoment;
		$canceladas = $XMLPath . "CANCELADAS" . DIRECTORY_SEPARATOR . $nfceMoment;

		// $this->util->createFolder($entradas);
		// $this->util->createFolder($assinadas);
		// $this->util->createFolder($enviadas);
		// $this->util->createFolder($canceladas);

		define('ENTRADAS_XML_PATH', $entradas);
		define('ASSINADAS_XML_PATH', $assinadas);
		define('ENVIADAS_XML_PATH', $enviadas);
		define('CANCELADAS_XML_PATH', $canceladas);
	}

	private function getNfeTools($nfceData){
		$configContent = array(
			'atualizacao' => '2018-08-29 00:00:00',
      		'tpAmb' => intval($nfceData['tpAmb']),
      		'razaosocial' => $nfceData['NMRAZSOCFILI'],
      		'cnpj' => $nfceData['NRINSJURFILI'],
      		'ie' => $nfceData['CDINSCESTA'],
      		'siglaUF' => $nfceData['SGESTADO'],
      		'schemes' => 'PL_008i2',
      		'versao' => self::VERSAO_LAYOUT,
      		'tokenIBPT' => null,
      		'CSC' => $nfceData['CSC'],
      		'CSCid' => $nfceData['CSCid'],
      		// proxy setado aqui não funciona
      		'aProxyConf' => [
      		    'proxyIp' => null,
      		    'proxyPort' => null,
      		    'proxyUser' => null,
      		    'proxyPass' => null
      		]
		);

		// lê certificado
		$certContent = self::getNFCeCert($nfceData);
		// cria instância nfce
		$nfeTools = new Tools(json_encode($configContent), Certificate::readPfx($certContent, $nfceData['DSSENHACERTNFCE']));
		// seta modelo
		$nfeTools->model(self::MODELO_DOC);
		// seta o proxy
		$nfeTools->soap->proxy($this->PROXY_IP, $this->PROXY_PORT, $this->PROXY_USER, $this->PROXY_PASSWORD);
		// seta protocolo
		$nfeTools->soap->protocol(SoapCurl::SSL_TLSV1_2);
        // timeout do envio NFCE
        $nfeTools->soap->timeout(4);

		return $nfeTools;
	}

	public function getNFCeCert($nfceData){
		try{
			if($certificadoLocalAntigo = @file_get_contents(realpath($this->systemPath) . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'CERTS' . DIRECTORY_SEPARATOR . $nfceData['NMARQCERTNFCE'])){
				$certificadoLocalAntigoValidado = Certificate::readPfx($certificadoLocalAntigo, $nfceData['DSSENHACERTNFCE']);
				if (!$certificadoLocalAntigoValidado->isExpired()){
					return $certificadoLocalAntigo;
				}else{
					$certificadoLocalAntigo = realpath($this->systemPath) . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'CERTS' . DIRECTORY_SEPARATOR . $nfceData['NMARQCERTNFCE'];
					throw new Exception("O certificado ".$certificadoLocalAntigo." é inválido ou está expirado.", 1);
				}
			}else{
				$params = array('CDFILIAL' => $nfceData['CDFILIAL']);
				$entidadeCertificado = $this->entityManager->getConnection()->fetchAssoc("LOCAL_CERTIFICADO_EXTERNO", $params);
				if($certificadoLocalNovo = @file_get_contents($this->systemPath . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'CERTS' . DIRECTORY_SEPARATOR . $nfceData['CDFILIAL'] . DIRECTORY_SEPARATOR . $nfceData['CDCAIXA'] . DIRECTORY_SEPARATOR . $nfceData['NMARQCERTNFCE'])){
					$certificadoLocalNovoValidado = Certificate::readPfx($certificadoLocalNovo, $entidadeCertificado['CDSENHACERTIF']);
					if (!$certificadoLocalNovoValidado->isExpired()){
						return $certificadoLocalNovo;
					}else{
						if($certificadoNuvem = @file_get_contents($entidadeCertificado['CERTIFICADO'])){
							$certificadoNuvemValidado = Certificate::readPfx($certificadoNuvem, $entidadeCertificado['CDSENHACERTIF']);
							if (!$certificadoNuvemValidado->isExpired()){
								$certificadoLocalNovo = realpath($this->systemPath) . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'CERTS' . DIRECTORY_SEPARATOR . $nfceData['CDFILIAL'] . DIRECTORY_SEPARATOR . $nfceData['CDCAIXA'] . DIRECTORY_SEPARATOR;
								if(@file_put_contents($certificadoLocalNovo . $nfceData['NMARQCERTNFCE'], $certificadoNuvem)){
									return $certificadoNuvem;
								}else{
									throw new Exception("Não foi possível gravar o certificado local. Verifique o caminho e tente novamente.", 1);
								}
							}else{
								throw new Exception("O certificado dessa filial, cadastrado no Retail, está inválido ou expirado. ", 1);
							}
						}
					}
				}else{
					if (!empty($entidadeCertificado['CERTIFICADO'])){
						if($certificadoNuvem = @file_get_contents($entidadeCertificado['CERTIFICADO'])){
							$certificadoNuvemValidado = Certificate::readPfx($certificadoNuvem, $entidadeCertificado['CDSENHACERTIF']);
							if (!$certificadoNuvemValidado->isExpired()){
								$certificadoLocalNovo = realpath($this->systemPath) . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'CERTS' . DIRECTORY_SEPARATOR . $nfceData['CDFILIAL'] . DIRECTORY_SEPARATOR . $nfceData['CDCAIXA'] . DIRECTORY_SEPARATOR;
								if($this->util->createFolder($certificadoLocalNovo)){
									if(@file_put_contents($certificadoLocalNovo . $nfceData['NMARQCERTNFCE'], $certificadoNuvem)){
										return $certificadoNuvem;
									}else{
										throw new Exception("Não foi possível gravar o certificado local. Verifique o caminho e tente novamente.", 1);
									}
								}else{
									throw new Exception("Não foi possível criar o diretório local para baixar o certificado. Verifique se as permissões de criação de pasta.", 1);
								}
							}else{
								throw new Exception("O certificado dessa filial, cadastrado no Retail, está inválido ou expirado. ", 1);
							}
						}else{
							throw new Exception("Não foi possível fazer o download do certificado. Verifique se a parametrização está correta e tente novamente. ", 1);
						}
					}else{
						$certificadoLocalAntigo = realpath($this->systemPath) . DIRECTORY_SEPARATOR . 'NFCE' . DIRECTORY_SEPARATOR . 'CERTS' . DIRECTORY_SEPARATOR;
						throw new Exception("Você não fez o upload do cerfiticado " . $nfceData['NMARQCERTNFCE'] . " no Retail ou não foi encontrado no diretório ".$certificadoLocalAntigo, 1);
					}
				}
			}
		} catch (\Exception $e){
			throw new \Exception($e->getMessage());
		}
	}

    public function cancelaVendaNFCE($CDFILIAL, $CDCAIXA, $NRORG, $NRSEQVENDA, $venda, $DSRAZAOCANCNFCE, $CDOPERADOR){
    	$result = array(
        	'error' => true,
        	'message' => '',
        	'mensagemNfce' => '',
        	'dadosImpressao' => array()
        );

        try {
	    	$params = array(
				'CDFILIAL' => $CDFILIAL
			);
			$nfceData = $this->entityManager->getConnection()->fetchAssoc("GET_NFCE_DATA", $params);
			if ($nfceData['IDAMBTRABNFCE'] == '1'){
				$nfceData['CSC'] =  $nfceData['CDCODSCONSPROD'];
				$nfceData['CSCid'] = $nfceData['CDIDTOKENPROD'];
			} else {
				$nfceData['CSC'] = $nfceData['CDCODSCONSHOMO'];
				$nfceData['CSCid'] = $nfceData['CDIDTOKENHOMO'];
			}

			$validateNfce = self::validateDataNfce($nfceData);
			if (!$validateNfce['error']){
				$filialInfo = array(
					'CDFILIAL' => $CDFILIAL,
					'CDCAIXA' => $CDCAIXA,
					'NRORG' => $NRORG
				);
				self::createPath($nfceData['IDAMBTRABNFCE'], $filialInfo, new \DateTime());

				$nfceData['tpAmb'] = $nfceData['IDAMBTRABNFCE'];
				$nfceData['CDFILIAL'] = $CDFILIAL;
				$nfceData['CDCAIXA'] = $CDCAIXA;
				$nfceData['NRORG'] = $NRORG;
				$nfeTools = self::getNfeTools($nfceData);
				// chama cancelamento
				$strNfeTools = $nfeTools->sefazCancela($venda['NRACESSONFCE'], $DSRAZAOCANCNFCE, $venda['NRPROTOCOLONFCE']);
				$stNfeTools = new Standardize($strNfeTools);
				$stNfeTools = $stNfeTools->toArray();

				self::validarTransmissaoCancelamento($stNfeTools, $result);
				if (!$result['error']){
					$result['XMLENVIO'] = $nfeTools->lastRequest;
					$result['XMLRETORNO'] = $strNfeTools;

					$saveResult = self::saveAuthorizeXML($result, $venda['NRACESSONFCE'], CANCELADAS_XML_PATH);
					if ($saveResult['error']){
                		$resultadoTransmissao['mensagemNfce'] .= $saveResult['message'];
	                }
		        	$updateNfceCanResult = self::updateVendaStatusNfceCan($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $stNfeTools, $DSRAZAOCANCNFCE, $strNfeTools, $CDOPERADOR);
		        	if ($updateNfceCanResult['error']){
		        		$resultadoTransmissao['mensagemNfce'] .= '<br> Erro ao salvar informações do cancelamento no banco de dados: ' . $updateNfceCanResult['message'];
		        	}
				}
			} else {
				$result['message'] = $validateNfce['message'];
			}
 		} catch (\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
        	$result['message'] = $e->getMessage();
        }

		return $result;
    }

    private function validarTransmissaoCancelamento($stNfeTools, &$result){
    	if ($stNfeTools['cStat'] == '128') {
    		$stNfeTools = $stNfeTools['retEvento']['infEvento'];
    		if ($stNfeTools['cStat'] == '101'|| $stNfeTools['cStat'] ==  '135' || $stNfeTools['cStat'] == '155'){
    			$result['error'] = false;
    			$result['mensagemNfce'] = $stNfeTools['xMotivo'] . '.';
    		}
    	}

    	if ($result['error']){
    		$result['message'] = 'Não foi possível cancelar o cupom fiscal. <br>' . $stNfeTools['xMotivo'] . '.';
    	}
    }

    private function updateVendaStatusNfceCan($CDFILIAL, $CDCAIXA, $NRSEQVENDA, $stNfeTools, $DSRAZAOCANCNFCE, $DSARQXMLCANCNFCE, $CDOPERADOR){
    	$result = array(
    		'error' => true,
    		'message' => ''
    	);

    	try {
    		$DSOBSSTATUSNFCE = null;
    		$NRPROTOCOLOCANC = null;
			$DTHRPROTOCOCANC = null;

    		if (!empty($stNfeTools['retEvento']['infEvento'])){
    			$stNfeTools = $stNfeTools['retEvento']['infEvento'];

    			$DSOBSSTATUSNFCE = $stNfeTools['cStat'] . ' - ' . $stNfeTools['xMotivo'] . ' (NFCe Cancelada na SEFAZ)';
				$NRPROTOCOLOCANC = $stNfeTools['nProt'];
				$DTHRPROTOCOCANC = \DateTime::createFromFormat('Y-m-d\TH:i:sP', $stNfeTools['dhRegEvento']);
    		}

    		$params = array(
    			'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'NRSEQVENDA' => $NRSEQVENDA,
				'DSOBSSTATUSNFCE' => $DSOBSSTATUSNFCE,
				'NRPROTOCOLOCANC' => $NRPROTOCOLOCANC,
				'DTHRPROTOCOCANC' => $DTHRPROTOCOCANC,
				'DSRAZAOCANCNFCE' => $DSRAZAOCANCNFCE,
				'DSARQXMLCANCNFCE' => $DSARQXMLCANCNFCE,
				'CDOPERADORCANC' => $CDOPERADOR
    		);
			$type = array(
    			'DTHRPROTOCOCANC' => \Doctrine\DBAL\TypeS\Type::DATETIME
    		);
    		if (!$this->util->databaseIsOracle()){
				$type['DSARQXMLCANCNFCE'] = \Doctrine\DBAL\Types\Type::BINARY;
			}
        	$this->entityManager->getConnection()->executeQuery("UPDATE_VENDA_STATUS_NFCE_CAN", $params, $type);

        	$result['error'] = false;
    	} catch (\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
    		$result['message'] = $e->getMessage();
    	}

    	return $result;
    }

	private function assinarXML($unsignedXML,$nrAcessoNFCE,$nfeTools){
		$signedXMLName = ASSINADAS_XML_PATH . $nrAcessoNFCE . ".xml";
		$XMLASSINADO = $nfeTools->signNFe($unsignedXML);
        // $sHandle = fopen($signedXMLName, "w");
        // fwrite($sHandle, $XMLASSINADO);
        // fclose($sHandle);
		return $XMLASSINADO;
	}

	protected function transmitirXML($nfeTools, $XMLASSINADO) {
		$XMLASSINADO = preg_replace('/[\n\r\t]/', '', $XMLASSINADO);
		$return = array(
			'error' => true,
			'data' => array(),
			'message' => '',
			'XMLENVIO' => $XMLASSINADO,
			'XMLRETORNO' => ''
		);

		$idLote = substr(str_replace(',', '', number_format(microtime(true)*1000000, 0)), 0, 15);

		try {
			$strNfeTools = $nfeTools->sefazEnviaLote(array($XMLASSINADO), $idLote, 1);
			$return['XMLRETORNO'] = $strNfeTools;
			$stNfeTools = new Standardize($strNfeTools);
			$return['data'] = $stNfeTools->toArray();
			$return['error'] = false;
		} catch(\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			$return['message'] = $e->getMessage();
		}

		return $return;
	}

	private function generatesStringXMLByIndexedArray($saleArray, $nrAcessoNFCE, $contingencia){
		$dom = new \DOMDocument('1.0', 'utf-8');
		//------ Atributos
		$xmlns = $dom->createAttribute('xmlns');
		$xmlns->value = 'http://www.portalfiscal.inf.br/nfe';

        //------ NFe
        $NFe = $dom->createElement('NFe');
        $NFe->appendChild($xmlns);
        $dom->appendChild($NFe);

        //------ infNFe
        $infNFe = $dom->createElement('infNFe');
        $Id = $dom->createAttribute('Id');
        $infNFeVersao = $dom->createAttribute('versao');
        $Id->value = 'NFe'.$nrAcessoNFCE;
        $infNFeVersao->value = self::VERSAO_LAYOUT;

		$infNFe->appendChild($Id);
		$infNFe->appendChild($infNFeVersao);

		$NFe->appendChild($infNFe);
        if($contingencia){
            $saleArray['ide']['dhCont'] = $saleArray['ide']['dhEmi'];
            $saleArray['ide']['xJust'] = 'Motivo Emissao em Contingencia: Caixa OffLine';
            $saleArray['infAdic']['infCpl'] = 'NFCe EMITIDA EM CONTINGENCIA';
            ksort($saleArray['infAdic']);
        }
        self::arrayElementsToXML($saleArray,$infNFe,$dom);

     	return $dom;

	}


	private function arrayElementsToXML($array,&$currentTag,&$dom){
		foreach($array as $key=>$value){
			$newElement =  null;
			$keyAsArray = explode('+',$key);
			if(count($keyAsArray) > 1){
				$key = $keyAsArray[0];
				$newElement = $dom->createElement($key);
				if($key == 'det' || $key == 'obsCont'){
					$attrName = $keyAsArray[1];
					$attrValue = $keyAsArray[2];
					$attribute = $dom->createAttribute($attrName);
					$attribute->value = $attrValue;
					$newElement->appendChild($attribute);
				}
				$currentTag->appendChild($newElement);
				if(is_array($value)){
					self::arrayElementsToXML($value,$newElement,$dom);
				}else{
					$newElement->nodeValue = $value;
				}
			}else{
				if(is_array($value)){
					$newElement = $dom->createElement($key);
					$currentTag->appendChild($newElement);
					self::arrayElementsToXML($value,$newElement,$dom);
				}else{
					$newElement = $dom->createElement($key,$value);
					$currentTag->appendChild($newElement);
				}
			}

        }
	}

	private function updateVendaNFCE($nfceInfo, $filialInfo, $nrseqvenda, $resultadoTransmissao, $DTEMISSAO, $deveCancelarVenda){
		$params = array(
			'CDSERIENFCE' => $nfceInfo['serieCH'],
			'NRACESSONFCE' => $nfceInfo['nrAcessoNFCE'],
			'NRNOTAFISCALCE' => $nfceInfo['nrNFE'],
			'NRLANCTONFCE' => $nfceInfo['randomNrNFE'],
			'IDTPAMBNFCE' => $nfceInfo['tpAmb'],
			'IDSITUVENDA' => $deveCancelarVenda ? 'C' : 'O',
			'IDSTATUSNFCE' => $resultadoTransmissao['IDSTATUSNFCE'],
			'DTEMISSAONFCE' => $DTEMISSAO,
			'NRPROTOCOLONFCE' => $resultadoTransmissao['NRPROTOCOLONFCE'],
			'DSOBSSTATUSNFCE' => $resultadoTransmissao['message'],
			'IDMODOPERACNFCE' => '1',
			'NRRECIBONFCE' => null,
			'CDOPERENVIONFCE' => null,
			'DTHRPROTOCONFCE' => $resultadoTransmissao['DTHRPROTOCONFCE'],
			'IDXMLVALIDNFCE' => 'S',
			'DSQRCODENFCE' => $resultadoTransmissao['QRCODENFCE'],
			'DSARQXMLNFCE' => $resultadoTransmissao['XMLENVIO'],
			'CDFILIAL' => $filialInfo['CDFILIAL'],
			'CDCAIXA' => $filialInfo['CDCAIXA'],
			'NRSEQVENDA' => $nrseqvenda,
			'CDVERSXMLNFCVND' => self::VERSAO_LAYOUT,
			'NRORG' => $filialInfo['NRORG']
		);

		$types = array(
			'DTEMISSAONFCE' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTHRPROTOCONFCE' => \Doctrine\DBAL\Types\Type::DATETIME
		);
		if (!$this->util->databaseIsOracle()){
			$types['DSARQXMLNFCE'] = \Doctrine\DBAL\Types\Type::BINARY;
		}

		$this->entityManager->getConnection()->executeQuery("UPDATE_VENDA_NFCE", $params, $types);
	}

	private function updateTotal($property_array,&$total_xml, $icms){
		$ICMSTot = $total_xml['ICMSTot'];
		$vBCAcumulado = $ICMSTot['vBC'];
		$ICMSTot['vBC'] = 0;
		foreach($ICMSTot as $tKey=>$totalProperty){
			self::updateTotalProperty($tKey,$totalProperty, $property_array);
			$ICMSTot[$tKey] = $this->numberFormat($totalProperty,2 ,'.', '');
		}

		$this->updateVBCTot($ICMSTot,$icms);

		$ICMSTot['vBC'] = $this->numberFormat($ICMSTot['vBC'] + $vBCAcumulado,2 ,'.', '');
		$total_xml['ICMSTot'] = $ICMSTot;
		return $total_xml;
	}

	private function updateTotalProperty($propertyName, &$propertyValue, $arrayToCompare){
		foreach($arrayToCompare as $pKey=>$item){
			if(is_array($item)){
			   self::updateTotalProperty($propertyName,$propertyValue,$item);
			}else{
				if($propertyName == $pKey && $propertyName != 'vBC'){
					$propertyValue = $propertyValue + $item;
				}
				// else if($propertyName == $pKey && $propertyName == 'vBC'){
				// 	$propertyValue = $item;
				// }
			}
		}
	}

	private function updateVBCTot(&$ICMSTot,$icms){
		$icmsArray  = array_values($icms);
		$icmsNode  = $icmsArray[0];
		if(isset($icmsNode['vBC'])){
			$ICMSTot['vBC'] += (double)$icmsNode['vBC'];
		}
	}
	private function formataCaracteresXML($strXML){
		$formattedXML = strtr(
	            $strXML,
     			array (
			      'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A',
      			  'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
      			  'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N',
				  'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
				  'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Ŕ' => 'R',
				  'Þ' => 's', 'ß' => 'B', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a',
				  'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
				  'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
				  'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
				  'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y',
				  'þ' => 'b', 'ÿ' => 'y', 'ŕ' => 'r'
    			)
			  );
		return $formattedXML;
	}

	private function isComb($cfop){
		$isComb = false;
		if($cfop =='1651' || $cfop =='1652' || $cfop =='1653' || $cfop =='1658' || $cfop =='1659' ||
		   $cfop =='1660' || $cfop =='1661' || $cfop =='1662' || $cfop =='1663' || $cfop =='1664' ||
		   $cfop =='2651' || $cfop =='2652' || $cfop =='2653' || $cfop =='2658' || $cfop =='2659' ||
		   $cfop =='2660' || $cfop =='2661' || $cfop =='2662' || $cfop =='2663' || $cfop =='2664' ||
		   $cfop =='3651' || $cfop =='3652' || $cfop =='3653' || $cfop =='5651' || $cfop =='5652' ||
		   $cfop =='5653' || $cfop =='5654' || $cfop =='5655' || $cfop =='5656' || $cfop =='5657' ||
		   $cfop =='5658' || $cfop =='5659' || $cfop =='5660' || $cfop =='5661' || $cfop =='5662' ||
		   $cfop =='5663' || $cfop =='5664' || $cfop =='5665' || $cfop =='5666' || $cfop =='5667' ||
		   $cfop =='6651' || $cfop =='6652' || $cfop =='6653' || $cfop =='6654' || $cfop =='6655' ||
		   $cfop =='6656' || $cfop =='6657' || $cfop =='6658' || $cfop =='6659' || $cfop =='6660' ||
		   $cfop =='6661' || $cfop =='6662' || $cfop =='6663' || $cfop =='6664' || $cfop =='6665' ||
		   $cfop =='6666' || $cfop =='6667' || $cfop =='7651' || $cfop =='7654' || $cfop =='7667') {

				$isComb = true;
		}

		return $isComb;
	}

	private function getIDSTATUSNFCE($cStat) {
		$idStatus = '';

		switch($cStat){
			case '100':
			case '150':
				$idStatus = 'A';
				break;
			case '105':
				$idStatus = 'T';
				break;
			case '215':
				$idStatus = 'R';
			case '110':
			case '301':
			case '302':
			case '303':
			case '999':
				$idStatus = 'D';
				break;
			default:
				$idStatus = 'R';
				break;
		}
		return $idStatus;
	}

	private function nfceMissingParameterMessage($missingParameters){
		$oneMissingParameter = count($missingParameters) == 1;
		$message = $oneMissingParameter ? 'O parâmetro ' : 'Os parâmetros ';

		if($oneMissingParameter) {
			$message .= $missingParameters[0];
		} else {
			$lastIndex = count($missingParameters) - 1;
			foreach ($missingParameters as $key => $missing) {
				$message .= $missing;

				$divisor = '';
				if($key == ($lastIndex - 1)){
					$divisor = ' e ';
				} else if ($key != $lastIndex){
					$divisor = ', ';
				}
				$message .= $divisor;
			}
		}

		return $message . ' da filial estão nulos e necessitam ser parametrizados.';
	}

	private function associateCodeToMeaning(){
		return array(
			'NMRAZSOCFILI' => 'razão social',
			'SGESTADO' => 'estado',
			'NRINSJURFILI' => 'CNPJ',
			'CDINSCESTA' => 'Inscrição Estadual',
			'CSC' => 'token do CSC',
			'CSCid' => 'identificador do CSC',
			'NMARQCERTNFCE' => 'nome do arquivo do certificado digital',
			'DSSENHACERTNFCE' => 'senha do arquivo do certificado digital'
		);
	}

	public function getInfoProducts($filialInfo, $nfceInfo){
		$paramsToGetProducts = array(
			'CDFILIAL'	 => $filialInfo['CDFILIAL'],
			'CDCAIXA'    => $filialInfo['CDCAIXA'],
			'NRSEQVENDA' => $nfceInfo['nrseqvenda']
		);
		return $this->entityManager->getConnection()->fetchAll("GET_PRODUCTS_TO_PRINT_NFCE", $paramsToGetProducts);

	}

	public function getInfoConsumer($filialInfo, $nfceInfo){
		$paramsToConsumer = array(
			':CDFILIAL'   => $filialInfo['CDFILIAL'],
			':CDCAIXA'    => $filialInfo['CDCAIXA'],
			':NRSEQVENDA' => $nfceInfo['nrseqvenda']
		);

        return $this->entityManager->getConnection()->fetchAssoc("GET_CONSUMER_SALE", $paramsToConsumer);
	}

   	private function montaArrayInfRespTec(){
   		// Grupo ZD. Informacoes do Responsavel Tecnico
   		$infRespTec = array(
         	'CNPJ' => '26269316000177',
            'xContato' => 'Wilson Lima de Paula',
            'email' => 'qualidade@teknisa.com',
            'fone' => '3121222300'
        );
        return $infRespTec;
   	}

   	private function validaEstadoICMS($sgestado, $cstImp){
   		return ($sgestado == 'PR' && in_array($cstImp, array('20', '30', '40', '41', '50', '51', '70'))) ||
   			($sgestado == 'RJ' && in_array($cstImp, array('20', '30', '40', '51', '70')));
   	}

}