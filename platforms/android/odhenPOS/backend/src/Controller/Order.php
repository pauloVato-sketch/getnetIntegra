<?php
namespace Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Zeedhi\DTO\Response\Notification;
use \Util\Exception;

class Order extends \Zeedhi\Framework\Controller\Simple {
	protected $waiterMessage;
	protected $params;
	protected $util;
	protected $callWaiterService;
	protected $consumerService;

	public function __construct(
		\Util\WaiterMessage $waiterMessage,
		\Controller\Params $params,
		\Util\Util $util,
		\Service\CallWaiter $callWaiterService,
		\Service\Consumer $consumerService
	){
		$this->waiterMessage = $waiterMessage;
		$this->params = $params;
		$this->util = $util;
		$this->callWaiterService = $callWaiterService;
		$this->consumerService = $consumerService;
	}

	public function login(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();
		$DSEMAILCONS = $params[0]['value'];
		$CDSENHACONSMD5 = $params[1]['value'];

		$clientData = $this->consumerService->getClientData();
		$consumer = $this->consumerService->getConsumerByMail($clientData[0]['CDCLIENTE'], $DSEMAILCONS);
		if (empty($consumer)){
			$response->addMessage(new Message($this->waiterMessage->getMessage('452'))); // Login e/ou senha inválidos.
		}
		else {
			$cryptedPass = $this->util->encrypt($CDSENHACONSMD5);
			if ($cryptedPass !== $consumer['CDSENHACONSMD5']){
				$response->addMessage(new Message($this->waiterMessage->getMessage('452'))); // Login e/ou senha inválidos.
			}
			else {
				$consumerDetails = $this->consumerService->getConsumerDetails($consumer['CDCLIENTE'], $consumer['CDCONSUMIDOR']);

				// Gets a list of tables.
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConsumerLoginRepository', $consumerDetails));
			}
		}
	}

