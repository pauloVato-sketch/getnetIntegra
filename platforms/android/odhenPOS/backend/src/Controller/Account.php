<?php

namespace Controller;

use Zeedhi\DTO\Response\Notification;
use Zeedhi\Framework\DTO\Response\Message;
use Zeedhi\Framework\DTO\Response;
use Zeedhi\Framework\DTO\Request;
use \Util\Exception;

class Account extends \Zeedhi\Framework\Controller\Simple {

	protected $waiterMessage;
	protected $util;
	protected $pedidoService;
	protected $orderService;
	protected $produtoService;
	protected $produtoOriginalService;
	protected $paramsService;
	protected $accountService;
	protected $tableService;
	protected $entityManager;

	public function __construct(
		\Util\WaiterMessage $waiterMessage,
		\Util\Util $util,
		\Service\Pedido $pedidoService,
		\Service\Order $orderService,
		\Service\Produto $produtoService,
		\Service\ProdutoOriginal $produtoOriginalService,
		\Service\Params $paramsService,
		\Service\Account $accountService,
		\Service\Table $tableService,
		\Doctrine\ORM\EntityManager $entityManager
	){
		$this->waiterMessage          = $waiterMessage;
		$this->util                   = $util;
		$this->pedidoService          = $pedidoService;
		$this->orderService           = $orderService;
		$this->produtoService         = $produtoService;
		$this->produtoOriginalService = $produtoOriginalService;
		$this->paramsService          = $paramsService;
		$this->accountService         = $accountService;
		$this->tableService           = $tableService;
		$this->entityManager          = $entityManager;
	}

