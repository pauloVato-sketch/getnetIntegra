<?php

namespace Service;

use \Util\Exception;

class Transactions {

	protected $entityManager;
	protected $tableService;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Service\Table $tableService,
		\Util\Util $util) {

		$this->entityManager = $entityManager;
		$this->tableService = $tableService;
		$this->util = $util;
	}

	public function buscaTransacoes($DTHRFIMMOVini, $DTHRFIMMOVfim, $NRADMCODE, $FIRST, $LAST, $chave){
		$session = $this->util->getSessionVars($chave);
		$CDFILIAL = $session['CDFILIAL'];
		$CDCAIXA = $session['CDCAIXA'];
		$params = array($DTHRFIMMOVini,$DTHRFIMMOVfim, $NRADMCODE, $CDFILIAL, $CDCAIXA, $FIRST, $LAST);
		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_TEMPO" ,$params);
	}

	public function buscaTransacoesMesa($session, $NRVENDAREST, $NRCOMANDA){
		$CDFILIAL = $session['CDFILIAL'];
		$CDLOJA   = $session['CDLOJA'];

		$NRVENDAREST = self::getGroupedTables($CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA);
		$params = array(
			'CDFILIAL'    => $CDFILIAL,
		    'NRVENDAREST' => array_filter(explode('_', $NRVENDAREST))
		);
		$types  = array(
			'CDFILIAL'    => \PDO::PARAM_STR,
		    'NRVENDAREST' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
		);

		$transacoes = array(
			'VALORPAGO' => 0,
			'VALORRETIRADA' => 0 
		);
		if ($session['IDCOLETOR'] !== 'C'){
			$entrada = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_TRANSACOES_ENTRADA_MOVCAIXA", $params, $types);
			$transacoes['VALORPAGO'] = !empty($entrada['VALORPAGO']) ? $entrada['VALORPAGO'] : 0;
			$saida   = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_TRANSACOES_SAIDA_MOVCAIXA", $params, $types);
			$transacoes['VALORRETIRADA'] = !empty($saida['VALORRETIRADA']) ? $saida['VALORRETIRADA'] : 0;
		} else {
			$entrada = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_TRANSACOES_MESA_MOVCAIXAMOB", $params, $types);
			$transacoes['VALORPAGO'] = !empty($entrada['VALORPAGO']) ? $entrada['VALORPAGO'] : 0;
			// para adiantamento (quando IDCOLETOR === 'C'), não existe movimentação do tipo saída
		}

		return $transacoes;
	}

	public function buscaTransacoesPosicao($session, $NRVENDAREST, $NRCOMANDA, $impPosicao){
		$CDFILIAL = $session['CDFILIAL'];
		$CDLOJA   = $session['CDLOJA'];

		$NRVENDAREST = self::getGroupedTables($CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA);
		$params = array(
			'CDFILIAL'    => $CDFILIAL,
		    'NRVENDAREST' => array_filter(explode('_', $NRVENDAREST)),
		    'NRLUGARMESA' => array_filter(explode(',', $impPosicao))
		);
		$types  = array(
			'CDFILIAL'    => \PDO::PARAM_STR,
		    'NRVENDAREST' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
		    'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
		);

		$entrada['VALORPAGO'] = 0;
		$movcaixamob = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_POSICAO_MOVCAIXAMOB", $params, $types);
		if (!empty($movcaixamob)){
			$entrada['VALORPAGO'] = array_sum(array_column($movcaixamob, 'VRMOV'));
		}

		return array_merge($entrada, Array('VALORRETIRADA' => 0));
	}

	public function buscaPagamentoMesa($chave, $NRMESA, $NRLUGARMESA, $NRVENDAREST){
		$session = $this->util->getSessionVars($chave);
		$CDFILIAL = $session['CDFILIAL'];
		$params = array($CDFILIAL, $NRMESA, $NRLUGARMESA, $NRVENDAREST);
		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_PAGAMENTO_MESA", $params);
	}

	public function buscaLinhaCancelamento($chave, $NRSEQMOVMOB){
		$session = $this->util->getSessionVars($chave);
		$CDFILIAL = $session['CDFILIAL'];
		$params = array($CDFILIAL, $NRSEQMOVMOB);
		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_LINHA_CANCELAMENTO", $params);
	}

	public function atualizaTransacaoCancelada($NRSEQMOVMOB) {
		$params = array($NRSEQMOVMOB);
		return $this->entityManager->getConnection()->executeQuery("SQL_UPDATE_CANCEL_TRANSACTION", $params);
	}

	public function moveTransactions($dataset){
		try{
			$connection  = null;
			$session     = $this->util->getSessionVars($dataset['chave']);
			$CDFILIAL    = $session['CDFILIAL'];
			$CDLOJA    = $session['CDLOJA'];
			$NRVENDAREST = $dataset['NRVENDAREST'];
			$NRCOMANDA   = $dataset['NRCOMANDA'];
			$NRLUGARMESA = $dataset['NRLUGARMESA'];
			$positions   = $dataset['positions'];

			//Deleta a primeira posição, pois ela é a posição de destino (NRLUGARMESA)
			unset($positions[0]);
			$positions = array_values($positions);

			$NRVENDAREST = self::getGroupedTables( $CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA);

			$stPosicoes = "_";
			foreach ($positions as $posicao) {
				$stPosicoes .= $posicao . "_";
			}
			$params = array(
				$NRLUGARMESA,
				$CDFILIAL,
				$NRVENDAREST,
				$stPosicoes
			);

			$answer = $this->entityManager->getConnection()->executeQuery("SQL_MOVER_TRANSACOES", $params);
			if(!$answer) {
				return array('funcao' => '0', 'error' => '051'); //051 - Erro de execução na função.
			}

			return array("funcao" => "1");
		} catch (\Exception $e) {
			Exception::logException($e);
			if($connection != null) {
				$connection->rollback();
			}
			throw new \Exception ($e->getMessage(),1);
		}
	}

	private function buildArrayParams($params, &$arrayPosicoes){
		if (is_string($arrayPosicoes)) {
			$arrayPosicoes = explode(",", $arrayPosicoes);
		}
		array_splice($params, 3 , 0, $arrayPosicoes);
		return $params;
	}

	private function getGroupedTables($CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA){
		// valida e busca os dados da mesa
		$valMesa = $this->tableService->dadosMesa($CDFILIAL, $CDLOJA, $NRCOMANDA, $NRVENDAREST);
		$stNrVendaRest = $valMesa['NRVENDAREST'];
		$stNrComanda = $valMesa['NRCOMANDA'];

		// Retorna todas as mesas de um agrupamento.
		$r_groupedTables = $this->entityManager->getConnection()->fetchAll("SQL_GET_GROUPED_TABLES", array($valMesa['NRMESA']));
		// Formata as mesas.
		$groupedTables = array();
		foreach ($r_groupedTables as $table) {
			$temp = array(
			   'NRCOMANDA' => $table['NRCOMANDA'],
			   'NRVENDAREST' => $table['NRVENDAREST']
			);
			array_push($groupedTables, $temp);
		}

		if (Empty($groupedTables)) {
			$stComandaVens = "_".$stNrComanda."_";
			$stVendaRest = "_".$stNrVendaRest."_";
		} else {
			$stComandaVens = "_";
			$stVendaRest = "_";
			foreach ($groupedTables as $mesa){
				$dadosMesa = $this->tableService->dadosMesa($CDFILIAL, $CDLOJA, $mesa['NRCOMANDA'], $mesa['NRVENDAREST']);
				$stComandaVens .= $dadosMesa['NRCOMANDA']."_";
				$stVendaRest .= $dadosMesa['NRVENDAREST']."_";
			}
		}
		return $stVendaRest;
	}

	public function buscaJsonComprovante($NRSEQMOVMOB){
		$params = array($NRSEQMOVMOB);
		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_JSON_TRANSACAO", $params);
	}

	public function atualizaEmailCliente($DSEMAILCLI, $NRSEQMOVMOB){
		$params = array(
			$DSEMAILCLI,
			$NRSEQMOVMOB
		);
		return $this->entityManager->getConnection()->executeQuery("SQL_UPDATE_EMAIL_CLIENTE", $params);
	}
}