<?php

namespace Service;

class Order {

	protected $entityManager;
	protected $util;
	protected $tableService;
	protected $billService;
	protected $impressaoService;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Util\Util $util,
		\Service\Table $tableService,
		\Service\Bill $billService,
		\Service\Impressao $impressaoService,
		\Service\KDS $KDS
	) {
		$this->entityManager = $entityManager;
		$this->util = $util;
		$this->tableService = $tableService;
		$this->billService = $billService;
		$this->impressaoService = $impressaoService;
		$this->KDS = $KDS;
	}

	private function setDefaultReasonValues($motivo) {
		if (empty($motivo['CDGRPOCOR'])) {
			$motivo['CDGRPOCOR'] = null;
		}
		if (empty($motivo['CDOCORR'])) {
			$motivo['CDOCORR'] = null;
		}
		if (empty($motivo['TXPRODCOMVEN'])) {
			$motivo['TXPRODCOMVEN'] = null;
		}
		return $motivo;
	}

	public function cancelaProduto($dataset){ //Detaset Params: chave, mode, dscomanda, produto, motivo, supervisor
		$session = $this->util->getSessionVars($dataset['chave']);
		$NRCOMANDA = $dataset['NRCOMANDA'];
		$NRVENDAREST = $dataset['NRVENDAREST'];
		$motivo = self::setDefaultReasonValues($dataset['motivo']);
		$stAmbiente = null;
		$IDPRODPRODUZ = $dataset['IDPRODPRODUZ'];
		// Buscando a mesa.
		$valMesaOrigem = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST, false);
		$mesaOrigem = $valMesaOrigem['NRMESA'];
		
        /* CHECK FOR ITEMS IN MOVCAIXADLV */
        $params = array(
            'CDFILIAL' => $session['CDFILIAL'],
            'NRVENDAREST' => $NRVENDAREST
        );
        $valTrans = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_MOVCAIXADLV", $params);
        if (!empty($valTrans)){
            return array('funcao' => '0', 'error' => '462');
        }

		if ($dataset['mode'] == 'C'){
			// valida e busca dados da comanda
			$valComanda = $this->billService->dadosComanda($session['CDFILIAL'], $NRCOMANDA, $NRVENDAREST, $session['CDLOJA']);
			$stDsComanda = $valComanda['DSCOMANDA'];
			$stNrMesa = $valComanda['NRMESA'];
		}
		else if ($dataset['mode'] == 'M'){
			// valida e busca os dados da mesa
			$valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $NRCOMANDA, $NRVENDAREST);
			$stDsComanda = '';
			$stNrMesa = $valMesa['NRMESA'];
			$params = array($session['CDFILIAL'], $session['CDLOJA'], $stNrMesa);
			$stAmbiente = $this->entityManager->getConnection()->fetchAssoc("SQL_AMBIENTE", $params);
			if (!empty($stAmbiente)) $stAmbiente = $stAmbiente['NMSALA'];

		}

		$stNrVendaRest = $dataset['produto']['NRVENDAREST'];
		$stNrComanda = $dataset['produto']['nrcomanda'];

		// ---

		$CDFILIAL     = $session['CDFILIAL'];
		$NRCONFTELA   = $session['NRCONFTELA'];
		$NRPRODCOMVEN = $dataset['produto']['NRPRODCOMVEN'];
		$params = array(
			$NRCONFTELA,
			$CDFILIAL,
			$NRCONFTELA,
			$CDFILIAL,
			$stNrComanda,
			$stNrVendaRest,
			$NRPRODCOMVEN,
			$NRCONFTELA,
			$CDFILIAL,
			$NRCONFTELA,
			$CDFILIAL,
			$stNrComanda,
			$stNrVendaRest,
			$NRPRODCOMVEN
		);
		$itemSelecionado = $this->entityManager->getConnection()->fetchAll("SQL_SELECIONA_ITEM", $params);
		// Se item tiver sido dividido, deleta o item (integral)
		if (!empty($itemSelecionado)) {
			if ($itemSelecionado[0]["IDDIVIDECONTA"] == "S") {
				$NRPRODCOMVEN = $itemSelecionado[0]["NRPRODORIG"];
				$params = array(
					$NRCONFTELA,
					$CDFILIAL,
					$NRCONFTELA,
					$CDFILIAL,
					$stNrComanda,
					$stNrVendaRest,
					$NRPRODCOMVEN,
					$NRCONFTELA,
					$CDFILIAL,
					$NRCONFTELA,
					$CDFILIAL,
					$stNrComanda,
					$stNrVendaRest,
					$NRPRODCOMVEN
				);
				$item = $this->entityManager->getConnection()->fetchAll("SQL_ITENS_ORIGINAIS", $params);
				$codigo = $dataset['produto']['codigo'];
				$dataset['produto'] = $item[0];
				$dataset['produto']['codigo'] = $codigo;
				$dataset['produto']['quantidade'] = $item[0]["QTPRODCOMVEN"];
				$data = array(
					"chave"        => $dataset['chave'],
					"NRVENDAREST"  => array($dataset['produto']['NRVENDAREST']),
					"NRCOMANDA"    => array($dataset['produto']['NRCOMANDA']),
					"NRPRODCOMVEN" => array($dataset['produto']['NRPRODCOMVEN']),
					"IDPRODPRODUZ" => $IDPRODPRODUZ
				);
				$answer = $this->cancelaProdutosDivididos($data);
			}
		}

		// ---

		$dataset['produto']['quantidade'] = floatval(str_replace(',', '.', $dataset['produto']['quantidade']));

		// verifica se a quantidade é menor que um e aceita somente para produtos pesáveis
		$getProdPes = $this->entityManager->getConnection()->fetchAll("SQL_GET_PROD_PES", array($dataset['produto']['codigo']));

		//Busca IDTIPOCOMPPROD do produto para produtos combinados não entrarem na regra de quantidade abaixo de 1 no cancelamento.
		$params = array ($dataset['produto']['codigo']);
		$prodData = $this->entityManager->getConnection()->fetchAll("SQL_GET_PRODUTO", $params);
		$IDTIPOCOMPPROD = $prodData[0]['IDTIPOCOMPPROD'];

		if ($dataset['produto']['quantidade'] < 1 && $getProdPes[0]['IDPESAPROD'] == 'N' && $IDTIPOCOMPPROD != '4'){
			return array('funcao' => '0', 'error' => '051'); //051 - Erro de execução na função.
		}

		// busco os produtos combinados ao produto passado para cancelamento
		$params = array($session['CDFILIAL'], $stNrComanda, $stNrVendaRest, $dataset['produto']['CDPRODPROMOCAO'], $dataset['produto']['NRSEQPRODCOM']);
		$itensCombinados = $this->entityManager->getConnection()->fetchAll("SQL_ITENS_COMBINADOS", $params);

		if(!empty($itensCombinados)){
			$acrescimo = $itensCombinados[0]['VRACRCOMVEN'];
		}

		// busco os dados do produto passado para cancelamento
		$params = array($session['CDFILIAL'], $stNrComanda, $stNrVendaRest, $dataset['produto']['NRPRODCOMVEN']);
		$item = $this->entityManager->getConnection()->fetchAll("SQL_ITEM", $params);

		if (!empty($itemSelecionado)){
			if (($itemSelecionado[0]["IDDIVIDECONTA"] == "S") && (empty($item))) {
				return array('funcao' => '0', 'error' => '454'); // 454 - Já houve o pagemento parcial do item
			}
		}

		if (empty($item)){
			return array('funcao' => '0', 'error' => '039'); //039 - Produto não cadastrado.
		}

		// CANCELAMENTO PARCIAL DE PEDIDOS AGRUPADOS (somente produtos normais).
		if ($dataset['produto']['quantidade'] < floatval($item[0]['QTPRODCOMVEN']) && $dataset['produto']['quantidade'] > 0 && empty($itensCombinados)){

			try {

				/* Open connection and begin transaction. */
				$connection = $this->entityManager->getConnection();
				$connection->beginTransaction();

				$params = array(
					floatval($item[0]['QTPRODCOMVEN']) - floatval($dataset['produto']['quantidade']),
					$session['CDFILIAL'],
					$stNrVendaRest,
					$stNrComanda,
					$dataset['produto']['NRPRODCOMVEN']
				);
				$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_QUANTIDADE", $params);

				//updata o pedido no controle de produção (kds)
				$operationParamsArray = array(
					'CDFILIAL'     => $session['CDFILIAL'],
					'NRVENDAREST'  => $stNrVendaRest,
					'NRCOMANDA'    => $stNrComanda,
					'NRPRODCOMVEN' => $dateset['produto']['NRPRODCOMVEN'],
					'QTPRODPEFOS'  => floatval($item[0]['QTPRODCOMVEN']) - floatval($dataset['produto']['quantidade'])
				);
				$this->KDS->insertKDSOPERACAOTEMP($operationParamsArray, 'cancelItem', $session['NRORG']);
				// $this->entityManager->getConnection()->executeQuery("SQL_CANC_QUANT_KDS", $params);

				if (!empty($item[0]['NRCOMANDAORI']) && !empty($item[0]['NRPRODCOMORI'])){
					$params = array(
						floatval($item[0]['QTPRODCOMVEN']) - floatval($dataset['produto']['quantidade']),
						$session['CDFILIAL'],
						$dataset['produto']['NRVENDAREST'],
						$item[0]['NRCOMANDAORI'],
						$item[0]['NRPRODCOMORI']
					);
					$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_QUANTIDADE", $params);
				}

				/* Altera a ITHRPEDIDO. */
				// Esta função está servindo apenas como ajuste.
				// Na data atual (06/07/2017) o Waiter/Fast Pass não realiza cancelamento de quantidades específicas.
				// Favor revisar caso seja alterado.
				$params = array(
					floatval($item[0]['QTPRODCOMVEN']) - floatval($dataset['produto']['quantidade']),
					$session['CDFILIAL'],
					substr($dataset['produto']['NRPRODCOMVEN'], strlen($dataset['produto']['NRPRODCOMVEN'])-3),
					$dataset['produto']['NRVENDAREST']
				);


				$connection->commit();
			}
			catch (\Exception $e){
				if($connection != null){
					   $connection->rollback();
				}
				  throw new Exception($e->getMessage(), 1);
			}
		}
		// CANCELAMENTO DE TODAS AS UNIDADES DO PEDIDO (e produtos com composição).
		else if (($dataset['produto']['quantidade'] == floatval($item[0]['QTPRODCOMVEN']) && $dataset['produto']['quantidade'] > 0) || !empty($itensCombinados)){

			try {
				/* Open connection and begin transaction. */
				$connection = $this->entityManager->getConnection();
				$connection->beginTransaction();

				// o produto a ser eliminado é uma promoção inteligente
				if (!empty($itensCombinados)){
					foreach($itensCombinados as $produto){
						$params = array(
							'TXPRODCOMVEN' => $motivo['TXPRODCOMVEN'],
							'CDGRPOCOR' => $motivo['CDGRPOCOR'],
							'CDOCORR' => $motivo['CDOCORR'],
							'IDSTPRCOMVEN' => '6',
							'CDSUPERVISOR' => $dataset['CDSUPERVISOR'],
							'CDFILIAL' => $session['CDFILIAL'],
							'NRCOMANDA' => $stNrComanda,
							'NRVENDAREST' => $stNrVendaRest,
							'NRPRODCOMVEN' => $produto['NRPRODCOMVEN'],
							'IDPRODPRODUZ' => $IDPRODPRODUZ,
							'DTHRPRODCANVEN' => $dataset['DTHRPRODCANVEN']
						);
						$types = array(
							'DTHRPRODCANVEN' => \Doctrine\DBAL\TypeS\Type::DATETIME
						);
						$this->entityManager->getConnection()->executeQuery("SQL_CANCELA_ITEM", $params, $types);

						$operationParamsArray = array(
							'CDFILIAL'     => $session['CDFILIAL'],
							'NRVENDAREST'  => $stNrVendaRest,
							'NRCOMANDA'    => $stNrComanda,
							'NRPRODCOMVEN' => $produto['NRPRODCOMVEN']
						);
						$this->KDS->insertKDSOPERACAOTEMP($operationParamsArray, 'cancelItem', $session['NRORG']);

						if (!empty($produto['NRCOMANDAORI']) && !empty($produto['NRPRODCOMORI'])){
							$params = array(
								'TXPRODCOMVEN' => $motivo['TXPRODCOMVEN'],
								'CDGRPOCOR' => $motivo['CDGRPOCOR'],
								'CDOCORR' => $motivo['CDOCORR'],
								'IDSTPRCOMVEN' => '6',
								'CDSUPERVISOR' => $dataset['CDSUPERVISOR'],
								'CDFILIAL' => $session['CDFILIAL'],
								'NRCOMANDA' => $produto['NRCOMANDAORI'],
								'NRVENDAREST' => $stNrVendaRest,
								'NRPRODCOMVEN' => $produto['NRPRODCOMORI'],
								'IDPRODPRODUZ' => $IDPRODPRODUZ,
								'DTHRPRODCANVEN' => $dataset['DTHRPRODCANVEN']
							);
							$types = array(
								'DTHRPRODCANVEN' => \Doctrine\DBAL\TypeS\Type::DATETIME
							);
							$this->entityManager->getConnection()->executeQuery("SQL_CANCELA_ITEM", $params, $types);
						}
					}
					$params = array($session['CDFILIAL'], $dataset['produto']['nrcomanda'], $dataset['produto']['NRVENDAREST']);
					$acrComVen = $this->entityManager->getConnection()->fetchAll("SQL_ACR_COM_VEN", $params);
					if (!empty($acrComVen)) $acrComVen[0]['VRACRCOMANDA'] -= $acrescimo;
				}
				// produto normal (não é promoção inteligente)
				else if (empty($itensCombinados)){

					$params = array(
						'TXPRODCOMVEN' => $motivo['TXPRODCOMVEN'],
						'CDGRPOCOR' => $motivo['CDGRPOCOR'],
						'CDOCORR' => $motivo['CDOCORR'],
						'IDSTPRCOMVEN' => '6',
						'CDSUPERVISOR' => $dataset['CDSUPERVISOR'],
						'CDFILIAL' => $session['CDFILIAL'],
						'NRCOMANDA' => $stNrComanda,
						'NRVENDAREST' => $stNrVendaRest,
						'NRPRODCOMVEN' => $dataset['produto']['NRPRODCOMVEN'],
						'IDPRODPRODUZ' => $IDPRODPRODUZ,
						'DTHRPRODCANVEN' => $dataset['DTHRPRODCANVEN']
					);
					$types = array(
						'DTHRPRODCANVEN' => \Doctrine\DBAL\TypeS\Type::DATETIME
					);
					$this->entityManager->getConnection()->executeQuery("SQL_CANCELA_ITEM", $params, $types);
					$operationParamsArray = array(
						'CDFILIAL'     => $session['CDFILIAL'],
						'NRVENDAREST'  => $stNrVendaRest,
						'NRCOMANDA'    => $stNrComanda,
						'NRPRODCOMVEN' => $dataset['produto']['NRPRODCOMVEN']
					);
					$this->KDS->insertKDSOPERACAOTEMP($operationParamsArray, 'cancelItem', $session['NRORG']);

					if (!empty($item[0]['NRCOMANDAORI']) && !empty($item[0]['NRPRODCOMORI'])){
						$params = array(
							'TXPRODCOMVEN' => $motivo['TXPRODCOMVEN'],
							'CDGRPOCOR' => $motivo['CDGRPOCOR'],
							'CDOCORR' => $motivo['CDOCORR'],
							'IDSTPRCOMVEN' => '6',
							'CDSUPERVISOR' => $dataset['CDSUPERVISOR'],
							'CDFILIAL' => $session['CDFILIAL'],
							'NRCOMANDA' => $item[0]['NRCOMANDAORI'],
							'NRVENDAREST' => $stNrVendaRest,
							'NRPRODCOMVEN' => $item[0]['NRPRODCOMORI'],
							'IDPRODPRODUZ' => $IDPRODPRODUZ,
							'DTHRPRODCANVEN' => $dataset['DTHRPRODCANVEN']
						);
						$types = array(
							'DTHRPRODCANVEN' => \Doctrine\DBAL\TypeS\Type::DATETIME
						);
						$this->entityManager->getConnection()->executeQuery("SQL_CANCELA_ITEM", $params, $types);
					}
				}
				$connection->commit();

			}
			catch(\Exception $e){
				$connection->rollback();
				  throw new \Exception($e->getMessage(), 1);
			}
		}
		else return array('funcao' => '0', 'error' => '017'); //017 - Quantidade inválida.

		$ItensImpressao = array();
		if (empty($itensCombinados)){ // produto normal
			if ($item[0]['IDIMPCANITEM'] == 'S'){
				foreach ($item as $chave => $produto){
					$impressoras = array ($produto['NRSEQIMPRLOJA'], $produto['NRSEQIMPRLOJA2']);
					foreach ($impressoras as $key => $value) {
						$allItens[] = array(
							'CDPRODUTO' => $produto['CDPRODUTO'],
							'NMPRODUTO' => $produto['NMPRODUTO'],
							'IDIMPCANITEM' => $produto['IDIMPCANITEM'],
							'NRMESA' => $produto['NRMESA'],
							'DSCOMANDAORI' => $produto['DSCOMANDAORI'],
							'NRPRODCOMORI' => $produto['NRPRODCOMORI'],
							'NRCOMANDAORI' => $produto['NRCOMANDAORI'],
							'VRPRECCOMVEN' => $produto['VRPRECCOMVEN'],
							'QTPRODCOMVEN' => $dataset['produto']['quantidade'],
							'NMSALA' => $stAmbiente,
							'MOTIVO' => $motivo['TXPRODCOMVEN'],
							'CDPORIMPPROD' => $produto['CDPORIMPPROD']
						);
						if ($key == 0) {
							$arrayImpressora = array(
								'NRSEQIMPRPROD' => $produto['NRSEQIMPRPROD'],
								'IDMODEIMPRES' => $produto['IDMODEIMPRES'],
								'DSENDPORTA' => $produto['DSENDPORTA'],
								'NRSEQIMPRLOJA' => $produto['NRSEQIMPRLOJA'],
								'CDPORTAIMPR' => $produto['CDPORTAIMPR'],
								'DSIPIMPR' => $produto['DSIPIMPR'],
								'DSIPPONTE' => $produto['DSIPPONTE']
							);
						} else if ($key == 1) {
							$arrayImpressora = array(
								'NRSEQIMPRPROD' => $produto['NRSEQIMPRPROD2'],
								'IDMODEIMPRES' => $produto['IDMODEIMPRES2'],
								'DSENDPORTA' => $produto['DSENDPORTA2'],
								'NRSEQIMPRLOJA' => $produto['NRSEQIMPRLOJA2'],
								'CDPORTAIMPR' => $produto['CDPORTAIMPR2'],
								'DSIPIMPR' => $produto['DSIPIMPR2'],
								'DSIPPONTE' => $produto['DSIPPONTE2']
							);
						}
						array_push($ItensImpressao, array_merge($allItens[0], $arrayImpressora));
					}
				}
			}
		}
		else { // promoção inteligente
			$c = 0;
			foreach ($itensCombinados as $chave => $produtosCombinados){
				if ($produtosCombinados['IDIMPCANITEM'] == 'S'){
					$impressoras = array($produtosCombinados['NRSEQIMPRLOJA'], $produtosCombinados['NRSEQIMPRLOJA2']);
					foreach ($impressoras as $key => $value) {
						$allItens[] = array(
							'NMPRODUTO' => $produtosCombinados['NMPRODUTO'],
							'IDIMPCANITEM' => $produtosCombinados['IDIMPCANITEM'],
							'CDFILIAL' => $produtosCombinados['CDFILIAL'],
							'NRVENDAREST' => $produtosCombinados['NRVENDAREST'],
							'NRCOMANDA' => $produtosCombinados['NRCOMANDA'],
							'NRPRODCOMVEN' => $produtosCombinados['NRPRODCOMVEN'],
							'CDPRODUTO' => $produtosCombinados['CDPRODUTO'],
							'QTPRODCOMVEN' => $produtosCombinados['QTPRODCOMVEN'],
							'VRPRECCOMVEN' => $produtosCombinados['VRPRECCOMVEN'],
							'TXPRODCOMVEN' => $produtosCombinados['TXPRODCOMVEN'],
							'IDSTPRCOMVEN' => $produtosCombinados['IDSTPRCOMVEN'],
							'VRDESCCOMVEN' => $produtosCombinados['VRDESCCOMVEN'],
							'NRLUGARMESA' => $produtosCombinados['NRLUGARMESA'],
							'CDGRPOCOR' => $produtosCombinados['CDGRPOCOR'],
							'CDOCORR' => $produtosCombinados['CDOCORR'],
							'NRMESAORIG' => $produtosCombinados['NRMESAORIG'],
							'CDLOJAORIG' => $produtosCombinados['CDLOJAORIG'],
							'DTHRINCOMVEN' => $produtosCombinados['DTHRINCOMVEN'],
							'IDPRODIMPFIS' => $produtosCombinados['IDPRODIMPFIS'],
							'CDLOJA' => $produtosCombinados['CDLOJA'],
							'VRACRCOMVEN' => $produtosCombinados['VRACRCOMVEN'],
							'NRSEQPRODCOM' => $produtosCombinados['NRSEQPRODCOM'],
							'NRSEQPRODCUP' => $produtosCombinados['NRSEQPRODCUP'],
							'DSCOMANDAORI' => $produtosCombinados['DSCOMANDAORI'],
							'NRCOMANDAORI' => $produtosCombinados['NRCOMANDAORI'],
							'NRPRODCOMORI' => $produtosCombinados['NRPRODCOMORI'],
							'VRPRECCLCOMVEN' => $produtosCombinados['VRPRECCLCOMVEN'],
							'CDCAIXACOLETOR' => $produtosCombinados['CDCAIXACOLETOR'],
							'CDPRODPROMOCAO' => $produtosCombinados['CDPRODPROMOCAO'],
							'NRMESADSCOMORIT' => $produtosCombinados['NRMESADSCOMORIT'],
							'CDVENDEDOR' => $produtosCombinados['CDVENDEDOR'],
							'CDFILIALPED' => $produtosCombinados['CDFILIALPED'],
							'NRPEDIDOFOS' => $produtosCombinados['NRPEDIDOFOS'],
							'CDSUPERVISOR' => $produtosCombinados['CDSUPERVISOR'],
							'CDSENHAPED' => $produtosCombinados['CDSENHAPED'],
							'CDORDERWAITER' => $produtosCombinados['CDORDERWAITER'],
							'NRSEQBENECONSIT' => $produtosCombinados['NRSEQBENECONSIT'],
							'NRATRAPRODCOVE' => $produtosCombinados['NRATRAPRODCOVE'],
							'IDORIGPEDCMD' => $produtosCombinados['IDORIGPEDCMD'],
							'DSOBSPEDDIGCMD' => $produtosCombinados['DSOBSPEDDIGCMD'],
							'NRORG' => $produtosCombinados['NRORG'],
							'IDDIVIDECONTA' => $produtosCombinados['IDDIVIDECONTA'],
							'NRPRODORIG' => $produtosCombinados['NRPRODORIG'],
							'IDPRODPRODUZ' => $produtosCombinados['IDPRODPRODUZ'],
							'NRINSCRCONS' => $produtosCombinados['NRINSCRCONS'],
							'NMCONSVEND' => $produtosCombinados['NMCONSVEND'],
							'DSENDEVENDA' => $produtosCombinados['DSENDEVENDA'],
							'QTITEMREFIL' => $produtosCombinados['QTITEMREFIL'],
							'IDPRODREFIL' => $produtosCombinados['IDPRODREFIL'],
							'CDGRPOCORDESCIT' => $produtosCombinados['CDGRPOCORDESCIT'],
							'DSOBSDESCIT' => $produtosCombinados['DSOBSDESCIT'],
							'CDSUPERVDESC' => $produtosCombinados['CDSUPERVDESC'],
							'IDUTIRESGBRINDE' => $produtosCombinados['IDUTIRESGBRINDE'],
							'IDSTLUGARMESA' => $produtosCombinados['IDSTLUGARMESA'],
							'MOTIVO' => $motivo['TXPRODCOMVEN'],
							'NRMESA' => $stNrMesa
						);

						if ($key == 0) {
							$arrayImpressora = array(
								'NRSEQIMPRPROD' => $produtosCombinados['NRSEQIMPRPROD'],
								'IDMODEIMPRES' => $produtosCombinados['IDMODEIMPRES'],
								'DSENDPORTA' => $produtosCombinados['DSENDPORTA'],
								'NRSEQIMPRLOJA' => $produtosCombinados['NRSEQIMPRLOJA'],
								'CDPORTAIMPR' => $produtosCombinados['CDPORTAIMPR'],
								'DSIPIMPR' => $produtosCombinados['DSIPIMPR'],
								'DSIPPONTE' => $produtosCombinados['DSIPPONTE']
							);
							array_push($ItensImpressao, array_merge($allItens[$c], $arrayImpressora));
						} else if ($key == 1) {
							$arrayImpressora = array(
								'NRSEQIMPRPROD' => $produtosCombinados['NRSEQIMPRPROD2'],
								'IDMODEIMPRES' => $produtosCombinados['IDMODEIMPRES2'],
								'DSENDPORTA' => $produtosCombinados['DSENDPORTA2'],
								'NRSEQIMPRLOJA' => $produtosCombinados['NRSEQIMPRLOJA2'],
								'CDPORTAIMPR' => $produtosCombinados['CDPORTAIMPR2'],
								'DSIPIMPR' => $produtosCombinados['DSIPIMPR2'],
								'DSIPPONTE' => $produtosCombinados['DSIPPONTE2']
							);
							array_push($ItensImpressao, array_merge($allItens[$c], $arrayImpressora));
						}
						$c++;
					}
				}
			}
		}

		$retornoImpressao = array();
		if (!empty($ItensImpressao)){
			$retornoImpressao = $this->impressaoService->imprimeCancelamento($dataset['chave'], $ItensImpressao, $dataset['mode'], $stDsComanda);
		}

		$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'CAN_GEN', $session['CDOPERADOR'], null, "Waiter - Cancelamento de produto", "Cancelamento do produto " . $dataset['produto']['codigo'] . ", referente à mesa " . $mesaOrigem  . ".");
		
		$result = array('funcao' => '1', 'message' => !empty($retornoImpressao['message']) ? $retornoImpressao['message'] : '');
		
		if(isset($retornoImpressao['saas'])&&$retornoImpressao['saas']){
			$result = array('funcao' => '1', 'message' => !empty($retornoImpressao['message']) ? $retornoImpressao['message'] : '', 'paramsImpressora' => $retornoImpressao);	
		}

		return $result;
	}

	public function cancelaProdutosDivididos($dataset){
		$connection = null;
		try{

			$session        = $this->util->getSessionVars($dataset['chave']);
			$CDFILIAL       = $session['CDFILIAL'];
			$NRCONFTELA     = $session['NRCONFTELA'];
			$NRVENDAREST    = $dataset["NRVENDAREST"];
			$NRCOMANDA      = $dataset["NRCOMANDA"];
			$NRPRODCOMVEN   = $dataset["NRPRODCOMVEN"];
			$IDPRODPRODUZ   = $dataset["IDPRODPRODUZ"];

			for($index = 0; $index < count($NRVENDAREST); $index++ ){

				$params = array(
					$NRCONFTELA,
					$CDFILIAL,
					$NRCONFTELA,
					$CDFILIAL,
					$NRCOMANDA[$index],
					$NRVENDAREST[$index],
					$NRPRODCOMVEN[$index],
					$NRCONFTELA,
					$CDFILIAL,
					$NRCONFTELA,
					$CDFILIAL,
					$NRCOMANDA[$index],
					$NRVENDAREST[$index],
					$NRPRODCOMVEN[$index]
				);
				$itens = $this->entityManager->getConnection()->fetchAll("SQL_ITENS_ORIGINAIS", $params);

				$params = array(
					$CDFILIAL,
					$NRVENDAREST[$index],
					$NRCOMANDA[$index],
					$NRPRODCOMVEN[$index]
				);

				$values = $this->entityManager->getConnection()->fetchAll("SQL_VALIDA_VALORES_ITEM", $params);

				$originalValues = $this->entityManager->getConnection()->fetchAll("SQL_VALIDA_VALORES_ORIG", $params);

				if($values[0]["QTPRODCOMVEN"] == $originalValues[0]["QTPRODCOMVEN"]){

					$this->entityManager->getConnection()->executeQuery("SQL_DELETA_PRODUTOS", $params);
					$params = array(
						'CDFILIAL' => $CDFILIAL,
						'NRVENDAREST' => $NRVENDAREST[$index],
						'NRCOMANDA' => $NRCOMANDA[$index],
						'NRPRODCOMVEN' => $NRPRODCOMVEN[$index],
						'IDPRODPRODUZ' => $IDPRODPRODUZ
					);
					$this->entityManager->getConnection()->fetchAll("SQL_INSERE_ITEM_COMANDA", $params);
					$this->entityManager->getConnection()->executeQuery("SQL_DELETA_CMD_VEN_ORIG", $params);

				} else {
					return array('funcao' => '0', 'error' => '454'); // 454 - Já houve o pagemento parcial do item
				}

			}

			return array('funcao' => '1');

		} catch(\Exception $e){
			throw new \Exception ($e->getMessage(),1);
		}
	}

}