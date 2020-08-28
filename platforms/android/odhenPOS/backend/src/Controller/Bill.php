<?php

namespace Controller;

use Zeedhi\DTO\Response\Notification;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class Bill extends \Zeedhi\Framework\Controller\Simple {

	protected $waiterMessage;
	protected $billService;
	protected $tableService;
	protected $paramsService;
	protected $entityManager;
	protected $comandaAPI;
	protected $util;

	public function __construct(
		\Util\WaiterMessage $waiterMessage,
		\Service\Bill $billService,
		\Service\Table $tableService,
		\Service\Params $paramsService,
		\Doctrine\ORM\EntityManager $entityManager,
		\Odhen\API\Service\Comanda $comandaAPI,
		\Util\Util $util
	){
		$this->waiterMessage   = $waiterMessage;
		$this->billService = $billService;
		$this->tableService = $tableService;
		$this->paramsService = $paramsService;
		$this->entityManager = $entityManager;
		$this->comandaAPI = $comandaAPI;
		$this->util = $util;
	}

	public function getBills (Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];
			$dataset = array(
				'chave' => $chave
			);
			$answer = $this->billService->consultaComandas($dataset);

			if ($answer['funcao'] == '1') {

				$bills = array();
				foreach ($answer['dados'] as $comanda) {
					$temp = array(
						'DSCOMANDA'    	 => $comanda['DSCOMANDA'],
						'NRVENDAREST'  	 => $comanda['NRVENDAREST'],
						'NRCOMANDA'    	 => $comanda['NRCOMANDA'],
						'LABELDSCOMANDA' => $comanda['LABELDSCOMANDA']
					);
					array_push($bills, $temp);
				}
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('BillRepository', $bills));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}


	public function openBill(Request\Filter $request, Response $response) {
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$chave        = $params[0]['value'];
			$DSCOMANDA    = str_pad($params[1]['value'], 10, '0', STR_PAD_LEFT);
			$CDCLIENTE    = $params[2]['value'];
			$CDCONSUMIDOR = $params[3]['value'];
			$nrMesa       = $params[4]['value'];
			$CDVENDEDOR   = $params[5]['value'];
			$DSCONSUMIDOR = $params[6]['value'];

			// prepare dataset for old backend
			$dataset = array(
				'chave'        => $chave,
				'dscomanda'    => $DSCOMANDA,
				'nrMesa'       => $nrMesa,
				'CDCLIENTE'    => $CDCLIENTE,
				'CDCONSUMIDOR' => $CDCONSUMIDOR,
				'CDVENDEDOR'   => $CDVENDEDOR,
				'DSCONSUMIDOR'   => $DSCONSUMIDOR
			);
			$answer = $this->billService->abreComanda($dataset);
			if ($answer['funcao'] == '1') {

				if (!empty($answer['dados']['CDCONSUMIDOR'])){
					$balanceDetails = self::getBalanceDetails($chave, $answer['dados']['CDCLIENTE'], $answer['dados']['CDCONSUMIDOR']);
				}
				else {
					$balanceDetails = null;
				}

				$return = array();
				array_push($return, array(
					'DSCOMANDA'      => $answer['dados']['DSCOMANDA'],
					'NRCOMANDA'      => $answer['dados']['NRCOMANDA'],
					'NRVENDAREST'    => $answer['dados']['NRVENDAREST'],
					'NRMESA'         => $answer['dados']['NRMESA'],
					'CDCLIENTE'      => $answer['dados']['CDCLIENTE'],
					'CDCONSUMIDOR'   => $answer['dados']['CDCONSUMIDOR'],
					'CDVENDEDOR'     => $answer['dados']['CDVENDEDOR'],
					'DETALHES'       => $balanceDetails,
					'NMCONSUMIDOR'   => $answer['dados']['NMCONSUMIDOR'],
					'LABELDSCOMANDA' => $answer['dados']['LABELDSCOMANDA']
				));
				
				// inicia a criação do XML para utilização na catraca.
				$this->billService->criaXMLCatraca($chave, $DSCOMANDA);
				
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('BillActiveBill', $return));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}

	}

	public function validateBill(Request\Filter $request, Response $response){
		try {

			$params    = $request->getFilterCriteria()->getConditions();
			$chave     = $params[0]['value'];
			$dsComanda = str_pad($params[1]['value'], 10, '0', STR_PAD_LEFT);

			// prepare dataset for old backend
			$dataset = array(
				'chave'     => $chave,
				'dscomanda' => $dsComanda
			);

			$answer = $this->billService->validaComanda($dataset);

			if ($answer['funcao'] == '1' ) {
				$activeBill = array();
				// Validação para comandas agrupadas.
				if($answer['vazio'] == 'N' && !empty($answer['dados']['DSCOMANDAPRI']) && $answer['dados']['IDSTCOMANDA'] == '4') {
					$response->addMessage(new Message('Operação bloqueada. A comanda informada está agrupada com a comanda ' . $answer['dados']['DSCOMANDAPRI']));	
				} else {
					if ($answer['vazio'] == 'N'){
						if ($answer['dados']['IDSTCOMANDA'] == '7'){
							//Comanda fechada
							$response->addMessage(new Message($this->waiterMessage->getMessage('432')));
						}

						if (!empty($answer['dados']['CDCONSUMIDOR'])){
							$balanceDetails = self::getBalanceDetails($chave, $answer['dados']['CDCLIENTE'], $answer['dados']['CDCONSUMIDOR']);
						}
						else {
							$balanceDetails = null;
						}

						array_push($activeBill, array(
							'DSCOMANDA'      => $answer['dados']['DSCOMANDA'],
							'NRCOMANDA'      => $answer['dados']['NRCOMANDA'],
							'NRVENDAREST'    => $answer['dados']['NRVENDAREST'],
							'NRMESA'         => $answer['dados']['NRMESA'],
							'CDCLIENTE'      => $answer['dados']['CDCLIENTE'],
							'CDCONSUMIDOR'   => $answer['dados']['CDCONSUMIDOR'],
							'CDVENDEDOR'     => $answer['dados']['CDVENDEDOR'],
							'DETALHES'       => $balanceDetails,
							'NMCONSUMIDOR'   => $answer['dados']['NMCONSUMIDOR'],
							'LABELDSCOMANDA' => $answer['dados']['LABELDSCOMANDA']
						));
						//Parametro 'R' retira o produto Couvert e consumação da ITCOMANDAVEN
						$this->tableService->controlaCouvert($chave, $answer['dados']['NRVENDAREST'], $answer['dados']['NRCOMANDA'], 'R', 'C');
						$this->tableService->controlaConsumacao($chave, $answer['dados']['NRVENDAREST'], $answer['dados']['NRCOMANDA'], 'R');
					}
					$activeBill[0]['VAZIO'] = $answer['vazio'];

					$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('BillActiveBill', $activeBill));
				}
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getBalanceDetails($chave, $CDCLIENTE, $CDCONSUMIDOR){
		return $this->paramsService->getBalanceDetails($chave, $CDCLIENTE, $CDCONSUMIDOR);
	}

	public function setTheTable(Request\Filter $request, Response $response){
		try {
			$params      = $request->getFilterCriteria()->getConditions();
			$chave       = $params[0]['value'];
			$NRMESA      = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];

			$dataset = array(
				'chave'     => $chave,
				'NRMESA'     => $NRMESA,
				'NRVENDAREST' => $NRVENDAREST
			);
			$answer = $this->tableService->setTable($dataset);
			$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('448'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SetTableRepository', $dataset));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getTraceAsString()));
		}
	}

	public function closeBill(Request\Filter $request, Response $response){
		try {
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();
			
			$params      = $request->getFilterCriteria()->getConditions();
			$chave       = $params[0]['value'];
			$NRMESA      = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];
			$NRCOMANDA   = $params[3]['value'];

			$session = $this->util->getSessionVars($chave);

			$dadosMesa = $this->tableService->getTablesFromTableGrouping($session['CDFILIAL'], $NRVENDAREST, $NRCOMANDA, $NRMESA, $session['NRORG'], $session['IDMODULO']);
			$arrayPosicoes = array ();
			$this->comandaAPI->liberaComanda($session['CDFILIAL'], $dadosMesa, $arrayPosicoes, $session['CDOPERADOR'], $session['CDLOJA'], 'CMD_PKC');

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array('error' => false)));

			$connection->commit();

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));;
		}
	}

	public function getGroupBills(Request\Filter $request, Response $response){
		try {
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();
			
			$session = $this->util->getSessionVars(null);

			$comandasAgrupadas = $this->billService->getGroupBills($session['CDFILIAL'], $session['CDLOJA']);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', $comandasAgrupadas));

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));;
		}
	}

	public function groupBills(Request\Filter $request, Response $response) {
		try {
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$session = $this->util->getSessionVars(null);
			$params       = $request->getFilterCriteria()->getConditions();

			$mainBill     = $params[0]['value'];
			$toGroupBills = $params[1]['value'];

			$groupResult = $this->billService->groupBills($session['CDFILIAL'], $session['CDLOJA'], $mainBill, $toGroupBills);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array()));

			$this->entityManager->getConnection()->commit();
		} catch (\Exception $e) {
			$this->entityManager->getConnection()->rollBack();
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));;
		}

	}

	public function ungroupBills(Request\Filter $request, Response $response) {
		try {
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$session = $this->util->getSessionVars(null);
			$params  = $request->getFilterCriteria()->getConditions();

			$toUngroupBills = $params[0]['value'];


			$ungroupResult = $this->billService->ungroupBills($session['CDFILIAL'], $session['CDLOJA'], $toUngroupBills);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array()));

			$this->entityManager->getConnection()->commit();
		} catch (\Exception $e) {
			$this->entityManager->getConnection()->rollBack();
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));;
		}

	}

}