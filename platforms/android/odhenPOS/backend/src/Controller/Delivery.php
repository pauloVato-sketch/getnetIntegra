<?php

namespace Controller;

use Zeedhi\DTO\Response\Notification;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class Delivery extends \Zeedhi\Framework\Controller\Simple {

	protected $util;
	protected $waiterMessage;
	protected $deliveryService;
	protected $impressaoDeliveryAPI;
	protected $generalFunctions;
	protected $vendaAPI;
	protected $registerService;
	protected $paymentService;
	protected $comandaService;

	public function __construct(
		\Util\Util $util,
		\Util\WaiterMessage $waiterMessage,
		\Service\Delivery $deliveryService,
		\Odhen\API\Service\ImpressaoDelivery $impressaoDeliveryAPI,
		\Doctrine\ORM\EntityManager $entityManager,
		\Service\GeneralFunctions $generalFunctions,
		\Service\Order $orderService,
		\Odhen\API\Service\Venda $vendaAPI,
		\Service\Register $registerService,
		\Service\Payment $paymentService,
		\Odhen\API\Service\Comanda $comandaService
	){
		$this->util 		   		= $util;
		$this->waiterMessage   		= $waiterMessage;
		$this->deliveryService 		= $deliveryService;
		$this->impressaoDeliveryAPI = $impressaoDeliveryAPI;
		$this->entityManager 		= $entityManager;
		$this->generalFunctions  	= $generalFunctions;
		$this->orderService			= $orderService;
		$this->vendaAPI				= $vendaAPI;
		$this->registerService		= $registerService;
		$this->paymentService		= $paymentService;
		$this->comandaService		= $comandaService;
	}

	public function getDeliveryOrders(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);

			$orders = $this->deliveryService->getDeliveryOrders($params);
			
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliveryRepository', $orders));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getDeliveryOrdersControl(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);

			$orders = $this->deliveryService->getDeliveryOrdersControl($params);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliveryControlRepository', $orders));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}
	
	public function getVendedores(Request\Filter $request, Response $response){
		try{
			$params = $this->util->getSessionVars(null);
			$result = $this->deliveryService->getVendedores($params);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliveryServiceVendedoresRepository', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function getVendedoresChegada(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
    		$session = $this->util->getSessionVars(null);
			$params['CDLOJA'] = $session['CDLOJA'];
			$params['CDFILIAL'] = $session['CDFILIAL'];
			$result = $this->deliveryService->getVendedoresChegada($params);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliveryServiceVendedoresChegadaRepository', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function sendOrders(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$params['DTALTOPER'] = new \DateTime();
			$params['DTSAIDACMD'] = $params['DTALTOPER'];

			$result = $this->deliveryService->saidaComandas($params);
			

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliverySendOrders', $result));

		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function checkOutOrders(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$params['DTALTOPER'] = new \DateTime();
			$params['DTCHEGACMD'] = $params['DTALTOPER'];
			$result = $this->deliveryService->checkOutOrders($params);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliveryCheckOutOrders', $result));

		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function checkOrdersEntregadorDlv(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$result = $this->deliveryService->checkOrdersEntregadorDlv($params);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('PedidosEntreguesRepository', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function printDeliveryOrders(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$result = $this->impressaoDeliveryAPI->imprimeDelivery($params['ORDERS']);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DeliveryPrint', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function saveMovcaixadlv(Request\Filter $request, Response $response){
		try{
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$clienteConsumidor = $this->deliveryService->getClienteConsumidorDlv($params);
			$params['CDCLIENTE'] = $clienteConsumidor['CDCLIENTE'];
			$params['CDCONSUMIDOR'] = $clienteConsumidor['CDCONSUMIDOR'];
			$this->deliveryService->deleteMovcaixadlv($params);
			foreach ($params['RECEBIMENTOS'] as $key => $recebimento) {
				$params['IDTIPOMOVIVEDLV'] 	= 'E';
				$params['CDTIPORECE'] 		= $recebimento['CDTIPORECE'];
				$params['VRMOVIVENDDLV'] 	= $recebimento['VRMOVIVENDDLV'];
				$this->util->newCode('NRSEQMOVDLV'.$params['CDFILIAL'].$params['NRVENDAREST']);
				$params['NRSEQMOVDLV'] = $this->util->getNewCode('NRSEQMOVDLV'.$params['CDFILIAL'].$params['NRVENDAREST'], 10);
				$this->deliveryService->saveMovcaixadlv($params);
			}
			$movcaixadlv = $this->deliveryService->getMovcaixadlv($params);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('Movcaixadlv', $movcaixadlv));
			$connection->commit();
		} catch (\Exception $e){
			$connection->rollback();
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function deliveryReprintCupomFiscal(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$session = $this->util->getSessionVars(null);
			$reprintType = 'C';
			$saleCodes = $params['ORDERS'];
			foreach ($saleCodes as $saleCode) {
				if(!isset($saleCode['NRNOTAFISCALCE'])){
					$saleCode['NRNOTAFISCALCE'] = $this->deliveryService->getNrNotaFiscal($params['ORDERS'][0][0])['NRNOTAFISCALCE'];
				}
				$result = $this->generalFunctions->reprintSaleCoupon($session, $reprintType, $saleCode['NRNOTAFISCALCE']);
			}
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('deliveryReprintCupomFiscal', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function cancelDeliveryProduct(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			
			$DTHRPRODCANVEN = new \DateTime();
			$produto = $params['produto'];
			$produto = json_decode($produto);
			$produto = array(
				'NRVENDAREST'    => $produto->NRVENDAREST,
				'NRPRODCOMVEN'   => $produto->NRPRODCOMVEN,
				'CDPRODPROMOCAO' => $produto->CDPRODPROMOCAO,
				'NRSEQPRODCOM'   => $produto->NRSEQPRODCOM,
				'NRSEQPRODCUP'   => $produto->NRSEQPRODCUP,
				'codigo'         => $produto->codigo,
				'quantidade'     => $produto->quantidade,
				'composicao'     => $produto->composicao,
				'nrcomanda'      => $params['NRCOMANDA']
			);

			$params['motivo'][0] = array(
				'CDGRPOCOR' => null,
				'CDOCORR' => null,
				'TXPRODCOMVEN' => null
			);
			$dataset = array(
				'chave'        	 => $params['chave'],
				'mode'         	 => 'C',
				'NRCOMANDA'    	 => $params['NRCOMANDA'],
				'NRVENDAREST'  	 => $params['NRVENDAREST'],
				'produto'      	 => $produto,
				'motivo'       	 => $params['motivo'][0],
				'CDSUPERVISOR' 	 => $params['CDSUPERVISOR'],
				'IDPRODPRODUZ' 	 => $params['IDPRODPRODUZ'],
				'DTHRPRODCANVEN' => $DTHRPRODCANVEN
			);
			$this->deliveryService->deleteMovcaixadlv($params);		
			$this->deliveryService->updateComandaPendente($params);
			$result = $this->orderService->cancelaProduto($dataset);
			$result['products'] = $this->deliveryService->getProdutosDlv($params);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('deliveryReprintCupomFiscal', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function cancelDeliveryOrder(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$session = $this->util->getSessionVars(null);
			$DTHRPRODCANVEN = new \DateTime();

			$this->deliveryService->deleteMovcaixadlv($params);		
			$produtos = $this->deliveryService->getProdutosDlv($params);
			foreach ($produtos as $produto) {
				$produto = array(
					'NRVENDAREST'    => $produto['NRVENDAREST'],
					'NRPRODCOMVEN'   => $produto['NRPRODCOMVEN'],
					'CDPRODPROMOCAO' => $produto['CDPRODPROMOCAO'],
					'NRSEQPRODCOM'   => $produto['NRSEQPRODCOM'],
					'NRSEQPRODCUP'   => $produto['NRSEQPRODCUP'],
					'codigo'         => $produto['CDPRODUTO'],
					'quantidade'     => $produto['QTPRODCOMVEN'],
					'composicao'     => null,
					'nrcomanda'      => $params['NRCOMANDA']
				);
				$params['motivo'][0] = array(
					'CDGRPOCOR' => null,
					'CDOCORR' => null,
					'TXPRODCOMVEN' => null
				);
				
				$dataset = array(
					'chave'        	 => $params['saleCode'],
					'mode'         	 => 'C',
					'NRCOMANDA'    	 => $params['NRCOMANDA'],
					'NRVENDAREST'  	 => $params['NRVENDAREST'],
					'produto'      	 => $produto,
					'motivo'       	 => $params['motivo'][0],
					'CDSUPERVISOR' 	 => $params['CDSUPERVISOR'],
					'IDPRODPRODUZ' 	 => $params['IDPRODPRODUZ'],
					'DTHRPRODCANVEN' => $DTHRPRODCANVEN
				);
				$result = $this->orderService->cancelaProduto($dataset);
			}
			$deliveryData = array(array(
				'CDFILIAL' => $params['CDFILIAL'],
				'NRVENDAREST' => $params['NRVENDAREST'],
				'NRCOMANDA' => $params['NRCOMANDA'],
				'NRMESA' => null,
				'NRORG' => $session['NRORG'],
				'NRPESMESAVEN' => '1',
				'CDVENDEDOR' => $session['CDVENDEDOR'],
				'DSCOMANDA' => 'DLV_'.$params['NRCOMANDA'],
				'DTHRMESAFECH' => null
			));
			
			$openingDate = new \DateTime($this->registerService->getRegisterOpeningDate($session['CDFILIAL'], $session['CDCAIXA'])['DTABERCAIX']);
			$DTVENDA = new \DateTime();
			$IDORIGEMVENDA = $this->paymentService->getIDORIGEMVENDADLV($params['NRVENDAREST']);
			$result = $this->vendaAPI->vendaMesa(
				$session['NRORG'],
				$session['CDFILIAL'],
				$session['CDLOJA'],
				$session['CDCAIXA'],
				$session['CDVENDEDOR'],
				$session['CDOPERADOR'],
				$openingDate, // DTABERCAIX
				$DTVENDA,
				0, //TOTAL COMANDA
				array(), // TIPORECE
				null, // NMCONSVEND
				null, // NRINSCRCONS
				null, // CDSENHAPED
				null, // VRTROCOVEND
				null, // EMAIL
				null, // VRDESCVENDA_VALOR
				null, // CDCLIENTE
				null, // CDCONSUMIDOR
				false, // simulatePrinter
				false, // simulateSaleValidation
				$deliveryData,
				array(), // arrayPosicoes
				$IDORIGEMVENDA,
				null, // VRTXSEVENDA
				0, // FIDELITYDISCOUNT
				0, // FIDELITYVALUE
        		null, // motivoDesconto
        		null, // CDGRPOCORDESC
        		null, // DSOBSFINVEN
        		true // DELIVERY
			);
			$this->comandaService->mudarStatusComanda($params['CDFILIAL'], $params['NRVENDAREST'], $params['NRCOMANDA'], 'X');
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('deliveryReprintCupomFiscal', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

	public function concludeOrderDlv(Request\Filter $request, Response $response){
		try{
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$this->deliveryService->ConcludeOrderDlv($params);
			$result = array('Pedido concluido com sucesso.');
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConcludeOrderDlv', $result));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));	
		}
	}

}