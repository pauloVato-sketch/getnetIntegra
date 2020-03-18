<?php

namespace Service;

use \Util\Exception;

class Table {

	protected $entityManager;
	protected $util;
	protected $waiterMessage;
	protected $billService;
	protected $paramsService;
	protected $precoService;
	protected $vendaAPI;
	protected $registerService;

	const IDORIGEMVENDA_TABLE = 'MES_PKR';
	const IDORIGEMVENDA_BILL = 'CMD_PKC';
	const IDORIGEMVENDA_REGISTER = 'BAL_MOB';
    const IDORIGEMVENDA_DRIVETHU = 'CMD_THR';

	public function __construct(\Doctrine\ORM\EntityManager $entityManager,
		\Util\Util $util,
	 	\Util\WaiterMessage $waiterMessage,
	 	\Service\Bill $billService,
		\Service\Params $paramsService,
		\Odhen\API\Service\Preco $precoService,
		\Odhen\API\Service\Venda $vendaAPI,
		\Service\Register $registerService
	){
		$this->entityManager    = $entityManager;
		$this->util             = $util;
		$this->waiterMessage    = $waiterMessage;
		$this->billService      = $billService;
		$this->paramsService    = $paramsService;
		$this->precoService     = $precoService;
		$this->vendaAPI         = $vendaAPI;
		$this->registerService  = $registerService;
	}

	public function definePosition($positions){
		return ($positions) ?
			array_map(function($p){
				$p['NRLUGARMESA'] = str_pad($p['NRLUGARMESA'], 2, '0', STR_PAD_LEFT);
				return $p;
			}, $positions) :
			array();
	}

	public function modifyTablePosition($chave, $NRVENDAREST, $positions){
		$result = array(
			'status'  => true,
			'message' => ''
		);

		try {
			$this->entityManager->getConnection()->beginTransaction();

			$session = $this->util->getSessionVars($chave);

			$posVendaRest = self::getPosition($session, $NRVENDAREST, array());
			if (empty($posVendaRest)){
				self::setPosition($session, $NRVENDAREST, $positions);
			} else {
				$positions    = array_column($positions, null, 'NRLUGARMESA');
				$posVendaRest = array_column($posVendaRest, null, 'NRLUGARMESA');

				$updatePosition = array_intersect_key($positions, $posVendaRest);
				$setPosition    = array_diff_key($positions, $updatePosition);
				$deletePosition = array_diff_key($posVendaRest, $updatePosition);

				if (!empty($updatePosition)){
					$modifyPosition = self::filterUpdatePositions($updatePosition, $posVendaRest);
					self::updatePosition($session, $NRVENDAREST, $modifyPosition);
				}

				if (!empty($setPosition)){
					self::setPosition($session, $NRVENDAREST, $setPosition);
				}

				if (!empty($deletePosition)){
					self::deletePosition($session, $NRVENDAREST, array_column($deletePosition, 'NRLUGARMESA'));
				}
			}

			$this->entityManager->getConnection()->commit();
		} catch (\Exception $e) {
			Exception::logException($e);
			$this->entityManager->getConnection()->rollBack();

			$result['status']  = false;
			$result['message'] = $this->waiterMessage->getMessage('456');
		}

		return $result;
	}

	public function filterUpdatePositions($updatePosition, $posVendaRest){
		$modifyPosition = array();

		foreach ($updatePosition as $key => $position) {
			if ($posVendaRest[$key]['CDCLIENTE'] !== $position['CDCLIENTE'] ||
				$posVendaRest[$key]['CDCONSUMIDOR'] !== $position['CDCONSUMIDOR'] ||
				$posVendaRest[$key]['DSCONSUMIDOR'] !== $position['DSCONSUMIDOR']){
				array_push($modifyPosition, $position);
			}
		}

		return $modifyPosition;
	}

	public function getPosition($session, $NRVENDAREST, $positions){
		$paramsQuery = self::formatAllPOSVENDAREST($session, $NRVENDAREST, $positions);

		return $this->entityManager->getConnection()->fetchAll("GET_CLIENTE_ALL_POSITION", $paramsQuery[0], $paramsQuery[1]);
	}

	public function formatAllPOSVENDAREST($session, $NRVENDAREST, $positions){
		$arrayPositions = $positions;
		$allPositions   = $positions ? 'N' : 'T';

		return array(
			array(
				$session['CDFILIAL'],
				$NRVENDAREST,
				array_filter($arrayPositions),
				$allPositions,
				$session['NRORG']
			),
			array(
				\PDO::PARAM_STR,
				\PDO::PARAM_STR,
				\Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
				\PDO::PARAM_STR,
				\PDO::PARAM_INT
			)
		);
	}

	public function setPosition($session, $NRVENDAREST, $positions){
		$cdContador = 'POSVENDAREST' . $session['CDFILIAL'] . $NRVENDAREST;

		foreach ($positions as $position) {
			if (!!$position['CDCLIENTE'] || !!$position['DSCONSUMIDOR']) {
				// não insere posição sem CDCLIENTE
				$this->util->newCode($cdContador);
				$nrseqpos = $this->util->getNewCode($cdContador, 10);

				$paramsInsert = self::formatCLIPOSVENDAREST($session, $NRVENDAREST, $nrseqpos, $position);
				$this->entityManager->getConnection()->executeQuery("INSERT_POSVENDAREST", $paramsInsert);
			}
		}
	}

	public function formatCLIPOSVENDAREST($session, $NRVENDAREST, $nrseqpos, $position){
		return array(
			'CDFILIAL'     => $session['CDFILIAL'],
			'NRVENDAREST'  => $NRVENDAREST,
			'NRSEQPOS'     => $nrseqpos,
			'NRLUGARMESA'  => $position['NRLUGARMESA'],
			'CDCLIENTE'    => $position['CDCLIENTE'],
			'CDCONSUMIDOR' => $position['CDCONSUMIDOR'],
			'DSCONSUMIDOR' => $position['DSCONSUMIDOR'],
			'NRORG'        => $session['NRORG']
		);
	}

	public function deletePosition($session, $NRVENDAREST, $positions){
		$paramsQuery = self::formatAllPOSVENDAREST($session, $NRVENDAREST, $positions);

		$this->entityManager->getConnection()->fetchAll("DELETE_POSVENDAREST", $paramsQuery[0], $paramsQuery[1]);
	}

	public function updatePosition($session, $NRVENDAREST, $positions){
		$deletePosition = array();

		foreach ($positions as $position) {
			if (!!$position['CDCLIENTE'] || !!$position['DSCONSUMIDOR']){
				$paramsQuery = self::formatCLIPOSVENDAREST($session, $NRVENDAREST, null, $position);
				$this->entityManager->getConnection()->fetchAll("UPDATE_POSVENDAREST", $paramsQuery);
			} else {
				array_push($deletePosition, $position['NRLUGARMESA']);
			}
		}

		if (!empty($deletePosition)){
			self::deletePosition($session, $NRVENDAREST, $deletePosition);
		}
	}

	public function abreMesa($dataset){
		try {
			// Open connection and begin transaction.
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$session = $this->util->getSessionVars($dataset['chave']);
			$mesa = $dataset['mesa'];
			$quantidade = $dataset['quantidade'];
			$cliente = $dataset['CDCLIENTE'];
			$consumidor = $dataset['CDCONSUMIDOR'];

			// Handles the consumer.
			if (empty($consumidor)) {
				$consumidor = null;
			} else {
				// Checks if the consumer exists.
				$consumerCheck = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER", array($cliente, $consumidor));
				if (empty($consumerCheck)) {
					return array('funcao' => '0', 'error' => '447'); // Consumidor não cadastrado.
				}
			}

			// Handles the client.
			if (empty($cliente)) {
				$cliente = $session['CDCLIENTE'];
			}
			if ($cliente == "X-X") { // This code indicates that we want to use the default client.
				$tempResult = $this->entityManager->getConnection()->fetchAssoc("SQL_GETCLIENTEPADRAO", array($session['CDFILIAL']));
				$cliente = $tempResult[0];
			}

			// Checks if the client exists - otherwise using the default client.
			$tempResult = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_CLIENTE", array($cliente));
			if (empty($tempResult)) {
				$tempResult = $this->entityManager->getConnection()->fetchAssoc("SQL_GETCLIENTEPADRAO", array($session['CDFILIAL']));
				$cliente = $tempResult['CDCLIENTE'];
			}

			$filial = $session['CDFILIAL'];

			// Generates the codes.
			$this->util->newCode('VENDAREST'.$filial);
			$stPrxVendaRest = $this->util->getNewCode('VENDAREST'.$filial, 10);
			$this->util->newCode('COMANDAVEN'.$filial);
			$stComanda = $this->util->getNewCode('COMANDAVEN'.$filial, 10);

			$params = array(
				$session['CDFILIAL'],
				$session['CDLOJA'],
				$mesa
			);
			$checkVendarest = $this->entityManager->getConnection()->fetchAssoc("SQL_CHECK_VENDAREST", $params);
			if (empty($checkVendarest)) {
				$params = array(
					$session['CDFILIAL'],
					$session['CDLOJA'],
					$mesa
				);
				$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_STATUS_MESA", $params);
			}

			$valVendaBalcao = $this->entityManager->getConnection()->fetchAssoc("SQL_VAL_VENDA_BALCAO", array($session['CDFILIAL'], $session['CDLOJA'], $mesa));
			$valValidaMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_MESA_BYNRMESA", array($session['CDFILIAL'], $session['CDLOJA'], $mesa));

			if (($valValidaMesa['IDSTMESAAUX'] == 'D') || (!empty($valVendaBalcao['NRMESAPADRAO']))){

				// Checks if the seller - otherwise using the current seller.
				if (!empty($dataset['CDVENDEDOR'])) {
					$cdVendedor = $dataset['CDVENDEDOR'];
				} else {
					$cdVendedor = $session['CDVENDEDOR'];
				}

				// Inserts data into VENDAREST.
				$params = array(
					'CDFILIAL' => $filial,
					'NRVENDAREST' => $stPrxVendaRest,
					'CDLOJA' => $session['CDLOJA'],
					'NRMESA' => $mesa,
					'CDVENDEDOR' => $cdVendedor,
					'CDOPERADOR' => $session['CDOPERADOR'],
					'NRPESMESAVEN' => $quantidade,
					'CDCLIENTE' => $cliente,
					'CDCONSUMIDOR' => $consumidor,
					'NRPOSICAOMESA' => $quantidade,
					'NRORG' => $session['NRORG']
				);
				$this->entityManager->getConnection()->executeQuery("SQL_INSERE_VENDAREST", $params);

				// Inserts data into COMANDAVEN.
				$params = array(
					'CDFILIAL'    		=>	$filial,
					'NRVENDAREST'    	=>	$stPrxVendaRest,
					'NRCOMANDA'    		=>	$stComanda,
					'CDLOJA'    		=>	$session['CDLOJA'],
					'DSCOMANDA'    		=>	'PKR_'.$stComanda,
					'IDSTCOMANDA'    	=>	'1',
					'VRACRCOMANDA'    	=>	0,
					'IDORGCMDVENDA'     =>	'MES_MOB',
					'DSCONSUMIDOR'      =>	null
				);
				$this->entityManager->getConnection()->executeQuery("SQL_ABRE_COMANDA", $params);

				// Changes the table's status to 'occupied'.
				$params = array(
					'IDSTMESAAUX' => 'O',
					'CDFILIAL'    => $filial,
					'CDLOJA'      => $session['CDLOJA'],
					'NRMESA'      => $mesa
				);
				$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_STATUS_MESA", $params);

				// Finishes up.
				$result = array(
					'funcao' => '1',
					'CDSALA' => $valValidaMesa['CDSALA'],
					'NRCOMANDA' => $stComanda,
					'NRVENDAREST' => $stPrxVendaRest
				);
				$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'ABE_MES', $session['CDOPERADOR'], null, "Waiter - Abertura de mesa", "Nº mesa aberta: ".$mesa);
				$connection->commit();

				$logAbertura = $this->entityManager->getConnection()->fetchAssoc("SQL_LOG_ABERTURA", array($session['CDFILIAL'], $stPrxVendaRest));
				if (empty($logAbertura)){
					$this->util->log("ERRO: a mesa " . $mesa . " não foi salva. NRVENDAREST: " . $stPrxVendaRest . ", NRCOMANDA: " . $stComanda);
				}
			}
			else {
				$connection->rollback();
				$result = array('funcao' => '0', 'error' => '059');
				$this->util->log("EXCEPTION 1: NRMESA: " . $mesa . ", NRVENDAREST: " . $stPrxVendaRest . ", NRCOMANDA: " . $stComanda);
			}
		} catch(\Exception $e){
			Exception::logException($e);
			$connection->rollback();
			throw new \Exception ($e->getMessage(), 1);
			$this->util->log("EXCEPTION 2: NRMESA: " . $mesa . ", NRVENDAREST: " . $stPrxVendaRest . ", NRCOMANDA: " . $stComanda);
		}

