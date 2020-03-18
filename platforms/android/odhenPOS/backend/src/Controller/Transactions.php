<?php

namespace Controller;

use Zeedhi\DTO\Response\Notification;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class Transactions extends \Zeedhi\Framework\Controller\Simple {

	protected $entityManager;
	protected $util;
	protected $transactionsService;
	protected $SMTPService;
	protected $date;
	
	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Util\Util $util,
		\Service\Transactions $transactionsService,
		\Service\SMTP $SMTPService,
		\Util\Date $date) {

		$this->entityManager = $entityManager;
		$this->util = $util;
		$this->transactionsService = $transactionsService;
		$this->SMTPService = $SMTPService;
		$this->date = $date;
	}
	
	public function buscaTrasacoesMesa(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();
		$NRVENDAREST = $params[0]['value'];
		$NRLUGARMESA = $params[1]['value'];
		if ($NRLUGARMESA == "") {
			$answer = $this->transactionsService->buscaTransacoesMesa($NRVENDAREST);
		} else {
			$answer = $this->transactionsService->buscaTransacoesPosicao($NRVENDAREST, $NRLUGARMESA);
		}
		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetTrasanctions', $answer));
	}
	
	public function buscaLinhaCancelamento(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();
		$chave = $params[0]['value'];
		$NRSEQMOVMOB = $params[1]['value'];
		$answer = $this->transactionsService->buscaLinhaCancelamento($chave, $NRSEQMOVMOB);
		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('FindRowToCancel', $answer));
	}

	public function buscaPagamentoMesa(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();
		$NRMESA = $params[0]['value'];
		$NRLUGARMESA = $params[1]['value'];
		$NRVENDAREST = $params[2]['value'];
		$chave = $params[3]['value'];
		$answer = $this->transactionsService->buscaPagamentoMesa($chave, $NRMESA, $NRLUGARMESA, $NRVENDAREST);
		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetTableTrasanctions', $answer));
	}
	
	public function buscaTransacoes(Request\Filter $request, Response $response){
		$params = $request->getFilterCriteria()->getConditions();
		if ($params[3]['value'] != null) {
		
			if ($params[0]['value'] || $params[2]['value']) {
				$DTHRFIMMOVini = $params[0]['value'];
				$DTHRFIMMOVfim = $params[1]['value'];
				$NRADMCODE = $params[2]['value'];
			} else {
				$DTHRFIMMOVini = $this->date->getDataAtual()->format(\Util\Date::FORMATO_BRASILEIRO)." 00:00:00";
				$DTHRFIMMOVfim = $this->date->getDataAtual()->format(\Util\Date::FORMATO_BRASILEIRO)." 23:59:59";
				$NRADMCODE = '';
			}
			$page = $request->getFilterCriteria()->getPage();
			$pageSize = $request->getFilterCriteria()->getPageSize();
			$FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
			$LAST = ($FIRST + $pageSize);
			if ($page > 1) {
				$FIRST++;
			}

			$chave = $params[3]['value'];
			
			$answer = $this->transactionsService->buscaTransacoes($DTHRFIMMOVini, $DTHRFIMMOVfim, $NRADMCODE, $FIRST, $LAST, $chave);
			foreach ($answer as &$transaction) {
				switch ($transaction['IDTIPORECE']) {
					case '1':
						$transaction['LABELTIPMOV'] = "Crédito";
						break;
					case '2':
						$transaction['LABELTIPMOV'] = "Débito";
						break;
					case '3':
						$transaction['LABELTIPMOV'] = "Cheque";
						break;
					case '4':
						$transaction['LABELTIPMOV'] = "Dinheiro";
						break;
					case '5':
						$transaction['LABELTIPMOV'] = "Ticket";
						break;
					case '6':
						$transaction['LABELTIPMOV'] = "Outras moedas";
						break;
					case '7':
						$transaction['LABELTIPMOV'] = "Contra-vale";
						break;
					case '8':
						$transaction['LABELTIPMOV'] = "Convênio";
						break;
					case '9':
						$transaction['LABELTIPMOV'] = "Crédito pessoal";
						break;
					case 'A':
						$transaction['LABELTIPMOV'] = "Débito consumidor";
						break;
					case 'B':
						$transaction['LABELTIPMOV'] = "Devolução";
						break;
					default:
						break;
				}
			}
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TransactionsRepository', $answer));
		}
	}
	
	public function sendTransactionEmail(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$NRSEQMOVMOB = $params[0]['value'];
			$DSEMAILCLI = $params[1]['value'];
			$NMCONSUMIDOR = '';
			$sender = 'Waiter';
			
			$JSONTEF = $this->transactionsService->buscaJsonComprovante($NRSEQMOVMOB);
			
			$json = (array)json_decode($JSONTEF[0]['TXMOVJSON']);
			
			$subject = 'Comprovante Waiter - '. $json['tef_request_details']->administrative_code;
			
			$customer_receipt = ($json['tef_request_details']->customer_receipt);
			$customer_receipt = str_replace("\n","</br>", $customer_receipt);
			$customer_receipt = str_replace("'","", $customer_receipt);
			
			//transforma em minusculo todos os caracters do e-mail digitados
			$DSEMAILCLI = strtolower($DSEMAILCLI);
			
			$this->SMTPService->sendEmail($NMCONSUMIDOR, $DSEMAILCLI, $sender, $subject, $customer_receipt);
			
			$merchant_receipt = ($json['tef_request_details']->merchant_receipt);
			$merchant_receipt = str_replace("\n","</br>", $merchant_receipt);
			$merchant_receipt = str_replace("'","", $merchant_receipt);

			$SMTP = $this->SMTPService->getSMTP();
			$this->SMTPService->sendEmail($NMCONSUMIDOR, $SMTP[0]['DSEMAILAUVND'], $sender, $subject, $merchant_receipt);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function updateTransactionEmail(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$NRSEQMOVMOB = $params[0]['value'];
			$DSEMAILCLI = $params[1]['value'];
			//transforma em minusculo todos os caracters do e-mail digitados
			$DSEMAILCLI = strtolower($DSEMAILCLI);
			$this->transactionsService->atualizaEmailCliente($DSEMAILCLI, $NRSEQMOVMOB);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function updateCanceledTransaction(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$NRSEQMOVMOB = $params[0]['value'];
			$this->transactionsService->atualizaTransacaoCancelada($NRSEQMOVMOB);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function moveTransactions(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$chave       = $params[0]['value'];
			$NRVENDAREST = $params[1]['value'];
			$NRCOMANDA   = $params[2]['value'];
			$NRLUGARMESA = $params[3]['value'];
			$positions   = $params[4]['value'];
			$positions = array_map(
				function($position) {
					return str_pad((string)$position, 2, '0', STR_PAD_LEFT);
				},$params[4]['value']
			);
			$dataset = array(
				"chave"       => $chave,
				"NRVENDAREST" => $NRVENDAREST,
				"NRCOMANDA"   => $NRCOMANDA,
				"NRLUGARMESA" => $NRLUGARMESA,
				"positions"   => $positions
			);
			$answer = $this->transactionsService->moveTransactions($dataset);
			if ($answer['funcao'] == '1') {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
			} else {
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('error' => $this->waiterMessage->getMessage($answer['error'])))));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}
}