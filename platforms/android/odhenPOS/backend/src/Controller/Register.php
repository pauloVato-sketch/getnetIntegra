<?php
namespace Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Zeedhi\DTO\Response\Notification;

class Register extends \Zeedhi\Framework\Controller\Simple {

	protected $caixaAPI;
	protected $databaseAPI;
	protected $util;
	protected $registerService;

	public function __construct(\Odhen\API\Service\Caixa $caixaAPI, \Odhen\API\Util\Database $databaseAPI, \Util\Util $util, \Service\Register $registerService){
		$this->caixaAPI = $caixaAPI;
		$this->databaseAPI = $databaseAPI;
		$this->util = $util;
		$this->registerService = $registerService;
	}

	public function openRegister(Request\Filter $request, Response $response) {
		$params        = $request->getFilterCriteria()->getConditions();
		$chave         = $params[0]['value'];
		$VRMOVIVEND    = $params[1]['value'];
		$session       = $this->util->getSessionVars($chave);
		$DTABERCAIX    = new \DateTime();
		$IDMONGO       = "";
		$IDATUTURCAIXA = 'I';

		$result = $this->caixaAPI->abreCaixa(
			$session['CDFILIAL'],
			$DTABERCAIX,
			$session['CDCAIXA'],
			$session['NRORG'],
			$session['CDOPERADOR'],
			$DTABERCAIX, // DTMOVTURCAIX é a mesma que DTABERCAIX na abertura
			$VRMOVIVEND,
			$IDMONGO,
			$IDATUTURCAIXA,
			true,
			$session['IDHABCAIXAVENDA'],
			$session['IDSINCCAIXADLV']
		);

		if (!$result['error']) {
			if (!empty($result['mensagemImpressao'])) {
				$response->addMessage(new Message($result['mensagemImpressao']));
			}
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('RegisterOpen', array('dadosImpressao' => $result['dadosImpressao'])));
		} else {
			$response->addMessage(new Message($result['message']));
		}
	}

	public function closeRegister(Request\Filter $request, Response $response) {
		$params     = $request->getFilterCriteria()->getConditions();
		$chave      = $params[0]['value'];
		$session    = $this->util->getSessionVars($chave);

		$estadoCaixa = $this->caixaAPI->getEstadoCaixa($session['CDFILIAL'], $session['CDCAIXA'], $session['NRORG']);

        if($estadoCaixa['estado'] === 'aberto') {
			$DTFECHCAIX = new \DateTime(); //Data de fechamento
			$TIPORECE   = $params[1]['value'];

			$result = $this->caixaAPI->fechaCaixa(
				'S', //chama funcao de sangria automatica na API
				null, //Data de abertura
				$DTFECHCAIX,
				$session['CDFILIAL'],
				$session['CDCAIXA'],
				$session['NRORG'],
				$session['CDOPERADOR'],
				$session['NRCONFTELA'],
				$TIPORECE,
				true,
				$session['IDHABCAIXAVENDA'], 
				$session['IDSINCCAIXADLV']
			);
 		} else {
        	$result = array('error' => true, 'message' => 'Operação bloqueada. O caixa já se encontra fechado.');	
        }
        	
		if (!$result['error']) {
			if (!empty($result['mensagemImpressao'])) {
				$response->addMessage(new Message($result['mensagemImpressao']));
			}
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('RegisterClose', array('dadosImpressao' => $result['dadosImpressao'])));
		} else {
			$response->addMessage(new Message($result['message'], 'error'));
		}
	}

	public function getClosingPayments(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();
		$chave = $params[0]['value'];
		$session = $this->util->getSessionVars($chave);
		$openingDate = $this->registerService->getRegisterOpeningDate($session['CDFILIAL'], $session['CDCAIXA']);
		$payments = $this->registerService->getRegisterClosingPayments($session['CDFILIAL'], $session['CDCAIXA'], $openingDate['DTABERCAIX']);
		foreach ($payments as &$currentPayment) {
			if ($currentPayment['LABELVRMOVIVEND'] != null) {
				$currentPayment['LABELVRMOVIVEND'] = $this->util->formataPreco($currentPayment['LABELVRMOVIVEND']);
			}
		}
		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('RegisterClosingPayments', $payments));
	}
}