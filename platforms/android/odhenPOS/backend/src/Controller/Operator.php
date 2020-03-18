<?php
namespace Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use Zeedhi\Framework\DTO\Response\Notification;
use Zeedhi\Framework\DataSource\DataSet;
use \Util\Exception;

class Operator extends \Zeedhi\Framework\Controller\Simple {

	protected $waiterMessage;
	protected $params;
	protected $operatorService;
	protected $util;
	protected $credentials;
	protected $operator;
	protected $session;

	public function __construct(
		\Util\WaiterMessage $waiterMessage,
		\Controller\Params $params,
		\Service\Operator $operatorService,
		\Util\Util $util,
		$session
	){
		$this->waiterMessage = $waiterMessage;
		$this->params = $params;
		$this->operatorService = $operatorService;
		$this->util = $util;
		$this->session = $session;
	}

	public function checkVersion($frontVersion){
		$json = file_get_contents(__DIR__.'\..\..\version.json');
		$configData = json_decode($json, true);
		$backVersion = $configData['backVersion'];

		if ($backVersion === $frontVersion) $versionOk = true;
		else $versionOk = false;

		return array(
			'versionOk' => $versionOk,
			'frontVersion' => $frontVersion,
			'backVersion' => $backVersion
		);
	}

 	const MAX_PAGES = 2000;
	public function getFiliaisLogin(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$page = $request->getFilterCriteria()->getPage();
			$pageSize = $request->getFilterCriteria()->getPageSize();
			$page = isset($page)? $page: 1;
			$pageSize = isset($pageSize)? $pageSize: self::MAX_PAGES;

			if(isset($params['filial'])){
				$filial = $params['filial'];
			}else{
				$filial = '%%';
			}

			if($filial == '%%' && isset($params['CDFILIAL|NMFILIAL'])){
				$filial = $params['CDFILIAL|NMFILIAL'];
			}else{
				$filial = '%%';
			}
			$FiliaisLogin = $this->operatorService->getFiliaisLogin($filial, $page, $pageSize);
			$response->addDataSet(new Dataset('FiliaisLogin', $FiliaisLogin));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getCaixasLogin(Request\Filter $request, Response $response){
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$params       = $this->util->getParams($params);
			$CDFILIAL     = $params['CDFILIAL'];
			$page = $request->getFilterCriteria()->getPage();
			$pageSize = $request->getFilterCriteria()->getPageSize();
			$page = isset($page)? $page: 1;
			$pageSize = isset($pageSize)? $pageSize: self::MAX_PAGES;

			if(isset($params['caixa'])){
				$caixa = $params['caixa'];
			}else{
				$caixa = '%%';
			}
			if($caixa == '%%' && isset($params['CDCAIXA|NMCAIXA'])){
				$caixa = $params['CDCAIXA|NMCAIXA'];
			}else{
				$caixa = '%%';
			}
			$CaixasLogin = $this->operatorService->getCaixasLogin($CDFILIAL, $caixa, $page, $pageSize);
			$response->addDataSet(new Dataset('CaixasLogin', $CaixasLogin));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getVendedoresLogin(Request\Filter $request, Response $response){
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$params       = $this->util->getParams($params);
			$CDFILIAL     = $params['CDFILIAL'];

			$page = $request->getFilterCriteria()->getPage();
			$pageSize = $request->getFilterCriteria()->getPageSize();
			$page = isset($page)? $page: 1;
			$pageSize = isset($pageSize)? $pageSize: self::MAX_PAGES;
			if(isset($params['operador'])){
				$operador = $params['operador'];
			}else{
				$operador = '%%';
			}
			if($operador == '%%' && isset($params['CDVENDEDOR|NMFANVEN'])){
				$operador = $params['CDVENDEDOR|NMFANVEN'];
			}else{
				$operador = '%%';
			}
			$VendedoresLogin = $this->operatorService->getVendedoresLogin($CDFILIAL, $operador, $page, $pageSize);
			$response->addDataSet(new Dataset('VendedoresLogin', $VendedoresLogin));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function login(Request\Filter $request, Response $response){
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$params       = $this->util->getParams($params);
			$filial       = $params['filial'];       //str_pad($params[0]['value'], 4, '0', STR_PAD_LEFT);
			$caixa        = $params['caixa'];        //str_pad($params[1]['value'], 3, '0', STR_PAD_LEFT);
			$operador     = $params['operador'];
			$senha        = $params['senha'];        //$params[3]['value'];
			$frontVersion = $params['version'];      //$params[4]['value'];
			$currentMode  = $params['currentMode'];  //$params[5]['value'];
			$checkVersion = $this->util->checkVersion($frontVersion);

			if ($checkVersion['versionOk']){

				if ($filial == null) $response->addMessage(new Message($this->waiterMessage->getMessage('413')));
				else if ($caixa == null) $response->addMessage(new Message($this->waiterMessage->getMessage('414')));
				else if ($operador == null) $response->addMessage(new Message($this->waiterMessage->getMessage('415')));
				else if ($senha == null) $response->addMessage(new Message($this->waiterMessage->getMessage('416')));
				else {

					$dataset = array(
						'filial'      => $filial,
						'caixa'       => $caixa,
						'operador'    => $operador,
						'senha'       => $senha,
						'currentMode' => $currentMode
					);
					$loginResult = $this->operatorService->operatorLogin($dataset);

					if ($loginResult['error'] == false){
						$loginData = $this->handleLoginResult($loginResult);
						if(isset($loginResult['paramsImpressora'])){
							$loginData[0]['paramsImpressora'] = $loginResult['paramsImpressora'];
						}
						$paramsData = $this->params->getParams($loginData[0]['chave']);

						if ($paramsData['error'] == false){

							$paramsData = $paramsData['dados'];

							// get datasets from params
							//$mesas                    = $paramsData['mesas'];
							$ambientes                = $paramsData['ambientes'];
							$grupos                   = $paramsData['grupos'];
							$clientes                 = $paramsData['clientes'];
                            $familias                 = $paramsData['familias'];
							$consumidores             = $paramsData['consumidores'];
							$vendedores               = $paramsData['vendedores'];
							$cardapio                 = $paramsData['cardapio'];
							$grupoRecebimentos        = $paramsData['grupoRecebimentos'];
							$recebimentos             = $paramsData['recebimentos'];
							$impressoras              = $paramsData['impressoras'];
							$mensObservacao           = $paramsData['mensObservacao'];
							$mensProducao             = $paramsData['mensProducao'];
							$mensCancelamento         = $paramsData['mensCancelamento'];
							$parametros               = $paramsData['parametros'];
							$WOW_SO_MANY_OBSERVATIONS = $paramsData['ALL_THE_OBSERVATIONS'];
                            $nextUpdateTime           = $paramsData['nextUpdateTime'];
                            $smartPromoProducts       = $paramsData['smartPromoProducts'];
                            $mensDescontoObs          = $paramsData['mensDescontoObs'];

							// send datasets to frontend
							$response->addDataSet(new Dataset('OperatorRepository', $loginData));
							$response->addDataSet(new Dataset('ParamsAreaRepository', $ambientes));
							$response->addDataSet(new Dataset('ParamsGroupRepository', $grupos));
							$response->addDataSet(new Dataset('ParamsClientRepository', $clientes));
                            $response->addDataSet(new Dataset('ParamsFamilyRepository', $familias));
							$response->addDataSet(new Dataset('ParamsSellerRepository', $vendedores));
							$response->addDataSet(new Dataset('ParamsMenuRepository', $cardapio));
							$response->addDataSet(new Dataset('ParamsGroupPriceChart', $grupoRecebimentos));
							$response->addDataSet(new Dataset('ParamsPriceChart', $recebimentos));
							$response->addDataSet(new Dataset('ParamsPrinterRepository', $impressoras));
							$response->addDataSet(new Dataset('ParamsProdMessageRepository', $mensProducao));
							$response->addDataSet(new Dataset('ParamsProdMessageCancelRepository', $mensCancelamento));
							$response->addDataSet(new Dataset('ParamsParameterRepository', $parametros));
							$response->addDataSet(new Dataset('ParamsObservationsRepository', $WOW_SO_MANY_OBSERVATIONS));
                            $response->addDataSet(new Dataset('ParamsPriceTimeRepository', $nextUpdateTime));
                            $response->addDataSet(new Dataset('SmartPromoRepository', array(array(json_encode($smartPromoProducts)))));
                            $response->addDataSet(new Dataset('ParamsMensDescontoObs', $mensDescontoObs));

							if (!empty($loginResult['mensagemFiscal'])) {
								$response->addMessage(new Message($loginResult['mensagemFiscal']));
							}
							if (!empty($loginResult['mensagemImpressora'])) {
								$response->addMessage(new Message($loginResult['mensagemImpressora']));
							}
						} else {
                            if ($paramsData['message']) $response->addMessage(new Message($paramsData['message'], 'E'));
                            else $response->addDataSet(new Dataset('OperatorRepository', array($paramsData['dump'])));
						}
					} else {
						if ($loginResult['message']) $response->addMessage(new Message($loginResult['message'], 'E'));
						else {
                            if (!isset($loginResult['dump'])) $loginResult['dump'] = 'Ocorreu um erro inesperado.';
                            $response->addDataSet(new Dataset('OperatorRepository', array($loginResult['dump'])));
                        }
					}
				}
			} else {
				$response->addMessage(new Message('A versão do dispositivo não está compatível com a do servidor.<br><br>Versão do dispositivo: '.$checkVersion['frontVersion'].'<br>Versão do servidor: '.$checkVersion['backVersion']));
			}
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addDataSet(new Dataset('OperatorRepository', array($e->getMessage())));
		}
	}

	private function handleLoginResult($dataset){
		return array(array(
			'chave'               => $dataset['chave'],
            'CDFILIAL'            => $dataset['CDFILIAL'],
			'CDOPERADOR'          => $dataset['CDOPERADOR'],
			'supervisor'          => $dataset['supervisor'],
			'CDVENDEDOR'          => $dataset['vendedor'],
			'NMOPERADOR'          => $dataset['NMOPERADOR'],
			'NMFANVEN'            => $dataset['NMFANVEN'],
			'NRATRAPADRAO'        => $dataset['NRATRAPADRAO'],
			'IDUTLQTDPED'         => $dataset['IDUTLQTDPED'],
			'IDCOLETOR'           => $dataset['IDCOLETOR'],
			'IDUTILTEF'           => $dataset['IDUTILTEF'],
			'IDPALFUTRABRCXA'     => $dataset['IDPALFUTRABRCXA'],
			'VRABERCAIX'          => $dataset['VRABERCAIX'],
			'IDLUGARMESA'         => $dataset['CONTROLEACESSO']['IDLUGARMESA'],
			'estadoCaixa'         => $dataset['estadoCaixa'],
			'obrigaFechamento'    => $dataset['obrigaFechamento'],
			'IDTPTEF'             => $dataset['IDTPTEF'],
			'AMBIENTEPRODUCAO'    => $dataset['AMBIENTEPRODUCAO'],
			'IDMODEIMPRES'        => $dataset['IDMODEIMPRES'],
			'CDTERTEF'		      => $dataset['CDTERTEF'],
			'CDLOJATEF'		      => $dataset['CDLOJATEF'],
			'DSENDIPSITEF'        => $dataset['DSENDIPSITEF'],
			"IDSOLDIGCONS"        => $dataset["IDSOLDIGCONS"],
			'IDCOMISVENDA'	   	  => $dataset['IDCOMISVENDA'],
			'IDUTLNMCONSMESA'	  => $dataset['IDUTLNMCONSMESA'],
			'IDAGRUPAPEDCOM'      => $dataset['IDAGRUPAPEDCOM'],
			'QTDMAXDIGNSU'		  => $dataset['QTDMAXDIGNSU'],
			'IDSOLICITANSU'       => $dataset['IDSOLICITANSU'],
            'IDPERDIGCONS'        => $dataset['IDPERDIGCONS'],
			'IDHABCAIXAVENDA'     => $dataset['IDHABCAIXAVENDA'],
            'IDTPTELAVE'          => $dataset['IDTPTELAVE'],
            'IDSOLICITACPF'       => $dataset['IDSOLICITACPF'],
            'IDTPEMISSAOFOS'	  => $dataset['IDTPEMISSAOFOS'],
            'NRINSJURFILI'	  	  => $dataset['NRINSJURFILI'],
			'IDUTLSSL'	  	  	  => $dataset['IDUTLSSL'],
			'CDPICTPROD'	  	  => $dataset['CDPICTPROD'],
			'IDLCDBARBALATOL'	  => $dataset['IDLCDBARBALATOL'],
			'NRPOSINICODBARR'	  => $dataset['NRPOSINICODBARR'],
			'NRPOSFINCODBARR'	  => $dataset['NRPOSFINCODBARR'],
            'IDLEITURAQRCODE'     => $dataset['IDLEITURAQRCODE'],
			'NRMAXPESMES'     	  => $dataset['NRMAXPESMES'],
            'IDEXTCONSONLINE'     => $dataset['IDEXTCONSONLINE'],
            'IDCTRLPEDVIAGEM'     => $dataset['IDCTRLPEDVIAGEM'],
            'IDCONSUBDESFOL'      => $dataset['IDCONSUBDESFOL'],
			'IDUTLSENHAOPER'	  => $dataset['IDUTLSENHAOPER'],
			'IDINFPRODPRODUZ'	  => $dataset['IDINFPRODPRODUZ'],
			'IDSOLOBSDESC'	  	  => $dataset['IDSOLOBSDESC'],
			'IDSOLTPSANGRIACX'	  => $dataset['IDSOLTPSANGRIACX'],
			'IDCAIXAEXCLUSIVO'    => $dataset['IDCAIXAEXCLUSIVO'],
			'IDSENHACUP'    	  => $dataset['IDSENHACUP'],
			'IDSOLOBSFINVEN'      => $dataset['IDSOLOBSFINVEN'],
			'CDLOJA'      		  => $dataset['CDLOJA'],
            'CDCAIXA'			  => $dataset['CDCAIXA'],
            'UTLCAMPANHA'         => $dataset['UTLCAMPANHA'],
            'IDSOLOBSCAN'         => $dataset['IDSOLOBSCAN'],
            'PRECOCOUVERT'        => $dataset['PRECOCOUVERT'],
            'CDCLIENTE'           => $dataset['CDCLIENTE'],
            'NMFANTCLIE'          => $dataset['NMFANTCLIE'],
			// permissões
			'cancelaItemGenerico' => $dataset['CONTROLEACESSO']['cancelaItemGenerico'],
			'cancelaMesaComanda'  => $dataset['CONTROLEACESSO']['cancelaMesaComanda'],
			'liberarMesa'         => $dataset['CONTROLEACESSO']['liberarMesa'],
			'cupomDesconto'       => $dataset['CONTROLEACESSO']['cupomDesconto'],
			'retirarConsumacao'   => $dataset['CONTROLEACESSO']['retirarConsumacao'],
			'retirarCouvert'      => $dataset['CONTROLEACESSO']['retirarCouvert'],
			'retirarTaxaServico'  => $dataset['CONTROLEACESSO']['retirarTaxaServico'],
			'agruparMesas'        => $dataset['CONTROLEACESSO']['agruparMesas'],
			'cancelarAgrupamento' => $dataset['CONTROLEACESSO']['cancelarAgrupamento'],
			'transferirMesa'      => $dataset['CONTROLEACESSO']['transferirMesa'],
			'transferirProduto'   => $dataset['CONTROLEACESSO']['transferirProduto'],
			'alterarQtPessoas'    => $dataset['CONTROLEACESSO']['alterarQtPessoas'],
			'parcialConta'        => $dataset['CONTROLEACESSO']['parcialConta'],
			'modoHabilitado'      => $dataset['CONTROLEACESSO']['modoHabilitado'],
			'abrirComanda'        => $dataset['CONTROLEACESSO']['abrirComanda'],
			'bloqComandaParcial'  => $dataset['CONTROLEACESSO']['bloqComandaParcial'],
			'infoMesAbrComanda'   => $dataset['CONTROLEACESSO']['infoMesAbrComanda'],
			'infConsAbrComanda'   => $dataset['CONTROLEACESSO']['infConsAbrComanda'],
			'geraNrComandaAut'    => $dataset['CONTROLEACESSO']['geraNrComandaAut'],
			'cancelaCupom'        => $dataset['CONTROLEACESSO']['cancelaCupom'],
			'administracaoTEF'    => $dataset['CONTROLEACESSO']['administracaoTEF'],
			'bloqueiaProduto'     => $dataset['CONTROLEACESSO']['bloqueiaProduto'],
			'leituraX'     		  => $dataset['CONTROLEACESSO']['leituraX'],
			'reimpressaoFiscal'   => $dataset['CONTROLEACESSO']['reimpressaoFiscal'],
			'sangria'   		  => $dataset['CONTROLEACESSO']['sangria'],
			'bloqueiaDispositivo' => $dataset['CONTROLEACESSO']['bloqueiaDispositivo'],
			'mensagemProducao'    => $dataset['CONTROLEACESSO']['mensagemProducao']
		));
	}

	public function validateSupervisor(Request\Filter $request, Response $response){
		try {
			$params      = $request->getFilterCriteria()->getConditions();
			$supervisor  = $params[0]['value'];
			$senha       = $params[1]['value'];
            $accessParam = $params[2]['value'];

			if (empty($supervisor)) $response->addMessage(new Message($this->waiterMessage->getMessage('422')));
			if (empty($senha)) $response->addMessage(new Message($this->waiterMessage->getMessage('423')));

			$dataset = array(
				'supervisor'  => $supervisor,
				'senha'       => $senha,
                'accessParam' => $accessParam
			);
			$loginResult = $this->operatorService->validaSupervisor($dataset);

			if ($loginResult['funcao'] == '1') $response->addDataSet(new Dataset('nothing', array(array('retorno' => '1'))));
			else $response->addMessage(new Message($this->waiterMessage->getMessage($loginResult['error'])));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($this->waiterMessage->getMessage($e->getMessage())));
		}
	}

    public function validateConsumerPass(Request\Filter $request, Response $response){
        try {
            $params       = $request->getFilterCriteria()->getConditions();
            $CDCLIENTE    = $params[0]['value'];
            $CDCONSUMIDOR = $params[1]['value'];
            $senha        = $params[2]['value'];

            $loginResult = $this->operatorService->validaConsumidor($CDCLIENTE, $CDCONSUMIDOR, $senha);

            if ($loginResult['funcao'] == '1') $response->addDataSet(new Dataset('nothing', array(array('retorno' => '1'))));
            else $response->addMessage(new Message($this->waiterMessage->getMessage($loginResult['error']), Message::TYPE_WARNING));
        } catch (\Exception $e){
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage(), Message::TYPE_ERROR));
        }
    }

	public function logout($request, Response $response){
		try {
			// $this->sessionHandler->endSession();
			$this->operatorService->logout();
			// $this->util->endSession($chave);
			$response->addNotification(new Notification($this->waiterMessage->getMessage('463'), Notification::TYPE_SUCCESS));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addNotification(new Notification('Ocorreu um problema ao finalizar a sessão no servidor.', Notification::TYPE_ERROR));
		}
	}

	public function trocaModoCaixa(Request\Filter $request, Response $response){
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$params       = $this->util->getParams($params);
			$currentMode  = $params['currentMode'];
			$chave        = $params['chaveSessao'];

			// $this->sessionHandler->endSession();
			// $this->util->endSession($chave);
			$session      = $this->util->getSessionVars($chave);

			$dataset = array(
				'filial'       => $session['CDFILIAL'],
				'caixa'        => $session['CDCAIXA'],
				'operador'     => $session['CDOPERADOR'],
				'senha'        => null,
				'currentMode'  => $currentMode,
				'sessionCache' => true
			);
			$loginResult = $this->operatorService->operatorLogin($dataset,false);
			if ($loginResult['error'] == false){

				$loginData = $this->handleLoginResult($loginResult);
				$loginData[0]['paramsImpressora'] = $loginResult['paramsImpressora'];
				$paramsData = $this->params->getParams($loginData[0]['chave']);

				if ($paramsData['error'] == false){

					$paramsData = $paramsData['dados'];

					// get datasets from params
					//$mesas                    = $paramsData['mesas'];
					$ambientes                = $paramsData['ambientes'];
					$grupos                   = $paramsData['grupos'];
					$clientes                 = $paramsData['clientes'];
					$consumidores             = $paramsData['consumidores'];
					$vendedores               = $paramsData['vendedores'];
					$cardapio                 = $paramsData['cardapio'];
					$grupoRecebimentos        = $paramsData['grupoRecebimentos'];
					$recebimentos             = $paramsData['recebimentos'];
					$impressoras              = $paramsData['impressoras'];
					$mensObservacao           = $paramsData['mensObservacao'];
					$mensProducao             = $paramsData['mensProducao'];
					$mensCancelamento         = $paramsData['mensCancelamento'];
					$parametros               = $paramsData['parametros'];
					$WOW_SO_MANY_OBSERVATIONS = $paramsData['ALL_THE_OBSERVATIONS'];
					$mensDescontoObs          = $paramsData['mensDescontoObs'];

					// send datasets to frontend
					$response->addDataSet(new Dataset('OperatorRepository', $loginData));
					$response->addDataSet(new Dataset('ParamsAreaRepository', $ambientes));
					$response->addDataSet(new Dataset('ParamsGroupRepository', $grupos));
					$response->addDataSet(new Dataset('ParamsClientRepository', $clientes));
					$response->addDataSet(new Dataset('ParamsSellerRepository', $vendedores));
					$response->addDataSet(new Dataset('ParamsMenuRepository', $cardapio));
					$response->addDataSet(new Dataset('ParamsGroupPriceChart', $grupoRecebimentos));
					$response->addDataSet(new Dataset('ParamsPriceChart', $recebimentos));
					$response->addDataSet(new Dataset('ParamsPrinterRepository', $impressoras));
					$response->addDataSet(new Dataset('ParamsProdMessageRepository', $mensProducao));
					$response->addDataSet(new Dataset('ParamsProdMessageCancelRepository', $mensCancelamento));
					$response->addDataSet(new Dataset('ParamsParameterRepository', $parametros));
					$response->addDataSet(new Dataset('ParamsObservationsRepository', $WOW_SO_MANY_OBSERVATIONS));
					$response->addDataSet(new Dataset('ParamsMensDescontoObs', $mensDescontoObs));

					if (!empty($loginResult['mensagemFiscal'])) {
						$response->addMessage(new Message($loginResult['mensagemFiscal']));
					}
					if (!empty($loginResult['mensagemImpressora'])) {
						$response->addMessage(new Message($loginResult['mensagemImpressora']));
					}
				} else {
					$response->addMessage(new Message($paramsData['message']));
				}
			} else {
				if ($loginResult['message']) $response->addMessage(new Message($loginResult['message']));
				else $response->addDataSet(new Dataset('OperatorRepository', array($loginResult['dump'])));
			}

			$response->addMessage(new Message("Troca efetuada com sucesso"));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage(), Message::TYPE_ERROR));
		}
	}

	public function findTefSSLConnectionId(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);

			$result = $this->operatorService->findTefSSLConnectionId($params);
			$result = $result === false ? array() : $result;
			$response->addDataSet(new Dataset('FindTefSSLConnectionId', $result));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage(), Message::TYPE_ERROR));
		}
	}

	private function validateUserByMailAndPassword(){
		$this->operator = $this->operatorService->getUserByMailAndPassword($this->credentials['email'], $this->credentials['senha']);
		if(!$this->operator)
			throw new \Exception("Credenciais inválidas.", 1);
	}

	private function validateCredentials(){
		if(empty($this->credentials['email']) || empty($this->credentials['senha']))
			throw new \Exception("Todos os campos devem ser preenchidos.", 1);
	}

	public function auth(Request\Filter $request, Response $response){

		try{
			$this->session->start();
			$params = $request->getFilterCriteria()->getConditions();
			$this->credentials = $this->util->getParams($params);
			$this->validateCredentials();
			$this->validateUserByMailAndPassword();
			$this->session->set('AUTH',array('NRORG'=>$this->operator['NRORG']));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('operador', array('cdoperador'=>$this->operator['CDOPERADOR'])));
		} catch (\InexistentException $e){
			Exception::logException($e);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('auth', array($e->getMessage())));
		}

	}

	public function findPendingPayments(Request\Filter $request, Response $response){
		try{
			$session = $this->util->getSessionVars(null);
			$result = $this->operatorService->findPendingPayments($session);

			if(!$result) {
				$result = array("error" => true, "data" => array(), "message" => "");
			} else {
				$result = array("error" => false, "data" => $result, "message" => "");
			}
		} catch (\InexistentException $e){
			Exception::logException($e);
			$result = array("error" => true, "data" => array(), "message" => $e->getMessage());
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('FindPendingPayments', $result));
	}

}