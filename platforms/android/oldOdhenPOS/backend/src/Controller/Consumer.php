<?php

namespace Controller;

use Zeedhi\DTO\Response\Notification;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class Consumer extends \Zeedhi\Framework\Controller\Simple {

    protected $entityManager;
    protected $util;
    protected $consumidorAPI;
    protected $caixaAPI;
    protected $vendaValAPI;
    protected $impressaoService;
    protected $consumerService;

    public function __construct(
        \Doctrine\ORM\EntityManager $entityManager,
        \Util\Util $util,
        \Odhen\API\Service\Consumidor $consumidorAPI,
        \Odhen\API\Service\Caixa $caixaAPI,
        \Odhen\API\Service\VendaValidacao $vendaValAPI,
        \Service\Impressao $impressaoService,
        \Service\Consumer $consumerService
    ){
        $this->entityManager = $entityManager;
        $this->util = $util;
        $this->consumidorAPI = $consumidorAPI;
        $this->caixaAPI = $caixaAPI;
        $this->vendaValAPI = $vendaValAPI;
        $this->impressaoService = $impressaoService;
        $this->consumerService = $consumerService;
    }

    public function chargePersonalCredit(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars($params['chave']);

            $date = new \DateTime('NOW');
            $NRDEPOSICONS = $date->format('ymdHi');

            $this->entityManager->getConnection()->beginTransaction();

            $NRSEQMOVEXT = 1;

            foreach ($params['TIPORECE'] as $recebimento){
                $result = $this->caixaAPI->insereMovimentacao($recebimento, $params['DATASALE']['TROCO'], $session['CDFILIAL'], $session['CDCAIXA'], $session['CDOPERADOR'], $session['NRORG']);
                $creditDetails = $this->consumidorAPI->creditaSaldo($params['CDCLIENTE'], $params['CDCONSUMIDOR'], $params['CDFAMILISALD'], $NRSEQMOVEXT++, $recebimento['CDTIPORECE'], $session['CDFILIAL'], $session['CDCAIXA'], $recebimento['VRMOVIVEND'], $NRDEPOSICONS, $result['NRSEQUMOVI'], $result['DTABERCAIX'], 'WTR - Credito Caixa ' . $session['CDCAIXA']);
            }

            $this->entityManager->getConnection()->commit();

            $creditDetails['VRSALDCONFAM'] = $params['VRRECARGA'];
            $printerResult = $this->impressaoService->printPersonalCreditVoucher($session['CDFILIAL'], $session['CDCAIXA'], $NRDEPOSICONS, $creditDetails, $params['TIPORECE'], $params['DATASALE']['TROCO']);

            $message = 'Recarga efetuada com sucesso.';
            if ($printerResult['error']){
                $message .= '<br><br>Não foi possível imprimir o comprovante.<br>' . $printerResult['message'];
                $creditDetails['dadosImpressao'] = null;
            }
            else {
                $creditDetails['dadosImpressao'] = $printerResult['message'];
            }

            $response->addMessage(new Message($message));
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ChargePersonalCredit', $creditDetails));
        } catch (\Exception $e){
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->getConnection()->rollBack();
            }
            Exception::logException($e);
            $response->addMessage(new Message('Erro ao creditar saldo pessoal: ' . $e->getMessage(), 'error'));
        }
    }

    public function cancelPersonalCredit(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars($params['chave']);

            $depositos = $this->caixaAPI->buscaNumeroDeposito($params['CDCLIENTE'], $params['CDCONSUMIDOR'], $params['NRDEPOSICONS'], $params['NRSEQMOVCAIXA']);
            if (empty($depositos)){
                $response->addMessage(new Message("Operação bloqueada. Número do depósito não encontrado para o consumidor informado."));
            }
            else if (sizeof($depositos) > 1 && $params['NRSEQMOVCAIXA'] === -1){
                // Mais de um depósito encontrado.
                $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CancelCreditRepository', $depositos));
                $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array('NRSEQMOVCAIXA' => $params['NRSEQMOVCAIXA'])));
            }
            else if ($params['NRSEQMOVCAIXA'] === null || $params['NRSEQMOVCAIXA'] == ""){
                // Depósito que tinha que ser especificado não veio especificado.
                $response->addMessage(new Message("Especifique o depósito a ser cancelado."));
            }
            else {
                $deposito = $depositos[0];
                // Verifica se o depósito já foi cancelado.
                $NRDEPOSICONS = $this->vendaValAPI->verificaCancelamento($deposito['CDCLIENTE'], $deposito['CDCONSUMIDOR'], $deposito['CDFAMILISALD'], $deposito['NRDEPOSICONS'], $deposito['NRSEQMOVCAIXA']);
                if (!empty($NRDEPOSICONS)) throw new \Exception("Operação bloqueada. O número do depósito informado já foi cancelado. Favor verificar.");
                // Verifica se a recarga foi feita com cartão de crédito/débito.
                if (!empty($deposito['CDNSUHOSTTEF'])) throw new \Exception("Cancelamentos de carga de cartão realizadas com cartão de crédito/débito, devem ser efetuados através das funções Tef.");
                // Verifica se a família possui saldo para ser cancelado.
                $saldoCanc = $this->consumidorAPI->verificaSaldoCanc($deposito['CDCLIENTE'], $deposito['CDCONSUMIDOR'], $deposito['CDFILIAL'], $deposito['CDFAMILISALD']);
                if ($saldoCanc < floatval($deposito['VRMOVEXTCONS']) && $params['confirmacao'] === null){
                    if ($deposito['IDSALDNEGFAM'] == 'S'){
                        $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array()));
                    }
                    else {
                        throw new \Exception("O consumidor informado não possui saldo suficiente para efetuar este cancelamento. Sendo assim o cancelamento não poderá ser efetuado pois a familia de saldo não permite que o consumidor fique com o saldo negativo.");
                    }
                }
                else {
                    // Verifica se o cancelamento está sendo realizado no mesmo caixa.
                    if ($deposito['CDCAIXA'] != $session['CDCAIXA']){
                        throw new \Exception("Cancelamento de crédito não permitido. Apenas será possível efetuar o seu cancelamento no caixa em que este foi concedido (Caixa ".$deposito['CDCAIXA'].").");
                    }
                    // Verifica se o depósito foi realizado dentro da abertura atual do caixa.
                    $estadoCaixa = $this->caixaAPI->getEstadoCaixa($session['CDFILIAL'], $session['CDCAIXA'], $session['NRORG']);
                    if ($deposito['DTABERCAIX'] != $estadoCaixa['DTABERCAIX']){
                        throw new \Exception("Cancelamento de crédito não permitido. Este crédito foi efetuado em outra abertura.");
                    }

                    $this->entityManager->getConnection()->beginTransaction();

                    $cancelDetails = $this->consumidorAPI->cancelaSaldo($deposito['CDCLIENTE'], $deposito['CDCONSUMIDOR'], $deposito['CDFAMILISALD'], $deposito['CDTIPORECE'], $deposito['CDFILIAL'], $deposito['CDCAIXA'], $deposito['VRMOVEXTCONS'], $deposito['NRDEPOSICONS'], $deposito['NRSEQMOVCAIXA'], $deposito['DTABERCAIX'], $session['NRORG'], 'WTR - Canc. Cred. Caixa ' . $deposito['CDCAIXA']);
                    $this->caixaAPI->cancelaMovimentacao($deposito['CDFILIAL'], $deposito['CDCAIXA'], $deposito['DTABERCAIX'], $deposito['NRSEQMOVCAIXA']);

                    $this->entityManager->getConnection()->commit();

                    $printerResult = $this->impressaoService->printCancelCreditVoucher($deposito['CDFILIAL'], $deposito['CDCAIXA'], $deposito['NRDEPOSICONS'], $cancelDetails, $deposito['VRMOVEXTCONS'], $deposito['NMTIPORECE']);

                    $message = 'Cancelamento efetuado com sucesso.';
                    if ($printerResult['error']){
                        $message .= '<br><br>Não possível imprimir o comprovante.<br>' . $printerResult['message'];
                        $cancelDetails['dadosImpressao'] = null;
                    }
                    else {
                        $cancelDetails['dadosImpressao'] = $printerResult['message'];
                    }

                    $response->addMessage(new Message($message));
                    $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CancelCreditRepository', $cancelDetails));
                }
            }
        } catch (\Exception $e){
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->getConnection()->rollBack();
            }
            Exception::logException($e);
            $response->addMessage(new Message('Erro ao cancelar o crédito: ' . $e->getMessage(), 'error'));
        }
    }

    public function transferPersonalCredit(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars($params['chave']);

            $estadoCaixa = $this->caixaAPI->getEstadoCaixa($session['CDFILIAL'], $session['CDCAIXA'], $session['NRORG']);
            $DTABERCAIX = $this->caixaAPI->convertToDateDB($estadoCaixa['DTABERCAIX']);

            $NRSEQMOVEXT = 1;

            $this->entityManager->getConnection()->beginTransaction();

            for ($i = 0; $i < sizeof($params['CDCLIENTE']); $i++){

                $saldoCartao = $this->consumidorAPI->buscaSaldoExtrato($params['CDCLIENTE'][$i], $params['CDCONSUMIDOR'][$i], $params['CDFAMILISALD'][$i], $session['CDFILIAL']);

                if (empty($saldoCartao)) $saldoCartao = 0;
                else $saldoCartao = floatval($saldoCartao['VRSALDCONEXT']);

                if ($saldoCartao <= 0){
                    throw new \Exception("O saldo do cartão " . $params['CDIDCONSUMID'][$i] . " encontra-se negativo ou zerado.");
                }

                $this->consumidorAPI->creditaSaldo(
                    $params['destCDCLIENTE'],
                    $params['destCDCONSUMIDOR'],
                    empty($params['destCDFAMILISALD']) ? $params['CDFAMILISALD'][$i] : $params['destCDFAMILISALD'],
                    str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT), // NRSEQMOVEXT
                    '001', // CDTIPORECE
                    $session['CDFILIAL'],
                    $session['CDCAIXA'],
                    $saldoCartao,
                    null, // NRDEPOSICONS,
                    null, // NRSEQUMOVI,
                    $DTABERCAIX,
                    substr('WTR - Transf. Saldo: ' . $params['CDIDCONSUMID'][$i] . ' - ' . $params['CDCLIENTE'][$i], 0, 50)
                );

                $this->consumidorAPI->debitaSaldo(
                    $params['CDCLIENTE'][$i],
                    $params['CDCONSUMIDOR'][$i],
                    $params['CDFAMILISALD'][$i],
                    '001', // NRSEQMOVEXT
                    '001', // CDTIPORECE
                    $session['CDFILIAL'],
                    $session['CDCAIXA'],
                    $saldoCartao,
                    null, // NRDEPOSICONS,
                    null, // NRSEQUMOVI,
                    $DTABERCAIX,
                    substr('WTR - Transf. Saldo: ' . $params['destCDIDCONSUMID'] . ' - ' . $params['destCDCLIENTE'], 0, 50)
                );

                $NRSEQMOVEXT++;
            }

            $this->entityManager->getConnection()->commit();

            $response->addMessage(new Message("Transferência realizada com sucesso."));
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TransferCreditRepository', array()));
        } catch (\Exception $e){
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->getConnection()->rollBack();
            }
            Exception::logException($e);
            $response->addMessage(new Message('Erro ao transferir o crédito: ' . $e->getMessage(), 'error'));
        }
    }

    public function getPersonalCredit(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars($params['chave']);

            $saldo = $this->consumidorAPI->consultaSaldo($session['CDFILIAL'], $params['CDCLIENTE'], $params['CDCONSUMIDOR'], $session['NRORG']);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConsumerBalanceRepository', $saldo));
        } catch (\Exception $e){
            if ($this->entityManager->getConnection()->isTransactionActive()) {
                $this->entityManager->getConnection()->rollBack();
            }
            Exception::logException($e);
            $response->addMessage(new Message('Erro ao consultar o crédito: ' . $e->getMessage(), 'error'));
        }
    }

    public function getFidelityCredit(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $params = $this->util->getParams($params);
            $session = $this->util->getSessionVars(null);

            $saldo = $this->consumidorAPI->buscaCreditoFidelidade($session['CDFILIAL'], $params['CDCLIENTE'], $params['CDCONSUMIDOR'], $session['NRORG']);
            $dadosTaxa = $this->caixaAPI->buscaDadosTaxa($session['CDFILIAL'], $session['CDLOJA']);

            $resposta = array(
                'VRSALDCONEXT' => $saldo['VRSALDCONEXT'],
                'IDPERALTDESCFID' => $saldo['IDPERALTDESCFID'],
                'IDCOMISVENDA' => $dadosTaxa['IDCOMISVENDA'],
                'VRCOMISVENDA' => floatval($dadosTaxa['VRCOMISVENDA'])
            );

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConsumerBalanceRepository', $resposta));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message('Erro ao consultar a fidelidade: ' . $e->getMessage(), 'error'));
        }
    }

    public function getCountries(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();

            if (!empty($params[0])) $NMPAIS = '%' . $params[0]['value'] . '%';
            else $NMPAIS = "%%";

            $page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

            $countries = $this->consumerService->getCountries($NMPAIS, $FIRST, $LAST);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CountryRepository', $countries));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function getStates(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();

            $CDPAIS = !empty($params[0]['value']) ? $params[0]['value'] : null;

            if (!empty($params[1])) $NMESTADO = '%' . $params[1]['value'] . '%';
            else $NMESTADO = "%%";

            $page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

            $states = $this->consumerService->getStates($CDPAIS, $NMESTADO, $FIRST, $LAST);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('StateRepository', $states));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function getCities(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();

            $CDPAIS = !empty($params[0]['value']) ? $params[0]['value'] : null;
            $SGESTADO = !empty($params[1]['value']) ? $params[1]['value'] : null;

            if (!empty($params[2])) $NMMUNICIPIO = '%' . $params[2]['value'] . '%';
            else $NMMUNICIPIO = "%%";

            $page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

            $cities = $this->consumerService->getCities($CDPAIS, $SGESTADO, $NMMUNICIPIO, $FIRST, $LAST);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CityRepository', $cities));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function getNeighborhoods(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();

            $CDPAIS = !empty($params[0]['value']) ? $params[0]['value'] : null;
            $SGESTADO = !empty($params[1]['value']) ? $params[1]['value'] : null;
            $CDMUNICIPIO = !empty($params[2]['value']) ? $params[2]['value'] : null;

            if (!empty($params[3])) $NMBAIRRO = '%' . $params[3]['value'] . '%';
            else $NMBAIRRO = "%%";

            $page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

            $neighborhoods = $this->consumerService->getNeighborhoods($CDPAIS, $SGESTADO, $CDMUNICIPIO, $NMBAIRRO, $FIRST, $LAST);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('NeighborhoodRepository', $neighborhoods));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function getSaleTypes(Request\Filter $request, Response $response){
        try {
            $saleTypes = $this->consumerService->getSaleTypes();
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SaleTypesRepository', $saleTypes));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function getConsumerTypes(Request\Filter $request, Response $response){
        try {
            $consumerTypes = $this->consumerService->getConsumerTypes();
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConsumerTypeRepository', $consumerTypes));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function addConsumer(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();

            $data = json_decode($params[0]['value'], true);

            $this->consumerService->addConsumer($data);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AddconsumerRepository', array()));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

}