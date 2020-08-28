<?php

namespace Controller;

use Zeedhi\DTO\Response\Notification;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class Payment extends \Zeedhi\Framework\Controller\Simple {

	protected $util;
	protected $paymentService;
	protected $registerService;
	protected $vendaAPI;
	protected $tableService;
	protected $consumerService;
	protected $utilAPI;
	protected $caixaAPI;
	protected $deliveryService;

	const IDORIGEMVENDA_TABLE = 'MES_PKR';
	const IDORIGEMVENDA_BILL = 'CMD_PKC';
	const IDORIGEMVENDA_REGISTER = 'BAL_MOB';
    const IDORIGEMVENDA_DRIVETHU = 'CMD_THR';

	public function __construct(
		\Util\Util $util,
		\Service\Payment $paymentService,
		\Service\Register $registerService,
		\Odhen\API\Service\Venda $vendaAPI,
		\Service\Table $tableService,
		\Odhen\API\Service\Consumidor $consumerService,
		\Odhen\API\Util\Util $utilAPI,
		\Odhen\API\Service\Caixa $caixaAPI,
		\Service\Delivery $deliveryService
	){
		$this->util = $util;
		$this->paymentService = $paymentService;
		$this->registerService = $registerService;
		$this->vendaAPI = $vendaAPI;
		$this->tableService = $tableService;
		$this->consumerService = $consumerService;
		$this->utilAPI = $utilAPI;
		$this->caixaAPI = $caixaAPI;
		$this->deliveryService = $deliveryService;
	}

	private function getIDORIGEMVENDA($IDMODULO, $IDUTCXDRIVETHU, $NRVENDAREST = null) {
		$IDORIGEMVENDA = null;
		switch ($IDMODULO) {
			case 'M':
				$IDORIGEMVENDA = self::IDORIGEMVENDA_TABLE;
				break;
			case 'C':
				$IDORIGEMVENDA = $IDUTCXDRIVETHU === 'S' ? self::IDORIGEMVENDA_DRIVETHU : self::IDORIGEMVENDA_BILL;
				break;
			case 'D':
				$IDORIGEMVENDA = $this->paymentService->getIDORIGEMVENDADLV($NRVENDAREST);
				break;
			default:
				$IDORIGEMVENDA = self::IDORIGEMVENDA_REGISTER;
				break;
		}
		return $IDORIGEMVENDA;
	}

	private function fixDiscount($porcentagemDesconto, $totalSemDesconto) {
		return $totalSemDesconto * $porcentagemDesconto;
	}

	public function preparePayAccount(Request\Filter $request, Response $response){
        $params = $request->getFilterCriteria()->getConditions();
        $params = $this->util->getParams($params);

        $chave         = !empty($params['chave']) ? $params['chave'] : null;
		$session 	   = $this->util->getSessionVars($chave);
		if(isset($params['IDMODULO'])){
			$session['IDMODULO'] = $params['IDMODULO'];
		}

        $DELIVERY = $params['DELIVERY'];

        if ($DELIVERY){
        	// prepara parametros para vendas originadas no modo delivery
        	self::prepareDeliveryDataSale($params);
		}

        //Trata as bandeiras dos recebimentos realizados pela Cielo LIO.
        if(!$DELIVERY){
	        foreach ($params['TIPORECE'] as $indice => $paramUnit) {
	        	if ($paramUnit['IDTPTEF'] === '7' || $paramUnit['IDTPTEF'] === '8' || $paramUnit['IDTPTEF'] === '9' || $paramUnit['IDTPTEF'] === '10') {
		        	$params['TIPORECE'][$indice]['CDBANCARTCR'] = self::trataBandeirasCieloLio($paramUnit['CDBANCARTCR'], $paramUnit['IDTIPORECE']);
		        }
	        }
        }

        $TIPORECE      = $params['TIPORECE'];
        $ITEMVENDA     = $params['ITEMVENDA'];
        $ITVENDADES    = !empty($params['ITVENDADES']) ? $params['ITVENDADES'] : null;
        $PRODSENHAPED  = !empty($params['PRODSENHAPED']) ? $params['PRODSENHAPED'] : null;
        $DATASALE      = $params['DATASALE'];
        $CDCLIENTE     = $params['CDCLIENTE'];
        $CDCONSUMIDOR  = $params['CDCONSUMIDOR'];
        $NRMESA        = $params['NRMESA'];
        $NRPESMESAVEN  = $params['NRPESMESAVEN'];
        $NRVENDAREST   = !empty($params['NRVENDAREST']) ? $params['NRVENDAREST'] : null;
        $NRCOMANDA     = !empty($params['NRCOMANDA']) ? $params['NRCOMANDA'] : null;
        $saleCode      = $params['saleCode'] . $chave;
        $arrayPosicoes = self::formatPositions($params['NRLUGARMESA']);
        $TIPODESCONTO  = isset($params['TIPODESCONTO'])? $params['TIPODESCONTO']: null;
        $NRINSCRCONS   = !empty($params['NRINSCRCONS']) ? $params['NRINSCRCONS']: null;
        $EMAIL         = !empty($params['EMAIL']) ? $params['EMAIL']: null;
        $NOMECONS      = !empty($params['NOMECONS']) ? $params['NOMECONS'] : null;
        $logServico    = !empty($params['servico']['logServico']) ? $params['servico']['logServico'] : null;
        $logDesconto   = !empty($params['desconto']['logDesconto']) ? $params['desconto']['logDesconto'] : null;
        $supervisorServ= !empty($params['servico']['CDSUPERVISOR']) ? $params['servico']['CDSUPERVISOR'] : $session['CDOPERADOR'];
        $supervisorDesc= !empty($params['desconto']['CDSUPERVISOR']) ? $params['desconto']['CDSUPERVISOR'] : $session['CDOPERADOR'];
        $CDVENDEDOR    = $params['CDVENDEDOR'];
        $motivoDesconto = !empty($params['desconto']['motivoDesconto']) ? $params['desconto']['motivoDesconto'] : null;
        $CDGRPOCORDESC = !empty($params['desconto']['CDGRPOCORDESC']) ? $params['desconto']['CDGRPOCORDESC'] : null;
		$DSOBSFINVEN = !empty($params['DSOBSFINVEN']) ? $params['DSOBSFINVEN'] : null;

		$result = self::payAccount($TIPORECE, $ITEMVENDA, $DATASALE, $CDCLIENTE, $CDCONSUMIDOR, $NRMESA, $NRPESMESAVEN, $NRVENDAREST, $NRCOMANDA, $chave, $saleCode, $arrayPosicoes, $TIPODESCONTO, $NRINSCRCONS, $EMAIL, $NOMECONS, $logServico, $logDesconto, $supervisorServ, $supervisorDesc, $CDVENDEDOR, $session, $motivoDesconto, $CDGRPOCORDESC, $DSOBSFINVEN, $ITVENDADES, $DELIVERY, $PRODSENHAPED);
		if(!$result['error']) {
			$this->paymentService->handleRemovePayment($params, $session);
		}

        $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('payAccount', $result));
    }

    private function prepareDeliveryDataSale(&$params){
    	if($params['IDSTCOMANDA'] == 'P'){
    		throw new \Exception("Operação não permitida. Este cupom já foi impresso.", 1);
    	}
    	$params['ITEMVENDA'] = [];
    	$paramsDlv = $this->paymentService->getPaymentDelivery($params['CDFILIAL'], $params['NRVENDAREST'], $params['NRCOMANDA']);
    	if(isset($paramsDlv['NRLUGARMESA'])){
    		$paramsDlv['NRLUGARMESA'] = array($paramsDlv['NRLUGARMESA']);
    	}
    	$params = $paramsDlv + $params;
    }

    public function payAccount($TIPORECE, $ITEMVENDA, $DATASALE, $CDCLIENTE, $CDCONSUMIDOR, $NRMESA, $NRPESMESAVEN, $NRVENDAREST, $NRCOMANDA, $chave, $saleCode, $arrayPosicoes, $TIPODESCONTO, $NRINSCRCONS, $EMAIL, $NOMECONS, $logServico, $logDesconto, $supervisorServ, $supervisorDesc, $CDVENDEDOR, $session, $motivoDesconto, $CDGRPOCORDESC, $DSOBSFINVEN, $ITVENDADES, $DELIVERY, $PRODSENHAPED){

        set_time_limit(0); // bloqueia timeout na venda

        $result = array(
            'error'   => true,
            'message' => ''
        );

        try {
			$estadoCaixa = $this->caixaAPI->getEstadoCaixa($session['CDFILIAL'], $session['CDCAIXA'], $session['NRORG']);
	        if($estadoCaixa['estado'] === 'aberto' || $session['IDCOLETOR'] === 'C') {
				/*** IMPEDE VENDAS DUPLICADAS ***/
				// Verifica se a venda já foi realizada
				// IDSTORDER = E - erro, C - concluído com sucesso, P - pendente (em processamento)
				$saleCodeObject = $this->paymentService->checkSaleCode($session['CDFILIAL'], $NRVENDAREST, $NRCOMANDA, $saleCode);
				if ($saleCodeObject) {
					if ($saleCodeObject['IDSTORDER'] == 'P') {
						$result['error'] = true;

						$reqDate = substr($saleCodeObject['DTHRINCREQ'], 0, -4);
						$reqDate = date_create($reqDate);
						$minutesDiff = $reqDate->diff(new \DateTime())->format('%I');
						$secondsDiff = $reqDate->diff(new \DateTime())->format('%S');

						if(intval($minutesDiff) >= 1) {
							$this->paymentService->updateSaleCode(
								'E', // E - erro
								json_encode($result),
								$session['CDFILIAL'],
								$NRVENDAREST,
								$NRCOMANDA,
								$saleCode
							);

							$result['message'] = 'Tempo para processamento da venda excedido. Favor tentar novamente.';
							$result['resetSaleCode'] = true;
						} else {
							$result['message'] = 'A requisição está em processamento, por favor aguarde um momento e tente novamente. <br><br>
								Tempo decorrido: ' . $minutesDiff . ':' . $secondsDiff . '<br> Tempo máximo: 01:00';
						}
					} else {
						$result = json_decode($saleCodeObject['DSOPERACAO'], true);
						if ($saleCodeObject['IDSTORDER'] == 'E') {
							// última venda com erro; reseta o code (CDORDERWAITER) para uma nova venda
							$result['resetSaleCode'] = true;
						}
					}
				} else {
                    $this->paymentService->insertSaleCode(
                        'P', // P - pendente (em processamento)
                        json_encode($result),
                        $session['CDFILIAL'],
                        $NRVENDAREST,
                        $NRCOMANDA,
                        $saleCode
                    );

                    $VRDESCONTO = $DATASALE['VRDESCONTO'] + $DATASALE['FIDELITYVALUE'];

					$CDFILIAL = $session['CDFILIAL'];
					$CDLOJA   = $session['CDLOJA'];
					$CDCAIXA  = $session['CDCAIXA'];
					$NRORG    = $session['NRORG'];
					$CDOPERADOR = $session['CDOPERADOR'];

					$CDCLIENTE    = $CDCLIENTE ? $CDCLIENTE : null;
					$CDCONSUMIDOR = $CDCONSUMIDOR ? $CDCONSUMIDOR : null;
					if (!$CDCONSUMIDOR) {
						$NMCONSVEND = 'CONSUMIDOR FINAL';
					} else {
						$NMCONSVEND =  $this->paymentService->findNMCONSUMIDOR($CDCLIENTE, $CDCONSUMIDOR, $NRORG);
						if (!$NRINSCRCONS) {
							$NRINSCRCONS = $this->paymentService->findNRCPFRESPCON($CDCLIENTE, $CDCONSUMIDOR, $NRORG);
						}
					}
					if ($NOMECONS != null && $NOMECONS != ""){
						$NMCONSVEND = $NOMECONS;
					}
					$simulatePrinter = false;
					$simulateSaleValidation = false;

					if ($session['IDCOLETOR'] !== 'C'){
						$moneyCurrency = $this->paymentService->getMoneyCurrency($session['FILIALVIGENCIA'], $session['NRCONFTELA'], $session['DTINIVIGENCIA']);

						// Trata de repique com parametrização para taxa de serviço por produto.
						if (!empty($DATASALE['REPIQUE']) && $session['IDTRATTAXASERV'] === 'P' && $session['CDPRODTAXASERV'] !== null && $session['IDCOMISVENDA'] != 'N') {
							if ($DATASALE['REPIQUE'] == $DATASALE['TROCO']) {
								$DATASALE['TROCO'] = 0;
							} else {
								$DATASALE['TROCO'] = $DATASALE['TROCO'] - $DATASALE['REPIQUE'];
							}
							$DATASALE['TOTAL'] += $DATASALE['REPIQUE'];
						}

						$VRTROCOVEND = array(
							'CDTIPORECE' => $moneyCurrency['CDTIPORECE'],
							'VRMOVIVEND' => $DATASALE['TROCO']
						);

						$recebimentoDeMesa = $session['IDMODULO'] !== 'B';
						$CDVENDEDOR = Empty($CDVENDEDOR) ? $session['CDVENDEDOR'] : $CDVENDEDOR;

						$openingDate = $this->registerService->getRegisterOpeningDate($CDFILIAL, $CDCAIXA);
						$openingDate = new \DateTime($openingDate['DTABERCAIX']);
						$DTVENDA = new \DateTime();

						if ($recebimentoDeMesa) {
							if($DELIVERY){
								$tableData = $this->deliveryService->getInfoOrder($CDFILIAL, $NRVENDAREST);
							}else{
								$tableData = $this->tableService->getTablesButNotGrouped($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRMESA, $NRORG, $session['IDMODULO']);
							}
							$VRACRCOMANDA = $this->deliveryService->getTaxaEntrega($CDFILIAL, $NRVENDAREST);

							$result = $this->vendaAPI->vendaMesa(
								$NRORG,
								$CDFILIAL,
								$CDLOJA,
								$CDCAIXA,
								isset($tableData[0]['CDVENDEDOR']) ? $tableData[0]['CDVENDEDOR'] : $CDVENDEDOR,
								$session['CDOPERADOR'],
								$openingDate,
								$DTVENDA,
                                $DATASALE['TOTAL'] + $DATASALE['VRTXSEVENDA'] + $DATASALE['VRCOUVERT'],
								$TIPORECE,
								$NMCONSVEND,
								$NRINSCRCONS,
								null, // CDSENHAPED
								$VRTROCOVEND,
								$EMAIL, // EMAIL
								$VRDESCONTO,
								$CDCLIENTE,
								$CDCONSUMIDOR,
								$simulatePrinter,
								$simulateSaleValidation,
								$tableData,
								$arrayPosicoes,
								$this->getIDORIGEMVENDA($session['IDMODULO'], $session['IDUTCXDRIVETHU'], $NRVENDAREST),
								$DATASALE['VRTXSEVENDA'],
                                $DATASALE['FIDELITYDISCOUNT'],
                                $DATASALE['FIDELITYVALUE'],
                            	$motivoDesconto,
                            	$CDGRPOCORDESC,
                            	$DSOBSFINVEN,
                            	$DELIVERY,
                            	$VRACRCOMANDA,
                            	isset($DATASALE['REPIQUE'])? $DATASALE['REPIQUE'] : null
							);

							if (!$result['error']) {
								$this->paymentService->updateTaxaServico($CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA);
							}
						} else {
							$CDSENHAPED = !empty($PRODSENHAPED) ? $PRODSENHAPED : $this->utilAPI->generateCDSENHAPEDodhenPOS($NRORG, $CDLOJA, $CDFILIAL, $session['IDSENHACUP']);

							$ITEMVENDA = self::handleProducts($session, $ITEMVENDA, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $CDVENDEDOR);
							$result = $this->vendaAPI->venda(
								$NRORG,
								$CDFILIAL,
								$CDLOJA,
								$CDCAIXA,
								$CDVENDEDOR,
								$session['CDOPERADOR'],
								$openingDate,
								$DTVENDA,
								$DATASALE['TOTAL'],
								$ITEMVENDA,
								$TIPORECE,
								$NMCONSVEND,
								$NRINSCRCONS,
								null, // NRSEQVENDA
								$VRTROCOVEND,
								$EMAIL, // EMAIL
								$VRDESCONTO,
								$CDCLIENTE,
								$CDCONSUMIDOR,
								$simulatePrinter,
								$simulateSaleValidation,
								$this->getIDORIGEMVENDA($session['IDMODULO'], $session['IDUTCXDRIVETHU']),
								$CDSENHAPED,
	                            $NRVENDAREST,
	                            $NRCOMANDA,
                                $DATASALE['FIDELITYDISCOUNT'],
                                $DATASALE['FIDELITYVALUE'],
                            	$motivoDesconto,
                            	$CDGRPOCORDESC,
                            	$DSOBSFINVEN,
                            	$ITVENDADES,
                            	isset($DATASALE['REPIQUE'])? $DATASALE['REPIQUE'] : null
							);
							if (!$result['error']){
								if (!empty($result['paramsImpressora'])) {
									$result['paramsImpressora'] = array($result['paramsImpressora']);
								}
							}

							if (!$result['error'] && $session['IDIMPPEDPROD'] === 'S') {
								$printOrderCupomResult = $this->paymentService->printOrderCupom($CDFILIAL, $CDLOJA, $ITEMVENDA, $CDVENDEDOR, $CDSENHAPED, $result['NRSEQVENDA'], $session['CDCAIXA']);

								if (isset($printOrderCupomResult['paramsImpressora']) && count($printOrderCupomResult['paramsImpressora']) > 0){
									$result['paramsImpressora'] = array_merge($result['paramsImpressora'], $printOrderCupomResult['paramsImpressora']);
								}

								if (isset($printOrderCupomResult['PedidoSmart']['paramsImpressora']) && count($printOrderCupomResult['PedidoSmart']['paramsImpressora']) > 0){
									$result['impressaoPedidoSmart'] = $printOrderCupomResult['PedidoSmart']['paramsImpressora'];
								}

								if ($printOrderCupomResult['error']) {
									if (!empty($result['mensagemImpressao'])){
										$result['mensagemImpressao'] .= '<br>';
									}
									$result['mensagemImpressao'] .= 'Ocorreu um problema na impressão do cupom de pedido. ' .  $printOrderCupomResult['message'];
								}
							}
						}

						// valida impressão do comprovante TEF
						if (!$result['error'] && !$simulatePrinter && !$simulateSaleValidation) {

							$this->paymentService->logServDesc($logServico, $logDesconto, $NRMESA, $NRCOMANDA, $session['CDFILIAL'], $session['CDCAIXA'], $session['CDOPERADOR'], $supervisorServ, $supervisorDesc, $result['NRSEQVENDA']);
							// verifica se há comprovantes a serem imprimidos
							$arrTiporece = array_filter($TIPORECE, function($recebimento){
								return !empty($recebimento['STLPRIVIA']);
							});

							if (!empty($arrTiporece)){
								$printTEFVoucherResult = $this->paymentService->printTEFVoucher($arrTiporece, $CDFILIAL, $CDCAIXA, $NRORG);
								if(isset($printTEFVoucherResult['paramsImpressora'])&&count($printTEFVoucherResult['paramsImpressora'])>0){
									$result['paramsImpressora'] = array_merge($result['paramsImpressora'], $printTEFVoucherResult['paramsImpressora']);
								}
								if ($printTEFVoucherResult['error']){
									if (!empty($result['mensagemImpressao'])){
										$result['mensagemImpressao'] .= '<br>';
									}
									$result['mensagemImpressao'] .= $printTEFVoucherResult['message'];
								} else {
									if (!empty($printTEFVoucherResult['data'])){
	                                    $result['dadosImpressao']['TEFVOUCHER'] = array();
	                                    foreach ($printTEFVoucherResult['data'] as $tefData){
	                                        array_push($result['dadosImpressao']['TEFVOUCHER'], $tefData);
	                                    }
									}
								}
							}
						}
					} else {
						$result = $this->paymentService->initiatesAnticipate(
							$chave,
							$DATASALE,
							$TIPORECE,
							$CDCLIENTE,
							$CDCONSUMIDOR,
							$NRMESA,
							$NRVENDAREST,
							$NRCOMANDA,
							$NRINSCRCONS,
							$arrayPosicoes
						);
					}

					if (!$result['error']) {
						$resultDadosMesa = $this->tableService->dadosMesa(
							$CDFILIAL,
							$CDLOJA,
							$NRCOMANDA,
							$NRVENDAREST,
							false
						);
						$result['IDSTMESAAUX'] = !empty($resultDadosMesa['IDSTMESAAUX']) ? $resultDadosMesa['IDSTMESAAUX'] : 'D';
					} else {
						$result['resetSaleCode'] = true;
					}

					$this->paymentService->updateSaleCode(
						$result['error'] ? 'E' : 'C', // E - erro, C - concluído com sucesso
						json_encode($result),
						$session['CDFILIAL'],
						$NRVENDAREST,
						$NRCOMANDA,
						$saleCode
					);
				}
		 	} else {
	        	$result['message'] = 'Não foi possível realizar a venda. O caixa se encontra fechado.';
	        }
		} catch (\Exception $e) {
			Exception::logException($e);
			$result['message'] = $e->getMessage();
			$result['resetSaleCode'] = true;

			try {
				$this->paymentService->updateSaleCode(
					'E', // E - erro
					json_encode($result),
					$session['CDFILIAL'],
					$NRVENDAREST,
					$NRCOMANDA,
					$saleCode
				);
			} catch (\Exception $e) {
				// do nothing
			}
		}

		return $result;
	}

	private function handleIDTPEMISSAOFOS($IDTPEMISSAOFOS) {
		/*
		ECF -> 'E'
		SAT -> 'S'
		FNC -> 'N'
		*/
		switch($IDTPEMISSAOFOS) {
			case 'ECF':
				return 'E';
			case 'SAT':
				return 'S';
			case 'FNC':
				return 'N';
			default:
				throw new \Exception("Erro na atualização do campo IDTPEMISVEND", 1);
		}
	}

	private function buildObsItemVenda($group, array $obs, $obsDigita) {
		$obsNames = $this->paymentService->getOcorTxts($group, $obs);

		if (!empty($obsDigita)) {
			$obsNames[] = $obsDigita;
		}

		return implode('; ', $obsNames);
	}

	private function handleProducts($session, $ITEMVENDA, $CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $CDVENDEDOR) {
		$handledProducts = array();
		$ordemImp = 1;
        $voucherUsage = array();
		foreach ($ITEMVENDA as $ITEM){
			$newProduct = array();
			$ITEM = self::fillEmptyProperties($ITEM);
			$newProduct['NMPRODUTO'] = $ITEM['DSBUTTON'];
			$newProduct['NMPRODPROMOCAO'] = $ITEM['DSBUTTON'];
			$newProduct['CDPRODUTO'] = $ITEM['CDPRODUTO'];
			$newProduct['QTPRODVEND'] = $ITEM['QTPRODCOMVEN'];
			$newProduct['QTPRODCOMVEN'] = $ITEM['QTPRODCOMVEN'];
			$newProduct['VRUNITVEND'] = $ITEM['PRITEM'];
            $newProduct['VRUNITVENDCL'] = $ITEM['VRPRECITEMCL'];
            $newProduct['REALSUBSIDY'] = $ITEM['REALSUBSIDY'];
			$newProduct['NRLUGARMESA'] = '1';
			$newProduct['VRDESITVEND'] = $ITEM['VRDESITVEND'];
			$newProduct['VRACRITVEND'] = $ITEM['VRACRITVEND'];;
			$newProduct['IDSITUITEM'] = 'A';
			$newProduct['IDTIPOITEM'] = null;
			$newProduct['ORDEMIMP'] = $ordemImp;
			$newProduct['IDTIPOCOMPPROD'] = $ITEM['IDTIPOCOMPPROD'];
			$newProduct['IDIMPPROMOCAO'] = $ITEM['IDTIPOCOMPPROD'] == '3' ? 'S' : 'N';
			$newProduct['IDIMPPRODUTO'] = $ITEM['IDIMPPRODUTO'];
			$newProduct['TXPRODCOMVEN'] = !empty($ITEM['TXPRODCOMVEN']) ? $ITEM['TXPRODCOMVEN'] : null;
			$newProduct['CDSUPERVISOR'] = null;
			$newProduct['DSOBSITEMVENDA'] = $this->buildObsItemVenda($session['CDGRPOCORPED'], $ITEM['CDOCORR'], $ITEM['DSOCORR_CUSTOM']);
			$newProduct['DSOBSPEDDIGITA'] = $ITEM['DSOCORR_CUSTOM'];
			$newProduct['DTHRPRODCANVEN'] = $ITEM['DTHRPRODCANVEN'];
			$newProduct['OBSERVACOES'] = array_map(function($obs) use ($session) {
				return array(
					'CDOCORR' => $obs,
					'CDGRPOCOR' => $session['CDGRPOCORPED']
				);
			}, $ITEM['CDOCORR']);
			$newProduct['CDGRPOCOR'] = null;
            $newProduct['ATRASOPROD'] = $ITEM['ATRASOPROD'] == 'Y' ? $session['NRATRAPADRAO'] : null;
            $newProduct['IDORIGEMVENDA'] = ($ITEM['TOGO'] == 'Y' && $session['IDCTRLPEDVIAGEM'] == 'S') ? 'TGO_BAL' : null;
			$newProduct['DSOBSDESCIT'] = null;
			$newProduct['CDGRPOCORDESCIT'] = null;
			$newProduct['CDVENDEDOR'] = $CDVENDEDOR;
            $newProduct['CDPRODPROMOCAO'] = null;
			$ordemImpPromo = 0.01;
			$comboItems = array();
			foreach ($ITEM['PRODUTOS'] as $currentComboItem) {
				$newItem = array();
                $newItem['CDPRODPROMOCAO'] = $ITEM['CDPRODUTO'];
				$newItem['CDPRODUTO'] = $currentComboItem['CDPRODUTO'];
				$newItem['NMPRODUTO'] = $currentComboItem['DSBUTTON'];
				$newItem['QTPRODVEND'] = $currentComboItem['QTPRODCOMVEN'] * $ITEM['QTPRODCOMVEN'];
				$newItem['QTPRODCOMVEN'] = $currentComboItem['QTPRODCOMVEN'] * $ITEM['QTPRODCOMVEN'];
				$newItem['VRUNITVEND'] = $currentComboItem['PRECO'];
                $newItem['VRUNITVENDCL'] = $currentComboItem['VRPRECITEMCL'];
                $newItem['REALSUBSIDY'] = $currentComboItem['REALSUBSIDY'];
				$newItem['VRDESITVEND'] = $currentComboItem['VRDESITVEND'];
				$newItem['NRLUGARMESA'] = '1';
				$newItem['ATRASOPROD'] = $currentComboItem['ATRASOPROD'] == 'Y' ? $session['NRATRAPADRAO'] : null;
                $newItem['IDORIGEMVENDA'] = ($currentComboItem['TOGO'] == 'Y' && $session['IDCTRLPEDVIAGEM'] == 'S') ? 'TGO_BAL' : null;
				$newItem['ORDEMIMP'] = $ordemImp + $ordemImpPromo;
				$newItem['IDTIPOCOMPPROD'] = '1';
				$newItem['IDIMPPROMOCAO'] = 'X';
				$newItem['VRACRITVEND'] = $currentComboItem['ADDITION'];
				$newItem['IDSITUITEM'] = 'A';
				$newItem['IDTIPOITEM'] = null;
				$newItem['TXPRODCOMVEN'] = !empty($currentComboItem['TXPRODCOMVEN']) ? $currentComboItem['TXPRODCOMVEN'] : null;
				$newItem['OBSERVACOES'] = array_map(function($obs) use ($session) {
					return array(
						'CDOCORR' => $obs,
						'CDGRPOCOR' => $session['CDGRPOCORPED']
					);
				}, $currentComboItem['CDOCORR']);
				$newItem['IDAPLICADESCPR'] = $currentComboItem['IDAPLICADESCPR'];
				$newItem['IDPERVALORDES'] = $currentComboItem['IDPERVALORDES'];
				$newItem['CDGRPOCOR'] = null;
				$newItem['DSOBSDESCIT'] = null;
				$newItem['CDGRPOCORDESCIT'] = null;
				$newItem['CDVENDEDOR'] = $CDVENDEDOR;

                $newItem['VOUCHER'] = $this->checkVoucherUsage($currentComboItem, $voucherUsage);

                $productDetails = $this->vendaAPI->getProductDetails($newItem['CDPRODUTO']);
                $newItem['IDPESAPROD'] = $productDetails['IDPESAPROD'];
                $newItem['IDIMPRODUVEZ'] = $productDetails['IDIMPRODUVEZ'];

				array_push($comboItems, $newItem);

				$ordemImpPromo += 0.01;
			}

            $newProduct['VOUCHER'] = $this->checkVoucherUsage($ITEM, $voucherUsage);

            $productDetails = $this->vendaAPI->getProductDetails($newProduct['CDPRODUTO']);
            $newProduct['IDPESAPROD'] = $productDetails['IDPESAPROD'];
            $newProduct['IDIMPRODUVEZ'] = $productDetails['IDIMPRODUVEZ'];

            if (!empty($ITEM['CAMPANHA'])){
                $newProduct['CDCAMPCOMPGANHE'] = $ITEM['CAMPANHA'];
                if ($this->util->databaseIsOracle()){
                    $newProduct['DTINIVGCAMPCG'] = \DateTime::createFromFormat('Y-m-d H:i:s', $ITEM['DTINIVGCAMPCG']);
                }
                else {
                    $newProduct['DTINIVGCAMPCG'] = \DateTime::createFromFormat('Y-m-d H:i:s.u', $ITEM['DTINIVGCAMPCG']);
                }
            }
            else {
                $newProduct['CDCAMPCOMPGANHE'] = null;
                $newProduct['DTINIVGCAMPCG'] = null;
            }

			if (!empty($comboItems)){
				// rotina específica do Madero
				$this->util->validaDescontoDiferenciado($CDFILIAL, $ITEM['CDPRODUTO'], $comboItems, 'VRDESITVEND');
			}

			$newProduct['itensCombo'] = $comboItems;

			array_push($handledProducts, $newProduct);

			$ordemImp++;
		}

		return $handledProducts;
	}

    private function checkVoucherUsage($product, $voucherUsage){
        if (empty($product['VOUCHER'])) return null;
        else {
            $voucher = $this->paymentService->validateVoucher($product['CDPRODUTO'], null, null, $product['VOUCHER']['CDCUPOMDESCFOS']);
            // Verifica se o voucher já foi utilizado neste pedido.
            if (in_array($product['VOUCHER']['CDCUPOMDESCFOS'], $voucherUsage)){
                throw new \Exception("O voucher " . $product['VOUCHER']['CDCUPOMDESCFOS'] . " é de uso único, mas está sendo aplicado em mais de um produto. Certifique-se de que apenas um produto esteja com este voucher aplicado.");
            }
            else {
                array_push($voucherUsage, $product['VOUCHER']['CDCUPOMDESCFOS']);
            }
            return $voucher;
        }
    }

	private function fillEmptyProperties($item) {

		if (!isset($item['CDOCORR'])) {
			$item['CDOCORR'] = null;
		}
		if (!isset($item['DSOCORR_CUSTOM'])) {
			$item['DSOCORR_CUSTOM'] = null;
		}
		if (!isset($item['PRODUTOS'])) {
			$item['PRODUTOS'] = array();
		}
		if(!isset($item['DTHRPRODCANVEN'])){
			$item['DTHRPRODCANVEN'] = null;
		}
		return $item;
	}

	public function getTransactioncode(Request\Filter $request, Response $response) {
		try {
			$session  = $this->util->getSessionVars(null);
			$CDFILIAL = $session['CDFILIAL'];
			$CDCAIXA  = $session['CDCAIXA'];
			$dateTime = date_format(new \DateTime('NOW', new \DateTimeZone('America/Sao_Paulo')), 'd/m/Y');

			$this->util->newCode('SEQTEF'.$CDFILIAL.$CDCAIXA.$dateTime);
			$SEQTEF = $this->util->getNewCode('SEQTEF'.$CDFILIAL.$CDCAIXA.$dateTime, 10);
			$result = array('SEQTEF' => $SEQTEF);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('getTransactioncode', $result));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getConsumerLimit(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$CDCLIENTE = $params['CDCLIENTE'];
			$CDCONSUMIDOR  = $params['CDCONSUMIDOR'];
			$type  = $params['type'];

            $session = $this->util->getSessionVars(null);

			$CDFILIAL = $session['CDFILIAL'];
            $CDLOJA = $session['CDLOJA'];
            $CDOPERADOR = $session['CDOPERADOR'];
            $IDPERCOMVENCPDC = $session['IDPERCOMVENCPDC'];

			if($type == 'debito'){
                $this->consumerService->atualizaSaldoMoviClie($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR);
				$clientLimit = $this->consumerService->getLimitDebConsumer($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR);
			}
            else if($type == 'credito'){
				//ainda nao tratando mais de uma familia aqui
                $clientLimit = $this->consumerService->getLimiteCredDisponivel($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR);
			}
            else if($type == 'all') {
                $this->consumerService->atualizaSaldoMoviClie($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR);
                $clientLimit = array(
					'debito' => $this->consumerService->getLimitDebConsumer($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR),
					'credito' => $this->consumerService->getLimiteCredDisponivel($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR)
				);
			}

            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR
            );
            $consumerDetails = $this->consumerService->populaDadosConsumidor($params);
            $clientLimit['IDTPVENDACONS'] = $consumerDetails['IDTPVENDACONS'];
            $clientLimit['IDPERCOMVENCPDC'] = $IDPERCOMVENCPDC === 'S';

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('GetConsumerLimit', $clientLimit));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message(utf8_encode($e->getMessage())));
		}
	}

	public function formatPositions($positions){
		return array_map(function($position){
			return str_pad($position, 2, '0', STR_PAD_LEFT);
		}, $positions);
	}

	public function printTEFVoucher(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$session = $this->util->getSessionVars($params['chave']);

			$printTEFVoucherResult = $this->paymentService->printTEFVoucher($params['arrTEFVoucher'], $session['CDFILIAL'], $session['CDCAIXA'], $session['NRORG']);
			if ($printTEFVoucherResult['error']){
				$response->addMessage(new Message($printTEFVoucherResult['message']));
			} else {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('PrintTEFVoucher', $printTEFVoucherResult));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message('Ocorreu um problema na impressão do comprovante TEF. ' . $e->getMessage()));
		}
	}

    public function processQRCodeSale(Request\Filter $request, Response $response){
        $result = array(
            'error'   => true,
            'message' => '',
            'resetSaleCode' => true
        );

        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars($chave);
            $saleInfo = self::formatSaleCode($params['QRCODE']);

            $validaVenda = $this->paymentService->buscaVendaRealizada($saleInfo['FIL'], $saleInfo['NVR'], $saleInfo['NCD']);
            if (!empty($validaVenda)){
                throw new \Exception("Venda já processada.");
            }

            $consumer = $this->consumerService->populaDadosConsumidor(array('CDCLIENTE' => $saleInfo['CLIE'], 'CDCONSUMIDOR' => $saleInfo['CONS']));
            if (!empty($consumer) && empty($params['CPF'])){
                if (!empty($consumer['NRCPFRESPCON'])) $params['CPF'] = $consumer['NRCPFRESPCON'];
            }

            $saleInfo['chave'] = $params['chave'];
            $saleInfo['saleCode'] = $params['saleCode'] . $params['chave'];

            $saleInfo['TIPORECE'] = self::formatRec($saleInfo);
            $saleInfo['ITEMVENDA'] = self::formatProds($saleInfo['PRODS']);
            $saleInfo['DATASALE'] = self::prepareDatasale($saleInfo['TIPORECE'][0]['VRMOVIVEND']);

            $TIPORECE      = $saleInfo['TIPORECE'];
            $ITEMVENDA     = $saleInfo['ITEMVENDA'];
            $DATASALE      = $saleInfo['DATASALE'];
            $CDCLIENTE     = $saleInfo['CLIE'];
            $CDCONSUMIDOR  = $saleInfo['CONS'];
            $NRMESA        = "";
            $NRPESMESAVEN  = "";
            $NRVENDAREST   = $saleInfo['NVR'];
            $NRCOMANDA     = $saleInfo['NCD'];
            $chave         = $saleInfo['chave'];
            $saleCode      = $saleInfo['saleCode'];
            $arrayPosicoes = array();
            $TIPODESCONTO  = null;
            $NRINSCRCONS   = !empty($params['CPF']) ? $params['CPF'] : null;
            $EMAIL         = null;
            $NOMECONS      = null;

            $result = self::payAccount($TIPORECE, $ITEMVENDA, $DATASALE, $CDCLIENTE, $CDCONSUMIDOR, $NRMESA, $NRPESMESAVEN, $NRVENDAREST, $NRCOMANDA, $chave, $saleCode, $arrayPosicoes, $TIPODESCONTO, $NRINSCRCONS, $EMAIL, $NOMECONS, null, null, null, null, null, $session, null, null, null, false, null);
        } catch (\Exception $e){
            Exception::logException($e);
            $result['message'] = 'Erro ao processar a venda: ' . $e->getMessage();
        }

        $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('QRCodeSaleRepository', $result));
    }

    private function formatSaleCode($qrCode){
        try {
            $qrCode = preg_replace('/\*/', '"', $qrCode);
            $qrCode = preg_replace('/\$/', ': ', $qrCode);
            $qrCode = preg_replace('/</', '{', $qrCode);
            $qrCode = preg_replace('/>/', '}', $qrCode);
            $qrCode = preg_replace('/\(/', '[', $qrCode);
            $qrCode = preg_replace('/\)/', ']', $qrCode);
            return json_decode($qrCode, true);
        } catch (\Exception $e){
            throw new \Exception("QR code mal-formatado.");
        }
    }

    private function formatRec($saleInfo){
        try {
            return array(array(
                'CDTIPORECE' => $saleInfo['REC'][0]['CDTREC'],
                'IDTIPORECE' => $saleInfo['REC'][0]['IDTREC'],
                'VRMOVIVEND' => $saleInfo['REC'][0]['VRT'],
                'CDNSUHOSTTEF' => $saleInfo['NSU']
            ));
        } catch (\Exception $e){
            throw new \Exception("QR code mal-formatado.");
        }
    }

    private function formatProds($produtos){
        try {
            $formatedProducts = array();
            $id = 1;
            foreach($produtos as $prod){
                $productInfo = $this->paymentService->getProduct($prod['CDP']);
                if (empty($productInfo)) throw new \Exception('Produto ' . $prod['CDP'] . ' não encontrado.');
                $product = array(
                    "DSBUTTON" => $productInfo['NMPRODUTO'],
                    "CDPRODUTO" => $prod['CDP'],
                    "QTPRODCOMVEN" => $prod['QT'],
                    "PRITEM" => $prod['VR'],
                    "IDTIPOCOMPPROD" => $productInfo['IDTIPOCOMPPROD'],
                    "IDIMPPRODUTO" => $prod['IMPPROD'],
                    "TXPRODCOMVEN" => "",
                    "CDOCORR" => $prod['OBS'],
                    "DSOCORR_CUSTOM" => null,
                    "ATRASOPROD" => "N",
                    "TOGO" => "N",
                    "PRODUTOS" => array() // Não implementado.
                );
                array_push($formatedProducts, $product);
            }
            return $formatedProducts;
        } catch (\Exception $e){
            throw new \Exception("QR code mal-formatado.");
        }
    }

    private function prepareDatasale($paymentValue){
        return array(
              'TOTALVENDA'   => $paymentValue,
              'FALTANTE'     => 0,
              'VALORPAGO'    => $paymentValue,
              'TROCO'        => 0,
              'TOTAL'        => $paymentValue,
              'VRTXSEVENDA'  => 0,
              'VRDESCONTO'   => 0,
              'PCTDESCONTO'  => 0,
              'TIPODESCONTO' => 'P'
        );
    }

    public function savePayment(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
        	$params = $this->util->getParams($params);
        	$session = $this->util->getSessionVars(null);

        	$result = $this->paymentService->savePayment($params, $session);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SavePayment', array()));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function removePayment(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
        	$params = $this->util->getParams($params);
        	$session = $this->util->getSessionVars(null);

        	$result = $this->paymentService->handleRemovePayment($params, $session);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('RemovePayment', array()));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getPayments(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
        	$params = $this->util->getParams($params);
        	$session = $this->util->getSessionVars(null);

        	$result = $this->paymentService->getPayments($params, $session);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('GetPayments', $result));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

 	public function trataBandeirasCieloLio($bandeira, $tipoRecebimento) {
        //Utiliza o retorno de bandeira da Cielo LIO, GetNet, PagSeguro e transforma em código das bandeiras da SITEF.
        $bandeirasIntegracoes = array(
            'VISA'   		     => array ('1' => '00001','2' => '20002'), // Cielo lio / PagSeguro
            'VISA CREDITO'       => array ('00001'), // Getnet
            'VISA ELECTRON'      => array ('20002'), // Getnet
            'MASTERCARD'         => array ('1' => '00002','2' => '20001'), // Cielo lio / Getnet
            'MASTER'             => array ('1' => '00002','2' => '20001'), // PagSeguro
            'ELO'    		     => array ('1' => '00031','2' => '20032'),
            'ALELO'   	         => array ('F' => '10022','G' => '10021'),
            'SODEXO'   	         => array ('F' => '10029','G' => '10028'),
            'VR'   		         => array ('F' => '10048','G' => '10047'),
            'HIPERCARD'          => array ('20037'), // Cielo lio
            'HIPER'              => array ('1' => '00012', '2' => '20037'), // Getnet
            'SOROCRED'           => array ('00015'),
            'SOROCRED CREDITO'   => array ('00015'), // Getnet
            'SOROCRED DEBITO'    => array ('20071'), // Getnet
            'SOROCRED VOUCHER'   => array ('10037'), // Getnet
            'BEN VISA VALE'      => array ('F' => '10141','G' => '10140'),
            'AMEX'               => array ('00004'),
            'DINERS'             => array ('00003'),
            'SENFF'              => array ('1' => '10155','F' => '10169','G' => '10170'),
            'GOODCARD'           => array ('1' => '00130','2' => '20131','F' => '10134', 'G' => '10133'), // Cielo lio
            'GOOD CARD'          => array ('1' => '00130','2' => '20131','F' => '10134', 'G' => '10133'), // PagSeguro
            'GOOD CARD FUEL'     => array ('10135'), // Getnet
            'TICKET'             => array ('1' => '00072','2' => '20076','F' => '10025', 'G' => '10024'),
            'TICKET RESTAURANTE' => array ('10024'),
            'TICKET ALIMENTACAO' => array ('10025'),
            'AURA'               => array ('00013'),
            'AVISTA'             => array ('00045'),
            'BANESCARD'          => array ('1' => '00035','2' => '20036'),
            'BIQ F'              => array ('10144'),
            'BNB CLUB'           => array ('1' => '00173','2' => '20145'),
            'CABAL'              => array ('1' => '00030','2' => '20003'),
            'CALCARD'            => array ('00090'),
            'CREDSHOP'           => array ('00071'),
            'CRED F'             => array ('10149'),
            'DOTZ'               => array ('00182'),
            'FORT BRASIL'        => array ('00137'),
            'GREEN CARD'         => array ('F' => '10173','G' => '10174'),
            'NUTRICASH'          => array ('10004'),
            'PLANVALE'           => array ('F' => '10072','G' => '10073'),
            'SAPORE BENEFICIOS'  => array ('10009'),
            'SICRED'             => array ('1' => '00041','2' => '20042'),
            'TIP CARD'           => array ('00223'),
            'TRICARD'            => array ('00093'),
            'VALECARD'           => array ('F' => '10237','G' => '10238'),
            'VALE SHOP'          => array ('10158'),
            'VERDE CARD'         => array ('1' => '00086','2' => '20152'),
            'JCB'                => array ('00011'),
            'CREDZ'              => array ('00070'),
            'MAXXVAN'            => array ('1' => '00105','2' => '20106'),
            'CALCARD'            => array ('00090'),
            'BANRICOMPRAS'       => array ('1' => '00084','2' => '20085'),
            'BENESE'             => array ('1' => '00091','2' => '20092','G' => '10192'),
            'SIMCRED'            => array ('00218'),
            'VEROCARD'           => array ('F' => '10176','G' => '10177'),
            'VERDECARD'          => array ('1' => '00086','2' => '20152'), // PagSeguro
            'UP BRASIL'          => array ('F' => '999999','G' => '999998'),
            'ACCORD CARTOES'     => array ('F' => '999997','G' => '999996'),
            'AGIPLAN'            => array ('999995'),
            'AUTO EXPRESSO'      => array ('999994'),
            'BANDES'             => array ('1' => '999993','2' => '999992'),
            'BARIGUI FINANCEIRA' => array ('999991'),
            'BONUS CBA'          => array ('999990'),
            'CENTRAL CARD'       => array ('999989'),
            'CHINA UNION PAY'    => array ('1' => '999988','2' => '999987'),
            'CREDSHOP'           => array ('999986'),
            'CREDSYSTEM'         => array ('999985'),
            'CROMO UP'           => array ('999984'),
            'DACASA FINANCEIRA'  => array ('999983'),
            'E-CARDES'           => array ('999982'),
            'ESPLANADA'          => array ('999981'),
            'FACESP'             => array ('F' => '999980','G' => '999979'),
            'MINAS CARD'         => array ('999978'),
            'OBOE CARD'          => array ('999977'),
            'PLENO CARD'         => array ('F' => '999976','G' => '999975'),
            'REDECOMPRAS'        => array ('999974'),
            'SPOT PROMO'         => array ('999973'),
            'UNIK'               => array ('999972'),
            'VILLELA BENEFICIO'  => array ('999971'),
            'DISCOVER'           => array ('1' => '999970','2' => '999969'),
            'REDE COMPRAS'       => array ('1' => '999968','2' => '999967'),
            'OUROCARD'           => array ('1' => '999966','2' => '999965'),
            'BANRICARD'          => array ('999964'), // PagSeguro
            'GOODVALE'           => array ('999963') // Getnet
        );

		$bandeira = trim($bandeira);
        if (!array_key_exists($bandeira, $bandeirasIntegracoes)) {
            return $bandeira;
        } else if (!array_key_exists($tipoRecebimento, $bandeirasIntegracoes[$bandeira])) {
            if (!array_key_exists(0, $bandeirasIntegracoes[$bandeira])) {
                return $bandeira;
            } else {
                return $bandeirasIntegracoes[$bandeira][0];
            }
        } else {
            return $bandeirasIntegracoes[$bandeira][$tipoRecebimento];
        }
    }
    public function validateVoucher(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);

            $voucher = $this->paymentService->validateVoucher($params['CDPRODUTO'], $params['NRVENDAREST'], $params['NRCOMANDA'], $params['CDCUPOMDESCFOS']);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('VoucherRepository', $voucher));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

}
