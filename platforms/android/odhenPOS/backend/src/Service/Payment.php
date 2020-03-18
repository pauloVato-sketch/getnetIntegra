<?php
namespace Service;

use \Util\Exception;

class Payment {

	protected $entityManager;
	protected $waiterMessage;
	protected $util;
	protected $tableService;
	protected $impressaoUtil;
	protected $impressaoPedido;
    protected $databaseUtil;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Util\WaiterMessage $waiterMessage, \Util\Util $util, \Service\Table $tableService, \Odhen\API\Lib\ImpressaoUtil $impressaoUtil, \Odhen\API\Service\ImpressaoPedido $impressaoPedido, \Odhen\API\Util\Database $databaseUtil){
		$this->entityManager = $entityManager;
		$this->waiterMessage = $waiterMessage;
		$this->util = $util;
		$this->tableService = $tableService;
 		$this->impressaoUtil = $impressaoUtil;
		$this->impressaoPedido = $impressaoPedido;
        $this->databaseUtil = $databaseUtil;
	}

	public function getMoneyCurrency($CDFILIAL, $NRCONFTELA) {
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'NRCONFTELA' => $NRCONFTELA
		);
		return $this->entityManager->getConnection()->fetchAssoc("SQL_GET_MONEY_CURRENCY", $params);
	}

	public function getOcorTxts($ocorrGroup, $ocorrIds) {
		$params = array(
			'CDGRPOCOR' => $ocorrGroup,
			'CDOCORR' => $ocorrIds
		);

		$types = array(
			'CDGRPOCOR' => \PDO::PARAM_STR,
			'CDOCORR' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
		);

		$values = $this->entityManager->getConnection()->fetchAll("SQL_GET_OCORR_TEXTS", $params, $types);

		return array_map(function($obs) {
			return $obs['DSOCORR'];
		}, $values);
	}

	public function initiatesAnticipate($chave, $DATASALE, $TIPORECE, $CDCLIENTE, $CDCONSUMIDOR, $NRMESA, $NRVENDAREST, $NRCOMANDA, $NRINSCRCONS, $arrayPosicoes){
		$result = Array(
			'error' => true,
			'message' => ''
		);
		$connection = false;

		try {
			$this->entityManager->getConnection()->beginTransaction();
			$connection = true;
		} catch (\Exception $e) {
			Exception::logException($e);
			$result['message'] = $this->waiterMessage->getMessage('265');
		}

		if ($connection) {
			try {
				$position = 'T'; // padrão adiantamento como mesa
				$session  = $this->util->getSessionVars($chave);
				$dateTime = new \DateTime('NOW', new \DateTimeZone('America/Sao_Paulo'));
				$NMCONSVEND = null;

				// pega CDCLIENTE e CDCONSUMIDOR para posição específica. caso não houver, CDCLIENTE/CDCONSUMIDOR da mesa se mantém
				if (!empty($arrayPosicoes)){
					$posvendarest = $this->tableService->getPosition($session, $NRVENDAREST, $arrayPosicoes);
					if (!empty($posvendarest)) {
						$CDCLIENTE 	  = $posvendarest[0]['CDCLIENTE'];
						$CDCONSUMIDOR = $posvendarest[0]['CDCONSUMIDOR'];
						$NMCONSVEND   = $posvendarest[0]['DSCONSUMIDOR'];
					}
					$position = $arrayPosicoes[0];
				}

				if (!empty($NRINSCRCONS) || !empty($NMCONSVEND)){
					// atualiza CPF/CNPJ na ITCOMANDAVEN para o adiantamento
					$params = array(
						'NRINSCRCONS' => $NRINSCRCONS,
						'NMCONSVEND' => $NMCONSVEND,
						'CDFILIAL' => $session['CDFILIAL'],
						'NRVENDAREST' => $NRVENDAREST,
						'NRCOMANDA' => $NRCOMANDA,
						'NRLUGARMESA' => $arrayPosicoes,
						'ALL' => $position
					);
					$type = array(
						'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
						'ALL' => \PDO::PARAM_STR
					);
					$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_ITCOMANDAVEN_ADIANTAMENTO", $params, $type);
				}

				$params = Array(
					'CDFILIAL' 		 => $session['CDFILIAL'],
					'CDCAIXA' 		 => $session['CDCAIXA'],
					'CDVENDEDOR' 	 => $session['CDVENDEDOR'],
					'NRLUGARMESA' 	 => $position,
					'NRVENDAREST'    => $NRVENDAREST,
					'NRCOMANDA'      => $NRCOMANDA,
					'NRMESA'   		 => $NRMESA,
					'NRSEQVENDA' 	 => null, // atualizado após registro ir para a MOVCAIXA
					'CDCLIENTE'		 => $CDCLIENTE,
					'CDCONSUMIDOR'	 => $CDCONSUMIDOR,
					'DTHRINCMOV' 	 => $dateTime,
					'IDTIPMOV' 		 => 'E', // 'E' = entrada
					// para transações com sucesso
					'IDADMTASK' 	 => '0',
					'IDSTMOV'	     => '1',
					//
					'IDATIVO' 		 => 'S',
					'NRORG' 		 => $session['NRORG'],
					'NRORGINCLUSAO'  => $session['NRORG'],
					'CDOPERINCLUSAO' => $session['CDOPERADOR'],
					'DTINCLUSAO'	 => $dateTime
				);
				$types = array(
					'DTHRINCMOV' => \Doctrine\DBAL\TypeS\Type::DATETIME,
					'DTINCLUSAO' => \Doctrine\DBAL\TypeS\Type::DATETIME
				);
				foreach ($TIPORECE as $recebimento) {
					// um NRSEQMOVMOB por recebimento
					$this->util->newCode('MOVCAIXAMOB' . $session['CDFILIAL']);
					$params['NRSEQMOVMOB']  = $this->util->getNewCode('MOVCAIXAMOB' . $session['CDFILIAL'], 10);
					$params['CDTIPORECE'] 	= $recebimento['CDTIPORECE'];
					$params['VRMOV'] 	    = $recebimento['VRMOVIVEND'];
					$params['IDTPTEF'] 		= $recebimento['IDTPTEF'];
					$params['NRADMCODE'] 	= $recebimento['NRCONTROLTEF'];
					$params['CDNSUTEFMOB']  = !empty($recebimento['CDNSUHOSTTEF']) ? $recebimento['CDNSUHOSTTEF'] : null;
					$params['DSBANDEIRA'] 	= $recebimento['CDBANCARTCR'];
					$params['TXPRIMVIATEF'] = !empty($recebimento['STLPRIVIA']) ? $recebimento['STLPRIVIA'] : null;
					$params['TXSEGVIATEF']  = !empty($recebimento['STLSEGVIA']) ? $recebimento['STLSEGVIA'] : null;

					$this->entityManager->getConnection()->executeQuery("SQL_INSERT_MOVCAIXAMOB", $params, $types);
				}

				$this->entityManager->getConnection()->commit();
				$result['error'] = false;
			} catch (\Exception $e) {
				Exception::logException($e);
				$this->entityManager->getConnection()->rollBack();
				$result['message'] = $e->getMessage();
			}
		}

		return $result;
	}

	public function printTEFVoucher($arrTiporece, $CDFILIAL, $CDCAIXA, $NRORG){
		$result = array(
			'error' => true,
			'message' => '',
			'data' => array()
		);

		try {
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'NRORG' => $NRORG
			);
			$dadosImpressora = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_DADOS_IMPRESSORA", $params);

			if (!empty($dadosImpressora)){
				$printerParams = $this->impressaoUtil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);

				$arrTEFVoucher = array_map(function($recebimento) use ($printerParams){
					// tratamento específico para os comprovantes do TEF da Cappta
					$STLPRIVIA = array_key_exists("STLPRIVIA", $recebimento) ? $recebimento['STLPRIVIA'] : null;
					$STLSEGVIA = array_key_exists("STLSEGVIA", $recebimento) ? $recebimento['STLSEGVIA'] : null;

					if($printerParams['impressaoFront']) {
						$STLPRIVIA = str_replace("\n", $printerParams['comandoEnter'], $STLPRIVIA);
						$STLSEGVIA = str_replace("\n", $printerParams['comandoEnter'], $STLSEGVIA);
					}
					
					return array(
						'STLPRIVIA' => $STLPRIVIA,
						'STLSEGVIA' => $STLSEGVIA
					);
				}, $arrTiporece);

				if (!$printerParams['impressaoFront']){
					$tefVoucherResult = array(
						'error' => false
					);
					foreach ($arrTEFVoucher as $tefVoucher) {
						$tefVoucherResult = $this->impressaoUtil->imprimeNaoFiscal($tefVoucher['STLPRIVIA'], $dadosImpressora);
						if (!$tefVoucherResult['error'] && $tefVoucher['STLSEGVIA'] !== ''){
							$tefVoucherResult = $this->impressaoUtil->imprimeNaoFiscal($tefVoucher['STLSEGVIA'], $dadosImpressora);
						}

						if ($tefVoucherResult['error']){
							break;
						}
					}

					if ($tefVoucherResult['error']){
						$message = !empty($tefVoucherResult['exceptionMessage']) ? 
							$dadosImpressora['NMIMPRLOJA'] . ': ' . $tefVoucherResult['exceptionMessage'] : $tefVoucherResult['message'];
						$result['message'] = 'Ocorreu um problema na impressão do comprovante TEF.<br><br>' . $message;
					} else {
						$result['error'] = false;
					}
				} else {
					$result['error'] = false;
					$result['data'] = $arrTEFVoucher;
				}
			} else {
				$result['message'] = 'Impressora não parametrizada para o caixa.';
	        }
		} catch(\Exception $e) {
			Exception::logException($e);
			$result['message'] = $e->getMessage();
		}

	    return $result;
	}

	public function findNRCPFRESPCON($CDCLIENTE, $CDCONSUMIDOR, $NRORG){
		$params = array(
			'CDCLIENTE' => $CDCLIENTE,
			'CDCONSUMIDOR' => $CDCONSUMIDOR,
			'NRORG' => $NRORG
		);

		$result = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRCPFRESPCON", $params);
		return !empty($result['NRCPFRESPCON']) ? $result['NRCPFRESPCON'] : null;
	}

	public function findNMCONSUMIDOR($CDCLIENTE, $CDCONSUMIDOR, $NRORG){
		$params = array(
			'CDCLIENTE' => $CDCLIENTE,
			'CDCONSUMIDOR' => $CDCONSUMIDOR,
			'NRORG' => $NRORG
		);

		$result = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NMCONSUMIDOR", $params);
		return $result['NMCONSUMIDOR'];
	}

	public function checkSaleCode($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $CDORDERWAITER) {
		self::changeTableParams($NRVENDAREST, $NRCOMANDA);

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'NRVENDAREST' => $NRVENDAREST,
			'NRCOMANDA' => $NRCOMANDA,
			'CDORDERWAITER' => $CDORDERWAITER
		);
		
		return $this->entityManager->getConnection()->fetchAssoc("SQL_CHECK_ORDERCODE", $params);
	}

	public function insertSaleCode($IDSTORDER, $DSOPERACAO, $CDFILIAL, $NRVENDAREST, $NRCOMANDA, $CDORDERWAITER) {
		self::changeTableParams($NRVENDAREST, $NRCOMANDA);

		$params = array(
			'IDSTORDER' => $IDSTORDER,
			'DSOPERACAO' => $DSOPERACAO,
			'CDFILIAL' => $CDFILIAL,
			'NRVENDAREST' => $NRVENDAREST,
			'NRCOMANDA' => $NRCOMANDA,
			'CDORDERWAITER' => $CDORDERWAITER,
			'DTHRINCREQ' => new \DateTime()
		);

        $type = array();
        if (!$this->databaseUtil->databaseIsOracle()){
            $type['DSOPERACAO'] = \Doctrine\DBAL\Types\Type::BINARY;
        }
        $type['DTHRINCREQ'] = \Doctrine\DBAL\Types\Type::DATETIME;

		$this->entityManager->getConnection()->executeQuery("SQL_INSERE_WAITER_ORDERS", $params, $type);
	}

	public function updateSaleCode($IDSTORDER, $DSOPERACAO, $CDFILIAL, $NRVENDAREST, $NRCOMANDA, $CDORDERWAITER) {
		self::changeTableParams($NRVENDAREST, $NRCOMANDA);

		$params = array(
			'IDSTORDER' => $IDSTORDER,
			'DSOPERACAO' => $DSOPERACAO,
			'CDFILIAL' => $CDFILIAL,
			'NRVENDAREST' => $NRVENDAREST,
			'NRCOMANDA' => $NRCOMANDA,
			'CDORDERWAITER' => $CDORDERWAITER
		);
        $type = array();
        if (!$this->databaseUtil->databaseIsOracle()){
            $type['DSOPERACAO'] = \Doctrine\DBAL\Types\Type::BINARY;
        }
		$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_WAITER_ORDERS", $params, $type);
	}

	private function changeTableParams(&$NRVENDAREST, &$NRCOMANDA){
		// modifica para 'X' NRVENDAREST e NRCOMANDA quando é modo balcão
		$NRVENDAREST = !empty($NRVENDAREST) ? $NRVENDAREST : 'X';
		$NRCOMANDA = !empty($NRCOMANDA) ? $NRCOMANDA : 'X';
	}

	public function printOrderCupom($CDFILIAL, $CDLOJA, $ITEMVENDA, $CDVENDEDOR, $CDSENHAPED){
		try {
			// adiciona produtos filhos a serem impressos
			$arrProduct = array();
			foreach ($ITEMVENDA as $product) {
				array_push($arrProduct, $product);
				if (!empty($product['itensCombo'])){
					foreach ($product['itensCombo'] as $combo) {
						array_push($arrProduct, $combo);
					}
				}
			}

			$result = $this->impressaoPedido->imprimePedido(
				$CDFILIAL,
				$CDLOJA,
				$arrProduct,
				$CDVENDEDOR,
				null,
				null,
				$CDSENHAPED,
				'B',
				'',
				false,
				true
			);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		return $result;
	}

    public function getProduct($CDPRODUTO){
        $params = array($CDPRODUTO);
        return $this->entityManager->getConnection()->fetchAssoc("SQL_GET_PRODUTO", $params);
    }

    public function buscaVendaRealizada($CDFILIAL, $NRVENDAREST, $NRCOMANDA){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'NRCOMANDA' => $NRCOMANDA,
        );
        return $this->entityManager->getConnection()->fetchAssoc("BUSCA_VENDA_REALIZADA", $params);
    }

    public function logServDesc ($logServico, $logDesconto, $NRMESA, $NRCOMANDA, $CDFILIAL, $CDCAIXA, $CDOPERADOR, $supervisorServ, $supervisorDesc, $NRSEQVENDA) {
    	if ($logServico != null) {
        	$idOperFos = $logServico;
        	if ($logServico == 'ADD_TAX') {
        		$dsMotivoFos = 'Waiter - Adiciona taxa de serviço.';
        		$dsLivreFos = 'Adiciona taxa de serviço da Mesa/Comanda: ' . $NRMESA . '/' . $NRCOMANDA . '_';
        	} else if ($logServico == 'RET_TAX'){
        		$dsMotivoFos = 'Waiter - Retira taxa de serviço.';
        		$dsLivreFos = 'Retira taxa de serviço da Mesa/Comanda: ' . $NRMESA . '/' . $NRCOMANDA . '_';
        	} else{
        		$dsMotivoFos = 'Waiter - Alterar valor da Taxa de Serviço.';
        		$dsLivreFos = 'Alterar valor da Taxa de Serviço da Mesa/Comanda: ' . $NRMESA . '/' . $NRCOMANDA . '_';
        	}
        	$this->util->logFOS($CDFILIAL, $CDCAIXA, $idOperFos, $CDOPERADOR, $supervisorServ, $dsMotivoFos, $dsLivreFos);
        }

        if ($logDesconto != null) {
        	$this->util->logFOS($CDFILIAL, $CDCAIXA, 'CUP_DES', $CDOPERADOR, $supervisorDesc, 'Waiter - Cupom com desconto.', 'Desconto no cupom nº ' . $NRSEQVENDA);
        }
    }

    public function getPaymentDelivery($cdfilial, $nrvendarest, $nrcomanda){
    	
    	$params = array(
    		'CDFILIAL' 	  => $cdfilial,
    		'NRCOMANDA'   => $nrcomanda,
    		'NRVENDAREST' => $nrvendarest
    	);
    	$resultPayment = $this->entityManager->getConnection()->fetchAssoc("GET_ORDER_PARAMS_DLV", $params);
    	$resultPayment['TIPORECE'] = $this->entityManager->getConnection()->fetchALL("GET_PAYMENT_DLV", $params);

    	$resultPayment['error'] = false;
    	return $resultPayment;
    }

    public function getIDORIGEMVENDADLV($NRVENDAREST) {
		$params = array(
			'NRVENDAREST' => $NRVENDAREST
		);
		return  $this->entityManager->getConnection()->fetchAssoc("GET_ORIGEM_VENDA_DLV", $params)['IDORGCMDVENDA'];
	}

	public function updateTaxaServico($CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA) {
	    $params = array(
	        'CDFILIAL' => $CDFILIAL,
	        'NRVENDAREST' => $NRVENDAREST,
	        'NRCOMANDA' => $NRCOMANDA,
	        'CDPRODUTO' => null
	    );
	    $posicoes = $this->entityManager->getConnection()->fetchAll("SQL_POSICAO_PARCIAL", $params);

	    if ($posicoes && is_array($posicoes) && count($posicoes) > 0) {
	        $precoTxSum = 0;
	        foreach ($posicoes as $posicao) {
	            if (isset($posicao['PRECOTX'])) {
	                $precoTxSum += $posicao['PRECOTX'];
	            }
	        }

	        $params = array(
	            'PRECOTXTOTAL' => $precoTxSum,
	            'CDFILIAL' => $CDFILIAL,
	            'CDLOJA' => $CDLOJA,
	            'NRVENDAREST' => $NRVENDAREST,
	            'NRCOMANDA' => $NRCOMANDA
	        );
	        $this->entityManager->getConnection()->executeQuery("UPDATE_VRCOMISVENDE", $params);
	    }
	}


	public function savePayment($paymentData, $session) {
		$dateTime = new \DateTime('NOW', new \DateTimeZone('America/Sao_Paulo'));
		$this->util->newCode('MOVCAIXAMOB' . $session['CDFILIAL']);
		var_dump($paymentData);die;
	    $params = array(
	        'CDFILIAL' 		 => $session['CDFILIAL'],
	        'CDCAIXA' 		 => $session['CDCAIXA'],
	        'CDVENDEDOR' 	 => $session['CDVENDEDOR'],
	        'NRVENDAREST' 	 => $paymentData['NRVENDAREST'],
	        'NRCOMANDA' 	 => $paymentData['NRCOMANDA'],
	        'NRSEQVENDA' 	 => null,
	        'NRMESA' 		 => $paymentData['NRMESA'],
	        'NRLUGARMESA' 	 => '00',
	        'NRSEQMOVMOB' 	 => $this->util->getNewCode('MOVCAIXAMOB' . $session['CDFILIAL'], 10),
	        'DTHRINCMOV' 	 => $dateTime,
            'VRMOV' 		 => $paymentData["VRMOVIVEND"],
            'IDTIPMOV' 		 => 'E',
            'DSBANDEIRA'	 => $paymentData['eletronicTransacion']['data']['CDBANCARTCR'],
            'NRADMCODE' 	 => $paymentData['eletronicTransacion']['data']['NRCONTROLTEF'],
            'IDADMTASK' 	 => '0',
            'NRORG' 		 => $session['NRORG'],
            'NRORGINCLUSAO'  => $session['NRORG'],
            'IDATIVO' 		 => 'S',
            'DTINCLUSAO' 	 => $dateTime,
            'CDOPERINCLUSAO' => $session['CDOPERADOR'],
            'CDTIPORECE' 	 => $paymentData['tiporece']['CDTIPORECE'],
            'CDNSUTEFMOB' 	 => $paymentData['eletronicTransacion']['data']['CDNSUHOSTTEF'],
            'TXPRIMVIATEF' 	 => $paymentData['eletronicTransacion']['data']['STLPRIVIA'],
            'TXSEGVIATEF' 	 => $paymentData['eletronicTransacion']['data']['STLSEGVIA'],
            'IDTPTEF' 		 => $paymentData['eletronicTransacion']['data']['IDTPTEF'],
            'CDCLIENTE' 	 => $paymentData['CDCLIENTE'],
            'CDCONSUMIDOR' 	 => $paymentData['CDCONSUMIDOR'],
            'IDSTMOV' 		 => '1',
            'NRCARTBANCO' 	 => $paymentData['eletronicTransacion']['data']['NRCARTBANCO'],
            'IDTIPORECE' 	 => $paymentData['eletronicTransacion']['data']['IDTIPORECE']            
	    );

		$types = array(
			'DTHRINCMOV' => \Doctrine\DBAL\TypeS\Type::DATETIME,
			'DTINCLUSAO' => \Doctrine\DBAL\TypeS\Type::DATETIME
		);

		$lugares = $paymentData['NRLUGARMESA'];
		$lugaresLength = sizeof($lugares);
		if($lugaresLength > 0 && $lugaresLength !== intval($paymentData['NRPESMESAVEN'])) {
			$result = Array();

			for($i = 0; $i < $lugaresLength; $i++) {
				if($i !== 0) {
					$this->util->newCode('MOVCAIXAMOB' . $session['CDFILIAL']);
					$params['NRSEQMOVMOB'] = $this->util->getNewCode('MOVCAIXAMOB' . $session['CDFILIAL'], 10);
				}

				$params['NRLUGARMESA'] = str_pad($lugares[$i], 2, "0", STR_PAD_LEFT);
				$result =  $this->entityManager->getConnection()->fetchAll("SQL_INSERT_MOVCAIXAMOB", $params, $types);	
			}

			return $result;
		} else {
	    	return $this->entityManager->getConnection()->fetchAll("SQL_INSERT_MOVCAIXAMOB", $params, $types);
		}
	}

	public function handleRemovePayment($paymentData, $session) {
		if(isset($paymentData['DATA'])) {
			$paymentData = $paymentData['DATA'];
		} 
		return $this->removePayment($paymentData, $session);
	}

	private function removePayment($payment, $session) {
		$NRPESMESAVEN = isset($payment['NRPESMESAVEN']) ? intval($payment['NRPESMESAVEN']) : 0;
		$lugaresLength = isset($payment['NRLUGARMESA']) ? sizeof($payment['NRLUGARMESA']) : 0;

		if($NRPESMESAVEN === 0)
			$NRPESMESAVEN++;

		if($lugaresLength === 0) {
			$NRLUGARMESA = array('00');
		} else if ($lugaresLength < $NRPESMESAVEN){
			$NRLUGARMESA = array_map(function($lugar) {
				return str_pad($lugar, 2, "0", STR_PAD_LEFT);
			}, $payment["NRLUGARMESA"]);
		}

		$params = array(
	    	'CDFILIAL' 		 => $session['CDFILIAL'],
	        'NRVENDAREST' 	 => empty($payment['NRVENDAREST']) ? '' : $payment['NRVENDAREST'],
	        'NRCOMANDA' 	 => empty($payment['NRCOMANDA']) ? '' : $payment['NRCOMANDA'],
            'NRADMCODE' 	 => array_column($payment, 'NRCONTROLTEF'),
            'CDNSUTEFMOB' 	 => array_column($payment, 'CDNSUHOSTTEF'),
            'NRLUGARMESA'    => $NRLUGARMESA, 
            'NRORG' 		 => $session['NRORG']
	    );

	    $types = array(
			'NRADMCODE'   => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
			'CDNSUTEFMOB' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
			'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
		);

	    return $this->entityManager->getConnection()->fetchAll("SQL_DELETE_MOVCAIXAMOB", $params, $types);
	}

	public function getPayments($paymentData, $session) {
		$params = array(
	    	'CDFILIAL' 		 => $session['CDFILIAL'],
	        'NRVENDAREST' 	 => $paymentData['NRVENDAREST'],
	        'NRCOMANDA' 	 => $paymentData['NRCOMANDA'],
            'NRORG' 		 => $session['NRORG']
	    );

		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_MOVCAIXAMOB", $params);
	}
}