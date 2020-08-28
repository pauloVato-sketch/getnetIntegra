<?php
namespace Controller;

use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class GeneralFunctions {

	protected $util;
	protected $generalFunctions;
	protected $caixaApi;

	public function __construct(\Util\Util $util, \Service\GeneralFunctions $generalFunctions, \Odhen\API\Service\Caixa $caixaApi){
		$this->util = $util;
		$this->generalFunctions = $generalFunctions;
		$this->caixaApi = $caixaApi;
	}

	public function reprintSaleCoupon(Request\Filter $request, Response $response) {
		try {
			$session = $this->util->getSessionVars(null);

			$params = $request->getFilterCriteria()->getConditions();
			$reprintType = $params[0]['value'];
			$saleCode = $params[1]['value'];

			$result = $this->generalFunctions->reprintSaleCoupon($session, $reprintType, $saleCode);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ReprintSaleCoupon', $result));
	}

	public function homologacaoSitef(Request\Filter $request, Response $response) {

		$sequencia = 4;
		$result = array();

		switch ($sequencia) {
			case 1:
			case 2:
			case 3:
			case 11:
				$result[0] = "command-2|1";
				break;
			case 4:
				$result[0] = "command-1|1";
				break;
			case 5:
				$result[0] = "command-1|3,quote-2";
				break;
			case 6:
				break;
			case 7:
				$result[0] = "command-2|3";
				break;
			case 8:
				break;
			case 9:
				$result[0] = "command-2|1|1,cardNumber-4000000000000044,cardDate-1314";
				break;
			case 10:
				$result[0] = "command-2|1|1,cardNumber-4000000000000000,cardDate-1222";
				break;
			case 12:
				$result[0] = "command-2|1|3,cardNumber-5000000000000001,cardDate-1222,quote-2";
				break;
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('HomologacaoSitef', $result));
	}

	public function selectUnblockedProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);
			if (isset($params[0])) {
				$filter = $params[0];
			} else {
				$filter = array (
					'columnName' => 'CDPRODUTO|NMPRODUTO',
					'value' => '%%',
					'operator' => 'ALL'
				);
			}
			$page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

			$result = $this->generalFunctions->selectUnblockedProducts($session, $filter, $FIRST, $LAST);

		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SelectProducts', $result));
	}

	public function selectBlockedProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);
			if (isset($params[0])) {
				$filter = $params[0];
			} else {
				$filter = array (
					'columnName' => 'CDPRODUTO|NMPRODUTO',
					'value' => '%%',
					'operator' => 'ALL'
				);
			}
			$page = $request->getFilterCriteria()->getPage();
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

			$result = $this->generalFunctions->selectBlockedProducts($session, $filter, $FIRST, $LAST);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SelectBlockedProducts', $result));
	}

	public function blockProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$arrProducts = $params['CDPRODUTO'];
			$session = $this->util->getSessionVars(null);

			$result = $this->generalFunctions->blockProducts($session, $arrProducts);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('BlockProducts', $result));
	}

	public function unblockProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$arrProducts = $params['CDPRODUTO'];
			$session = $this->util->getSessionVars(null);

			$result = $this->generalFunctions->unblockProducts($session, $arrProducts);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('UnblockProducts', $result));
	}

	public function impressaoLeituraX(Request\Filter $request, Response $response) {
		try {
			$session = $this->util->getSessionVars(null);
			$dadosImpressao = array();
			$result = $this->generalFunctions->impressaoLeituraX($session, $dadosImpressao);
			$result['dadosImpressao'] = $dadosImpressao;
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ImpressaoLeituraX', $result));
	}

	public function getNrControlTef(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$params = $this->util->getParams($params);
			$result = $this->generalFunctions->getNrControlTef($params['CDNSUHOSTTEF']);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('GetNrControlTef', $result));
	}

	public function ultimasVendasDesc(Request\Filter $request, Response $response) {
		try {
			$session = $this->util->getSessionVars(null);
			$filter = $request->getFilterCriteria()->getConditions();
			$filter = !empty($filter) ? $filter[0]['value'] : '%%';

			$result = $this->generalFunctions->ultimasVendasDesc($session, $filter);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message('Ocorreu um problema ao carregar as vendas.', 'error'));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('UltimasVendasDesc', $result));
	}

	public function ultimosPagamentosDesc(Request\Filter $request, Response $response) {
		try {
			$session = $this->util->getSessionVars(null);
			$page = $request->getFilterCriteria()->getPage();
			$page = $page == 0 ? 1 : $page;
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

			$result = $this->generalFunctions->ultimosPagamentosDesc($session, $FIRST, $LAST);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message('Ocorreu um problema ao carregar os pagamentos.', 'error'));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('UltimosPagamentosDesc', $result));
	}

	public function tipoRecebimento(Request\Filter $request, Response $response) {
		try {
			$session = $this->util->getSessionVars(null);

			$result = $this->generalFunctions->tipoRecebimento($session);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message($e->getMessage(), 'error'));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TipoRecebimento', $result));
	}

	public function tipoSangria(Request\Filter $request, Response $response) {
		try {
			$result = $this->generalFunctions->tipoSangria();
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message($e->getMessage(), 'error'));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TipoSangria', $result));
	}

	public function saveSangria(Request\Filter $request, Response $response) {
		try {
			$session = $this->util->getSessionVars(null);
			$params = $request->getFilterCriteria()->getConditions();
			$itemsSangria = null;
			$imprimeSangria = null;
			if (!empty($params)) {
				$itemsSangria = !empty($params[0]) ? $params[0]['value'] : null;
				$imprimeSangria = !empty($params[1]) ? $params[1]['value'] : null;

				$result = $this->caixaApi->saveSangria($session, $itemsSangria, $imprimeSangria);

				$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification("Sangria realizada com sucesso.", \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
			} else {
				$result = array(
					'error' => true,
					'message' => 'Não foi possível recuperar os dados referentes ao lançamento de Sangria.'
				);
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SaveSangria', $result));
	}
}