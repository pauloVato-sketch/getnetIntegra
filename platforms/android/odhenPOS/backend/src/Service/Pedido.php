<?php

namespace Service;

use \Util\Exception;

class Pedido {

	protected $entityManager;
	protected $util;
	protected $billService;
	protected $tableService;
    protected $paymentService;
	protected $precoAPI;
	protected $impressaoAPI;
	protected $impressaoDelphiAPI;
	protected $instanceManager;
	protected $impProdParams = array();

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Util\Util $util,
		\Service\Bill $billService,
		\Service\Table $tableService,
        \Service\Payment $paymentService,
		\Odhen\API\Service\Preco $precoAPI,
		\Odhen\API\Service\ImpressaoPedido $impressaoAPI,
		\Odhen\API\Lib\ImpressaoDelphi $impressaoDelphiAPI,
		\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager
	){
		$this->entityManager      = $entityManager;
		$this->util               = $util;
		$this->billService        = $billService;
		$this->tableService       = $tableService;
        $this->paymentService    = $paymentService;
		$this->precoAPI           = $precoAPI;
		$this->impressaoAPI       = $impressaoAPI;
		$this->impressaoDelphiAPI = $impressaoDelphiAPI;
		$this->instanceManager    = $instanceManager;

		$this->utilizaImpressaoPonte = $this->instanceManager->getParameter('UTILIZA_IMPRESSAO_PONTE');
        $this->utilizaImpressaoPHP = $this->instanceManager->getParameter('UTILIZA_IMPRESSAO_PHP');
	}

	public function fazPedido($dataset){
		try {
			$session     = $this->util->getSessionVars($dataset['chave']);
			$modo        = $dataset['mode'];
			$nrVendaRest = $dataset['NRVENDAREST'];
			$nrComanda   = $dataset['NRCOMANDA'];
			$pedido      = $dataset['pedido'];
			$ambiente    = null;
			$cliente     = null;
			$consumidor  = null;
			$ordemImp    = 0;
			$posVendaEst = array();
			$vendedorAut = $dataset['vendedorAut'];

			if (empty($dataset['supervisor'])) {
				$dataset['supervisor'] = null;
			}

			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////
			//                                        PARTE 1                                          //
			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////

			if ($modo == 'C'){
				// Valida e busca dados da comanda.
				$valComanda = $this->billService->dadosComanda($session['CDFILIAL'], $nrComanda, $nrVendaRest, $session['CDLOJA']);
				$dsComanda = $valComanda['DSCOMANDA'];
				$cliente = $valComanda['CDCLIENTE'];
				$consumidor = $valComanda['CDCONSUMIDOR'];
				$nrMesa = str_pad($valComanda['NRMESA'], 4, '0', STR_PAD_LEFT);
				$CDSALA = null;
                $DTHRABERMESA = $valComanda['DTHRABERMESA'];
			}
			else if ($modo == 'M' || $modo == 'O'){
				// Valida e busca dados da mesa.
				$valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $nrComanda, $nrVendaRest);

				$dsComanda = '';
				$nrVendaRest = $valMesa['NRVENDAREST'];
				$nrComanda = $valMesa['NRCOMANDA'];
				$cliente = $valMesa['CDCLIENTE'];
				$consumidor = $valMesa['CDCONSUMIDOR'];
				$nrMesa = str_pad($valMesa['NRMESA'], 4, '0', STR_PAD_LEFT);

				// Busca ambiente da mesa.
				$params = array($session['CDFILIAL'], $session['CDLOJA'], $nrMesa);
				$r_ambiente = $this->entityManager->getConnection()->fetchAssoc("SQL_AMBIENTE", $params);
				$ambiente = $r_ambiente['NMSALA'];
				$CDSALA = $r_ambiente['CDSALA'];

				// Valida se a mesa está aberta.
				$params = array($session['CDFILIAL'], $session['CDLOJA'], $nrMesa);
				$r_valida_mesa = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_MESA_BYNRMESA", $params);
				if ($r_valida_mesa['IDSTMESAAUX'] != 'O'){
					return array('funcao' => '0', 'error' => '438'); // A mesa não está disponível para realizar pedidos.
				}

				// Busca Cliente e Consumidor pelas posições
				$posVendaEst = $this->tableService->getPosition($session, $nrVendaRest, array_unique(array_column($pedido, 'posicao')));
				$posVendaEst = array_column($posVendaEst, null, 'NRLUGARMESA');
                $DTHRABERMESA = $valMesa['DTHRABERMESA'];
			}

			/*** IMPEDE PEDIDOS DUPLICADOS ***/
			// Verifica se o pedido já foi feito.
			$checkParams = array(
				'CDORDERWAITER' => $dataset['orderCode'],
				'CDFILIAL' => $session['CDFILIAL'],
				'NRVENDAREST' => $dataset['NRVENDAREST'],
				'NRCOMANDA' => $dataset['NRCOMANDA']
			);
			$checkOrderCode = $this->entityManager->getConnection()->fetchAssoc("SQL_CHECK_ORDERCODE", $checkParams);
			// Caso o pedido tenha sido feito, retorna.
			if ($checkOrderCode['CDORDERWAITER']) {
				return array('funcao' => "0", 'error' => '264'); // Pedido realizado com sucesso.
			}

			// Caso tente inserir um pedido já inserido, identifica o erro pelo UNIQUE e continua.
			// tratamento comandas agrupadas
			if (!isset($dataset['ultimaComanda']) || $dataset['ultimaComanda']) {
				try {
					$checkParams = array(
						'CDFILIAL' => $session['CDFILIAL'],
						'NRVENDAREST' => $dataset['NRVENDAREST'],
						'NRCOMANDA' => $dataset['NRCOMANDA'],
						'CDORDERWAITER' => $dataset['orderCode'],
						'IDSTORDER' => 'C',
						'DSOPERACAO' => null,
						'DTHRINCREQ' => null
					);
					$this->entityManager->getConnection()->executeQuery("SQL_INSERE_WAITER_ORDERS", $checkParams);
				} catch (\Exception $e){
					Exception::logException($e);
					return array('funcao' => "0", 'error' => '264'); // Pedido realizado com sucesso.
				}
			}

			/*** DEFINIÇÃO DE VALORES ***/
			$idx = 0;

			// Código do NRSEQPRODCOM e NRSEQPRODCUP.
			$params = array(
				'CDFILIAL' => $session['CDFILIAL'],
				'NRVENDAREST' => $nrVendaRest,
				'NRCOMANDA' => $nrComanda,
				'NRORG' => $session['NRORG']
			);
			$lastNRSEQPRODCOM = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NRSEQCOM", $params);
			$lastNRSEQPRODCOM = intval($lastNRSEQPRODCOM['NRSEQPRODCOM']);
			$this->util->newCode('ITCOMANDAVEN' . $session['CDFILIAL']);
			$nrProdComCup = $this->util->getNewCode('ITCOMANDAVEN' . $session['CDFILIAL'],10);

			// Busca vendedor.
			$params = array(
				$session['CDFILIAL'],
				$dataset['NRCOMANDA'],
				$dataset['NRVENDAREST']
			);
			$vendedorVendaRest = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_VENDEDOR", $params);

			$vendedorAberturaMesa = $vendedorVendaRest['CDVENDEDOR'];
			$vendedorLogado = empty($vendedorAut) ? $session['CDVENDEDOR'] : $vendedorAut;
			$vendedorPadrao = $session['CDVENDPADRAO'];

			if (!empty($vendedorLogado)) {
                $cdVendedor = $vendedorLogado;
            } else if (!empty($vendedorAberturaMesa)) {
                $cdVendedor = $vendedorAberturaMesa;
            } else {
                $cdVendedor = $vendedorPadrao;
            }

			/*** ORDEM DE IMPRESSÃO ***/
			// Ordena o array de pedidos para a impressão adequada, considerando posição, e depois a ordem em que foi pedido.
			$sort = array();
			foreach($pedido as $k=>$v){
				$sort['posicao'][$k] = $v['posicao'];
				$sort['ORDEMIMP'][$k] = $v['ORDEMIMP'];
			}
			array_multisort($sort['posicao'], SORT_ASC, $sort['ORDEMIMP'], SORT_ASC, $pedido);

			// valida se posição pode fazer pedido caso houver adiantamento
			if ($session['IDCOLETOR'] === 'C'){
				$adiantamentoPendente = self::adiantamentoPendente($session, $pedido, $nrVendaRest);
				if ($adiantamentoPendente['error']){
					return array('funcao' => '0', 'error' => '461');
				}
			}

            // Calcula número total de posições, considerando mesas agrupadas.
            $params = array(
                $nrMesa
            );
            $mesasAgrupadas = $this->entityManager->getConnection()->fetchAll("SQL_GET_GROUPED_TABLES", $params);

            $NRTOTALPOSICOES = 0;
            foreach ($mesasAgrupadas as $mesa){
                $NRTOTALPOSICOES += intval($mesa['NRPESMESAVEN']);
            }

            // Armazena vouchers de uso único que estão sendo aplicados neste pedido.
            $voucherUsage = array();

			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////
			//                                        PARTE 2                                          //
			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////

			/*** PROCESSAMENTO DE PRODUTOS ***/
			foreach($pedido as $produto){

                $produto['posicao'] = str_pad($produto['posicao'], 2, '0', STR_PAD_LEFT);

                if ($modo == 'M' || $modo == 'O'){
                    $params = array(
                        'NRVENDAREST' => $nrVendaRest,
                        'NRCOMANDA' => $nrComanda,
                        'NRLUGARMESA' => $produto['posicao']
                    );
                    $positionData = $this->entityManager->getConnection()->fetchAssoc("GET_POSITION", $params);

                    // Valida se a conta foi solicitada para a posição.
                    if ($positionData['IDSTLUGARMESA'] === 'S'){
                        throw new \Exception('N&atilde;o &eacute; poss&iacute;vel realizar pedidos para a posi&ccedil;&atilde;o ' . $produto['posicao'] . ', pois a conta j&aacute; foi solicitada para a mesma.');
                    }

                    // Valida se a posição realmente existe.
                    if ($NRTOTALPOSICOES == 0) $NRTOTALPOSICOES = intval($positionData['NRPESMESAVEN']);
                    if (intval($produto['posicao']) > $NRTOTALPOSICOES){
                        throw new \Exception('Uma das posi&ccedil;&otilde;es informadas (' . $produto['posicao'] . '), n&atilde;o existe mais na mesa atual. Favor entrar na mesa novamente e conferir.');
                    }
                }

				// utiliza CDCLIENTE e CDCONSUMIDOR se estiverem na POSVENDAEST por NRVENDAREST e NRLUGARMESA
				if (isset($posVendaEst[$produto['posicao']])){
					$cdCliente = $posVendaEst[$produto['posicao']]['CDCLIENTE'];
					$cdConsumidor = $posVendaEst[$produto['posicao']]['CDCONSUMIDOR'];
				} else {
					$cdCliente = $cliente;
					$cdConsumidor = $consumidor;
				}

				// Verifica se produto está bloqueado.
				$params = array(
					$session['CDFILIAL'],
					$session['CDLOJA'],
					$produto['codigo']
				);
				$prodBloq = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_PRODBLOQ", $params);
				if (!empty($prodBloq)){
					return array('funcao' => '0', 'error' => '444', 'aux' => $produto); // Produto bloqueado.
				}

				/*** PROCESSAMENTO DE PRODUTOS DO TIPO PROMOÇÃO ***/
				$promo_int = '';
				if ($produto['produtos']){
					$ordemImp = $ordemImp + 1;
					// Processa a composição escolhida dentro do produto promoção.

					$promo_int = $this->formataPromocaoCombinada($session['CDFILIAL'], $cdCliente, $cdConsumidor, $session['CDLOJA'], $produto['produtos'], $produto['codigo'], $nrVendaRest, $ordemImp, $produto['IDTIPCOBRA'] != null, $session['IDTIPCOBRA'], $produto['IDIMPPRODUTO']);
					// Os produtos da composição serão inseridos separadamente mais adiante.
				}

				// busca nome do produto que deve aparecer na impressão
				$params = array(
					'CDPRODUTO' => $produto['codigo'],
					'CDFILIAL' => $session['FILIALVIGENCIA'],
					'NRCONFTELA' => $session['NRCONFTELA'],
                    'DTINIVIGENCIA' => new \DateTime($session['DTINIVIGENCIA'])
				);
                $types = array(
                    'DTINIVIGENCIA' => \Doctrine\DBAL\Types\Type::DATETIME
                );
				$descProduto = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_PRODUCT_DESC", $params, $types);
				$produto['desc'] = !empty($descProduto['DESCPROD']) ? $descProduto['DESCPROD'] : $produto['desc'];

				// Valida e busca os dados do produto pai.
				$r_valida_prod = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_PROD", array($produto['codigo']));

				if (empty($r_valida_prod)){
					if ($produto['IDTIPCOBRA'] == null){
						return array('funcao' => '0', 'error' => '023'); // Produto não cadastrado.
					}
					else {
						$r_valida_prod = array(
							'CDARVPROD' => '00000000000000',
							'NMPRODUTO' => $produto['desc'],
							'IDPESAPROD' => 'N',
							'IDTIPOCOMPPROD' => '3',
							'IDIMPPRODUTO' => '2',
							'IDCONTROLAREFIL' => 'N',
                            'IDIMPRODUVEZ' => 'N'
						);
					}
				}

				// Caso for promoção inteligente, mostra o produto pai na descrição.
				if ($r_valida_prod['IDTIPOCOMPPROD'] == '3' || $r_valida_prod['IDTIPOCOMPPROD'] == '6'){
					foreach($produto['produtos'] as $produtos)
						if ($produtos > 0) $NMPROMOCAO = $produto['desc'];
				}
				$r_valida_prod["NMPRODUTO"] = $produto['desc'];

				// Valida se o produto possui alíquota cadastrada.
				$params = array($session['CDFILIAL'], $produto['codigo']);
				$r_get_aliquota = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_ALIQUOTA", $params);
				if ($r_get_aliquota['COUNT'] == 0 && $produto['IDTIPCOBRA'] == null){
					return array('funcao' => '0', 'error' => '440'); // Produto sem alíquota.
				}

				// Substitui a impressora padrão pela impressora escolhida.
				if ($produto['IMPRESSORA']){
					$imp_produtos[$idx]['NRSEQIMPRLOJA'] = $produto['IMPRESSORA'];
				}

				// Busca o preço.
				if ($produto['IDTIPCOBRA'] != null){
					$preco     = 0;
					$desconto  = 0;
					$acrescimo = 0;
                    $subsidy   = 0;
				}
                else {
                    $r_retornaPreco = $this->precoAPI->buscaPreco($session['CDFILIAL'], $cdCliente, $produto['codigo'], $session['CDLOJA'], $cdConsumidor);
                    if (!$r_retornaPreco["error"]){
                        $preco     = floatval($r_retornaPreco['PRECO']);
                        $desconto  = floatval(bcmul(strval($r_retornaPreco['DESC']), strval($produto['quantidade']), 2));
                        $acrescimo = floatval(bcmul(strval($r_retornaPreco['ACRE']), strval($produto['quantidade']), 2));
                        $subsidy   = floatval($r_retornaPreco['PRECOCLIE']);
                    } else if ((empty($preco) || $preco == 0) && $produto['IDTIPCOBRA'] == null){
                        return array('funcao' => '0', 'error' => '443', 'aux' => $produto);
                    }
				}

                // Tratamento de voucher.
                if ($produto['VOUCHER']){
                    // Busca os dados do voucher e valida se o mesmo é válido.
                    try {
                        $voucher = $this->paymentService->validateVoucher($produto['codigo'], $nrVendaRest, $nrComanda, $produto['VOUCHER']['CDCUPOMDESCFOS']);
                        if ($voucher['IDUSOUNICO'] === "S"){
                            // Verifica se o voucher já foi utilizado neste pedido.
                            if (in_array($voucher['CDCUPOMDESCFOS'], $voucherUsage)){
                                throw new \Exception("O voucher " . $voucher['CDCUPOMDESCFOS'] . " é de uso único, mas está sendo aplicado em mais de um produto. Certifique-se de que apenas um produto esteja com este voucher aplicado.");
                            }
                            else {
                                array_push($voucherUsage, $voucher['CDCUPOMDESCFOS']);
                            }
                        }
                    } catch (\Exception $e){
                        return array('funcao' => '0', 'message' => 'Erro ao aplicar o voucher no produto ' . $produto['desc'] . ':<br><br>' . $e->getMessage());
                    }

                    // Aplica o desconto do voucher sobre o preço do produto.
                    $totalPrice = floatval(bcsub(strval($produto['quantidade'] * ($preco + $subsidy) + $acrescimo), strval($desconto), 2));
                    if ($voucher['IDTIPODESC'] === "P"){
                        $voucherDiscount = bcmul(strval($totalPrice), strval($voucher['VRDESCCUPOM']/100), 2);
                    }
                    else {
                        $voucherDiscount = $voucher['VRDESCCUPOM'];
                    }
                    $desconto += floatval($voucherDiscount);
                    $precoReal = floatval(bcadd(strval($produto['quantidade'] * ($preco + $subsidy)), strval($acrescimo), 2));
                    if ($desconto >= $precoReal){
                        $desconto = floatval(bcsub(strval($precoReal), '0.01', 2));
                    }
                    $produto['VOUCHER'] = $voucher['CDCUPOMDESCFOS'];
                }

                // Campanha compre e ganhe
                $imp_produtos[$idx]['CDCAMPCOMPGANHE'] = $produto['CDCAMPCOMPGANHE'];
                $imp_produtos[$idx]['DTINIVGCAMPCG'] = $produto['DTINIVGCAMPCG'];

                if (!empty($produto['CDCAMPCOMPGANHE'])){
                    $preco     = bcmul('0.01', strval($produto['quantidade'] * sizeof($produto['produtos'])), 2);
                    $desconto  = 0;
                    $acrescimo = 0;
                    $subsidy   = 0;
                }
                if (!empty($produto['DESCCOMPGANHE'])){
                    $preco = bcsub($preco, strval($produto['DESCCOMPGANHE']), 2);
                }

				// Código do tratamento do NRSEQPRODCOM (somente promoção inteligente).
				if ($r_valida_prod['IDTIPOCOMPPROD'] == '3' || $r_valida_prod['IDTIPOCOMPPROD'] == '6'){
					$nrSeqProdCom = str_pad((string) ($lastNRSEQPRODCOM + 1), 3, '0', STR_PAD_LEFT);
					$lastNRSEQPRODCOM++;
				}
				else {
					$nrSeqProdCom = null;
				}

				/*** FORMATAÇÃO DO PRODUTO PRINCIPAL PARA INSERÇÃO ***/
				$imp_produtos[$idx]['CDFILIAL'] = $session['CDFILIAL'];
				$imp_produtos[$idx]['CDLOJA'] = $session['CDLOJA'];
				$imp_produtos[$idx]['CDCAIXA'] = $session['CDCAIXA'];
				$imp_produtos[$idx]['NRMESA'] = $nrMesa;
				$imp_produtos[$idx]['NMSALA'] = $ambiente;
				$imp_produtos[$idx]['CDSALA'] = $CDSALA;
                $imp_produtos[$idx]['FILIALVIGENCIA'] = $session['FILIALVIGENCIA'];
				$imp_produtos[$idx]['NRCONFTELA'] = $session['NRCONFTELA'];
                $imp_produtos[$idx]['DTINIVIGENCIA'] = $session['DTINIVIGENCIA'];
				$imp_produtos[$idx]['NRVENDAREST'] = $nrVendaRest;
				$imp_produtos[$idx]['NRCOMANDA'] = $nrComanda;
				$imp_produtos[$idx]['CDPRODUTO'] = $produto['codigo'];
				$imp_produtos[$idx]['DTHRINCOMVEN'] = $produto['DTHRINCOMVEN'];
				$imp_produtos[$idx]['IDTIPOCOMPPROD'] = $r_valida_prod['IDTIPOCOMPPROD']; // TIPO DO PRODUTO (3 indica uma promoção).
				$imp_produtos[$idx]['CDARVPROD'] = $r_valida_prod['CDARVPROD'];
				$imp_produtos[$idx]['NMPRODUTO'] = $r_valida_prod['NMPRODUTO'];
				$imp_produtos[$idx]['NRSEQPRODCOM'] = $nrSeqProdCom;
				$imp_produtos[$idx]['NRLUGARMESA'] = $produto['posicao']; // posição na mesa
				$imp_produtos[$idx]['IDPOSOBSPED'] = $session['IDPOSOBSPED']; // imprime observação antes ou depois
				$imp_produtos[$idx]['CDVENDEDOR'] = $cdVendedor;
				$imp_produtos[$idx]['ORDEMIMP'] = $ordemImp++; // ordem de impressão (importante)
				$imp_produtos[$idx]['QTPRODCOMVEN'] = $produto['quantidade']; // quantidade passada pelo client
                $imp_produtos[$idx]['IDPESAPROD'] = $r_valida_prod['IDPESAPROD'];
                $imp_produtos[$idx]['IDIMPRODUVEZ'] = $r_valida_prod['IDIMPRODUVEZ'];
				$imp_produtos[$idx]['VRPRECCOMVEN'] = $preco;
				$imp_produtos[$idx]['VRPRECCLCOMVEN'] = $subsidy;
				$imp_produtos[$idx]['VRDESCCOMVEN'] = $desconto;
				$imp_produtos[$idx]['VRACRCOMVEN'] = $acrescimo;
                $imp_produtos[$idx]['CDCUPOMDESCFOS'] = $produto['VOUCHER'];
				$imp_produtos[$idx]['ocorrencias'] = $produto['ocorrencias']; // Código das observações.
				// Observações a serem impressas.
				if (empty($produto['observacao'])) $imp_produtos[$idx]['TXPRODCOMVEN'] = null;
				else $imp_produtos[$idx]['TXPRODCOMVEN'] = $produto['observacao'];
				// Observação customizada.
				if (empty($produto['CUSTOMOBS'])) $imp_produtos[$idx]['DSOBSPEDDIG'] = null; // observação customizada
				else $imp_produtos[$idx]['DSOBSPEDDIG'] = $produto['CUSTOMOBS'];
				// Atraso de produtos.
				if ($produto['ATRASOPROD'] === 'Y') $imp_produtos[$idx]['ATRASOPROD'] = $session['NRATRAPADRAO']; // atraso do pedido
				else $imp_produtos[$idx]['ATRASOPROD'] = null;
                // Para viagem.
                $imp_produtos[$idx]['IDORIGEMVENDA'] = null;
                if ($session['IDCTRLPEDVIAGEM'] == 'S' && $produto['TOGO'] === 'Y'){
                    $imp_produtos[$idx]['IDORIGEMVENDA'] = 'TGO_MES';
                }

				/*** REFIL MECHANICS ***/
				$imp_produtos[$idx] = $this->trataRefil(
					$session,
					$produto,
					$imp_produtos[$idx],
					$dataset['NRVENDAREST'],
					$dataset['NRCOMANDA'],
					$r_valida_prod
				);

				/*** PREPARA IMPRESSÃO DE PRODUTOS PAIS ***/
				if ($r_valida_prod['IDTIPOCOMPPROD'] == '3' || $r_valida_prod['IDTIPOCOMPPROD'] == '6'){ // Promoções intelignentes.
					if (($r_valida_prod['IDIMPPRODUTO'] == '') || ($r_valida_prod['IDIMPPRODUTO'] == '1')){
						// Insere o produto pai na ITCOMANDAVEN e os filhos na ITCOMANDAEST.
						$imp_produtos[$idx]['IDTIPOINSERCAO'] = 'V';
					}
					else {
						// Insere os filhos na ITCOMANDAVEN - o produto pai não será impresso.
						$imp_produtos[$idx]['IDTIPOINSERCAO'] = 'N';
					}
                    $imp_produtos[$idx]['CDPRODPROMOCAO'] = $produto['codigo'];
					$imp_produtos[$idx]['NMPRODPROMOCAO'] = $NMPROMOCAO;
					$imp_produtos[$idx]['IDIMPPROMOCAO'] = 'S';
				}
				else { // Produtos normais.
					$imp_produtos[$idx]['IDTIPOINSERCAO'] = 'V';
					$imp_produtos[$idx]['CDPRODPROMOCAO'] = null;
					$imp_produtos[$idx]['NMPRODPROMOCAO'] = null;
					$imp_produtos[$idx]['IDIMPPROMOCAO'] = 'N';
				}

                // Mecânicas do rodízio.
                $rodizioFlag = false;
                if ($r_valida_prod['IDTIPOCOMPPROD'] == '6'){
                    // Verifica se o rodízio já foi pedido para a posição atual.
                    $params = array(
                        'CDFILIAL' => $session['CDFILIAL'],
                        'NRVENDAREST' => $nrVendaRest,
                        'NRCOMANDA' => $nrComanda,
                        'CDPRODUTO' => $produto['codigo']
                    );
                    $rodizioDetails = $this->entityManager->getConnection()->fetchAssoc("GET_RODIZIO", $params);

                    if (!empty($rodizioDetails)){
                        // Trata o tempo do rodízio que já foi pedido.
                        if ($this->util->databaseIsOracle()){
                            $DTHRABERMESA = \DateTime::createFromFormat('Y-m-d H:i:s', $DTHRABERMESA);
                        }
                        else {
                            $DTHRABERMESA = \DateTime::createFromFormat('Y-m-d H:i:s.u', $DTHRABERMESA);
                        }
                        // Verifica se o tempo do rodízio foi definido.
                        if (!empty($session['HRTEMPOROD'])){
                            $tempoRodizio = $DTHRABERMESA->getTimestamp() + 3600 * intval(substr($session['HRTEMPOROD'], 0, 2)) + 60 * intval(substr($session['HRTEMPOROD'], 2));
                        }
                        else {
                            // Se o tempo do rodízio não foi definido, o rodízio fica para sempre.
                            $tempoRodizio = $DTHRABERMESA + 1;
                        }
                        $tempoAtual = $produto['DTHRINCOMVEN']->getTimestamp();
                        // Verifica se o tempo do rodízio ainda é válido.
                        if ($tempoRodizio > $tempoAtual){
                            $imp_produtos[$idx]['IDTIPOINSERCAO'] = 'N';
                            $rodizioFlag = true;
                        }
                        else {
                            throw new \Exception("O tempo de dura&ccedil;&atilde;o do rod&iacute;zio est&aacute; esgotado.");
                        }
                    }
                }

				/*** FORMATAÇÃO PRODUTOS FILHO PARA INSERÇÃO ***/
				if (is_array($promo_int)){
					// Navega nos produtos selecionados na promoção.
					$idxPai = $idx;
					$ordemImpPromoc = 0.01;
					foreach($promo_int as $produto_int) {
						// Valida se produto existe.
						$params = array($produto_int['CDPRODUTO']);
						$r_get_produto = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_PRODUTO", $params);
						if ($r_get_produto['CDPRODUTO'] == ''){
							return array('funcao' => '0', 'error' => '023'); // Produto não cadastrado.
						}

						// Valida se o produto possui alíquota cadastrada.
						$params = array($session['CDFILIAL'], $produto_int['CDPRODUTO']);
						$r_get_aliquota = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_ALIQUOTA", $params);
						if ($r_get_aliquota['COUNT'] == 0){
							return array('funcao' => '0', 'error' => '440'); // Produto sem alíquota.
						}

						if ($produto['IMPRESSORA']) {
							$imp_produtos[$idx]['NRSEQIMPRLOJA'] = $produto['IMPRESSORA'];
						}

						// Insere o produto filho no array junto com os demais.
						$idx++;
						$imp_produtos[$idx]['CDFILIAL'] = $session['CDFILIAL'];
						$imp_produtos[$idx]['CDLOJA'] = $session['CDLOJA'];
						$imp_produtos[$idx]['CDCAIXA'] = $session['CDCAIXA'];
						$imp_produtos[$idx]['NRMESA'] = $nrMesa;
						$imp_produtos[$idx]['NMSALA'] = $ambiente;
						$imp_produtos[$idx]['CDSALA'] = $CDSALA;
                        $imp_produtos[$idx]['FILIALVIGENCIA'] = $session['FILIALVIGENCIA'];
						$imp_produtos[$idx]['NRCONFTELA'] = $session['NRCONFTELA'];
                        $imp_produtos[$idx]['DTINIVIGENCIA'] = $session['DTINIVIGENCIA'];
						$imp_produtos[$idx]['NRVENDAREST'] = $nrVendaRest;
						$imp_produtos[$idx]['NRCOMANDA'] = $nrComanda;
						$imp_produtos[$idx]['DTHRINCOMVEN'] = $produto['DTHRINCOMVEN'];
						$imp_produtos[$idx]['CDPRODUTO'] = $produto_int['CDPRODUTO'];
						$imp_produtos[$idx]['IDTIPOCOMPPROD'] = 'X';
						if (empty($produto['codigo']) || $produto['IDTIPCOBRA'] != null){
							$imp_produtos[$idx]['CDPRODPROMOCAO'] = null;
						}
						else {
							$imp_produtos[$idx]['CDPRODPROMOCAO'] = $produto['codigo'];
						}
						$imp_produtos[$idx]['NMPRODPROMOCAO'] = $NMPROMOCAO; // nome produto pai
						$imp_produtos[$idx]['CDARVPROD'] = $r_get_produto['CDARVPROD'];
						$imp_produtos[$idx]['NMPRODUTO'] = $r_get_produto['NMPRODUTO'];
						$imp_produtos[$idx]['NRSEQPRODCOM'] = $nrSeqProdCom;
						$imp_produtos[$idx]['NRLUGARMESA'] = $produto['posicao']; // posição na mesa
						$imp_produtos[$idx]['IDPOSOBSPED'] = $session['IDPOSOBSPED']; // imprime observação antes ou depois do produto
						$imp_produtos[$idx]['CDVENDEDOR'] = $cdVendedor;
						$imp_produtos[$idx]['IDIMPPROMOCAO'] = 'N';
						$imp_produtos[$idx]['ORDEMIMP'] = $produto_int['ORDEMIMP'] + $ordemImpPromoc; // ordem de impressão (importante)
						$imp_produtos[$idx]['QTPRODCOMVEN'] = $produto['quantidade'] * $produto_int['QTPRODCOMVEN']; // quantidade
                        $imp_produtos[$idx]['IDPESAPROD'] = $r_get_produto['IDPESAPROD'];
                        $imp_produtos[$idx]['IDIMPRODUVEZ'] = $r_get_produto['IDIMPRODUVEZ'];
						$imp_produtos[$idx]['ocorrencias'] = $produto_int['ocorrencias']; // código das observações
						// Observações a serem impressas.
						if (empty($produto_int['TXPRODCOMVEN'])) $imp_produtos[$idx]['TXPRODCOMVEN'] = null;
						else $imp_produtos[$idx]['TXPRODCOMVEN'] = $produto_int['TXPRODCOMVEN'];
						// Observação customizada.
						if (empty($produto_int['OBSERVACAO'])) $imp_produtos[$idx]['DSOBSPEDDIG'] = null;
						else $imp_produtos[$idx]['DSOBSPEDDIG'] = $produto_int['OBSERVACAO'];
						// Atraso de produtos.
						if ($produto_int['ATRASOPROD'] === 'Y') $imp_produtos[$idx]['ATRASOPROD'] = $session['NRATRAPADRAO']; // atraso do pedido
						else $imp_produtos[$idx]['ATRASOPROD'] = null;
                        // Para viagem.
                         $imp_produtos[$idx]['IDORIGEMVENDA'] = null;
                        if ($session['IDCTRLPEDVIAGEM'] == 'S' && $produto_int['TOGO'] === 'Y'){
                            $imp_produtos[$idx]['IDORIGEMVENDA'] = 'TGO_MES';
                        }

						// REFIL FOR SMART PROMO (there is no refil!)
						$imp_produtos[$idx]['REFIL']       = $imp_produtos[$idxPai]['REFIL'];
						$imp_produtos[$idx]['QTITEMREFIL'] = $imp_produtos[$idxPai]['QTITEMREFIL'];

						/*** PREPARA IMPRESSÃO (e preço) DE PRODUTOS FILHO ***/
						if ($r_valida_prod['IDIMPPRODUTO'] == '2'){
							$imp_produtos[$idx]['IDTIPOINSERCAO'] = 'V';
							$imp_produtos[$idx]['VRPRECCOMVEN'] = $produto_int['VRPRECCOMVEN'];
                            $imp_produtos[$idx]['VRPRECCLCOMVEN'] = $produto_int['VRPRECCLCOMVEN'];
							$imp_produtos[$idx]['VRDESCCOMVEN'] = $produto_int['VRDESCCOMVEN'];
							$imp_produtos[$idx]['VRACRCOMVEN'] = $produto_int['VRACRCOMVEN'];
                            $imp_produtos[$idx]['RODIZIO'] = false;
						}
						else {
							$imp_produtos[$idx]['IDTIPOINSERCAO'] = 'E';
							$imp_produtos[$idx]['VRPRECCOMVEN'] = $preco / count($produto_int);
							$imp_produtos[$idx]['VRPRECCLCOMVEN'] = $subsidy;
							$imp_produtos[$idx]['VRDESCCOMVEN'] = $desconto / count($produto_int);
							$imp_produtos[$idx]['VRACRCOMVEN'] = $acrescimo / count($produto_int);
                            $imp_produtos[$idx]['RODIZIO'] = $rodizioFlag; // Marca se irá ser tratado como rodízio.
                            // Se for rodizio, o produto irá ficar com o NRPRODCOMVEN do rodízio já inserido.
                            if ($rodizioFlag) $imp_produtos[$idx]['NRPRODCOMVEN'] = $rodizioDetails['NRPRODCOMVEN'];
						}
                        $imp_produtos[$idx]['CDCUPOMDESCFOS'] = $produto_int['VOUCHER'];
						$ordemImpPromoc += 0.01;

                        $imp_produtos[$idx]['CDCAMPCOMPGANHE'] = $produto['CDCAMPCOMPGANHE'];
                        $imp_produtos[$idx]['DTINIVGCAMPCG'] = $produto['DTINIVGCAMPCG'];
					}
				}
				$idx++;
			}
			/*-- FIM DE PROCESSAMENTO DE PRODUTOS --*/

			/*** AGRUPA PEDIDOS SEMELHANTES NA ITCOMANDAEST ***/
			$idx = 0;
			$productBuffer = $imp_produtos;
			$imp_produtos = array();
			foreach($productBuffer as $prod){
				if ($prod['IDTIPOINSERCAO'] == 'V') $imp_produtos[$idx] = $prod;
				else {
					$control = false;
					foreach($imp_produtos as &$item){
						if ($prod['CDPRODUTO'] == $item['CDPRODUTO'] && $prod['NRSEQPRODCOM'] == $item['NRSEQPRODCOM'] && $item['IDTIPOINSERCAO'] != 'V'){
							$item['QTPRODCOMVEN'] += 1;
							$item['VRPRECCOMVEN'] += $prod['VRPRECCOMVEN'];
							$item['VRDESCCOMVEN'] += $prod['VRDESCCOMVEN'];
							if (!empty($prod['TXPRODCOMVEN'])) $item['TXPRODCOMVEN'] = $item['TXPRODCOMVEN'] . '; ' . $prod['TXPRODCOMVEN'];
							$control = true;
						}
					}
					if (!$control) $imp_produtos[] = $prod;
				}
				$idx++;
			}
			/*** DEFINIÇÃO DE CÓDIGOS ***/
			if (empty($dataset['saleProdPass'])) {
				$nrPedido = $this->geraSenhaProducao($session['CDLOJA'], $session['CDFILIAL'], $session['CDCAIXA']);
			} else {
			    $nrPedido = $dataset['saleProdPass'];
			}


			$params = array(
				$session['CDFILIAL'],
				$session['CDLOJA']
			);
			$CDGRPOCORPED = $this->entityManager->getConnection()->fetchAll("SQL_GET_CDGRPOCORPED", $params);

			if (empty($CDGRPOCORPED)){
				return array('funcao' => '0', 'error' => '067'); // Código de grupo de observação não encontrado. Verifique o cadastro de observações.
			} else {
				$CDGRPOCORPED = $CDGRPOCORPED[0]['CDGRPOCORPED'];
			}

			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////
			//                                        PARTE 3                                          //
			/////////////////////////////////////////////////////////////////////////////////////////////
			/////////////////////////////////////////////////////////////////////////////////////////////

			/*** INSERÇÃO DE PEDIDOS NO BANCO ***/
			foreach($imp_produtos as $ins_produto){
				if ($session['IDCONTROPROD'] =='S'){
					$params = array($ins_produto['CDFILIAL'], $ins_produto['CDLOJA'], $ins_produto['CDPRODUTO']);
					$r_prod_loja = $this->entityManager->getConnection()->fetchAssoc("SQL_PROD_LOJA", $params);
				}

				// Define setor de produção.
				$idStPrComVen = '3';
				if (($session['IDCONTROPROD'] == 'S') && ($r_prod_loja['CDSETOR'] != '')) $idStPrComVen = '1';

				// Caso o produto esteja marcado como impresso no cupom fiscal, tem que inserir na ITCOMANDAVEN.
				if ($ins_produto['IDTIPOINSERCAO'] == 'V'){
					$this->util->newCode('ITCOMANDAVEN' . $ins_produto['CDFILIAL'] . $ins_produto['NRCOMANDA']);
					$nrProdComVen = $this->util->getNewCode('ITCOMANDAVEN' . $ins_produto['CDFILIAL'] . $ins_produto['NRCOMANDA'], 6);
					if (empty($ins_produto['CDPRODPROMOCAO'])) {
						$ins_produto['CDPRODPROMOCAO'] = null;
					}
					if ($ins_produto['VRPRECCOMVEN'] == 0 && $ins_produto['REFIL'] !== 'S') {
                        return array('funcao' => '0', 'message' => 'Produto ' . $ins_produto['CDPRODUTO'] . ' sem preço.');
                    }

                    $TOTAL = floatval(bcsub($ins_produto['QTPRODCOMVEN'] * ($ins_produto['VRPRECCOMVEN'] + $ins_produto['VRPRECCLCOMVEN']) + $ins_produto['VRACRCOMVEN'], $ins_produto['VRDESCCOMVEN'], 2));
                    if ($TOTAL < 0.01){
                        return array('funcao' => '0', 'message' => 'O valor calculado para o produto ' . $ins_produto['NMPRODUTO'] . ' ficou abaixo de R$0,01. Verifique a parametrização.');
                    }

                    if (!empty($ins_produto['DTINIVGCAMPCG'])){
                        if ($this->util->databaseIsOracle()){
                            $DTINIVGCAMPCG = \DateTime::createFromFormat('Y-m-d H:i:s', $ins_produto['DTINIVGCAMPCG']);
                        }
                        else {
                            $DTINIVGCAMPCG = \DateTime::createFromFormat('Y-m-d H:i:s.u', $ins_produto['DTINIVGCAMPCG']);
                        }
                    }
                    else {
                        $DTINIVGCAMPCG = null;
                    }

					// Insere na ITCOMANDAVEN.
					$params = array(
						'CDFILIAL' => $ins_produto['CDFILIAL'],
						'NRVENDAREST' => $ins_produto['NRVENDAREST'],
						'NRCOMANDA' => $ins_produto['NRCOMANDA'],
						'NRPRODCOMVEN' => $nrProdComVen,
						'CDPRODUTO' => $ins_produto['CDPRODUTO'],
						'QTPRODCOMVEN' => $ins_produto['QTPRODCOMVEN'],
						'VRPRECCOMVEN' => $ins_produto['VRPRECCOMVEN'],
						'TXPRODCOMVEN' => $ins_produto['TXPRODCOMVEN'],
						'IDSTPRCOMVEN' => $idStPrComVen,
						'VRDESCCOMVEN' => intval($ins_produto['VRDESCCOMVEN']*100)/100,
						'NRLUGARMESA' => $ins_produto['NRLUGARMESA'],
						'DTHRINCOMVEN' => $ins_produto['DTHRINCOMVEN'],
						'IDPRODIMPFIS' => 'N',
						'CDLOJA' => $ins_produto['CDLOJA'],
						'VRACRCOMVEN' => $ins_produto['VRACRCOMVEN'],
						'NRSEQPRODCOM' => $ins_produto['NRSEQPRODCOM'],
						'NRSEQPRODCUP' => $nrProdComCup,
						'VRPRECCLCOMVEN' => floatval($ins_produto['VRPRECCLCOMVEN']),
						'CDCAIXACOLETOR' => $ins_produto['CDCAIXA'],
						'CDPRODPROMOCAO' => $ins_produto['CDPRODPROMOCAO'],
						'CDVENDEDOR' => $ins_produto['CDVENDEDOR'],
						'CDSENHAPED' => $nrPedido,
						'NRATRAPRODCOVE' => $ins_produto['ATRASOPROD'],
                        'IDORIGEMVENDA' => $ins_produto['IDORIGEMVENDA'],
						'IDORIGPEDCMD' => 'MOB',
						'DSOBSPEDDIGCMD' => $ins_produto['DSOBSPEDDIG'],
						'IDPRODREFIL' => $ins_produto['REFIL'],
						'QTITEMREFIL' => $ins_produto['QTITEMREFIL'],
						'NRORG' => $session['NRORG'],
                        'CDCUPOMDESCFOS' => $ins_produto['CDCUPOMDESCFOS'],
                        'CDCAMPCOMPGANHE' => $ins_produto['CDCAMPCOMPGANHE'],
                        'DTINIVGCAMPCG' => $DTINIVGCAMPCG
					);
					$types = array (
						'DTHRINCOMVEN' => \Doctrine\DBAL\TypeS\Type::DATETIME,
                        'DTINIVGCAMPCG' => \Doctrine\DBAL\TypeS\Type::DATETIME
					);
					$this->entityManager->getConnection()->executeQuery("SQL_INS_ITCOMANDAVEN", $params, $types);

					// Guarda as observações na tabela.
					if (!empty($ins_produto['ocorrencias'])){
						foreach($ins_produto['ocorrencias'] as $CDOCORR){
							$params = array(
								'CDFILIAL'     => $ins_produto['CDFILIAL'],
								'NRVENDAREST'  => $ins_produto['NRVENDAREST'],
								'NRCOMANDA'    => $ins_produto['NRCOMANDA'],
								'NRPRODCOMVEN' => $nrProdComVen,
								'CDGRPOCOR'    => $CDGRPOCORPED,
								'CDOCORR'      => $CDOCORR
							);
							$this->entityManager->getConnection()->executeQuery("SQL_INS_OBSITCOMANDAVEN", $params);

							/*** INSERÇÃO DE ACRÉSCIMOS PEDIDOS NO BANCO ***/
							$this->insereAcrescimo($ins_produto, $CDOCORR, $cdCliente, $idStPrComVen, $nrProdComCup, $nrPedido, $cdConsumidor, $session);
						}
					}
				}
				else if ($ins_produto['IDTIPOINSERCAO'] == 'E'){
                    $insereItem = true;
                    if ($ins_produto['RODIZIO']){
                        // Se for rodízio, precisa verificar se o item existe.
                        $params = array(
                            'CDFILIAL' => $ins_produto['CDFILIAL'],
                            'NRVENDAREST' => $ins_produto['NRVENDAREST'],
                            'NRCOMANDA' => $ins_produto['NRCOMANDA'],
                            'NRPRODCOMVEN' => $ins_produto['NRPRODCOMVEN'],
                            'CDPRODUTO' => $ins_produto['CDPRODUTO']
                        );
                        $itensRodizio = $this->entityManager->getConnection()->fetchAssoc("GET_RODIZIO_ITEMS", $params);
                        // Se o item já existir, atualiza a quantidade ao invés de inserir.
                        if (!empty($itensRodizio)){
                            $params['QTPROCOMEST'] = floatval($itensRodizio) + $ins_produto['QTPRODCOMVEN'];
                            $this->entityManager->getConnection()->executeQuery("ATUALIZA_RODIZIO", $params);
                            $insereItem = false;
                        }
                        else {
                            $nrProdComVen = $ins_produto['NRPRODCOMVEN'];
                            $insereItem = true;
                        }
                    }

                    // Insere na ITCOMANDAEST (somente promoção inteligente).
                    if ($insereItem){ // Não insere se o produto foi modificado acima.
                        $params = array(
                            $ins_produto['CDFILIAL'],
                            $ins_produto['NRVENDAREST'],
                            $ins_produto['NRCOMANDA'],
                            $nrProdComVen,
                            $ins_produto['CDPRODUTO'],
                            $ins_produto['QTPRODCOMVEN'],
                            $ins_produto['VRPRECCOMVEN'],
                            intval($ins_produto['VRDESCCOMVEN']*100)/100,
                            $ins_produto['TXPRODCOMVEN'],
                            $ins_produto['ATRASOPROD'],
                            $ins_produto['DSOBSPEDDIG']
                        );
                        $this->entityManager->getConnection()->executeQuery("SQL_INS_ITCOMANDAEST", $params);

                        // Guarda as observações na tabela.
                        if (!empty($ins_produto['ocorrencias'])){
                            foreach($ins_produto['ocorrencias'] as $CDOCORR){
                                $params = array(
                                    $ins_produto['CDFILIAL'],
                                    $ins_produto['NRVENDAREST'],
                                    $ins_produto['NRCOMANDA'],
                                    $nrProdComVen,
                                    $ins_produto['CDPRODUTO'],
                                    $CDGRPOCORPED,
                                    $CDOCORR
                                );
                                $this->entityManager->getConnection()->executeQuery("SQL_INS_OBSITCOMANDAEST", $params);

                                /*** INSERÇÃO DE ACRÉSCIMOS PEDIDOS NO BANCO ***/
                                $this->insereAcrescimo($ins_produto, $CDOCORR, $cdCliente, $idStPrComVen, $nrProdComCup, $nrPedido, $cdConsumidor, $session);
                            }
                        }
                    }
                }
            }

            /////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////////////////////////////////////////////////////////////////////
            //                                        PARTE 4                                          //
            /////////////////////////////////////////////////////////////////////////////////////////////
            /////////////////////////////////////////////////////////////////////////////////////////////

			/*** IMPRESSÃO DOS PEDIDOS ***/
			if (!$this->utilizaImpressaoPHP && !$this->utilizaImpressaoPonte) {
				$this->impressaoDelphiAPI->criaCdsImpressoras($session['CDFILIAL'], $session['CDLOJA']);
			}
			$imp_produtos = $productBuffer;

			array_push($this->impProdParams, array(
				'CDFILIAL' => $session['CDFILIAL'],
				'CDLOJA' => $session['CDLOJA'],
				'PRODUTOS' => $imp_produtos,
				'CDVENDEDOR' => $cdVendedor,
				'DSCOMANDA' => $dsComanda,
				'NRMESA' => $nrMesa,
				'NRPEDIDO' => $nrPedido,
				'MODO' => $modo
			));
		} catch(\Exception $e){
			Exception::logException($e);
			$message = utf8_encode($e->getMessage());
			$message = is_string($message) ? $message : 'erro ao converter mensagem de erro. Confira o log.';
			throw new \Exception('Erro ao gerar pedido: '. $message, 1);
		}

		/* GERAÇÃO DO LOG */
		$produtos = '';
		$result =  array('funcao' => '1');
		foreach ($imp_produtos as $ins_produto){
			$produtos .= $ins_produto['CDPRODUTO'] . ", ";
		}
		$produtos = rtrim($produtos, ", .");
		$produtos = substr($produtos, 0, 150);
		$this->util->logFOS($session['CDFILIAL'], $session['CDCAIXA'], 'PED_MES', $session['CDOPERADOR'], $dataset['supervisor'], "Waiter - Pedido", "Pedido na mesa ".$nrMesa." dos produtos: ".$produtos.".");

		return $result;
	}


    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////
    //                                        FUNÇÕES                                          //
    /////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////


	private function adiantamentoPendente($session, $pedido, $nrVendaRest){
		$result = array(
			'error' => false,
			'message' => ''
		);
		// monsta array de posições
		$positions = array_map(function($position){
			return str_pad($position, 2, '0', STR_PAD_LEFT);
		}, array_column($pedido, 'posicao'));

		$params = array(
			'CDFILIAL'    => $session['CDFILIAL'],
		    'NRVENDAREST' => array($nrVendaRest),
		    'NRLUGARMESA' => $positions
		);
		$types  = array(
			'CDFILIAL'    => \PDO::PARAM_STR,
		    'NRVENDAREST' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY,
		    'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
		);

		$movcaixamob = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_TRANSACOES_POSICAO_MOVCAIXAMOB", $params, $types);

		if (!empty($movcaixamob)){
			$result['error'] = true;
		}

		return $result;
	}

	private function insereAcrescimo($ins_produto, $CDOCORR, $cdCliente, $idStPrComVen, $nrProdComCup, $nrPedido, $cdConsumidor, $session){
		$params = array(
			$ins_produto['CDFILIAL'],
			$ins_produto['CDLOJA'],
			$CDOCORR
		);
		$obs_type = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_OBS_TYPE", $params);

		// Verifica se a observação é um acrescimo.
		if ($obs_type['IDCONTROLAOBS'] === 'A' && !empty($obs_type['CDPRODUTO'])) {
			// Busca o preço do acréscimo.
			$r_retornaPreco = $this->precoAPI->buscaPreco($ins_produto['CDFILIAL'], $cdCliente, $obs_type['CDPRODUTO'], $ins_produto['CDLOJA'], $cdConsumidor);
			$preco     = floatval($r_retornaPreco['PRECO']);
            $subsidio  = floatval($r_retornaPreco['PRECOCLIE']);
			$desconto  = floatval($r_retornaPreco['DESC']);
			$acrescimo = floatval($r_retornaPreco['ACRE']);

			// Valida o preço.
			if (!empty($preco) && $preco > 0) {
				// Insere na ITCOMANDAVEN (o acréscimo).
				$this->util->newCode('ITCOMANDAVEN' . $ins_produto['CDFILIAL'] . $ins_produto['NRCOMANDA']);
				$nrProdComVen = $this->util->getNewCode('ITCOMANDAVEN' . $ins_produto['CDFILIAL'] . $ins_produto['NRCOMANDA'], 6);
				$params = array(
					'CDFILIAL' => $ins_produto['CDFILIAL'],
					'NRVENDAREST' => $ins_produto['NRVENDAREST'],
					'NRCOMANDA' => $ins_produto['NRCOMANDA'],
					'NRPRODCOMVEN' => $nrProdComVen,
					'CDPRODUTO' => $obs_type['CDPRODUTO'],
					'QTPRODCOMVEN' => $ins_produto['QTPRODCOMVEN'],
					'VRPRECCOMVEN' => $preco,
					'TXPRODCOMVEN' => null,
					'IDSTPRCOMVEN' => $idStPrComVen,
					'VRDESCCOMVEN' => intval($desconto*100)/100,
					'NRLUGARMESA' => $ins_produto['NRLUGARMESA'],
					'DTHRINCOMVEN' => $ins_produto['DTHRINCOMVEN'],
					'IDPRODIMPFIS' => 'N',
					'CDLOJA' => $ins_produto['CDLOJA'],
					'VRACRCOMVEN' => $acrescimo,
					'NRSEQPRODCOM' => $ins_produto['NRSEQPRODCOM'],
					'NRSEQPRODCUP' => $nrProdComCup,
					'VRPRECCLCOMVEN' => $subsidio,
					'CDCAIXACOLETOR' => $ins_produto['CDCAIXA'],
					'CDPRODPROMOCAO' => $ins_produto['CDPRODPROMOCAO'],
					'CDVENDEDOR' => $ins_produto['CDVENDEDOR'],
					'CDSENHAPED' => $nrPedido,
					'NRATRAPRODCOVE' => $ins_produto['ATRASOPROD'],
					'IDORIGPEDCMD' => 'MOB',
					'DSOBSPEDDIGCMD' => $ins_produto['DSOBSPEDDIG'],
					'IDPRODREFIL' => $ins_produto['REFIL'],
					'QTITEMREFIL' => $ins_produto['QTITEMREFIL'],
                    'IDORIGEMVENDA' => $ins_produto['IDORIGEMVENDA'],
					'NRORG' => $session['NRORG'],
                    'CDCUPOMDESCFOS' => null,
                    'CDCAMPCOMPGANHE' => $ins_produto['CDCAMPCOMPGANHE'],
                    'DTINIVGCAMPCG' => $DTINIVGCAMPCG
				);
				$types = array (
					'DTHRINCOMVEN' => \Doctrine\DBAL\TypeS\Type::DATETIME,
                    'DTINIVGCAMPCG' => \Doctrine\DBAL\TypeS\Type::DATETIME
				);
				$this->entityManager->getConnection()->executeQuery("SQL_INS_ITCOMANDAVEN", $params, $types);
			}
		}
	}

	private function trataRefil($session, $produto, $produtoImpressao, $NRVENDAREST, $NRCOMANDA, $r_valida_prod) {
		if ($produto['REFIL']){
			$produtoImpressao['REFIL'] = 'S'; // Produto será inserido como refil.
			// Verifica se o refil já foi cobrado.
			$params = array(
				$session['CDFILIAL'],
				$NRVENDAREST,
				$NRCOMANDA,
				$produto['codigo'],
				$produtoImpressao['NRLUGARMESA']
			);

			$refilCheck = $this->entityManager->getConnection()->fetchAssoc("SQL_CHECK_REFIL", $params);

			if (empty($refilCheck)) {
				// Primeiro refil.
				$produtoImpressao['QTITEMREFIL'] = 1;
			}
			else {
				// Refil já foi inserido e cobrado, então removemos os preços.
				$produtoImpressao['VRPRECCOMVEN'] = 0;
				$produtoImpressao['VRPRECCLCOMVEN'] = 0;
				$produtoImpressao['VRDESCCOMVEN'] = 0;
				$produtoImpressao['VRACRCOMVEN'] = 0;
				$produtoImpressao['QTITEMREFIL'] = 0;

				// Incrementando o contador de refil.
				$newQuantity = $refilCheck['QTITEMREFIL'];
				$newQuantity++;
				$params = array(
					'QTITEMREFIL' => $newQuantity,
					'CDFILIAL' => $session['CDFILIAL'],
					'NRVENDAREST' => $NRVENDAREST,
					'NRCOMANDA' => $NRCOMANDA,
					'CDPRODUTO' => $produto['codigo'],
					'NRLUGARMESA' => $produtoImpressao['NRLUGARMESA']
				);
				$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_REFIL_QTTY", $params);
			}
		} else {
			$produtoImpressao['REFIL'] = 'N';
			if ($r_valida_prod['IDCONTROLAREFIL'] === 'N') {
				$produtoImpressao['QTITEMREFIL'] = null;
			} else {
				$produtoImpressao['QTITEMREFIL'] = 0;
			}
		}
		return $produtoImpressao;
	}

	public function formataPromocaoCombinada($cdfilial, $cdcliente, $cdconsumidor, $cdloja, $produtos, $CDPRODPROMOCAO, $nrVendaRest, $ordemImp, $isCombo, $IDTIPCOBRA, $IDIMPPRODUTO){
		$composicao = array();
		$treatedProducts = array();

		foreach ($produtos as $produto){
			$precoPromocao = $this->precoAPI->buscaPreco($cdfilial, $cdcliente, $produto['CDPRODUTO'], $cdloja, $cdconsumidor);

			$precoTotal = $precoPromocao["PRECO"] + $precoPromocao["PRECOCLIE"] - $precoPromocao["DESC"] + $precoPromocao["ACRE"];

            // Busca voucher.
            if (!empty($produto['VOUCHER'])){
                $params = array(
                    'CDCUPOMDESCFOS' => $produto['VOUCHER']['CDCUPOMDESCFOS']
                );
                $voucherData = $this->entityManager->getConnection()->fetchAssoc("SQL_VOUCHER_DATA", $params);
            }
            else {
                $voucherData = null;
            }

			$item = array();
            $item["CDPRODUTO"]      = $produto['CDPRODUTO'];
            $item["CDPRODUTOPAI"]   = $CDPRODPROMOCAO;
            $item["VRPRECCOMVEN"]   = floatval($precoPromocao["PRECO"]) ? floatval($precoPromocao["PRECO"]) : 0.0;
            $item["VRPRECCLCOMVEN"] = floatval($precoPromocao["PRECOCLIE"]) ? floatval($precoPromocao["PRECOCLIE"]) : 0.0;
            $item["QTPRODCOMVEN"]   = isset($produto['QTPRODCOMVEN']) ? $produto['QTPRODCOMVEN'] : 1;
            $item["ORDEMIMP"]       = $ordemImp;
            $item["ATRASOPROD"]     = $produto['ATRASOPROD'];
            $item["TOGO"]           = $produto['TOGO'];
            $item["OBSERVACAO"]     = $produto['DSOCORR_CUSTOM'];
            $item["ocorrencias"]    = $produto['CDOCORR'];
            $item["TXPRODCOMVEN"]   = $produto['TXPRODCOMVEN'];
            $item["IMPRESSORA"]     = $produto['IMPRESSORA'];
            $item['VRDESCCOMVEN']   = floatval(bcmul(strval($precoPromocao['DESC']), strval($item['QTPRODCOMVEN']), 2));
            $item['VRACRCOMVEN']    = floatval(bcmul(strval($precoPromocao['ACRE']), strval($item['QTPRODCOMVEN']), 2));
            $item['PRECOREAL']      = $precoTotal;
            $item['VOUCHER']        = $voucherData;

            if (!$isCombo){
                $descontoPromocao = $this->calculaDesconto($cdfilial, $CDPRODPROMOCAO, $produto['CDPRODUTO'], $precoTotal, $treatedProducts);
                if ($descontoPromocao < 0) $item['VRDESCCOMVEN'] -= $descontoPromocao;
                else $item['VRACRCOMVEN'] += $descontoPromocao;
            }

            array_push($composicao, $item);
			array_push($treatedProducts, $produto['CDPRODUTO']);
		}

		// rotina específica do Madero
		$this->util->validaDescontoDiferenciado($cdfilial, $CDPRODPROMOCAO, $composicao, 'VRDESCCOMVEN');

		// Trata os preços de produtos combinados.
		if ($isCombo){
			// Verifica o preço mais caro.
			$maxPrice = 0;
			if ($IDTIPCOBRA === 'C'){
				foreach ($composicao as $comboProd){
					if ($comboProd['VRPRECCOMVEN'] > $maxPrice){
						$maxPrice = $comboProd['VRPRECCOMVEN'];
					}
				}
			}

			// Ajusta quantidades.
			$specialQuant = 0;
			$quantity = round(1 / sizeof($composicao), 2);
			if ($quantity * sizeof($composicao) != 1){
				$specialQuant = $quantity + (1 - $quantity * sizeof($composicao));
			}

			// Implementa os ajustes de preço e quantidade.
			foreach ($composicao as &$comboProd){
				if ($IDTIPCOBRA == 'C'){
					$comboProd['VRPRECCOMVEN'] = $maxPrice;
				}

				if ($specialQuant > 0){
					$comboProd['QTPRODCOMVEN'] = $specialQuant;
					$specialQuant = 0;
				}
				else {
					$comboProd['QTPRODCOMVEN'] = $quantity;
				}
			}
		}

        // Campanha promocional.
        if ($IDIMPPRODUTO == '2'){ // Somente produtos filho que são cobrados.
            // Hora do pedido.
            if (isset($produtos[0]['DATETIME'])){
                $time = $produtos[0]['DATETIME']; // Hora do pedido quando tiver sendo recalculado.
            }
            else {
                $time = new \DateTime();
            }
            $time = $time->format('Hi');
            // Busca dados da campanha.
            $campanha = $this->campanhaPromocional($cdcliente, $cdconsumidor, $produtos, $time);
            // Define os produtos que serão modificados.
            if (!empty($campanha)){
                if ($campanha['IDAPLICADESACR'] == '1'){
                    $this->aplicaDescontoCampanha($composicao, $campanha, $campanha['CDPRODPRIN']);
                }
                else if ($campanha['IDAPLICADESACR'] == '2'){
                    $this->aplicaDescontoCampanha($composicao, $campanha, $campanha['CDPRODCOMB']);
                }
                else if ($campanha['IDAPLICADESACR'] == '3'){
                    $this->aplicaDescontoCampanha($composicao, $campanha, $campanha['CDPRODCOMB2']);
                }
                else if ($campanha['IDAPLICADESACR'] == '4'){ // Aplica o valor do desconto em todos os itens.
                    $this->rateiaDescontoCampanha($composicao, $campanha);
                }
            }
        }

        // Aplica descontos do voucher.
        foreach ($composicao as &$produto){
            if ($produto['VOUCHER']){
                $total = floatval(bcsub(strval($produto['QTPRODCOMVEN'] * ($produto['VRPRECCOMVEN'] + $produto['VRPRECCLCOMVEN']) + $produto['VRACRCOMVEN']), strval($produto['VRDESCCOMVEN']), 2));
                if ($produto['VOUCHER']['IDTIPODESC'] === "P"){
                    $voucherDiscount = bcmul(strval($total), strval($produto['VOUCHER']['VRDESCCUPOM']/100), 2);
                }
                else {
                    $voucherDiscount = $produto['VOUCHER']['VRDESCCUPOM'];
                }
                $produto['VRDESCCOMVEN'] += floatval($voucherDiscount);

                $precoReal = floatval(bcadd(strval($produto['QTPRODCOMVEN'] * ($produto['VRPRECCOMVEN'] + $produto['VRPRECCLCOMVEN'])), strval($produto['VRACRCOMVEN']), 2));
                if ($produto['VRDESCCOMVEN'] >= $precoReal){
                    $produto['VRDESCCOMVEN'] = floatval(bcsub(strval($precoReal), strval($produto['QTPRODCOMVEN'] * 0.01), 2));
                }
                $produto['VOUCHER'] = $produto['VOUCHER']['CDCUPOMDESCFOS'];
            }
        }

		return $composicao;
	}

    public function campanhaPromocional($CDCLIENTE, $CDCONSUMIDOR, $PRODUTOS, $HORA){
        // Busca tipo do consumidor.
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );
        $CDTIPOCONS = $this->entityManager->getConnection()->fetchAssoc("TIPO_CONSUMIDOR", $params);
        $CDTIPOCONS = !empty($CDTIPOCONS) ? $CDTIPOCONS['CDTIPOCONS'] : null;
        // Constrói parâmetro para a query.
        $productString = '_';
        foreach ($PRODUTOS as $product){
            $productString .= $product['CDPRODUTO'].'_';
        }
        // Busca dados da campanha.
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDTIPOCONS' => $CDTIPOCONS,
            'HORA' => $HORA,
            'STRPRODUCTS' => $productString
        );
        $campanha = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_CAMPANHA", $params);

        if ($campanha === false) $campanha = array();
        return $campanha;
    }

    public function aplicaDescontoCampanha(&$composicao, $campanha, $CDPRODUTO){
        foreach ($composicao as &$produto){
            if ($produto['CDPRODUTO'] == $CDPRODUTO){
                if ($campanha['IDPERCVALOR'] == 'V'){
                    $valor = floatval($campanha['VRDESCACRE']);
                }
                else {
                    $valor = $produto['PRECOREAL'] * floatval($campanha['VRDESCACRE']) / 100;
                }

                if ($campanha['IDDESCACRE'] == 'D'){
                    $produto['VRDESCCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($valor)), '1', '2'));
                }
                else {
                    $produto['VRACRCOMVEN'] = floatval(bcmul(str_replace(',','.',strval($valor)), '1', '2'));
                }
                break;
            };
        }
    }

    public function rateiaDescontoCampanha(&$composicao, $campanha){
        // Preco total de TOTOS os produtos, considerando descontos e acrescimos da ITEMPRECODIA.
        $totalPrice = array_reduce($composicao, function($total, $produto){
            return $total + $produto['PRECOREAL'];
        }, 0);
        // Preco final de TODOS os produtos, considerando o desconto ou acrescimo da campanha.
        if ($campanha['IDDESCACRE'] == 'D'){
            $modo = 'VRDESCCOMVEN';
            if ($campanha['IDPERCVALOR'] == 'V'){
                $finalPrice = $totalPrice - floatval($campanha['VRDESCACRE']);
            }
            else {
                $finalPrice = $totalPrice * (1 - floatval($campanha['VRDESCACRE']) / 100);
            }
        }
        else {
            $modo = 'VRACRCOMVEN';
            if ($campanha['IDPERCVALOR'] == 'V'){
                $finalPrice = $totalPrice + floatval($campanha['VRDESCACRE']);
            }
            else {
                $finalPrice = $totalPrice * (1 + floatval($campanha['VRDESCACRE']) / 100);
            }
        }
        // Aplica o desconto/acrescimo parcial nos produtos.
        foreach ($composicao as &$produto){
            if ($campanha['IDPERCVALOR'] == 'V'){
                $valor = ($produto['PRECOREAL'] / $totalPrice) * floatval($campanha['VRDESCACRE']);
            }
            else {
                $valor = $produto['PRECOREAL'] * floatval($campanha['VRDESCACRE']) / 100;
            }
            $produto[$modo] = floatval(bcmul(str_replace(',','.',strval($valor)), '1', '2'));
        }
        // Total do desconto/acrescimo implementado.
        $totalDescAcre = 0;
        foreach ($composicao as &$produto){
            $totalDescAcre += $produto[$modo];
        }
        // Rateia a diferença entre os descontos/acrescimos, caso exista.
        $diferenca = ($totalPrice - $finalPrice) - $totalDescAcre;
        if ($diferenca > 0.01){
            $qtdRateio = intval($diferenca/0.01);
            $i = 0;
            while ($qtdRateio > 0){
                foreach ($composicao as &$produto){
                    $totalProduto = round($produto['PRECOREAL'] + $produto['VRACRCOMVEN'] - $produto['VRDESCCOMVEN'], 2);
                    if ($produto[$modo] >= 0.01 && $totalProduto > 0.01){
                        $produto[$modo] += 0.01;
                        $qtdRateio--;
                    }
                    if ($qtdRateio == 0) break;
                }

                $i++;
                if ($i == 1000) throw new \Exception("Erro no rateio do desconto.");
            }
        }
    }

	public function calculaDesconto($cdfilial, $cdprodutoPai, $cdproduto, $precoAux, $produtos_ja_tratados){
        // Desconto por produto.
        $params = array($cdprodutoPai, $cdproduto);
        $descontoPorProduto = $this->entityManager->getConnection()->fetchAssoc("SQL_DESCONTO_POR_PRODUTO", $params);
        // Desconto por filial.
        $params = array($cdfilial, $cdprodutoPai, $cdproduto);
        $descontoPorFilial = $this->entityManager->getConnection()->fetchAssoc("SQL_DESCONTO_POR_FILIAL", $params);

        // Desconto por filial leva prioridade.
        if (!empty($descontoPorFilial)) $discountDetails = $descontoPorFilial;
        else $discountDetails = $descontoPorProduto;

        // Retorna caso não exista desconto.
        if (empty($discountDetails)) return 0;

        $valorFinal = 0;

        // Verifica se o desconto vai ser aplicado somente para o primeiro produto ou para todos.
        if ($discountDetails["IDAPLICADESCPR"] === "I"){
            $verificaAplicacaoDeDesconto = !in_array($cdproduto, $produtos_ja_tratados);
        }
        else $verificaAplicacaoDeDesconto = true;

        if ($verificaAplicacaoDeDesconto){
            if ($discountDetails["IDPERVALOR"] === "P"){
                // DESCONTO PERCENTUAL.
                $vrdesconto = isset($discountDetails["VRDESCONTO"]) ? $discountDetails["VRDESCONTO"] : -1;
                if (floatval($vrdesconto) > 0){
                    if (floatval($vrdesconto) >= 100){
                        // Caso o desconto seja acima de 100%, fixar ele em 1 centavo à menos do preço do item.
                        $valorFinal = $precoAux - 0.01;
                    }
                    else {
                        // Calcula o desconto.
                        $discount = floatval($precoAux) * (floatval($vrdesconto) / 100);
                        $valorFinal = $this->util->truncate($discount, 2);
                    }
                }
            }
            else {
                // DESCONTO POR VALOR.
                $vrdesconto = isset($discountDetails["VRDESCONTO"]) ? $discountDetails["VRDESCONTO"] : -1;
                if (floatval($vrdesconto) > 0){
                    if (floatval($vrdesconto) >= $precoAux){
                        $valorFinal = $precoAux - 0.01;
                    }
                    else {
                        $valorFinal = $vrdesconto;
                    }
                }
            }
        }

        if ($discountDetails['IDDESCACRPROMO'] === "D") $valorFinal *= -1;

        return $valorFinal;
	}

	public function impressaoPedido($multiplasComandas){
		$session = $this->util->getSessionVars(null);
		$result = array(
			'error' => false,
			'message' => ''
		);

		if ($session['IDIMPPEDPROD'] === 'S') {
			// reinicia timeout para impressão
			set_time_limit(60);
			$messages = array();
			$multiplasComandasArr = array();
			foreach ($this->impProdParams as $params) {
				$ultimaComanda = $params['DSCOMANDA'] == $this->impProdParams[count($this->impProdParams)-1]['DSCOMANDA'];
				$resultImpressao = $this->impressaoAPI->imprimePedido($params['CDFILIAL'], $params['CDLOJA'],
											$params['PRODUTOS'], $params['CDVENDEDOR'], $params['DSCOMANDA'], $params['NRMESA'],
						    				$params['NRPEDIDO'], $params['MODO'], '', $multiplasComandas, $ultimaComanda, '', $params["PRODUTOS"][0]['CDCAIXA']);
				if ($resultImpressao['error']) {
					$result['error'] = true;
					if (!in_array($resultImpressao['message'], $messages)) {
						array_push($messages, $resultImpressao['message']);
						$result['message'] .= $resultImpressao['message'] . ' ';
					}
				}
			}
			if(isset($resultImpressao['paramsImpressora'])){
                $result['paramsImpressora'] = $resultImpressao['paramsImpressora'];
			}
		}
		return $result;
	}

	public function insertProdutosDesistencia($produtosDesistencia, $connection){
		$session = $this->util->getSessionVars(null);
		$chave = $session['CDFILIAL'].$session['CDCAIXA'].$produtosDesistencia[0]['NRVENDA'];
		foreach ($produtosDesistencia as $produtoDesistencia) {
			$this->util->newCode($chave, '3');
			$NRSEQITCOMVENDES = $this->util->getNewCode($chave, '3');
			$params = array(
				'CDFILIAL'			=> $session['CDFILIAL'],
				'CDCAIXA'			=> $session['CDCAIXA'],
				'NRORG'				=> $session['NRORG'],
				'NRVENDAREST'       => $produtoDesistencia['NRVENDA'],
				'NRSEQITCOMVENDES'  => $NRSEQITCOMVENDES,
				'QTPRODITCOMVENDES' => $produtoDesistencia['QTPRODITCOMVENDES'],
				'CDPRODUTO'         => $produtoDesistencia['CDPRODUTO'],
				'VRPRECCOMVEN'      => $produtoDesistencia['VRPRECCOMVEN'],
				'VRDESCCOMVEN'      => $produtoDesistencia['VRDESCCOMVEN'],
				'VRACRCOMVEN'       => $produtoDesistencia['VRACRCOMVEN']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_DESISTE_ITEM", $params);
			// Trata dos acréscimos que contém preço.
			if (!empty($produtoDesistencia['CDOCORR'])) {
				foreach ($produtoDesistencia['CDOCORR'] as $CDOCORR) {
					$params = array(
						$session['CDFILIAL'],
						$session['CDLOJA'],
						$CDOCORR
					);
					$obs_type = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_OBS_TYPE", $params);
					if ($obs_type['IDCONTROLAOBS'] === 'A' && !empty($obs_type['CDPRODUTO'])) {
						// Busca o preço do acréscimo.
						$r_retornaPreco = $this->precoAPI->buscaPreco($session['CDFILIAL'], null, $obs_type['CDPRODUTO'], $session['CDLOJA'], null);
						$preco     = floatval($r_retornaPreco['PRECO']);
						$desconto  = floatval($r_retornaPreco['DESC']);
						$acrescimo = floatval($r_retornaPreco['ACRE']);

						// Valida o preço.
						if (!empty($preco) && $preco > 0) {
							$this->util->newCode($chave, '3');
							$NRSEQITCOMVENDES = $this->util->getNewCode($chave, '3');
							$params = array(
								'CDFILIAL'			=> $session['CDFILIAL'],
								'CDCAIXA'			=> $session['CDCAIXA'],
								'NRORG'				=> $session['NRORG'],
								'NRVENDAREST'       => $produtoDesistencia['NRVENDA'],
								'NRSEQITCOMVENDES'  => $NRSEQITCOMVENDES,
								'QTPRODITCOMVENDES' => $produtoDesistencia['QTPRODITCOMVENDES'],
								'CDPRODUTO'         => $obs_type['CDPRODUTO'],
								'VRPRECCOMVEN'      => $preco,
								'VRDESCCOMVEN'      => $desconto,
								'VRACRCOMVEN'       => $acrescimo
							);
							$this->entityManager->getConnection()->executeQuery("SQL_DESISTE_ITEM", $params);
						}
					}
				}
			}
		}
	}

	public function geraSenhaProducao($cdloja, $cdfilial, $cdcaixa) {
		$params = array(
			'CDCAIXA' => $cdcaixa,
			'CDFILIAL' => $cdfilial,
			'CDLOJA' => $cdloja
		);
		$IDSENHACUP = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_IDSENHACUP", $params);
		$IDSENHACUP = $IDSENHACUP['IDSENHACUP'];
		if ($IDSENHACUP === 'S') {
			$today = date("d/m/Y");
			$this->util->newCode('VENDADIA'.$cdfilial.$today);
			return $nrPedido = $this->util->getNewCode('VENDADIA'.$cdfilial.$today,5);
		} else if ($IDSENHACUP === 'A' || $IDSENHACUP === 'L') {
			$CDSENHAPED = $this->entityManager->getConnection()->fetchAll("SQL_GET_CDSENHAPED", $params);
			$CDSENHAPED = array_map("intval", array_column($CDSENHAPED, 'CDSENHAPED'));
			$max = $IDSENHACUP === 'A' ? 99999 : 999;
			$nrPedido = $this->geraSenhaPedido($max, $CDSENHAPED);
			return $nrPedido;
		}
	}

	private function geraSenhaPedido($max, $CDSENHAPED) {
		$randomNrpedido = mt_rand(1, $max);
		if (empty($CDSENHAPED)) return $randomNrpedido;
		if (!in_array($randomNrpedido, $CDSENHAPED)) {
			return $randomNrpedido;
		} else {
			$nrPedido = $this->geraSenhaPedido($max, $CDSENHAPED);
		}
		return $nrPedido;
	}

}