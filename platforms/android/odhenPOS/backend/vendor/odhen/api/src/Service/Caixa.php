<?php

namespace Odhen\API\Service;

use Odhen\API\Remote\Printer\Command;
use Odhen\API\Util\Exception;

class Caixa {

    protected $entityManager;
    protected $util;
    protected $databaseUtil;
    protected $impressaoUtil;
    protected $impressaoSATService;
    protected $satRequest;
    protected $nfceRequest;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Odhen\API\Util\Util $util,
        \Odhen\API\Util\DataBase $databaseUtil,
        \Odhen\API\Lib\ImpressaoUtil $impressaoUtil,
        \Odhen\API\Service\ImpressaoSAT $impressaoSATService,
        \Odhen\API\Remote\SAT\SAT $satRequest,
        \Odhen\API\Service\NotaFiscal $nfceRequest,
        \Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager
    ) {

        $this->instanceManager = $instanceManager;
        $this->entityManager = $entityManager;
        $this->util = $util;
        $this->databaseUtil = $databaseUtil;
        $this->impressaoUtil = $impressaoUtil;
        $this->impressaoSATService = $impressaoSATService;
		$this->satRequest = $satRequest;
		$this->nfceRequest = $nfceRequest;
		$this->utilizaImpressaoPonte = $this->instanceManager->getParameter('UTILIZA_IMPRESSAO_PONTE');
	}

	public function abreCaixa($CDFILIAL, $DTABERCAIX, $CDCAIXA, $NRORG, $CDOPERADOR, $DTMOVTURCAIX, $VRMOVIVEND, $IDMONGO, $IDATUTURCAIXA, $deveImprimirSuprimento, $IDHABCAIXAVENDA, $IDSINCCAIXADLV) {
		try {
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			// validar primeiramente o index criado
			// Rotina para criacao de index, caso ele nao exista
			// $index = $this->entityManager->getConnection()->fetchAssoc("CHECK_INDEX", array('INDEXNAME' => 'BUSCA_CONSUMIDOR'));

			// if (!$index) {
			// 	$this->entityManager->getConnection()->executeQuery("CREATE_BUSCA_CONSUMIDOR");
			// }

			// Segue a abertura de caixa
			$estadoCaixa = $this->getEstadoCaixa($CDFILIAL, $CDCAIXA, $NRORG);
			$this->deleteOlderLogs();

			if ($estadoCaixa['estado'] == 'fechado') {
				$contador = 'MOVCAIXA' . $CDFILIAL . $CDCAIXA . $this->databaseUtil->dateTimeToString($DTABERCAIX);
				$NRSEQUMOVI = $this->util->geraCodigo($connection, $contador, $NRORG, 1, 10);
				$CDATIVASAT = self::getCDATIVASAT($CDFILIAL, $CDCAIXA, $NRORG);

                $NRCONFTELA = $this->util->getConfTela($CDFILIAL, $CDCAIXA);

				$params = array(
					'CDFILIAL' => $NRCONFTELA['CDFILIAL'],
					'CDCAIXA' => $CDCAIXA,
                    'NRCONFTELA' => $NRCONFTELA['NRCONFTELA'],
                    'DTINIVIGENCIA' => $NRCONFTELA['DTINIVIGENCIA'],
					'NRORG'=> $NRORG
				);
                $types = array(
                    'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
                );

				self::insertTurcaixa($CDFILIAL, $CDCAIXA, $DTABERCAIX, $CDOPERADOR, $DTMOVTURCAIX, $CDATIVASAT, $NRORG, $IDMONGO, $IDATUTURCAIXA);
				$tipoRecebimentoFundo = $connection->fetchAssoc("GET_CDTIPORECE_FUNDO", $params, $types);
				$CDTIPORECE = $tipoRecebimentoFundo['CDTIPORECE'] ? $tipoRecebimentoFundo['CDTIPORECE'] : null;
				$NMTIPORECE = $tipoRecebimentoFundo['NMTIPORECE'] ? $tipoRecebimentoFundo['NMTIPORECE'] : null;
				$IDTIPOMOVIVE = 'A';

				$VRMOVIVEND = !empty($VRMOVIVEND) ? $VRMOVIVEND : 0;
				$VRMOVIVEND = is_string($VRMOVIVEND) ? floatval(str_replace(',', '.', $VRMOVIVEND)) : $VRMOVIVEND;

				$mensagemImpressao = null;
				$params = array(
					'CDFILIAL' => $CDFILIAL,
					'CDCAIXA' => $CDCAIXA,
					'DTABERCAIX' => $DTABERCAIX,
					'IDTIPOMOVIVE' => $IDTIPOMOVIVE,
					'VRMOVIVEND' => $VRMOVIVEND,
					'CDTIPORECE' => $CDTIPORECE,
					'DTHRINCMOV' => $DTABERCAIX,
					'DTMOVIMCAIXA' => $DTMOVTURCAIX,
					'NRORG' => $NRORG,
					'NRORGINCLUSAO' => $NRORG,
					'CDOPERADOR' => $CDOPERADOR,
					'CDOPERINCLUSAO' => $CDOPERADOR,
					'IDMONGO' => $IDMONGO,
					'NRSEQUMOVI' => $NRSEQUMOVI,
					'NRSEQUMOVIMSDE' => $NRSEQUMOVI,
					'NRSEQVENDA' => null,
					'CDTPSANGRIA' => null,
					'DSOBSSANGRIACX' => null
				);
				$type = array(
					'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
					'DTHRINCMOV' => \Doctrine\DBAL\Types\Type::DATETIME,
					'DTMOVIMCAIXA' => \Doctrine\DBAL\Types\Type::DATE
				);
				$this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXA", $params, $type);

				$dadosImpressao = array();
				$issaas = $this->util->isSaas();

				if ($deveImprimirSuprimento) {
					$respostaImpressao = $this->imprimeSuprimento($CDFILIAL, $CDCAIXA, $CDOPERADOR, $CDTIPORECE, $VRMOVIVEND, $dadosImpressao);
					if ($respostaImpressao['error']) {
						$mensagemImpressao = 'Não foi possível imprimir o comprovante do suprimento. <br><br>' . $respostaImpressao['message'];
					}
				}

				$connection->commit();

				$successMessage = "Caixa aberto com sucesso.";
				$this->controlaFosSinc($IDHABCAIXAVENDA, $IDSINCCAIXADLV, 'start', $successMessage);

				if(empty($dadosImpressao) && $issaas){
					$dadosImpressao['paramsImpressora'] = $respostaImpressao;
				}

				return array(
					'error' => false,
					'message' => $successMessage,
					'mensagemImpressao' => $mensagemImpressao,
					'dadosImpressao' => $dadosImpressao
				);
			} else {
				return array(
					'error' => true,
					'message' => "O caixa " . $CDCAIXA . " se encontra aberto. Operação inválida."
				);
			}

		} catch(\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			if ($this->entityManager->getConnection()->isTransactionActive()) {
				$this->entityManager->getConnection()->rollBack();
			}
			return array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}
	}

	public function imprimeSuprimento($CDFILIAL, $CDCAIXA, $CDOPERADOR, $CDTIPORECE, $VRMOVIVEND, &$dadosImpressao) {
		$resposta = array(
			'error' => false
		);
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA
		);
		$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $params);
		if (!empty($dadosImpressora)) {
			$params = array(
				'CDOPERADOR' => $CDOPERADOR
			);
			$dadosOperador = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_OPERADOR", $params);
			if (!empty($dadosOperador)) {
				$params = array(
					'CDTIPORECE' => $CDTIPORECE
				);
				$dadosRecebimento = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_RECEBIMENTO", $params);
				if (!empty($dadosRecebimento)) {
					$printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
					$texto = '';
					$texto .= $this->impressaoUtil->centraliza($printerParams, 'COMPROVANTE DE SUPRIMENTO') . $printerParams['comandoEnter'];
					$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
					$texto .= 'CAIXA: ' . $dadosImpressora['CDCAIXA'] . ' - ' . $dadosImpressora['NMCAIXA'] . $printerParams['comandoEnter'];
					$data = new \DateTime();
					$data = $data->format('d/m/Y H:i:s');
					$texto .= 'DATA: ' . $data . $printerParams['comandoEnter'];
					$linhaOperador = 'OPERADOR: ' . $dadosOperador['CDOPERADOR'] . '-' . $dadosOperador['NMOPERADOR'];
					if (strlen($linhaOperador) > $printerParams['largura']){
						$linhaOperador = $this->impressaoUtil->centraliza($printerParams, $linhaOperador);
					}
					$texto .= $linhaOperador . $printerParams['comandoEnter'];
					$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
					$VRMOVIVEND = $this->impressaoUtil->formataNumero($VRMOVIVEND, 2);
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $dadosRecebimento['CDTIPORECE'] . ' ' . $dadosRecebimento['NMTIPORECE'], $VRMOVIVEND);
					if (!$printerParams['impressaoFront']){
						$printerParams['letterType'] = $printerParams['tipoLetra'];
						$comandos = new Command();
						$comandos->text($texto, $printerParams);
						$comandos->cutPaper();
						$issaas = $this->util->isSaas();
						if($issaas){
							$resposta = array(
								'saas'      => true,
								'impressora'=> $dadosImpressora,
								'comandos'  => $comandos->getCommands(),
								'error'     => false);
						}else{
							$resposta = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
						}
					} else {
						$dadosImpressao['open'] = $texto;
					}
				} else {
					$resposta = array(
						'error' => true,
						'message' => 'Recebimento não encontrado para realização do suprimento.'
					);
				}
			} else {
				$resposta = array(
					'error' => true,
					'message' => 'Operador não encontrado para realização do suprimento.'
				);
			}
		}
		return $resposta;
	}

	private function insertTurcaixa($CDFILIAL, $CDCAIXA, $DTABERCAIX, $CDOPERADOR, $DTMOVTURCAIX, $CDATIVASAT, $NRORG, $IDMONGO, $IDATUTURCAIXA) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTABERCAIX' => $DTABERCAIX,
			'CDOPERADOR' => $CDOPERADOR,
			'DTMOVTURCAIX' => $DTMOVTURCAIX,
			'CDATIVACAOSAT' => $CDATIVASAT,
			'NRORG' => $NRORG,
			'IDMONGO' => $IDMONGO,
			'IDATUTURCAIXA' => $IDATUTURCAIXA
		);

		$type = array(
			'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
			'DTMOVTURCAIX' => \Doctrine\DBAL\Types\Type::DATE
		);

		$this->entityManager->getConnection()->executeQuery("INSERT_TURCAIXA", $params, $type);
	}

	public function fechaCaixa($CLOSEPOSSIMPLE, $DTABERCAIX, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $CDOPERFECH, $NRCONFTELA, $TIPORECE, $deveImprimirRelatorio, $IDHABCAIXAVENDA, $IDSINCCAIXADLV) {
		$retornoFechaCaixa = array();
		$dadosImpressao = array();

		try {
			$this->entityManager->getConnection()->beginTransaction();
			$estadoCaixa = $this->getEstadoCaixa($CDFILIAL, $CDCAIXA, $NRORG);

			if ($estadoCaixa['estado'] == 'aberto') {

				if ($DTABERCAIX == null) {
					$DTABERCAIX = self::getUltimaAberturaCaixa($CDFILIAL, $CDCAIXA);
				}

				$params = array(
					'CDFILIAL' => $CDFILIAL,
					'CDCAIXA'  => $CDCAIXA
				);
				$caixa = $this->entityManager->getConnection()->fetchAssoc("VERIFICA_TIPO_EMISSAO_CAIXA", $params);

				if($caixa['IDTPEMISSAOFOS'] === 'SAT') {
					$response = $this->updateSatSales(array_merge($caixa, $params));
					if($response['error']) {
						return $response;
					}
				} else {
					$this->inutilizaNFCE($CDFILIAL, $CDCAIXA, $DTABERCAIX, $NRORG);
				}

				$IDATUTURCAIXA = $estadoCaixa['IDATUTURCAIXA'] == 'S' ? 'A' : 'I';

				if ($CLOSEPOSSIMPLE == 'S') {
					$paramsMovCaixa = self::preparaParamsMovCaixa($DTABERCAIX, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $NRCONFTELA, $TIPORECE);
					// Insere uma linha na movcaixa para cada tipo de recebimento de sangria automática
					foreach ($paramsMovCaixa[0] as $dadosMovCaixa) {
						$this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXA_OPENPOS", $dadosMovCaixa, $paramsMovCaixa[1]);
					}
				}

				$paramsTurcaixa = self::preparaParamsTurcaixa($CDOPERFECH, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $IDATUTURCAIXA);
				$this->entityManager->getConnection()->executeQuery("UPDATE_DTFECHAMENTO_TURCAIXA", $paramsTurcaixa[0], $paramsTurcaixa[1]);

				$mensagemImpressao = null;
				$issaas = $this->util->isSaas();
				$paramsImpressora = [];
				if ($deveImprimirRelatorio) {
					$respostaImpressao = $this->imprimeSangriaFechamento($CDFILIAL, $CDCAIXA, $CDOPERFECH, $NRCONFTELA, $NRORG, $DTABERCAIX, $dadosImpressao);
					if($issaas){
						array_push($paramsImpressora, $respostaImpressao);
					}
					if (!$respostaImpressao['error']) {
						// Imprime parcial do dia
						$respostaImpressao = $this->imprimeLeituraX($CDFILIAL, $DTABERCAIX, $CDCAIXA, $CDOPERFECH, $NRORG, false, false, $dadosImpressao);
						if($issaas){
							array_push($paramsImpressora, $respostaImpressao);
						}
						if (!$respostaImpressao['error']) {
							// Imprime o dia inteiro
							$respostaImpressao = $this->imprimeLeituraX($CDFILIAL, $DTABERCAIX, $CDCAIXA, $CDOPERFECH, $NRORG, true, true, $dadosImpressao);
							if($issaas){
								array_push($paramsImpressora, $respostaImpressao);
							}
							if ($respostaImpressao['error']){
								$mensagemImpressao = 'Não foi possível imprimir o relatório Total Dia. <br><br>' . $respostaImpressao['message'];
							}
						} else {
							$mensagemImpressao = 'Não foi possível imprimir o relatório de fechamento. <br><br>' . $respostaImpressao['message'];
						}
					} else {
						$mensagemImpressao = 'Não foi possível imprimir a sangria do fechamento. <br><br>' . $respostaImpressao['message'];
					}
				}

				$this->entityManager->getConnection()->commit();

				$successMessage = "Caixa fechado com sucesso.";
				$this->controlaFosSinc($IDHABCAIXAVENDA, $IDSINCCAIXADLV, 'stop', $successMessage);

				if(empty($dadosImpressao) && $issaas){
					$dadosImpressao['paramsImpressora'] = $paramsImpressora;
				}

				$retornoFechaCaixa = array(
					'error' => false,
					'message' => $successMessage,
					'mensagemImpressao' => $mensagemImpressao,
					'dadosImpressao' => $dadosImpressao
				);
			} else {
				$retornoFechaCaixa = array(
					'error' => true,
					'message' => "Não existe um caixa aberto para executar o fechamento. Operação inválida."
				);
			}
		} catch(\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			if ($this->entityManager->getConnection()->isTransactionActive()) {
				$this->entityManager->getConnection()->rollBack();
			}
			$retornoFechaCaixa = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		return $retornoFechaCaixa;
	}

	private function updateSatSales($estadoCaixa) {
		$satData = $this->validateSAT($estadoCaixa);

		if($satData['error']) {
			return $satData;
		} else {
			$satResponse = explode("|", $satData['satResponse']);
			if(!empty($satResponse[20])) {
				$params = array (
					'CDFILIAL'     => $estadoCaixa['CDFILIAL'],
					'CDCAIXA'      => $estadoCaixa['CDCAIXA'],
					'NRACESSONFCE' => $satResponse[20]
				);

				$ultimaVenda = $this->entityManager->getConnection()->fetchAssoc("GET_ULTIMA_VENDA_SAT", $params);
				if(!empty($ultimaVenda)) {
					$params['NRSEQVENDA'] = $ultimaVenda['NRSEQVENDA'];

					$this->entityManager->getConnection()->executeQuery("UPDATE_VENDA_INSERIDA_SAT", $params);
					$this->entityManager->getConnection()->executeQuery("UPDATE_VENDA_ALTERADA_SAT", $params);
				}
			}

			return array ('error' => false, 'message' => '');
		}
	}

	private function preparaParamsMovCaixa($DTABERCAIX, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $NRCONFTELA, $TIPORECE) {
		$connection = $this->entityManager->getConnection();

		$dadosMovCaixa = array();

		$contador = 'MOVCAIXA' . $CDFILIAL . $CDCAIXA . $this->databaseUtil->dateTimeToString($DTABERCAIX);

        $NRCONFTELA = $this->util->getConfTela($CDFILIAL, $CDCAIXA);

		$dadosSangriaAutomatica = self::buscaSangriaAutomatica($CDFILIAL, $CDCAIXA, $DTABERCAIX, $NRCONFTELA['CDFILIAL'], $NRCONFTELA['NRCONFTELA'], $NRCONFTELA['DTINIVIGENCIA']);
		foreach ($dadosSangriaAutomatica as $sangria) {
			/*
			* G -> Sangria (Fechamento do caixa)
			* A -> Abertura
			* S -> Saída
			* E -> Entrada
			*/
			$IDTIPOMOVIVE = 'G';
			$NRSEQUMOVI = $this->util->geraCodigo($connection, $contador, $NRORG, 1, 10);

			$linhaMovCaixa = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'DTABERCAIX' => $DTABERCAIX,
				'NRSEQUMOVI' => $NRSEQUMOVI,
				'NRSEQUMOVIMSDE' => $NRSEQUMOVI,
				'IDTIPOMOVIVE' => $IDTIPOMOVIVE,
				'VRMOVIVEND' => $sangria['VRMOVIVEND'],
				'CDTIPORECE' => $sangria['CDTIPORECE'],
				'DTHRINCMOV' => $DTFECHCAIX,
				'DTMOVIMCAIXA' => $DTABERCAIX,
				'NRORG' => $NRORG
			);

			array_push($dadosMovCaixa, $linhaMovCaixa);
		}

		foreach($TIPORECE as $sangria){
			if ($sangria['IDSANGRIAAUTO'] === 'N' && $sangria['VRMOVIVEND'] > 0){
				$IDTIPOMOVIVE = 'G';
				$NRSEQUMOVI = $this->util->geraCodigo($connection, $contador, $NRORG, 1, 10);

				$linhaMovCaixa = array(
					'CDFILIAL' => $CDFILIAL,
					'CDCAIXA' => $CDCAIXA,
					'DTABERCAIX' => $DTABERCAIX,
					'NRSEQUMOVI' => $NRSEQUMOVI,
					'NRSEQUMOVIMSDE' => $NRSEQUMOVI,
					'IDTIPOMOVIVE' => $IDTIPOMOVIVE,
					'VRMOVIVEND' => $sangria['VRMOVIVEND'],
					'CDTIPORECE' => $sangria['CDTIPORECE'],
					'DTHRINCMOV' => $DTFECHCAIX,
					'DTMOVIMCAIXA' => $DTABERCAIX,
					'NRORG' => $NRORG
				);
				array_push($dadosMovCaixa, $linhaMovCaixa);
			}

		}

		$types = array(
			'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME,
			'DTHRINCMOV' => \Doctrine\DBAL\TypeS\Type::DATETIME,
			'DTMOVIMCAIXA' => \Doctrine\DBAL\TypeS\Type::DATE
		);

		return array($dadosMovCaixa, $types);
	}

	private function preparaParamsTurcaixa($CDOPERFECH, $DTFECHCAIX, $CDFILIAL, $CDCAIXA, $NRORG, $IDATUTURCAIXA) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTFECHCAIX' => $DTFECHCAIX,
			'CDOPERFECH' => $CDOPERFECH,
			'NRORG' => $NRORG,
			'IDATUTURCAIXA' => $IDATUTURCAIXA
		);
		$type = array(
			'DTFECHCAIX' => \Doctrine\DBAL\Types\Type::DATETIME
		);

		return array($params, $type);
	}

	public function checaEstadoFiscal($CDFILIAL, $CDCAIXA, $NRORG) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA'  => $CDCAIXA
		);
		$estadoCaixa = $this->entityManager->getConnection()->fetchAssoc("VERIFICA_TIPO_EMISSAO_CAIXA", $params);
		$resultado = array(
			'error' => true,
			'message' => null
		);

		if (!isset($estadoCaixa['IDTPEMISSAOFOS'])) {
			$resultado['message'] = 'Caixa sem tipo de emissão parametrizado.';
		} else {
			switch($estadoCaixa['IDTPEMISSAOFOS']) {
				case 'ECF':
					$resultado['message'] = 'Tipo emissão do caixa deve ser SAT ou NFC-e.';
					break;
				case 'SAT':
					$resultado = $this->validateSAT($estadoCaixa);
					if($resultado['error']){
						$resultado['message'] = 'Não foi possível comunicar com o SAT. <br><br>' . $resultado['message'];
					}
					break;
				case 'FNC': // NFCE

					$resultado = $this->validateNFCE($CDFILIAL, $CDCAIXA, $NRORG);
					break;
			}
		}

		if(!$resultado['error']){
			//valida impressora
			if(!$this->utilizaImpressaoPonte){
				$resultado = array(
					'error' => true,
					'message' => 'Não é possível abrir caixa recebedor sem utilizar o Odhen-periféricos.'
				);
			} else {
				if (!empty($estadoCaixa['IDMODEIMPRES'])){

					$printerParams = $this->impressaoUtil->buscaParametrosImpressora($estadoCaixa['IDMODEIMPRES']);


					if (!$printerParams['impressaoFront']){
						$issaas = $this->util->isSaas();

						if($issaas){
							$validaImpressora['paramsImpressora'] = $estadoCaixa;
						}else{
							$validaImpressora = $this->impressaoUtil->requisicaoPonteTest($estadoCaixa);
						}
						$resultado = array_merge($resultado, $validaImpressora);
					}

					if($resultado['error']){
						$resultado['message'] = 'Não foi possível comunicar com a impressora. <br><br>' . $resultado['message'];
					}
				} else {
					$resultado = array(
						'error' => true,
						'message' => 'Não é possível abrir caixa recebedor sem uma impressora válida vinculada.'
					);
				}
			}
		}

		return $resultado;
	}

	private function validateNFCE($CDFILIAL, $CDCAIXA, $NRORG){
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

			$result = $this->nfceRequest->validateDataNfce($nfceData);
			if (!$result['error']){
				$filialInfo = array(
					'CDFILIAL' => $CDFILIAL,
					'CDCAIXA' => $CDCAIXA,
					'NRORG' => $NRORG
				);
				$this->nfceRequest->createPath($nfceData['IDAMBTRABNFCE'], $filialInfo, new \DateTime());
				$nfceData['CDFILIAL'] = $CDFILIAL;
				$nfceData['CDCAIXA'] = $CDCAIXA;
				$nfceData['NRORG'] = $NRORG;
				$this->nfceRequest->getNFCeCert($nfceData);
			}
		} catch (\Exception $e) {
			Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

	    return $result;
	}

	private function validateSAT($estadoCaixa){
		$SATValidation = $this->validateSATParams($estadoCaixa);
		if ($SATValidation['error'] == false) {
			$SATValidation = $this->_validateSat($estadoCaixa);
            //Criação de flag, utilizada no odhenPOS para evitar bloqueio no login pela não comunicação com o SAT.
			$SATValidation['login'] = $SATValidation['error'] === true ? true : false;
		}

        return $SATValidation;

	}

    private function validateSATParams($printarParams) {
        $result = array(
            'error' => true,
            'message' => ''
        );

        if (empty($printarParams['CDATIVASAT'])) {
            $result['message'] = 'CDATIVASAT não parametrizado.';
        } else if (empty($printarParams['DSSATHOST'])) {
            $result['message'] = 'DSSATHOST não parametrizado.';
        }
         else {
            $result['error'] = false;
        }

        return $result;
    }

    private function _validateSat($estadoCaixa) {
        $this->satRequest->setSatInfo($estadoCaixa);
        $respostaConsulta = $this->satRequest->consultarSAT();
        if ($respostaConsulta['error'] == false) {
            return $this->satRequest->consultarStatusOperacional($estadoCaixa['CDATIVASAT']);
        }else{
            return $respostaConsulta;
        }
    }

    public function getEstadoCaixa($CDFILIAL, $CDCAIXA, $NRORG) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA'  => $CDCAIXA,
			'NRORG'    => $NRORG
		);
		$estadoCaixa = $this->entityManager->getConnection()->fetchAll("VERIFICA_ABERTURA_ESTADO_CAIXA", $params);
		if (empty($estadoCaixa) == true) {
			return array(
				'estado' => 'fechado'
			);
		} else {
			$IDOBRIGFECHCAIX = $this->entityManager->getConnection()->fetchAssoc("SQL_OBRIGA_FECH", $params)['IDOBRIGFECHCAIX'];

			return array(
				'estado' => 'aberto',
				'DTABERCAIX' => $estadoCaixa[0]['DTABERCAIX'],
				'DTFECHCAIX' => $estadoCaixa[0]['DTFECHCAIX'],
				'IDATUTURCAIXA' => $estadoCaixa[0]['IDATUTURCAIXA'],
				'CDOPERADOR' => $estadoCaixa[0]['CDOPERADOR'],
				'NMOPERADOR' => $estadoCaixa[0]['NMOPERADOR'],
				'obrigaFechamento' => self::obrigaFechamento($estadoCaixa[0]['DTABERCAIX'], $IDOBRIGFECHCAIX)
			);
		}
	}

	public function obrigaFechamento($DTABERCAIX, $IDOBRIGFECHCAIX) {
		$tomorrow = new \DateTime($DTABERCAIX);
		$tomorrow->add(date_interval_create_from_date_string('1 days'));
		$now = new \DateTime();
		return ($now->format('Y-m-d') === $tomorrow->format('Y-m-d') && $IDOBRIGFECHCAIX === 'S') ||
			$now >= $tomorrow;
	}

    public function getUltimaAberturaCaixa($CDFILIAL, $CDCAIXA) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA'  => $CDCAIXA
        );

		$DTABERCAIX = $this->entityManager->getConnection()->fetchAssoc("VERIFICA_ABERTURA_ESTADO_CAIXA", $params);

        return self::convertToDateDB($DTABERCAIX['DTABERCAIX']);
    }

    public function convertToDateDB($dateString) {
        if (is_string($dateString)) {
            if ($this->databaseUtil->databaseIsOracle()) {
                return \DateTime::createFromFormat('Y-m-d H:i:s', $dateString);
            } else {
                return \DateTime::createFromFormat('Y-m-d H:i:s.u', $dateString);
            }
        }else{
            return $dateString;
        }
    }

    public function imprimeTotalDiaFechamento($CDFILIAL, $DTABERCAIX, $CDCAIXA, $CDOPERADOR, $NRORG, $flagFinal) {
        $resposta = array(
			'error' => false
		);
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);
		$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $params);
		if (!empty($dadosImpressora)) {
			$IDMODEIMPRES = $dadosImpressora['IDMODEIMPRES'];
			$CDPORTAIMPR = $dadosImpressora['CDPORTAIMPR'];
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'NRORG' => $NRORG
			);
			$printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);
			if (!$printerParams['impressaoFront']){
				$dadosFilial = $this->entityManager->getConnection()->fetchAll("GET_DADOS_FILIAL", $params);
				$NMRAZSOCFILI = $dadosFilial[0]['NMRAZSOCFILI'];
				$NRINSJURFILI = $this->util->aplicaMascaraCpfCnpj($dadosFilial[0]['NRINSJURFILI']);
				$DSENDEFILI = $dadosFilial[0]['DSENDEFILI'];
				$NMBAIRFILI = $dadosFilial[0]['NMBAIRFILI'];
				$NMMUNICIPIO = $dadosFilial[0]['NMMUNICIPIO'];
				$SGESTADO = $dadosFilial[0]['SGESTADO'];
				$CDINSCESTA = $dadosFilial[0]['CDINSCESTA'];
				$NMFILIAL = $dadosFilial[0]['NMFILIAL'];
				$CDINSCMUNI = $dadosFilial[0]['CDINSCMUNI'];
				$texto = '';
				$texto .= $this->impressaoSATService->cabecalhoCupomNF($printerParams, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI);
				$params = array(
					':CDOPERADOR' => $CDOPERADOR
				);
				$NMOPERADOR = $this->entityManager->getConnection()->fetchAssoc("GET_NMOPERADOR", $params)['NMOPERADOR'];
				if ($flagReducaoZ == true && $flagFinal == true) {
					$texto .= $this->impressaoUtil->centraliza($printerParams, 'RELATORIO FECHAMENTO DE CAIXA - TOTAL DIA') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
				} elseif ($flagReducaoZ == true) {
					$texto .= $this->impressaoUtil->centraliza($printerParams, 'RELATORIO FECHAMENTO DE CAIXA') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
				} else {
					$texto .= $this->impressaoUtil->centraliza($printerParams, 'RELATORIO PARCIAL DIA') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
				}

				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'FILIAL', $CDFILIAL) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'CAIXA', $CDCAIXA) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'DATA', $DTABERCAIX->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'OPERADOR', $CDOPERADOR.' - '.$NMOPERADOR) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
				$params = array(
					'CDFILIAL'   => $CDFILIAL,
					'CDCAIXA'    => $CDCAIXA,
					'DTABERTUR'  => $DTABERCAIX,
					'DTABERCAIX' => $DTABERCAIX
				);
				$types = array(
					'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATE,
					'DTABERTUR' => \Doctrine\DBAL\TypeS\Type::DATE
				);
				$VRVENDBRUT = $this->entityManager->getConnection()->fetchAssoc("GET_VRVENDBRUT", $params, $types)['VRVENDBRUT'];

				// $VRTOTINI = $this->entityManager->getConnection()->fetchAssoc("GET_VRTOTINI", $params)['VRTOTINI'];
				$NRTRANSACOES = $this->entityManager->getConnection()->fetchAssoc("GET_NRTRANSACOES", $params, $types)['NRTRANSACOES'];
				$NRPRIMSEQ = $this->entityManager->getConnection()->fetchAssoc("GET_NRPRIMSEQ", $params, $types)['NRPRIMSEQ'];
				$NRFINALSEQ = $this->entityManager->getConnection()->fetchAssoc("GET_NRFINALSEQ", $params, $types)['NRFINALSEQ'];
				$CANCELAMENTOS = $this->entityManager->getConnection()->fetchAssoc("GET_CANCELAMENTOS", $params, $types);

				$TRANSCANC = $CANCELAMENTOS['TRANSCANC'];
				$VRCANCEL = $CANCELAMENTOS['VRCANCEL'];
				$VRVENDBRUT += $VRCANCEL;
				if ($flagFinal == true) {
					$VRDESITVEND = $this->entityManager->getConnection()->fetchAssoc("GET_VRDESITVENDFIN", $params, $types)['VRDESITVENDFIN'];

					$VRACRITVEND = $this->entityManager->getConnection()->fetchAssoc("GET_VRACRITVENDFIN", $params, $types)['VRACRITVENDFIN'];

					$VRMOVIVEND  = $this->entityManager->getConnection()->fetchAssoc("GET_VRMOVIVENDFIN", $params, $types)['VRMOVIVENDFIN'];

				} else {
					$VRDESITVEND = $this->entityManager->getConnection()->fetchAssoc("GET_VRDESITVEND", $params, $types)['VRDESITVEND'];

					$VRACRITVEND = $this->entityManager->getConnection()->fetchAssoc("GET_VRACRITVEND", $params, $types)['VRACRITVEND'];

					$VRMOVIVEND  = $this->entityManager->getConnection()->fetchAssoc("GET_VRMOVIVEND", $params, $types)['VRMOVIVEND'];

				}

				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. DE TRANSACOES CONCLUIDAS' , $NRTRANSACOES) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. DO CUPOM INICIAL' , $NRPRIMSEQ) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. DO CUPOM FINAL' , $NRFINALSEQ) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. CUPONS CANCELADOS' , $TRANSCANC) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(1)VALOR VENDA BRUTA' , $this->impressaoUtil->formataNumero($VRVENDBRUT,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(2)VALOR DE CANCELAMENTO' , $this->impressaoUtil->formataNumero($VRCANCEL,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(3)VALOR TOTAL DESCONTOS' , $this->impressaoUtil->formataNumero($VRDESITVEND,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(4)VALOR TOTAL ACRESCIMOS' , $this->impressaoUtil->formataNumero($VRACRITVEND,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'VALOR VENDA LIQUIDA (1-2-3)' , $this->impressaoUtil->formataNumero($VRVENDBRUT-$VRDESITVEND-$VRCANCEL,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
				if ($flagFinal == true) {
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'VALOR FUNDO DE TROCO - TOTAL DIA' , $this->impressaoUtil->formataNumero($VRMOVIVEND,2)) . $printerParams['comandoEnter'];
				} else {
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'VALOR FUNDO DE TROCO' , $this->impressaoUtil->formataNumero($VRMOVIVEND,2)) . $printerParams['comandoEnter'];
				}
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
				$IMPOSTOS  = $this->entityManager->getConnection()->fetchAll("GET_IMPOSTOS", $params, $types);

				$quarter = floor($printerParams['largura']/4);
				$texto .= $this->impressaoUtil->centraliza($printerParams, 'IMPOSTOS') . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->preenche($quarter,  'SIGLA', ' ');
				$texto .= $this->impressaoUtil->preenche($quarter,  'ALIQ', ' ');
				$texto .= $this->impressaoUtil->preenche($quarter,  'VR.BASE', ' ');
				$texto .= $this->impressaoUtil->preenche($quarter,  'VR.IMP', ' ').$printerParams['comandoEnter'];

				foreach ($IMPOSTOS as $imposto) {
					$texto .= $this->impressaoUtil->preenche($quarter, $imposto['SGIMPOSTO'], ' ');
					$texto .= $this->impressaoUtil->preenche($quarter, $this->impressaoUtil->formataNumero($imposto['VRPEALPRODIT'],2), ' ');
					$texto .= $this->impressaoUtil->preenche($quarter, $this->impressaoUtil->formataNumero($imposto['VRBASE'],2), ' ');
					$texto .= $this->impressaoUtil->preenche($quarter, $this->impressaoUtil->formataNumero($imposto['VRIMPOSTO'],2), ' ') . $printerParams['comandoEnter'];
				}
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
				if ($flagFinal == true) {
					$PAGAMENTOS  = $this->entityManager->getConnection()->fetchAll("GET_PAGAMENTOSFIN", $params, $types);

					$texto .= $this->impressaoUtil->centraliza($printerParams, 'MEIOS DE PAGAMENTO RECEBIDOS - TOTAL DIA') . $printerParams['comandoEnter'];
				} else {
					$PAGAMENTOS  = $this->entityManager->getConnection()->fetchAll("GET_PAGAMENTOS", $params, $types);

					$texto .= $this->impressaoUtil->centraliza($printerParams, 'MEIOS DE PAGAMENTO RECEBIDOS') . $printerParams['comandoEnter'];
				}
				foreach ($PAGAMENTOS as $pagamento) {
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $pagamento['NMTIPORECE'] , $this->impressaoUtil->formataNumero($pagamento['VALOR_TOTAL'], 2)) . $printerParams['comandoEnter'];
					if ($pagamento['VRTROCO'] > 0){
						$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'TROCO - '.$pagamento['NMTIPORECE'] , $this->impressaoUtil->formataNumero($pagamento['VRTROCO'], 2)) . $printerParams['comandoEnter'];
					}
				}
				$now =  new \DateTime();
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
				$texto .= $this->impressaoUtil->centraliza($printerParams, $now->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->centraliza($printerParams, 'Teknisa Software - www.teknisa.com') . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);

				$printerParams['letterType'] = $printerParams['tipoLetra'];
				$comandos = new Command();
				$comandos->text($texto, $printerParams);
				$comandos->cutPaper();
				$resposta = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
			}
		}
		return $resposta;
	}

    public function imprimeSangriaFechamento($CDFILIAL, $CDCAIXA, $CDOPERADOR, $NRCONFTELA, $NRORG, $DTABERCAIX, &$dadosImpressao) {
		$resposta = array(
			'error' => false
		);

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);
		$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $params);
		if (!empty($dadosImpressora)) {
			if(is_null($DTABERCAIX)){
				$DTABERCAIX = self::getUltimaAberturaCaixa($CDFILIAL, $CDCAIXA);
			} else {
				$DTABERCAIX = $DTABERCAIX;
			}
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'DTABERCAIX' => self::convertToDateDB($DTABERCAIX)
			);
			$types = array(
				'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME
			);
			$sangriasRealizadas = $this->entityManager->getConnection()->fetchAll("GET_SANGRIA", $params, $types);

			$IDMODEIMPRES = $dadosImpressora['IDMODEIMPRES'];
			$CDPORTAIMPR = $dadosImpressora['CDPORTAIMPR'];
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'NRORG' => $NRORG
			);
			$printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);
			$dadosFilial = $this->entityManager->getConnection()->fetchAll("GET_DADOS_FILIAL", $params);
			$NMRAZSOCFILI = $dadosFilial[0]['NMRAZSOCFILI'];
			$NRINSJURFILI = $this->util->aplicaMascaraCpfCnpj($dadosFilial[0]['NRINSJURFILI']);
			$DSENDEFILI = $dadosFilial[0]['DSENDEFILI'];
			$NMBAIRFILI = $dadosFilial[0]['NMBAIRFILI'];
			$NMMUNICIPIO = $dadosFilial[0]['NMMUNICIPIO'];
			$SGESTADO = $dadosFilial[0]['SGESTADO'];
			$CDINSCESTA = $dadosFilial[0]['CDINSCESTA'];
			$NMFILIAL = $dadosFilial[0]['NMFILIAL'];
			$CDINSCMUNI = $dadosFilial[0]['CDINSCMUNI'];
			$texto = '';
			$texto .= $this->impressaoSATService->cabecalhoCupomNF($printerParams, $NMRAZSOCFILI, $NRINSJURFILI, $DSENDEFILI, $NMBAIRFILI, $NMMUNICIPIO, $SGESTADO, $CDINSCESTA, $CDINSCMUNI);
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'RELATORIO DE SAIDA NUMERARIO - SANGRIA') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];

			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'FILIAL', $CDFILIAL) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'CAIXA', $CDCAIXA) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'ABERTURA', $DTABERCAIX->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);

			//INICIO TOTAL VENDAS
			$params = array(
				'P_CDFILIAL' => $CDFILIAL,
				'P_CDCAIXA' => $CDCAIXA,
				'P_DTABERCAIX' => $DTABERCAIX
			);

			$types = array(
				'P_DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME
			);

			$vendas = $this->entityManager->getConnection()->fetchAll("TOTAL_VENDAS", $params, $types);

			$totalVendas = 0;

			foreach($vendas as $venda){
				$totalVendas += $venda['VRTOTAL'];
			}

			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,'TOTAL VENDAS', $this->impressaoUtil->formataNumero($totalVendas,2)) . $printerParams['comandoEnter'];
			//FIM TOTAL VENDAS

			//INICIO TOTAL CARTÃO
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'DTABERCAIX' => $DTABERCAIX,
				'FINAL' => ''
			);

			$types = array(
				'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME
			);

			$credito = $this->entityManager->getConnection()->fetchAssoc("TOTAL_CREDITO", $params, $types);


			$totalCredito = $credito['VRCREDITO'];

			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams,'Compra de Credito', $this->impressaoUtil->formataNumero($totalCredito,2)) . $printerParams['comandoEnter'];
			//FIM TOTAL CARTÃO

			//INICIO FORMAS DE PAGAMENTO
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= 'FORMA DE PAGAMENTO' . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);

			$params = array(
				'P_CDFILIAL' => $CDFILIAL,
				'P_CDCAIXA' => $CDCAIXA,
				'P_DTABERCAIX' => $DTABERCAIX,
				'P_NRCONFTELA' => $NRCONFTELA
			);
			$types = array(
				'P_DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME
			);

			$sangria = 0;
			$sobra = 0;
			$contra_vale_recebido = 0;
			$contra_vale_emitido = 0;
			$suprimento = 0;


			foreach ($sangriasRealizadas as $sangrias) {
				if ($sangrias['IDTIPOREG'] === '1') {
	                $sangria += $sangrias['VRSAIDA'];
	            	$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $sangrias['NMTIPORECE'], $this->impressaoUtil->formataNumero($sangrias['VRSAIDA'], 2)) . $printerParams['comandoEnter'];
            	}
			}

			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'CONTRA VALE EMITIDO (+)', $this->impressaoUtil->formataNumero($contra_vale_emitido,2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'CONTRA VALE RECEBIDO (-)', $this->impressaoUtil->formataNumero($contra_vale_recebido,2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'SUPRIMENTO (-)', $this->impressaoUtil->formataNumero($suprimento,2)) . $printerParams['comandoEnter'];

			//FIM FORMA DE PAGAMENTOS
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'SANGRIA', $this->impressaoUtil->formataNumero($sangria+$contra_vale_recebido-$suprimento-$contra_vale_emitido,2)) . $printerParams['comandoEnter'];

			if($sangria != $totalVendas){
				if($sangria > $totalVendas){
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'SOBRA', $this->impressaoUtil->formataNumero($sangria-$totalVendas,2)) . $printerParams['comandoEnter'];
				} else {
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'FALTA', '-' .$this->impressaoUtil->formataNumero($totalVendas-$sangria,2)) . $printerParams['comandoEnter'];
				}
			}

			$now =  new \DateTime();
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->centraliza($printerParams, $now->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'Teknisa Software - www.teknisa.com') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);

			if (!$printerParams['impressaoFront']){
				$printerParams['letterType'] = $printerParams['tipoLetra'];
				$comandos = new Command();
				$comandos->text($texto, $printerParams);
				$comandos->cutPaper();
				$issaas = $this->util->isSaas();
				if($issaas){
					$resposta = array(
						'saas'      => true,
						'impressora'=> $dadosImpressora,
						'comandos'  => $comandos->getCommands(),
						'error'     => false);
				}else{
					$resposta = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
				}
			} else {
				$dadosImpressao['sangria'] = $texto;
			}
		}

		return $resposta;
    }

	function buscaSangriaAutomatica($CDFILIAL, $CDCAIXA, $DTABERCAIX, $FILIALVIGENCIA, $NRCONFTELA, $DTINIVIGENCIA) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTABERCAIX' => $DTABERCAIX,
            'FILIALVIGENCIA' => $FILIALVIGENCIA,
			'NRCONFTELA' => $NRCONFTELA,
            'DTINIVIGENCIA' => $DTINIVIGENCIA
		);
		$types = array(
			'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME,
            'DTINIVIGENCIA' => \Doctrine\DBAL\TypeS\Type::DATETIME
		);

		return $this->entityManager->getConnection()->fetchAll("GET_SANGRIA_AUTOMATICA", $params, $types);
	}

	public function buscaTiporeceSangriaAutomatica($CDFILIAL, $CDCAIXA, $DTABERCAIX) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTABERCAIX' => $DTABERCAIX
		);
		$types = array(
			'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME
		);

		return $this->entityManager->getConnection()->fetchAll("GET_TIPORECE_SANGRIA_AUTOMATICA", $params, $types);
	}

	private function getCDATIVASAT($CDFILIAL, $CDCAIXA, $NRORG){
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);

		$CDATIVASAT = $this->entityManager->getConnection()->fetchAssoc("BUSCA_CDATIVASAT", $params);
		return !empty($CDATIVASAT['CDATIVASAT']) ? $CDATIVASAT['CDATIVASAT'] : null;
	}

	public function insereMovimentacao($recebimento, $TROCO, $CDFILIAL, $CDCAIXA, $CDOPERADOR, $NRORG){
        $connection = $this->entityManager->getConnection();

        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA'  => $CDCAIXA
        );
        $DTABERCAIX = $this->entityManager->getConnection()->fetchAssoc("VERIFICA_ABERTURA_ESTADO_CAIXA", $params);
        $DTABERCAIX = self::convertToDateDB($DTABERCAIX['DTABERCAIX']);

        $contador = 'MOVCAIXA' . $CDFILIAL . $CDCAIXA . $this->databaseUtil->dateTimeToString($DTABERCAIX);

        $CDTIPORECETROCO = null;
        $NRSEQUMOVI = $this->util->geraCodigo($connection, $contador, $NRORG, 1, 10);
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'DTABERCAIX' => $DTABERCAIX,
            'IDTIPOMOVIVE' => "E",
            'VRMOVIVEND' => $recebimento['VRMOVIVEND'],
            'CDTIPORECE' => $recebimento['CDTIPORECE'],
            'DTHRINCMOV' => new \DateTime(),
            'DTMOVIMCAIXA' => $DTABERCAIX,
            'NRORG' => $NRORG,
            'NRORGINCLUSAO' => $NRORG,
            'CDOPERINCLUSAO' => $CDOPERADOR,
            'NRSEQUMOVI' => $NRSEQUMOVI,
            'NRSEQUMOVIMSDE' => $NRSEQUMOVI,
            'NRSEQVENDA' => null,
			'CDTPSANGRIA' => null,
			'DSOBSSANGRIACX' => null
        );
        $type = array(
            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTHRINCMOV' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTMOVIMCAIXA' => \Doctrine\DBAL\Types\Type::DATE
        );
        $this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXA", $params, $type);

        if ($recebimento['IDTIPORECE'] == "4"){
            $CDTIPORECETROCO = $recebimento['CDTIPORECE'];
        }

        if ($TROCO > 0 && $CDTIPORECETROCO != null){
            $NRSEQUMOVI = $this->util->geraCodigo($connection, $contador, $NRORG, 1, 10);
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA,
                'DTABERCAIX' => $DTABERCAIX,
                'IDTIPOMOVIVE' => "S",
                'VRMOVIVEND' => $TROCO,
                'CDTIPORECE' => $CDTIPORECETROCO,
                'DTHRINCMOV' => new \DateTime(),
                'DTMOVIMCAIXA' => $DTABERCAIX,
                'NRORG' => $NRORG,
                'NRORGINCLUSAO' => $NRORG,
                'CDOPERINCLUSAO' => $CDOPERADOR,
                'NRSEQUMOVI' => $NRSEQUMOVI,
                'NRSEQUMOVIMSDE' => $NRSEQUMOVI,
                'NRSEQVENDA' => null,
				'CDTPSANGRIA' => null,
				'DSOBSSANGRIACX' => null
            );
            $type = array(
                'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
                'DTHRINCMOV' => \Doctrine\DBAL\Types\Type::DATETIME,
                'DTMOVIMCAIXA' => \Doctrine\DBAL\Types\Type::DATE
            );
            $this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXA", $params, $type);
        }

        return array('NRSEQUMOVI' => $NRSEQUMOVI, 'DTABERCAIX' => $DTABERCAIX);
    }

    public function buscaNumeroDeposito($CDCLIENTE, $CDCONSUMIDOR, $NRDEPOSICONS, $NRSEQMOVCAIXA){
        if ($NRSEQMOVCAIXA === -1 || $NRSEQMOVCAIXA === null || $NRSEQMOVCAIXA == ""){
            $NRSEQMOVCAIXA = '%%';
        }
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'NRDEPOSICONS' => $NRDEPOSICONS,
            'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA
        );
        $result = $this->entityManager->getConnection()->fetchAll("BUSCA_NRDEPOSICONS", $params);
        return $result;
	}

    public function cancelaMovimentacao($CDFILIAL, $CDCAIXA, $DTABERCAIX, $NRSEQMOVCAIXA){
        if ($this->databaseUtil->databaseIsOracle()){
            $DTABERCAIX = \DateTime::createFromFormat('Y-m-d H:i:s', $DTABERCAIX);
        }
        else {
            $DTABERCAIX = \DateTime::createFromFormat('Y-m-d H:i:s.u', $DTABERCAIX);
        }
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA'  => $CDCAIXA,
            'DTABERCAIX' => $DTABERCAIX,
            'NRSEQUMOVI' => $NRSEQMOVCAIXA
        );
        $type = array(
            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $this->entityManager->getConnection()->executeQuery("UPDATE_MOVCAIXA", $params, $type);
	}

	public function imprimeLeituraX($CDFILIAL, $DTABERCAIX, $CDCAIXA, $CDOPERADOR, $NRORG, $flagCaixa, $flagFinal, &$dadosImpressao) {
		if ($DTABERCAIX == null) {
			$DTABERCAIX = self::getUltimaAberturaCaixa($CDFILIAL, $CDCAIXA);
			$isLeituraX = true;
		} else {
			$isLeituraX = false;
		}
		$resposta = array(
			'error' => false
		);
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);
		$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $params);
		if (!empty($dadosImpressora)) {
			$IDMODEIMPRES = $dadosImpressora['IDMODEIMPRES'];
			$CDPORTAIMPR = $dadosImpressora['CDPORTAIMPR'];

			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'NRORG' => $NRORG
			);

			$printerParams = $this->impressaoUtil->buscaParametrosImpressora($IDMODEIMPRES);
			//Dados Gerais
			$dadosFilial = $this->entityManager->getConnection()->fetchAll("GET_DADOS_FILIAL", $params);
			$NMRAZSOCFILI = $dadosFilial[0]['NMRAZSOCFILI'];
			$NRINSJURFILI = $this->util->aplicaMascaraCpfCnpj($dadosFilial[0]['NRINSJURFILI']);
			$DSENDEFILI = $dadosFilial[0]['DSENDEFILI'];
			$NMBAIRFILI = $dadosFilial[0]['NMBAIRFILI'];
			$NMMUNICIPIO = $dadosFilial[0]['NMMUNICIPIO'];
			$SGESTADO = $dadosFilial[0]['SGESTADO'];
			$CDINSCESTA = $dadosFilial[0]['CDINSCESTA'];
			$NMFILIAL = $dadosFilial[0]['NMFILIAL'];
			$CDINSCMUNI = $dadosFilial[0]['CDINSCMUNI'];
			$texto = '';
			$now =  new \DateTime();
			$titulo = 'ODHEN - AUTOMACAO COMERCIAL';
			$tipoRelat = 'RELATORIO DE FECHAMENTO DE CAIXA';
			$textoFundoTroco = 'VALOR FUNDO DE TROCO';

			$params = array(
				'CDOPERADOR' => $CDOPERADOR
			);

			$dadosOperador = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_OPERADOR", $params);
			$NMOPERADOR = isset($dadosOperador) ? $dadosOperador['NMOPERADOR'] : '';

			if ($flagCaixa) {
				// Impressao via fechamento de caixa
				$tipoRelat .= ' - TOTAL DIA';
				$textoFundoTroco .= ' - TOTAL DIA';
			} else {
				// Impressao via tela de Funcoes Gerais > Impressao da Leitura X
				$titulo = $NMFILIAL;
				$tipoRelat = ($isLeituraX) ? 'RELATORIO PARCIAL DIA': $tipoRelat;
			}

			$DTABERCAIX = ($this->databaseUtil->databaseIsOracle() && $flagFinal) ? $DTABERCAIX->format('Y-m-d') : $DTABERCAIX;

			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'DTABERCAIX' => $DTABERCAIX,
				'FINAL' => $flagFinal ? 1 : 0
			);
			$types = ($this->databaseUtil->databaseIsOracle() && $flagFinal) ? array() : array('DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME);

			//Busca dados para preenchimento do relatorio
			$VRTOTINI = $this->entityManager->getConnection()->fetchAssoc("GET_VRTOTINI", $params, $types)['VRTOTINI'];
			$VRVENDBRUT = $this->entityManager->getConnection()->fetchAssoc("GET_VRVENDBRUT", $params, $types)['VRVENDBRUT'];
			$NRTRANSACOES = $this->entityManager->getConnection()->fetchAssoc("GET_NRTRANSACOES", $params, $types)['NRTRANSACOES'];
			$NRPRIMSEQ = $this->entityManager->getConnection()->fetchAssoc("GET_NRPRIMSEQ", $params, $types)['NRPRIMSEQ'];
			$NRFINALSEQ = $this->entityManager->getConnection()->fetchAssoc("GET_NRFINALSEQ", $params, $types)['NRFINALSEQ'];
			$CANCELAMENTOS = $this->entityManager->getConnection()->fetchAssoc("GET_CANCELAMENTOS", $params, $types);
			$ITENSCANC = $this->entityManager->getConnection()->fetchAssoc("GET_ITENS_CANCELADOS", $params, $types);
			$TRANSCANC = $CANCELAMENTOS['TRANSCANC'];
			$VRCANCEL = $CANCELAMENTOS['VRCANCEL'];
			$itensCancelados = $ITENSCANC['VRCANCEL'];
			$VRVENDBRUT += $VRCANCEL;
			$VRDESITVEND = $this->entityManager->getConnection()->fetchAssoc("GET_VRDESITVEND", $params, $types)['VRDESITVEND'];
			$VRACRITVEND = $this->entityManager->getConnection()->fetchAssoc("GET_VRACRITVEND", $params, $types)['VRACRITVEND'];
			$VRMOVIVEND  = $this->entityManager->getConnection()->fetchAssoc("GET_VRMOVIVEND", $params, $types)['VRMOVIVEND'];
			$compraCredito = $this->entityManager->getConnection()->fetchAssoc("TOTAL_CREDITO", $params, $types)['VRCREDITO'];
			$PAGAMENTOS  = $this->entityManager->getConnection()->fetchAll("GET_PAGAMENTOS", $params, $types);
			$IMPOSTOS  = $this->entityManager->getConnection()->fetchAll("GET_IMPOSTOS", $params, $types);

			//Construindo o relatorio
			//CABECALHO 1
			$texto .= $this->impressaoUtil->centraliza($printerParams, $titulo) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'CNPJ: ' . $NRINSJURFILI . '    IE: ' . $CDINSCESTA) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, $NMRAZSOCFILI) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, $NMBAIRFILI . ' - ' . $NMMUNICIPIO . ' - ' . $SGESTADO) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			//CABECALHO 2
			$texto .= $this->impressaoUtil->centraliza($printerParams, $tipoRelat) . $printerParams['comandoEnter'];
			$texto .= $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->preenche(floor($printerParams['largura']/5), 'FILIAL' , ' ') . $CDFILIAL . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->preenche(floor($printerParams['largura']/5), 'CAIXA' , ' ') . $CDCAIXA . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->preenche(floor($printerParams['largura']/5), 'DATA' , ' ') . $now->format('d/m/Y H:i:s') . $printerParams['comandoEnter'];
			$linhaOperador = $this->impressaoUtil->preenche(floor($printerParams['largura']/5), 'OPERADOR', ' ') . $CDOPERADOR . '-' . $NMOPERADOR;
			if (strlen($linhaOperador) > $printerParams['largura']){
				$linhaOperador = $this->impressaoUtil->centraliza($printerParams, $linhaOperador);
			}
			$texto .= $linhaOperador . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			if (!$flagCaixa && !$isLeituraX) {
				//TOTALIZADORES 1
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'TOTALIZADOR INICIAL' , $this->impressaoUtil->formataNumero($VRTOTINI,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'TOTALIZADOR FINAL' , $this->impressaoUtil->formataNumero($VRTOTINI - $VRVENDBRUT,2)) . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			}
			//CORPO 1
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. DE TRANSACOES CONCLUIDAS' , $NRTRANSACOES) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. DO CUPOM INICIAL' , $NRPRIMSEQ) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. DO CUPOM FINAL' , $NRFINALSEQ) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'NR. CUPONS CANCELADOS' , $TRANSCANC) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			//CORPO 2
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(1)VALOR VENDA BRUTA' , $this->impressaoUtil->formataNumero($VRVENDBRUT, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(2)VALOR CANCELAMENTO CUPONS' , $this->impressaoUtil->formataNumero($VRCANCEL, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(2)VALOR CANCELAMENTO ITENS' , $this->impressaoUtil->formataNumero($itensCancelados, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(3)VALOR TOTAL DESCONTOS' , $this->impressaoUtil->formataNumero($VRDESITVEND, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, '(4)VALOR TOTAL ACRESCIMOS' , $this->impressaoUtil->formataNumero($VRACRITVEND, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'VALOR VENDA LIQUIDA (1-2-3)' , $this->impressaoUtil->formataNumero($VRVENDBRUT-$VRDESITVEND-$VRCANCEL, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $textoFundoTroco , $this->impressaoUtil->formataNumero($VRMOVIVEND, 2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			//IMPOSTOS
			if ($flagCaixa) {
				$quarter = floor($printerParams['largura']/4);
				$texto .= $this->impressaoUtil->centraliza($printerParams, 'IMPOSTOS') . $printerParams['comandoEnter'];
				$texto .= $this->impressaoUtil->preenche($quarter,  'SIGLA', ' ');
				$texto .= $this->impressaoUtil->preenche($quarter,  'ALIQ', ' ');
				$texto .= $this->impressaoUtil->preenche($quarter,  'VR.BASE', ' ');
				$texto .= $this->impressaoUtil->preenche($quarter,  'VR.IMP', ' ').$printerParams['comandoEnter'];
				foreach ($IMPOSTOS as $imposto) {
					$texto .= $this->impressaoUtil->preenche($quarter, $imposto['SGIMPOSTO'], ' ');
					$texto .= $this->impressaoUtil->preenche($quarter, $this->impressaoUtil->formataNumero($imposto['VRPEALPRODIT'],2), ' ');
					$texto .= $this->impressaoUtil->preenche($quarter, $this->impressaoUtil->formataNumero($imposto['VRBASE'],2), ' ');
					$texto .= $this->impressaoUtil->preenche($quarter, $this->impressaoUtil->formataNumero($imposto['VRIMPOSTO'],2), ' ') . $printerParams['comandoEnter'];
				}
				$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			}
			//TOTALIZADORES 2
			$texto .= $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'TOTALIZADORES') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'COMPRA DE CREDITO' , $this->impressaoUtil->formataNumero($compraCredito,2)) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			//FINAL 1
			$texto .= $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'MEIOS DE PAGAMENTO RECEBIDOS') . $printerParams['comandoEnter'];
			foreach ($PAGAMENTOS as $pagamento) {
				$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $pagamento['NMTIPORECE'] , $this->impressaoUtil->formataNumero($pagamento['VALOR_TOTAL'], 2)) . $printerParams['comandoEnter'];
			}
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$linha = null;
			foreach ($PAGAMENTOS as $pagamento) {
				if ($pagamento['VRTROCO'] > 0){
					$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'TROCO '.$pagamento['NMTIPORECE'] , $this->impressaoUtil->formataNumero($pagamento['VRTROCO'], 2)) . $printerParams['comandoEnter'];
					$linha = empty($linha) ? $this->impressaoUtil->imprimeLinha($printerParams) : '';
				}
			}
			$texto .= $linha = !empty($linha) ? $linha : '';

			//FINAL 2
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'DATA ' . $now->format('d/m/Y') . ' HORA ' . $now->format('H:i:s')) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'Teknisa Software - www.teknisa.com') . $printerParams['comandoEnter'];

			if (!$printerParams['impressaoFront']){
				$printerParams['letterType'] = $printerParams['tipoLetra'];
				$comandos = new Command();
				$comandos->text($texto, $printerParams);
				$comandos->cutPaper();
				$issaas = $this->util->isSaas();
				if($issaas){
					$resposta = array(
						'saas'      => true,
						'impressora'=> $dadosImpressora,
						'comandos'  => $comandos->getCommands(),
						'error'     => false);
				}else{
					$resposta = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
				}
				$resposta = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
			} else {
				if ($flagCaixa){
					$dadosImpressao['totaldia'] = $texto;
				} else {
					$dadosImpressao['parcial'] = $texto;
				}
			}
		}
		return $resposta;
	}

	public function inutilizaNFCE($CDFILIAL, $CDCAIXA, $DTABERCAIX, $NRORG) {
		$DTABERCAIX = ($this->databaseUtil->databaseIsOracle()) ? $DTABERCAIX->format('Y-m-d H:i:s') : $DTABERCAIX;
		$params = array (
			'DTABERCAIX' => $DTABERCAIX,
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA
		);
		$types = ($this->databaseUtil->databaseIsOracle()) ? array() : array('DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME);

		$paramVendas = $this->entityManager->getConnection()->fetchAll("BUSCA_DATA_ULTIMA_VENDA", $params, $types);
		if (!empty($paramVendas)) {
			$vendasPorSerieNFCE = array();
			foreach($paramVendas as $paramVenda){
				$paramVenda['CDSERIENFCE'] = empty($paramVenda['CDSERIENFCE']) ? $CDCAIXA : $paramVenda['CDSERIENFCE'];
				if (array_key_exists($paramVenda['CDSERIENFCE'], $vendasPorSerieNFCE)) {
					array_push($vendasPorSerieNFCE[$paramVenda['CDSERIENFCE']], $paramVenda);
				} else {
					$vendasPorSerieNFCE[$paramVenda['CDSERIENFCE']][0] = $paramVenda;
				}
			}

			foreach ($vendasPorSerieNFCE as $vendaPorSerieNFCE) {
				$CDSERIENFCE = end($vendaPorSerieNFCE)['CDSERIENFCE'];
				$IDTPAMBNFCE = end($vendaPorSerieNFCE)['IDTPAMBNFCE'];
				$nrNFCE = array_map("intval", array_column($vendaPorSerieNFCE,'NRNOTAFISCALCE'));
				$params = array ( 'CDCONTADOR' => 'NFCE_' . $CDFILIAL . $CDSERIENFCE . '_AMB' . $IDTPAMBNFCE);
				$contadorNRNOTAFISCALCE = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_CONTADOR", $params);
				if(end($nrNFCE) < intval($contadorNRNOTAFISCALCE['NRSEQUENCIAL'])) {
					array_push($nrNFCE, (intval($contadorNRNOTAFISCALCE['NRSEQUENCIAL']) + 1));
				}
				$notasAusentes = array_diff(range($nrNFCE[0] , end($nrNFCE), 1), $nrNFCE);
				if (!empty($notasAusentes)) {
					foreach ($notasAusentes as $notaAusente) {
						$notaAusente = str_pad($notaAusente, 9, "0", STR_PAD_LEFT);
						$params = array(
							'CDCAIXA' => $CDCAIXA,
							'CDFILIAL' => $CDFILIAL,
							'CDSERIENFCE' => $CDSERIENFCE,
							'NRNOTAFISCALCE' => $notaAusente
						);
						$notaInutilizada = $this->entityManager->getConnection()->fetchAll("BUSCA_VENDA_INUTILIZADA", $params);

						if (empty($notaInutilizada)){
							$this->insertInutilizaNFCE($CDFILIAL, $CDCAIXA, $notaAusente, $DTABERCAIX, $CDSERIENFCE, $IDTPAMBNFCE, $NRORG);
						}
					}
				}
			}
		}
	}

	public function insertInutilizaNFCE($CDFILIAL, $CDCAIXA, $NRNOTAFISCALCE, $DTENTRVENDA, $CDSERIENFCE, $IDTPAMBNFCE, $NRORG){
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRNOTAFISCALCE' => $NRNOTAFISCALCE,
			'DTENTRVENDA' => self::convertToDateDB($DTENTRVENDA),
			'CDSERIENFCE' => $CDSERIENFCE,
			'IDTPAMBNFCE' => $IDTPAMBNFCE,
			'IDSTATUSNFCE' => 'P',
			'DTHRINUTNFCE' => new \DateTime(),
			'NRPROTOINUTNFCE' => NULL,
			'IDTPEMISINUNFCE' => NULL,
			'DSRAZAOINUTNFCE' => NULL,
			'DSOBSINUTNFCE' => NULL,
			'IDIMPINUTILIZA' => 'N',
			'NRORG' => $NRORG
		);
		$types = array(
			'DTENTRVENDA' => \Doctrine\DBAL\TypeS\Type::DATE,
			'DTHRINUTNFCE' => \Doctrine\DBAL\TypeS\Type::DATETIME
		);
		$this->entityManager->getConnection()->executeQuery("INSERT_INUTILIZANFCE", $params, $types);
	}

    public function buscaDadosTaxa($CDFILIAL, $CDLOJA){
        $params = array (
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA
        );
        $dadosTaxa = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_TAXA_SERVICO", $params);
        return $dadosTaxa;
    }

    public function saveSangria($session, $itemsSangria, $imprimeSangria){
    	try {
	    	$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();
			$DTABERCAIX = self::getUltimaAberturaCaixa($session['CDFILIAL'], $session['CDCAIXA']);

	        for ($i=0; $i<count($itemsSangria); $i++) {
	        	$contador = 'MOVCAIXA' . $session['CDFILIAL'] . $session['CDCAIXA'] . $this->databaseUtil->dateTimeToString($DTABERCAIX);
				$NRSEQUMOVI = $this->util->geraCodigo($connection, $contador, $session['NRORG'], 1, 10);
				$LABELVRMOVIVEND = !empty($itemsSangria[$i]['valorSangria']) ? $itemsSangria[$i]['valorSangria'] : 0;
				$VRMOVIVEND = is_string($LABELVRMOVIVEND) ? floatval(str_replace(',', '.', $LABELVRMOVIVEND)) : $LABELVRMOVIVEND;
				$tiposRecebimento[$i] = array(
					'CDTIPORECE' => $itemsSangria[$i]['CDTIPORECE'],
		      		'NMTIPORECE' => $itemsSangria[$i]['tipoRecebimento'],
		      		'VRMOVIVEND' => $VRMOVIVEND,
		      		'LABELVRMOVIVEND' => $LABELVRMOVIVEND,
		      		'IDSANGRIAAUTO' => 'N'
				  );

				$CDTPSANGRIA = isset($itemsSangria[$i]['CDTPSANGRIA']) ? $itemsSangria[$i]['CDTPSANGRIA'] : null;

		        $params = array(
		            'CDFILIAL' => $session['CDFILIAL'],
		            'CDCAIXA' => $session['CDCAIXA'],
		            'DTABERCAIX' => $DTABERCAIX,
		            'IDTIPOMOVIVE' => "G",
		            'VRMOVIVEND' => $VRMOVIVEND,
		            'CDTIPORECE' => $itemsSangria[$i]['CDTIPORECE'],
		            'DTHRINCMOV' => new \DateTime(),
		            'DTMOVIMCAIXA' => $DTABERCAIX,
		            'NRORG' => $session['NRORG'],
		            'NRORGINCLUSAO' => $session['NRORG'],
		            'CDOPERINCLUSAO' => $session['CDOPERADOR'],
		            'NRSEQUMOVI' => $NRSEQUMOVI,
		            'NRSEQUMOVIMSDE' => $NRSEQUMOVI,
		            'NRSEQVENDA' => null,
					'CDTPSANGRIA' => $CDTPSANGRIA,
					'DSOBSSANGRIACX' => $itemsSangria[$i]['obsSangria']
		        );
		        $types = array(
		            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
		            'DTHRINCMOV' => \Doctrine\DBAL\Types\Type::DATETIME,
		            'DTMOVIMCAIXA' => \Doctrine\DBAL\Types\Type::DATE
		        );
		        $connection->executeQuery("INSERT_MOVCAIXA", $params, $types);
	    	}
	    	$connection->commit();
	        $dadosImpressao = array();
	        $mensagemImpressao = null;

    		if ($imprimeSangria) {
    			$respostaImpressao = $this->imprimeSangria($session['CDFILIAL'], $session['CDCAIXA'], $session['CDOPERADOR'], null, $session['NRORG'], $DTABERCAIX, $tiposRecebimento, $dadosImpressao);
    			if ($respostaImpressao['error']) {
		        	$mensagemImpressao = 'Não foi possível imprimir o relatório da sangria de caixa. <br><br>' . $respostaImpressao['message'];
				}

    		}
	        return array(
				'error' => false,
				'message' => null,
				'mensagemImpressao' => $mensagemImpressao,
				'dadosImpressao' => $dadosImpressao
			);

	    } catch(\Exception $e) {
			if ($connection != null) $connection->rollback();
			return array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}
    }

    private function imprimeSangria($CDFILIAL, $CDCAIXA, $CDOPERADOR, $NRCONFTELA, $NRORG, $DTABERCAIX, $TIPORECE, &$dadosImpressao) {
		$resposta = array(
			'error' => false
		);
		if(is_null($DTABERCAIX)){
			$DTABERCAIX = self::getUltimaAberturaCaixa($CDFILIAL, $CDCAIXA);
		}

		$paramsImpressora = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'NRORG' => $NRORG
		);
		$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_IMPRESSORA", $paramsImpressora);

		$paramsOperador = array('CDOPERADOR' => $CDOPERADOR);
		$NOMEOPERADOR = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_OPERADOR", $paramsOperador);

		$paramsSangria = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTABERCAIX' => self::convertToDateDB($DTABERCAIX)
		);
		$types = array(
			'DTABERCAIX' => \Doctrine\DBAL\TypeS\Type::DATETIME
		);
		$sangriasRealizadas = $this->entityManager->getConnection()->fetchAll("GET_SANGRIA", $paramsSangria, $types);

		if (!empty($dadosImpressora)) {
			$TEKNISA = 'TEKNISA SERVICE -  www.teknisa.com';
			$paramsFilial = array(
				'CDFILIAL' => $CDFILIAL,
				'NRORG' => $NRORG
			);
			$dadosFilial = $this->entityManager->getConnection()->fetchAll("GET_DADOS_FILIAL", $paramsFilial);
			$printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
			$texto = '';

			// INICIO PRIMEIRO CABECALHO
			$texto .= $this->impressaoUtil->centraliza($printerParams, $dadosFilial[0]['NMRAZSOCFILI']) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'CNPJ: ' . $dadosFilial[0]['NRINSJURFILI'] . ' IE:'. $dadosFilial[0]['CDINSCESTA']) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'END. FILIAL') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, $dadosFilial[0]['NMBAIRFILI'] . ' - ' .  $dadosFilial[0]['NMMUNICIPIO'] . ' - ' . $dadosFilial[0]['SGESTADO']) . $printerParams['comandoEnter'];
			// FIM PRIMEIRO CABECALHO

			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'RECIBO SAIDA NUMERARIO') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];

			$now =  new \DateTime();
			$texto .= 'CAIXA' . '     ' . $CDCAIXA . $printerParams['comandoEnter'];
			$texto .= 'DATA' . '      ' . $now->format('d/m/Y') . $printerParams['comandoEnter'];
			$texto .= 'OPERADOR' . '  ' . $CDOPERADOR . '-' . $NOMEOPERADOR['NMOPERADOR'] . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'FORMA DE PAGAMENTO', 'VALOR') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);

			foreach ($TIPORECE as $tiposRecebimentos) {
            	$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $tiposRecebimentos['NMTIPORECE'], 'R$' . $this->impressaoUtil->formataNumero($tiposRecebimentos['VRMOVIVEND'], 2)) . $printerParams['comandoEnter'];
			}

			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'EMISSAO ' . $now->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->centraliza($printerParams, $TEKNISA) . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams). $printerParams['comandoEnter']. $printerParams['comandoEnter']. $printerParams['comandoEnter'];

			if (!$printerParams['impressaoFront']){
				$texto .= $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
			}
			// INICIO SEGUNDO CABECALHO
			$texto .= $this->impressaoUtil->centraliza($printerParams, 'SAIDA DE NUMERARIO - SANGRIA') . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];

			if (!$printerParams['impressaoFront']){
				$texto .= $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
			}

			$texto .= 'CAIXA' . '     ' . $CDCAIXA . $printerParams['comandoEnter'];
			$texto .= 'DATA' . '      ' . $now->format('d/m/Y') . $printerParams['comandoEnter'];
			$texto .= 'OPERADOR' . '  ' .   $CDOPERADOR . '-' . $NOMEOPERADOR['NMOPERADOR'] . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, 'FORMA DE PAGAMENTO', 'VALOR') . $printerParams['comandoEnter'];
			$texto .= $this->impressaoUtil->imprimeLinha($printerParams);

			foreach ($sangriasRealizadas as $sangria) {
				if ($sangria['IDTIPOREG'] === '1') {
            		$texto .= $this->impressaoUtil->alinhaInicioFim($printerParams, $sangria['NMTIPORECE'],  'R$' . $this->impressaoUtil->formataNumero($sangria['VRSAIDA'], 2)) . $printerParams['comandoEnter'];
            	}
			}

            $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
            $texto .= 'OPERADOR' . ' ' .  $NOMEOPERADOR['NMOPERADOR'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'] . $printerParams['comandoEnter'];
            $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
            $texto .= $this->impressaoUtil->centraliza($printerParams, 'EMISSAO ' . $now->format('d/m/Y H:i:s')) . $printerParams['comandoEnter'];
            $texto .= $this->impressaoUtil->centraliza($printerParams, $TEKNISA) . $printerParams['comandoEnter'];
            $texto .= $this->impressaoUtil->imprimeLinha($printerParams);
			// FIM SEGUNDO CABECALHO

			if (!$printerParams['impressaoFront']){
				$printerParams['letterType'] = $printerParams['tipoLetra'];
				$comandos = new Command();
				$comandos->text($texto, $printerParams);
				$comandos->cutPaper();
				$resposta = $this->impressaoUtil->requisicaoPonte($dadosImpressora, $comandos);
			} else {
				$dadosImpressao['sangria'] = $texto;
			}
		}

		return $resposta;
    }

    private function controlaFosSinc($IDHABCAIXAVENDA, $IDSINCCAIXADLV, $command, &$message){
		if($IDHABCAIXAVENDA == 'EVB' && $IDSINCCAIXADLV == 'S'){
			try{
				shell_exec('net '.$command.' FOSSINC');
				shell_exec('net '.$command.' FosImp');
				shell_exec('net '.$command.' FosExp');
			} catch(\Exception $e){
				Exception::logException($e, Exception::LOG_TYPES['EXCEPTION_LOG']);
				$message .= '<br><br> Ocorreu um problema ao manipular o serviço do sincronizador de pedidos.';
			}
		}
    }

    private function deleteOlderLogs() {
    	$DIAS_LOG_VENDA = $this->instanceManager->getParameter('DIAS_LOG_VENDA');

    	if(is_int($DIAS_LOG_VENDA) && $DIAS_LOG_VENDA >= 0) {
			$logDir = dirname(__DIR__) . '/../../../../../../LOG/vendaApi/';
			$date = date('Y-m-d', strtotime('-' . $DIAS_LOG_VENDA . ' days'));

			if(is_dir($logDir)) {
				$logFiliais = scandir($logDir);

				if($logFiliais !== false) {
					foreach ($logFiliais as $logFilial) {
						if($logFilial !== "." && $logFilial !== "..") {
							$currentLogDir = $logDir . $logFilial . "/";
							$caixasDir = $currentLogDir;
							$logCaixas = scandir($currentLogDir);

							if($logCaixas !== false) {
								foreach ($logCaixas as $logCaixa) {
									if($logCaixa !== "." && $logCaixa !== "..") {
										$currentLogDir = $caixasDir . $logCaixa;
										$logs = scandir($currentLogDir);

										if($logs !== false) {
											foreach ($logs as $log) {
												if($log !== "." && $log !== "..") {
													if(explode(" ", $log)[0] < $date) {
														unlink($currentLogDir . '/' . $log);
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
    	}
	}
}