	public function order(Request\Filter $request, Response $response){
		try {
			$connection  = null;
			$params = $request->getFilterCriteria()->getConditions();

			$chave             = $params[0]['value'];
			$mode              = $params[1]['value'];
			$multiplasComandas = $params[2]['value'];
			//$NRVENDAREST       = $params[3]['value'];
			$produtos          = $params[4]['value'];
			$orderCode         = $params[5]['value'];
			$vendedorAut       = $params[6]['value'];
			$saleProdPass	   = !empty($params[7]['value']) ? $params[7]['value'] : null;

			$produtos          = json_decode($produtos, true);
			$horaAtual         = new \DateTime();
			// Open connection and begin transaction.
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();
			$comandas = array_values(array_unique(array_column($produtos, 'NRCOMANDA')));
			foreach ($comandas as $comanda) {
				$pedido = array();
				foreach ($produtos as $produto){
					if($produto['NRCOMANDA'] == $comanda){
						/* Converts the products of a Smart Promo into an array. */
						if (!empty($produto['PRODUTOS'])){
							foreach ($produto['PRODUTOS'] as &$subProd){
								$subProd = (array)$subProd;
							}
						}
						$NRVENDAREST = $produto['NRVENDAREST'];
						array_push($pedido, array(
								'codigo'       => $produto['CDPRODUTO'],
								'desc'         => $produto['DSBUTTON'],
								'quantidade'   => $produto['QTPRODCOMVEN'],
								'posicao'      => $produto['NRLUGARMESA'],
								'observacao'   => $produto['TXPRODCOMVEN'],
								'CUSTOMOBS'    => $produto['CUSTOMOBS'],
								'ocorrencias'  => $produto['CDOCORR'],
								'preco'        => $produto['VRPRECCOMVEN'],
								'IDIMPPRODUTO' => $produto['IDIMPPRODUTO'],
								'IDTIPCOBRA'   => $produto['IDTIPCOBRA'],
								'produtos'     => $produto['PRODUTOS'],
								'ORDEMIMP'     => $produto['ID'],
								'UNIQUEID'     => $produto['UNIQUEID'],
								'ATRASOPROD'   => $produto['ATRASOPROD'],
                                'TOGO'         => $produto['TOGO'],
								'IMPRESSORA'   => $produto['PRINTER'],
								'REFIL'        => $produto['REFIL'],
								'NRCOMANDA'    => $produto['NRCOMANDA'],
								'NRVENDAREST'  => $produto['NRVENDAREST'],
                                'VOUCHER'      => $produto['VOUCHER'],
                                'CDCAMPCOMPGANHE' => $produto['CAMPANHA'],
                                'DTINIVGCAMPCG'   => $produto['DTINIVGCAMPCG'],
                                'DESCCOMPGANHE'   => $produto['DESCCOMPGANHE'],
								'DTHRINCOMVEN' => $horaAtual
							)
						);
					}
				};

				$dataset = array(
					'chave'       => $chave,
					'mode'        => $mode,
					'NRCOMANDA'   => $comanda,
					'NRVENDAREST' => $NRVENDAREST,
					'pedido'      => $pedido,
					'orderCode'   => $orderCode,
					'ultimaComanda' => $comanda == $comandas[count($comandas)-1],
					'vendedorAut'   => $vendedorAut,
					'saleProdPass'  => $saleProdPass
				);
				$answer = $this->pedidoService->fazPedido($dataset);
				if (!$answer['funcao'] == '1') {
					if (!empty($answer['message'])) {
	                    $message = $answer['message'];
	                } else {
	                	switch ($answer['error']) {
                			case '444':
                				$message = "Produto ".$answer['aux']['desc']." está bloqueado!";
            					break;
            				case '443':
                				$message = "Produto ".$answer['aux']['desc']." está sem preço!";
            					break;
            				default:
                				$message = $this->waiterMessage->getMessage($answer['error']);
                				break;
	                	}
	                	if ($answer['error'] == '264'){
	                		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing','paramsImpressora'=>false))));
	                	}
	                }
	                throw new Exception($message, 1);
				}
			}
			$connection->commit();
			$connection  = null;
			// realiza impressão dos pedidos
			$resultImpressao = $this->pedidoService->impressaoPedido($multiplasComandas);
			if ($resultImpressao['error']) {
				$messageString = 'O pedido foi realizado porém não foi possível imprimir o pedido, verifique a conexão com a impressora.';
				$messageString .= '<br><br>' . $resultImpressao['message'];
				$response->addMessage(new Message($messageString));
			}
			$paramsImpressora = isset($resultImpressao['paramsImpressora']) ? $resultImpressao['paramsImpressora'] : false;
			$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('410'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing','paramsImpressora'=>$paramsImpressora))));

		} catch (\Exception $e) {
			Exception::logException($e);
			if ($connection != null) $connection->rollback();
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function cancelProduct(Request\Filter $request, Response $response){
		try {
			$params       = $request->getFilterCriteria()->getConditions();
			$chave        = $params[0]['value'];
			$modo         = $params[1]['value'];
			$NRCOMANDA    = $params[2]['value'];
			$NRVENDAREST  = $params[3]['value'];
			$produto      = $params[4]['value'];
			$motivo       = $params[5]['value'];
			$CDSUPERVISOR = $params[6]['value'];
			$IDPRODPRODUZ = $params[7]['value'];

			$DTHRPRODCANVEN = new \DateTime();

			$produto = json_decode($produto);
			$produto = array(
				'NRVENDAREST'    => $produto[0],
				'nrcomanda'      => $produto[1],
				'NRPRODCOMVEN'   => $produto[2],
				'CDPRODPROMOCAO' => $produto[3],
				'NRSEQPRODCOM'   => $produto[4],
				'NRSEQPRODCUP'   => $produto[5],
				'codigo'         => $produto[6],
				'quantidade'     => $produto[7],
				'composicao'     => $produto[8]
			);

			$dataset = array(
				'chave'        	 => $chave,
				'mode'         	 => $modo,
				'NRCOMANDA'    	 => $NRCOMANDA,
				'NRVENDAREST'  	 => $NRVENDAREST,
				'produto'      	 => $produto,
				'motivo'       	 => $motivo[0],
				'CDSUPERVISOR' 	 => $CDSUPERVISOR,
				'IDPRODPRODUZ' 	 => $IDPRODPRODUZ,
				'DTHRPRODCANVEN' => $DTHRPRODCANVEN
			);
			$answer = $this->orderService->cancelaProduto($dataset);
			if ($answer['funcao'] == '1'){
				if (!empty($answer['message'])) {
					$response->addMessage(new Message($answer['message'], 'error'));
				}
				if(isset($answer['paramsImpressora'])){
					$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('paramsImpressora', $answer['paramsImpressora']));
				}
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));

			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}
		}
		catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getAccountItems(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];
			$modo = $params[1]['value'];
			$nrcomanda = $params[2]['value'];
			$nrvendarest = $params[3]['value'];
			$posicao = $params[4]['value'];

			$dataset = array(
				'chave'       => $chave,
				'mode'        => $modo,
				'NRCOMANDA'   => $nrcomanda,
				'NRVENDAREST' => $nrvendarest,
				'agrupamento' => array(),
				'posicao'     => $posicao
			);

			$itens = $this->accountService->handleAccountItems($dataset);
			if ($itens['funcao'] == '1') {
				unset($itens['funcao']);
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetAccountItems', $itens));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage($itens['error'])));
			}
		}
		catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getAccountItemsWithoutCombo(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];
			$modo = $params[1]['value'];
			$nrcomanda = $params[2]['value'];
			$nrvendarest = $params[3]['value'];
			$posicao = $params[4]['value'];

			$dataset = array(
				'chave'       => $chave,
				'mode'        => $modo,
				'NRCOMANDA'   => $nrcomanda,
				'NRVENDAREST' => $nrvendarest,
				'agrupamento' => array(),
				'posicao'     => $posicao
			);

			$itens = self::handleAccountItemsWithoutCombo($dataset);