		return $result;
	}

	public function agruparMesas($dataset){ //Dataset Params: mesa, listaMesas, pessoas
		try {
			$connection = null;
			$session  = $this->util->getSessionVars($dataset['chave']);
			$cdFilial = $session['CDFILIAL'];
			$cdLoja   = $session['CDLOJA'];

			$nrMesa     = $dataset['mesa'];
			$listaMesas = $dataset['listaMesas'];

			// Verifica se a mesa principal já se encontra agrupada.
			$params = array(
				'CDFILIAL' => $cdFilial,
				'CDLOJA' => $cdLoja,
				'PARAM' => $nrMesa
			);
			$grouping = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRJUNMESA", $params);

			/* Open connection and begin transaction. */
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			// Verifica se algumas das mesas já fazem parte de um agrupamento.
			if (empty($grouping)){
				// Cria novo agrupamento.
				$this->util->newCode('JUNCAOMESA' . $cdFilial . $cdLoja);
				$nrJunMesa = $this->util->getNewCode('JUNCAOMESA' . $cdFilial . $cdLoja, 10);
				$params = array($cdFilial, $cdLoja, $nrJunMesa);
				$this->entityManager->getConnection()->executeQuery("SQL_INS_JUNCAOMESA", $params);

                // Mesa selecionada.
                $params = array($cdFilial, $cdLoja, $nrJunMesa, $nrMesa);
                $this->entityManager->getConnection()->executeQuery("SQL_INS_MESAJUNCAO", $params);

                // Demais mesas.
				foreach ($listaMesas as $mesa) {
					$params = array($cdFilial, $cdLoja, $nrJunMesa, $mesa);
					$this->entityManager->getConnection()->executeQuery("SQL_INS_MESAJUNCAO", $params);
				}
			}
			else {
				// Junta as mesas agrupadas ao agrupamento já existente.
				foreach ($listaMesas as $mesa){
					$params = array($cdFilial, $cdLoja, $grouping['NRJUNMESA'], $mesa);
					$this->entityManager->getConnection()->executeQuery("SQL_INS_MESAJUNCAO", $params);
				}
			}

            // Busca comandas relacionadas no agrupamento sendo realizado.
			array_push($dataset['listaMesas'], $dataset['mesa']);

			$strParams = implode("_", $dataset['listaMesas']);
			$params = array(
				'CDFILIAL' => $cdFilial,
				'NRORG' => $session['NRORG'],
				'PARAM' => $strParams
			);
			$comandas = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_NRCOMANDA", $params);

            // Busca mesa e comanda principal do agrupamento.
            $comandaPrincipal = $this->buscaMesaPrincipal($session['CDFILIAL'], $session['CDLOJA'], $nrMesa);

            // Atualiza a tabela NOVOCODIGO e transfere produtos e posições para a mesa principal.
            $maxNRSEQUENCIAL = 0;
            $nrPessoas = 0;
            foreach ($comandas as $comanda){
                $params = array(
                    'CDCONTADOR' => 'ITCOMANDAVEN'.$session['CDFILIAL'].$comanda['NRCOMANDA'],
                    'NRORG' => $session['NRORG']
                );
                $NRSEQUENCIAL = $this->entityManager->getConnection()->fetchAssoc("SQL_PREPARE_FIX_NOVOCODIGO", $params);
                if ($NRSEQUENCIAL && intval($NRSEQUENCIAL['NRSEQUENCIAL']) > intval($maxNRSEQUENCIAL)) $maxNRSEQUENCIAL = $NRSEQUENCIAL['NRSEQUENCIAL'];
                // Calcula o número total de posições.
                $nrPessoas += intval($comanda['NRPESMESAVEN']);
            }

            // Altera quantidade da mesa principal.
            $params = array(
                'NRPESMESAVEN' => $nrPessoas,
                'NRPOSICAOMESA' => $nrPessoas,
                'CDFILIAL' => $cdFilial,
                'NRVENDAREST' => $comandaPrincipal['NRVENDAREST']
            );
            $this->entityManager->getConnection()->executeQuery("SQL_ALTERA_QTD_PESSOAS", $params);

            $positionIndex = $comandaPrincipal['NRPESMESAVEN'];

            $stNrSeqProdCom_Ant = '';
            $nrVendaRest_Ant = '';
            $r_NRSEQCOM = null;
            foreach ($comandas as $comanda){
                $params = array(
                    'NRSEQUENCIAL' => $maxNRSEQUENCIAL,
                    'CDCONTADOR' => 'ITCOMANDAVEN'.$session['CDFILIAL'].$comanda['NRCOMANDA'],
                    'NRORG' => $session['NRORG']
                );
                $this->entityManager->getConnection()->executeQuery("SQL_FIX_NOVOCODIGO", $params);

                if ($comanda['NRVENDAREST'] != $comandaPrincipal['NRVENDAREST']){
                    // Transfere todas as posições para e mesa principal.
                    $this->transferePosicao($cdFilial, $comanda['NRVENDAREST'], $comandaPrincipal['NRVENDAREST'], $positionIndex, $session['NRORG']);

                    // Verifica os itens consumidos na mesa de origem.
                    $pesquisaItensParams = array(
                        $cdFilial,
                        $comanda['NRVENDAREST'],
                        $comanda['NRCOMANDA']
                    );
                    $itensComandas = $this->entityManager->getConnection()->fetchAll("SQL_PESQUISA_ITENS_COMANDA", $pesquisaItensParams);

                    foreach ($itensComandas as $itemComanda) { // Insere os itens na mesa de destino.

                        $nrVendaRestDestino = $comandaPrincipal['NRVENDAREST'];
                        $nrComandaDestino = $comandaPrincipal['NRCOMANDA'];

                        /* PARTE 1 - EVITANDO REPETIÇÃO DE NRSEQPRODCOM PARA PROMOÇÃO INTELIGENTE. */
                        // Se não for produto combinado, não vai ter NRSEQPRODCOM.
                        $stNrSeqProdCom_Transf = null;
                        if (!empty($itemComanda['NRSEQPRODCOM'])){
                            // Não incrementa se for uma sequência de itens de um mesmo produto combinado.
                            if ($stNrSeqProdCom_Ant != $itemComanda['NRSEQPRODCOM'] || $itemComanda['NRVENDAREST'] != $nrVendaRest_Ant){
                                // Pega o último NRSEQPRODCOM da mesa destino e incrementa.
                                $params = array(
                                    'CDFILIAL' => $cdFilial,
                                    'NRVENDAREST' => $nrVendaRestDestino,
                                    'NRCOMANDA' => $nrComandaDestino,
                                    'NRORG' => $session['NRORG']
                                );
                                $r_NRSEQCOM = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRSEQCOM", $params);
                                $stNrSeqProdCom_Transf = str_pad((string) (intval($r_NRSEQCOM['NRSEQPRODCOM']) + 1), 3, '0', STR_PAD_LEFT);
                            }
                        }

                        // Guarda o NRSEQPRODCOM do produto atual caso o próximo produto a ser inserido faça parte do mesmo produto combinado.
                        $stNrSeqProdCom_Ant = $itemComanda['NRSEQPRODCOM'];
                        $nrVendaRest_Ant = $itemComanda['NRVENDAREST'];

                        /* PARTE 2 - TRANSFERINDO O PEDIDO. */
                        // Garante que o TXPRODCOMVEN seja nulo caso esteja com espaço em branco ou vazio
                        $txprodcomven = $itemComanda['TXPRODCOMVEN'];
                        if (empty($txprodcomven) || $txprodcomven == ' ') $txprodcomven = null;
                        // Gera um novo código de pedido, uma vez que a mesa de destino pode ter pedidos nela.
                        $this->util->newCode('ITCOMANDAVEN' . $cdFilial . $nrComandaDestino);
                        $NRPRODCOMVEN = $this->util->getNewCode('ITCOMANDAVEN' . $cdFilial . $nrComandaDestino, 6);

                        // Prepara o dataset de inserção e transfere para a função de inserção.
                        $insereItemComandaParams = array(
                            'chave'           => $dataset['chave'],
                            'NRPRODCOMVEN'    => $NRPRODCOMVEN,
                            'CDFILIAL'        => $itemComanda['CDFILIAL'],
                            'CDLOJA'          => $itemComanda['CDLOJA'],
                            'CDCAIXACOLETOR'  => $itemComanda['CDCAIXACOLETOR'],
                            'NRVENDAREST'     => $nrVendaRestDestino,
                            'NRCOMANDA'       => $nrComandaDestino,
                            'CDPRODUTO'       => $itemComanda['CDPRODUTO'],
                            'TXPRODCOMVEN'    => $txprodcomven,
                            'NRLUGARMESA'     => str_pad($positionIndex + intval($itemComanda['NRLUGARMESA']), 2, '0', STR_PAD_LEFT),
                            'NRSEQPRODCOM'    => $stNrSeqProdCom_Transf,
                            'IDSTPRCOMVEN'    => $itemComanda['IDSTPRCOMVEN'],
                            'CDGRPOCOR'       => $itemComanda['CDGRPOCOR'],
                            'CDOCORR'         => $itemComanda['CDOCORR'],
                            'mesaOrigem'      => $comanda['NRMESA'],
                            'mesaDestino'     => $comandaPrincipal['NRMESA'],
                            'lojaOrigem'      => $session['CDLOJA'],
                            'NRMESADSCOMORIT' => $comanda['NRMESA'],
                            'IDPRODIMPFIS'    => $itemComanda['IDPRODIMPFIS'],
                            'NRSEQPRODCUP'    => $itemComanda['NRSEQPRODCUP'],
                            'DSCOMANDAORI'    => $itemComanda['DSCOMANDAORI'],
                            'NRCOMANDAORI'    => $itemComanda['NRCOMANDAORI'],
                            'NRPRODCOMORI'    => $itemComanda['NRPRODCOMORI'],
                            'CDVENDEDOR'      => $itemComanda['CDVENDEDOR'],
                            'CDSUPERVISOR'    => null, // $dataset['CDSUPERVISOR'],
                            'CDPRODPROMOCAO'  => $itemComanda['CDPRODPROMOCAO'],
                            'QTPRODCOMVEN'    => $itemComanda['QTPRODCOMVEN'],
                            'VRPRECCOMVEN'    => $itemComanda['VRPRECCOMVEN'],
                            'VRPRECCLCOMVEN'  => $itemComanda['VRPRECCLCOMVEN'],
                            'VRACRCOMVEN'     => $itemComanda['VRACRCOMVEN'],
                            'VRDESCCOMVEN'    => $itemComanda['VRDESCCOMVEN'],
                            'NRPEDIDOFOS'     => $itemComanda['NRPEDIDOFOS'],
                            'CDSENHAPED'      => $itemComanda['CDSENHAPED'],
                            'NRATRAPRODCOVE'  => $itemComanda['NRATRAPRODCOVE'],
                            'IDORIGPEDCMD'    => $itemComanda['IDORIGPEDCMD'],
                            'DSOBSPEDDIGCMD'  => $itemComanda['DSOBSPEDDIGCMD'],
                            'IDPRODREFIL'     => $itemComanda['IDPRODREFIL'],
                            'QTITEMREFIL'     => $itemComanda['QTITEMREFIL'] == null ? null : floatval($itemComanda['QTITEMREFIL']),
                            'DTHRINCOMVEN'    => new \DateTime($itemComanda['DTHRINCOMVEN']),
                            'IDDIVIDECONTA'   => $itemComanda['IDDIVIDECONTA']
                        );
                        $res = $this->insereItComandaVen($insereItemComandaParams);
                        if ($res["funcao"] == 0) {
                            throw new \Exception ("Ocorreu um erro na hora de transferir os produtos.",1);
                        }

                        /* PARTE 3 - INSERINDO E ALTERANDO TABELAS. */
                        // Função para atualizar a tabela ITCOMANDAEST com os novos valores de NRVENDAREST e NRCOMANDA.
                        $this->alteraComandaEst($comanda, $insereItemComandaParams, $itemComanda['NRPRODCOMVEN']);

                        // Atualiza a tabela ITPEDIDOFOSREL com a mesa nova.
                        $params = array(
                            $nrVendaRestDestino,
                            $nrComandaDestino,
                            $NRPRODCOMVEN,
                            $itemComanda['CDFILIAL'],
                            $itemComanda['NRVENDAREST'],
                            $itemComanda['NRCOMANDA'],
                            $itemComanda['NRPRODCOMVEN']
                        );
                        $this->entityManager->getConnection()->executeQuery("SQL_UPDATE_ITPEDIDOFOSREL", $params);

                        $params = array(
                            $itemComanda['CDFILIAL'],
                            $nrVendaRestDestino,
                            $nrComandaDestino,
                            $NRPRODCOMVEN
                        );
                        $paramsForChangingOrderTable = $this->entityManager->getConnection()->fetchAll("SQL_GET_NRPEDIDOFOS", $params);
                        if (!empty($paramsForChangingOrderTable)) {
                            // Atualiza PEDIDOFOS para alterar o número da mesa na caixinha do K-D-S.
                            $params = array(
                                $comandaPrincipal['NRMESA'],
                                $comanda['NRMESA'],
                                $itemComanda['CDFILIAL'],
                                $paramsForChangingOrderTable[0]['NRPEDIDOFOS']
                            );
                            $this->entityManager->getConnection()->executeQuery("SQL_UPDATE_PEDIDOFOS", $params);
                        }
                    }

                    // PARTE 4 - TRATANDO E REMOVENDO REFERÊNCIAS DA MESA DE ORIGEM.
                    // Apagando os itens (ITCOMANDAVEN).
                    $params = array(
                        $cdFilial,
                        $comanda['NRVENDAREST']
                    );
                    $this->entityManager->getConnection()->executeQuery("SQL_DELETA_ITENS_COMANDA_ORIGEM", $params);

                    $positionIndex += floatval($comanda['NRPESMESAVEN']);
                }
            }

            // Insere transferência no log.
			$mesas = implode(", ", $dataset['listaMesas']);
			$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'AGR_MES', $session['CDOPERADOR'], null, "Waiter - Agrupamento de mesa", "Foram agrupadas as seguintes mesas: " . $mesas . ".");
            $connection->commit();
			return array('funcao' => '1');
		}
		catch (\Exception $e){
			Exception::logException($e);
			if($connection != null){
				$connection->rollback();
			}
			throw new \Exception ($e->getMessage(),1);
		}
	}

    private function transferePosicao($CDFILIAL, $origem, $destino, $positionIndex, $NRORG){
        $params = array(
            $CDFILIAL,
            $origem,
            'T',
            'T',
            $NRORG
        );
        $positions = $this->entityManager->getConnection()->fetchAll("GET_CLIENTE_ALL_POSITION", $params);

        $CDCONTADOR = 'POSVENDAREST' . $CDFILIAL . $destino;
        foreach ($positions as $position){
            $this->util->newCode($CDCONTADOR);
            $NRSEQPOS = $this->util->getNewCode($CDCONTADOR, 10);

            $params = array(
                'CMDPRINCIPAL' => $destino,
                'NRSEQPOS' => $NRSEQPOS,
                'POSITION' => str_pad($positionIndex + intval($position['NRLUGARMESA']), 2, '0', STR_PAD_LEFT),
                'CDFILIAL' => $CDFILIAL,
                'NRVENDAREST' => $position['NRVENDAREST'],
                'NRLUGARMESA' => $position['NRLUGARMESA'],
                'NRORG' => $NRORG
            );
            $this->entityManager->getConnection()->executeQuery("TRANSFER_POSVENDAREST", $params);
        }
    }

	public function alteraQtdPessoas($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);

		// Checks if the table exists.
		$valMesa = $this->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $dataset['NRCOMANDA'], $dataset['NRVENDAREST']);
		// Checks if the number of positions can be altered.
		$params = array(
			$session['CDFILIAL'],
			$dataset['NRVENDAREST'],
			$dataset['NRCOMANDA']
		);
		$diffPos = $this->entityManager->getConnection()->fetchAssoc("SQL_DIFFERENT_POSITIONS", $params);

		// Number of positions can't be set to a number less than the number of positions with orders associated to them.
		if (intval($dataset['quantidade']) < intval($diffPos['DIFFPOS'])){
			$result = array('funcao' => '0', 'error' => '254'); //254 - Não foi possível reduzir o número de posições pois existem posições com pedidos associados à elas.
		}
		else {
			// Changes the number of positions of the table.
			$params = array(
				'NRPESMESAVEN'  => $dataset['quantidade'],
				'NRPOSICAOMESA' => $dataset['quantidade'],
				'CDFILIAL'      => $session['CDFILIAL'],
				'NRVENDAREST'   => $valMesa['NRVENDAREST']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_QTD_PESSOAS", $params);

			$result = array('funcao' => '1');
		}

		return $result;
	}

	public function cancelaAberturaMesa($chave, $NRMESA){ //Detaset Params: mesaComanda
		try{
			$connection = null;
			$session = $this->util->getSessionVars($chave);
			$params  = self::buildParamsQueryAberMesa($session, $NRMESA);

			$r_buscaComandas = $this->billService->buscaComandas($chave, $NRMESA);
			if (Empty($r_buscaComandas)) {
				$params['IDSTMESAAUX'] = 'D';
				$this->entityManager->getConnection()->executeQuery("SQL_MUDA_STATUS", $params);
				return array(
					'error' => true,
					'message' => 'Mesa não encontrada.'
				);
			} else {
				$stListaComandas = '';
				foreach($r_buscaComandas as $comanda){
					$stListaComandas .= '_' . $comanda['NRCOMANDA'];
				}
				$stListaComandas .= '_';
				$r_buscaItComanda = self::buscaItensComanda($chave, $stListaComandas);

				$existeProduto = false;
				foreach($r_buscaItComanda as $itcomanda){
					if (($itcomanda['CDPRODUTO'] != $session['CDPRODCOUVER']) && ($itcomanda['CDPRODUTO'] != $session['CDPRODCONSUM'])) {
						$existeProduto = true;
					}
				}

				if (!$existeProduto) {
				   /* Open connection and begin transaction. */
					$connection = $this->entityManager->getConnection();
					$connection->beginTransaction();

					foreach($r_buscaComandas as $comanda){
						$params['NRVENDAREST'] = $comanda['NRVENDAREST'];
						$params['NRCOMANDA']   = $comanda['NRCOMANDA'];
						$params['NRMESA']      = $comanda['NRMESA'];
						$params['IDSTMESAAUX'] = 'D';

						$this->entityManager->getConnection()->executeQuery("SQL_DELETA_ITCOMANDAVEN", $params);
						$this->entityManager->getConnection()->executeQuery("SQL_DELETA_COMANDA_VEN", $params);
						$this->entityManager->getConnection()->executeQuery("SQL_DELETA_VENDA_REST", $params);
						$this->entityManager->getConnection()->executeQuery("SQL_DELETA_POS_VENDA_REST", $params);
						$this->entityManager->getConnection()->executeQuery("SQL_MUDA_STATUS", $params);
						$this->entityManager->getConnection()->executeQuery("SQL_DELETA_ITCOMANDAVENDES", $params);
					}

					$params['NRMESA'] = $NRMESA;
					$r_juncao = $this->entityManager->getConnection()->fetchAll("SQL_VERIFICA_JUNCAO", $params);

					if (!Empty($r_juncao['NRJUNMESA'])){
						$params['NRJUNMESA'] = $r_juncao['NRJUNMESA'];
						$this->entityManager->getConnection()->executeQuery("SQL_DEL_MESA_JUN", $params);
						$this->entityManager->getConnection()->executeQuery("SQL_DEL_JUN_MESA", $params);
					}

					$this->cancelarAgrupamento($session['CDFILIAL'], $session['CDLOJA'], $NRMESA);

					$connection->commit();

					$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'LIB_MES', $session['CDOPERADOR'], "", "Waiter - Cancelamento de abertura", "Cancelamento de Abertura da mesa " . $NRMESA . ".");

					return array(
						'error' => false
					);
				} else if ($session['IDCOLETOR'] !== 'C') {
					// verifica se todos os produtos estão cancelados
					$todosCancelados = true;
					foreach($r_buscaItComanda as $itcomanda){
						if (($itcomanda['CDPRODUTO'] != $session['CDPRODCOUVER']) && ($itcomanda['CDPRODUTO'] != $session['CDPRODCONSUM'])) {
							if ($itcomanda['IDSTPRCOMVEN'] !== '6') {
								$todosCancelados = false;
							}
						}
					}
					if ($todosCancelados) {
						return $this->recebeMesaVazia(
							$session,
							$r_buscaComandas[0]['NRVENDAREST'],
							$r_buscaComandas[0]['NRCOMANDA'],
							$r_buscaComandas[0]['NRMESA']
						);
					} else {
						return array('error' => true, 'message' => 'Operação não permitida. Já foram lançados produtos para esta mesa.');
					}
				} else {
					return array('error' => true, 'message' => 'Operação não permitida. Já foram lançados produtos para esta mesa.');
				}
			}
		} catch(\Exception $e) {
			Exception::logException($e);
			if($connection != null){
				$connection->rollback();
			}
			throw new \Exception($e->getMessage(), 1); //051 - Erro de execução na função.
		}
	}

	private function getIDORIGEMVENDA($IDMODULO, $IDUTCXDRIVETHU) {
		$IDORIGEMVENDA = null;
		switch ($IDMODULO) {
			case 'M':
				$IDORIGEMVENDA = self::IDORIGEMVENDA_TABLE;
				break;
			case 'C':
				$IDORIGEMVENDA = $IDUTCXDRIVETHU === 'S' ? self::IDORIGEMVENDA_DRIVETHU : self::IDORIGEMVENDA_BILL;
				break;
			default:
				$IDORIGEMVENDA = self::IDORIGEMVENDA_REGISTER;
				break;
		}
		return $IDORIGEMVENDA;
	}

	private function recebeMesaVazia($session, $NRVENDAREST, $NRCOMANDA, $NRMESA) {
		$tableData = $this->getTablesFromTableGrouping(
			$session['CDFILIAL'],
			$NRVENDAREST,
			$NRCOMANDA,
			$NRMESA,
			$session['NRORG']
		);
		$openingDate = $this->registerService->getRegisterOpeningDate($session['CDFILIAL'], $session['CDCAIXA']);
		$openingDate = new \DateTime($openingDate['DTABERCAIX']);
		$DTVENDA = new \DateTime();
		$IDORIGEMVENDA = $this->getIDORIGEMVENDA($session['IDMODULO'], $session['IDUTCXDRIVETHU']);
		return $this->vendaAPI->vendaMesa(
			$session['NRORG'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['CDCAIXA'],
			$session['CDVENDEDOR'],
			$session['CDOPERADOR'],
			$openingDate, // DTABERCAIX
			$DTVENDA,
			0, //TOTAL COMANDA
			array(), // TIPORECE
			null, // NMCONSVEND
			null, // NRINSCRCONS
			null, // CDSENHAPED
			null, // VRTROCOVEND
			null, // EMAIL
			null, // VRDESCVENDA_VALOR
			null, // CDCLIENTE
			null, // CDCONSUMIDOR
			false, // simulatePrinter
			false, // simulateSaleValidation
			$tableData,
			array(), // arrayPosicoes
			$IDORIGEMVENDA,
			null // VRTXSEVENDA
		);
	}

    public function getTablesButNotGrouped($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRMESA, $NRORG){
        $arrayTables = array();
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRMESA'   => $NRMESA
        );

        $tableData = $this->entityManager->getConnection()->fetchAssoc("GET_NRVENDAREST_NRCOMANDA", $params);
        $arrayTables = array(array(
            'CDFILIAL' => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'NRCOMANDA' => $NRCOMANDA,
            'NRMESA' => $NRMESA,
            'NRORG' => $NRORG,
            'NRPESMESAVEN' => $tableData['NRPESMESAVEN'],
            'CDVENDEDOR' => $tableData['CDVENDEDOR'],
            'DSCOMANDA' => $tableData['DSCOMANDA'],
            'DTHRMESAFECH' => $tableData['DTHRMESAFECH']
        ));
        return $arrayTables;
    }

	public function getTablesFromTableGrouping($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRMESA, $NRORG){
		$arrayTables = array();
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'NRMESA'   => $NRMESA
		);

		$tableData = $this->entityManager->getConnection()->fetchAssoc("GET_NRVENDAREST_NRCOMANDA", $params);
		$juncaoMesa = $this->entityManager->getConnection()->fetchAssoc("GET_JUNCAOMESA", $params);
		if (!empty($juncaoMesa)) {
			$paramsMesaJuncao = array(
				'CDFILIAL'  => $CDFILIAL,
				'NRJUNMESA' => $juncaoMesa['NRJUNMESA']
			);
			$groupedTables = $this->entityManager->getConnection()->fetchAll("GET_NRMESAJUNCAO", $paramsMesaJuncao);
			foreach ($groupedTables as $currentTable) {
				$paramsNrMesa = array(
					'CDFILIAL' => $CDFILIAL,
					'NRMESA' => $currentTable['NRMESA']
				);
				$tableData = $this->entityManager->getConnection()->fetchAll("GET_NRVENDAREST_NRCOMANDA", $paramsNrMesa);
				$arrayTables = array_merge($arrayTables, $tableData);
			}
		} else {
			$arrayTables = array(array(
				'CDFILIAL' => $CDFILIAL,
				'NRVENDAREST' => $NRVENDAREST,
				'NRCOMANDA' => $NRCOMANDA,
				'NRMESA' => $NRMESA,
				'NRORG' => $NRORG,
				'NRPESMESAVEN' => $tableData['NRPESMESAVEN'],
				'CDVENDEDOR' => $tableData['CDVENDEDOR'],
				'DSCOMANDA' => $tableData['DSCOMANDA'],
				'DTHRMESAFECH' => $tableData['DTHRMESAFECH']
			));
		}
		return $arrayTables;
	}

	public function buildParamsQueryAberMesa($session, $NRMESA){
		return array(
			'CDFILIAL'    => $session['CDFILIAL'],
			'CDLOJA'      => $session['CDLOJA'],
			'NRORG'       => $session['NRORG'],
			'NRMESA'      => $NRMESA,
			'IDSTMESAAUX' => '',
			'NRVENDAREST' => '',
			'NRCOMANDA'   => '',
			'NRJUNMESA'   => ''
		);
	}

	public function buscaItensComanda($chave, $stListaComandas) {
		$session = $this->util->getSessionVars($chave);
		$filial = $session['CDFILIAL'];
		$params = array(
			$filial,
			$stListaComandas
		);
		return $this->entityManager->getConnection()->fetchAll("SQL_ITENS_COMANDAS", $params);
	}

    public function cancelarAgrupamento($CDFILIAL, $CDLOJA, $NRMESA){
        try {
            $connection = null;

            $params = array($CDFILIAL, $CDLOJA, $NRMESA);
            $numJunMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_NUM_JUNC_MESA", $params);
            if (!Empty($numJunMesa)){

                $params = array($CDFILIAL, $CDLOJA, $numJunMesa['NRJUNMESA']);

               /* Open connection and begin transaction. */
                $connection = $this->entityManager->getConnection();
                $connection->beginTransaction();

                //Cria um array com todas as mesas do agrupamento
                $params = array($CDFILIAL, $CDLOJA, $numJunMesa['NRJUNMESA']);
                $listaMesas = $this->entityManager->getConnection()->fetchAll("SQL_NUM_MESAS_AGRUPADAS", $params);
                $params = array(
                    ':CDFILIAL' => $CDFILIAL,
                    ':CDLOJA'   => $CDLOJA,
                    ':NRMESA'   => $NRMESA
                );
                $this->entityManager->getConnection()->executeQuery("SQL_DEL_MESA_JUN", $params);
                $this->entityManager->getConnection()->executeQuery("SQL_DEL_JUN_MESA", $params);

                $connection->commit();

                $mesas = null;
                foreach ($listaMesas as $mesa) {
                  $mesas .= $mesa['NRMESA'] . " ,";
                }

                return array('funcao' => '07');
            }
            else {
                return array('funcao' => '0', 'error' => '051');
            }//051 - Erro de execução na função.
        }
        catch(\Exception $e){
        	Exception::logException($e);
            if($connection != null){
                $connection->rollback();
            }
            throw new \Exception ($e->getMessage(),1); //051 - Erro de execução na função.
        }
    }

	public function disponibilizaMesa($dataset, $nrmesa){

		$session = $this->util->getSessionVars($dataset['chave']);

		//Busca o nrvendarest da mesa
		$params = array($session['CDFILIAL'], $session['CDLOJA'], $nrmesa);
		$nrVendaRest = $this->entityManager->getConnection()->fetchAssoc("SQL_NUM_VEND_REST", $params);

		//Deleta a mesa da tabela vendarest
		$params = array($session['CDFILIAL'], $session['CDLOJA'], $nrmesa);
		$this->entityManager->getConnection()->executeQuery("SQL_DEL_VEND_REST", $params);


		//Deleta a mesa na tabela comandaven
		$params = array($session['CDFILIAL'], $session['CDLOJA'], $nrvendarest['nrvendarest']);
		$this->entityManager->getConnection()->executeQuery("SQL_DEL_COMANDA_VEN", $params);

		//Deleta a mesa na tabela itcomandaven
		$this->entityManager->getConnection()->executeQuery("SQL_DEL_ITCOMANDA_VEN", $params);

		//Atualiza o status da mesa na tabela mesa
		$params = array(
			'IDSTMESAAUX' => 'D',
			'CDFILIAL'    => $session['CDFILIAL'],
			'CDLOJA'      => $session['CDLOJA'],
			'NRMESA'      => $nrmesa
		);

		$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_STATUS_MESA",$params);
	}

	public function consultaMesas($dataset){

		$session = $this->util->getSessionVars($dataset['chave']);
		$params = array(
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['NRCONFTELA'],
			$session['CDFILICONFTE'],
			$session['NRCONFTELA'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['NRCONFTELA'],
			$session['CDFILICONFTE'],
			$session['NRCONFTELA'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['CDFILIAL'],
			$session['CDLOJA']
		);

		$consMesas = $this->entityManager->getConnection()->fetchAll("SQL_CONSULTA_MESA", $params);

		$params = array(
			$session['CDFILIAL'],
			$session['CDLOJA']
		);
		$mesasAgrupadas = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_AGRUPADAS", $params);

		//Determina as mesas que estão sem consumo.
		foreach ($consMesas as &$mesa){
			//Somente mesas que estão abertas => IDSTMESAAUX = 'O'.
			if ($mesa['IDSTMESAAUX'] == 'O'){
				// mesa está ocupada mas não existe nada na VENDAREST, da update para disponível
				$params = array(
					$session['CDFILIAL'],
					$mesa['NRMESA']
				);
				$mesaVendaRest = $this->entityManager->getConnection()->fetchAll("SQL_GET_VENDAREST", $params);
				if (empty($mesaVendaRest)) {
					// caso a mesa sumir (apagar VENDAREST pra baixo), da update para disponível novamente
					$params = array(
						'CDFILIAL' => $session['CDFILIAL'],
						'CDLOJA' => $session['CDLOJA'],
						'NRMESA' => $mesa['NRMESA'],
						'IDSTMESAAUX' => 'D'
					);
					$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_MESAS", $params);
					$mesa['IDSTMESAAUX'] = 'D';
					$mesa['TEMPOCONSUMIR'] = 'N';
				} else {
					$stUltimaVenda = self::buscaUltimaVenda($dataset['chave'], $mesa['NRMESA']);
					if ((!empty($session['NRMINSEMCONS'])) && (!empty($stUltimaVenda))) {
						$mesa['TEMPOCONSUMIR'] = self::tempoSemPedido($session['NRMINSEMCONS'], str_replace('/', '-', $stUltimaVenda));
					} else {
						$mesa['TEMPOCONSUMIR'] = 'N';
					}
				}
			} else {
				$mesa['TEMPOCONSUMIR'] = 'N';
			}
		}

		$result = array();
		foreach ($consMesas as &$mesa){
			$stAgrupada = '';
			$stMesasAgrupadas = array();
			$stNrJunMesa = '';

			//Tratamento das mesas agrupadas.
			foreach ($mesasAgrupadas as $agrup){
				if ($mesa['NRMESA'] === $agrup['NRMESA']){
					$stAgrupada = 'S';
					$stNrJunMesa = $agrup['NRJUNMESA'];

					foreach($mesasAgrupadas as $junta){
						if ($junta['NRJUNMESA'] === $stNrJunMesa) {
							$stMesasAgrupadas[] = $junta['NRMESA'];
						}
					}
				}
			}
			if (Empty($stMesasAgrupadas)){
			  $stAgrupada = 'N';
			}

			$params = array(
				$session['CDFILIAL'],
				$session['CDLOJA'],
				$mesa['NRMESA']
			);

			$dadosMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_DADOS_MESA", $params);

			//Constrói array de retorno.
			$result[$mesa['NRMESA']] = array(
				'CODIGO'                => $mesa['NRMESA'],
				'NMMESA'                => $mesa['NMMESA'],
				'NRCOMANDA'             => $dadosMesa['NRCOMANDA'],
				'NRVENDAREST'           => $dadosMesa['NRVENDAREST'],
				'status'                => $mesa['IDSTMESAAUX'],
				'reserva'               => $mesa['IDCONTROLE'],
				'consumo'               => $mesa['TEMPOCONSUMIR'],
				'agrupada'              => $stAgrupada,
				'pessoas'               => $mesa['NRPESMESAVEN'],
				'NRPOSICAOMESA'         => $mesa['NRPOSICAOMESA'],
				'mesasAgrupadas'        => $stMesasAgrupadas,
				'numerodosagrupamentos' => $stNrJunMesa,
				'CDSALA'                => $mesa['CDSALA'],
				'NMSALA'                => $mesa['NMSALA'],
				'NMVENDEDORABERT'       => $mesa['NMVENDEDORABERT'],
				'IDATRASO'              => 'N'
			);
		}

		$params = array(
			':CDFILIAL' => $session['CDFILIAL']
		);
		$mesasComAtraso = $this->entityManager->getConnection()->fetchAll("GET_TABLES_WITH_DELAYED_ITEMS", $params);
		foreach ($mesasComAtraso as $mesaAtual){
			$result[$mesaAtual['NRMESA']]['IDATRASO'] = 'S';
		}

		$result['funcao'] = '1';
		return $result;
	}

	private function buscaUltimaVenda($chave, $mesa){
		$session = $this->util->getSessionVars($chave);
		$params = array($session['CDFILIAL'], $session['CDLOJA'], $mesa);
		$ultimaVenda = $this->entityManager->getConnection()->fetchAll("SQL_ULTIMA_VENDA", $params);
		if (Empty($ultimaVenda[0]['DTULTIMAVENDA'])){
			$dataDeAbertura = $this->entityManager->getConnection()->fetchAll("SQL_DATA_ABERTURA", $params);
			if (!empty($dataDeAbertura)) {
				return $dataDeAbertura[0]['DTABERTMESA'];
			}
		} else {
			return $ultimaVenda[0]['DTULTIMAVENDA'];
		}
	}

	private function tempoSemPedido($nrminsemcons, $dtUltimaVenda){
		if (!Empty($dtUltimaVenda)){
			$data = new \DateTime($dtUltimaVenda); //Hora atual.
			$diferenca = $data->diff(new \DateTime('NOW')); //Diferença entre hora atual e o tempo de inatividade da mesa.
			$stTempo = $diferenca->format('%H:%I:%S');

			//Tempo configurado para determinar se uma mesa está inativa por muito tempo.
			$inHoras = floor($nrminsemcons / 60);
			$inMinutos = $nrminsemcons % 60;
			$stTempoParam = new \DateTime($inHoras.':'.$inMinutos.':00');
			$stTempoParam = $stTempoParam->format('H:i:s');

			//Comparação final.
			if (strtotime($stTempo) >= strtotime($stTempoParam)) return 'S';
			else return 'N';
		}
		else return 'N';
	}

	public function buscaMesasAgrupadas ($nrcomanda, $nrvendarest) {

		$session = $this->util->getSessionVars(null);
		//Busca o NRMESA relacionado
		$params = array(
			'CDFILIAL' => $session['CDFILIAL'],
			'NRVENDAREST' => $nrvendarest,
			'NRCOMANDA' => $nrcomanda
		);

		$NRMESA = $this->entityManager->getConnection()->fetchAssoc("BUSCA_NRMESA", $params);
		$NRMESA = $NRMESA['NRMESA'];

		//Faz a busca das mesas agrupadas com a mesa selecionada
        $params = array(
            'CDFILIAL' => $session['CDFILIAL'],
            'CDLOJA' => $session['CDLOJA'],
            'NRMESA' => $NRMESA
        );
		$mesasAgrupadas = $this->entityManager->getConnection()->fetchAll("BUSCA_MESAS_AGRUPADAS", $params);

		return $mesasAgrupadas;
	}

	public function dadosMesa($cdfilial, $cdloja, $NRCOMANDA, $NRVENDAREST, $obrigatorio = true){
		$params = array(
			$cdfilial,
			$NRCOMANDA,
			$NRVENDAREST,
			$cdloja
		);
		$dadosMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_MESA_POS", $params);
		if ($dadosMesa == false && $obrigatorio) {
			throw new \Exception($this->waiterMessage->getMessage('004'), 1);
		}
		return $dadosMesa;
	}

	public function insTransfereMesa($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);
		$filial  = $session['CDFILIAL'];
		$loja    = $session['CDLOJA'];

		$mesaOrigem  = $dataset['mesaOrigem'];
		$mesaDestino = $dataset['mesaDestino'];
		$comandas    = $dataset['comandas'];

		$nrVendaRestDestino = $dataset['NRVENDAREST'];
		$nrComandaDestino   = $dataset['NRCOMANDADEST'];
		$params = array(
			$filial,
			$loja,
			$mesaDestino
		);
		$statusMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_STATUS_MESA", $params);

		// Grava os itens na mesa de destino.
		foreach ($comandas as $comanda) {

			$stNrSeqProdCom_Ant = '';
			$nrVendaRest_Ant = '';
			$r_NRSEQCOM = null;

			// Verifica os itens consumidos na mesa de origem.
			$pesquisaItensParams = array(
				$filial,
				$comanda['NRVENDAREST'],
				$comanda['NRCOMANDA']
			);
			$itensComandas = $this->entityManager->getConnection()->fetchAll("SQL_PESQUISA_ITENS_COMANDA", $pesquisaItensParams);

			foreach ($itensComandas as $itemComanda) { // Insere os itens na mesa de destino.

				/* PARTE 1 - EVITANDO REPETIÇÃO DE NRSEQPRODCOM PARA PROMOÇÃO INTELIGENTE. */
				// Se não for produto combinado, não vai ter NRSEQPRODCOM.
				if (!empty($itemComanda['NRSEQPRODCOM'])){
					// Não incrementa se for uma sequência de itens de um mesmo produto combinado.
					if ($stNrSeqProdCom_Ant != $itemComanda['NRSEQPRODCOM'] || $itemComanda['NRVENDAREST'] != $nrVendaRest_Ant){
						// Pega o último NRSEQPRODCOM da mesa destino e incrementa.
						$params = array(
							'CDFILIAL' => $filial,
							'NRVENDAREST' => $nrVendaRestDestino,
							'NRCOMANDA' => $nrComandaDestino,
							'NRORG' => $session['NRORG']
						);
						$r_NRSEQCOM = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRSEQCOM", $params);
						$stNrSeqProdCom_Transf = str_pad((string) (intval($r_NRSEQCOM['NRSEQPRODCOM']) + 1), 3, '0', STR_PAD_LEFT);
					}
				}
				else {
				  // NRSEQPRODCOM para produtos normais é null.
				  $stNrSeqProdCom_Transf = null;
				}
				// Guarda o NRSEQPRODCOM do produto atual caso o próximo produto a ser inserido faça parte do mesmo produto combinado.
				$stNrSeqProdCom_Ant = $itemComanda['NRSEQPRODCOM'];
				$nrVendaRest_Ant = $itemComanda['NRVENDAREST'];


				/* PARTE 2 - TRANSFERINDO O PEDIDO. */
				// Garante que o TXPRODCOMVEN seja nulo caso esteja com espaço em branco ou vazio
				$txprodcomven = $itemComanda['TXPRODCOMVEN'];
				if (empty($txprodcomven) || $txprodcomven == ' ') $txprodcomven = null;
				// Gera um novo código de pedido, uma vez que a mesa de destino pode ter pedidos nela.
				$this->util->newCode('ITCOMANDAVEN' . $filial . $dataset['NRCOMANDADEST']);
				$NRPRODCOMVEN = $this->util->getNewCode('ITCOMANDAVEN' . $filial . $dataset['NRCOMANDADEST'],6);

				// Prepara o dataset de inserção e transfere para a função de inserção.
				$insereItemComandaParams = array(
					'chave'           => $dataset['chave'],
					'NRPRODCOMVEN'    => $NRPRODCOMVEN,
					'CDFILIAL'        => $itemComanda['CDFILIAL'],
					'CDLOJA'          => $itemComanda['CDLOJA'],
					'CDCAIXACOLETOR'  => $itemComanda['CDCAIXACOLETOR'],
					'NRVENDAREST'     => $statusMesa['NRVENDAREST'],
					'NRCOMANDA'       => $statusMesa['NRCOMANDA'],
					'CDPRODUTO'       => $itemComanda['CDPRODUTO'],
					'TXPRODCOMVEN'    => $txprodcomven,
					'NRLUGARMESA'     => $itemComanda['NRLUGARMESA'],
					'NRSEQPRODCOM'    => $stNrSeqProdCom_Transf,
					'IDSTPRCOMVEN'    => $itemComanda['IDSTPRCOMVEN'],
					'CDGRPOCOR'       => $itemComanda['CDGRPOCOR'],
					'CDOCORR'         => $itemComanda['CDOCORR'],
					'mesaOrigem'      => $mesaOrigem,
					'mesaDestino'	  => $mesaDestino,
					'lojaOrigem'      => $loja,
					'NRMESADSCOMORIT' => $mesaOrigem,
					'IDPRODIMPFIS'    => $itemComanda['IDPRODIMPFIS'],
					'NRSEQPRODCUP'    => $itemComanda['NRSEQPRODCUP'],
					'DSCOMANDAORI'    => $itemComanda['DSCOMANDAORI'],
					'NRCOMANDAORI'    => $itemComanda['NRCOMANDAORI'],
					'NRPRODCOMORI'    => $itemComanda['NRPRODCOMORI'],
					'CDVENDEDOR'      => $itemComanda['CDVENDEDOR'],
                    'CDSUPERVISOR'    => $dataset['CDSUPERVISOR'],
					'CDPRODPROMOCAO'  => $itemComanda['CDPRODPROMOCAO'],
					'QTPRODCOMVEN'    => $itemComanda['QTPRODCOMVEN'],
					'VRPRECCOMVEN'    => $itemComanda['VRPRECCOMVEN'],
					'VRPRECCLCOMVEN'  => $itemComanda['VRPRECCLCOMVEN'],
					'VRACRCOMVEN'     => $itemComanda['VRACRCOMVEN'],
					'VRDESCCOMVEN'    => $itemComanda['VRDESCCOMVEN'],
					'NRPEDIDOFOS'     => $itemComanda['NRPEDIDOFOS'],
					'CDSENHAPED'      => $itemComanda['CDSENHAPED'],
					'NRATRAPRODCOVE'  => $itemComanda['NRATRAPRODCOVE'],
					'IDORIGPEDCMD'    => $itemComanda['IDORIGPEDCMD'],
					'DSOBSPEDDIGCMD'  => $itemComanda['DSOBSPEDDIGCMD'],
					'IDPRODREFIL'     => $itemComanda['IDPRODREFIL'],
					'QTITEMREFIL'     => $itemComanda['QTITEMREFIL'] == null ? null : floatval($itemComanda['QTITEMREFIL']),
					'DTHRINCOMVEN'    => new \DateTime($itemComanda['DTHRINCOMVEN']),
					'IDDIVIDECONTA'   => $itemComanda['IDDIVIDECONTA']
				);
				$res = $this->insereItComandaVen($insereItemComandaParams);
				if ($res["funcao"] == 0) {
					throw new \Exception ("Ocorreu um erro na hora de transferir os produtos.",1);
				}

				/* PARTE 3 - INSERINDO E ALTERANDO TABELAS. */
				// Função para atualizar a tabela ITCOMANDAEST com os novos valores de NRVENDAREST e NRCOMANDA.
				$this->alteraComandaEst($comanda, $insereItemComandaParams, $itemComanda['NRPRODCOMVEN']);

				// Atualiza a tabela ITPEDIDOFOSREL com a mesa nova.
				$params = array(
					$statusMesa['NRVENDAREST'],
					$statusMesa['NRCOMANDA'],
					$NRPRODCOMVEN,
					$itemComanda['CDFILIAL'],
					$itemComanda['NRVENDAREST'],
					$itemComanda['NRCOMANDA'],
					$itemComanda['NRPRODCOMVEN']
				);
				$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_ITPEDIDOFOSREL", $params);

				$params = array(
					$itemComanda['CDFILIAL'],
					$statusMesa['NRVENDAREST'],
					$statusMesa['NRCOMANDA'],
					$NRPRODCOMVEN
				);
				$paramsForChangingOrderTable = $this->entityManager->getConnection()->fetchAll("SQL_GET_NRPEDIDOFOS", $params);
				if (!empty($paramsForChangingOrderTable)) {
					// Atualiza PEDIDOFOS para alterar o número da mesa na caixinha do K-D-S.
					$params = array(
						$mesaDestino,
						$mesaOrigem,
						$itemComanda['CDFILIAL'],
						$paramsForChangingOrderTable[0]['NRPEDIDOFOS']
					);
					$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_PEDIDOFOS", $params);
				}

			}

			// PARTE 4 - TRATANDO E REMOVENDO REFERÊNCIAS DA MESA DE ORIGEM.
			// Apagando os itens (ITCOMANDAVEN).
			$params = array(
				$filial,
				$comanda['NRVENDAREST']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_DELETA_ITENS_COMANDA_ORIGEM", $params);

			// Deleta a COMANDAVEN da mesa de origem.
			$params = array(
				$filial,
				$loja,
				$comanda['NRVENDAREST']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_DELETA_COMANDA_ORIGEM", $params);

			// Deleta a VENDAREST da mesa de origem.
			$params = array(
				$filial,
				$loja,
				$comanda['NRVENDAREST']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_DELETA_VENDA_ORIGEM", $params);

			// Coloca a mesa de origem como disponível.
			$params = array(
				':IDSTMESAAUX' =>  'D'               ,
				':CDFILIAL'    =>  $filial           ,
				':CDLOJA'      =>  $loja             ,
				':NRMESA'      =>  $comanda['NRMESA']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_MESAS", $params);
		}

		// Coloca a mesa de destino como ocupada.
		$params = array(
			':IDSTMESAAUX' =>  'O'               ,
			':CDFILIAL'    =>  $filial           ,
			':CDLOJA'      =>  $loja             ,
			':NRMESA'      =>  $mesaDestino
		);
		$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_MESAS", $params);


		// PARTE 5 - TRATANDO AGRUPAMENTOS.
		// Verifica se a mesa de origem está agrupada
		$params = array(
			':CDFILIAL' => $filial,
			':CDLOJA'   => $loja,
			':NRMESA'   => $mesaOrigem
		);
		$r_verifica_juncao = $this->entityManager->getConnection()->fetchAssoc("SQL_VERIFICA_JUNCAO", $params);

		// Caso a mesa esteja em algum agrupamento, o agrupamento deve ser apagado.
		if (!empty($r_verifica_juncao)) {
			$params = array(
				$filial,
				$loja,
				$r_verifica_juncao['NRJUNMESA'],
			);
			$this->entityManager->getConnection()->executeQuery("SQL_DELETA_MESA_JUNCAO", $params);

			$params = array(
				$filial,
				$loja,
				$r_verifica_juncao['NRJUNMESA']
			);
			$this->entityManager->getConnection()->fetchAll("SQL_DELETA_JUNCAO_MESA", $params);
		}

		return array('funcao' => '1');
	}

	public function alteraComandaEst($antigo, $novo, $nrProdComVen){
		$params = array(
			":NRVENDAREST"      => $novo['NRVENDAREST'],
			":NRCOMANDA"        => $novo['NRCOMANDA'],
			":NRPRODCOMVEN"     => $novo['NRPRODCOMVEN'],
			":OLDNRVENDAREST"   => $antigo['NRVENDAREST'],
			":OLDNRCOMANDA"     => $antigo['NRCOMANDA'],
			":OLDNRPRODCOMVEN"  => $nrProdComVen
		);
        $observations = $this->entityManager->getConnection()->fetchAll("SQL_GET_OBSERVATIONS_EST", $params);
        $this->entityManager->getConnection()->executeQuery("SQL_DELETE_OBSITCOMANDAEST", $params);
		$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_ITCOMANDAVENEST", $params);
        foreach ($observations as $observation){
            $params = array(
                $observation['CDFILIAL'],
                $novo['NRVENDAREST'],
                $novo['NRCOMANDA'],
                $novo['NRPRODCOMVEN'],
                $observation['CDPRODUTO'],
                $observation['CDGRPOCOR'],
                $observation['CDOCORR']
            );
            $this->entityManager->getConnection()->executeQuery("SQL_INS_OBSITCOMANDAEST", $params);
        }
	}

	public function transfereMesa($dataset){
		try {
			$session = $this->util->getSessionVars($dataset['chave']);
			$filial  = $session['CDFILIAL'];
			$loja    = $session['CDLOJA'];

			$mesaDestino = $dataset['mesaDestino'];

			// Validates the origin table and gets its data.
			$valMesaOrigem = $this->dadosMesa($filial, $loja, $dataset['NRCOMANDA'], $dataset['NRVENDAREST']);

			$mesaOrigem      = $valMesaOrigem['NRMESA'];
			$pessoasOri      = $valMesaOrigem['NRPESMESAVEN'];
			$stCdCliente     = $valMesaOrigem['CDCLIENTE'];
			$nrVendaRestOrig = $valMesaOrigem['NRVENDAREST'];

			// Validates the destination table and gets its data.
			$params = array(
				$mesaDestino,
				$filial
			);
			$valMesaDestino = $this->entityManager->getConnection()->fetchAll("SQL_VALIDA_MESA_ABERTA", $params);

			// Open connection and begin transaction.
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			/******************** VALIDATION ********************/
			// Tables transfer can't ocurr if either table is about to pay the bill, so we validate this here.
			/* CHECKS IF THE ORIGIN TABLE IS AVAILABLE FOR TRANSFER */
			$params = array(
				$filial,
				$mesaOrigem,
				$loja
			);
			$valTrans = $this->entityManager->getConnection()->fetchAll("SQL_VAL_TRANS", $params);
			if (empty($valTrans)){
				$connection->rollback();
				return array('funcao' => '0', 'error' => '445');
			}

            /* CHECK FOR ITEMS IN MOVCAIXADLV */
            $params = array(
                'CDFILIAL' => $filial,
                'NRVENDAREST' => $nrVendaRestOrig
            );
            $valTrans = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_MOVCAIXADLV", $params);
            if (!empty($valTrans)){
                $connection->rollback();
                return array('funcao' => '0', 'error' => '462');
            }

			/* CHECKS IF THE DESTINATION TABLE IS AVAILABLE FOR TRANSFER */
			$params = array(
				$filial,
				$mesaDestino,
				$loja
			);
			$valTrans = $this->entityManager->getConnection()->fetchAll("SQL_VAL_TRANS", $params);
			if (empty($valTrans)){
				$connection->rollback();
				return array('funcao' => '0', 'error' => '446');
			}
			/**************** END OF VALIDATION *****************/

			/*************** BEGIN TRANSFER CASES ***************/
			if (empty($valMesaDestino)){ /* DESTINATION TABLE IS AVAILABLE */

				// Opens the destination table.
				$openTableDataset = array(
					'chave'        => $dataset['chave'],
					'mesa'         => $mesaDestino,
					'quantidade'   => $pessoasOri,
					'CDCLIENTE'    => $stCdCliente,
					'CDCONSUMIDOR' => null,
					'CDVENDEDOR'   => null
				);
				$tableCheck = $this->abreMesa($openTableDataset);

				if ($tableCheck["funcao"] == "0"){
					throw new \Exception ('Erro ao abrir a mesa selecionada.', 1);
				}

				// Gets data from the destination table.
				$valMesaDestino = $this->dadosMesa($filial, $loja, $tableCheck['NRCOMANDA'], $tableCheck['NRVENDAREST']);

				$nrVendaRestDest = $valMesaDestino['NRVENDAREST'];
				$nrComandaDest = $valMesaDestino['NRCOMANDA'];
			}
			else { /* DESTINATION TABLE WAS ALREADY OPEN */
				$valMesaDestino = $valMesaDestino[0];
				$nrVendaRestDest = $valMesaDestino['NRVENDAREST'];
				$nrComandaDest = $valMesaDestino['NRCOMANDA'];
				// Makes sure the number of positions match.
				if ($valMesaDestino['NRPESMESAVEN'] > $pessoasOri) $number = $valMesaDestino['NRPESMESAVEN'];
				else $number = $pessoasOri;
				$this->changePositionsQuantity($valMesaDestino, $number);
			}

			// Gets the orders associated to the origin table.
			$params = array(
				$filial,
				$loja,
				$mesaOrigem,
				$filial,
				$loja,
				$filial,
				$loja,
				$filial,
				$loja,
				$mesaOrigem
			);
			$comandas = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_COMANDAS", $params);
			if(!empty($comandas)) {
				$listaComandas = '';
				foreach ($comandas as $comanda) {
					$listaComandas = $listaComandas . '_' . $comanda['NRCOMANDA'];
				}
				$listaComandas = $listaComandas . '_';
			}

			// Transfers the products over to the destination table.
			$params = array(
				'chave'         => $dataset['chave'],
				'filial'        => $filial,
				'mesaOrigem'    => $mesaOrigem,
				'mesaDestino'   => $mesaDestino,
				'NRCOMANDADEST' => $nrComandaDest,
				'NRVENDAREST'   => $nrVendaRestDest,
				'comandas'      => $comandas,
				'listaComandas' => $listaComandas,
				'quantidadePes' => $pessoasOri,
                'CDSUPERVISOR'  => $dataset['CDSUPERVISOR']
			);
			$res = $r_transfere_mesa = $this->insTransfereMesa($params);

			// reconstroi as caixas do KDS para sumir com os pedidos "deletados"
			$this->entityManager->getConnection()->executeQuery("SQL_REBUILD_KDS");

			if ($res["funcao"] == "1") $connection->commit();
			else throw new \Exception ("Erro ao transferir os produtos para a mesa de destino.", 1);

			if (isset($r_transfere_mesa['error'])) throw new \Exception ("TransfereMesa", 1);
			if (!$res) $res = array('funcao' => '1');

            $this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'TRA_MES', $session['CDOPERADOR'], $dataset['CDSUPERVISOR'], "Waiter - Transferência de Mesa", "Transferência da mesa " . $valMesaOrigem['NRMESA'] . " para mesa " . $valMesaDestino['NRMESA'] . ".");

			return $res;
		} catch(\Exception $e) {
			Exception::logException($e);
			if (!empty($connection)) $connection->rollback();
			throw new \Exception ($e->getMessage(),1);
		}
	}

	public function changePositionsQuantity($tableData, $positions){
		$params = array(
			$positions,
			$positions,
			$tableData['CDFILIAL'],
			$tableData['NRVENDAREST']
		);
	   $this->entityManager->getConnection()->executeQuery("SQL_CHANGE_POSITIONS_QUANTITY", $params);
	}

	public function reOpenTable($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);

		$params = array(
			'NRMESA' => $dataset['mesa'],
			'CDFILIAL' => $session['CDFILIAL']
		);

		$dadosMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_DADOS", $params);

		//Parametro 'R' retira o produto Couvert e consumação da ITCOMANDAVEN
		$this->controlaCouvert($dataset['chave'], $dadosMesa['NRVENDAREST'], $dadosMesa['NRCOMANDA'], 'R');
		$this->controlaConsumacao($dataset['chave'], $dadosMesa['NRVENDAREST'], $dadosMesa['NRCOMANDA'], 'R');

		$params = array(
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$dataset['mesa'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$dataset['mesa']
		);

		$this->entityManager->getConnection()->executeQuery("SQL_SET_STATUS_MESA", $params);
		$params = array(
			'DTHRMESAFECH' => null,
			'CDFILIAL' => $session['CDFILIAL'],
			'NRVENDAREST' => $dadosMesa['NRVENDAREST'],
			'NRMESA' => $dataset['mesa']
		);
		$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_HR_FECHAMENTO_MESA", $params);
		$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'REA_MES', $session['CDOPERADOR'], null, "Waiter - Reabertura de mesa", "Reabertura da mesa " . $dataset['mesa'] . ".");

		//return 0;
		return array('funcao' => '1');
	}

	public  function separarMesas($dataset) {
		try {
			$connection = null;
			$session  = $this->util->getSessionVars($dataset['chave']);
			$cdFilial = $session['CDFILIAL'];
			$cdLoja   = $session['CDLOJA'];
			$listaMesas = $dataset['listaMesas'];

			// valida e busca dados da mesa
			$valMesa = $this->dadosMesa($cdFilial, $cdLoja, $dataset['NRCOMANDA'], $dataset['NRVENDAREST']);
			$nrMesa = $valMesa['NRMESA'];

		    /* Open connection and begin transaction. */
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			// busca número da junção
			$params = array($cdFilial, $cdLoja, $nrMesa);
			$nrJuncaoMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_NRJUNCAOMESA", $params);

			foreach ($listaMesas as $mesa){
				// separa mesa por mesa
				$params = array($cdFilial, $cdLoja, $nrJuncaoMesa['NRJUNMESA'], $mesa);
				$this->entityManager->getConnection()->executeQuery("SQL_SEPARA", $params);
			}

			// busca agrupamento
			$params = array($cdFilial, $cdLoja, $nrJuncaoMesa['NRJUNMESA']);
			$r_agrupada = $this->entityManager->getConnection()->fetchAll("SQL_AGRUPADA", $params);

			// se sobrou somente a mesa principal, apaga o agrupamento
			if (count($r_agrupada) === 1) {
				// apaga o registro na MESAJUNCAO
				$params = array($cdFilial, $cdLoja, $nrJuncaoMesa['NRJUNMESA']);
				$this->entityManager->getConnection()->executeQuery("SQL_DELETA_MESAJUNCAO", $params);

				// apaga o registro na JUNCAOMESA
				$params = array($cdFilial, $cdLoja, $nrJuncaoMesa['NRJUNMESA']);
				$this->entityManager->getConnection()->executeQuery("SQL_DELETA_JUNCAOMESA", $params);
			}

			$mesas = null;
			foreach ($dataset['listaMesas'] as $mesa){
				$mesas .= $mesa . ", ";
			}
			$mesas = rtrim($mesas, ", ");
			$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'SEP_MES', $session['CDOPERADOR'], null, "Waiter - Separação de mesas", "Separação das mesas " . $mesas . " da junção " . $nrJuncaoMesa['NRJUNMESA'] . ".");
			$connection->commit();
			return array('funcao' => '1');

		} catch (\Exception $e) {
			Exception::logException($e);
			if($connection != null){
				$connection->rollback();
			}
			throw new \Exception ($e->getMessage(),1);
		}
	}

	public function setTable($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);
		$params = array(
			$dataset["NRMESA"],
			$session["CDFILIAL"],
			$dataset["NRVENDAREST"]
		);
		return $this->entityManager->getConnection()->executeQuery("SQL_SET_TABLE", $params);
	}


	public function transfereProduto($dataset){
		try {
			$connection = null;
			$r_dadosItemComanda = array();
			$session = $this->util->getSessionVars($dataset['chave']);

			// Valida e busca os dados da mesa origem.
			$valMesaOrigem = $this->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $dataset['produtos'][0]['NRCOMANDA'], $dataset['produtos'][0]['NRVENDAREST']);

			// Mesa destino.
			$mesaDestino = $dataset['mesaDestino'];

			// Valida e busca os dados da mesa destino.
			$params = array(
				$mesaDestino,
				$session['CDFILIAL']
			);
			$valMesaDest = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_MESA_ABERTA", $params);
			if (empty($valMesaDest)){
				// Abre a mesa caso ela não esteja aberta.
				$abreMesaDataset = array(
					'chave'        => $dataset['chave'],
					'mesa'         => $mesaDestino,
					'quantidade'   => $dataset['maxPosicoes'],
					'CDCLIENTE'    => $valMesaOrigem['CDCLIENTE'],
					'CDCONSUMIDOR' => $valMesaOrigem['CDCONSUMIDOR']
				);
				$this->abreMesa($abreMesaDataset);
				$validaAbertura = $this->entityManager->getConnection()->fetchAll("SQL_VALIDA_MESA_ABERTA", $params);
				$valMesaDest = $this->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $validaAbertura[0]['NRCOMANDA'], $validaAbertura[0]['NRVENDAREST']);
			}
			$stNrVendaRestDest = $valMesaDest['NRVENDAREST'];
			$stNrComandaDest = $valMesaDest['NRCOMANDA'];

			/* Open connection and begin transaction. */
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			/******************* VALIDAÇÕES ********************/

            /* VALIDA SE MESA DESTINO ESTÁ DISPONÍVEL PARA TRANSFERÊNCIA */
            $params = array(
                $session['CDFILIAL'],
                $dataset['mesaDestino'],
                $session['CDLOJA']
            );
            $valTrans = $this->entityManager->getConnection()->fetchAll("SQL_VAL_TRANS", $params);

            if (empty($valTrans)){
                $connection->rollback();
                return array('funcao' => '0', 'error' => '446');
            }

            /* CHECK FOR ITEMS IN MOVCAIXADLV */
            $params = array(
                'CDFILIAL' => $session['CDFILIAL'],
                'NRVENDAREST' => $valMesaOrigem['NRVENDAREST']
            );
            $valTrans = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_MOVCAIXADLV", $params);
            if (!empty($valTrans)){
                $connection->rollback();
                return array('funcao' => '0', 'error' => '462');
            }

			foreach($dataset['produtos'] as $produto){
				$stNrVendaRestOri = $produto['NRVENDAREST'];
				$stNrComandaOri   = $produto['NRCOMANDA'];

                /* VALIDA SE MESA ORIGEM ESTÁ DISPONÍVEL PARA TRANSFERÊNCIA */
				$params = array(
					$stNrVendaRestOri,
					$stNrComandaOri
				);
				$NRMESA = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRMESA", $params);

				if (!empty($NRMESA)){
					$NRMESA = $NRMESA['NRMESA'];

					$params = array(
						$session['CDFILIAL'],
						$NRMESA,
						$session['CDLOJA']
					);
					$valTrans = $this->entityManager->getConnection()->fetchAll("SQL_VAL_TRANS", $params);

					if (empty($valTrans)){
						$connection->rollback();
						return array('funcao' => '0', 'error' => '445');
					}
				}

                /* VALIDA SE O CLIENTE/CONSUMIDOR DO DESTINO É DIFERENTE DO DE ORIGEM */
                /* CLIENTE/CONSUMIDOR DA MESA */
                $params = array(
                    'NRVENDAREST' => $produto['NRVENDAREST'],
                    'NRCOMANDA' => $produto['NRCOMANDA'],
                    'NRPRODCOMVEN' => $produto['NRPRODCOMVEN']
                );
                $positionDetailsOri = $this->entityManager->getConnection()->fetchAssoc("GET_POSITION_CLIENT", $params);
                if ($positionDetailsOri['CDCLIENTE'] == null && $positionDetailsOri['CDCONSUMIDOR'] == null){
                    $positionDetailsOri['CDCLIENTE'] = $valMesaOrigem['CDCLIENTE'];
                    $positionDetailsOri['CDCONSUMIDOR'] = $valMesaOrigem['CDCONSUMIDOR'];
                }

                /* CLIENTE/CONSUMIDOR DA POSIÇÃO DE DESTINO */
                $params = array(
                    'CDFILIAL' => $session['CDFILIAL'],
                    'NRVENDAREST' => $valMesaDest['NRVENDAREST'],
                    'NRLUGARMESA' => $dataset['posicao']
                );
                $positionDetailsDest = $this->entityManager->getConnection()->fetchAssoc("BUSCA_NOME_POR_POSICAO_NULL", $params);
                if ($positionDetailsDest['CDCLIENTE'] == null && $positionDetailsDest['CDCONSUMIDOR'] == null){
                    $positionDetailsDest['CDCLIENTE'] = $valMesaDest['CDCLIENTE'];
                    $positionDetailsDest['CDCONSUMIDOR'] = $valMesaDest['CDCONSUMIDOR'];
                }

                if (($valMesaOrigem['CDCLIENTE'] != $valMesaDest['CDCLIENTE'] || $valMesaOrigem['CDCONSUMIDOR'] != $valMesaDest['CDCONSUMIDOR']) && $valMesaOrigem['NRMESA'] != $valMesaDest['NRMESA']){
                    throw new \Exception("Não é possível transferir produtos para uma mesa que possui cliente/consumidor diferente dos produtos selecionados.");
                }
                if ($positionDetailsOri['CDCLIENTE'] != $positionDetailsDest['CDCLIENTE'] || $positionDetailsOri['CDCONSUMIDOR'] != $positionDetailsDest['CDCONSUMIDOR']){
                    throw new \Exception("Não é possível transferir produtos para uma posição que possui cliente/consumidor diferente dos produtos selecionados.");
                }

			}

			/******************* FIM DAS VALIDAÇÕES ********************/

			$posicao = $dataset['posicao'];
			if (empty($posicao)) $posicao = '01';
			$posicao = str_pad($posicao, 2, '0', STR_PAD_LEFT);

			$groupedTables = $this->getGroupedTables($dataset['chave'], $session['CDFILIAL'], $session['CDLOJA'], $stNrVendaRestDest, $stNrComandaDest);

			$params = array(
				$session['CDFILIAL'],
				$groupedTables['stVendaRest'],
				$groupedTables['stMesa']
			);

			$r_nrpessoas = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_POSICAO", $params);

			$NRPESMESAVEN = floatval($r_nrpessoas['NRPESMESAVEN']);

			// Valida posição para ver se existe.
			if ($posicao > $NRPESMESAVEN){
				throw new \Exception ("Operação bloqueada. Posição não existe para mesa de destino.",1);

				$params = array(
					'NRPESMESAVEN'  => $NRPESMESAVEN,
					'NRPOSICAOMESA' => $NRPESMESAVEN,
					'CDFILIAL'      => $session['CDFILIAL'],
					'NRVENDAREST'   => $stNrVendaRestDest
				);
				$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_QTD_PESSOAS", $params);
			}

			$stNrSeqProdCom_Ant = '';
			$nrVendaRest_Ant = '';
			$r_NRSEQCOM = null;
            $arrayDeProdutos = ''; // For logs.

			/* INICIO DA TRANSFERÊNCIA DE PRODUTOS. */
			foreach($dataset['produtos'] as $produto){

				$stNrVendaRestOri = $produto['NRVENDAREST'];
				$stNrComandaOri   = $produto['NRCOMANDA'];
				$stNrProdComVenOri = $produto['NRPRODCOMVEN'];

				// Transferência normal (mesas diferentes).
				if ($stNrVendaRestOri !== $stNrVendaRestDest){

					// Busca os dados do produto de origem na mesa de origem.
					$params = array(
						$session['CDFILIAL'],
						$stNrVendaRestOri,
						$stNrComandaOri,
						$stNrProdComVenOri,
						$session['CDFILIAL'],
						$stNrVendaRestOri,
						$stNrComandaOri,
						$stNrProdComVenOri
					);
					$r_dadosItemComanda = $this->entityManager->getConnection()->fetchAll("SQL_DADOS_ITEM_COMANDA", $params);

					//$doQtdeTransferida = number_format($produto['quantidade'], 0);
					$doQtdeTransferida = floatval(str_replace(',', '.', $produto['quantidade']));

					$this->util->newCode('ITCOMANDAVEN' . $session['CDFILIAL'] . $stNrComandaDest);
					$stNrProdComVen_Aux = $this->util->getNewCode('ITCOMANDAVEN' . $session['CDFILIAL'] . $stNrComandaDest, 6);

					/* EVITA REPETIÇÃO DE NRSEQPRODCOM PARA PROMOÇÃO INTELIGENTE. */
					// Se não for produto combinado, não vai ter NRSEQPRODCOM.
					if (!empty($r_dadosItemComanda[0]['NRSEQPRODCOM'])) {

						// Não incrementa se for uma sequência de itens de um mesmo produto combinado.
						if ($stNrSeqProdCom_Ant != $r_dadosItemComanda[0]['NRSEQPRODCOM'] || $produto['NRVENDAREST'] != $nrVendaRest_Ant){

							// Pega o último NRSEQPRODCOM da mesa destino e incrementa.
							$params = array(
								'CDFILIAL' => $r_dadosItemComanda[0]['CDFILIAL'],
								'NRVENDAREST' => $valMesaDest['NRVENDAREST'],
								'NRCOMANDA' => $valMesaDest['NRCOMANDA'],
								'NRORG' => $session['NRORG']
							);
							$r_NRSEQCOM = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRSEQCOM", $params);
							$stNrSeqProdCom_Transf = str_pad((string) (intval($r_NRSEQCOM['NRSEQPRODCOM']) + 1), 3, '0', STR_PAD_LEFT);
						}
					}
					else {
						// NRSEQPRODCOM para produtos normais é null.
						$stNrSeqProdCom_Transf = null;
					}

					// Início do tratamento da inserção dos produtos.
					// ---------------------------------------------------------
					foreach ($r_dadosItemComanda as &$itemComanda){
						if(!empty($r_dadosItemComanda)){

							// Insere um novo registro na comanda nova.
							if ($itemComanda['IDSTPRCOMVEN'] === 'S'){
								$params = array($itemComanda['CDFILIAL'], $itemComanda['CDLOJA'], $itemComanda['CDPRODUTO']);
								$r_ProdLoja = $this->entityManager->getConnection()->fetchAll("SQL_PROD_LOJA", $params);
							}

							if ($itemComanda['IDSTPRCOMVEN'] === 'S' && $r_ProdLoja['CDSETOR'] != '') $IDSTPRCOMVEN = '1';
							else if ($itemComanda['IDSTPRCOMVEN'] === 'N') $IDSTPRCOMVEN = '4';
							else $IDSTPRCOMVEN = $itemComanda['IDSTPRCOMVEN'];

							if (empty($itemComanda['IDPRODIMPFIS'])) $IDPRODIMPFIS = 'N';
							else $IDPRODIMPFIS = $itemComanda['IDPRODIMPFIS'];

							$txprodcomven = $itemComanda['TXPRODCOMVEN'];
							if (empty($itemComanda['TXPRODCOMVEN']) || $itemComanda['TXPRODCOMVEN'] == ' ') $txprodcomven = null;

							$params = array(
							   "CDFILIAL"       => $itemComanda['CDFILIAL'],
							   "NRVENDAREST"    => $valMesaDest['NRVENDAREST'],
							   "NRCOMANDA"      => $valMesaDest['NRCOMANDA'],
							   "NRPRODCOMVEN"   => $stNrProdComVen_Aux,
							   "CDPRODUTO"      => $itemComanda['CDPRODUTO'],
							   "QTPRODCOMVEN"   => floatval($doQtdeTransferida),
							   "VRPRECCOMVEN"   => floatval($itemComanda['VRPRECCOMVEN']),
							   "TXPRODCOMVEN"   => $txprodcomven,
							   "IDSTPRCOMVEN"   => $IDSTPRCOMVEN,
							   "VRDESCCOMVEN"   => floatval($itemComanda['VRDESCCOMVEN']),
							   "NRLUGARMESA"    => str_pad($posicao, 2, '0', STR_PAD_LEFT),
							   "NRMESAORIG"     => $valMesaOrigem['NRMESA'],
							   "CDLOJAORIG"     => $itemComanda['CDLOJA'],
							   "DTHRINCOMVEN"   => new \DateTime($itemComanda['DTHRINCOMVEN']),
							   "IDPRODIMPFIS"   => $IDPRODIMPFIS,
							   "CDLOJA"         => $session['CDLOJA'],
							   "NRSEQPRODCOM"   => $stNrSeqProdCom_Transf,
							   "NRSEQPRODCUP"   => $itemComanda['NRSEQPRODCUP'],
							   "VRACRCOMVEN"    => floatval($itemComanda['VRACRCOMVEN']),
							   "DSCOMANDAORI"   => $itemComanda['DSCOMANDAORI'],
							   "NRCOMANDAORI"   => $stNrComandaOri,
							   "NRPRODCOMORI"   => $stNrProdComVenOri,
							   "CDCAIXACOLETOR" => $itemComanda['CDCAIXACOLETOR'],
							   "VRPRECCLCOMVEN" => floatval($itemComanda['VRPRECCLCOMVEN']),
							   "CDPRODPROMOCAO" => $itemComanda['CDPRODPROMOCAO'],
							   "CDVENDEDOR"     => $itemComanda['CDVENDEDOR'],
							   "NRPEDIDOFOS"    => $itemComanda['NRPEDIDOFOS'],
							   "CDFILIALPED"    => $itemComanda['CDFILIAL'],
							   "CDSENHAPED"     => $itemComanda['CDSENHAPED'],
							   "NRATRAPRODCOVE" => $itemComanda['NRATRAPRODCOVE'],
							   "IDORIGPEDCMD"   => $itemComanda['IDORIGPEDCMD'],
							   "DSOBSPEDDIGCMD" => $itemComanda['DSOBSPEDDIGCMD'],
							   "IDPRODREFIL"    => $itemComanda['IDPRODREFIL'],
							   "QTITEMREFIL"    => $itemComanda['QTITEMREFIL'] == null ? null : floatval($itemComanda['QTITEMREFIL']),
							   "IDDIVIDECONTA"  => $itemComanda['IDDIVIDECONTA'],
                               "CDSUPERVISOR"   => $dataset['CDSUPERVISOR']
							);
							$this->entityManager->getConnection()->executeQuery("SQL_INSERE_ITEM_COMANDA_VEN", $params);
							$this->alteraComandaEst($itemComanda, $params, $r_dadosItemComanda[0]['NRPRODCOMVEN']);

                            $arrayDeProdutos .= $itemComanda['CDPRODUTO'] . ', ';

							// Trata transferência no KDS.
							// Passo 1: Gera novo pedido (PEDIDOFOS).
							$this->splitItemFromOrderKDS(
								$itemComanda['CDFILIAL'],
								$stNrVendaRestOri,
								$stNrComandaOri,
								$stNrProdComVenOri,
								$valMesaDest['NRVENDAREST'],
								$valMesaDest['NRCOMANDA'],
								$stNrProdComVen_Aux,
								$mesaDestino,
								$itemComanda['NRSEQPRODCUP'],
								floatval($posicao)
							);

							if (floatval($itemComanda['QTPRODCOMVEN']) - floatval($doQtdeTransferida) == 0) {
								// apaga o registro na mesa antiga
								$params = array($itemComanda['CDFILIAL'], $itemComanda['NRVENDAREST'], $itemComanda['NRCOMANDA'], $itemComanda['NRPRODCOMVEN']);
								$this->entityManager->getConnection()->executeQuery("SQL_DEL_PROD", $params);
							}
							else {
								// altera a quantidade de produtos na comanda velha
								$params = array(
									number_format($itemComanda['QTPRODCOMVEN'], 0) - number_format($doQtdeTransferida, 0),
									$itemComanda['CDFILIAL'],
									$itemComanda['NRCOMANDA'],
									$itemComanda['NRPRODCOMVEN'],
									$itemComanda['NRVENDAREST']
								);
								$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_QUANTIDADE", $params);

								//Parametros para a função
								$params = array(
								  'QT'           => number_format($itemComanda['QTPRODCOMVEN'], 0) - number_format($doQtdeTransferida, 0),
								  'CDFILIAL'     => $itemComanda['CDFILIAL'],
								  'NRPEDIDOFOS'  => $itemComanda['NRPEDIDOFOS'],
								  'NRPRODCOMVEN' => $itemComanda['NRPRODCOMVEN'],
								  'NRVENDAREST'  => $itemComanda['NRVENDAREST']
								);

								//Função para atualizar a tabela ITPEDIDOFOS com os novos valores
								//$this->alteraPedidoFos($params);
							}

							// Guarda o NRSEQPRODCOM do produto atual caso o próximo produto a ser inserido faça parte do mesmo produto combinado.
							$stNrSeqProdCom_Ant = $itemComanda['NRSEQPRODCOM'];
							$nrVendaRest_Ant = $itemComanda['NRVENDAREST'];
						}

					}
				}
				// Se for somente mudar o produto de posição (transferir para a mesma mesa).
				else {
					// Atualiza a posição na ITCOMANDAVEN.
					$params = array(
						str_pad($dataset['posicao'], 2, '0', STR_PAD_LEFT),
						$session['CDFILIAL'],
						$stNrVendaRestOri,
						$stNrComandaOri,
						$stNrProdComVenOri
					);
					$this->entityManager->getConnection()->executeQuery("SQL_UPD_POS_MESA", $params);

					// Atualiza a posição no KDS.
					$params = array(
						$session['CDFILIAL'],
						$stNrVendaRestOri,
						$stNrComandaOri,
						$stNrProdComVenOri
					);
					$itensITPEDIDOFOS = $this->entityManager->getConnection()->fetchAll("GET_ITPEDIDOFOS_BY_ITPEDIDOFOSREL", $params);

					// Para cada item a ser trocado de posição, deve-se procurar a chave dele na ITPEDIDOFOS
					foreach ($itensITPEDIDOFOS as $oldItem) {
						$params = array(
							floatval($dataset['posicao']),
							$oldItem['CDFILIAL'],
							$oldItem['NRPEDIDOFOS'],
							$oldItem['NRITPEDIDOFOS']
						);
						$this->entityManager->getConnection()->executeQuery("SQL_UPD_POS_KDS", $params);
					}
				}
			}

			$connection->commit();

            $this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'TRA_PRO', $session['CDOPERADOR'], $dataset['CDSUPERVISOR'], "Waiter - Transferência de Produto", "Transferência de produtos da mesa " . $valMesaOrigem['NRMESA'] . " para mesa " . $mesaDestino . ". Produtos: " . substr($arrayDeProdutos, 0, strlen($arrayDeProdutos) - 2) . ".");

			return array('funcao' => '1');

		} catch(\Exception $e) {
			Exception::logException($e);
			if ($connection != null) $connection->rollback();
			throw new \Exception ($e->getMessage(),1); // Erro de execução na função.
		}
	}

	private function splitItemFromOrderKDS($CDFILIAL, $oldNRVENDAREST, $oldNRCOMANDA, $oldNRPRODCOMVEN, $NRVENDAREST, $NRCOMANDA, $NRPRODCOMVEN, $NRMESA, $NRSEQPRODCUP, $NRLUGARMESAIT){
		$params = array(
			$CDFILIAL,
			$oldNRVENDAREST,
			$oldNRCOMANDA,
			$oldNRPRODCOMVEN,
			$NRVENDAREST,
			$NRCOMANDA,
			$NRPRODCOMVEN,
			$NRMESA,
			$NRSEQPRODCUP,
			$NRLUGARMESAIT
		);

		$this->entityManager->getConnection()->executeQuery("INS_PEDIDOALT", $params);
	}


	private function getGroupedTables($chave, $CDFILIAL, $CDLOJA, $NRVENDAREST, $NRCOMANDA){
		// valida e busca os dados da mesa
		$valMesa = $this->dadosMesa($CDFILIAL, $CDLOJA, $NRCOMANDA, $NRVENDAREST);
		$stNrVendaRest = $valMesa['NRVENDAREST'];
		$stNrComanda = $valMesa['NRCOMANDA'];
		$stNrMesa = $valMesa['NRMESA'];
		// Retorna todas as mesas de um agrupamento.
		$r_groupedTables = $this->entityManager->getConnection()->fetchAll("SQL_GET_GROUPED_TABLES", array($valMesa['NRMESA']));
		// Formata as mesas.
		$groupedTables = array();
		foreach ($r_groupedTables as $table){
			$temp = array(
			   'NRCOMANDA' => $table['NRCOMANDA'],
			   'NRVENDAREST' => $table['NRVENDAREST'],
			   'NRMESA' => $table['NRMESA']
			);
			array_push($groupedTables, $temp);
		}

		if (Empty($groupedTables)){
			$stComandaVens = "_".$stNrComanda."_";
			$stVendaRest = "_".$stNrVendaRest."_";
			$stMesa = "_".$stNrMesa."_";
		}else {
			$stComandaVens = "_";
			$stVendaRest = "_";
			$stMesa = "_";
			foreach ($groupedTables as $mesa){
				$dadosMesa = $this->dadosMesa($CDFILIAL, $CDLOJA, $mesa['NRCOMANDA'], $mesa['NRVENDAREST']);
				$stComandaVens .= $dadosMesa['NRCOMANDA']."_";
				$stVendaRest .= $dadosMesa['NRVENDAREST']."_";
				$stMesa .= $dadosMesa['NRMESA']."_";
			}
		}
		return array("stVendaRest" => $stVendaRest, "stMesa" => $stMesa);
	}

	public function valAbertura($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);
		$IDSTMESAAUX = $dataset['tipo'];

        // Busca mesa agrupada e retorna a principal (menor NRVENDAREST).
        $mesaPrincipal = $this->buscaMesaPrincipal($session['CDFILIAL'], $session['CDLOJA'], $dataset['mesa']);
        if (!empty($mesaPrincipal)) $dataset['mesa'] = $mesaPrincipal['NRMESA'];

		$params = array(
			$session['CDFILIAL'],
			$dataset['mesa']
		);
		$dados = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCAVENDAREST", $params);
		if (empty($dados)) {
			$IDSTMESAAUX = 'D';
			// Caso a mesa sumir (apagar VENDAREST pra baixo), Daniel pediu para dar update para disponível novamente.
			$params = array(
				'CDFILIAL' => $session['CDFILIAL'],
				'CDLOJA' => $session['CDLOJA'],
				'NRMESA' => $dataset['mesa'],
				'IDSTMESAAUX' => 'D'
			);
			$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_MESAS", $params);
		}else{
			$validaOperador = $this->validaOperador($session, $dados);
			if($validaOperador['error']){
				return $validaOperador;
			}
		}
		$dadosMesa = $this->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $dados['NRCOMANDA'], $dados['NRVENDAREST'], false);


		$tipoFrontEnd = $dataset['tipo'];
		if (count($dadosMesa) == 0){
			$mesa = array('IDSTMESAAUX' => '');
		} else{
			$mesa = $dadosMesa;
		}
		$situacao = '';

		if ($mesa['IDSTMESAAUX'] === 'S') { // mesa solicitada
			$situacao = "SOLICITADA";
		} else if ($mesa['IDSTMESAAUX'] === 'R'){
			$situacao = "RECEBIMENTO";
		} else if($mesa['IDSTMESAAUX'] === 'P'){
			$situacao = "PAGA";
		} else {
			if ($tipoFrontEnd == 'D') { // mesa está disponivel para abertura
				if(count($dadosMesa) === 0) {
					$situacao = "OK";
				} else {
					$situacao = "ABERTA";
				}
			} else if ($tipoFrontEnd == 'O') { // mesa já está ocupada
				if (count($dadosMesa) !== 0) {
					$situacao = "OK";
				} else {
					$situacao = "DISPONIVEL";
				}
			} else if ($tipoFrontEnd === 'S') { // mesa está com a conta solicitada
				if (count($dadosMesa) != 0) {
					if ($dadosMesa['IDSTMESAAUX'] == 'D') {
						$situacao = "DISPONIVEL";
					} else {
						$situacao = "OCUPADA";
					}
				}
			} else if ($tipoFrontEnd === 'R') { // mesa está reservada
				if (count($dadosMesa) == 0) {
					$situacao = "DISPONIVEL";
				}
			} else if ($tipoFrontEnd === 'P') { // mesa está paga
				if (count($dadosMesa) != 0) {
					if ($dadosMesa['IDSTMESAAUX'] == 'D') {
						$situacao = "DISPONIVEL";
					} else {
						$situacao = "OCUPADA";
					}
				}
			}
		}

		/* NRJUNMESA caso ela estiver agrupada. */
		$params = array(
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$dataset['mesa']
		);
		$nrJunMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRJUNMESA_ABERTURA", $params);

		// if table wasn't found in VENDAREST, get table data anyway from MESA table
		if (empty($dadosMesa)) {
			$params = array(
				$session['CDFILIAL'],
				$session['CDLOJA'],
				$dataset['mesa']
			);
			$dadosMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_DADOS_MESA", $params);
		}

		$result = array(
			'error'         => false,
			'retorno'       => $situacao,
			'NRPESMESAVEN'  => $dadosMesa['NRPESMESAVEN'],
			'CDSALA'        => $dadosMesa['CDSALA'],
			'NRMESA'        => $dataset['mesa'],
            'NMMESA'        => $dadosMesa['NMMESA'],
			'NRVENDAREST'   => $dadosMesa['NRVENDAREST'],
			'NRCOMANDA'     => $dadosMesa['NRCOMANDA'],
			'CDCLIENTE'     => $dadosMesa['CDCLIENTE'],
			'NMRAZSOCCLIE'  => $dadosMesa['NMRAZSOCCLIE'],
			'CDCONSUMIDOR'  => $dadosMesa['CDCONSUMIDOR'],
			'NMCONSUMIDOR'  => $dadosMesa['NMCONSUMIDOR'],
			'CDVENDEDOR'    => $dadosMesa['CDVENDEDOR'],
			'NRJUNMESA'     => $nrJunMesa['NRJUNMESA'],
			'NRCPFRESPCON'  => $dadosMesa['NRCPFRESPCON'],
			'NRPOSICAOMESA' => $dadosMesa['NRPOSICAOMESA'],
            'currentStatus' => $dadosMesa['IDSTMESAAUX'],
			'IDSTMESAAUX'   => $IDSTMESAAUX,
            'NMVENDEDORABERT' => isset($dadosMesa['NMVENDEDORABERT']) ? $dadosMesa['NMVENDEDORABERT'] : ""
		);

		return $result;
	}

	public function validaOperador($dadosSessao, $dadosMesa){
		$params = array(
			':CDFILIAL' => $dadosSessao['CDFILIAL'],
			':CDLOJA'   => $dadosSessao['CDLOJA']
		);
		$IDUTLSENHAOPER = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_IDUTLSENHAOPER", $params)['IDUTLSENHAOPER'];
		if($IDUTLSENHAOPER == 'S' && $dadosSessao['CDVENDEDOR'] != $dadosMesa['CDVENDEDOR']){
			return array(
				'error' => true,
				'message' => 'Impossível comandar para essa mesa. Mesa aberta por: '.$dadosMesa['NMFANVEN']
			);
		}else{
			return array(
				'error' => false
			);
		}
	}

	public function insereItComandaVen($dataset) {
		try {
			$session = $this->util->getSessionVars($dataset['chave']);
			$filial  = $session['CDFILIAL'];
			$loja    = $session['CDLOJA'];

			$mesaOrigem  = $dataset['mesaOrigem'];

			if($dataset['IDSTPRCOMVEN'] == 'S') {
				$prodLojaParams = array(
					$filial,
					$loja,
					$dataset['CDPRODUTO']
				);
				$resultProdLoja = $this->entityManager->getConnection()->fetchAll("SQL_PROD_LOJA", $prodLojaParams);
			}

			$txprodcomven = $dataset['TXPRODCOMVEN'];
			if (Empty($txprodcomven) || $txprodcomven == ' ')
				$txprodcomven = null;

			if (($dataset['IDSTPRCOMVEN'] == 'S') and ($resultProdLoja[0]['CDSETOR'] != '')) {
				$P_IDSTPRCOMVEN = '1';
			} else if ($dataset['IDSTPRCOMVEN'] == 'N') {
				$P_IDSTPRCOMVEN = '4';
			} else {
				$P_IDSTPRCOMVEN = $dataset['IDSTPRCOMVEN'];
			}

			if (empty($dataset['IDPRODIMPFIS'])) {
				$P_IDPRODIMPFIS = 'N';
			} else {
				$P_IDPRODIMPFIS = $dataset['IDPRODIMPFIS'];
			}

			$insItemParams = array(
				'CDFILIAL'       => $dataset['CDFILIAL'],
				'NRVENDAREST'    => $dataset['NRVENDAREST'],
				'NRCOMANDA'      => $dataset['NRCOMANDA'],
				'NRPRODCOMVEN'   => $dataset['NRPRODCOMVEN'],
				'CDPRODUTO'      => $dataset['CDPRODUTO'],
				'QTPRODCOMVEN'   => floatval($dataset['QTPRODCOMVEN']),
				'VRPRECCOMVEN'   => floatval($dataset['VRPRECCOMVEN']),
				'TXPRODCOMVEN'   => $txprodcomven,
				'IDSTPRCOMVEN'   => $P_IDSTPRCOMVEN,
				'VRDESCCOMVEN'   => floatval($dataset['VRDESCCOMVEN']),
				'NRLUGARMESA'    => str_pad($dataset['NRLUGARMESA'], 2, '0', STR_PAD_LEFT),
				'NRMESAORIG'     => $dataset['mesaOrigem'],
				'CDLOJAORIG'     => $dataset['lojaOrigem'],
				'DTHRINCOMVEN'   => $this->handleDTHRINCOMVEN($dataset['DTHRINCOMVEN']),
				'IDPRODIMPFIS'   => $P_IDPRODIMPFIS,
				'CDLOJA'         => $dataset['CDLOJA'],
				'NRSEQPRODCOM'   => $dataset['NRSEQPRODCOM'],
				'NRSEQPRODCUP'   => $dataset['NRSEQPRODCUP'],
				'VRACRCOMVEN'    => floatval($dataset['VRACRCOMVEN']),
				'DSCOMANDAORI'   => $dataset['DSCOMANDAORI'],
				'NRCOMANDAORI'   => $dataset['NRCOMANDAORI'],
				'NRPRODCOMORI'   => $dataset['NRPRODCOMORI'],
				'CDCAIXACOLETOR' => $dataset['CDCAIXACOLETOR'],
				'VRPRECCLCOMVEN' => floatval($dataset['VRPRECCLCOMVEN']),
				'CDPRODPROMOCAO' => $dataset['CDPRODPROMOCAO'],
				'CDVENDEDOR'     => $dataset['CDVENDEDOR'],
                'CDSUPERVISOR'   => $dataset['CDSUPERVISOR'],
				'NRPEDIDOFOS'    => $dataset['NRPEDIDOFOS'],
				'CDFILIALPED'    => $dataset['CDFILIAL'],
				'CDSENHAPED'     => $dataset['CDSENHAPED'],
				'NRATRAPRODCOVE' => $dataset['NRATRAPRODCOVE'],
				'IDORIGPEDCMD'   => $dataset['IDORIGPEDCMD'],
				'DSOBSPEDDIGCMD' => $dataset['DSOBSPEDDIGCMD'],
				'IDPRODREFIL'    => $dataset['IDPRODREFIL'],
				'QTITEMREFIL'    => $dataset['QTITEMREFIL'] == null ? null : floatval($dataset['QTITEMREFIL']),
				'IDDIVIDECONTA'  => $dataset['IDDIVIDECONTA']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_INSERE_ITEM_COMANDA_VEN", $insItemParams);

			return array('funcao' => '1');
		} catch (\Exception $e) {
			Exception::logException($e);
			throw new \Exception ($e->getMessage(),1); // Erro de execução na função.
		}
	}

	private function handleDTHRINCOMVEN($DTHRINCOMVEN) {
		if (empty($DTHRINCOMVEN)) {
			$DTHRINCOMVEN = new \DateTime();
		} else {
			if (is_string($DTHRINCOMVEN)) {
				try {
					$DTHRINCOMVEN = new \DateTime($DTHRINCOMVEN);
				} catch(\Exception $e) {
					$DTHRINCOMVEN = new \DateTime();
					$this->util->log($e->getMessage());
				}
			}
		}
		return $DTHRINCOMVEN;
	}


	public function controlaCouvert($chave, $nrvendarest, $nrcomanda, $modo, $modoHabilitado = null){
		//Modo pode ser: I-INSERE R-RETIRA C-CONSULTA
		$session = $this->util->getSessionVars($chave);

		$totalCouvert = array();
		if ($modo !== 'R') {
			$mesasAgrupadas = $this->buscaMesasAgrupadas($nrcomanda, $nrvendarest);
			$mesasAgrupadas = $this->filtraMesasAgrupadas($modoHabilitado, $mesasAgrupadas, $nrvendarest, $nrcomanda);

			foreach ($mesasAgrupadas as &$mesa) {
				$params = array(
					'CDFILIAL'     => $session['CDFILIAL'],
					'NRCOMANDA'    => $mesa['NRCOMANDA'],
					'NRVENDAREST'  => $mesa['NRVENDAREST'],
					'CDPRODUTO'    => $session['CDPRODCOUVER']
				);
				$couvert = $this->entityManager->getConnection()->fetchAll("VERIFICA_COUVERT", $params);
				$ultCouvert = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_COMISSAO_VENDA", $params);

                $params = array(
                    $session['CDFILIAL'],
                    $mesa['NRVENDAREST'],
                    'T',
                    'T',
                    $session['NRORG']
                );
                $posicoesMesa = $this->entityManager->getConnection()->fetchAll("GET_CLIENTE_ALL_POSITION", $params);
                $posicoesMesa = array_column($posicoesMesa, null, 'NRLUGARMESA');

				if ($modo == 'C' && $ultCouvert['IDUTILCOUVERT'] == 'N') {
					return $totalCouvert = [];
				}
				if ($modo == 'I') {
					$mesa['NRPOSICAOMESA'] = $mesa['NRPESMESAVEN'];
				}
				// se não existir o produto couvert, inclui um produto, caso já exista, este sera atualizado.
				if ((bool)$couvert === false) {
					$params = array(
						'CDFILIAL'     => $session['CDFILIAL'],
						'NRCOMANDA'    => $mesa['NRCOMANDA'],
						'NRVENDAREST'  => $mesa['NRVENDAREST'],
						'CDLOJA'       => $session['CDLOJA']
					);
					$posicoesPagas = $this->entityManager->getConnection()->fetchAll("VERIFICA_POSICOES_PAGAS", $params);
					$NRPESMESAVENIT = !is_null($posicoesPagas) ? array_column($posicoesPagas, 'NRPESMESAVENIT') : null;
					for ($i = 1; $i <= intval($mesa['NRPOSICAOMESA']); $i++) {
						$nrLugarMesa = str_pad($i, 2, '0', STR_PAD_LEFT);
						if (!in_array($i, $NRPESMESAVENIT)){
	                        if (isset($posicoesMesa[$nrLugarMesa])){
	                            $CDCLIENTE = $posicoesMesa[$nrLugarMesa]['CDCLIENTE'];
	                            $CDCONSUMIDOR = $posicoesMesa[$nrLugarMesa]['CDCONSUMIDOR'];
	                        }
	                        else {
	                            $CDCLIENTE = $mesa['CDCLIENTE'];
	                            $CDCONSUMIDOR = $mesa['CDCONSUMIDOR'];;
	                        }
	                        $buscaPreco = $this->precoService->buscaPreco($session['CDFILIAL'], $CDCLIENTE, $session['CDPRODCOUVER'], $session['CDLOJA'], $CDCONSUMIDOR);

	                        if ($buscaPreco["error"]) $buscaPreco['PRECO'] = $session['PRECOCOUVERT'];

	                        $PRECOCOUVERT = floatval(bcsub(str_replace(',','.',strval($buscaPreco['PRECO'] + $buscaPreco['PRECOCLIE'] + $buscaPreco['ACRE'])), str_replace(',','.',strval($buscaPreco['DESC'])), '2'));
	                        $totalCouvert[$nrLugarMesa] = $PRECOCOUVERT;

							if ($modo == 'I') {
								$this->util->newCode('ITCOMANDAVEN' . $session['CDFILIAL'] . $mesa['NRCOMANDA']);
								$NRPRODCOMVEN = $this->util->getNewCode('ITCOMANDAVEN' . $session['CDFILIAL'] . $mesa['NRCOMANDA'], 6);

								$params = array(
									$session['CDFILIAL'],
									$mesa['NRVENDAREST'],
									$mesa['NRCOMANDA'],
									$NRPRODCOMVEN,
									$session['CDPRODCOUVER'],
									1,
									$buscaPreco['PRECO'],
									'4',
									$buscaPreco['DESC'],
									date('d-m-Y H:i:s'),
									'Produto Couvert Artistico - Incluido OdhenPOS',
									$nrLugarMesa,
									$buscaPreco['ACRE'],
									$session['CDLOJA'],
									null,
									'MOB',
									null,
									$buscaPreco['PRECOCLIE']
								);
								$this->entityManager->getConnection()->executeQuery("SQL_INSERE_COUVERT", $params);
							}
						}
						else {
							$totalCouvert[$nrLugarMesa] = 0;
						}
					}
				} else {
					$couvert = array_column($couvert, null, 'NRLUGARMESA');
					for ($i = 1; $i <= intval($mesa['NRPOSICAOMESA']); $i++) {
						$nrLugarMesa = str_pad($i, 2, '0', STR_PAD_LEFT);

						if (isset($couvert[$nrLugarMesa])) {
							$totalCouvert[$nrLugarMesa] = $couvert[$nrLugarMesa]['PRECOCOUVERT'];
						} else {
							$totalCouvert[$nrLugarMesa] = 0;
						}
					}
				}
			}
		} else {
			$this->retiraCouvert($chave, $nrvendarest, $nrcomanda, $modoHabilitado);
		}
		return $totalCouvert;
	}

	private function retiraCouvert($chave, $nrvendarest, $nrcomanda, $modoHabilitado) {

		$session = $this->util->getSessionVars($chave);

		$mesasAgrupadas = $this->buscaMesasAgrupadas($nrcomanda, $nrvendarest);
		$mesasAgrupadas = $this->filtraMesasAgrupadas($modoHabilitado, $mesasAgrupadas, $nrvendarest, $nrcomanda);

		//Retira o Couvert de todas as mesas do agrupamento
		foreach ($mesasAgrupadas as &$mesa) {
			$params = array(
				'CDFILIAL'      => $session['CDFILIAL'],
				'NRCOMANDA'     => $mesa['NRCOMANDA'],
				'NRVENDAREST'   => $mesa['NRVENDAREST'],
				'CDPRODUTO'     => $session['CDPRODCOUVER'],
				'IDUTILCOUVERT' => 'S'
			);

			$this->entityManager->getConnection()->executeQuery("SQL_RETIRA_ITEM", $params);
			$this->entityManager->getConnection()->executeQuery("SQL_ULTILIZA_COUVERT", $params);
		}
	}

	public function filtraMesasAgrupadas($modoHabilitado, $mesasAgrupadas, $nrvendarest, $nrcomanda) {
		if ($modoHabilitado === 'C' && sizeof($mesasAgrupadas) > 1) {
			$mesasAgrupadas = array_filter($mesasAgrupadas, function($dadosMesa) use($nrvendarest, $nrcomanda) {
				return $dadosMesa['NRVENDAREST'] == $nrvendarest && $dadosMesa['NRCOMANDA'] == $nrcomanda;
			}, ARRAY_FILTER_USE_BOTH);
		}
		return $mesasAgrupadas;
	}


	public function controlaConsumacao($chave, $nrvendarest, $nrcomanda, $modo) {
		//Modo pode ser: I-INSERE R-RETIRA C-CONSULTA
		$dadosCaixa = $this->paramsService->dadosCaixa($chave);
		$dadosCaixa = $dadosCaixa[0];
		$session = $this->util->getSessionVars($chave);
		$modoHabilitado = $session['IDMODULO'];

		$valoresConsumacao = array();

		if ($modo != 'R') {
			$mesasAgrupadas = $this->buscaMesasAgrupadas($nrcomanda, $nrvendarest);
			$mesasAgrupadas = $this->filtraMesasAgrupadas($modoHabilitado, $mesasAgrupadas, $nrvendarest, $nrcomanda);
			foreach ($mesasAgrupadas as &$mesa) {
				//Pega o produto da consumação de acordo com o sexo do consumidor
				if ($mesa['IDSEXOCONS'] == "F") {
					$cdProdutoConsuma = $dadosCaixa['CDPRODCONSUF'];
				} else {
					$cdProdutoConsuma = $dadosCaixa['CDPRODCONSUM'];
				}
				if ($modo == 'I') {$mesa['NRPOSICAOMESA'] = $mesa['NRPESMESAVEN'];}

				$valoresConsumacao = $this->buscaPrecoConsumacao($session, $mesa, $cdProdutoConsuma);

				if ($modo == 'I') {
					foreach ($valoresConsumacao as $nrLugarMesa => $valor) {
						if ($valor > 0) {
							$this->insereItConsumacao(
								$chave,
								$cdProdutoConsuma,
								$valor,
								$mesa['NRCOMANDA'],
								$mesa['NRVENDAREST'],
								$nrLugarMesa
							);
						}
					}
				}
			}
		} else {
			$this->retiraConsumacao($chave, $nrvendarest, $nrcomanda, $modoHabilitado);
		}

		return $valoresConsumacao;
	}

	private function atualizaComandaVen($valor, $filial, $nrcomanda, $nrvendarest) {
		//Atualiza o campo VRCONSUMAMIN na tabela COMANDAVEN depois de inserir
		$params = array(
			$valor,
			$filial,
			$nrcomanda,
			$nrvendarest
		);
		$this->entityManager->getConnection()->executeQuery("SQL_ATUALIZA_CONSUMACAO_MINIMA", $params);
	}

	private function insereItConsumacao($chave, $cdProdutoConsuma, $precoConsumacao, $nrComanda, $nrVendaRest, $nrLugarMesa) {
		$session = $this->util->getSessionVars($chave);

		if (!empty($cdProdutoConsuma)) {
			$this->util->newCode('ITCOMANDAVEN'.$session['CDFILIAL'].$nrComanda);
			$nrProdComVen = $this->util->getNewCode('ITCOMANDAVEN'.$session['CDFILIAL'].$nrComanda, 6);

			$params = array(
				"chave"           => $chave,
				"NRPRODCOMVEN"    => $nrProdComVen,
				"CDFILIAL"        => $session['CDFILIAL'],
				"CDLOJA"          => $session['CDLOJA'],
				"CDCAIXACOLETOR"  => $session['CDCAIXA'],
				"NRVENDAREST"     => $nrVendaRest,
				"NRCOMANDA"       => $nrComanda,
				"CDPRODUTO"       => $cdProdutoConsuma,
				'TXPRODCOMVEN'    => 'Produto consumacao minima -  Incluido Waiter.',
				'NRLUGARMESA'     => $nrLugarMesa,
				'NRSEQPRODCOM'    => null,
				'IDSTPRCOMVEN'    => '4',
				'CDGRPOCOR'       => null,
				'CDOCORR'         => null,
				'mesaOrigem'      => null,
				'lojaOrigem'      => null,
				'NRMESADSCOMORIT' => null,
				'IDPRODIMPFIS'    => null,
				'NRSEQPRODCUP'    => null,
				'DSCOMANDAORI'    => null,
				'NRCOMANDAORI'    => $nrComanda,
				'NRPRODCOMORI'    => null,
				'CDVENDEDOR'      => $session['CDVENDEDOR'],
				'CDPRODPROMOCAO'  => null,
				'QTPRODCOMVEN'    => 1,
				'VRPRECCOMVEN'    => floatval($precoConsumacao),
				'VRPRECCLCOMVEN'  => '0',
				'VRACRCOMVEN'     => '0',
				'VRDESCCOMVEN'    => '0',
				'NRATRAPRODCOVE'  => 0,
				'IDORIGPEDCMD'    => 'MOB',
				'DSOBSPEDDIGCMD'  => null,
				'NRPEDIDOFOS'     => null,
				'CDSENHAPED'      => null,
				'IDPRODREFIL'     => 'N',
				'QTITEMREFIL'     => null,
				'DTHRINCOMVEN'    => new \DateTime(),
				'IDDIVIDECONTA'  => 'N'
			);

			$res = $this->insereItComandaVen($params);
			if ($res["funcao"] == 0) throw new \Exception ("Consumacao:136",1);
		}
	}

	private function retiraConsumacao($chave, $nrvendarest, $nrcomanda, $modoHabilitado) {
		$session = $this->util->getSessionVars($chave);

		$mesasAgrupadas = $this->buscaMesasAgrupadas($nrcomanda, $nrvendarest);
		$mesasAgrupadas = $this->filtraMesasAgrupadas($modoHabilitado, $mesasAgrupadas, $nrvendarest, $nrcomanda);

		//Retira o Couvert de todas as mesas do agrupamento
		foreach ($mesasAgrupadas as &$mesa) {
			$params = array(
				'CDFILIAL'      => $session['CDFILIAL'],
				'NRCOMANDA'     => $mesa['NRCOMANDA'],
				'NRVENDAREST'   => $mesa['NRVENDAREST'],
				'CDPRODUTO'     => $session['CDPRODCONSUM']
			);

			$this->entityManager->getConnection()->executeQuery("SQL_RETIRA_ITEM", $params);

			$this->atualizaComandaVen(0, $session['CDFILIAL'], $mesa['NRCOMANDA'], $mesa['NRVENDAREST']);
		}
	}

	private function buscaPrecoConsumacao($session, $mesa, $cdProdutoConsuma) {
		// busca preço do produto consumação
		$r_retornaPreco = $this->precoService->buscaPreco($session['CDFILIAL'], $session['CDCLIENTE'], $cdProdutoConsuma, $session['CDLOJA'], '');
		$precoConsumacao = $r_retornaPreco['PRECO'] + $r_retornaPreco['ACRE'] - $r_retornaPreco['DESC'];

		// busca valores na comanda
		$valoresComanda = $this->buscaValorComandaPorPessoa($mesa['NRCOMANDA'], $mesa['NRVENDAREST'], $cdProdutoConsuma, $session);

		$vrComanda = $valoresComanda['VALORPORPESSOA'];
		$vrConsumacao = $valoresComanda['VALORCONSUMACAO'];

		$consumacaoTotal = array();
		for ($i = 1; $i <= intval($mesa['NRPOSICAOMESA']); $i++) {
			$key = str_pad($i, 2, '0', STR_PAD_LEFT);
			$valorPessoa = isset($vrComanda[$key]) ? $vrComanda[$key] : 0;

			if ($mesa['IDSTMESAAUX'] === 'R' && $valorPessoa === 0) {
				if (isset($vrConsumacao[$key])) {
					$valorConsumacao = $precoConsumacao;
				} else {
					$valorConsumacao = 0;
				}
			} else {
				$valorConsumacao = $precoConsumacao - $valorPessoa;
				$valorConsumacao = $valorConsumacao < 0 ? 0 : $valorConsumacao;
			}

			$consumacaoTotal[$key] = $valorConsumacao;
		}

		return $consumacaoTotal;
	}

	private function buscaValorComandaPorPessoa($nrComanda, $nrVendaRest, $cdProdutoConsuma, $session) {
		$prodsNaoConsumacao = array($cdProdutoConsuma);
		if ($session['IDCOUVERART'] === 'S') {
			$prodsNaoConsumacao[] = $session['CDPRODCOUVER'];
		}

		$params = array(
			'NRCOMANDA'   => $nrComanda,
			'NRVENDAREST' => $nrVendaRest,
			'CDPRODUTO'   => $prodsNaoConsumacao
		);

		$types = array(
			'NRCOMANDA'   => \PDO::PARAM_STR,
			'NRVENDAREST' => \PDO::PARAM_STR,
			'CDPRODUTO'  => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
		);

		$valoresPorPessoa = $this->entityManager->getConnection()->fetchAll('SQL_BUSCA_VALORCOMANDA', $params, $types);
		$valoresPorPessoa = array_column($valoresPorPessoa, 'VRPRECCOMVEN', 'NRLUGARMESA');

		$params['CDPRODUTO'] = $cdProdutoConsuma;
		$valoresConsumacao = $this->entityManager->getConnection()->fetchAll('SQL_BUSCA_VALORCOMANDA_CONSUMACAO', $params);
		$valoresConsumacao = array_column($valoresConsumacao, 'VRPRECCOMVEN', 'NRLUGARMESA');

		return array('VALORPORPESSOA' => $valoresPorPessoa, 'VALORCONSUMACAO' => $valoresConsumacao);
	}

    public function getPositionControlDetails($CDFILIAL, $NRVENDAREST, $CDOPERADOR){
        $params = array(
            'CDFILIAL'    => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'CDOPERADOR'  => $CDOPERADOR
        );
        $positionControl = $this->entityManager->getConnection()->fetchAll('GET_POSITION_CONTROL', $params);
        $result = array();
        foreach ($positionControl as $position){
            array_push($result, $position['NRLUGARMESA']);
        }
        return $result;
    }

    public function getLockedPositions($CDFILIAL, $NRVENDAREST, $CDOPERADOR){
        $params = array(
            'CDFILIAL'    => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'CDOPERADOR'  => $CDOPERADOR
        );
        $lockedPositions = $this->entityManager->getConnection()->fetchAll('GET_LOCKED_POSITIONS', $params);
        $result = array();
        foreach ($lockedPositions as $position){
            array_push($result, $position['NRLUGARMESA']);
        }
        return $result;
    }

    public function resetPositionControl($CDFILIAL, $NRVENDAREST, $CDOPERADOR){
        $params = array(
            'CDFILIAL'    => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'CDOPERADOR' => $CDOPERADOR
        );
        $this->entityManager->getConnection()->executeQuery('RESET_POSITION_CONTROL', $params);
    }

    public function insertPositionControl($CDFILIAL, $NRVENDAREST, $CDOPERADOR, $position){
        $positionControl = self::getPositionControlDetails($CDFILIAL, $NRVENDAREST, $CDOPERADOR);
        if (array_search(str_pad($position, 2, '0', STR_PAD_LEFT), $positionControl) !== false){
            return 'Está posição já está sendo recebida.';
        }

        $params = array(
            'CDFILIAL'    => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'CDOPERADOR'  => $CDOPERADOR,
            'NRLUGARMESA' => str_pad($position, 2, '0', STR_PAD_LEFT)
        );
        $this->entityManager->getConnection()->executeQuery('INSERT_POSITION_CONTROL', $params);

        return null;
    }

    public function deletePositionControl($CDFILIAL, $NRVENDAREST, $CDOPERADOR, $position){
        $params = array(
            'CDFILIAL'    => $CDFILIAL,
            'NRVENDAREST' => $NRVENDAREST,
            'CDOPERADOR'  => $CDOPERADOR,
            'NRLUGARMESA' => str_pad($position, 2, '0', STR_PAD_LEFT)
        );
        $this->entityManager->getConnection()->executeQuery('DELETE_POSITION_CONTROL', $params);

        return null;
    }

    public function lockAllPositions($CDFILIAL, $NRVENDAREST, $CDOPERADOR){
        $positionControl = self::getPositionControlDetails($CDFILIAL, $NRVENDAREST, $CDOPERADOR);

        if (!empty($positionControl)){
            return 'Algumas posições já estão sendo recebidas. Escolha uma posição.';
        }
        else {
            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'NRVENDAREST' => $NRVENDAREST
            );
            $NRPOSICAOMESA = $this->entityManager->getConnection()->fetchAssoc("GET_NRPOSICAOMESA", $params);
            $NRPOSICAOMESA = intval($NRPOSICAOMESA['NRPOSICAOMESA']);

            $positions = array();
            for ($i = 1; $i <= $NRPOSICAOMESA; $i++){
                self::insertPositionControl($CDFILIAL, $NRVENDAREST, $CDOPERADOR, $i);
            }

            return null;
        }
    }

    private function buscaMesaPrincipal($CDFILIAL, $CDLOJA, $NRMESA){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
            'NRMESA' => $NRMESA
        );
        $mesa = $this->entityManager->getConnection()->fetchAssoc("BUSCA_MESA_PRINCIPAL", $params);
        return $mesa;
    }

}