	public function requestLogin(Request\Filter $request, Response $response){
		try {

			$params       =	$request->getFilterCriteria()->getConditions();
			$nome         = $params[0]['value'];
			$mesa         = $params[1]['value'];
			$frontVersion = $params[2]['value'];
			$ip           = $params[3]['value'];

			$checkVersion = $this->util->checkVersion($frontVersion);
			if ($checkVersion['versionOk'] == true){
				// valida se parâmetros estão preenchidos
				if ($nome == null) {
					$response->addMessage(new Message($this->waiterMessage->getMessage('434')));
				} else if ($mesa == null) {
					$response->addMessage(new Message($this->waiterMessage->getMessage('435')));
				} else {
					// ajusta o número da mesa para 4 dígitos
					$mesa = str_pad($mesa, 4, '0', STR_PAD_LEFT);

					// monta o array de parâmetro para o backend velho
					$dataset = array(
						'nome' => $nome,
						'mesa' => $mesa
					);

					$oldResult = $this->consumerService->requestAccess($dataset);

					if ($oldResult['funcao'] == '1') {
						// caso tenha dado tudo certo, troca de tela no frontend e armazena nracessouser para posterior login
						$nracessouser = array(
							array(
								'NRACESSOUSER' => $oldResult['NRACESSOUSER'],
								'NMUSUARIO'    => $oldResult['NMUSUARIO'],
								'NMMESA'       => $oldResult['NMMESA']
							)
						);
						$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderRequestLoginRepository', $nracessouser));
					}
					else if ($oldResult['error'] == '056') {
						// já foi autorizado, faz login
						$loginParams = array(
							'nracessouser' => $oldResult['NRACESSOUSER'],
							'ip'           => $ip
						);
						$loginData = self::handleLoginUser($loginParams);

						if ($loginData['error'] == false) {
							$loginData = $loginData['dados'];

							// send datasets to frontend
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OperatorRepository', $loginData['OperatorRepository']));
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsGroupRepository', $loginData['ParamsGroupRepository']));
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsMenuRepository', $loginData['ParamsMenuRepository']));
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsParameterRepository', $loginData['ParamsParameterRepository']));
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsObservationsRepository', $loginData['ParamsObservationsRepository']));
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableActiveTable', $loginData['TableActiveTable']));
						} else {
							//if ($loginData['error'] != '053') {
							//}
							$response->addMessage(new Message($loginData['message']));
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
						}
					} else {
						// caso tenha dado algum erro, manda mensagem para o usuário
						$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
						// se o acesso está para ser aprovado pelo garçom
						if ($oldResult['error'] == '055'){
							$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
						}
					}
				}
			}
			else {
				$response->addMessage(new Message('A versão do dispositivo não está compatível com a do servidor.<br><br>Versão do dispositivo: '.$checkVersion['frontVersion'].'<br>Versão do servidor: '.$checkVersion['backVersion']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		}
	}

	//Esta função irá retornar todas as mesas em uso para o frontend
	public function findAllTables(Request\Filter $request, Response $response) {
		try {

			$oldResult = $this->consumerService->findAllTables();


			// caso tenha dado tudo certo, só troca de tela no frontend (no back não faz nada)
			if ($oldResult['funcao'] == '1') {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderGetAccessRepository', $oldResult['solicitacoes']));
			} else {
				// caso tenha dado algum erro, manda mensagem para o usuário
				$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function allowUserAccess(Request\Filter $request, Response $response) {
		try {

			$params       = $request->getFilterCriteria()->getConditions();
			$chave        = $params[0]['value'];
			$nracessouser = $params[1]['value'];

			// prepara dataset para o backend velho
			$dataset = array(
				'chave' => $chave,
				'nracessouser' => $nracessouser
			);

			// chama o backend velho
			$oldResult = $this->consumerService->allowUserAccess($dataset);

			if($oldResult['funcao'] == '1'){
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}else{
				$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
			}


		} catch (\Exception $e) {
			Exception::logException($e);
		   $response->addMessage(new Message($e->getMessage()));
		}
	}

	public function callWaiter(Request\Filter $request, Response $response){
		$params       = $request->getFilterCriteria()->getConditions();

		$nracessouser = $params[0]['value'];
		$tipoChamada  = $params[1]['value'];

		$dataset = array(
				'NRACESSOUSER' => $nracessouser,
				'tipoChamada'  => $tipoChamada
		);
		$oldResult = $this->callWaiterService->callWaiter($dataset);
	}

	public function answerTable(Request\Filter $request, Response $response){
		$params       = $request->getFilterCriteria()->getConditions();
		$nracessouser = $params[0]['value'];

		$dataset = array(
			'NRACESSOUSER' => $nracessouser
		);

		$oldResult = $this->callWaiterService->answerTable($dataset);
		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
	}

	public function controlUserAccess(Request\Filter $request, Response $response) {
		try {

			$params       = $request->getFilterCriteria()->getConditions();
			$nracessouser = $params[0]['value'];
			$status       = $params[1]['value'];
			$chave        = $params[2]['value'];

			// prepara dataset para o backend velho
			$dataset = array(
				'nracessouser' => $nracessouser,
				'status'       => $status,
				'chave'        => $chave
			);

			// chama o backend velho
			$oldResult = $this->consumerService->controlUserAccess($dataset);

			if ($oldResult['funcao'] == '1') {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
			}

		} catch (\Exception $e) {
			Exception::logException($e);
		   $response->addMessage(new Message($e->getMessage()));
		}
	}

	public function checkBlockedUsers(Request\Filter $request, Response $response) {
		try {

			// chama o backend velho
			$oldResult = $this->consumerService->checkBlockedUsers();


			if ($oldResult['funcao'] == '1') {
				if (Empty($oldResult['retorno'])) {
					$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('OK' => true))));
				} else {
					$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('OK' => false))));
				}
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
			}

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getBlockedIps(Request\Filter $request, Response $response) {
		try {

			$params       = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];

			// chama o backend velho
			$oldResult = $this->consumerService->getAllBlockedIps($chave);

			$retorno = array();

			foreach ($oldResult as $each) {
				array_push($retorno, array(
					'vazio'        => '',
					'NMUSUARIO'     => $each['NMUSUARIO'],
					'HORABLOQUEIO' => 'bloqueado em: ' . $each['DTULTATU'],
					'DESCMESA'     => 'na mesa ' . $each['NMMESA'],
					'IP'           => $each['DSIP'],
					'NRACESSOUSER' => $each['NRACESSOUSER']
				));
			}
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderCheckBlockedUsers', $retorno));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function returnAccess(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$nmusuario = $params[0]['value'];

			$dataset = array(
				'nmusuario' => $nmusuario
			);

			$oldResult = $this->consumerService->checkAcess($dataset);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderReturnAccess', $oldResult));

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	private function handleLoginUser($loginParams) {
		// faz login
		$oldResult = $this->consumerService->userLogin($loginParams);
		if ($oldResult['funcao'] == '1') {

			$loginData = array();
			array_push($loginData, array(
				'chave'          => $oldResult['chave'],
				'mesa'           => $oldResult['NRMESA'],
				'NRACESSOUSER'   => $oldResult['NRACESSOUSER'],
				'NMUSUARIO'      => $oldResult['NMUSUARIO'],
				'IDACESSOUSER'   => $oldResult['IDACESSOUSER'],
				'NRCOMANDA'      => $oldResult['NRCOMANDA'],
				'NRVENDAREST'    => $oldResult['NRVENDAREST'],
				'modoHabilitado' => 'O'
			));

			$tableActiveTable = array();
			array_push($tableActiveTable, array(
				'NRCOMANDA'   => $oldResult['NRCOMANDA'],
				'NRVENDAREST' => $oldResult['NRVENDAREST']
			));

			$paramsData = $this->params->getParams($loginData[0]['chave'], $loginParams['ip']);

			if ($paramsData['error'] == false) {
				$paramsData = $paramsData['dados'];

				$dados = array(
					'OperatorRepository'           => $loginData,
					'ParamsGroupRepository'        => $paramsData['grupos'],
					'ParamsMenuRepository'         => $paramsData['cardapio'],
					'ParamsParameterRepository'    => $paramsData['parametros'],
					'ParamsObservationsRepository' => $paramsData['ALL_THE_OBSERVATIONS'],
					'TableActiveTable'             => $tableActiveTable
				);

				$result = array(
					'error' => false,
					'dados' => $dados
				);
			} else {
				$result = $paramsData;
			}
		} else {
			$result = array(
				'error' => true,
				'message' => $this->waiterMessage->getMessage($oldResult['error'])
			);
		}

		return $result;
	}

	public function loginUser(Request\Filter $request, Response $response) {
		try {

			$params       = $request->getFilterCriteria()->getConditions();
			$nracessouser = $params[0]['value'];
			$ip           = $params[1]['value'];

			// prepara dataset para o backend velho
			$loginParams = array(
				'nracessouser' => $nracessouser,
				'ip'           => $ip
			);
			$loginData = self::handleLoginUser($loginParams);

			if ($loginData['error'] == false) {
				$loginData = $loginData['dados'];

				// send datasets to frontend
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OperatorRepository', $loginData['OperatorRepository']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsGroupRepository', $loginData['ParamsGroupRepository']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsMenuRepository', $loginData['ParamsMenuRepository']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsParameterRepository', $loginData['ParamsParameterRepository']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsObservationsRepository', $loginData['ParamsObservationsRepository']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableActiveTable', $loginData['TableActiveTable']));

			} else {
				//if ($loginData['error'] != '053') { // @todo conferir com leo
				//    $response->addMessage(new Message($this->waiterMessage->getMessage($loginData['error'])));
				//}
				//if ($loginData['error'] == '057' || $loginData['error'] =='439') {
				//	$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('bloqueado', array(array('nothing' => 'nothing'))));
				//}
				$response->addMessage(new Message($loginData['message']));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		}
	}

	public function newConsumer(Request\Filter $request, Response $response) {
		try {

			$params = $request->getFilterCriteria()->getConditions();
			$NMCONSUMIDOR = $params[0]['value'];
			$DSEMAILCONS = $params[1]['value'];
			$NRCELULARCONS = $params[2]['value'];
			$CDSENHACONSMD5 = $params[3]['value'];
			$CDIDCONSUMID = $params[4]['value'];

			// Get data from client
			$clientData = $this->consumerService->getClientData();
			$CDCLIENTE = $clientData[0]['CDCLIENTE'];
			$CDCCUSCLIE = $clientData[0]['CDCCUSCLIE'];

			// Checks if consumer exists.
			$consumer = $this->consumerService->getConsumerByMail($CDCLIENTE, $DSEMAILCONS);

			// Register new consumer only if CDCCUSCLIE is not null.
			if ($CDCCUSCLIE == null){
				$response->addMessage(new Message($this->waiterMessage->getMessage('450')));
			}
			else if (!empty($consumer)){
				$response->addMessage(new Message($this->waiterMessage->getMessage('451')));
			}
			else {
				// Encrypts password.
				$CDSENHACONSMD5 = $this->util->encrypt($CDSENHACONSMD5);

				// Generates CDCONSUMIDOR
				$this->util->newCode('CONSUMIDORORD');
				$codConsumidor = $this->util->getNewCode('CONSUMIDORORD', 18);
				$CDCONSUMIDOR = "ORD" . $codConsumidor;

				// Defining not null fields
				$IDSITCONSUMI = '1';
				$IDCONSUMIDOR = 'C';
				$IDATUCONSUMI = 'S';
				$IDTPVENDACONS = '7';
				$IDIMPCPFCUPOM = 'S';
				$IDCADCONFLIBCOM = 'S';
				$IDPERCONSPRODEX = 'S';
				$IDTPSELMANHA = 'N';
				$IDTPSEALMOCO = 'N';
				$IDTPSELTARDE = 'N';
				$IDCRACHAMESTRE = 'N';

				$this->consumerService->registerConsumer($CDCLIENTE, $CDCONSUMIDOR, $CDSENHACONSMD5, $CDIDCONSUMID, $NMCONSUMIDOR, $DSEMAILCONS, $NRCELULARCONS, $IDSITCONSUMI, $CDCCUSCLIE, $IDCONSUMIDOR, $IDATUCONSUMI, $IDTPVENDACONS, $IDIMPCPFCUPOM, $IDCADCONFLIBCOM, $IDPERCONSPRODEX, $IDTPSELMANHA, $IDTPSEALMOCO, $IDTPSELTARDE, $IDCRACHAMESTRE);
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
				$response->addMessage(new Message($this->waiterMessage->getMessage('449')));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}
}