			if ($itens['funcao'] == '1') {
				unset($itens['funcao']);
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetAccountItemsWithoutCombo', $itens));
			} else {
				if ($itens['error'] != '006') {
					$response->addMessage(new Message($this->waiterMessage->getMessage($itens['error'])));
				}
			}
		}
		catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function getAccountOriginalItems(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$chave = $params[0]['value'];
			$modo = $params[1]['value'];
			$nrcomanda = $params[2]['value'];
			$nrvendarest = $params[3]['value'];
			$posicao = $params[4]['value'];

			$dataset = array(
				'chave'       => $chave,
				'mode'        => $modo,
				'NRCOMANDA'   => $nrcomanda,
				'NRVENDAREST' => $nrvendarest,
				'agrupamento' => array(),
				'posicao'     => $posicao
			);

			$itens = self::handleAccountOriginalItems($dataset);
			if ($itens['funcao'] == '1') {
				unset($itens['funcao']);
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetOriginalAccountItems', $itens));
			} else {
				if(!is_array($itens['error']))
					// Retora itens vazios para verificação no front
					$response->addMessage(new Message($this->waiterMessage->getMessage($itens['error'])));
				else
					$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetOriginalAccountItems', $itens['error']));

			}
		}
		catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	private function handleAccountItemsWithoutCombo($dataset){
		$answer = $this->produtoService->action($dataset);

		$itens = array();
		if ($answer['funcao'] == '1') {
			unset($answer['funcao']);

			foreach ($answer as $item) {

				$item['POS'] = intval($item['posicao']); //Required in order to group items by position.
				$item['posicao'] = 'posição ' . (string)intval($item['posicao']);
				$item['preco'] = '' . $item['preco'];

				array_push($itens, $item);
			};

			$itens['funcao'] = '1';
		} else {
			$itens['funcao'] = '0';
			$itens['error'] = $answer['error'];
		}

		return $itens;
	}

	private function handleAccountOriginalItems($dataset){
		$answer = $this->produtoOriginalService->action($dataset);

		$itens = array();
		if ($answer['funcao'] == '1') {
			unset($answer['funcao']);

			foreach ($answer as $item) {

				$item['POS'] = intval($item['posicao']); //Required in order to group items by position.
				$item['posicao'] = 'posição ' . (string)intval($item['posicao']);
				$item['preco'] = '' . $item['preco'];

				array_push($itens, $item);
			};

			$itens['funcao'] = '1';
		} else {
			$itens['funcao'] = '0';
			$itens['error'] = $answer['error'];
		}

		return $itens;
	}

	public function getAccountDetails(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]['value'];
			$modo        = $params[1]['value'];
			$nrcomanda   = $params[2]['value'];
			$nrvendarest = $params[3]['value'];
			$funcao      = $params[4]['value'];

			if (is_array($params[5]['value'])) {
				$posicao = array_map(function($position){
					return str_pad((string)$position, 2, '0', STR_PAD_LEFT);
				}, $params[5]['value']);
			} else {
				if ($params[5]['value'] != '') {
					$posicao = str_pad((string)$params[5]['value'], 2, '0', STR_PAD_LEFT);
				} else {
					$posicao = $params[5]['value'];
				}
			}

            $updateDiscount = $params[6]['value']; // Indica se irá atualizar os descontos do crédito fidelidade na ITCOMANDAVEN.

			$dataset = array(
				'chave'       => $chave,
				'mode'        => $modo,
				'NRCOMANDA'   => $nrcomanda,
				'NRVENDAREST' => $nrvendarest,
				'funcao'      => $funcao,
				'agrupamento' => array(),
				'posicao'     => $posicao,
                'updateDiscount' => $updateDiscount
			);
			$answer = $this->accountService->dadosParcial($dataset);
			$details = array();
			$itens = array();
			$dadosImpressao = array();

			if ($answer['funcao'] == '1'){
				array_push($details, array(
					'NRPESMESAVEN'     => $answer['pessoas'],
					'permanencia'      => substr_replace($answer['permanencia'], ':', 2, 0),
					'produtos'         => '' . $answer['produtos'],
					'desconto'         => '' . $answer['lblDesconto'],
					'servico'          => '' . $answer['servico'],
					'couvert'          => '' . $answer['couvert'],
					'consumacao'       => '' . $answer['consumacao'],
					'total'            => '' . $answer['total'],
					'valorPago'        => '' . $answer['valorPago'],
					'vlrprodutos'      => $this->util->strToFloat($answer['produtos']),
					'vlrprodcobtaxa'   => $answer['vlrprodcobtaxa'],
					'vlrdesconto'      => $this->util->strToFloat($answer['desconto']),
					'vlrservico'       => $this->util->strToFloat($answer['servico']),
					'vlrservoriginal'  => floatval($answer['taxaOriginal']),
					'vlrcouvert'       => $this->util->strToFloat($answer['couvert']),
					'vlrconsumacao'    => $this->util->strToFloat($answer['consumacao']),
					'vlrtotal'         => $this->util->strToFloat($answer['total']),
					'vlrpago'          => $this->util->strToFloat($answer['valorPago']),
                    'totalSubsidy'     => $answer['totalSubsidy'],
                    'realSubsidy'      => $answer['realSubsidy'],
					'numeroProdutos'   => $answer['numeroProdutos'],
					'posicao'          => $answer['posicao'],
					'fidelityDiscount' => $answer['fidelityDiscount'],
					'fidelityValue'    => $answer['fidelityValue'],
					'NMVENDEDORABERT'  => $answer['NMVENDEDORABERT']
				));

				// Validação para impressão Front que vem como String, impressão Saas e Back vem como Array.
				if(!empty($answer['dadosImpressao'])){
					if (!is_array($answer['dadosImpressao']) || !array_key_exists('impressaoBack', $answer['dadosImpressao'])){
						$dadosImpressao['dadosImpressao'] = $answer['dadosImpressao'];
					}
				}

				$itens = $this->accountService->handleAccountItems($dataset);
				if ($itens['funcao'] == '1') {
					unset($itens['funcao']);
					if ($funcao == 'I') {
						if (!is_array($answer['dadosImpressao']) || !array_key_exists('impressaoBack', $answer['dadosImpressao']) || !$answer['dadosImpressao']['impressaoBack']['error']) {
							//Parcial impressa com sucesso.
							$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('417'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
						} else {
							//Erro na impressão da parcial de conta.
							$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification('Não foi possível imprimir a parcial de conta. ' . $answer['dadosImpressao']['impressaoBack']['message'], \Zeedhi\Framework\DTO\Response\Notification::TYPE_ERROR));
						}
					}
				} else {
					if ($funcao == "I") {
						if ($modo == 'C') {
							$return = '430';
						}
						else {
							if (empty($posicao)) $return = '006';
							else $return = '425'; //Não foi realizado nenhum pedido para esta posição.
						}
						$response->addMessage(new Message($this->waiterMessage->getMessage($return)));
					}
					$itens = array(array()); // retorno de dataSet vazio para o front quando não há pedidos
				}
			} else {
				$details = array(array());
				$itens = array(array());
				$response->addMessage(new Message($this->waiterMessage->getMessage($answer['error'])));
			}

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetAccountDetails', $details));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountGetAccountItems', $itens));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('dadosImpressao', $dadosImpressao));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => 'nothing'))));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array(array('nothing' => $e->getMessage()))));
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('dadosImpressao', array('')));
			$response->addMessage(new Message($e->getMessage(), 'error'));
		}
	}

	public function beginPaymentAccount(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave        = $params[0]['value'];
			$CDVENDEDOR   = $params[1]['value'];
			$NRVENDAREST  = $params[2]['value'];
			$NRCOMANDA    = $params[3]['value'];
			$NRMESA       = $params[4]['value'];
			$NRLUGARMESA  = $params[5]['value'];
			$CDTIPORECE   = $params[6]['value'];
			$IDTIPMOV     = $params[7]['value'];
			$VRMOV        = $params[8]['value'];
			$DSBANDEIRA   = $params[9]['value'];
			$IDTPTEF      = $params[10]['value'];

			$dataset = array(
				'chave'       => $chave,
				'CDVENDEDOR'  => $CDVENDEDOR,
				'NRVENDAREST' => $NRVENDAREST,
				'NRCOMANDA'   => $NRCOMANDA,
				'NRMESA'      => $NRMESA,
				'NRLUGARMESA' => $NRLUGARMESA,
				'CDTIPORECE'  => $CDTIPORECE,
				'IDTIPMOV'    => $IDTIPMOV,
				'VRMOV'       => $VRMOV,
				'DSBANDEIRA'  => $DSBANDEIRA,
				'IDTPTEF'     => $IDTPTEF
			);
			$answer = $this->accountService->inicializaPagamento($dataset);

			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountPaymentBegin', $answer));
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function finishPaymentAccount(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();

            $NRSEQMOVMOB  = $params[0]['value'];
            $NRSEQMOB     = $params[1]['value'];
            $DSBANDEIRA   = $params[2]['value'];
            $NRADMCODE    = $params[3]['value'];
            $IDADMTASK    = $params[4]['value'];
            $IDSTMOV      = $params[5]['value'];
            $TXMOVUSUARIO = $params[6]['value'];
            $TXMOVJSON    = $params[7]['value'];
            $CDNSUTEFMOB  = $params[8]['value'];
            $TXPRIMVIATEF = $params[9]['value'];
            $TXSEGVIATEF  = $params[10]['value'];
            $NRVENDAREST  = $params[11]['value'];
            $NRCOMANDA    = $params[12]['value'];
            $chave        = $params[13]['value'];

            $dataset = array(
                'NRSEQMOVMOB'   =>  $NRSEQMOVMOB,
                'NRSEQMOB'      =>  $NRSEQMOB,
                'DSBANDEIRA'    =>  $DSBANDEIRA,
                'NRADMCODE'     =>  $NRADMCODE,
                'IDADMTASK'     =>  $IDADMTASK,
                'IDSTMOV'       =>  $IDSTMOV,
                'TXMOVUSUARIO'  =>  $TXMOVUSUARIO,
                'TXMOVJSON'     =>  $TXMOVJSON,
                'CDNSUTEFMOB'   =>  $CDNSUTEFMOB,
                'TXPRIMVIATEF'  =>  $TXPRIMVIATEF,
                'TXSEGVIATEF'   =>  $TXSEGVIATEF
            );
            $paymentsDone = $this->finalizaPagamentoService->action($dataset);

            $result = array(
            	'payments' => $paymentsDone,
            	'tableClosed' => false
        	);
            if (isset($chave) && isset($NRVENDAREST) && isset($NRCOMANDA)) {
	            if ($this->accountIsFullyPaid($chave, $NRVENDAREST, $NRCOMANDA)) {
	            	$closeTableResponse = $this->closeTable($chave, $NRCOMANDA, $NRVENDAREST);
	            	if ($closeTableResponse['error'] == false) {
	            		$result['tableClosed'] = true;
	            		$session = $this->util->getSessionVars($chave);
		            	if ($session['IDMODULO'] == 'M') {
					        $response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('403'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
					    } else if ($session['IDMODULO'] == 'C') {
					        $response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($this->waiterMessage->getMessage('431'), \Zeedhi\Framework\DTO\Response\Notification::TYPE_SUCCESS));
					    }
	            	} else {
	            		$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($closeTableResponse['message'], \Zeedhi\Framework\DTO\Response\Notification::TYPE_ERROR));
	            	}
	            }
            }
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountPaymentFinish', $result));
        } catch (\Exception $e) {
        	Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

	private function formatStringNumberToFloat($valor) {
		$valor = str_replace(' ', '', $valor);
		$valor = str_replace(',', '.', $valor);
		return floatval($valor);
	}

    private function accountIsFullyPaid($chave, $NRVENDAREST, $NRCOMANDA) {
    	$session = $this->util->getSessionVars($chave);
		$dataset = array(
            'chave'       => $chave,
            'mode'        => $session['IDMODULO'],
            'NRVENDAREST' => $NRVENDAREST,
            'NRCOMANDA'   => $NRCOMANDA,
            'funcao'      => 'C',
            'agrupamento' => array(),
            'posicao'     => ''
        );
        $parcialResponse = $this->parcialService->action($dataset);
        $total = $this->formatStringNumberToFloat($parcialResponse['total']);
        return $total == 0;
    }

    private function closeTable($chave, $NRCOMANDA, $NRVENDAREST) {
    	$session = $this->util->getSessionVars($chave);
    	$dataset = array(
		    'chave'           => $chave,
		    'NRCOMANDA'       => $NRCOMANDA,
		    'NRVENDAREST'     => $NRVENDAREST,
		    'consumacao'      => true,
		    'servico'         => true,
		    'couvert'         => true,
		    'valorConsumacao' => 0,
		    'pessoas'         => 1,
		    'modo'            => $session['IDMODULO'],
		    'IMPRESSAOPARCIAL' => 'N'
		);

		$answer = $this->fechaContaMesaService->action($dataset);
		if ($answer['funcao'] == '1') {
		    return array(
		    	'error' => false
	    	);
		} else {
		    return array(
		    	'error' => true,
		    	'message' => $this->waiterMessage->getMessage($answer['error'])
	    	);
		}
    }

	public function typedCreditPayment(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$online = $this->paramsService->getParamsVendaOnline();

			$amount             = $params[0]['value'];
			$idAutorizadora     = $params[1]['value'];
			$dtVencimento       = $params[2]['value'];
			$numCartao          = $params[3]['value'];
			$codSeguranca       = $params[4]['value'];
			$NRSEQMOVMOB        = $params[5]['value'];
			$idLojaSitef        = $online[0]['IDLOJAESITEF'];
			$codigoLojaSitef    = $online[0]['CDLOJAESITEF'];

			$idAutorizadora = ltrim($idAutorizadora, '0');

			//Tratamento do Valor no Formato esperado
			$amount = number_format($amount,2,".","");

			$dataset = array(
				"valorPedido"           => $amount,
				"codigoBandeira"        => $idAutorizadora,
				"validadeCartao"        => $dtVencimento,
				"numeroCartao"          => $numCartao,
				"codigoSegurancaCartao" => $codSeguranca,
				"idLojaSitef"           => $idLojaSitef,
				"codigoLojaSitef"       => $codigoLojaSitef,
				"codigoPedido"          => $NRSEQMOVMOB
			);

			if (class_exists("\Odhen\Controller\PaymentService")){


				$endpoint = "https://esitef-homologacao.softwareexpress.com.br/e-sitef-hml/Payment2?wsdl";
				$proxy_host = "192.168.122.3";
				$proxy_port = "8080";
				$proxy_user = "teknisa";
				$proxy_pass = "teknisa";
				$paymentService = new \Odhen\Controller\PaymentService();
				$paymentService->configEndpoint($endpoint);
				$paymentService->configProxy($proxy_host, $proxy_port, $proxy_user, $proxy_pass);
				$paymentResponse = $paymentService->beginCreditCardPayment($dataset);

				$paymentStatus = 0;
				$tipoPagamento = "";
				$cardUsed = "";
				$numeroTransacao = "";
				$dataSitef = "";
				$nsu = "";
				$message = $paymentResponse['message'];
				$cupom =  "";

				if (is_null($paymentResponse['error'])) {
					$paymentStatus = 1;
					$tipoPagamento = $paymentResponse['tipoPagamento'];
					$cardUsed = $paymentResponse['cardUsed'];
					$numeroTransacao = $paymentResponse['numeroTransacao'];
					$dataSitef = $paymentResponse['dataSitef'];
					$nsu = $paymentResponse['nsu'];
					$cupomCliente = str_replace(array("\r\n", "\r", "\n"), "", $paymentResponse['merchantReceipt']);
					$cupomEstabelecimento = str_replace(array("\r\n", "\r", "\n"), "", $paymentResponse['customerReceipt']);
				}

				$resposta[] = '[
					{
						"tef_request_type": 4,
						"tef_request_details": {
							"payment_transaction_status": ' . $paymentStatus . ',
							"acquirer_affiliation_key": "",
							"acquirer_name": "' . $tipoPagamento . '",
							"card_brand_name": "' . $cardUsed . '",
							"acquirer_authorization_code": "SITEF",
							"payment_product": 1,
							"payment_installments": 0,
							"payment_amount": "' . $amount . '",
							"available_balance": null,
							"unique_sequential_number": "' . $nsu .'",
							"acquirer_unique_sequential_number": null,
							"acquirer_authorization_datetime": "' . $dataSitef . '",
							"administrative_code": "' . $nsu . '",
							"administrative_task": 0,
							"user_message": "' . $message . '",
							"merchant_receipt": "' . $cupomEstabelecimento . '",
							"customer_receipt": "' . $cupomCliente . '",
							"reduced_receipt": ""
						}
					}
				 ]';

				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountPaymentTypedCredit', $resposta));
			} else {
				$response->addMessage(new Message($this->waiterMessage->getMessage('453')));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function checkRefil(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave       = $params[0]['value'];
			$NRVENDAREST = $params[1]['value'];
			$NRCOMANDA   = $params[2]['value'];
			$CDPRODUTO   = $params[3]['value'];
			$NRLUGARMESA = $params[4]['value'];

			$dataset = array(
				'chave'     => $chave,
				'NRVENDAREST' => $NRVENDAREST,
				'NRCOMANDA' => $NRCOMANDA,
				'CDPRODUTO' => $CDPRODUTO,
				'NRLUGARMESA' => $NRLUGARMESA
			);

			$result = $this->produtoService->checkRefil($dataset);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CheckRefilRepository', $result));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function changeClientConsumer(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$chave          = $params[0]['value'];
			$NRVENDAREST    = $params[1]['value'];
			$NRCOMANDA      = $params[2]['value'];
			$positions      = $params[3]['value'];
			$CDCLIENTE      = $params[4]['value'];
			$CDCONSUMIDOR   = $params[5]['value'];
			$fidelitySearch = $params[6]['value'];

			$session = $this->util->getSessionVars($chave);

			$result = $this->accountService->changeClientConsumer($chave, $NRVENDAREST, $NRCOMANDA, $positions, $CDCLIENTE, $CDCONSUMIDOR);

			if ($result['status']) {
				// get NRMESA from query
				$dataset = array(
					'chave' => $chave,
					'mesa'  => $result['data']['NRMESA'],
					'tipo'  => 'O'
				);
				$answerTable = $this->tableService->valAbertura($dataset);
				if (!$answerTable['error']) {
					// recebe posições da mesa
					$posicoes = $this->tableService->getPosition($session, $answerTable['NRVENDAREST'], array());
					$resultObject = array(array(
						'IDSTMESAAUX'  => $answerTable['retorno'],
						'NRPESMESAVEN' => $answerTable['NRPESMESAVEN'],
						'NRPOSICAOMESA' => isset($answerTable['NRPOSICAOMESA']) ? $answerTable['NRPOSICAOMESA'] : $answerTable['NRPESMESAVEN'],
						'NRMESA'        => $result['data']['NRMESA'],
						'STATUS'        => $answerTable['retorno'],
						'CDSALA'        => $answerTable['CDSALA'],
						'NMMESA'        => $answerTable['NMMESA'],
						'NRVENDAREST'   => $answerTable['NRVENDAREST'],
						'NRCOMANDA'     => $answerTable['NRCOMANDA'],
						'NRJUNMESA'     => $answerTable['NRJUNMESA'],
						'posicoes'      => $posicoes,
						'CDCLIENTE'     => (isset($answerTable['CDCLIENTE'])? $answerTable['CDCLIENTE']: null),
						'NMRAZSOCCLIE'  => (isset($answerTable['NMRAZSOCCLIE'])? $answerTable['NMRAZSOCCLIE']: null),
						'CDCONSUMIDOR'  => (isset($answerTable['CDCONSUMIDOR'])? $answerTable['CDCONSUMIDOR']: null),
						'NMCONSUMIDOR'  => (isset($answerTable['NMCONSUMIDOR'])? $answerTable['NMCONSUMIDOR']: null),
						'CDVENDEDOR'    => (isset($answerTable['CDVENDEDOR'])? $answerTable['CDVENDEDOR']: null)
					));
					$response->addNotification(new \Zeedhi\Framework\DTO\Response\Notification($result['message']));
					$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TableActiveTable', $resultObject));
				} else {
					$response->addMessage(new Message($this->waiterMessage->getMessage($answerTable['error'])));
				}

				$fidelitySearch = array(
					'fidelitySearch' => array()
				);
				if (!empty($CDCLIENTE) && !empty($CDCONSUMIDOR) && $fidelitySearch && $session['IDEXTCONSONLINE'] == 'S'){
					$fidelitySearch['fidelitySearch'] = $this->accountService->fidelitySearch($session, $CDCLIENTE, $CDCONSUMIDOR);
				}
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('AccountChangeClientConsumer', $fidelitySearch));
			} else {
				$response->addMessage(new Message($result['message']));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function saleCancel(Request\Filter $request, Response $response){
		try {
			$params         = $request->getFilterCriteria()->getConditions();
			$chave          = $params[0]['value'];
			$CODIGOCUPOM    = $params[1]['value'];
			$CDSUPERVISOR 	= $params[2]['value'];

			$result = $this->accountService->saleCancel($CODIGOCUPOM, $chave, $CDSUPERVISOR);
			if (!$result['error']){
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('saleCancel', $result));
			} else{
				$response->addMessage(new Message($result['message']));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

	public function changeProductDiscount(Request\Filter $request, Response $response){
		try {
			$params = $this->util->getParams($request->getFilterCriteria()->getConditions());

			$result = $this->accountService->changeProductDiscount($params['NRVENDAREST'], $params['NRCOMANDA'],
				$params['VRDESCONTO'], $params['TIPODESCONTO'], $params['NRPRODCOMVEN'], $params['CDSUPERVISOR'],
				$params['motivoDesconto'], $params['CDGRPOCORDESC']);

			if (!$result['error']){
				$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array('nothing' => 'nothing')));
			} else{
				$response->addMessage(new Message($result['message']));
			}
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage()));
		}
	}

    public function updateCartPrices(Request\Filter $request, Response $response){
        try {
            $params     = $request->getFilterCriteria()->getConditions();
            $chave      = $params[0]['value'];
            $products   = $params[1]['value'];
            $cliente    = $params[2]['value'];
            $consumidor = $params[3]['value'];

            $session = $this->util->getSessionVars($chave);

            $priceData = $this->accountService->updateCartPrices($products, $session['CDFILIAL'], $session['CDLOJA'], $cliente, $consumidor);
            $priceData['IDTPVENDACONS'] = $this->accountService->getIDTPVENDACONS($cliente, $consumidor);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CartPricesRepository', $products));
            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', $priceData));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function consumerSearch(Request\Filter $request, Response $response){
    	set_time_limit(90);
        try {
            $params    = $request->getFilterCriteria()->getConditions();
            $chave     = $params[0]['value'];
            $CDCLIENTE = $params[1]['value'];
            $code      = $params[2]['value'];

            $session = $this->util->getSessionVars($chave);
            $consumer = $this->accountService->consumerSearch($session['CDFILIAL'], $CDCLIENTE, $code);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ConsumerSearchRepository', $consumer));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function verificaProdutosBloqueados(Request\Filter $request, Response $response){
        try {
            $params   = $request->getFilterCriteria()->getConditions();
            $produtos = $params[0]['value'];

            $session = $this->util->getSessionVars($produtos);
            $result = $this->accountService->verificaProdutosBloqueados($session, $produtos);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('VerificaProdutosBloqueados', $result));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function searchCard(Request\Filter $request, Response $response){
        try {
            $params      = $request->getFilterCriteria()->getConditions();
            $searchValue = $params[0]['value'];

            $result = $this->accountService->searchCard($searchValue);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ParamsCardsRepository', $result));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

	public function filterProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);
			if (isset($params[0])) {
				$filter = $params[0]['value'];
			} else {
				$filter = '%%';
			}
			$page = $request->getFilterCriteria()->getPage();
			$page = $page == 0 ? 1 : $page;
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

			$result = $this->accountService->filterProducts($session, $filter, $FIRST, $LAST);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message($e->getMessage()));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('FilterProducts', $result));
	}

    public function transferPosition(Request\Filter $request, Response $response){
        try {
            $params = $this->util->getParams($request->getFilterCriteria()->getConditions());
            $session = $this->util->getSessionVars(null);

            $valMesa = $this->tableService->dadosMesa($session['CDFILIAL'], $session['CDLOJA'], $params['NRCOMANDA'], $params['NRVENDAREST']);
            if ($valMesa['IDSTMESAAUX'] !== 'R'){
                throw new \Exception("O status da mesa foi alterado. Verifique se ela não foi aberta novamente, ou se o pagamento da mesma já foi realizado.");
            }

            $this->accountService->transferPosition($params['NRVENDAREST'], $params['NRCOMANDA'], $params['products'], $params['position'], $params['CDCLIENTE'], $params['CDCONSUMIDOR']);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('TransferPositionRepository', array()));
        } catch (\Exception $e) {
            Exception::logException($e);
            $result = array();
            $response->addMessage(new Message($e->getMessage()));
        }
    }

	public function selectVendedores(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);
			if (isset($params[0])) {
				$filter = $params[0]['value'];
			} else {
				$filter = '%%';
			}
			$page = $request->getFilterCriteria()->getPage();
			$page = $page == 0 ? 1 : $page;
            $pageSize = $request->getFilterCriteria()->getPageSize();
            $FIRST = $request->getFilterCriteria()->getPageSize() * ($page-1);
            $LAST = ($FIRST + $pageSize);
            if ($page > 1) $FIRST++;

			$result = $this->accountService->selectVendedores($session, $filter, $FIRST, $LAST);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message($e->getMessage(), 'error'));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SelectVendedores', $result));
	}

	public function validatePassword(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);

			if (isset($params[0])) {
				$password = $params[0]['value'];
			} else {
				throw new \Exception("Erro ao recuperar senha.");
			}

			$result = $this->accountService->validatePassword($session, $password);
		} catch (\Exception $e) {
			Exception::logException($e);
			$result = array();
			$response->addMessage(new Message($e->getMessage(), 'alert'));
		}

		$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('ValidatePassword', $result));
	}

	public function selectComandaProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);

			if (empty($params[0])) {
				throw new \Exception("Erro ao recuperar comanda.");
			}

			$result = $this->accountService->selectComandaProducts($session['CDFILIAL'], $params[0]['value']);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('SelectComandaProducts', $result));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage(), 'alert'));
		}
	}

	public function updateComandaProducts(Request\Filter $request, Response $response) {
		try {
			$params = $request->getFilterCriteria()->getConditions();
			$session = $this->util->getSessionVars(null);

			if (empty($params[0])) {
				throw new \Exception("Erro ao recuperar comanda.");
			}

			$comandaAtual = $params[0]['value'];
			$vendaRestComandaAtual = $params[1]['value'];
			$comandaDestino = $params[2]['value'];
			$vendaRestComandaDestino = $params[3]['value'];
			$CDPRODUTO = $params[4]['value'];
			$NRPRODCOMVEN = $params[5]['value'];
			$CDSUPERVISOR = $params[6]['value'];

			$this->accountService->updateComandaProducts($session, $comandaAtual, $vendaRestComandaAtual, $comandaDestino, $vendaRestComandaDestino, $CDPRODUTO, $NRPRODCOMVEN, $CDSUPERVISOR);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('UpdateComandaProducts', array()));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message($e->getMessage(), 'error'));
		}
	}

	public function setDiscountFidelity(Request\Filter $request, Response $response){
		try {
			$params = $request->getFilterCriteria()->getConditions();

			$NRVENDAREST = $params[0]['value'];
			$NRCOMANDA = $params[1]['value'];
			$positions = $params[2]['value'];
			$VRDESCFID = $params[3]['value'];

            if (empty($positions)) $positions = array();

			$this->accountService->setDiscountFidelity($NRVENDAREST, $NRCOMANDA, $positions, $VRDESCFID);
			$response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('nothing', array()));
		} catch (\Exception $e) {
			Exception::logException($e);
			$response->addMessage(new Message("Erro ao salvar Crédito Fidelidade.<br>Tente novamente.<br><br>" . $e->getMessage(), 'error'));
		}
	}

    public function getCampanha(Request\Filter $request, Response $response){
        try {
            $session = $this->util->getSessionVars(null);
            $params = $request->getFilterCriteria()->getConditions();

            $produtos = json_decode($params[0]['value'], true);

            $time = new \DateTime();
            $time = $time->format('Hi');
            $campanha = $this->pedidoService->campanhaPromocional(null, null, $produtos, $time);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('GetCampanhaRepo', $campanha));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage(), 'error'));
        }
    }

    public function calculaDescontoSubgrupo(Request\Filter $request, Response $response){
        try {
            $params   = $request->getFilterCriteria()->getConditions();
            $products = $params[0]['value'];

            $products = $this->accountService->calculaDescontoSubgrupo($products);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('CalculaDescontoSubgrupo', $products));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

    public function produtosDesistencia(Request\Filter $request, Response $response){
    	try {
			$params = $request->getFilterCriteria()->getConditions();

			// Open connection and begin transaction.
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$itensDesistencia = $params[0]['value'];

			$this->pedidoService->insertProdutosDesistencia($itensDesistencia, $connection);
			$connection->commit();

		} catch (\Exception $e) {
			Exception::logException($e);
			if ($connection != null) $connection->rollback();
			$response->addMessage(new Message($e->getMessage()));
		}
	}

    public function updateServiceTax(Request\Filter $request, Response $response){
        try {
            $params = $request->getFilterCriteria()->getConditions();
            $NRVENDAREST = $params[0]['value'];
            $NRCOMANDA = $params[1]['value'];
            $TOTALPRODS = $params[2]['value'];
            $VRACRESCIMO = $params[3]['value'];
            $TIPOGORJETA = $params[4]['value'];

            $this->accountService->updateServiceTax($NRVENDAREST, $NRCOMANDA, $TOTALPRODS, $VRACRESCIMO, $TIPOGORJETA);

            $response->addDataSet(new \Zeedhi\Framework\DataSource\DataSet('UpdateServiceTax', array()));
        } catch (\Exception $e) {
            Exception::logException($e);
            $response->addMessage(new Message($e->getMessage()));
        }
    }

}