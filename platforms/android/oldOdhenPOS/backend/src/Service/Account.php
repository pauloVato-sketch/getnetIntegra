<?php
namespace Service;

use \Util\Exception;

class Account {

	protected $entityManager;
	protected $precoAPI;
	protected $util;
	protected $vendaAPI;
	protected $tableService;
    protected $pedidoService;
	protected $waiterMessage;
	protected $billService;
	protected $impressaoService;
	protected $transactionsService;
	protected $date;
	protected $registerService;
	protected $caixaAPI;
    protected $extratocons;
 	protected $parametrosAPI;
    protected $utilAPI;
    protected $consumidorAPI;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Odhen\API\Service\Preco $precoAPI,
		\Util\Util $util,
		\Odhen\API\Service\Venda $vendaAPI,
		\Service\Table $tableService,
        \Service\Pedido $pedidoService,
		\Util\WaiterMessage $waiterMessage,
		\Service\Bill $billService,
		\Service\Impressao $impressaoService,
		\Service\Transactions $transactionsService,
		\Util\Date $date,
		\Service\Register $registerService,
		\Odhen\API\Service\Caixa $caixaAPI,
        \Odhen\API\Service\Extratocons $extratocons,
 		\Odhen\API\Service\Parametros $parametrosAPI,
        \Odhen\API\Util\Util $utilAPI,
        \Odhen\API\Service\Consumidor $consumidorAPI
	){
		$this->entityManager       = $entityManager;
		$this->precoAPI            = $precoAPI;
		$this->util                = $util;
		$this->vendaAPI            = $vendaAPI;
		$this->tableService        = $tableService;
        $this->pedidoService       = $pedidoService;
		$this->waiterMessage       = $waiterMessage;
		$this->billService         = $billService;
		$this->impressaoService    = $impressaoService;
		$this->transactionsService = $transactionsService;
		$this->date                = $date;
		$this->registerService 	   = $registerService;
		$this->caixaAPI   		   = $caixaAPI;
        $this->extratocons         = $extratocons;
		$this->parametrosAPI       = $parametrosAPI;
        $this->utilAPI             = $utilAPI;
        $this->consumidorAPI	   = $consumidorAPI;
	}

	private function recalcProductPrice($CDFILIAL, $CDLOJA, $arrayItens, $positions, $CDCLIENTE, $CDCONSUMIDOR){
		$result = array(
			'status'  => true,
			'message' => ''
		);

        $promocoes = array();
		if (!empty($arrayItens)){
			foreach ($arrayItens as &$produto) {
				$client   = $CDCLIENTE;
				$consumer = $CDCONSUMIDOR;
				if (isset($positions[$produto['NRLUGARMESA']])){
					if (!!$positions[$produto['NRLUGARMESA']]['CDCLIENTE']){
						$client   = $positions[$produto['NRLUGARMESA']]['CDCLIENTE'];
						$consumer = $positions[$produto['NRLUGARMESA']]['CDCONSUMIDOR'];
					}
				}

                if ($this->utilAPI->databaseIsOracle()){
                    $DATETIME = \DateTime::createFromFormat('Y-m-d H:i:s', $produto['DTHRINCOMVEN']);
                }
                else {
                    $DATETIME = \DateTime::createFromFormat('Y-m-d H:i:s.u', $produto['DTHRINCOMVEN']);
                }

				$precProduct = $this->precoAPI->buscaPreco($produto['CDFILIAL'], $client, $produto['CDPRODUTO'], $produto['CDLOJA'], $consumer, null, $DATETIME);
				if (!$precProduct['error']){
	                if ($produto['CDPRODPROMOCAO']){
	                    $promoDiscount = $this->pedidoService->calculaDesconto($produto['CDFILIAL'], $produto['CDPRODPROMOCAO'], $produto['CDPRODUTO'], $precProduct['PRECO']+$precProduct['PRECOCLIE'], array());
	                    if ($promoDiscount < 0) $precProduct['DESC'] -= $promoDiscount;
	                    else $precProduct['ACRE'] += $promoDiscount;
                        $chave = $produto['CDPRODPROMOCAO'].$produto['NRSEQPRODCOM'];
                        if (!array_key_exists($chave, $promocoes)){
                            $promocoes[$chave] = array();
                        }
                        array_push($promocoes[$produto['CDPRODPROMOCAO'].$produto['NRSEQPRODCOM']], $produto);
	                }

	                $produto['VRPRECCOMVEN'] = $precProduct['PRECO'];
					$produto['VRPRECCLCOMVEN'] = $precProduct['PRECOCLIE'];
					$produto['VRDESCCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($precProduct['DESC'])), str_replace(',','.',strval($produto['QTPRODCOMVEN'])), '2'));
					$produto['VRACRCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($precProduct['ACRE'])), str_replace(',','.',strval($produto['QTPRODCOMVEN'])), '2'));
					$paramsUpdate = $this->formatUpdateITCOMANDAVEN($produto, $produto['VRPRECCOMVEN'], $produto['VRPRECCLCOMVEN'],
						$produto['VRDESCCOMVEN'], $produto['VRACRCOMVEN'], $produto['DSOBSDESCIT'], $produto['CDGRPOCORDESCIT'], 'N');
					$this->entityManager->getConnection()->executeQuery("UPDATE_PRICE_ON_ITCOMANDAVEN", $paramsUpdate);
				}
                else {
					$result['status']  = false;
					$result['message'] = $precProduct['message'];
					break;
				}
			}

			if ($result['status']){
				// rotina específica do Madero
				$this->handleItensPromo($arrayItens);
			}

            // Campanha promocional.
            $this->recalculoCampanha($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $promocoes, $positions);
		}

		return $result;
	}

    private function recalculoCampanha($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $promocoes, $positions){
        if (!empty($promocoes)){
            foreach ($promocoes as $promoGroup){
                $CDPRODPROMOCAO = $promoGroup[0]['CDPRODPROMOCAO'];
                $NRVENDAREST = $promoGroup[0]['NRVENDAREST'];
                if ($this->utilAPI->databaseIsOracle()){
                    $DATETIME = \DateTime::createFromFormat('Y-m-d H:i:s', $promoGroup[0]['DTHRINCOMVEN']);
                }
                else{
                    $DATETIME = \DateTime::createFromFormat('Y-m-d H:i:s.u', $promoGroup[0]['DTHRINCOMVEN']);
                }
                $products = array();
                foreach ($promoGroup as $promoItem){
                    $item = array();
                    $item['CDPRODUTO'] = $promoItem['CDPRODUTO'];
                    $item['QTPRODCOMVEN'] = $promoItem['QTPRODCOMVEN'];
                    $item['ATRASOPROD'] = null;
                    $item['TOGO'] = null;
                    $item['DSOCORR_CUSTOM'] = null;
                    $item['CDOCORR'] = null;
                    $item['TXPRODCOMVEN'] = null;
                    $item['IMPRESSORA'] = null;
                    $item['DATETIME'] = $DATETIME;
                    array_push($products, $item);
                }

                $client   = $CDCLIENTE;
				$consumer = $CDCONSUMIDOR;
				if (isset($positions[$promoGroup[0]['NRLUGARMESA']])){
					if (!!$positions[$promoGroup[0]['NRLUGARMESA']]['CDCLIENTE']){
						$client   = $positions[$promoGroup[0]['NRLUGARMESA']]['CDCLIENTE'];
						$consumer = $positions[$promoGroup[0]['NRLUGARMESA']]['CDCONSUMIDOR'];
					}
				}

                $precosCampanha = $this->pedidoService->formataPromocaoCombinada($CDFILIAL, $client, $consumer, $CDLOJA, $products, $CDPRODPROMOCAO, $NRVENDAREST, null, false, null, '2');

                for ($i = 0; $i < sizeof($precosCampanha); $i++){
                    $precosCampanha[$i]['VRDESCCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($precosCampanha[$i]['VRDESCCOMVEN'])), str_replace(',','.',strval($precosCampanha[$i]['QTPRODCOMVEN'])), '2'));
                    $precosCampanha[$i]['VRACRCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($precosCampanha[$i]['VRACRCOMVEN'])), str_replace(',','.',strval($precosCampanha[$i]['QTPRODCOMVEN'])), '2'));
                    $paramsUpdate = $this->formatUpdateITCOMANDAVEN($promoGroup[$i], $precosCampanha[$i]['VRPRECCOMVEN'], $precosCampanha[$i]['VRPRECCLCOMVEN'], $precosCampanha[$i]['VRDESCCOMVEN'], $precosCampanha[$i]['VRACRCOMVEN'], $promoGroup[$i]['DSOBSDESCIT'], $promoGroup[$i]['CDGRPOCORDESCIT'], 'N');
                    $this->entityManager->getConnection()->executeQuery("UPDATE_PRICE_ON_ITCOMANDAVEN", $paramsUpdate);
                }
            }
        }
    }

    public function formatUpdateITCOMANDAVEN($item, $price, $subs, $desc, $acre, $DSOBSDESCIT, $CDGRPOCORDESCIT, $IDDESCMANUAL){
		return array(
			'CDFILIAL'        => $item['CDFILIAL'],
			'NRVENDAREST'     => $item['NRVENDAREST'],
			'NRCOMANDA'       => $item['NRCOMANDA'],
			'NRPRODCOMVEN'    => $item['NRPRODCOMVEN'],
			'NRORG'           => $item['NRORG'],
			'VRPRECCOMVEN'    => $price,
            'VRPRECCLCOMVEN'  => $subs,
			'VRDESCCOMVEN'    => $desc,
			'VRACRCOMVEN'     => $acre,
			'DSOBSDESCIT'     => !empty($DSOBSDESCIT) ? substr($DSOBSDESCIT, 0, 100) : null,
			'CDGRPOCORDESCIT' => $CDGRPOCORDESCIT,
            'IDDESCMANUAL'    => $IDDESCMANUAL
		);
	}

	public function updateTableData($chave, $tableData, $CDCLIENTE, $CDCONSUMIDOR){
		$session = $this->util->getSessionVars($chave);

		foreach ($tableData as $table) {
			// VRDESCFID = 0 por causa da alteração de cliente/consumidor
			$paramsUpdate = $this->formatUpdateTableData($session['CDFILIAL'], $table, $CDCLIENTE, $CDCONSUMIDOR, 0);
			$this->entityManager->getConnection()->executeQuery("UPDATE_VENDAREST", $paramsUpdate);
			$this->entityManager->getConnection()->executeQuery("UPDATE_COMANDAVEN_DESCFID", $paramsUpdate);
		}
	}

	public function formatUpdateTableData($CDFILIAL, $table, $CDCLIENTE, $CDCONSUMIDOR, $VRDESCFID){
		return array(
			'CDFILIAL'     => $CDFILIAL,
			'NRVENDAREST'  => $table['NRVENDAREST'],
			'NRCOMANDA'    => $table['NRCOMANDA'],
			'VRDESCFID'    => $VRDESCFID,
			'CDCLIENTE'    => $CDCLIENTE,
			'CDCONSUMIDOR' => $CDCONSUMIDOR
		);
	}

	public function changeClientConsumer($chave, $NRVENDAREST, $NRCOMANDA, $positions, $CDCLIENTE, $CDCONSUMIDOR){

		$session    = $this->util->getSessionVars($chave);
        $this->checkClientConsumer($session['CDFILIAL'], $CDCLIENTE, $CDCONSUMIDOR);

		$result = array(
			'status'  => false,
			'message' => '',
			'data'    => array()
		);
		$connection = false;
		$positions  = $this->tableService->definePosition($positions); // ajusta posições com 0 a esquerda
		$CDFILIAL   = $session['CDFILIAL'];
        $CDLOJA     = $session['CDLOJA'];
		try {
			$this->entityManager->getConnection()->beginTransaction();
			$connection = true;
		} catch (\Exception $e) {
			Exception::logException($e);
			$result['message'] = $this->waiterMessage->getMessage('265');
		}

		if ($connection){
			if ($session['IDMODULO'] !== 'C'){
				$tableData = $this->tableService->dadosMesa($CDFILIAL, $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST);
				$positions = array_column($positions, null, 'NRLUGARMESA');

                $params = array(
                    'CDFILIAL' => $session['CDFILIAL'],
                    'NRVENDAREST' => $NRVENDAREST
                );
                $valTrans = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_MOVCAIXADLV", $params);
                if (!empty($valTrans)){
                    return array('status' => false, 'message' => $this->waiterMessage->getMessage('462'));
                }

				$modifyTablePosition = $this->tableService->modifyTablePosition($chave, $NRVENDAREST, $positions);
				if ($modifyTablePosition['status']){
					if (empty($positions)){
						$CDCLIENTE = $CDCLIENTE ? $CDCLIENTE : $session['CDCLIENTE'];
						// cliente e consumidor por mesa. CDCLIENTE e CDCONSUMIDOR é mantido
						$this->updateTableData($chave, array($tableData), $CDCLIENTE, $CDCONSUMIDOR);
					} else {
						// cliente e consumidor por posição. CDCLIENTE e CDCONSUMIDOR salvo na mesa é utilizado como parâmetro quando a posição não for setada na recalcProductPrice
						$CDCLIENTE    = $tableData['CDCLIENTE'];
						$CDCONSUMIDOR = $tableData['CDCONSUMIDOR'];
					}
					// busca junção de mesas, se houver
					$tableGroup = $this->tableService->getTablesFromTableGrouping($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $tableData['NRMESA'], $session['NRORG']);
					// busca itens a partir da NRVENDAREST, NRCOMANDA e posições
					$arrayItens = $this->vendaAPI->buscaItensPedidos($CDFILIAL, $CDLOJA, $tableGroup, array_column($positions, 'NRLUGARMESA'));
					$arrayItens = $this->removeCancelItens($arrayItens);
					// busca preços e executa update
					$recalcResult = $this->recalcProductPrice($CDFILIAL, $session['CDLOJA'], $arrayItens, $positions, $CDCLIENTE, $CDCONSUMIDOR);

					if ($recalcResult['status']){
						$result['status']  = true;
						$result['message'] = $this->waiterMessage->getMessage('457');
						$result['data']    = $tableData;
					} else {
						$result['message'] = $recalcResult['message'];
					}
				} else {
					$result['message'] = $modifyTablePosition['message'];
				}
			} else {
				$comandData = $this->billService->dadosComanda($CDFILIAL, $NRCOMANDA, $NRVENDAREST, $CDLOJA);
				$this->updateTableData($chave, $comandData, $CDCLIENTE, $CDCONSUMIDOR);
				// busca itens a partir da NRVENDAREST e NRCOMANDA
				$arrayItens = $this->vendaAPI->buscaItensPedidos($CDFILIAL, $CDLOJA, $comandData, array());
				$arrayItens = $this->removeCancelItens($arrayItens);
				// busca preços e executa update
				$recalcResult = $this->recalcProductPrice($CDFILIAL, $session['CDLOJA'], $arrayItens, array(), $CDCLIENTE, $CDCONSUMIDOR);

				if ($recalcResult['status']){
					$result['status']  = true;
					$result['message'] = $this->waiterMessage->getMessage('457');
					$result['data']    = $comandData;
				} else {
					$result['message'] = $recalcResult['message'];
				}
			}
		}

		if ($result['status']){
			$this->entityManager->getConnection()->commit();
		} else {
			$this->entityManager->getConnection()->rollBack();
		}
		return $result;
	}

	public function removeCancelItens($arrayItens){
		$returnItens = array();

		foreach ($arrayItens as $item) {
			if ($item['IDSTPRCOMVEN'] !== '6'){
				array_push($returnItens, $item);
			}
		}

		return $returnItens;
	}

	public function handleAccountItems($dataset){
		$impPosicao = $dataset['posicao'];
		$NRCOMANDA = $dataset['NRCOMANDA'];
		$NRVENDAREST = $dataset['NRVENDAREST'];
		$session = $this->util->getSessionVars($dataset['chave']);
		$NRCONFTELA = $session['NRCONFTELA'];
		if ($dataset['mode'] === 'C') {
			// valida e busca dados da comanda
			$valComanda = $this->billService->dadosComanda($session['CDFILIAL'], $NRCOMANDA, $NRVENDAREST, $session['CDLOJA']);
			$stNrVendaRest = $valComanda['NRVENDAREST'];
			$stNrComanda = $valComanda['NRCOMANDA'];
		}
		else if ($dataset['mode'] === 'M') {
			// valida e busca os dados da mesa
			$valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST);
			$stNrVendaRest = $valMesa['NRVENDAREST'];
			$stNrComanda = $valMesa['NRCOMANDA'];

		    // Retorna todas as mesas de um agrupamento.
		    $r_groupedTables = $this->entityManager->getConnection()->fetchAll("SQL_GET_GROUPED_TABLES", array($valMesa['NRMESA']));
		    // Formata as mesas.
		    $groupedTables = array();
		    foreach ($r_groupedTables as $table){
			    $temp = array(
			  	    'NRCOMANDA' => $table['NRCOMANDA'],
			  	 	'NRVENDAREST' => $table['NRVENDAREST']
			    );
			    array_push($groupedTables, $temp);
		  }
		}

		if (Empty($groupedTables)){
			$stComandaVens = "_".$stNrComanda."_";
		}
		else {
			$stComandaVens = "_";
			foreach ($groupedTables as $mesa){
				$dadosMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $mesa['NRCOMANDA'], $mesa['NRVENDAREST']);
				$stComandaVens .= $dadosMesa['NRCOMANDA']."_";
			}
		}

		if ($impPosicao == '') $impPosicao = 'T';

		if(is_array($impPosicao)){
			if (empty($impPosicao)){
				$arrayPosicoes[0] = 'T';
				$impPosicao = "T";
			} else {
				$arrayPosicoes = $impPosicao;
				$impPosicao = implode(",", $arrayPosicoes);
			}
		} else {
			 if ($impPosicao == '')
				$impPosicao = 'T';
				$arrayPosicoes = array($impPosicao);
		}

		$stPosicoes = $impPosicao;
		if($impPosicao != "T"){
			$stPosicoes = "_";
			foreach ($arrayPosicoes as $posicao) {
				$stPosicoes .= $posicao . "_";
			}
		}

		$result = array();
		$params = array(
			$NRCONFTELA,
			$session['CDFILIAL'],
			$NRCONFTELA,
			$session['CDFILIAL'],
			$stComandaVens,
			$stPosicoes,
			$impPosicao,
			$NRCONFTELA,
			$session['CDFILIAL'],
			$NRCONFTELA,
			$session['CDFILIAL'],
			$stComandaVens,
			$stPosicoes,
			$impPosicao,
			$session['CDFILIAL'],
			$stComandaVens,
			$stPosicoes,
			$impPosicao
		);
		$r_ItensDetalhados = $this->entityManager->getConnection()->fetchAll("SQL_ITENS_DETALHADOS", $params);
        $r_ItensDetalhados = $this->precoAPI->subgroupDiscountTableInterface($r_ItensDetalhados, $session['CDFILIAL'], $session['CDLOJA']);

		if (empty($r_ItensDetalhados)){
			if ($dataset['mode'] == 'M') return array('funcao' => '0', 'error' => '006'); // Não foi realizado nenhum pedido para esta mesa/comanda.
			else return array('funcao' => '0', 'error' => '430');
		}
		$listaItens = array();
		foreach($r_ItensDetalhados as &$item) {
			if ($item['CDPRODUTO'] != $session['CDPRODCOUVER']) {

				if (empty($item['GRUPO'])) {
					$item['GRUPO'] = 'ACRESCIMOS';
				}

				$listaItens[$item['NRCOMANDA'] . $item['NRPRODCOMVEN']] = array(
					'DSBUTTON'       => $item['DSBUTTON'],
					'NRVENDAREST'    => $item['NRVENDAREST'],
					'nrcomanda'      => $item['NRCOMANDA'],
					'NRPRODCOMVEN'   => $item['NRPRODCOMVEN'],
					'CDPRODPROMOCAO' => $item['CDPRODPROMOCAO'],
					'NRSEQPRODCOM'   => $item['NRSEQPRODCOM'],
					'NRSEQPRODCUP'   => $item['NRSEQPRODCUP'],
					'TXPRODCOMVEN'   => $item['TXPRODCOMVEN'],
					'GRUPO'          => $item['GRUPO'],
					'codigo'         => $item['CDPRODUTO'],
					'quantidade'     => number_format($item['QTPRODCOMVEN'], 3, ',', ''),
					'preco'          => number_format(floatval(bcdiv(str_replace(',','.',strval(((($item['VRPRECCOMVEN'] + $item['VRPRECCLCOMVEN']) * $item['QTPRODCOMVEN']) + $item['VRACRCOMVEN']))), '1', '2')),2,',',''),
					'QTPRODCOMVEN'   => floatval($item['QTPRODCOMVEN']),
					'VRPRECCOMVEN'   => floatval($item['VRPRECCOMVEN'] + $item['VRPRECCLCOMVEN']),
					'desconto'       => number_format($item['VRDESCCOMVEN'], 2, ',', ''),
					'posicao'        => $item['NRLUGARMESA'],
					'composicao'     => null,
					'DTHRINCOMVEN'   => substr($item['HORA'],0,5).' - '.substr($item['DATA'],0,5),
					'IDDIVIDECONTA'  => $item['IDDIVIDECONTA'],
					'NRPRODORIG'     => $item['NRPRODORIG'],
					'NRMESA'         => $item['NRMESA'],
                    'toGoText'       => !empty($item['IDORIGEMVENDA']) ? 'PARA VIAGEM' : '',
                    'NMVENDEDOR'	 => isset($item['NMVENDEDOR']) ? $item['NMVENDEDOR'] : ''
				);
				if ($item['CDPRODPROMOCAO']){
					$listaItens[$item['NRCOMANDA'] . $item['NRPRODCOMVEN']]['composicao'] = $this->buscaComposicao($item['CDFILIAL'], $item['NRVENDAREST'], $item['NRCOMANDA'], $item['NRPRODCOMVEN'], $item['NRSEQPRODCOM']);
				}
			}

		}

		$itens = Array();
		foreach ($listaItens as $item) {
			$item['POS'] = intval($item['posicao']); //Required in order to group items by position.
			$item['posicao'] = 'posição ' . (string)intval($item['posicao']);
			$item['preco'] = '' . $item['preco'];

			array_push($itens, $item);
		};
		$itens['funcao'] = '1';
		return $itens;
	}

	private function buscaComposicao($filial, $nrVendaRest, $nrComanda, $nrProdComVen, $nrSeqProdCom){
		$params = array(
			$filial,
			$nrVendaRest,
			$nrComanda,
			$nrProdComVen
		);
		return $this->entityManager->getConnection()->fetchAll("SQL_ITENS_ITCOMANDAEST", $params);
	}


	public function divideProdutos($dataset){
		$connection = null;

		try {
			$connection = $this->entityManager->getConnection();
	        $connection->beginTransaction();

			$session      = $this->util->getSessionVars($dataset['chave']);
			$CDFILIAL     = $session['CDFILIAL'];
			$NRCONFTELA   = $session['NRCONFTELA'];
			$NRVENDAREST  = $dataset["NRVENDAREST"];
			$NRCOMANDA    = $dataset["NRCOMANDA"];
			$NRPRODCOMVEN = $dataset["NRPRODCOMVEN"];
			$NRLUGARMESA  = $dataset["NRLUGARMESA"];

			$NRCOMANDA = $NRCOMANDA[0];
			$NRVENDAREST = $NRVENDAREST[0];

			$numberOfParts = count($NRLUGARMESA);

			for ($index = 0; $index < count($NRPRODCOMVEN); $index++) {

				$params = array(
					$NRCONFTELA,
					$CDFILIAL,
					$NRCONFTELA,
					$CDFILIAL,
					$NRCOMANDA,
					$NRVENDAREST,
					$NRPRODCOMVEN[$index],
					$NRCONFTELA,
					$CDFILIAL,
					$NRCONFTELA,
					$CDFILIAL,
					$NRCOMANDA,
					$NRVENDAREST,
					$NRPRODCOMVEN[$index]
				);
				$itemToSplit = $this->entityManager->getConnection()->fetchAssoc('SQL_ITENS_COMANDA', $params);

				$params = array(
					$CDFILIAL,
					$NRVENDAREST,
					$NRCOMANDA,
					$NRPRODCOMVEN[$index]
				);
				$this->entityManager->getConnection()->executeQuery('SQL_INSERE_ITEM_COMANDA_ORIG', $params);

				$itemsToInsert = array();
				$adicionalItem = 0;

				for ($pos = 0; $pos < $numberOfParts; $pos++) {

					$posicao = $NRLUGARMESA[$pos];
					$this->util->newCode('ITCOMANDAVEN'.$CDFILIAL.$NRCOMANDA);
					$NRPRODCOMVENNOVO = $this->util->getNewCode('ITCOMANDAVEN'.$CDFILIAL.$NRCOMANDA , 6);

					$itemDesc = $this->trunc($itemToSplit['VRDESCCOMVEN'] / $numberOfParts, 2);
					$itemAcr = $this->trunc($itemToSplit['VRACRCOMVEN'] / $numberOfParts, 2);
					$itemQuantity = $this->trunc($itemToSplit['QTPRODCOMVEN'] / $numberOfParts, 3);

					if ($pos == $numberOfParts - 1) {
						$multipliedItem = $this->trunc($itemToSplit['VRPRECCOMVEN'] * $itemQuantity, 2);
						$itemCalculatedValue = $multipliedItem * ($numberOfParts - 1);
						$itemQuantity = $this->adjustValueIfNecessary($itemQuantity, $numberOfParts, $itemToSplit['QTPRODCOMVEN'], 3);
						$multipliedItem = $this->trunc($itemToSplit['VRPRECCOMVEN'] * $itemQuantity, 2);
						$itemCalculatedValue += $multipliedItem;

						$itemDesc = $this->adjustValueIfNecessary($itemDesc, $numberOfParts, $itemToSplit['VRDESCCOMVEN'], 2);
						$itemAcr = $this->adjustValueIfNecessary($itemAcr, $numberOfParts, $itemToSplit['VRACRCOMVEN'], 2);

						$itemOriginalValue = $this->trunc($itemToSplit['VRPRECCOMVEN'] * $itemToSplit['QTPRODCOMVEN'], 2);
						if ($itemCalculatedValue != $itemOriginalValue) {
							$adicionalItem = intval(($itemOriginalValue - $itemCalculatedValue) * 100);
						}
					}

					$currentItem = array(
						$NRPRODCOMVENNOVO,
						$itemQuantity,
						$itemDesc,
						$posicao,
						$itemAcr,
						$NRPRODCOMVEN[$index],
						$CDFILIAL,
						$NRVENDAREST,
						$NRCOMANDA,
						$NRPRODCOMVEN[$index]
					);
					$this->entityManager->getConnection()->executeQuery('SQL_INSERE_ITEM_POSICAO', $currentItem);
				}

				$params = array(
					$CDFILIAL,
					$NRVENDAREST,
					$NRCOMANDA,
					$NRPRODCOMVEN[$index]
				);
				$this->entityManager->getConnection()->executeQuery('SQL_DELETA_PRODUTO_ORIGINAL', $params);

				$params = array(
					$CDFILIAL,
					$NRVENDAREST,
					$NRCOMANDA,
					$NRPRODCOMVEN[$index]
				);

				$values = $this->entityManager->getConnection()->fetchAll('SQL_VALIDA_VALORES', $params);

				foreach ($values as $value) {
					if (round($value['VRTOTAL'], 2) <= 0) {

						$this->entityManager->getConnection()->executeQuery('SQL_DELETA_PRODUTO_ORIGINAL', $params);

						$params = array(
							'CDFILIAL' => $CDFILIAL,
							'NRVENDAREST' => $NRVENDAREST,
							'NRCOMANDA' => $NRCOMANDA,
							'NRPRODCOMVEN' => $NRPRODCOMVEN[$index],
							'IDPRODPRODUZ' => null
						);

						$this->entityManager->getConnection()->executeQuery('SQL_INSERE_ITEM_COMANDA', $params);
						$this->entityManager->getConnection()->executeQuery('SQL_DELETA_CMD_VEN_ORIG', $params);
					}
				}
			}

			$connection->commit();
			return array('funcao' => '1');

		} catch(\Exception $e){
			Exception::logException($e);
			if ($connection != null) {
            	$connection->rollback();
        	}
			throw new \Exception ($e->getMessage(), 1);
		}
	}

	private function adjustValueIfNecessary($valueToAdjust, $numberOfParts, $totalValue, $handleDecimalValue) {
		$totalCalculatedQuantity = $valueToAdjust * $numberOfParts;
		if ($totalCalculatedQuantity != $totalValue) {
			$valueToAdjust += round($totalValue - $totalCalculatedQuantity, $handleDecimalValue);
		}
		return $valueToAdjust;
	}

	private function trunc($value, $decimals = 2) {
		return floatval(bcdiv(str_replace(',','.',strval($value)), '1', str_replace(',','.',strval($decimals))));
	}

	public function fechaContaMesa($dataset){ //Detaset Params: consumacao, couvert, mesa, pessoas, servico, valorConsumacao
		$connection = null;
		try {
			$session = $this->util->getSessionVars($dataset['chave']);
			/* Open connection and begin transaction. */
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$modo = $dataset['modo'];

			if ($modo == 'C') {
				// valida e busca dados da comanda
				$valComanda = $this->billService->dadosComanda($session['CDFILIAL'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST'], $session['CDLOJA']);
				$valComanda['NRMESA'] = str_pad($valComanda['NRMESA'], 4, '0', STR_PAD_LEFT); // ajusta

				$NRMESA = $valComanda['NRMESA'];

				$comandas = array();
				array_push($comandas, $valComanda);

			} else if ($modo == 'M') {
				// valida e busca dados da mesa
				$valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST']);
				$valMesa['NRMESA'] = str_pad($valMesa['NRMESA'], 4, '0', STR_PAD_LEFT);

				$NRMESA = $valMesa['NRMESA'];

				$comandas = $this->billService->buscaComandas($dataset['chave'], $valMesa['NRMESA']);
			}

			if ($dataset['consumacao'] && $session['IDCONSUMAMIN'] === 'S') {
				$this->tableService->controlaConsumacao($dataset['chave'], $dataset['NRVENDAREST'], $dataset['NRCOMANDA'], 'I');
			}

			if ($session['IDCOUVERART'] === 'S'){
				if ($dataset['couvert']) {
					$this->tableService->controlaCouvert($dataset['chave'], $dataset['NRVENDAREST'], $dataset['NRCOMANDA'], 'I', $modo);
					$IDUTILCOUVERT = 'S';
				} else {
					$this->tableService->controlaCouvert($dataset['chave'], $dataset['NRVENDAREST'], $dataset['NRCOMANDA'], 'R', $modo);
					$IDUTILCOUVERT = 'N';
				}
				$this->utilCouvert($session['CDFILIAL'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST'], $IDUTILCOUVERT, $modo);
			}

			$DTHRMESAFECH = new \DateTime();
			foreach ($comandas as $comanda){
				if ($modo == 'M') {
					// altera status da comanda para 2 (conta solicitada)
					$params = array(
						'2',
						$session['CDFILIAL'],
						$comanda['NRCOMANDA'],
						$comanda['NRVENDAREST'],
					);
					$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_STATUS_COMANDA", $params);
					// atualiza horário de fechamento da mesa
					$params = array(
						'DTHRMESAFECH' => $DTHRMESAFECH,
						'CDFILIAL' => $session['CDFILIAL'],
						'NRVENDAREST' => $comanda['NRVENDAREST'],
						'NRMESA' => $comanda['NRMESA']
					);
					$type = array(
						'DTHRMESAFECH' => \Doctrine\DBAL\Types\Type::DATETIME
					);
					$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_HR_FECHAMENTO_MESA", $params, $type);

					// altera status da mesa para S (conta solicitada)
					$params = array(
						'IDSTMESAAUX' => 'S',
						'CDFILIAL'    => $session['CDFILIAL'],
						'CDLOJA'      => $session['CDLOJA'],
						'NRMESA'      => $comanda['NRMESA']
					);
					$this->entityManager->getConnection()->executeQuery('SQL_ALTERA_STATUS_MESA', $params);
				}
				// Executa o cálculo de taxa de servico da mesa/comanda
				$this->alteraTaxaServico($session['VRCOMISVENDA'], $session['CDFILIAL'], $comanda['NRVENDAREST'], $comanda['NRCOMANDA'], $dataset['txporcentservico'], $dataset['servico']);
			}

			$parcialData = array(
				'chave'       => $dataset['chave'],
				'mode'        => $modo,
				'NRCOMANDA'   => $dataset['NRCOMANDA'],
				'NRVENDAREST' => $dataset['NRVENDAREST'],
				'funcao'      => $dataset['IMPRIMEPARCIAL'],
				'agrupamento' => array(),
				'posicao'     => null
			);

			$dadosParcial = $this->dadosParcial($parcialData);
			//Altera VRCOMISVENDE na tabela Comandaven quando taxa de servico e alterada na opcao Alterar Taxa na tela de fechamento de conta.
			$taxadeservicoParcial = str_replace(",", ".", $dadosParcial['servico']);
			$params = array(
				floatval($taxadeservicoParcial),
				$session['CDFILIAL'],
				$dataset['NRVENDAREST'],
				$dataset['NRCOMANDA']
			);
			$this->entityManager->getConnection()->executeQuery("UPDATE_COMISSAO_VENDA", $params);

			if ($modo === 'M') {
				$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'FEC_MES', $session['CDOPERADOR'], null, "Waiter - Fechamento de conta", "Fechamento de conta da mesa " . $NRMESA . ".");
			}
			if ($connection !=null) {
				$connection->commit();
			}
			$paramsImpressora = array();
			if(isset($dadosParcial['dadosImpressao']['paramsImpressora'])){
				$paramsImpressora = $dadosParcial['dadosImpressao']['paramsImpressora'];
				$dadosParcial['dadosImpressao'] = '';
			}
			return array(
				'funcao' => '1',
				'dadosImpressao' => $dadosParcial['dadosImpressao'],
				'paramsImpressora' => $paramsImpressora
			);
		} catch (\Exception $e) {
			Exception::logException($e);
			if ($connection !=null)	$connection->rollback();
			throw new \Exception ($e->getMessage(),1);
		}
	}

	private function alteraTaxaServico($VRCOMISVENDA, $CDFILIAL, $NRVENDAREST, $NRCOMANDA, $TAXA, $SERVICO){
		if($SERVICO){
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'NRVENDAREST' => $NRVENDAREST,
				'NRCOMANDA' => $NRCOMANDA
			);

	    	$dadosComissao = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_COMISSAO_VENDA", $params);
	    	$VRCOMISVENDE = $dadosComissao['VRCOMISVENDE'];

	    	if(!isset($TAXA)) {
		    	$TAXA = empty(floatval($dadosComissao["VRCOMISPOR"])) ? $VRCOMISVENDA : floatval($dadosComissao["VRCOMISPOR"]);
		    } else {
		    	$TAXA = floatval($TAXA);
		    }
		} else {
			$TAXA = 0;
			$VRCOMISVENDE = 0;
		}

		$params = array(
			'VRCOMISPOR'   => $TAXA,
			'VRCOMISVENDE' => $VRCOMISVENDE,
			'CDFILIAL'     => $CDFILIAL,
			'NRVENDAREST'  => $NRVENDAREST,
			'NRCOMANDA'    => $NRCOMANDA
		);

		$this->entityManager->getConnection()->executeQuery('UPDATE_PORCENTAGEM_COMISSAO', $params);
	}

	private function checkEmptyAndGetDefaultValue($paramName, $transactionObject, $newValue) {
		$valueToUpdate = $newValue;
		if (!isset($valueToUpdate)) {
			$valueToUpdate = $transactionObject[$paramName];
		}
		return $valueToUpdate;
	}

	public function finalizaPagamento($dataset){
		$connection = null;
        try {
        	$connection->beginTransaction();

            $NRSEQMOVMOB  = $dataset['NRSEQMOVMOB'];
            $NRSEQMOB     = $dataset['NRSEQMOB'];
            $DSBANDEIRA   = $dataset['DSBANDEIRA'];
            $NRADMCODE    = $dataset['NRADMCODE'];
            $IDADMTASK    = $dataset['IDADMTASK'];
            $IDSTMOV      = $dataset['IDSTMOV'];
            $TXMOVUSUARIO = $dataset['TXMOVUSUARIO'];
            $TXMOVJSON    = $dataset['TXMOVJSON'];
            $CDNSUTEFMOB  = $dataset['CDNSUTEFMOB'];
            $TXPRIMVIATEF = $dataset['TXPRIMVIATEF'];
            $TXSEGVIATEF  = $dataset['TXSEGVIATEF'];
            $params = array($NRSEQMOVMOB);
            $transactionObject = $this->entityManager->getConnection()->fetchAssoc('SQL_BUSCA_TRANSACOES', $params);
            if (empty($transactionObject)) {
            	throw new \Exception("Transação não encontrada", 1);
            }

            $params = array(
                "NRSEQMOB" => self::checkEmptyAndGetDefaultValue('NRSEQMOB', $transactionObject, $NRSEQMOB),
                "NRADMCODE" => self::checkEmptyAndGetDefaultValue('NRADMCODE', $transactionObject, $NRADMCODE),
                "IDADMTASK" => self::checkEmptyAndGetDefaultValue('IDADMTASK', $transactionObject, $IDADMTASK),
                "IDSTMOV" => self::checkEmptyAndGetDefaultValue('IDSTMOV', $transactionObject, $IDSTMOV),
                "TXMOVUSUARIO" => self::checkEmptyAndGetDefaultValue('TXMOVUSUARIO', $transactionObject, $TXMOVUSUARIO),
                "TXMOVJSON" => self::checkEmptyAndGetDefaultValue('TXMOVJSON', $transactionObject, $TXMOVJSON),
                "CDNSUTEFMOB" => self::checkEmptyAndGetDefaultValue('CDNSUTEFMOB', $transactionObject, $CDNSUTEFMOB),
                "TXPRIMVIATEF" => self::checkEmptyAndGetDefaultValue('TXPRIMVIATEF', $transactionObject, $TXPRIMVIATEF),
                "TXSEGVIATEF" => self::checkEmptyAndGetDefaultValue('TXSEGVIATEF', $transactionObject, $TXSEGVIATEF),
                "NRSEQMOVMOB" => $NRSEQMOVMOB
            );
            $this->entityManager->getConnection()->executeQuery('SQL_UPDATE_MOVCAIXAMOB', $params);

            // Busca o tipo de recebimento correto, caso exista.
            $CDTIPORECE = $this->entityManager->getConnection()->fetchAssoc('SQL_GET_TIPORECE', array($DSBANDEIRA));
            if (!empty($CDTIPORECE)) {
                $this->entityManager->getConnection()->executeQuery('SQL_UPDATE_MOVCAIXAMOB_CDTIPORECE', array($CDTIPORECE['CDTIPORECE'], $NRSEQMOVMOB));
            }

            $params = array($NRSEQMOVMOB);
            $retorno = $this->entityManager->getConnection()->fetchAll('SQL_BUSCA_TRANSACOES', $params);

            $OLD_DSBANDEIRA = $retorno[0]['DSBANDEIRA'];

            $params = array(
                $DSBANDEIRA,
                $NRSEQMOVMOB
            );
            if ($OLD_DSBANDEIRA == null) {
                $this->entityManager->getConnection()->executeQuery('SQL_UPDATE_DSBANDEIRA', $params);
            }

            $connection->commit();

            return $retorno;
        } catch (\Exception $e){
        	Exception::logException($e);
        	if ($connection != null) $connection->rollback();
            throw new \Exception ($e->getMessage(), 1);
        }
	}

	public function inicializaPagamento($dataset){
		$connection = null;
		try {
			$session = $this->util->getSessionVars($dataset['chave']);

			$cdfilial     = $session['CDFILIAL'];
			$cdcaixa      = $session['CDCAIXA'];
			$CDVENDEDOR   = $dataset['CDVENDEDOR'] == null ? $session['CDVENDEDOR'] : $dataset['CDVENDEDOR'];
			$NRVENDAREST  = $dataset['NRVENDAREST'];
			$NRCOMANDA    = $dataset['NRCOMANDA'];
			$NRMESA       = $dataset['NRMESA'];
			$NRLUGARMESA  = $dataset['NRLUGARMESA'];
			$CDTIPORECE   = $dataset['CDTIPORECE'];
			$IDTIPMOV     = $dataset['IDTIPMOV'];
			$VRMOV        = $dataset['VRMOV'];
			$DSBANDEIRA   = $dataset['DSBANDEIRA'];
			$IDTPTEF      = $dataset['IDTPTEF'];
			$IDATIVO      = 'S';


			$this->util->newCode('MOVCAIXAMOB'.$cdfilial);
			$NRSEQMOVMOB = $this->util->getNewCode('MOVCAIXAMOB'.$cdfilial, 10);

			$NRSEQVENDA = null; // Modificar quando implementar a baixa na mesa ou posição // Update quando atualizar o MOVCAIXA

			$params = array(
				$NRSEQMOVMOB,
				$cdfilial,
				$cdcaixa,
				$CDVENDEDOR,
				$NRVENDAREST,
				$NRCOMANDA,
				$NRSEQVENDA,
				$NRMESA,
				$NRLUGARMESA,
				$CDTIPORECE,
				'E', // $IDTIPMOV
				$VRMOV,
				$DSBANDEIRA,
				$IDTPTEF,
				$IDATIVO
			);

			$this->entityManager->getConnection()->executeQuery("SQL_INSERT_MOVCAIXAMOB", $params);

			$params = array($NRSEQMOVMOB);
			return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			if ($connection != null) $connection->rollback();
			throw new \Exception ($e->getMessage(), 1);
		}
	}

	//tambem imprime
	public function dadosParcial($dataset){
		$connection = null;
		try {
			$session = $this->util->getSessionVars($dataset['chave']);
			$modo        = $dataset["mode"];
			$NRCOMANDA   = $dataset['NRCOMANDA'];
			$NRVENDAREST = $dataset['NRVENDAREST'];
			$tipo        = $dataset["funcao"];
			$cdfilial    = $session["CDFILIAL"];
			$cdloja      = $session["CDLOJA"];

			$nrvendarest = $nrcomanda = $nrpessoas = $dscomanda = $nrmesa = 0;
			$impPosicao  = $dataset['posicao'];
			if ($modo === "C") {
				$dadosComanda = $this->billService->dadosComanda($cdfilial, $NRCOMANDA, $NRVENDAREST, $cdloja);
				if ($this->validaComanda($dataset['chave'], $NRCOMANDA, $NRVENDAREST) === false) {
					return array('funcao' => '0', 'error' => '004');
				}
				$nrvendarest  = $dadosComanda["NRVENDAREST"];
				$nrcomanda    = $dadosComanda["NRCOMANDA"];
				$nrpessoas    = '1';
				$dscomanda    = $dadosComanda["DSCOMANDA"];
				$mesaComanda = null;
				$nrmesa = $this->util->zeroFill($dadosComanda["NRMESA"], 4);
				$IDSTMESAAUX = 'O';
				$VRDESCFID = $dadosComanda['VRDESCFID'];
				$NMVENDEDORABERT = "";
			} else if ($modo === "M") {
				$dadosMesa = $this->tableService->dadosMesa($cdfilial, $cdloja, $NRCOMANDA, $NRVENDAREST);
				$nrvendarest		= $dadosMesa["NRVENDAREST"];
				$nrcomanda			= $dadosMesa["NRCOMANDA"];
				$nrpessoas			= $dadosMesa["NRPESMESAVEN"];
				$dscomanda			= "";
				$mesaComanda		= $dadosMesa['NRMESA'];
				$nrmesa				= $mesaComanda;
				$IDSTMESAAUX		= $dadosMesa['IDSTMESAAUX'];
				$VRDESCFID			= $dadosMesa['VRDESCFID'];
				$NMVENDEDORABERT	= $dadosMesa["NMVENDEDORABERT"];
				$params = array($cdfilial, $cdloja, $mesaComanda);
				$r_ambiente = $this->entityManager->getConnection()->fetchAssoc("SQL_AMBIENTE", $params);
				if ($r_ambiente) {
					$ambiente = $r_ambiente["NMSALA"];
				}
			}

			// Retorna todas as mesas de um agrupamento, caso a mesa esteja agrupada.
			$r_groupedTables = $this->entityManager->getConnection()->fetchAll("SQL_GET_GROUPED_TABLES", array($mesaComanda));
			if (empty($r_groupedTables)) {
				// Mesa não está agrupada.
				$stComandaVens = "_".$nrcomanda."_";
				$stVendaRests  = "_".$nrvendarest."_";
			}
			else {
				// Mesa está agrupada.
				$stComandaVens = "_";
				$stVendaRests  = "_";
				$nrpessoas = 0;

				foreach ($r_groupedTables as $table){
					$dadosMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $table['NRCOMANDA'], $table['NRVENDAREST']);
					$stComandaVens .= $dadosMesa['NRCOMANDA']."_";
					$stVendaRests  .= $dadosMesa['NRVENDAREST']."_";
					$nrpessoas += $dadosMesa['NRPESMESAVEN'];
				}
			}

			//Se não for uma posição específica, muda para pegar todas.
			if (is_array($impPosicao)){
				if (empty($impPosicao)){
					$arrayPosicoes[0] = 'T';
					$impPosicao = "T";
				} else {
					$arrayPosicoes = $impPosicao;
					$impPosicao = implode(",", $arrayPosicoes);
				}
			} else {
				if ($impPosicao == '') {
					$impPosicao = 'T';
				}
				$arrayPosicoes = array($impPosicao);
			}

			$stPosicoes = $impPosicao;
			if ($impPosicao != "T") {
				$stPosicoes = "_";
				foreach ($arrayPosicoes as $posicao) {
					$stPosicoes .= $posicao . "_";
				}
			}

			$params = array(
				$cdfilial,
				$stComandaVens,
				$stPosicoes,
				$impPosicao,
				$cdfilial,
				$stComandaVens,
				$stPosicoes,
				$impPosicao
			);
			$produtosParcial = $this->entityManager->getConnection()->fetchAll("SQL_PRODUTOS_PARCIAL", $params);
            $produtosParcial = $this->precoAPI->subgroupDiscountTableInterface($produtosParcial, $session['CDFILIAL'], $session['CDLOJA']);

			$params = array(
				$cdfilial,
				$stVendaRests,
				$stComandaVens,
				$stPosicoes,
				$impPosicao
			);
			$numeroProdutos = $this->entityManager->getConnection()->fetchAssoc("SQL_PRODUTOS_PARCIAL_NUMERO_PRODUTOS", $params);
			$numeroProdutos = $numeroProdutos['NRITENS'];

			if (!is_array($impPosicao)){
				if ($impPosicao == 'T') {
					$impPosicao = '';
				}
			}

			$cdprodcouver = $session["CDPRODCOUVER"];
			$cdprodconsum = $session["CDPRODCONSUM"];

			$totalProdutosTaxa = 0;
			$totalProdutosTaxaDesc = 0;
			$totalSemDesconto = 0;
			$totalDesconto = 0;
            $totalSubsidy = 0;
			self::calculaValoresParcial($cdfilial, $produtosParcial, $cdprodcouver, $cdprodconsum, $totalProdutosTaxa, $totalProdutosTaxaDesc, $totalSemDesconto, $totalDesconto, $totalSubsidy);
			$totalSemDesconto = round($totalSemDesconto, 2);
			$totalProdutosTaxa = round(round($totalProdutosTaxa, 2) - $totalProdutosTaxaDesc, 2);
			$total = round($totalSemDesconto - $totalDesconto, 2);

			$fidelityValue = 0;
			$fidelityDiscount = 0;
			// calcula valores do Crédito Fidelidade
			self::adjustDiscountFidelity($cdfilial, $nrvendarest, $produtosParcial, $cdprodcouver, $cdprodconsum, $total, $numeroProdutos, $arrayPosicoes, $VRDESCFID, $fidelityValue, $fidelityDiscount, $totalProdutosTaxa, $totalSubsidy);

			/* Open connection and begin transaction. */
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			// calcula consumação e couvert com array indexado pela posição com o valor referente
			//Parametro 'C' significa Consulta
			$consumacao = 0;
			if ($session['IDCONSUMAMIN'] === "S"){
				$valoresConsumacao = $this->tableService->controlaConsumacao($dataset['chave'], $nrvendarest, $nrcomanda, 'C');
				if ($arrayPosicoes[0] === 'T') {
					$consumacao += array_sum($valoresConsumacao);
				} else {
					foreach($arrayPosicoes as $posicao) {
						$consumacao += $valoresConsumacao[$posicao];
					}
				}
			}
			$couvert = 0;
			if ($session['IDCOUVERART'] === 'S') {
				$valoresCouvert = $this->tableService->controlaCouvert($dataset['chave'], $nrvendarest, $nrcomanda, 'C', $modo);

				if (!empty($valoresCouvert)){
					if ($arrayPosicoes[0] === 'T') {
						$couvert += array_sum($valoresCouvert);
					} else {
						foreach($arrayPosicoes as $posicao) {
							$couvert += $valoresCouvert[$posicao];
						}
					}
				}
			}
			$total += $consumacao + $couvert;

			// calcula descontos e acréscimos para a comanda
			if ($modo === "C") {
				$dadosComanda = $this->billService->dadosComanda($cdfilial, $nrcomanda, $nrvendarest, $cdloja);
				if ($dadosComanda["VRDESCOMANDA"] > 0) {
					$total -= $dadosComanda["VRDESCOMANDA"];
				}
				$total += $dadosComanda["VRACRCOMANDA"];
			}

			// calcula taxa de serviço
			$taxaDeServico = self::taxaDeServico($dataset['chave'], $totalProdutosTaxa, $cdfilial, $nrvendarest, $nrcomanda, $IDSTMESAAUX, $produtosParcial);
			$total += $taxaDeServico;

			// calcula tempo de permanência
			$tempoPermanencia = self::calculaTempoPermanencia($cdfilial, $nrvendarest, $nrcomanda);
			$horas            = $tempoPermanencia["horas"];
			$minutos          = $tempoPermanencia["minutos"];
			$segundos         = $tempoPermanencia["segundos"];

			$total = round($total - $fidelityValue, 2);
			// calcula total por pessoa
			$totalPorPessoa = floatval($total) / floatval($nrpessoas);
			if ($connection !=null) {
				$connection->commit();
				$connection = null;
			}

			$dadosImpressao = '';
			// monta arquivo de impressão
			if ($tipo === "I"){
				$this->impressaoService->imprimeParcial($produtosParcial ,$dataset['chave'], $cdfilial, $stVendaRests,
				 $stComandaVens, $cdprodcouver, $modo, $nrvendarest, $nrcomanda, $cdloja, $nrmesa, $impPosicao, $nrpessoas,
				 $totalSemDesconto, $totalDesconto, $taxaDeServico, $total, $totalPorPessoa, $horas, $minutos,
				 $segundos, $dscomanda, $dadosImpressao, $fidelityValue, $couvert);
			}

			// calcula valor já pago
			$valorPago = self::valorPago($dataset, $session, $NRVENDAREST, $impPosicao, $nrcomanda);
			if ($session['IDCOLETOR'] === 'C'){
				if ($valorPago >= $total) {
					$valorPago = $total;
					$total = 0;
				} else {
					$total = round($total - $valorPago, 2);
				}
			}

            $realSubsidy = 0;
            foreach ($produtosParcial as $produto){
                $VRPRECCOMVEN = floatval($produto['VRPRECCOMVEN']);
                $VRPRECCLCOMVEN = floatval($produto['VRPRECCLCOMVEN']);
                $DISCOUNT = floatval(bcdiv(str_replace(',','.',strval($produto['VRDESCCOMVEN'])), str_replace(',','.',strval($produto['QTPRODCOMVEN'])), '2'));

                $productSubsidy = self::calculateSubsidy($VRPRECCOMVEN, $VRPRECCLCOMVEN, $DISCOUNT);
                $realSubsidy += floatval(bcmul(str_replace(',','.',strval($produto['QTPRODCOMVEN'])), str_replace(',','.',strval($productSubsidy)), '2'));
			}

			return array(
				"pessoas"          		=> $nrpessoas,
				"posicao"          		=> $dataset['posicao'],
				"permanencia"      		=> $horas . $minutos,
				"produtos"         		=> $this->util->formataPreco($totalSemDesconto),
				"desconto"         		=> $this->util->formataPreco($totalDesconto),
				"lblDesconto"	   		=> $this->util->formataPreco($totalDesconto + $fidelityValue),
				"servico"          		=> $this->util->formataPreco($taxaDeServico),
				"couvert"          		=> $this->util->formataPreco($couvert),
				"consumacao"       		=> $this->util->formataPreco($consumacao),
				"total"            		=> $this->util->formataPreco($total),
				"valorPago"        		=> $this->util->formataPreco($valorPago),
                "totalSubsidy"     		=> $totalSubsidy,
                "realSubsidy"      		=> $realSubsidy,
				"funcao"           		=> '1',
				'numeroProdutos'   		=> $numeroProdutos,
				'dadosImpressao'   		=> $dadosImpressao,
				'fidelityDiscount' 		=> $fidelityDiscount,
				'fidelityValue'    		=> $fidelityValue,
				'vlrprodcobtaxa'   		=> $totalProdutosTaxa,
				'NMVENDEDORABERT'		=> isset($NMVENDEDORABERT) ? $NMVENDEDORABERT : ""
			);
		} catch(\Exception $e) {
			Exception::logException($e);
			if ($connection !=null) $connection->rollback();
			throw new \Exception ($e->getMessage(),1);
		}
	}

	private function validaComanda($chave, $NRCOMANDA, $NRVENDAREST){
		$session = $this->util->getSessionVars($chave);
		$params = array(
			$session["CDFILIAL"],
			$NRCOMANDA,
			$NRVENDAREST
		);
		return (bool)$this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_COMANDA", $params);
	}

	private function calculaValoresParcial($cdfilial, $produtosParcial, $cdprodcouver, $cdprodconsum, &$totalProdutosTaxa, &$totalProdutosTaxaDesc, &$totalSemDesconto, &$totalDesconto, &$totalSubsidy){

        $params = array(
            'CDFILIAL' => $cdfilial
        );
        $IDCONDESCTXSERV = $this->entityManager->getConnection()->fetchAssoc("SQL_FILIAL_DETAILS", $params)['IDCONDESCTXSERV'];

		foreach ($produtosParcial as $item){
			if ($item['CDPRODUTO'] != $cdprodcouver && $item['CDPRODUTO'] != $cdprodconsum) {
				$vrProd = floatval(bcmul(str_replace(',','.',strval($item['QTPRODCOMVEN'])), str_replace(',','.',strval($item['VRPRECCOMVEN'] + $item['VRPRECCLCOMVEN'])), 2)) + $item['VRACRCOMVEN'];
				if ($item['IDCOBTXSERV'] === 'S') {
                    $totalProdutosTaxa += $vrProd;
                    if ($IDCONDESCTXSERV === 'S' || $item['IDDESCMANUAL'] === 'N') {
                        $totalProdutosTaxaDesc += $item['VRDESCCOMVEN'];
                    }
				}
				$totalSemDesconto += $vrProd;
				$totalDesconto += $item['VRDESCCOMVEN'];
                $totalSubsidy += floatval(bcmul(str_replace(',','.',strval($item['QTPRODCOMVEN'])), str_replace(',','.',strval($item['VRPRECCLCOMVEN'])), 2));
			}
		}
	}

	private function valorPago($dataset, $session, $nrvendarest, $impPosicao, $nrcomanda){
		if ($session['IDCOLETOR'] !== 'C' || $impPosicao === "") {
			$transacoes = $this->transactionsService->buscaTransacoesMesa($session, $nrvendarest, $nrcomanda);
		} else {
			// apenas caixa com IDCOLETOR === 'C' controla o valor pago por posição
			$transacoes = $this->transactionsService->buscaTransacoesPosicao($session, $nrvendarest, $nrcomanda, $impPosicao);
		}

		// operação com os valores recebidos menos troco dado
		$valorPago = $transacoes['VALORPAGO'] - $transacoes['VALORRETIRADA'];

		return round($valorPago, 2);
	}

	private function taxaDeServico($chave, $totalProdutos, $cdfilial, $nrvendarest, $nrcomanda, $IDSTMESAAUX, $produtosParcial){
		$session = $this->util->getSessionVars($chave);
		$idcomisvenda = $session["IDCOMISVENDA"];
		$taxaDeServico = 0;
		$params = array(
			'CDFILIAL' => $cdfilial,
			'NRVENDAREST' => $nrvendarest,
			'NRCOMANDA' => $nrcomanda
		);
    	$dadosComissao = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_COMISSAO_VENDA", $params);

        if ($idcomisvenda === "S") {
           if (is_null($dadosComissao['VRCOMISPOR'])) {
            	$taxaCalculada = $this->util->truncate($totalProdutos * floatval($session["VRCOMISVENDA"])/100, 2);
        	} else {
        		$taxaCalculada = $this->util->truncate($totalProdutos * floatval($dadosComissao["VRCOMISPOR"])/100, 2);
        	}
        }
        else if ($idcomisvenda === "G"){
            $taxaCalculada = 0;
            foreach ($produtosParcial as $item){
                if ($item['CDPRODUTO'] != $session["CDPRODCOUVER"] && $item['CDPRODUTO'] != $session["CDPRODCONSUM"]){
                    $vrProd = round(floatval(bcmul(str_replace(',','.',strval($item['QTPRODCOMVEN'])), str_replace(',','.',strval(($item['VRPRECCOMVEN'] + $item['VRPRECCLCOMVEN']))), 2)) - $item['VRDESCCOMVEN'] + $item['VRACRCOMVEN'], 2);
                    if ($item['IDCOBTXSERV'] == 'S' && $item['IDVRINCIDECONTA'] == "S"){
                        $taxaCalculada += $this->util->truncate($vrProd * floatval($item['VRPERCCOMISVEND'])/100, 2);
                    }
                }
            }
        }

        if ($idcomisvenda !== "N"){
            // caso existir, retorna o valor calculado se for diferente de zero; se não, salva e retorna
            if ($IDSTMESAAUX !== 'O') {
				if (floatval($dadosComissao['VRCOMISVENDE']) != 0 || $dadosComissao['VRCOMISPOR'] > 0) {
					$taxaDeServico = $taxaCalculada;
				}
			} else {
				if ($taxaCalculada > 0) {
					$params = array(
						$taxaCalculada,
						$cdfilial,
						$nrvendarest,
						$nrcomanda
					);
					$this->entityManager->getConnection()->executeQuery("UPDATE_COMISSAO_VENDA", $params);
				}
				$taxaDeServico = $taxaCalculada;
			}
        }

		return $taxaDeServico;
	}

	private function calculaTempoPermanencia($cdfilial, $nrvendarest, $nrcomanda){
		$params = array($cdfilial, $nrvendarest, $nrcomanda);
		$tempoPermanencia = $this->entityManager->getConnection()->fetchAssoc("SQL_TEMPO_PERMANENCIA", $params);
		$now     = $this->date->getDataAtual();
		// @todo validar o formato de saida do campo "$tempoPermanencia["DATA"]".
		// Assume-se aqui que seu formato é d/m/Y H:i:s
		$entrada           = $this->date->getDataDeString($tempoPermanencia["DATA"], "Y-m-d H:i:s.u");
		$intervalo         = $entrada->diff($now);
		$intervaloHoras    = str_pad((int)$intervalo->format('%R%h'), 2, '0', STR_PAD_LEFT);
		$intervaloMinutos  = str_pad((int)$intervalo->format('%R%i'), 2, '0', STR_PAD_LEFT);
		$intervaloSegundos = str_pad((int)$intervalo->format('%R%s'), 2, '0', STR_PAD_LEFT);

		return array (
			"horas"    => $intervaloHoras,
			"minutos"  => $intervaloMinutos,
			"segundos" => $intervaloSegundos
		);
	}

	public function saleCancel($CODIGOCUPOM, $chave, $CDSUPERVISOR){
        $session = $this->util->getSessionVars($chave);
        $CDFILIAL = $session['CDFILIAL'];
        $CDCAIXA = $session['CDCAIXA'];
        $connection = $this->entityManager->getConnection();

		$venda = $this->getVendabyCodigoCupom($CDFILIAL, $CDCAIXA, $CODIGOCUPOM);

		$result = array(
			'error' => true,
			'message' => '',
			'data' => array(
				'dadosImpressao' => array(),
				'dataTEF' => array()
			)
		);

        try {
    		if (!empty($venda)){
    			$result['data']['dataTEF'] = self::getTransactions($venda['NRSEQVENDA'], $CDFILIAL, $CDCAIXA, $session['NRORG']);
                $connection->beginTransaction();
    			$resultCancel = $this->vendaAPI->cancelaCupom($CDFILIAL, $CDCAIXA, $session['NRORG'], $venda['NRSEQVENDA'], $session['CDOPERADOR'], $CDSUPERVISOR);
    			if (!$resultCancel['error']){
                    $connection->commit();
                    $result['error'] = false;
                    $result['message'] = 'Venda cancelada com sucesso.';
    				// formata mensagem de acordo com os erros do fluxo de cancelamento
    				if (isset($resultCancel['mensagemNfce'])){
    					$result['message'] .= ' <br><br> ' . $resultCancel['mensagemNfce'];
    				}
                    else if (isset($resultCancel['mensagemImpressao'])){
    					$result['message'] .= ' <br><br> ' . $resultCancel['mensagemImpressao'];
    				}
    				$result['data']['dadosImpressao'] = $resultCancel['dadosImpressao'];
    			} else{
                    if ($connection != null){
                        $connection->rollback();
                    }
    				$result = $resultCancel;
    			}
    		}
            else {
    			$result['message'] = 'Venda para este código de cupom não encontrada ou não relacionada a abertura deste caixa.';
    		}

    		return $result;
        } catch (\Exception $e){
            if ($connection != null){
                $connection->rollback();
            }
            $result['message'] = $e->getMessage();
            return $result;
        }
	}

	public function getVendabyCodigoCupom($CDFILIAL, $CDCAIXA, $NRNOTAFISCALCE){
		$venda = array();

		$DTABERCAIX = $this->registerService->getRegisterOpeningDate($CDFILIAL, $CDCAIXA);
		if (!empty($DTABERCAIX)){
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'CDCAIXA' => $CDCAIXA,
				'DTABERTUR' => $this->caixaAPI->convertToDateDB($DTABERCAIX['DTABERCAIX']),
				'NRNOTAFISCALCE' => $NRNOTAFISCALCE
			);
			$type = array(
                'DTABERTUR' => \Doctrine\DBAL\Types\Type::DATETIME
            );

			$venda = $this->entityManager->getConnection()->fetchAssoc('SQL_GET_VENDA_BY_NRNOTAFISCALCE', $params, $type);
		}

		return $venda;
	}

	public function getTransactions($NRSEQVENDA, $CDFILIAL, $CDCAIXA, $NRORG){
		$params = array(
			'NRSEQVENDA' => $NRSEQVENDA,
			'CDFILIAL' 	 => $CDFILIAL,
			'CDCAIXA' 	 => $CDCAIXA,
			'NRORG' 	 => $NRORG
		);

		$recebimentos = $this->entityManager->getConnection()->fetchAll('SQL_GET_MOVCAIXA_BY_NRSEQVENDA', $params);

		return array_filter($recebimentos, function($recebimento){
			return !empty($recebimento['NRCONTROLTEF']);
		});
	}

	public function changeProductDiscount($NRVENDAREST, $NRCOMANDA, $VRDESCONTO, $TIPODESCONTO, $NRPRODCOMVEN, $CDSUPERVISOR, $motivoDesconto, $CDGRPOCORDESC){
		$result = array(
			'error' => true,
			'message' => ''
		);
		$connection = null;

		try {
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$session  = $this->util->getSessionVars(null);
			$products = self::getITCOMANDAVEN($session, $NRVENDAREST, $NRCOMANDA, $NRPRODCOMVEN);
			$CDSUPERVISOR = !empty($CDSUPERVISOR) ? $CDSUPERVISOR : $session['CDOPERADOR'];
			if (!empty($CDGRPOCORDESC)){
				$params = array(
					'CDFILIAL' => $session['CDFILIAL'],
					'CDLOJA' => $session['CDLOJA']
				);
				$CDGRPOCORDESC = $this->entityManager->getConnection()->fetchAssoc("SQL_GRUPO_OBS_DESC", $params);
				$CDGRPOCORDESC = !empty($CDGRPOCORDESC['CDGRPOCORDESC']) ? $CDGRPOCORDESC['CDGRPOCORDESC'] : null;
			}

			foreach ($products as $product) {
				$prodPrice = floatval($product['VRPRECCOMVEN']);
                $prodSubsidy = floatval($product['VRPRECCLCOMVEN']);
				$currentDiscount = $TIPODESCONTO == 'V' ? $VRDESCONTO : $this->trunc(floatval(bcdiv(str_replace(',','.',strval($VRDESCONTO)), '100', '4')) * floatval(bcmul(str_replace(',','.',strval($product['QTPRODCOMVEN'])), str_replace(',','.',strval($prodPrice)), '2')), 2);

				$paramsUpdate = self::formatUpdateITCOMANDAVEN($product, $prodPrice, $prodSubsidy, $currentDiscount, floatval($product['VRACRCOMVEN']), $motivoDesconto, $CDGRPOCORDESC, 'S');

				$this->entityManager->getConnection()->executeQuery("UPDATE_PRICE_ON_ITCOMANDAVEN", $paramsUpdate);
				$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'CUP_DES', $session['CDOPERADOR'], $CDSUPERVISOR, 'Waiter - Cupom com desconto.', 'Desconto no cupom nº ' . $NRCOMANDA);
			}

			$connection->commit();

			$result['error'] = false;
		} catch (\Exception $e) {
			if ($connection != null){
				$connection->rollback();
			}

			$result['message'] = $e->getMessage();
		}

		return $result;
	}

	private function getITCOMANDAVEN($session, $NRVENDAREST, $NRCOMANDA, $NRPRODCOMVEN){
        $params = array(
            'CDFILIAL' => $session['CDFILIAL'],
            'NRORG' => $session['NRORG'],
            'NRVENDAREST' => $NRVENDAREST,
            'NRCOMANDA' => $NRCOMANDA,
            'NRPRODCOMVEN' => array_filter(array_unique($NRPRODCOMVEN))
        );
        $type = array(
            'NRPRODCOMVEN' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
        );

        return $this->entityManager->getConnection()->fetchAll("SQL_ITCOMANDAVEN_NRPRODCOMVEN", $params, $type);
    }

    private function checkClientConsumer($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR){
        if (!empty($CDCLIENTE) && empty($CDCONSUMIDOR)){
            $params = array(
                $CDFILIAL
            );
            $CLIENTE_PADRAO = $this->entityManager->getConnection()->fetchAssoc("SQL_GETCLIENTEPADRAO", $params);

            if ($CDCLIENTE !== $CLIENTE_PADRAO['CDCLIENTE']){
                throw new \Exception("Favor informar um consumidor para o cliente escolhido.");
            }
        }
    }

    public function updateCartPrices(&$products, $CDFILIAL, $CDLOJA = null, $CDCLIENTE = null, $CDCONSUMIDOR = null){

        $this->checkClientConsumer($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR);

        $numeroProdutos = 0;
        foreach ($products as &$product){

            $price = $this->precoAPI->buscaPreco($CDFILIAL, $CDCLIENTE, $product['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);

            if (!empty($product['PRODUTOS']) && $product['IDIMPPRODUTO'] == '2'){
                $price['error'] = true;
            }

            if (!$price['error']){
                $product['PRITEM'] = $price['PRECO'];
                $product['PRECO'] = $price['PRECO'];
                $product['VRDESITVEND'] = $price['DESC'];
                $product['VRACRITVEND'] = $price['ACRE'];
                $product['VRPRECITEMCL'] = $price['PRECOCLIE'];
                $product['PRITOTITEM'] = floatval(bcsub(str_replace(',','.',strval($price['PRECO'] + $price['PRECOCLIE'] + $price['ACRE'])), str_replace(',','.',strval($price['DESC'])), '2'));
                $numeroProdutos++;
            }
            else {
                if (!empty($product['PRODUTOS'])){
                    if ($product['IDIMPPRODUTO'] == '2' && $product['IDTIPOCOMPPROD'] !== 'C'){
                        // Promoção Inteligente.
                        $treatedProducts = array();
                        foreach ($product['PRODUTOS'] as &$subProduct){
                            $priceData = $this->precoAPI->buscaPreco($CDFILIAL, $CDCLIENTE, $subProduct['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
                            $productActualPrice = round($priceData['PRECO'] + $priceData['PRECOCLIE'] + $priceData['ACRE'] - $priceData['DESC'], 2);
                            $promoDiscount = $this->pedidoService->calculaDesconto($CDFILIAL, $product['CDPRODUTO'], $subProduct['CDPRODUTO'], $productActualPrice, $treatedProducts);
                            $subProduct['PRECO'] = $subProduct['PRITEM'] = $priceData['PRECO'];
                            $subProduct['VRPRECITEMCL'] = $priceData['PRECOCLIE'];
                            $price = floatval(bcadd(str_replace(',','.',strval($productActualPrice)), str_replace(',','.',strval($promoDiscount)), '2'));

                            $subProduct['DISCOUNT'] = -1 * $promoDiscount;
                            if ($promoDiscount > 0){
                                $subProduct['VRACRITVEND'] = floatval(bcadd(str_replace(',','.',strval($priceData['ACRE'])), str_replace(',','.',strval($subProduct['DISCOUNT'])), '2'));
                            }
                            else {
                                $subProduct['VRDESITVEND'] = floatval(bcadd(str_replace(',','.',strval($priceData['DESC'])), str_replace(',','.',strval($subProduct['DISCOUNT'])), '2'));
                            }

                            $subProduct['PRICE'] = $subProduct['TOTPRICE'] = $price;
                            $numeroProdutos++;
                            array_push($treatedProducts, $subProduct['CDPRODUTO']);
                        }
                    }
                    else {
                        // Produto Combinado.
                        $maxPrice = 0;
                        foreach ($product['PRODUTOS'] as &$subProduct){
                            $priceData = $this->precoAPI->buscaPreco($CDFILIAL, $CDCLIENTE, $subProduct['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
                            $subProduct['PRICE']  = $subProduct['PRITEM'] = round($priceData['PRECO'] + $priceData['PRECOCLIE'] + $priceData['ACRE'] - $priceData['DESC'], 2);
                            if ($subProduct['PRICE'] <= 0) $subProduct['PRICE'] = 0.01;

                            if ($product['IDTIPCOBRA'] === 'C' && $subProduct['PRICE'] > $maxPrice){
                                $maxPrice = $subProduct['PRICE'];
                            }
                        }

                        foreach ($product['PRODUTOS'] as &$subProduct){
                            if ($product['IDTIPCOBRA'] === 'C'){
                                $subProduct['PRICE']  = $subProduct['PRITEM'] = $maxPrice;
                            }
                            $subProduct['PRECO']  = $subProduct['PRITEM'] = number_format($subProduct['PRICE'], 3, '.', '');
                            $subProduct['STRPRICE'] = number_format($subProduct['PRICE'], 2, ',', '.');

                            $numeroProdutos++;
                        }
                    }
                    foreach ($product['PRODUTOS'] as &$subProduct) {
                        $totalObsAcrescimo = 0;
                        foreach ($subProduct['CDOCORR'] as &$CDOCORR) {
                            $this->calculaObsAcrescimo($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $CDOCORR, $totalObsAcrescimo, $numeroProdutos, $product['QTPRODCOMVEN']);
                        }
                        $subProduct['totalObsAcrescimo'] = $totalObsAcrescimo;
		        	}
                }
                else {
                    throw new \Exception($price['message'], 1);
                }
            }

            $totalObsAcrescimo = 0;
            foreach ($product['CDOCORR'] as &$CDOCORR){
                $this->calculaObsAcrescimo($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $CDOCORR, $totalObsAcrescimo, $numeroProdutos, $product['QTPRODCOMVEN']);
            }
            $product['totalObsAcrescimo'] = $totalObsAcrescimo;
        }

        $products = $this->calculaDescontoSubgrupo($products);

        $valorVenda = 0;
        $subsidioTotal = 0;
        $subsidioReal = 0;
        foreach ($products as &$product){
            if (!empty($product['PRODUTOS']) && $product['IDIMPPRODUTO'] == '2'){
                foreach ($product['PRODUTOS'] as &$subProduct){
                    $valorVenda += floatval(bcmul(str_replace(',','.',strval($product['QTPRODCOMVEN'] * $subProduct['QTPRODCOMVEN'])), str_replace(',','.',strval($subProduct['PRICE'])), '2')) + $subProduct['totalObsAcrescimo'];
                    $subsidioTotal += floatval(bcmul(str_replace(',','.',strval($product['QTPRODCOMVEN'] * $subProduct['QTPRODCOMVEN'])), str_replace(',','.',strval($subProduct['VRPRECITEMCL'])), '2'));
                    $subProduct['REALSUBSIDY'] = self::calculateSubsidy($subProduct['PRECO'], $subProduct['VRPRECITEMCL'], $subProduct['VRDESITVEND']);
                    $subProduct['REALSUBSIDY'] = floatval(bcmul(str_replace(',','.',strval($product['QTPRODCOMVEN'] * $subProduct['QTPRODCOMVEN'])), str_replace(',','.',strval($subProduct['REALSUBSIDY'])), '2'));
                    $subsidioReal += $subProduct['REALSUBSIDY'];
                }
            }
            else if ($product['IDTIPOCOMPPROD'] === 'C'){
                foreach ($product['PRODUTOS'] as &$subProduct){
                    $total = floatval(bcmul(str_replace(',','.',strval($subProduct['PRICE'])), str_replace(',','.',strval($subProduct['QTPRODCOMVEN'])), '2'));
                    if ($total <= 0) $total = 0.01;
                    $valorVenda += $total + $subProduct['totalObsAcrescimo'];
                }
            }
            else {
                $product['QTPRODCOMVEN'] = str_replace(',','.', strval($product['QTPRODCOMVEN']));
                $product['PRECO'] = str_replace(',','.', strval($product['PRECO']));
                $product['VRPRECITEMCL'] = str_replace(',','.', strval($product['VRPRECITEMCL']));
                $product['VRACRITVEND'] = str_replace(',','.', strval($product['VRACRITVEND']));
                $product['VRDESITVEND'] = str_replace(',','.', strval($product['VRDESITVEND']));

                $valorVenda += floatval(bcsub(floatval(bcmul($product['QTPRODCOMVEN'], ($product['PRECO'] + $product['VRPRECITEMCL']), 2)) + floatval(bcmul($product['QTPRODCOMVEN'], $product['VRACRITVEND'], 2)), floatval(bcmul($product['QTPRODCOMVEN'], $product['VRDESITVEND'], 2)), 2));
                $valorVenda += $product['totalObsAcrescimo'];
                $subsidioTotal += floatval(bcmul(str_replace(',','.',strval($product['QTPRODCOMVEN'])), str_replace(',','.',strval($product['VRPRECITEMCL'])), '2'));
                $product['REALSUBSIDY'] = self::calculateSubsidy($product['PRECO'], $product['VRPRECITEMCL'], $product['VRDESITVEND']);
                $product['REALSUBSIDY'] = floatval(bcmul(str_replace(',','.',strval($product['QTPRODCOMVEN'])), str_replace(',','.',strval($product['REALSUBSIDY'])), '2'));
                $subsidioReal += $product['REALSUBSIDY'];
            }
        }

        return array(
            'valorVenda' => floatval(bcmul(str_replace(',','.',strval($valorVenda)), '1', '2')),
            'subsidioTotal' => floatval(bcmul(str_replace(',','.',strval($subsidioTotal)), '1', '2')),
            'subsidioReal' => floatval(bcmul(str_replace(',','.',strval($subsidioReal)), '1', '2')),
            'numeroProdutos' => $numeroProdutos
        );
    }

    private function calculaObsAcrescimo($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $CDOCORR, &$totalObsAcrescimo, &$numeroProdutos, $QTPRODCOMVEN){
        $params = array(
            $CDFILIAL,
            $CDLOJA,
            $CDOCORR
        );
        $observation = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_OBS_TYPE", $params);

        // Verifica se a observação é um acrescimo.
        if ($observation['IDCONTROLAOBS'] === 'A' && !empty($observation['CDPRODUTO'])) {
            // Busca o preço do acréscimo.
            $price = $this->precoAPI->buscaPreco($CDFILIAL, $CDCLIENTE, $observation['CDPRODUTO'], $CDLOJA, $CDCONSUMIDOR);
            if (!$price['error']){
                $totalObsAcrescimo += floatval(bcmul(str_replace(',','.',strval($QTPRODCOMVEN)), str_replace(',','.',strval(number_format(($price['PRECO'] + $price['PRECOCLIE']) - $price['DESC'] + $price['ACRE'], 2, '.', ''))), '2'));
                $numeroProdutos++;
            } else {
                throw new \Exception($price['message'], 1);
            }
        }
    }

    private function calculateSubsidy($VRPRECITEM, $VRPRECITEMCL, $VRDESITVEND){
        if ($VRPRECITEMCL > 0 && $VRDESITVEND > 0){

            $finalPrice = floatval(bcsub(str_replace(',','.',strval($VRPRECITEM + $VRPRECITEMCL)), str_replace(',','.',strval($VRDESITVEND)), '2'));
            if ($finalPrice <= 0.01) return 0;

            $totalPrice = $VRPRECITEM + $VRPRECITEMCL;
            $discount = ($VRPRECITEMCL / $totalPrice) * $VRDESITVEND;

            return floatval(bcsub(str_replace(',','.',strval($VRPRECITEMCL)), str_replace(',','.',strval($discount)), '2'));
        }
        else return $VRPRECITEMCL;
    }

    public function consumerSearch($CDFILIAL, $CDCLIENTE, $code){
    	$CDCLIENTE = !empty($CDCLIENTE) ? $CDCLIENTE : 'T';

        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'code' => $code
        );
        $consumer = $this->entityManager->getConnection()->fetchAll("SQL_CONSUMER_CDEXCONSUMID", $params);
        if (empty($consumer)){
            $params['code'] = str_pad($code, 10, '0', STR_PAD_LEFT);
            $consumer = $this->entityManager->getConnection()->fetchAll("SQL_CONSUMER_CDIDCONSUMID", $params);
            if (empty($consumer)){
                $params['code'] = $code;
                $consumer = $this->entityManager->getConnection()->fetchAll("SQL_GET_CONSUMER_DETAILS", $params);
                if (empty($consumer)){
                    $consumer = $this->entityManager->getConnection()->fetchAll("SQL_CONSUMER_CDIDCONSUMID", $params);
                    if (empty($consumer)){
                        $consumer = $this->entityManager->getConnection()->fetchAll("SQL_CONSUMER_NRCPFRESPCON", $params);
                    }
                }
            }
        }

        return $consumer;
    }

    public function getIDTPVENDACONS($CDCLIENTE, $CDCONSUMIDOR){
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'code' => $CDCONSUMIDOR
        );
        $consumer = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_DETAILS", $params);
        return $consumer['IDTPVENDACONS'];
    }

    public function verificaProdutosBloqueados($session, $produtos){
		$prodpai = array_column($produtos, 'PRODUTOS');
		$prodfilho = array();
		for ($i = 0; $i < Count($prodpai); $i++) {
			if (!empty($prodpai[$i])){
				$prodfilho = array_merge($prodfilho, $prodpai[$i]);
			}
		}
		$params = array(
            'CDFILIAL' => $session['CDFILIAL'],
			'CDLOJA' => $session['CDLOJA'],
			'NRORG' => $session['NRORG'],
			'NRCONFTELA' => $session['NRCONFTELA'],
			'CDPRODUTO' => array_unique(array_merge(array_column($prodfilho, 'CDPRODUTO'), array_column($produtos, 'CDPRODUTO')))
		);
		$type = array(
			'CDPRODUTO' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
		);

        return $this->entityManager->getConnection()->fetchAll("BUSCA_NOMEPRODBLOQ", $params, $type);
    }

    public function searchCard($searchValue){
        $session = $this->util->getSessionVars(null);

        $params = array(
            'SEARCH_VALUE' => $searchValue
        );

        if ($session['IDEXTCONSONLINE'] !== 'S'){
            $result = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_CARTOES", $params);
        }
        else {
            $result = $this->extratocons->consultaCartao($params['SEARCH_VALUE']);
            if (!empty($result)){
                $result = $result[0];
                if (!array_key_exists(0, $result)){
                    $result = array(0 => $result);
                }
            }

            foreach ($result as &$consumer){
                if (is_array($consumer['CDFAMILISALD'])) $consumer['CDFAMILISALD'] = null;
                if (is_array($consumer['NMFAMILISALD'])) $consumer['NMFAMILISALD'] = null;
                $consumer['ID'] = $consumer['CDCLIENTE']."-".$consumer['CDCONSUMIDOR']."-".$consumer['CDFAMILISALD'];
                $consumer['IDSITCONSUMI'] = '1';
            }
        }

        return $result;
    }

	public function filterProducts($session, $filter, $FIRST, $LAST){
		$CDFILIAL = $session['CDFILIAL'];
		$CDLOJA = $session['CDLOJA'];
		$NRCONFTELA = $session['NRCONFTELA'];
		$now = $this->date->getDataAtual("d/m/Y");

		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDLOJA' => $CDLOJA,
			'NRCONFTELA' => $NRCONFTELA,
			'DATAATUAL' => $now,
			'FILTER' => strtoupper($filter),
			'FIRST' => $FIRST,
			'LAST' => $LAST,
			'SEARCH' => $filter[0] == '%' ? 0 : 1
		);

		$types = array(
			'DATAATUAL' => \Doctrine\DBAL\TypeS\Type::DATE
		);

		$produtos = $this->entityManager->getConnection()->fetchAll('FILTRAR_PRODUTOS', $params, $types);
		if ($produtos){
			$precos = $this->parametrosAPI->buscaTabelaPadrao($CDFILIAL, $CDLOJA, $CDLOJA, $produtos);
			if (!$precos['error']) {
				$obsIndexadasPorProduto = $this->parametrosAPI->montaObservacoes($CDFILIAL, $CDLOJA);
				$produtos = array_values($precos['precosIndexadosPorProduto']);
				foreach ($produtos as &$produto) {
					$produto['GRUPOS'] = array();
					$produto['PRODUTOS'] = array();
					$produto['PRITEM'] = floatval($produto['PRITEM']);
					$produto['VRPRECITEMCL'] = floatval($produto['VRPRECITEMCL']);
					$produto['HRFIMVENPROD'] = floatval($produto['HRFIMVENPROD']);
					$produto['HRINIVENPROD'] = floatval($produto['HRINIVENPROD']);
					$produto['PRECO'] = $this->util->formataPreco($produto['PRECO']);
					$produto['OBSERVATIONS'] = array_key_exists($produto['CDPRODUTO'], $obsIndexadasPorProduto)
						? $obsIndexadasPorProduto[$produto['CDPRODUTO']] : array();
				}
			} else {
				throw new \Exception($precos['message']);
			}
		}
		return array_values($produtos);
	}

    public function transferPosition($NRVENDAREST, $NRCOMANDA, $products, $position, $CDCLIENTE, $CDCONSUMIDOR){
        try {
            $session = $this->util->getSessionVars(null);

            $connection = $this->entityManager->getConnection();
            $connection->beginTransaction();

            // Valida se a posição está sendo recebida.
            $positionControl = $this->tableService->getPositionControlDetails($session['CDFILIAL'], $NRVENDAREST, $session['CDOPERADOR']);
            foreach ($positionControl as $lockedPosition){
                if (intval($lockedPosition) == $position){
                    throw new \Exception("Não foi possível realizar a transferência. Esta posição está sendo recebida.");
                }
            }

            foreach ($products as $NRPRODCOMVEN){

                $params = array(
                    'NRVENDAREST' => $NRVENDAREST,
                    'NRCOMANDA' => $NRCOMANDA,
                    'NRPRODCOMVEN' => $NRPRODCOMVEN
                );
                $positionDetails = $this->entityManager->getConnection()->fetchAssoc("GET_POSITION_CLIENT", $params);

                // Valida se o cliente/consumidor são diferentes.
                if ($positionDetails['CDCLIENTE'] != $CDCLIENTE || $positionDetails['CDCONSUMIDOR'] != $CDCONSUMIDOR){
                    throw new \Exception("Não é possível transferir produtos para posições que possuam cliente/consumidor diferente da posição de origem.");
                }

                $params = array(
                    'POSITION' => str_pad($position, 2, '0', STR_PAD_LEFT),
                    'NRVENDAREST' => $NRVENDAREST,
                    'NRCOMANDA' => $NRCOMANDA,
                    'NRPRODCOMVEN' => $NRPRODCOMVEN
                );
                $this->entityManager->getConnection()->fetchAssoc("TRANSFER_POSITION", $params);

            }

            $connection->commit();
        } catch (\Exception $e){
            $connection->rollback();
            throw new \Exception($e->getMessage());
        }
    }

	public function selectVendedores($session, $filter, $FIRST, $LAST){
		$params = array(
			'CDVENDEDOR' => 'T',
			'FILTER' => strtoupper($filter),
			'FIRST' => $FIRST,
			'LAST' => $LAST
		);

		$result = $this->entityManager->getConnection()->fetchAll('VENDEDORES_OPERADORES_ATIVOS', $params);

		return array_values($result);
	}

	public function validatePassword($session, $password){
		$params = array(
            'CDOPERADOR' => $session['CDOPERADOR']
        );
        $operador = $this->entityManager->getConnection()->fetchAssoc("VALIDA_OPERADOR", $params);

		$passwordResult = $this->utilAPI->validaSenha($password, $operador['CDSENHAOPERWEB']);
        if (!$passwordResult) throw new \Exception('Senha inválida.');

		return array('error' => false, 'message' => 'OK');
	}

	public function selectComandaProducts($CDFILIAL, $NRCOMANDA){
		$params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRCOMANDA' => $NRCOMANDA
        );
        $result = $this->entityManager->getConnection()->fetchAll('SQL_COMANDA_ITENS', $params);

        if (!$result) throw new \Exception('Comanda sem pedidos lançados.');

		return array_values($result);
	}

	public function updateComandaProducts($session, $comandaAtual, $vendaRestComandaAtual, $comandaDestino, $vendaRestComandaDestino, $CDPRODUTO, $NRPRODCOMVEN, $CDSUPERVISOR){
		try {
        	$this->entityManager->getConnection()->beginTransaction();
	        $produtosLog = '';

			$params = array(
	            'CDFILIAL' => $session['CDFILIAL'],
	            'NRCOMANDA' => $comandaDestino
	        );

	        for ($i = 0; $i < count($CDPRODUTO); $i++) {
	        	$this->util->newCode('ITCOMANDAVEN' . $session['CDFILIAL'] . $comandaDestino);
				$maxNRPRODCOMVEN = $this->util->getNewCode('ITCOMANDAVEN' . $session['CDFILIAL'] . $comandaDestino, 6);

		        $params = array(
		            'CDFILIAL' => $session['CDFILIAL'],
		            'COMANDAATUAL' => $comandaAtual,
		            'VRCOMANDAATUAL' => $vendaRestComandaAtual,
		            'MAXNRPRODCOMVEN' => str_pad($maxNRPRODCOMVEN, 6, "0", STR_PAD_LEFT),
		            'CDPRODUTO' => $CDPRODUTO[$i],
		            'NRPRODCOMVEN' => $NRPRODCOMVEN[$i],
		            'COMANDADEST' => $comandaDestino,
		            'VRCOMANDADEST' => $vendaRestComandaDestino,
		            'CDSUPERVISOR' => $CDSUPERVISOR
		        );

		        $produtosLog .= $produtosLog == '' ? $CDPRODUTO[$i] : ', ' . $CDPRODUTO[$i];

				$this->entityManager->getConnection()->executeQuery('UPDATE_ITCOMANDAVEN_TRANSFER', $params);
			}

			$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'TRA_PRO', $session['CDOPERADOR'], $CDSUPERVISOR,
				"Waiter - Transferência de Produto", "Transferência de produtos da comanda " . $comandaAtual . " para comanda " .
				$comandaDestino . ". Produtos: " . $produtosLog . ".");

			$this->entityManager->getConnection()->commit();
		} catch (\Exception $e) {
			$this->entityManager->getConnection()->rollBack();
			throw $e;
		}
	}

	public function setDiscountFidelity($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $positions, $VRDESCFID){
		$connection = null;

		try {
			$connection = $this->entityManager->getConnection();
	        $connection->beginTransaction();

			// desconto do Crédito Fidelidade por mesa ou posição
			if (empty($positions)){
				$table = array(
					'NRVENDAREST' => $NRVENDAREST,
					'NRCOMANDA' => $NRCOMANDA
				);
				$paramsUpdate = $this->formatUpdateTableData($CDFILIAL, $table, null, null, $VRDESCFID);
				$this->entityManager->getConnection()->executeQuery("UPDATE_COMANDAVEN_DESCFID", $paramsUpdate);
			} else {
				$positionsLength = count($positions);
				$VRDESCFIDPOS = floatval(bcdiv(str_replace(',','.',strval($VRDESCFID / $positionsLength)), '1', '2'));
				$positions = array_fill_keys($positions, $VRDESCFIDPOS);

				$preTotDiscount = $positionsLength * $VRDESCFIDPOS;
				if ($preTotDiscount < $VRDESCFID){
					$this->util->adjustDifference($positions, round($VRDESCFID - $preTotDiscount, 2), 'array', 'VRDESCCOMVEN');
				}

				$params = array(
					'CDFILIAL' => $CDFILIAL,
					'NRVENDAREST' => $NRVENDAREST
				);
				foreach ($positions as $position => $VRDESCFIDPOS) {
					$params['NRLUGARMESA'] = $position;
					$params['VRDESCFIDPOS'] = $VRDESCFIDPOS;
					$this->entityManager->getConnection()->executeQuery("UPDATE_POSVENDAREST_DESCFID", $params);
				}
			}

			$connection->commit();
		} catch(\Exception $e){
			if ($connection != null) {
            	$connection->rollback();
        	}
			throw new $e;
		}
	}

	public function adjustDiscountFidelity($cdfilial, $nrvendarest, $produtosParcial, $cdprodcouver, $cdprodconsum, $total, $numeroProdutos, $arrayPosicoes, $VRDESCFID, &$fidelityValue, &$fidelityDiscount, &$totalProdutosTaxa, &$totalSubsidy){
        // parcial solicitada por posição
        if ($arrayPosicoes[0] != 'T'){
        	// valor de desconto deve ser controlado por posição
        	$VRDESCFID = 0;
        	$params = array(
        		'CDFILIAL' => $cdfilial,
        		'NRVENDAREST' => $nrvendarest,
        		'NRLUGARMESA' => $arrayPosicoes
        	);
        	$type = array(
        		'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
        	);
        	$posvendarest = $this->entityManager->getConnection()->fetchAll('SQL_POSVENDAREST_POSICAO', $params, $type);

        	// verifica se todas as posições selecionadas apresentam cliente e consumidores setados
        	if (count($arrayPosicoes) == count($posvendarest)){
        		// valida se todas as posições apresentam o mesmo cliente e consumidor
        		if (count(array_unique(array_column($posvendarest, 'CDCLIENTE'))) == 1 &&
        			count(array_unique(array_column($posvendarest, 'CDCONSUMIDOR'))) == 1){
        			$VRDESCFID = floatval(array_sum(array_column($posvendarest, 'VRDESCFIDPOS')));
        		}
        	}
        }
    	// verifica se desconto fidelidade foi setado
    	if ($VRDESCFID > 0 && $numeroProdutos > 0){
	        $minCost = floatval(0.01 * $numeroProdutos);
			$maxDiscount = round($total - $minCost, 2);

			if ($VRDESCFID >= $maxDiscount){
				$fidelityDiscount = ($VRDESCFID > $total) ? $total : $VRDESCFID;
				$fidelityValue = $maxDiscount;
			} else {
				$fidelityDiscount = $VRDESCFID;
				$fidelityValue = $VRDESCFID;
			}

			// reteia desconto nos produtos
			$vrDescBaseCalc = floatval(bcdiv(str_replace(',','.',strval($fidelityValue / $maxDiscount)), '1', '2'));
			$vrTotDescCalc = 0;
			$arrProdDesc = array();
			foreach ($produtosParcial as $key => $item){
				if ($item['CDPRODUTO'] != $cdprodcouver && $item['CDPRODUTO'] != $cdprodconsum) {
					$vrProd = $this->util->calculaTotalItem($item);
					$desc = floatval(bcdiv(str_replace(',','.',strval($vrProd * $vrDescBaseCalc)), '1', '2'));
					$item['VRDESCCOMVEN'] += $desc;
					$arrProdDesc[$key] = $item;
					$vrTotDescCalc += $desc;
				}
			}
			if ($vrTotDescCalc < $fidelityValue){
				$this->util->adjustDifference($arrProdDesc, round($fidelityValue - $vrTotDescCalc, 2), 'prodObject', 'VRDESCCOMVEN');
			}
			// inclui ao array principal de produtos o rateio do desconto
			foreach ($arrProdDesc as $key => $item) {
				$produtosParcial[$key] = $item;
			}

			// recalcula preço dos produtos para a taxa de serviço
			$totalProdutosTaxa = 0;
			$totalProdutosTaxaDesc = 0;
			$totalSemDesconto = 0;
			$totalDesconto = 0;
            $totalSubsidy = 0;
			self::calculaValoresParcial($cdfilial, $produtosParcial, $cdprodcouver, $cdprodconsum, $totalProdutosTaxa, $totalProdutosTaxaDesc, $totalSemDesconto, $totalDesconto, $totalSubsidy);
			$totalProdutosTaxa = round(round($totalProdutosTaxa, 2) - $totalProdutosTaxaDesc, 2);
    	}
	}

	public function fidelitySearch($session, $CDCLIENTE, $CDCONSUMIDOR){
		$result = array();

		try {
		    $saldo = $this->consumidorAPI->buscaCreditoFidelidade($session['CDFILIAL'], $CDCLIENTE, $CDCONSUMIDOR, $session['NRORG']);
	        $dadosTaxa = $this->caixaAPI->buscaDadosTaxa($session['CDFILIAL'], $session['CDLOJA']);

	        $result = array(
	            'VRSALDCONEXT' => $saldo['VRSALDCONEXT'],
	            'IDPERALTDESCFID' => $saldo['IDPERALTDESCFID'],
	            'IDCOMISVENDA' => $dadosTaxa['IDCOMISVENDA'],
	            'VRCOMISVENDA' => floatval($dadosTaxa['VRCOMISVENDA'])
	        );
		} catch (\Exception $e) {
			// erro ainda não tratado
			Exception::logException($e);
		}

		return $result;
	}

	private function handleItensPromo($arrayItens){
		$comboProduct = $this->getComboProduct($arrayItens);
		// valida se contém produtos promocionais
		if (!empty($comboProduct)) {
			$comboToUpdate = array();
			foreach ($comboProduct as $combo){
				$descontoAplicado = $this->util->validaDescontoDiferenciado($combo[0]['CDFILIAL'], $combo[0]['CDPRODPROMOCAO'], $combo, 'VRDESCCOMVEN');
				if ($descontoAplicado) {
					$comboToUpdate = array_merge($comboToUpdate, $combo);
				}
			}

			if (!empty($comboToUpdate)){
				// atualiza ITCOMANDAVEN com novos valores de desconto
				foreach ($comboToUpdate as $product) {
					$this->entityManager->getConnection()->executeQuery("UPDATE_DESCONTO_PROMOCAO", $product);
				}
			}
		}
	}

	private function getComboProduct($arrayItens){
		// separa os itens que são derivados de promoção
		$arrayItens = array_filter($arrayItens, function($produto){
			return !empty($produto['CDPRODPROMOCAO']);
		});

		// agrupa os itens que pertencem a uma mesma promoção
		$comboProduct = array_reduce($arrayItens, function($carry, $produto){
			$chave = $produto['CDPRODPROMOCAO']	. $produto['NRSEQPRODCOM'];
			$carry[$chave][] = $produto;

			return $carry;
		}, array());

		return array_values($comboProduct);
	}

    public function calculaDescontoSubgrupo($produtos){
        $productsParam = array();
        foreach($produtos as $key => &$produto){
            if (!empty($produto['PRODUTOS']) && $produto['IDIMPPRODUTO'] == '2' && $produto['IDTIPOCOMPPROD'] !== 'C'){
                foreach($produto['PRODUTOS'] as $subKey => &$subProduto){
                    $index = $key.':'.$subKey;
                    $productsParam[$index] = array(
                        'INDEX'      => $index,
                        'CDPRODUTO'  => $subProduto['CDPRODUTO'],
                        'QUANTIDADE' => floatval(bcmul(str_replace(',','.',strval($produto['QTPRODCOMVEN'])), str_replace(',','.',strval($subProduto['QTPRODCOMVEN'])), '3')),
                        'VALOR'      => $subProduto['PRITEM'] + $subProduto['VRPRECITEMCL'] + $subProduto['VRACRITVEND'],
                        'DESCONTO'   => floatval($subProduto['VRDESITVEND'])
                    );
                    $productsParam[$index]['TOTAL'] = floatval(bcsub(str_replace(',','.',strval($productsParam[$index]['VALOR'])), str_replace(',','.',strval($productsParam[$index]['DESCONTO'])), '2'));
                }
            }
            else {
                $productsParam[$key] = array(
                    'INDEX'      => $key,
                    'CDPRODUTO'  => $produto['CDPRODUTO'],
                    'QUANTIDADE' => $produto['QTPRODCOMVEN'],
                    'VALOR'      => $produto['PRITEM'] + $produto['VRPRECITEMCL'] + $produto['VRACRITVEND'],
                    'DESCONTO'   => floatval($produto['VRDESITVEND'])
                );
                $productsParam[$key]['TOTAL'] = floatval(bcsub(str_replace(',','.',strval($productsParam[$key]['VALOR'])), str_replace(',','.',strval($productsParam[$key]['DESCONTO'])), '2'));
                $productsParam[$key]['ADICIONAIS'] = floatval(bcsub(str_replace(',','.',strval($produto['PRITOTITEM'])), str_replace(',','.',strval($productsParam[$key]['TOTAL'])), '2'));
            }
        }

        $descontosSubgrupo = $this->precoAPI->applySubgroupDiscount($productsParam);

        foreach ($descontosSubgrupo as $key => &$produto){
            if (strrpos($key, ":")){
                $s = explode(":", $key);
                $produtos[$s[0]]['PRODUTOS'][$s[1]]['VRDESITVEND'] = floatval(bcmul(str_replace(',','.',strval($produto['DESCONTO'])), '1', '2'));
                $produtos[$s[0]]['PRODUTOS'][$s[1]]['PRICE'] = $produto['TOTAL'];
                $produtos[$s[0]]['PRODUTOS'][$s[1]]['TOTPRICE'] = $produto['TOTAL'];
                $produtos[$s[0]]['PRODUTOS'][$s[1]]['REALPRICE'] = $produto['TOTAL'];
            }
            else {
                $produtos[$produto['INDEX']]['VRDESITVEND'] = floatval(bcmul(str_replace(',','.',strval($produto['DESCONTO'])), '1', '2'));
                $produtos[$produto['INDEX']]['PRITOTITEM'] = $produto['TOTAL'] + $produto['ADICIONAIS'];
            }
        }

        // Ajusta preço de promoções.
        foreach ($produtos as &$produto){
            if (!empty($produto['PRODUTOS']) && $produto['IDIMPPRODUTO'] == '2' && $produto['IDTIPOCOMPPROD'] !== 'C'){
                $totalPrice = 0;
                foreach($produto['PRODUTOS'] as &$subProduto){
                    $totalPrice += floatval(bcmul(str_replace(',','.',strval($subProduto['QTPRODCOMVEN'])), str_replace(',','.',strval($subProduto['PRICE'])), '2'));
                }
                $produto['PRITOTITEM'] = $totalPrice + $produto['EXTRAS'];
            }
        }

        return $produtos;
    }

	private function utilCouvert($CDFILIAL, $NRCOMANDA, $NRVENDAREST, $IDUTILCOUVERT, $modoHabilitado = null){
		$mesasAgrupadas = $this->tableService->buscaMesasAgrupadas($NRCOMANDA, $NRVENDAREST);
		$mesasAgrupadas = $this->tableService->filtraMesasAgrupadas($modoHabilitado, $mesasAgrupadas, $NRVENDAREST, $NRCOMANDA);

		foreach ($mesasAgrupadas as &$mesa) {
			$params = array(
				'CDFILIAL'      => $CDFILIAL,
	            'NRCOMANDA'     => $mesa['NRCOMANDA'],
	            'NRVENDAREST'   => $mesa['NRVENDAREST'],
	            'IDUTILCOUVERT' => $IDUTILCOUVERT
			);
			$this->entityManager->getConnection()->executeQuery("SQL_ULTILIZA_COUVERT", $params);
		}
	}

    public function updateServiceTax($NRVENDAREST, $NRCOMANDA, $TOTALPRODS, $VRACRESCIMO, $TIPOGORJETA){
        $session = $this->util->getSessionVars(null);

        if ($TIPOGORJETA === 'V'){
            $VRCOMISVENDE = $VRACRESCIMO;
            $VRCOMISPOR = round($VRACRESCIMO / $TOTALPRODS * 100, 3);
        }
        else {
            $VRCOMISVENDE = round($TOTALPRODS * ($VRACRESCIMO / 100), 2);
            $VRCOMISPOR = $VRACRESCIMO;
        }

        $params = array(
            'CDFILIAL'     => $session['CDFILIAL'],
            'NRVENDAREST'  => $NRVENDAREST,
            'NRCOMANDA'    => $NRCOMANDA,
            'VRCOMISVENDE' => $VRCOMISVENDE,
            'VRCOMISPOR'   => $VRCOMISPOR
        );
        $this->entityManager->getConnection()->executeQuery("SQL_ATUALIZA_GORJETA", $params);

        $params = array(
            'CDFILIAL'    => $session['CDFILIAL'],
            'NRVENDAREST' => $NRVENDAREST
        );
        $this->entityManager->getConnection()->executeQuery("LIMPA_FIDELIDADE", $params);
    }

}