<?php

namespace Service;

class ProdutoOriginal {

	protected $entityManager;
	protected $util;
	protected $tableService;
	protected $billService;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Util\Util $util,
		\Service\Table $tableService,
		\Service\Bill $billService) {
		$this->entityManager = $entityManager;
		$this->util = $util;
		$this->tableService = $tableService;
		$this->billService   = $billService;
	}

	public function action($dataset){
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
			$arrayPosicoes = $impPosicao;
			$impPosicao = implode(",", $arrayPosicoes);
		} else {
			 if ($impPosicao == '')
				$impPosicao = 'T';
				$arrayPosicoes = array($impPosicao);
		}
		$result = array();
		$params = array(
			$NRCONFTELA,
			$session['CDFILIAL'],
			$NRCONFTELA,
			$session['CDFILIAL'],
			$stComandaVens,
			$NRCONFTELA,
			$session['CDFILIAL'],
			$NRCONFTELA,
			$session['CDFILIAL'],
			$stComandaVens
		);

		$r_ItensDetalhados = $this->entityManager->getConnection()->fetchAll("SQL_ITENS_ORIGINAIS", $params);
		if (empty($r_ItensDetalhados)){
			if ($dataset['mode'] == 'M'){
				return array('funcao' => '0', 'error' => []);
				// $r_ItensDetalhados['funcao'] = '0';
				// $r_ItensDetalhados['itens'] = [];
				// return $r_ItensDetalhados;
			} // Retornará vazio para checagem no front
			else return array('funcao' => '0', 'error' => '430');
		}


		foreach($r_ItensDetalhados as &$item) {
			if ($item['CDPRODUTO'] != $session['CDPRODCOUVER']){
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
					'quantidade'     => round($item['QTPRODCOMVEN'], 0),
					'preco'          => number_format(round(($item['VRPRECCOMVEN'] - $item['VRDESCCOMVEN'] + $item['VRACRCOMVEN']), 2),2,',','.'),
					'desconto'       => $item['VRDESCCOMVEN'],
					'posicao'        => $item['NRLUGARMESA'],
					'composicao'     => null,
					'DTHRINCOMVEN'   => substr($item['HORA'],0,5).' - '.substr($item['DATA'],0,5)
				);
				if ($item['CDPRODPROMOCAO']){
					$listaItens[$item['NRCOMANDA'] . $item['NRPRODCOMVEN']]['composicao'] = self::buscaComposicao($item['CDFILIAL'], $item['NRVENDAREST'], $item['NRCOMANDA'], $item['NRPRODCOMVEN'], $item['NRSEQPRODCOM']);
				}
			}
		}

		$listaItens['funcao'] = '1';
		return $listaItens;
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
}