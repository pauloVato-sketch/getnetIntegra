<?php

namespace Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Zeedhi\DTO\Response\Notification;
use \Util\Exception;

class Table extends \Zeedhi\Framework\Controller\Simple {

	protected $entityManager;
	protected $waiterMessage;
	protected $tableService;
	protected $consumerService;
	protected $callWaiterService;
	protected $impressaoService;
	protected $paramsService;
	protected $accountService;
	protected $KDS;
	protected $positionCodeService;
	protected $util;
	protected $orderService;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Util\WaiterMessage $waiterMessage,
		\Service\Table $tableService,
		\Service\Consumer $consumerService,
		\Service\CallWaiter $callWaiterService,
		\Service\Impressao $impressaoService,
		\Service\Params $paramsService,
		\Service\Account $accountService,
		\Service\KDS $KDS,
		\Service\PositionCode $positionCodeService,
		\Util\Util $util,
		\Service\Order $orderService
	){
		$this->entityManager = $entityManager;
		$this->waiterMessage = $waiterMessage;
		$this->tableService = $tableService;
		$this->consumerService = $consumerService;
		$this->callWaiterService = $callWaiterService;
		$this->impressaoService = $impressaoService;
		$this->paramsService = $paramsService;
		$this->accountService = $accountService;
		$this->KDS = $KDS;
		$this->positionCodeService = $positionCodeService;
		$this->util = $util;
		$this->orderService = $orderService;
	}

	public function open(Request\Filter $request, Response $response) {
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$chave        = $params[0]['value'];
			$NRMESA       = $params[1]['value'];
			$NRPESMESAVEN = $params[2]['value'];
			$CDCLIENTE    = $params[3]['value'];
			$CDCONSUMIDOR = $params[4]['value'];
			$CDVENDEDOR   = $params[5]['value'];
			$posicoes = $this->tableService->definePosition($params[6]['value']); // ajusta posições com 0 a esquerda

			$session = $this->util->getSessionVars($chave);

			// prepare dataset for old backend (login)
			$dataset = array(
				'chave'        => $chave,
				'mesa'         => $NRMESA,
				'quantidade'   => $NRPESMESAVEN,
				'CDCLIENTE'    => $CDCLIENTE,
				'CDCONSUMIDOR' => $CDCONSUMIDOR,
				'CDVENDEDOR'   => $CDVENDEDOR
			);
			$answerOpenTable = $this->tableService->abreMesa($dataset);

			if ($answerOpenTable['funcao'] == '1') {
				$return = array();
				array_push($return, array(
					'CDCLIENTE' => (isset($answerOpenTable['CDCLIENTE'])? $answerOpenTable['CDCLIENTE']: null),
					'CDSALA' => (isset($answerOpenTable['CDSALA'])? $answerOpenTable['CDSALA']: null),
					'ERROR' => false
				));

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OPENDATA', $return));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answerOpenTable['error'])));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OPENDATA', array(array('CDCLIENTE' => isset($answerOpenTable['CDCLIENTE']) ? $answerOpenTable['CDCLIENTE'] : null, 'ERROR' => true))));
			}

			$dataset = array(
				'chave' => $chave,
				'mesa'  => $NRMESA,
				'tipo'  => 'O'
			);
			$answerTable = $this->tableService->valAbertura($dataset);

			if (!$answerTable['error']) {
				// popula tabela de cliente por posição
				if (!empty($posicoes) && $answerOpenTable['funcao'] == '1'){
					$answerSetPosition = $this->tableService->modifyTablePosition($chave, $answerTable['NRVENDAREST'], $posicoes);

					if (!$answerSetPosition['status']){
						$response->addMessage(new Message($answerSetPosition['message']));
					}
				}

				// recebe posições da mesa
				$posicoes = $this->tableService->getPosition($session, $answerTable['NRVENDAREST'], array());

				$result = $this->prepareActiveTableDataset(
					$answerTable['retorno'],
                    $answerTable['DTHRABERMESA'],
					$answerTable['NRPESMESAVEN'],
					$NRMESA,
					$answerTable['retorno'],
					$answerTable['CDSALA'],
					$answerTable['NMMESA'],
					$answerTable['NRVENDAREST'],
					$answerTable['NRCOMANDA'],
					$answerTable['NRJUNMESA'],
					$posicoes,
					(isset($answerTable['CDCLIENTE'])? $answerTable['CDCLIENTE']: null),
					(isset($answerTable['NMRAZSOCCLIE'])? $answerTable['NMRAZSOCCLIE']: null),
					(isset($answerTable['CDCONSUMIDOR'])? $answerTable['CDCONSUMIDOR']: null),
					$answerTable['NRCPFRESPCON'],
					(isset($answerTable['NMCONSUMIDOR'])? $answerTable['NMCONSUMIDOR']: null),
					(isset($answerTable['CDVENDEDOR'])? $answerTable['CDVENDEDOR']: null),
					null,
					$answerTable['NRPOSICAOMESA'],
                    true,
					$answerTable['NMVENDEDORABERT']
				);

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableActiveTable', $result));
			}else{
				$response->addMessage(new Message($answerTable['message']));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	private function prepareActiveTableDataset(
		$IDSTMESAAUX, $DTHRABERMESA, $NRPESMESAVEN, $NRMESA, $STATUS,
		$CDSALA, $NMMESA, $NRVENDAREST, $NRCOMANDA,
		$NRJUNMESA, $posicoes, $CDCLIENTE, $NMRAZSOCCLIE,
		$CDCONSUMIDOR, $NRCPFRESPCON, $NMCONSUMIDOR, $CDVENDEDOR,
		$DETALHES, $NRPOSICAOMESA, $positionControl, $NMVENDEDORABERT
	)  {
			return array(
				array(
					'IDSTMESAAUX'   => $IDSTMESAAUX,
                    'DTHRABERMESA'  => $DTHRABERMESA,
					'NRPESMESAVEN'  => $NRPESMESAVEN,
					'NRMESA'        => $NRMESA,
					'STATUS'        => $STATUS,
					'CDSALA'        => $CDSALA,
					'NMMESA'        => $NMMESA,
					'NRVENDAREST'   => $NRVENDAREST,
					'NRCOMANDA'     => $NRCOMANDA,
					'NRJUNMESA'     => $NRJUNMESA,
					'posicoes'      => $posicoes,
					'CDCLIENTE'     => $CDCLIENTE,
					'NMRAZSOCCLIE'  => $NMRAZSOCCLIE,
					'CDCONSUMIDOR'  => $CDCONSUMIDOR,
					'NRCPFRESPCON'  => $NRCPFRESPCON,
					'NMCONSUMIDOR'  => $NMCONSUMIDOR,
					'CDVENDEDOR'    => $CDVENDEDOR,
					'DETALHES'      => $DETALHES,
					'NRPOSICAOMESA' => isset($NRPOSICAOMESA) ? $NRPOSICAOMESA : $NRPESMESAVEN,
					'POSITIONCONTROL' => $positionControl,
					'NMVENDEDORABERT' => isset($NMVENDEDORABERT) ? $NMVENDEDORABERT : ""
				)
			);
		}

	public function reopen(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];
			$mesa = $params[1]['value'];

            // If the table is being received, we can't reopen it. This code checks if the table is being received.
            $session = $this->util->getSessionVars($chave);
            $params = array(
                $session['CDFILIAL'],
                $mesa
            );
            $tableData = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCAVENDAREST", $params);
            $tableDetails = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $tableData['NRCOMANDA'], $tableData['NRVENDAREST']);

            if ($tableDetails['IDSTMESAAUX'] === 'R'){
                throw new \Exception('Não foi possível reabrir a mesa, pois ela está sendo recebida.');
            }

            // Reopens the table.
			$dataset = array(
				'chave' => $chave,
				'mesa'  => $mesa
			);
			$answer = $this->tableService->reOpenTable($dataset);

			if ($answer['funcao'] == '1') {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function cancelOpen(Request\Filter $request, Response $response) {
		try {

			$params = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];
			$nrMesa  = $params[1]['value'];

			$answer = $this->tableService->cancelaAberturaMesa($chave, $nrMesa);
			if ($answer['error'] === false) {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', $answer));
			} else {
				$response->addMessage(new Message($answer['message']));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function closeAccount(Request\Filter $request, Response $response) {
		try {
			$params           = $request->getFilterCriteria()->getConditions();
			$chave            = $params[0]['value'];
			$NRCOMANDA        = $params[1]['value'];
			$NRVENDAREST      = $params[2]['value'];
			$modo             = $params[3]['value'];
			$consumacao       = $params[4]['value'];
			$servico          = $params[5]['value'];
			$couvert          = $params[6]['value'];
			$valorConsumacao  = $params[7]['value'];
			$pessoas          = $params[8]['value'];
			$CDSUPERVISOR     = $params[9]['value'];
			$NRMESA    		  = $params[10]['value'];
			$IMPRIMEPARCIAL   = $params[11]['value'];
			$txporcentservico = $params[12]['value'];

			$session 		 = $this->util->getSessionVars($chave);
			$dataset = array(
				'chave'           => $chave,
				'NRCOMANDA'       => $NRCOMANDA,
				'NRVENDAREST'     => $NRVENDAREST,
				'consumacao'      => $consumacao,
				'servico'         => $servico,
				'couvert'         => $couvert,
				'valorConsumacao' => $valorConsumacao,
				'pessoas'         => $pessoas,
				'modo'            => $modo,
				'IMPRIMEPARCIAL'  => $IMPRIMEPARCIAL,
				'txporcentservico' => $txporcentservico
			);
			$answer = $this->accountService->fechaContaMesa($dataset);
			if ($answer['funcao'] == '1') {
				if ($modo == 'M') {
					$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('403'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
				} else if ($modo == 'C'){
					$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('431'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
				}

				if (!Empty($CDSUPERVISOR)) {
					if ($servico) {
						$idOperFos = 'ADD_TAX';
						$dsMotivoFos = 'Waiter - Adiciona taxa de serviço.';
						$dsLivreFos = 'Adiciona taxa de serviço da Mesa/Comanda: ' . $NRMESA . '/' . $NRCOMANDA . '_';
					} else {
						$idOperFos = 'RET_TAX';
						$dsMotivoFos = 'Waiter - Retira taxa de serviço.';
						$dsLivreFos = 'Retira taxa de serviço da Mesa/Comanda: ' . $NRMESA . '/' . $NRCOMANDA . '_';
					}
					$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], $idOperFos, $session['CDOPERADOR'], $CDSUPERVISOR, $dsMotivoFos, $dsLivreFos);
				}

				$dadosImpressao = array();
				// Validação para impressão Front que vem como String, impressão Saas e Back vem como Array.
				if (!empty($answer['dadosImpressao'])){
					if (!is_array($answer['dadosImpressao']) || !array_key_exists('impressaoBack', $answer['dadosImpressao'])){
						$dadosImpressao['dadosImpressao'] = $answer['dadosImpressao'];
					}
				}

				if (!empty($answer['dadosImpressao'])){
					if (!is_array($answer['dadosImpressao']) || !array_key_exists('impressaoBack', $answer['dadosImpressao']) || !$answer['dadosImpressao']['impressaoBack']['error']) {
						//Parcial impressa com sucesso.
						$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('417'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
					} else {
						//Erro na impressão da parcial de conta.
						$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification('Não foi possível imprimir a parcial de conta. ' . $answer['dadosImpressao']['impressaoBack']['message'], \Zeedhi\Framework\DTO\Response\Notification::TYPE_ERROR));
					}
				}

				$paramsImpressora = isset($answer['paramsImpressora']) ? $answer['paramsImpressora'] : array();

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('dadosImpressao', $dadosImpressao));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('paramsImpressora', $paramsImpressora));

			}
			else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => $e->getMessage()))));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('dadosImpressao', array('')));
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function group(Request\Filter $request, Response $response) {
		try {

			$params = $request->getFilterCriteria()->getConditions();

			$chave = $params[0]['value'];
			$mesa = $params[1]['value'];
			$listaMesa = $params[2]['value'];

			$listaMesas = json_decode($listaMesa);

			$dataset = array(
				'chave'       => $chave,
				'mesa'        => $mesa,
				'listaMesas'  => $listaMesas
			);
			$answer = $this->tableService->agruparMesas($dataset);

			if ($answer['funcao'] == '1') {
				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('405'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function split(Request\Filter $request, Response $response) {
		try {

			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]['value'];
			$NRCOMANDA   = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];
			$listaMesa   = $params[3]['value'];

			$listaMesas = json_decode($listaMesa);

			$dataset = array(
				'chave'       => $chave,
				'NRCOMANDA'   => $NRCOMANDA,
				'NRVENDAREST' => $NRVENDAREST,
				'listaMesas'  => $listaMesas
			);

			$answer = $this->tableService->separarMesas($dataset);

			if ($answer['funcao'] == '1') {
				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('408'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function sendMessage(Request\Filter $request, Response $response) {
		try {
			$params      = $request->getFilterCriteria()->getConditions();
			$chave       = $params[0]['value'];
			$NRCOMANDA   = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];
			$impressoras = $params[3]['value'];
			$mensagem    = $params[4]['value'];
			$historico   = $params[5]['value'];
			if ($historico == 'vazio') {
				$historico = null;
			}

			$modo = $params[6]['value'];

			$impressora = json_decode($impressoras);

			$dataset = array(
				'chave'          => $chave,
				'NRCOMANDA'      => $NRCOMANDA,
				'NRVENDAREST'    => $NRVENDAREST,
				'nrimpressora'   => $impressora,
				'mensagem'       => $mensagem,
				'historico'      => $historico,
				'modoHabilitado' => $modo
			);

			$answer = $this->impressaoService->enviaMensagemProducao($dataset);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', $answer));
			if($answer['error']){
				$response->addMessage(new Message($answer['message']));
			}else{
				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('402'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
			}

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getTables(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$dataset = array(
				'chave' => $params[0]['value']
			);

			$answer = $this->tableService->consultaMesas($dataset);

			if ($answer['funcao'] == '1'){

				unset($answer['funcao']);

				$tableData = array();
				foreach ($answer as $mesa){
					// diponível
					if ($mesa['status'] == 'D'){
						$sprite = 'mesa-vazia';
						if ($mesa['reserva'] == 'R') $consumption = 'reserva';
						else $consumption = '';
					}
					// em recebimento
					else if ($mesa['status'] == 'R'){
						if ($mesa['agrupada'] == 'S') $sprite = 'mesa-agrupada';
						else $sprite = 'mesa-ocupada';
						$consumption = 'em-pagamento';
					}
					// conta solicitada
					else if ($mesa['status'] == 'S'){
						if ($mesa['agrupada'] == 'S') $sprite = 'mesa-agrupada';
						else $sprite = 'mesa-ocupada';
						$consumption = 'conta';
					}
					// mesa paga
					else if ($mesa['status'] == 'P'){
						if ($mesa['agrupada'] == 'S') $sprite = 'mesa-agrupada';
						else $sprite = 'mesa-ocupada';
						$consumption = 'mesa-paga';
					}
					// ocupada
					else if ($mesa['status'] == 'O'){
						if ($mesa['agrupada'] == 'S') $sprite = 'mesa-agrupada';
						else $sprite = 'mesa-ocupada';
						if ($mesa['consumo'] == 'S') $consumption = 'sem-consumo';
						else $consumption = '';
					}
					if ($mesa['IDATRASO'] == 'S') $delayed = 'delayedProds';
					else $delayed = '';

					if ($mesa['numerodosagrupamentos'] != '') $mesa['numerodosagrupamentos'] = '(' . $mesa['numerodosagrupamentos'] . ')';

					array_push($tableData, array(
						'NRMESA'          => $mesa['CODIGO'],
						'NRCOMANDA'       => $mesa['NRCOMANDA'],
						'NRVENDAREST'     => $mesa['NRVENDAREST'],
						'IDSTMESAAUX'     => $mesa['status'],
						'NMMESA'          => $mesa['NMMESA'],
						'CDSALA'          => $mesa['CDSALA'],
						'NMSALA'          => $mesa['NMSALA'],
						'reserva'         => $mesa['reserva'],
						'agrupada'        => $mesa['agrupada'],
						'NRPESMESAVEN'    => $mesa['pessoas'],
						'NRPOSICAOMESA'   => $mesa['NRPOSICAOMESA'],
						'NMVENDEDORABERT' => $mesa['NMVENDEDORABERT'],
						'mesasAgrupadas'  => $mesa['mesasAgrupadas'],
						'NRJUNMESA'       => $mesa['numerodosagrupamentos'],
						'sprite'          => $sprite,
						'consumption'     => $consumption,
						'delayedProducts' => $delayed
					));
				}

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableRepository', $tableData));

				//From here, its the Order's notifications.
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderGetAccessRepository', array()));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderGetCallRepository', array()));

				// self::getAccess($request, $response);
				// self::getCall($request, $response);
			}
			else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getAccess(Request\Filter $request, Response $response){
		// busca lista de acessos pendentes
		try {
			// instanciando a classe de mensagens (importante)

			// chama o backend velho
			$oldResult = $this->consumerService->listUserAccess();

			$listCalls = array();
			// Formatando a data array para ser retornado

			foreach ($oldResult["solicitacoes"] as $solicitacao) {
				$tempo = $solicitacao["TEMPO"];
				$tempo = date('i:s',mktime(0,0,$tempo,01,01,2000))." seg";
				$solicitacaoFront = array(
					'vazio'         => '',
					'grupo'         => 'Liberação de Acesso',
					'tempo'         => $tempo,
					'USERMESA'      => $solicitacao["USERMESA"],
					'mesa'          => $solicitacao["NRMESA"],
					'NMMESA'        => $solicitacao["NMMESA"],
					'nracessouser'  => $solicitacao["NRACESSOUSER"],
					'widgetName'    => 'formLiberacao'
				);
				array_push($listCalls, $solicitacaoFront);
			}
			// caso tenha dado tudo certo, só troca de tela no frontend (no back não faz nada)
			if ($oldResult['funcao'] == '1') {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderGetAccessRepository', $listCalls));
			} else {
				// caso tenha dado algum erro, manda mensagem para o usuário
				$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			 $response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getCall(Request\Filter $request, Response $response){
		try {
			// instanciando a classe de mensagens (importante)

			// chama o backend velho
			$oldResult = $this->callWaiterService->getCall();


			// caso tenha dado tudo certo, só troca de tela no frontend (no back não faz nada)
			if ($oldResult['funcao'] == '1') {

				$listCalls = array();
			//Este forech vai carregar o arrey que vamos retornar no repositorio
				foreach ($oldResult['chamadas'] as $indice) {
					if ($indice['IDTIPOCHAMADA'] == 'C') {
						$labelUser = $indice["NMUSUARIO"] . " na mesa ";
					} else {
						$labelUser = $indice["NMUSUARIO"] . " solicitando conta na mesa ";
					}

					$tempo = $indice["TEMPO"];
					$tempo = date('i:s',mktime(0,0,$tempo,01,01,2000))." seg";

					array_push($listCalls, array(

						'vazio'         => '',
						'grupo'         => 'chamadas',
						'tempo'         => $tempo,
						'USERMESA'      => $labelUser,
						'mesa'          => $indice["NRMESA"],
						'NMMESA'        => $indice["NMMESA"],
						'nracessouser'  => $indice["NRACESSOUSER"],
						'widgetName'    => 'formAccess'
					));
				}

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OrderGetCallRepository', $listCalls));
			} else {
				// caso tenha dado algum erro, manda mensagem para o usuário
				$response->addMessage(new Message($this->waiterMessage->getMessage($oldResult['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($this->waiterMessage->getMessage($e->getMessage())));
		}
	}

	public function validateOpening(Request\Filter $request, Response $response){
		try {
			$params            = $request->getFilterCriteria()->getConditions();
			$chave             = $params[0]['value'];
			$nrMesa            = $params[1]['value'];
			$statusVisualizado = $params[2]['value'];
			// modo pode ser M ou O (mesa ou order)
			$modo              = $params[3]['value'];

			$session = $this->util->getSessionVars($chave);
			$dataset = array(
				'chave' => $chave,
				'mesa'  => $nrMesa,
				'tipo'  => $statusVisualizado
			);
			$answer = $this->tableService->valAbertura($dataset);

			if (!$answer['error']) {
				// this will only change if the table status is different from 'D' AND the table doesn't exists in VENDAREST anymore
				$statusVisualizado = $answer['IDSTMESAAUX'];
				if ($modo == 'M') { // se for modo mesa
					$retorno = '';
					if ($answer['retorno'] ==  'ABERTA') {
						$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('419'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
					} else if ($answer['retorno'] == 'DISPONIVEL') {
						$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('420'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
					} else if ($answer['retorno'] == 'SOLICITADA') {
						// This message must be shown as part of the front end in order to give the user the
						// choice to reopen the table after the bill has been ordered. The message code is 418.
					} else if ($answer['retorno'] == 'RECEBIMENTO') {
						$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('424'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_ALERT));
					} else if ($answer['retorno'] == 'OCUPADA') {
						$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('419'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_ALERT));
					} else if ($answer['retorno'] == 'OK') {
						$retorno = $statusVisualizado;
					}
				} else if ($modo == 'O') { // se for modo order não valida status
					$retorno = $statusVisualizado;
				}

				if (!empty($answer['CDCONSUMIDOR'])) {
					$balanceDetails = self::getBalanceDetails($chave, $answer['CDCLIENTE'], $answer['CDCONSUMIDOR']);
				} else {
					$balanceDetails = null;
				}
				// recebe posições da mesa
				$posicoes = $this->tableService->getPosition($session, $answer['NRVENDAREST'], array());

                $this->tableService->resetPositionControl($session['CDFILIAL'], $answer['NRVENDAREST'], $session['CDOPERADOR']);
                $positionControl = $this->tableService->getPositionControlDetails($session['CDFILIAL'], $answer['NRVENDAREST'], $session['CDOPERADOR']);
                if ($answer['currentStatus'] === 'R' && $answer['NRPOSICAOMESA'] == count($positionControl)){
                    $positionControl = false;
                }
                else {
                    $positionControl = true;
                }

				$result = $this->prepareActiveTableDataset(
					$retorno,
                    $answer['DTHRABERMESA'],
					$answer['NRPESMESAVEN'],
					$answer['NRMESA'],
					$answer['retorno'],
					$answer['CDSALA'],
					$answer['NMMESA'],
					$answer['NRVENDAREST'],
					$answer['NRCOMANDA'],
					$answer['NRJUNMESA'],
					$posicoes,
					!empty($answer['CDCLIENTE']) ? $answer['CDCLIENTE'] : null,
					!empty($answer['NMRAZSOCCLIE']) ? $answer['NMRAZSOCCLIE'] : null,
					!empty($answer['CDCONSUMIDOR']) ? $answer['CDCONSUMIDOR'] : null,
					$answer['NRCPFRESPCON'],
					!empty($answer['NMCONSUMIDOR']) ? $answer['NMCONSUMIDOR'] : null,
					!empty($answer['CDVENDEDOR']) ? $answer['CDVENDEDOR'] : null,
					$balanceDetails,
					$answer['NRPOSICAOMESA'],
                    $positionControl,
					$answer['NMVENDEDORABERT']
				);

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableActiveTable', $result));
			} else {
				$response->addMessage(new Message($answer['message']));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getBalanceDetails($chave, $CDCLIENTE, $CDCONSUMIDOR){
		return $this->paramsService->getBalanceDetails($chave, $CDCLIENTE, $CDCONSUMIDOR);
	}

	public function getMessageHistory (Request\Filter $request, Response $response) {
		try {
			$params      = $request->getFilterCriteria()->getConditions();
			$chave       = $params[0]['value'];
			$NRCOMANDA   = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];

			$dataset = array(
				'chave'     => $chave,
				'NRCOMANDA' => $NRCOMANDA,
				'NRVENDAREST' => $NRVENDAREST
			);
			$answer = $this->impressaoService->buscaMensagemImpressa($dataset);

			if ($answer['funcao'] == '1'){
				$retorno = array(array(
					'TXMOTIVCANCE' => $answer['retorno']
				));

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableGetMessageHistory', $retorno));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function transferTable(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave        = $params[0]['value'];
			$NRCOMANDA    = $params[1]['value'];
			$NRVENDAREST  = $params[2]['value'];
			$destino      = $params[3]['value'];
            $CDSUPERVISOR = $params[4]['value'];

			$dataset = array(
				'chave'        => $chave,
				'NRCOMANDA'    => $NRCOMANDA,
				'NRVENDAREST'  => $NRVENDAREST,
				'mesaDestino'  => $destino,
                'CDSUPERVISOR' => $CDSUPERVISOR
			);
			$answer = $this->tableService->transfereMesa($dataset);
			if ($answer['funcao'] == '1'){
				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('409'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			}
			else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function transferItem(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave        = $params[0]['value'];
			$mesaDestino  = $params[1]['value'];
			$NRCOMANDA    = $params[2]['value'];
			$NRVENDAREST  = $params[3]['value'];
			$produto      = $params[4]['value'];
			$posicao      = str_pad($params[5]['value'], 2, '0', STR_PAD_LEFT);
            $CDSUPERVISOR = $params[6]['value'];
            $maxPosicoes = $params[7]['value'];

			$produtos = json_decode($produto);
			$arrayProdutos = array();

			foreach ($produtos as $prod) {
				array_push($arrayProdutos, (array)$prod);
			};

			$dataset = array(
				'chave'        => $chave,
				'mesaDestino'  => $mesaDestino,
				'NRCOMANDA'    => $NRCOMANDA,
				'NRVENDAREST'  => $NRVENDAREST,
				'produtos'     => $arrayProdutos,
				'posicao'      => $posicao,
				'CDSUPERVISOR' => $CDSUPERVISOR,
				'maxPosicoes' => $maxPosicoes
			);
			$answer = $this->tableService->transfereProduto($dataset);

			if ($answer['funcao'] == '1'){
                $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('406'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
			}
            else {
				throw new \Exception($this->waiterMessage->getMessage($answer['error']), 1);
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function setPositions(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]['value'];
			$NRCOMANDA   = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];
			$quantidade  = $params[3]['value'];

			$dataset = array(
				'chave'       => $chave,
				'NRCOMANDA'   => $NRCOMANDA,
				'NRVENDAREST' => $NRVENDAREST,
				'quantidade'  => $quantidade
			);

			$answer = $this->tableService->alteraQtdPessoas($dataset);

			if ($answer['funcao'] == '1') {
				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('411'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}

		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getDelayedProducts(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]['value'];
			$NRVENDAREST = $params[1]['value'];
			$NRCOMANDA   = $params[2]['value'];

			$result = $this->paramsService->getDelayedProducts($chave, $NRVENDAREST, $NRCOMANDA);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DelayedProductsRepository', $result));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function releaseTheProduct(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);

			$chave       = $params[0]['value'];
			$CDFILIAL    = $params[1]['value'];
			$NRVENDAREST = $params[2]['value'];
			$NRCOMANDA   = $params[3]['value'];
			$products    = $params[4]['value'];
			$printerCode = $params[5]['value'];

			$operationParams = array(
				'CDFILIAL'      => $CDFILIAL,
				'NRVENDAREST'   => $NRVENDAREST,
				'NRCOMANDA'     => $NRCOMANDA,
				'PRODUCTS'      => $products
			);

			$this->KDS->insertKDSOPERACAOTEMP($operationParams, 'releaseItem', $session['NRORG']);

			$releaseList = json_decode($products, true);

			// Print the released products.
			$this->impressaoService->printRelease($chave, $NRCOMANDA, $NRVENDAREST, $releaseList, $printerCode);
			// Gets an updated list of products to send back.
			$isReleased = false;
			$retries = 0;
			$liberaProduto = array_column($releaseList, 'NRPRODCOMVEN');
			//tenta 3 vezes pegar o datasource sem os itens pedidos para serem liberados
			while (!$isReleased && $retries < 3) {
				sleep(3);
				$result = $this->paramsService->getDelayedProducts($chave, $NRVENDAREST, $NRCOMANDA);
				$delayedProducts = array_column($result, 'NRPRODCOMVEN');
				$arrIntersectProducts = array_intersect($delayedProducts, $liberaProduto);
				if (count($arrIntersectProducts) == 0){
					$isReleased = true;
				}
				$retries++;
			}
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('DelayedProductsRepository', $result));
		} catch (\Exception $e){
			$response->addMessage(new Message('Ocorreu um erro na hora de liberar o produto: '.$e->getMessage()));
		}
	}

	public function splitProducts(Request\Filter $request, Response $response){
		try {

			$params = $request->getFilterCriteria()->getConditions();

			$chave          = $params[0]["value"];
			$NRVENDAREST    = $params[1]["value"];
			$NRCOMANDA      = $params[2]["value"];
			$NRPRODCOMVENS  = $params[3]["value"];
			$NRLUGARMESA    = $params[4]["value"];

			if (is_array($NRLUGARMESA)){
				$NRLUGARMESA = array_map(
					function($position){
						return str_pad((string)$position, 2, '0', STR_PAD_LEFT);
					}, $NRLUGARMESA);
			}
            else {
				if ($NRLUGARMESA != ''){
					$NRLUGARMESA = str_pad((string)$params[4]['value'], 2, '0', STR_PAD_LEFT);
                }
				else {
					$NRLUGARMESA = $params[4]['value'];
                }
			}

			$dataset = array(
				"chave"         => $chave,
				"NRVENDAREST"   => $NRVENDAREST,
				"NRCOMANDA"     => $NRCOMANDA,
				"NRPRODCOMVENS" => $NRPRODCOMVENS,
				"NRLUGARMESA"   => $NRLUGARMESA
			);
			$answer = $this->accountService->divideProdutos($dataset);

			if ($answer['funcao'] == '1'){
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
				$response->addMessage(new Message($this->waiterMessage->getMessage('459')));
			}
            else {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function cancelSplitedProducts(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();

		$chave       = $params[0]["value"];
		$NRVENDAREST = $params[1]["value"];
		$NRCOMANDA   = $params[2]["value"];
		$selection   = $params[3]["value"];

		$dataset = array (
			"chave"       => $chave,
			"NRVENDAREST" => $NRVENDAREST,
			"NRCOMANDA"   => $NRCOMANDA,
			"selection"   => $selection
		);
		$answer = $this->orderService->cancelaProdutosDivididos($dataset);

		if ($answer['funcao'] == '1') {
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			$response->addMessage(new Message($this->waiterMessage->getMessage('460')));
		} else {
			$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		}
	}

	public function generatePositionCode(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]["value"];
			$NRVENDAREST = $params[1]["value"];
			$NRCOMANDA   = $params[2]["value"];
			$position    = $params[3]["value"];

			$session = $this->util->getSessionVars($chave);
			$CDFILIAL = $session['CDFILIAL'];

			$code = $this->positionCodeService->getCode($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $position);

			if (empty($code)){
				do {
					$code = chr(65 + rand(0,25)).chr(65 + rand(0,25)).rand(0,9).rand(0,9);
				}
				while ($this->positionCodeService->codeExists($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $position, $code));

				$this->positionCodeService->insertCode($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $position, $code);
			}

			$response->addMessage(new Message('O código desta posição é: '.$code));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('PositionCodeRepository', array('CODE' => $code)));

		} catch(\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message('O sistema não está configurado corretamente para utilizar esta funcionalidade. Favor preparar o banco antes de utilizar o check-in de posições.'));
		}
	}

	public function changeTableStatus(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]["value"];
			$NRVENDAREST = $params[1]["value"];
			$NRCOMANDA   = $params[2]["value"];
			$status      = $params[3]["value"];

			$session = $this->util->getSessionVars($chave);
			$mesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST);
			$NRMESA = str_pad($mesa['NRMESA'], 4, '0', STR_PAD_LEFT);

            if ($status === 'R'){
                if ($mesa['IDSTMESAAUX'] === 'R'){
                    throw new \Exception('A mesa já está sendo recebida por outro garçom.');
                }
                else if ($mesa['IDSTMESAAUX'] !== 'S'){
                    throw new \Exception('Não foi possível receber a mesa. Verifique se a mesma foi reaberta, ou se ela já foi recebida.');
                }
                else {
                    $this->tableService->resetPositionControl($session['CDFILIAL'], $NRVENDAREST, 'T');
                }
            }

            if ($status === 'S'){
                $this->tableService->resetPositionControl($session['CDFILIAL'], $NRVENDAREST, $session['CDOPERADOR']);
            }

            $positionControl = $this->tableService->getPositionControlDetails($session['CDFILIAL'], $NRVENDAREST, $session['CDOPERADOR']);
            if (empty($positionControl) || $status !== 'S'){ // If there are locked positions, we can't change the status to S.
                $params = array(
                    'IDSTMESAAUX' => $status,
                    'CDFILIAL'    => $session['CDFILIAL'],
                    'CDLOJA'      => $session['CDLOJA'],
                    'NRMESA'      => $NRMESA
                );
                $this->entityManager->getConnection()->executeQuery("SQL_ALTERA_STATUS_MESA", $params);
            }

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		} catch(\Exception $e) {
			Exception::logException($e);
            $response->addMessage(new Message('Não foi possível atualizar o status da mesa: '.$e->getMessage()));
		}
	}

	public function checkAccess(Request\Filter $request, Response $response) {
		try {

			$params       = $request->getFilterCriteria()->getConditions();
			$chave        = $params[0]['value'];
			$NRCOMANDA    = $params[1]['value'];
			$NRVENDAREST  = $params[2]['value'];

			$oldResult = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST);

			$status = $oldResult['IDSTMESAAUX'];

			if ($status == 'O') {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OK', array(array('OK' => true))));
			} else if($status == 'S'){
				$response->addMessage(new Message($this->waiterMessage->getMessage('439')));
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OK', array(array('OK' => false))));
			} else{
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('OK', array(array('OK' => false))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

    public function positionControl(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars(null);
            $message = null;

            if ($params['position'] == null){
                // Only runs when you first enter accountPaymentNamed.json.
                $message = $this->tableService->lockAllPositions($session['CDFILIAL'], $params['NRVENDAREST'], $session['CDOPERADOR']);
            }
            else {
                if ($params['unselecting']){ // Runs when you unselect a position.
                    $message = $this->tableService->deletePositionControl($session['CDFILIAL'], $params['NRVENDAREST'], $session['CDOPERADOR'], $params['position']);

                    // If there are no more selected positions, we have to lock all positions again.
                    if (empty($params['positions'])){
                        $message = $this->tableService->lockAllPositions($session['CDFILIAL'], $params['NRVENDAREST'], $session['CDOPERADOR']);
                    }
                }
                else { // Runs when you select a position.
                    // Checks if the position being locked is already locked. This should ONLY occur the first time the receiving screen is entered, and no positions are selected.
                    // In this case, resets the control before inserting the selected position.
                    $lockedPositions = $this->tableService->getLockedPositions($session['CDFILIAL'], $params['NRVENDAREST'], $session['CDOPERADOR']);
                    if (array_search(str_pad($params['position'], 2, '0', STR_PAD_LEFT), $lockedPositions) !== false){
                        $this->tableService->resetPositionControl($session['CDFILIAL'], $params['NRVENDAREST'], $session['CDOPERADOR']);
                    }
                    $message = $this->tableService->insertPositionControl($session['CDFILIAL'], $params['NRVENDAREST'], $session['CDOPERADOR'], $params['position']);
                }
            }

            if ($message) $response->addMessage(new Message($message, 'alert'));
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('PositionControlRepository', array(array('message' => $message))));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage(), 'error'));
        }
    }

}