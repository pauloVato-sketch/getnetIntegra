<?php

namespace Odhen\API\Service;

class Comanda {

	protected $entityManager;
	protected $util;
	protected $consumidor;
	protected $preco;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Odhen\API\Util\Util $util,
		\Odhen\API\Service\Consumidor $consumidor,
		\Odhen\API\Service\Preco $preco)  {

		$this->entityManager 		= $entityManager;
		$this->util          		= $util;
		$this->consumidor    		= $consumidor;
		$this->preco    	 		= $preco;
	}

	const MOVIMENTACAO_ENTRADA = 'E';
    const MOVIMENTACAO_SAIDA   = 'S';

	const status = array(
        '1' => 'Em Aberto',
        '2' => 'Conta Solicitada',
        '3' => 'Pago',
        '4' => 'Cancelado',
        '5' => 'Transferido/Despachado',
        '6' => 'Não Pago',
        'P' => 'Impresso',
        'O' => 'Concluido'
    );

    const TIPO_PAGAMENTO_DINHEIRO = '001';

	/**
	 * @param  [array] produtos array com as informações dos produtos
	 * @param  [array] pagamento array com os dados do pagamento
	 * @param  [array] dadosConsumidor array com os dados do consumidor
	 * @param  [array] dadosFilial array com os dados da filial
	 * @param  [array] dadosPedido array com os dados do pedido como, isTogo, NRMESA NRPESMESA
	 */
	public function comanda($produtos, $pagamento, $dadosConsumidor, $dadosFilial, $dadosPedido){
		try {

			$dadosConsumidor = $this->consumidor->populaDadosConsumidor($dadosConsumidor);
			$enderecoComanda = self::defineEnderecoComanda($dadosConsumidor, $dadosFilial);
			if($enderecoComanda['error'] == false){
				$this->defineRetiradaBalcao($dadosPedido);
				$validaDados = self::validaDadosComanda($dadosFilial);

				if($validaDados['error'] == false){
					$dadosNovaComanda = self::abreComanda($validaDados['data'], $dadosFilial, $dadosConsumidor, $dadosPedido, $enderecoComanda['enderecoComanda'], $pagamento);
					if($dadosNovaComanda['error'] == false){
						$dadosPagamento = self::registraPagamento($pagamento['tipoRecebimento'], $dadosConsumidor, $dadosFilial, $dadosNovaComanda['dadosComanda']);
						/*TODO: Pagamento online*/
						if($dadosPagamento['error'] == false){
							$produtosTratados = self::preparaProdutos($produtos, $dadosFilial, $dadosConsumidor, $dadosNovaComanda['dadosComanda'], $pagamento);
							$retornoInsereProdutos = self::insereProdutos($dadosFilial, $dadosNovaComanda['dadosComanda'], $produtosTratados, $dadosConsumidor, $validaDados['data']);

								if ($retornoInsereProdutos['error'] == false) {
									return array(
										'error' => false,
										'dadosComanda' => $dadosNovaComanda['dadosComanda'],
										'dadosPagamento' => $dadosPagamento['dadosPagamento'],
										'dadosConsumidor' => $dadosConsumidor,
										'produtosTratados' => $produtosTratados);
								} else {
									$retornoInsereProdutos['dadosComanda'] = $dadosNovaComanda['dadosComanda'];
									$retornoInsereProdutos['dadosConsumidor'] = $dadosConsumidor;
									return $retornoInsereProdutos;
								}

						} else {
							$dadosProdutos['dadosComanda'] = $dadosNovaComanda['dadosComanda'];
							$dadosProdutos['dadosConsumidor'] = $dadosConsumidor;
							return $dadosProdutos;
						}
					} else {
						return $dadosNovaComanda;
					}
				} else {
					return $validaDados;
				}
			} else {
				return $enderecoComanda;
			}
		} catch(\Exception $e){
			$result = array(
				'error' => true,
				'message' => $e->getMessage()
			);
			return $result;
		}

	}

	public function trataDivisaoDesconto($assembledProducts, $orderDiscount, $pagamento){
		$produtosCupom = array();
		if(isset($pagamento['promoCoupon'])){
			$produtosCupom = $pagamento['promoCoupon']['produtos'];
		}
        if(!empty($produtosCupom)){
            foreach($produtosCupom as $key => $value){
                foreach($assembledProducts as &$product){
                    if($product['CDPRODUTO'] == str_replace('.','',$key)){
                        $product['DESC'] = $product['DESC'] + $value['desconto'];
                    }
                }
            }

            return $assembledProducts;

        }else{
            $orderValue = 0;

			foreach($assembledProducts as $product){
				$itemDiscount = isset($product['DESC']) ? $product['DESC'] : 0;
				$orderValue  += ($product['PRECO'] - $itemDiscount) * $product['QTDPRODUTO'];
			}

			foreach($assembledProducts as &$product){
				$itemDiscount    = isset($product['DESC']) ? $product['DESC']  : 0;
				$discountToShare = number_format(((($product['PRECO'] - $itemDiscount) * $product['QTDPRODUTO']) * $orderDiscount) / $orderValue,2);
				$product['DESC'] = $product['DESC'] + $discountToShare;
				$product['DESCFLAG'] = true;
			}

			return $assembledProducts;
        }
    }

	public function defineEnderecoComanda($dadosConsumidor, $dadosFilial){
		try {
			$params = array('CDFILIAL' => $dadosFilial['CDFILIAL']);
			$enderecoFilial = $this->entityManager->getConnection()->fetchAssoc("ENDERECO_LOJA", $params);
			if(empty($dadosConsumidor['NRCEPCONS'])){
				$cepComanda = $enderecoFilial['NRCEPFILI'];
				$dsEndeComanda = $enderecoFilial['DSENDEFILI'];
				$dsBairroComanda = $enderecoFilial['NMBAIRFILI'];
			} else {
				$cepComanda = $dadosConsumidor['NRCEPCONS'];
				$dsEndeComanda = $dadosConsumidor['DSENDECONS'];
				$dsBairroComanda = $dadosConsumidor['NMBAIRRO'];
			}
			$enderecoComanda = array(
				'NRCEPCONSCOMAND' => $cepComanda,
				'DSBAIRRO' => $dsBairroComanda,
				'DSENDECONSCOMAN' => $dsEndeComanda);
			return array(
				'error' => false,
				'enderecoComanda' => $enderecoComanda);
		} catch (\Exception $e) {
			return array(
        		'error' => true,
        		'message' => $e->getMessage()
        	);
		}
	}

	const ENDERECO_LOJA = "
		SELECT FL.NMFILIAL, MP.NMMUNICIPIO, EF.DSENDEFILI, EF.NMBAIRFILI,
	   		EF.SGESTADO, EF.NRCEPFILI, EF.NRTELEFILI
  		FROM ENDEFILI EF
  				JOIN MUNICIPIO MP ON MP.CDMUNICIPIO = EF.CDMUNICIPIO
				JOIN FILIAL FL ON FL.CDFILIAL = EF.CDFILIAL
		WHERE EF.CDFILIAL = :CDFILIAL
				AND EF.IDTPENDEFILI = 'P'
   	";

	public function insereProdutos($dadosFilial, $dadosComanda, $dadosProdutos, $dadosConsumidor, $dadosGerais){
		try {
        	$connection = $this->entityManager->getConnection();
        	$CDCONTADOR = 'ITCOMANDAVEN' . $dadosFilial['CDFILIAL'];
        	$NRORG = 1;
			$nrSeqProdCup = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 10);
            foreach($dadosProdutos as $produto){
				$idStPrComVen = $this->getTheThing($dadosFilial['CDFILIAL'], $dadosFilial['CDLOJA'], $produto['CDPRODUTO'], $dadosGerais['IDCONTROPROD']);
				$CDCONTADOR = 'ITCOMANDAVEN'.$dadosFilial['CDFILIAL'].$dadosComanda['NRCOMANDA'];
				$nrProdComVen = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 6);

				if(!$produto['DESC'] || $produto['DESC'] == 0) {

                    $calculaDesconto = (isset($produto["IDTIPOCOMPPROD"])) ? ($produto["IDTIPOCOMPPROD"] != 'C') : true;

                    if($calculaDesconto) {
                        if(gettype($produto['PRICE']) == 'string') {
                            if(floatval($produto['PRECO']) != $produto['price']) {
                                $produto['DESC'] = $produto['PRECO'] - $produto['price'];
                            }
                        } else {
                            if($produto['PRECO'] != $produto['PRICE']) {
                                $produto['DESC'] = $produto['PRECO'] - $produto['PRICE'];
                            }
                        }
                    }
				}

				$desconto = (isset($produto['DESCFLAG']) && $produto['DESCFLAG']) ? $produto['DESC'] : $produto['DESC'] * $produto['QTDPRODUTO'];

				$insertParams = array(
                    'CDFILIAL'       => $dadosFilial['CDFILIAL'],
                    'NRVENDAREST'    => $dadosComanda['NRVENDAREST'],
                    'NRCOMANDA'      => $dadosComanda['NRCOMANDA'],
                    'NRPRODCOMVEN'   => $nrProdComVen,
                    'CDPRODUTO'      => $produto['CDPRODUTO'],
                    'QTPRODCOMVEN'   => $produto['QTDPRODUTO'],
                    'VRPRECCOMVEN'   => $produto['PRECO'],
                    'TXPRODCOMVEN'   => $produto['TXPRODCOMVEN'],
                    'IDSTPRCOMVEN'   => $idStPrComVen,
                    'VRDESCCOMVEN'   => $desconto,
                    'NRLUGARMESA'    => '1',
                    'IDPRODIMPFIS'   => 'N',
                    'CDLOJA'         => $dadosFilial['CDLOJA'],
                    'VRACRCOMVEN'    => $produto['ACRE'] * $produto['QTDPRODUTO'],
                    'NRSEQPRODCOM'   => $produto['NRSEQPRODCOM'],
                    'NRSEQPRODCUP'   => $nrSeqProdCup,
                    'VRPRECCLCOMVEN' => 0,
                    'CDCAIXACOLETOR' => $dadosFilial['CDCAIXA'],
                    'CDPRODPROMOCAO' => $produto['CDPRODPROMOCAO'],
                    'CDVENDEDOR'     => $dadosGerais['CDVENDEDOR'],
                    'DSOBSPEDDIGCMD' => $produto['CUSTOMOBS']
				);
				$this->entityManager->getConnection()->executeQuery("INSERT_PRODUCT", $insertParams);
				foreach($produto['PROMOPRODS'] as $promoItem) {
                    $promoParams = array(
                        'CDFILIAL'        => $dadosFilial['CDFILIAL'],
                        'NRVENDAREST'     => $dadosComanda['NRVENDAREST'],
                        'NRCOMANDA'       => $dadosComanda['NRCOMANDA'],
                        'NRPRODCOMVEN'    => $nrProdComVen,
                        'CDPRODUTO'       => $promoItem['CDPRODUTO'],
                        'QTPROCOMEST'     => $produto['QTDPRODUTO'],
                        'VRPRECCOMEST'    => $promoItem['PRICE'],
                        'VRDESITCOMEST'   => $promoItem['DESC'],
                        'TXPRODCOMVENEST' => $promoItem['TXPRODCOMVEN']
                    );
                    $this->entityManager->getConnection()->executeQuery("INSERT_SMARTPROMO", $promoParams);
				}

				$nrProdComVenPai = $nrProdComVen;

				$pricedObservations   = array();
                $ordinaryObservations = array();

				foreach($produto['PRICEDOBSERVATIONS'] as $pricedObservation) {
                    $obsKey = $pricedObservation['CDGRPOCOR'].$pricedObservation['CDOCORR'];
                    if(isset($pricedObservations[$obsKey])) {
                        $pricedObservations[$obsKey]['QTDPRODUTO']++;
                    } else {
                        $pricedObservations[$obsKey] = $pricedObservation;
                        $pricedObservations[$obsKey]['QTDPRODUTO'] = 1;
                    }
				}

				if(isset($produto['ORDINARYOBSERVATIONS'])) {
                    foreach($produto['ORDINARYOBSERVATIONS'] as $ordinaryObservation) {
                        $obsKey = $ordinaryObservation['CDGRPOCOR'].$ordinaryObservation['CDOCORR'];

                        if(isset($ordinaryObservations[$obsKey])) {
                            $ordinaryObservations[$obsKey]['QTDOBS']++;
                        } else {
                            $ordinaryObservations[$obsKey] = $ordinaryObservation;
                            $ordinaryObservations[$obsKey]['QTDOBS'] = 1;
                        }
					}
                }

				foreach($pricedObservations as $pricedObservation) {
                    if(sizeof($pricedObservation) > 0) {
						$nrSeqProdCup = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 10);
						$nrProdComVen = $this->util->geraCodigo($connection, $CDCONTADOR, $NRORG, 1, 6);

                        if(isset($pricedObservation['QTDPRODUTO'])) {
                            // $productQuantity = $pricedObservation['QTDPRODUTO'];
                            $productQuantity = $pricedObservation['QTDPRODUTO'] * $produto['QTDPRODUTO'];
                        } else {
                            $productQuantity = $produto['QTDPRODUTO'];
                        }

                        $insertParams = array(
                            'CDFILIAL'       => $dadosFilial['CDFILIAL'],
                            'NRVENDAREST'    => $dadosComanda['NRVENDAREST'],
                            'NRCOMANDA'      => $dadosComanda['NRCOMANDA'],
                            'NRPRODCOMVEN'   => $nrProdComVen,
                            'CDPRODUTO'      => $pricedObservation['CODIGOOCORRENCIA'],
                            'QTPRODCOMVEN'   => $productQuantity,
                            'VRPRECCOMVEN'   => $pricedObservation['PRICE'],
                            'TXPRODCOMVEN'   => null, //observations do not have observations
                            'IDSTPRCOMVEN'   => $idStPrComVen,
                            'VRDESCCOMVEN'   => 0,
                            'NRLUGARMESA'    => '1',
                            'IDPRODIMPFIS'   => 'N',
                            'CDLOJA'         => $dadosFilial['CDLOJA'],
                            'VRACRCOMVEN'    => 0,
                            'NRSEQPRODCOM'   => $produto['NRSEQPRODCOM'],
                            'NRSEQPRODCUP'   => $nrSeqProdCup,
                            'VRPRECCLCOMVEN' => 0,
                            'CDCAIXACOLETOR' => $dadosFilial['CDCAIXA'],
                            'CDPRODPROMOCAO' => $produto['CDPRODPROMOCAO'],
                            'CDVENDEDOR'     => null,
                            'DSOBSPEDDIGCMD' => !empty($produto['CUSTOMOBS']) ? $produto['CUSTOMOBS'] : ''
						);
                        $this->entityManager->getConnection()->executeQuery("INSERT_PRODUCT", $insertParams);

                        $insertObservationParams = array(
                            'CDFILIAL'          => $dadosFilial['CDFILIAL'],
                            'NRVENDAREST'       => $dadosComanda['NRVENDAREST'],
                            'NRCOMANDA'         => $dadosComanda['NRCOMANDA'],
                            'NRPRODCOMVEN'      => $nrProdComVenPai,
                            'NRPRODCOMVENOBS'   => $nrProdComVen,
                            'CDOCORR'           => $pricedObservation['CDOCORR'],
                            'CDGRPOCOR'         => $pricedObservation['CDGRPOCOR'],
                            'QTPRODCOMVENOBS'   => $productQuantity
                        );
                        $this->entityManager->getConnection()->executeQuery("INSERT_PRICED_OBSERVATION", $insertObservationParams);
                    }
                }

				foreach($ordinaryObservations as $ordinaryObs) {
                    $ordinaryObsParams = array(
                        'CDFILIAL'        => $dadosFilial['CDFILIAL'],
                        'NRVENDAREST'     => $dadosComanda['NRVENDAREST'],
                        'NRCOMANDA'       => $dadosComanda['NRCOMANDA'],
                        'NRPRODCOMVEN'    => $nrProdComVenPai,
                        'NRPRODCOMVENOBS' => null,
                        'CDOCORR'         => $ordinaryObs['CDOCORR'],
                        'CDGRPOCOR'       => $ordinaryObs['CDGRPOCOR'],
                        'QTPRODCOMVENOBS' => $ordinaryObs['QTDOBS']
                    );

                    $this->entityManager->getConnection()->executeQuery("INSERT_PRICED_OBSERVATION", $ordinaryObsParams);
				}
			}
		} catch (\Exception $e) {
			throw new \Exception($e->getMessage());
		}
	}

	public function getTheThing($CDFILIAL, $CDLOJA, $CDPRODUTO, $IDCONTROPROD){
        try {
        	$params = array(
				':CDFILIAL' => $CDFILIAL,
				':CDLOJA' => $CDLOJA,
				':CDPRODUTO' => $CDPRODUTO
			);
        	$theThing = $this->entityManager->getConnection()->fetchAssoc("GET_SECTOR_CODE", $params);
            if ($IDCONTROPROD === 'S' && $theThing !== null){
                $finalThing = '1';
            }
            else {
                $finalThing = '4';
            }
            return $finalThing;
        } catch (\Exception $e) {
        	return array(
        		'error' => true,
        		'message' => $e->getMessage());
        }
    }

	private function preparaProdutos($produtos, $dadosFilial, $dadosConsumidor, $dadosComanda, $pagamento){
		try {
			$produtosTratados = array();

			$lastNRSEQPRODCOM = $this->buscaPromoProdCode($dadosFilial['CDFILIAL'], $dadosComanda['NRVENDAREST'], $dadosComanda['NRCOMANDA']);
			$lastNRSEQPRODCOM = intval($lastNRSEQPRODCOM['NRSEQPRODCOM']);

			$productsEx = $this->_trataProdutosCombo($produtos, $lastNRSEQPRODCOM, $dadosConsumidor['CDCLIENTE'], $dadosFilial['CDFILIAL'], $dadosFilial['CDLOJA']);
			foreach ($productsEx as $produto){
				$produto['fakeQuantity'] = '1.000';

				if(!isset($produto['CUSTOMOBS'])) {
                    $produto['CUSTOMOBS'] = null;
				}

				$detalhesProduto = $this->checkProduct($produto['CDPRODUTO'], $dadosFilial['CDFILIAL']);

				$discountFlag = $produto['DESC'] == 0 ? false : true;
				$produto['DESCFLAG'] = $discountFlag;

				if ($produto['IDTIPOCOMPPROD'] != 'C'){
                    $precoProduto = $this->getPrice($produto['CDPRODUTO'], $dadosConsumidor['CDCLIENTE'], $dadosFilial['CDFILIAL'], $dadosFilial['CDLOJA'], $produto['DESC']);
                    $detalhesProduto = array_merge($detalhesProduto, $precoProduto);
				}

				if (!empty($produto['PROMOPRODS'])) {
					for($i = 0; $i < $produto['QTPRODCOMVEN']; $i++) {
						$nrSeqProdCom = str_pad((string) ($lastNRSEQPRODCOM + 1), 3, '0', STR_PAD_LEFT);
						$lastNRSEQPRODCOM++;
                        $produto['NRSEQPRODCOM'] = $nrSeqProdCom;
						$produto['CDPRODPROMOCAO'] = $produto['CDPRODUTO'];

						foreach($produto['PROMOPRODS'] as &$promoItem){
							if(!isset($promoItem['CUSTOMOBS'])){
                                $promoItem['CUSTOMOBS'] = null;
							}

							$precoPromo = $this->precoSmartPromo($produto['CDPRODUTO'], $promoItem['GRUPO_PROMO'], $promoItem['CDPRODUTO'], $dadosConsumidor['CDCLIENTE'], $dadosFilial['CDFILIAL'], $dadosFilial['CDLOJA'], $dadosFilial['CDCAIXA']);
							$promoItem = array_merge($promoItem, $precoPromo);

							$promoItem = $this->checkDesconto($promoItem);

							$promoItem['NRSEQPRODCOM'] = $nrSeqProdCom;
							$promoItem['CDPRODPROMOCAO'] = $produto['CDPRODUTO'];

							if($promoItem['price'] > 6){
                                $promoItem['TXPRODCOMVEN'] = $produto['TXPRODCOMVEN'];
                                if($produto['ORDINARYOBSERVATIONS']){
                                    $promoItem['ORDINARYOBSERVATIONS'] = $produto['ORDINARYOBSERVATIONS'];
                                }
                            } else {
                                $promoItem['TXPRODCOMVEN'] = null;
							}

							$promoItem['PROMOPRODS'] = array();

							if ($produto['IDIMPPRODUTO'] == '2') {
                                $promoItem['PRICEDOBSERVATIONS'] = $produto['PRICEDOBSERVATIONS'];
                                foreach($promoItem['PRICEDOBSERVATIONS'] as &$pricedObservation){
                                    $pricedObservation['QTDPRODUTO'] = ($produto['fakeQuantity'] / 2);
                                }
                                array_push($produtosTratados, $promoItem);
                            }
						}
					}
				} else if ($produto['IDTIPOCOMPPROD'] !== "C") {
                    $produto['NRSEQPRODCOM'] = null;
                    $produto['CDPRODPROMOCAO'] = null;
                }
                $detalhesProduto['QTDPRODUTO'] = $produto['QTPRODCOMVEN'];

                $detalhesProduto = array_merge($detalhesProduto, $produto);

                if ($detalhesProduto['IDIMPPRODUTO'] == '1') {
                    array_push($produtosTratados, $detalhesProduto);
                }
			}

			if(isset($pagamento['discount'])){
				$produtosTratados = $this->trataDivisaoDesconto($produtosTratados, $pagamento['discount'], $pagamento);
			}
			return $produtosTratados;
        } catch (\Exception $e) {
            throw new \Exception('Erro de validação: <br>' . $e->getMessage());
        }
	}

	private function _trataProdutosCombo($produtos, $lastNRSEQPRODCOM, $CDCLIENTE, $CDFILIAL, $CDLOJA){
		$produtosFinais = array();

		for ($i = 0; $i < sizeof($produtos); $i++) {
			if ($produtos[$i]['IDTIPOCOMPPROD'] != 'C') {
                array_push($produtosFinais, $produtos[$i]);
            } else {
				$nrSeqProdCom = str_pad((string) ($lastNRSEQPRODCOM + 1), 3, '0', STR_PAD_LEFT);
				$lastNRSEQPRODCOM++;
				$IDTIPCOBRA = $this->getIdTipCobra($CDFILIAL, $CDLOJA);
				$precoMax = 0;

				if ($IDTIPCOBRA == 'C') {
					foreach ($produtos[$i]['SELECTION'] as $comboProd) {
                        if ($comboProd['price'] > $precoMax) {
                            $precoMax = $comboProd['price'];
                        }
                    }
				}

				$quantidadeItem = $produtos[$i]['QTPRODCOMVEN'];
				$quantidadeEspecial = 0;

				$quantidade = round(1 / sizeof($produtos[$i]['SELECTION']), 2);

				if ($quantidade * sizeof($produtos[$i]['SELECTION']) != 1) {
                    $quantidadeEspecial = $quantidade + (1 - $quantidade * sizeof($produtos[$i]['SELECTION']));
				}

				$precoObs = 0;
				foreach ($produtos[$i]['SELECTION'] as &$comboProds) {
                    if(isset($comboProds['MARKEDOBS'])) {
                        foreach($comboProds['MARKEDOBS'] as $observacoes) {
                            if(isset($observacoes['PRICE'])) {
                                $precoObs += $observacoes['PRICE'];
                                $comboProds['price'] += $precoObs;
                            }
                        }
                    }
				}

				foreach ($produtos[$i]['SELECTION'] as $comboProd) {
					if ($IDTIPCOBRA == 'C') {
                        $preco = $precoMax + $precoObs;
                    } else {
						$preco = round($comboProd['price'], 2) + $precoObs;
					}

					if ($quantidadeEspecial > 0) {
                        $QTPRODCOMVEN = $quantidadeEspecial;
                        $quantidadeEspecial = 0;
                    } else {
                        $QTPRODCOMVEN = $quantidade;
					}

					$TXPRODCOMVEN = null;
					if (array_key_exists('MARKEDOBS', $comboProd)) {
                        $TXPRODCOMVEN = '';
                        foreach ($comboProd['MARKEDOBS'] as $comboObs) {
                            $TXPRODCOMVEN .= $comboObs['DSOCORR'].'; ';
                        }
					}

					if(array_key_exists('CUSTOMOBS', $produtos[$i])) {
                        if($produtos[$i]['CUSTOMOBS'] !== "" && $produtos[$i]['CUSTOMOBS'] !== null) {
                            $TXPRODCOMVEN .= $produtos[$i]['CUSTOMOBS'].'; ';
                        }
					}

					$templateProduto = array(
                        "ID"                 => null,
                        "CDFILIAL"           => null,
                        "CDPRODUTO"          => $comboProd['CDPRODUTO'],
                        "DSBUTTON"           => $comboProd['NOME'],
                        "QTPRODCOMVEN"       => $QTPRODCOMVEN * $quantidadeItem,
                        "PRICE"              => $comboProd['price'],
                        "PRECO"              => $preco,
                        "DESC"               => 0,
                        "ACRE"               => 0,
                        "NRCONFTELA"         => null,
                        "IDIMPPRODUTO"       => $comboProd['IDIMPPRODUTO'],
                        "IDTIPOCOMPPROD"     => 'C',
                        "NRSEQPRODCOM"       => $nrSeqProdCom,
                        "CDPRODPROMOCAO"     => null,
                        "TXPRODCOMVEN"       => $TXPRODCOMVEN,
                        "PRICEDOBSERVATIONS" => array(),
                        "PROMOPRODS"         => array(),
                        "SELECTION"          => array()
					);

					array_push($produtosFinais, $templateProduto);
				}
			}
		}
		return $produtosFinais;
	}

	public function getIdTipCobra($CDFILIAL, $CDLOJA){
        $params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':CDLOJA' => $CDLOJA
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_IDTIPCOBRA", $params);
        return !empty($result[0]['IDTIPCOBRA']) ? $result[0]['IDTIPCOBRA'] : array();
	}

	public function checkProduct($CDPRODUTO, $CDFILIAL){
        try {
            /* Gets the product details. */
            $productDetails = $this->buscaDetalhesProduto($CDPRODUTO);

            if (empty($productDetails['CDARVPROD'])) {
                throw new Exception\InvalidProductException('Produto não cadastrado.');
            }

            /* Checks if the product is taxed. */
            $aliquote = $this->getProductAliquote($CDFILIAL, $productDetails['CDPRODUTO']);

            if ($aliquote['COUNT'] === 0) {
                throw new Exception\UntaxedProductException('Produto sem imposto.');
            }

            return $productDetails;
        } catch (Exception\SqlException $e) {
            throw new Exception\SqlException($this->deliveryMessage->getMessage('0').$e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
	}

	public function getPrice($productCode, $CDCLIENTE, $CDFILIAL, $CDLOJA, $desc){
        try {
            $pricingDetails = array();

            $pricingTable = $this->getPricingTable($CDCLIENTE, $CDFILIAL, $CDLOJA);
            $effectiveDate = $this->checkEffective($pricingTable['CDFILTABPREC'], $pricingTable['CDTABEPREC']);

            $itemPrice = $this->checkPrice($pricingTable['CDFILTABPREC'], $pricingTable['CDTABEPREC'], $productCode, $effectiveDate);
            if (!empty($itemPrice)){

                $return['PRECOVAR']   = floatval($itemPrice['IDPRECVARIA']);
                $return['PRECOCLIE']  = floatval($itemPrice['PRECOCLIE']);

                $dailyPrice = $this->dailyPrice($pricingTable['CDFILTABPREC'], $pricingTable['CDTABEPREC'], $productCode, $effectiveDate);

                if (empty($dailyPrice)){
                    $return['PRECOSUGER'] = floatval($itemPrice['PRECOSUGER']);
                    $return['PRECO'] = floatval($itemPrice['PRECO']);
                    $return['ACRE'] = 0;
                } else {
                    $return["PRECOSUGER"] = 0;

                    if ($dailyPrice['PERC'] === 'V')
                        $mod = $dailyPrice['VRPRECODIA'];
                    else
                        $mod = round(($itemPrice['PRECO'] * $dailyPrice['VRPRECODIA']) / 100, 2);

                    if ($dailyPrice['DESCACRE'] === 'A') {

                        if ($dailyPrice['IDVISUACUPOM'] === 'N') {
                            $return['ACRE'] = 0;
                            $return['PRECO'] = floatval(round($itemPrice['PRECO'] + $mod, 2));
                        } else {
                            $return['ACRE'] = floatval($mod);
                            $return['PRECO'] = floatval(round($itemPrice['PRECO'], 2));
                        }
                    } else {
                        $return['ACRE'] = 0;

                        if ($dailyPrice['IDVISUACUPOM'] === 'N') {
                            $return['PRECO'] = floatval(round($itemPrice['PRECO'] - $mod, 2));
                        } else {
                            $return['DESC'] = floatval($mod) + $desc;
                            $return['PRECO'] = floatval(round($itemPrice['PRECO'], 2));
                        }
                    }
                }
            }

            return $return;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
	}

	public function getPricingTable($CDCLIENTE, $CDFILIAL, $CDLOJA){
        try {
            $pricingTable = $this->clientBranchPricingTable($CDCLIENTE, $CDFILIAL);

            if(empty($pricingTable)) {
                $pricingTable = $this->clientPricingTable($CDCLIENTE);

                if (empty($pricingTable)) {
                    $pricingTable = $this->storePricingTable($CDFILIAL, $CDLOJA);

                    if (empty($pricingTable)) {
                        $pricingTable = $this->paravendPricingTable($CDFILIAL);
                    }
                }
            }

            if(!empty($pricingTable)) {
                if ($pricingTable['CDFILTABPREC'] === null) {
                    $pricingTable['CDFILTABPREC'] = $CDFILIAL;
                }
            } else {
                throw new \UnexpectedValueException('Não há tabela de preços cadastrada.');
            }

            return $pricingTable;

        } catch (\UnexpectedValueException $e) {
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
	}

	public function clientBranchPricingTable($CDCLIENTE, $CDFILIAL){
        $params = array(
            ':CDCLIENTE' => $CDCLIENTE,
            ':CDFILIAL' => $CDFILIAL
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_CLIENTBRANCH_PRICING_TABLE", $params);
        return (!empty($result[0]['CDTABEPREC']) ? $result[0] : array());
	}

	public function clientPricingTable($CDCLIENTE){
        $params = array(
            ':CDCLIENTE' => $CDCLIENTE
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_CLIENT_PRICING_TABLE", $params);
        return (!empty($result[0]['CDTABEPREC']) ? $result[0] : array());
	}

	public function storePricingTable($CDFILIAL, $CDLOJA){
        $params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':CDLOJA' => $CDLOJA
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_STORE_PRICING_TABLE", $params);
        return (!empty($result[0]['CDTABEPREC']) ? $result[0] : array());
	}

	public function paravendPricingTable($CDFILIAL){
        $params = array(
            ':CDFILIAL' => $CDFILIAL
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_PARAVEND_PRICING_TABLE", $params);
        return (!empty($result[0]['CDTABEPREC']) ? $result[0] : array());
	}

	public function checkEffective($CDFILIAL, $priceTable) {
        try {
            $salesTable = $this->getSalesTable($CDFILIAL, $priceTable);

            if (!empty($salesTable))
                return $salesTable['DTINIVGPREC'];
            else
                throw new \UnexpectedValueException('Tabela de preços está inativa.');

        } catch (\UnexpectedValueException $e) {
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
	}

	public function getSalesTable($CDFILIAL, $CDTABEPREC){
        $params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':CDTABEPREC' => $CDTABEPREC
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_SALES_TABLE", $params);
        return (!empty($result) ? $result[0] : $result);
	}

	public function checkPrice($CDFILIAL, $pricingTable, $productCode, $effectiveDate) {
        try {

            $itemPrice = $this->getItemPrice($CDFILIAL, $pricingTable, $effectiveDate, $productCode);

            if (empty($itemPrice)) {
                throw new \UnexpectedValueException('Preço não cadastrado.');
            } else if ($itemPrice['PRECO']+$itemPrice['PRECOCLIE'] === 0) {
                throw new \UnexpectedValueException('Produto sem preço cadastrado.');
            } else {
                return $itemPrice;
            }

        } catch (\UnexpectedValueException $e) {
            throw new \Exception($e->getMessage());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
	}

	public function getItemPrice($CDFILIAL, $CDTABEPREC, $DATAVIG, $CDPRODUTO){
        $params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':CDTABEPREC' => $CDTABEPREC,
            ':DATAVIG' => $DATAVIG,
            ':CDPRODUTO' => $CDPRODUTO
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_ITEM_PRICE", $params);
        return (!empty($result) ? $result[0] : $result);
	}

	public function dailyPrice($CDFILIAL, $pricingTableCode, $productCode, $effectiveDate) {
        try {
            $currentDay = date('d/m/Y');
            $nextDay = date('d/m/Y', strtotime('+1 day'));

            if ($this->isHoliday($CDFILIAL, $currentDay))
                $day = 'F';
            else if ($this->isHoliday($CDFILIAL, $nextDay))
                $day = 'V';
            else
                $day = (int)date('w') + 1;

            $hour = date('Hi');

            $priceByDay = $this->getDailyPrice($CDFILIAL, $pricingTableCode, $effectiveDate, $productCode, $day, $hour);

            if(empty($priceByDay)) {
                $priceByDay = $this->getDailyPrice($CDFILIAL, $pricingTableCode, $effectiveDate, $productCode, 'T', $hour);
            }

            return $priceByDay;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

	}

	public function isHoliday($CDFILIAL, $currentDay) {
        try {
            $check = $this->holidayCheck($CDFILIAL, $currentDay);

            if (!empty($check))
                return true;

            return false;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
	}

	public function holidayCheck($CDFILIAL, $DTFERIFILI){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'DTFERIFILI' => \DateTime::createFromFormat('d/m/Y', $DTFERIFILI)
        );
        $type = array(
            'DTFERIFILI' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $result = $this->entityManager->getConnection()->fetchAll("HOLIDAY_CHECK", $params, $type);
        return (!empty($result) ? $result[0] : $result);
	}

	public function getDailyPrice($CDFILIAL, $CDTABEPREC, $DTINIVGPREC, $CDPRODUTO, $day, $hour){
        $params = array(
            ':CDFILIAL'     => $CDFILIAL,
            ':CDTABEPREC'   => $CDTABEPREC,
            ':DTINIVGPREC'  => $DTINIVGPREC,
            ':CDPRODUTO'    => $CDPRODUTO,
            ':CDPRPAITABPR' => $CDPRODUTO,
            ':NRDIASEMANPR' => $day,
            ':CDTIPOCONSPD' => 'T',
            ':HORA'         => $hour
        );

        $result = $this->entityManager->getConnection()->fetchAll("ITEM_DAY_PRICE", $params);
        return (!empty($result) ? $result[0] : $result);
	}

	public function precoSmartPromo($CDPRODPROMOCAO, $CDGRUPROMOC, $CDPRODUTO, $CDCLIENTE, $CDFILIAL, $CDLOJA, $CDCAIXA){
    	$pricingTable = $this->getPricingTable($CDCLIENTE, $CDFILIAL, $CDLOJA);
    	$effectiveDate = $this->checkEffective($pricingTable['CDFILTABPREC'], $pricingTable['CDTABEPREC']);
    	return $this->getPrecoSmartPromo($CDPRODPROMOCAO, $CDGRUPROMOC, $CDPRODUTO, $CDFILIAL, $pricingTable['CDTABEPREC'], $effectiveDate);
	}

	public function getPrecoSmartPromo($CDPRODPROMOCAO, $CDGRUPROMOC, $CDPRODUTO, $CDFILIAL, $pricingTable, $effectiveDate){
    	$effectiveDate = $this->getSalesTable($CDFILIAL, $pricingTable);

    	$params = array(
    		':CDPRODPROMOCAO' => $CDPRODPROMOCAO,
    		':CDGRUPROMOC'    => $CDGRUPROMOC,
    		':CDPRODUTO'      => $CDPRODUTO,
            ':CDFILIAL'       => $CDFILIAL,
            ':CDTABEPREC'     => $pricingTable,
            ':DTINIVGPREC'    => $effectiveDate['ORIGINAL']
        );

        $result = $this->entityManager->getConnection()->fetchAll("GET_SMARTPROMO_PRICE", $params);
        return (!empty($result) ? $result[0] : $result);
	}

	private function processObservations($product){
        if (!empty($product['selectItems'])){
            $TXPRODCOMVEN = '';
            foreach ($product['selectItems'] as $observation){
                if (isset($observation['DSOCORR'])) {
                    $TXPRODCOMVEN .= $observation['DSOCORR'] . '; ';
                }
            }
            return $TXPRODCOMVEN;
        }
        else {
            return null;
        }
    }

	private function checkDesconto($produto){
        try {

            $desconto = empty($produto['DESC']) ? 0 : floatval($produto['DESC']);
            if ($produto['TIPO'] == 'P') {
                $produto['DESC'] = $produto['PRECO'] * ($produto['VRDESC']/100) + $desconto;
            } else if ($produto['TIPO'] == 'V') {
                $produto['DESC'] = $produto['VRDESC'] + $desconto;
            } else { // Products that haven't had a discount registered.
                $produto['DESC'] = $desconto;
            }

            if ($produto['DESC'] > $produto['price']) {
                $produto['DESC'] = $produto['PRECO'] - 0.01;
            }

            /* Last digit can't be 9. */
            $produto['DESC'] = intval($produto['DESC']*100)/100;
            return $produto;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

	public function buscaPromoProdCode($CDFILIAL, $NRVENDAREST, $NRCOMANDA){
		$params = array(
            ':CDFILIAL' => $CDFILIAL,
            ':NRVENDAREST' => $NRVENDAREST,
            ':NRCOMANDA' => $NRCOMANDA
        );
		$result = $this->entityManager->getConnection()->fetchAssoc("GET_NRSEQPRODCOM", $params);
		return $result;
	}

	private function buscaDetalhesProduto($CDPRODUTO){
		try {
			$params = array(
				':CDPRODUTO' => $CDPRODUTO
			);

			$result = $this->entityManager->getConnection()->fetchAll("GET_PRODUCT_DETAILS", $params);
			return !empty($result) ? $result[0] : $result;
		} catch (\Exception $e) {
			return array(
				'error' => true,
				'message' => $e->getMessage());
		}
	}

	public function getProductAliquote($CDFILIAL, $CDPRODUTO){
		try {
			$params = array(
				':CDFILIAL'  => $CDFILIAL,
				':CDPRODUTO' => $CDPRODUTO
			);

			$result = $this->entityManager->getConnection()->fetchAll("GET_ALIQUOTA", $params);
			return !empty($result) ? $result[0] : $result;
		} catch (\Exception $e) {
			return array(
				'error' => true,
				'message' => $e->getMessage());
		}
	}

	private function defineRetiradaBalcao(&$dadosPedido){
		if($dadosPedido['ISTOGO'] == true){
			$dadosPedido['IDRETBALLOJA'] = 'S';
		} else if($dadosPedido['ISTOGO'] == false) {
			$dadosPedido['IDRETBALLOJA'] = 'N';
		}
	}

	private function validaDadosComanda($dadosFilial){
		$result = array();
		$retornoVendedorPadrao = self::retornaVendedorPadrao($dadosFilial['CDFILIAL']);

		if($retornoVendedorPadrao['error'] == false){
			$result['data']['CDVENDEDOR'] = $retornoVendedorPadrao['data']['CDVENDEDOR'];
			$retornoOperador = self::retornaOperador($retornoVendedorPadrao['data']);

			if ($retornoOperador['error'] == false) {
				$result['data']['CDOPERADOR'] = $retornoOperador['CDOPERADOR'];
				$retornoIdcontroprod = self::retornaIdcontroprod($dadosFilial);

				if ($retornoIdcontroprod['error'] == false) {
					$result['error'] = false;
					$result['data']['IDCONTROPROD'] = $retornoIdcontroprod['IDCONTROPROD'];
					return $result;
				} else {
					return $retornoIdcontroprod;
				}
			} else {
				return $retornaOperador;
			}
		} else {
			return $retornoVendedorPadrao;
		}
	}

	private function trataAgendamento($dadosPedido){
		$isScheduled = $dadosPedido['schedule']['TOSCHEDULE'];
		if($isScheduled){
			$scheduleTime = date("H:i:s", strtotime($dadosPedido['schedule']['scheduleData']['time']));
			$DTHRAGENDADA = $dadosPedido['schedule']['scheduleData']['date'] . ' ' . $scheduleTime;
			return array(
				'isScheduled' => $isScheduled,
				'DTHRAGENDADA' => $DTHRAGENDADA);
		} else {
			return array(
				'isScheduled' => $isScheduled,
				'DTHRAGENDADA' => null);
		}
	}

	private function trataSTComanda($arrayScheduled, $validaDados){
		 if ($arrayScheduled['isScheduled']) {
            $IDSTCOMANDA = 'A';
        } else if ($validaDados['IDCONTROPROD'] == 'S'){
            $IDSTCOMANDA = '1';
        } else {
            $IDSTCOMANDA = '3';
        }
        return $IDSTCOMANDA;
	}

	private function abreComanda($validaDados, $dadosFilial, $dadosConsumidor, $dadosPedido, $enderecoComanda, $pagamento){
		try {
			$NRVENDAREST = self::generateVendarestSeq($dadosFilial['CDFILIAL']);
			$NRCOMANDA = self::generateComandavenSeq($dadosFilial['CDFILIAL']);
			$paramsInsertVendaRest = self::preparaInsereVendaRest($dadosFilial, $validaDados, $NRVENDAREST, $dadosConsumidor, $dadosPedido);
			$insereVendaRest = self::insereVendaRest($paramsInsertVendaRest);
			$arrayScheduled = self::trataAgendamento($dadosPedido);
			$IDSTCOMANDA = self::trataSTComanda($arrayScheduled, $validaDados);
			$descricaoCupom = '';
			if(isset($pagamento['promoCoupon'])){
				$descricaoCupom = $pagamento['promoCoupon']['cupom'];
			}
			if($insereVendaRest['error'] == false) {
				$paramsInsertComanda = self::preparaInsereComandaVen($dadosFilial, $dadosConsumidor, $dadosPedido, $NRVENDAREST, $NRCOMANDA, $IDSTCOMANDA, $arrayScheduled, $enderecoComanda, $descricaoCupom);
				$insereComandaVen = self::insereComandaVen($paramsInsertComanda);
				if($insereComandaVen['error'] == false){
					return array('error' => false,
						'dadosComanda' => $paramsInsertComanda);
				} else {
					return $insereComandaVen;
				}
			} else {
				return $insereVendaRest;
			}
		} catch(\Exception $e){
			return array(
				'error' => true,
				'message' => $e->getMessage()
			);
		}
	}

	public function preparaInsereVendaRest($dadosFilial, $validaDados, $NRVENDAREST, $dadosConsumidor, $dadosPedido){
		$dataatual = new \DateTime();
		$params = array(
			'CDFILIAL' => $dadosFilial['CDFILIAL'],
			'NRVENDAREST' => $NRVENDAREST,
			'CDLOJA'=> $dadosFilial['CDLOJA'],
			'NRMESA'=> isset($dadosPedido['NRMESA']) ? $dadosPedido['NRMESA'] : null,
			'CDVENDEDOR' => $validaDados['CDVENDEDOR'],
	        'DTHRABERMESA' => $dataatual,
	        'DTHRFECHMESA' => '',
	        'CDOPERADOR' => $validaDados['CDOPERADOR'],
	        'NRPESMESAVEN' => isset($dadosPedido['NRPESMESAVEN']) ? $dadosPedido['NRPESMESAVEN'] : null,
	        'CDCLIENTE' => $dadosConsumidor['CDCLIENTE'],
	        'CDCONSUMIDOR' => $dadosConsumidor['CDCONSUMIDOR'],
	        'IDPEDIDOPAGO' => 'S');
		return $params;
	}

	public function insereVendaRest($params){
		$types = array(
			'DTHRABERMESA' => \Doctrine\DBAL\Types\Type::DATE
		);
		try {
			$this->entityManager->getConnection()->executeQuery("INSERT_VENDAREST", $params, $types);
            $return = array(
				'error' => false
			);
			return $return;
		} catch (\Exception $e) {
			$return = array(
				'error'   => true,
				'message' => 'Erro ao inserir vendarest. Erro: '.$e->getMessage()
			);
		}
		return $return;
	}

    public function cancelaPedido($dadosPedido){
    	self::cancelaVendaRest($dadosPedido);
    	self::cancelaComandaVen($dadosPedido);
    }

    private function cancelaVendaRest($dadosPedido){
    	$params = array(
			':CDFILIAL' => $dadosPedido['dadosComanda']['CDFILIAL'],
			':CDCLIENTE' => $dadosPedido['dadosConsumidor']['CDCLIENTE'],
    		':CDCONSUMIDOR' => $dadosPedido['dadosConsumidor']['CDCONSUMIDOR'],
    		':NRVENDAREST' => $dadosPedido['dadosComanda']['NRVENDAREST']
    	);
    	$this->entityManager->getConnection()->executeQuery("CANCELA_VENDAREST", $params);
    }

    private function cancelaComandaVen($dadosPedido){
    	$params = array(
    		':CDFILIAL' => $dadosPedido['dadosComanda']['CDFILIAL'],
    		':NRCOMANDA' => $dadosPedido['dadosComanda']['NRCOMANDA'],
    		':NRVENDAREST' => $dadosPedido['dadosComanda']['NRVENDAREST']
    	);
    	$this->entityManager->getConnection()->executeQuery("CANCELA_COMANDAVEN", $params);
    }

	private function preparaInsereComandaVen($dadosFilial, $dadosConsumidor, $dadosPedido, $NRVENDAREST, $NRCOMANDA, $IDSTCOMANDA, $arrayScheduled, $enderecoComanda, $descricaoCupom){
		$CDCAMPANHA = null;
		$VRPONTOBRINDE = null;
		$CDCAMPANHA = null;
		$params = array(
			'CDFILIAL'	=> $dadosFilial['CDFILIAL'],
			'NRVENDAREST'	=> $NRVENDAREST,
			'NRCOMANDA'	=> $NRCOMANDA,
			'CDLOJA'	=> $dadosFilial['CDLOJA'],
			'DSCOMANDA'	=> 'DLV_'.$NRCOMANDA,
			'IDSTCOMANDA'	=>  $IDSTCOMANDA,
			'SGSEXOCON'	=> $dadosConsumidor['IDSEXOCONS'] ? $dadosConsumidor['IDSEXOCONS'] : 'F',
			'TXMOTIVCANCE'	=> '',
			'VRACRCOMANDA'	=> 0,
			'CDPAIS'	=> $dadosConsumidor['CDPAIS'],
			'SGESTADO'	=> $dadosConsumidor['SGESTADO'],
			'CDMUNICIPIO'	=> $dadosConsumidor['CDMUNICIPIO'],
			'NRCEPCONSCOMAND'	=> $enderecoComanda['NRCEPCONSCOMAND'],
			'CDBAIRRO'	=> $dadosConsumidor['CDBAIRRO'],
			'DSBAIRRO'	=> $enderecoComanda['DSBAIRRO'],
			'DSENDECONSCOMAN'	=> $enderecoComanda['DSENDECONSCOMAN'],
			'DSCOMPLENDCOCOM'	=> $dadosConsumidor['DSCOMPLENDECONS'],
			'DSREFENDCONSCOM'	=> $dadosConsumidor['DSREFERENDECONS'],
			'IDORGCMDVENDA'	=> '',
			'IDRETBALLOJA'	=> $dadosPedido['IDRETBALLOJA'],
			'NRCOMANDAEXT'	=> '',
			'IDCOMANDAPAGA' => 'S',
			'IDSINCAGENDA' => $arrayScheduled['isScheduled'] ? 'N' : 'S',
            'DTHRAGENDADA' => $arrayScheduled['DTHRAGENDADA'] ? $arrayScheduled['DTHRAGENDADA'] : null,
            'CDCAMPANHA'    => isset($dadosPedido['fidelityData']) ? $dadosPedido['fidelityData']['CDCAMPANHA'] : null,
            'VRPONTOBRINDE' => isset($dadosPedido['fidelityData']) ? $dadosPedido['fidelityData']['VRPONTOBRINDE'] : null,
            'VRDESCFIDELIDAD' => isset($dadosPedido['fidelityData']) ? $dadosPedido['fidelityData']['VALUE'] : null,
			'DSCUPOMPROMO' => $descricaoCupom,
			'IDEXTCONSAPP' => isset($dadosPedido['IDEXTCONSAPP']) ? $dadosPedido['IDEXTCONSAPP'] : 'N'
        );
        return $params;
	}

	public function insereComandaVen($params){
		$types = array(
			'DTHRABERMESA' => \Doctrine\DBAL\TypeS\Type::DATE
		);
		try {
			$this->entityManager->getConnection()->executeQuery("INSERT_COMANDAVEN", $params, $types);
            $return = array(
				'error' => false
			);
		} catch (\Exception $e) {
			$return = array(
				'error'   => true,
				'message' => 'Erro ao inserir comandaven. Erro: '.$e->getMessage()
			);
		}
		return $return;
	}

    private function registraPagamento($pagamento, $dadosConsumidor, $dadosFilial, $dadosNovaComanda){
		try {
            $dadosPagamento = $this->trataPedido($pagamento, $dadosConsumidor, $dadosNovaComanda, $dadosFilial);
	        if($dadosPagamento['error'] == false){
		        return array(
		        	'error' => false,
		        	'dadosPagamento' => $dadosPagamento['dadosPagamento']);
	        } else {
	        	return $dadosPagamento;
	        }
		} catch(\Exception $e){
			$return = array(
					'error' => true,
					'message' => $e->getMessage());
		}
    }

    private function trataPedido($arrayPagamento, $dadosConsumidor, $dadosNovaComanda, $dadosFilial){
    	try {
    		$troco = 0;
    		$dadosPagamento = array();
	    	foreach ($arrayPagamento as $pagamento){
	        	if ($pagamento['CDTIPORECE'] == self::TIPO_PAGAMENTO_DINHEIRO){
	                $troco = self::calculaTroco($pagamento);
	            }
	            if ($troco > 0){
	            	$pagamento['TROCO'] = $troco;
	            	$NRSEQMOVDLV = self::geraMovcaixadlvSeq($dadosFilial['CDFILIAL'], $dadosNovaComanda['NRVENDAREST']);
		        	$paramsPagamento = self::preparaInsereMovCaixa($dadosConsumidor, $dadosNovaComanda, $pagamento, $NRSEQMOVDLV, self::MOVIMENTACAO_SAIDA);
					$dadosMovCaixaTroco = $this->insereMovCaixaDlv($paramsPagamento);

					if($dadosMovCaixaTroco['error'] === true){
						return $dadosMovCaixaTroco;
					}
		        }
		        $NRSEQMOVDLV = self::geraMovcaixadlvSeq($dadosFilial['CDFILIAL'], $dadosNovaComanda['NRVENDAREST']);
		        $paramsPagamento = self::preparaInsereMovCaixa($dadosConsumidor, $dadosNovaComanda, $pagamento, $NRSEQMOVDLV, self::MOVIMENTACAO_ENTRADA);
				$dadosMovCaixa = $this->insereMovCaixaDlv($paramsPagamento);

				if($dadosMovCaixa['error'] === true){
					return $dadosMovCaixa;
				}

	            array_push($dadosPagamento, $paramsPagamento);
			}

            return array(
            	'error' => false,
            	'dadosPagamento' => $dadosPagamento
            );
    	} catch (\Exception $e) {
    		return array(
    			'error' => true,
    			'message' => $e->getMessage());
    	}
    }

    private function calculaTroco($pagamento){
    	$troco = $pagamento['TROCO'] - $pagamento['VALOR'];
        return $troco;
    }

    private function preparaInsereMovCaixa($dadosConsumidor, $dadosNovaComanda, $pagamento, $NRSEQMOVDLV, $TIPO_MOVIMENTACAO){
    	try{
    		if($TIPO_MOVIMENTACAO == self::MOVIMENTACAO_SAIDA){
	    		$VRMOVIVENDDLV = $pagamento['TROCO'];
	    	} else if($TIPO_MOVIMENTACAO == self::MOVIMENTACAO_ENTRADA){
				$VRMOVIVENDDLV = $pagamento['VALOR'];
	    	}
	    	$params = array(
	    		'CDFILIAL' 			=> 		$dadosNovaComanda['CDFILIAL'],
				'NRVENDAREST' 		=>     	$dadosNovaComanda['NRVENDAREST'],
				'NRSEQMOVDLV' 		=>     	$NRSEQMOVDLV,
				'IDTIPOMOVIVEDLV' 	=> 		$TIPO_MOVIMENTACAO,
				'VRMOVIVENDDLV' 	=>   	$VRMOVIVENDDLV,
				'CDTIPORECE' 		=>      $pagamento['CDTIPORECE'],
				'CDCLIENTE' 		=>      $dadosConsumidor['CDCLIENTE'],
				'CDCONSUMIDOR' 		=>    	$dadosConsumidor['CDCONSUMIDOR'],
				'CDFAMILISALD'		=>		isset($pagamento['CDFAMILISALD']) ? $pagamento['CDFAMILISALD'] : null
			);
			return $params;
    	} catch(\Exception $e){
    		return array(
    			'error' => true,
    			'message' => $e->getMessage());
    	}
    }

	/**
	 * [retornaOperador description]
	 * @param  [array] $vendedorPadrao [verifica se no vendedor padrão existe um operador na coluna CDOPERADOR]
	 * @return [array] $return [retorna error true caso nao exista operador, ou o cdoperador ]
	 */
	public function retornaOperador($vendedorPadrao){
		try {
			if(!empty($vendedorPadrao['CDOPERADOR'])){
				$return = array(
					'error' => false,
					'CDOPERADOR' => $vendedorPadrao['CDOPERADOR']);
			} else {
				$return = array(
					'error' => true,
					'message' => 'Vendedor padrão da filial não contém operador vinculado.');
			}
			return $return;
		} catch (Exception $e) {
			$return = array(
					'error' => true,
					'message' => 'Erro ao buscar parametros de vendedor padrao. Erro: '.$e->getMessage());
		}

	}

	public function retornaVendedorPadrao($CDFILIAL) {
		$params = array(
			':CDFILIAL'   => $CDFILIAL
		);
		try {
			$standardSeller = $this->entityManager->getConnection()->fetchAssoc("GET_STANDARD_SELLER", $params);
			if(!$standardSeller){
				$return = array(
					'error'    => true,
					'message'  => 'Parametrizacao de vendedor padrao incompleta ou inexistente'
				);
			} else {
				$return = array(
					'error' => false,
					'data'  => $standardSeller
				);
			}
		} catch (\Exception $e) {
			$return = array(
				'error'   => true,
				'message' => 'Erro ao buscar parametros de vendedor padrao. Erro: '.$e->getMessage()
			);
		}
		return $return;
	}

	public function retornaIdcontroprod($dadosFilial){
		try {
			$params = array(
				':CDFILIAL' => $dadosFilial['CDFILIAL'],
				':CDLOJA' => $dadosFilial['CDLOJA']
			);
            $result = $this->entityManager->getConnection()->fetchAssoc("GET_PRODUCT_CONTROL_ID", $params);
            if(!empty($result['IDCONTROPROD'])){
            	$return = array(
            		'error' => false,
            		'IDCONTROPROD' => $result['IDCONTROPROD']);
            } else {
            	$return = array(
            		'error' => true,
            		'message' => 'Controle de produção não definido na loja.');
            }
            return $return;
        } catch (\Exception $e) {
        	$return = array(
				'error'   => true,
				'message' => 'Erro ao buscar parametros de controle de produção. Erro: '.$e->getMessage()
			);
        }
	}

	public function generateVendarestSeq($CDFILIAL){
		$vendarestCounter = 'VENDAREST'.$CDFILIAL;
		$connection 	  = $this->entityManager->getConnection();
		$NRORG    		  = 1;
		$quantity 		  = 1;
		$size     		  = 10;
		$vendarestSeq 	  = $this->util->geraCodigo($connection,$vendarestCounter, $NRORG, $quantity, $size);
		return $vendarestSeq;
	}

	public function generateComandavenSeq($CDFILIAL){
		$comandavenCounter = 'COMANDAVEN'.$CDFILIAL;
		$connection 	   = $this->entityManager->getConnection();
		$NRORG    		   = 1;
		$quantity 		   = 1;
		$size     		   = 10;
		$comandavenSeq 	   = $this->util->geraCodigo($connection,$comandavenCounter, $NRORG, $quantity, $size);
		return $comandavenSeq;
	}

	public function generateItemsSeq($CDFILIAL){
		$itemsCounter      = 'ITCOMANDAVEN'.$CDFILIAL;
        $connection 	   = $this->entityManager->getConnection();
		$NRORG    		   = 1;
		$quantity 		   = 1;
		$size     		   = 10;
		$itemsSeq    	   = $this->util->geraCodigo($connection,$itemsCounter, $NRORG, $quantity, $size);
		return $itemsSeq;
	}

	public function generateItcomandavenSeq($CDFILIAL, $NRCOMANDA){
		$itcomandavenCounter = 'ITCOMANDAVEN'.$CDFILIAL.$NRCOMANDA;
        $connection 	     = $this->entityManager->getConnection();
		$NRORG    		     = 1;
		$quantity 		     = 1;
		$size     		     = 6;
		$NRPRODCOMVEN     = $this->util->geraCodigo($connection,$itcomandavenCounter, $NRORG, $quantity, $size);
		return $NRPRODCOMVEN;

	}

	public function geraMovcaixadlvSeq($CDFILIAL, $NRVENDAREST){
		try{
			$movcaixadlvCounter = 'MOVCAIXADLV'.$CDFILIAL.$NRVENDAREST;
			$connection 	    = $this->entityManager->getConnection();
			$NRORG    		    = 1;
			$quantity 		    = 1;
			$size     		    = 10;
			$movcaixadlvSeq     = $this->util->geraCodigo($connection,$movcaixadlvCounter, $NRORG, $quantity, $size);
			return $movcaixadlvSeq;
		} catch(\Exception $e){
			return array('message' => $e->getMessage());
		}

	}

	public function insereMovCaixaDlv($params){
		try {
            $success = $this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXADLV", $params);

            $return = array(
				'error' => false
			);
		} catch (\Exception $e) {
			$return = array(
				'error'   => true,
				'message' => 'Erro ao inserir pagamento. Erro: '.$e->getMessage()
			);
		}

		return $return;
	}

    public function getExternalSaleData($NRCOMANDAEXT) {
	    try {
	        $data   = array(); // dados a serem retornados

	        $return = self::getLastSale($NRCOMANDAEXT);

	        if (!$return['error']) {
	            $lastSale = $return['data'];

	            if (!empty($lastSale)) {
	                // formata os dados da última venda aprovada do cliente
	                $deliveryFee   = $lastSale['IDRETBALLOJA'] == 'S' ? 0 : $lastSale['VRACRCOMANDA'];
	                $addressAsArray = explode(',',$lastSale['DSENDECONSCOMAN']);
	                $streetAddress = $addressAsArray[0] !== "" ? $addressAsArray[0] : null;
	                $number = null;
	                if(count($addressAsArray) > 1){
	                    $number = $addressAsArray[1];
	                }

	                $orderDiscount = $this->util->float($lastSale['VRDESCOMANDA']);
	                $lastSaleData = array(
	                    'CDFILIAL'   => $lastSale['CDFILIAL'],
	                    'CDCLIENTE'       => $lastSale['CDCLIENTE'],
	                    'CDCONSUMIDOR'     => $lastSale['CDCONSUMIDOR'],
	                    'DTVENDA'         => $lastSale['DTVENDA'],
	                    'NRCOMANDA'         => $lastSale['NRCOMANDA'],
	                    'NRVENDAREST'     => $lastSale['NRVENDAREST'],
	                    'IDSTCOMANDA'      => self::status[$lastSale['IDSTCOMANDA']],
	                    'isOpen'           => true,
	                    'PRODUCTSDETAILS'  => array(),
	                    'RECEBIMENTOS'      => array(),
	                    'orderDescription' => array(
	                        'IDRETBALLOJA'     => $lastSale['IDRETBALLOJA'] == 'S' ? 'TGO' : 'DLV',
	                        'productsValue' => 0,
	                        'deliveryFee'   => $deliveryFee,
	                        'orderDiscount' => $orderDiscount,
	                        'totalValue'    => 0,
	                        'description'   => '',
	                        'orderCPF'      => null,
	                        'changeValue'   => 0,
	                        'deliveryAddress' => array(
	                            'streetAddress' => $streetAddress,
	                            'DSBAIRRO'  => $lastSale['DSBAIRRO'],
	                            'number'        => $number,
	                            'DSCOMPLENDCOCOM'  => $lastSale['DSCOMPLENDCOCOM'],
	                            'DSREFENDCONSCOM'     => $lastSale['DSREFENDCONSCOM'],
	                            'CDMUNICIBGE'          => $lastSale['CDMUNICIBGE'],
	                            'SGESTADO'         => $lastSale['SGESTADO'],
	                            'CDPAIS'       => $lastSale['CDPAIS'],
	                            'NRCEPCONSCOMAND'       => $lastSale['NRCEPCONSCOMAND'],
	                            'phone'         => null,
	                            'cellphone'     => null
	                        )
	                    )
	                );
	                // busca pelos produtos relacionados à venda
	                $return = self::getLastSaleProducts($lastSale['CDFILIAL'], $lastSale['NRVENDAREST'], $lastSale['NRCOMANDA']);
	                if (!$return['error']) {
	                    $products = $return['data'];
	                    // formata dados retornados para cada item da venda
	                    foreach ($products as $product) {
	                        if(!empty($product['NRINSCRCONS'])){
	                            $lastSaleData['orderDescription']['orderCPF'] = $product['NRINSCRCONS'];
	                        }
	                        $VRPRECCOMVEN = $this->util->float($product['VRPRECCOMVEN']);
	                        $VRDESCCOMVEN  = $this->util->float($product['VRDESCCOMVEN']);
	                        $QTPRODCOMVEN  = $this->util->float($product['QTPRODCOMVEN']);
	                        $NRPRODCOMVEN = $product['NRPRODCOMVEN'];

	                        $lastSaleData['orderDescription']['productsValue'] += $VRPRECCOMVEN*$QTPRODCOMVEN - $VRDESCCOMVEN;

	                        $productData = array(
	                            'CDPRODUTO'        => $product['CDPRODUTO'],
	                            'NMPRODUTO'        => $product['NMPRODUTO'],
	                            'IDIMPPRODUTO'     => $product['IDIMPPRODUTO'],
	                            'QTPRODCOMVEN'     => $QTPRODCOMVEN,
	                            'VRPRECITEM'       => $VRPRECCOMVEN,
	                            'VRDESCCOMVEN'     => $VRDESCCOMVEN,
	                            'OBSERVACOES'      => array()
	                        );

	                        $return = self::getLastSaleProductObservations($lastSale['CDFILIAL'], $lastSale['NRVENDAREST'], $lastSale['NRCOMANDA'], $NRPRODCOMVEN);
	                        if(!$return['error']){
	                            $observations = $return['data'];
	                            foreach($observations as $observation){
	                                $price = $observation['VRPRECCOMVEN'];
	                                $price = $price == null  ||  $price == 0 ? null : $this->util->float($price);

	                                if($price !== null){
	                                    $lastSaleData['orderDescription']['productsValue'] += $price;
	                                }
	                                $observationData = array(
	                                    'CDGRPOCOR'    => $observation['CDGRPOCOR'],
	                                    'CDOCORR'      => $observation['CDOCORR'],
	                                    'DSOCORR'      => $observation['DSOCORR'],
	                                    'quantity'     => 1,
	                                    'price'        => $price
	                                );

	                                array_push($productData['OBSERVACOES'], $observationData);
	                            }
	                        }
	                        array_push($lastSaleData['PRODUCTSDETAILS'], $productData);
	                    }
	                }

	                // busca pelos produtos combo relacionados à venda
	                $return = self::getLastSaleCombos($lastSale['CDFILIAL'], $lastSale['NRVENDAREST'], $lastSale['NRCOMANDA']);

	                if (!$return['error']) {
	                    $combos = $return['data'];
	                    foreach ($combos as $combo) {
	                        $comboData = array(
	                            'productCode'   => $combo['CDPRODPROMOCAO'],
	                            'productName'   => $combo['NMPRODUTO'],
	                            'quantity'      => 1,
	                            'unitValue'     => $this->util->float($combo['VRPRECCOMVEN']),
	                            'discount'      => $this->util->float($combo['VRDESCCOMVEN']),
	                            'observations'  => array(),
	                            'childItems'    => array()
	                        );

	                        $return = self::getLastSaleComboProducts($lastSale['CDFILIAL'], $lastSale['NRVENDAREST'], $lastSale['NRCOMANDA'], $combo['CDPRODPROMOCAO'], $combo['NRSEQPRODCOM']);
	                        if (!$return['error']) {
	                            $comboProducts = $return['data'];

	                            $comboName = array();
	                            $comboDescription = array();

	                            foreach ($comboProducts as $comboProduct) {
	                                if(!empty($comboProduct['NRINSCRCONS'])){
	                                    $lastSaleData['orderDescription']['orderCPF'] = $comboProduct['NRINSCRCONS'];
	                                }
	                                $VRPRECCOMVEN = $this->util->float($comboProduct['VRPRECCOMVEN']);
	                                $VRDESCCOMVEN  = $this->util->float($comboProduct['VRDESCCOMVEN']);
	                                $QTPRODCOMVEN  = $this->util->float($comboProduct['QTPRODCOMVEN']);
	                                $NRPRODCOMVEN = $comboProduct['NRPRODCOMVEN'];

	                                $lastSaleData['orderDescription']['productsValue'] += $VRPRECCOMVEN*$QTPRODCOMVEN - $VRDESCCOMVEN;

	                                $comboProductData = array(
	                                    'productCode'   => $comboProduct['CDPRODUTO'],
	                                    'productName'   => $comboProduct['NMPRODUTO'],
	                                    'quantity'      => $QTPRODCOMVEN,
	                                    'unitValue'     => $VRPRECCOMVEN,
	                                    'discount'      => $VRDESCCOMVEN,
	                                    'observations'  => array()
	                                );

	                                $return = self::getLastSaleProductObservations($lastSale['CDFILIAL'], $lastSale['NRVENDAREST'], $lastSale['NRCOMANDA'], $NRPRODCOMVEN);
	                                if(!$return['error']){
	                                    $observations = $return['data'];
	                                    foreach($observations as $observation){
	                                        $price = $observation['VRPRECCOMVEN'];
	                                        $price = $price == null  ||  $price == 0 ? null : $this->util->float($price);
	                                        if($price !== null){
	                                            $lastSaleData['orderDescription']['productsValue'] += $price;
	                                        }
	                                        $observationData = array(
	                                            'obsGroupCode' => $observation['CDGRPOCOR'],
	                                            'obsCode'      => $observation['CDOCORR'],
	                                            'obsName'      => $observation['DSOCORR'],
	                                            'quantity'     => 1,
	                                            'price'        => $price
	                                        );
	                                        array_push($comboProductData['observations'], $observationData);
	                                    }
	                                }

	                                array_push($comboData['childItems'], $comboProductData);
	                            }

	                            array_push($lastSaleData['PRODUCTSDETAILS'], $comboData);
	                        }
	                    }
	                }

	                // busca pelas formas de pagamento relacionadas à venda
	                $return = self::getLastSalePayments($lastSale['CDFILIAL'], $lastSale['NRVENDAREST']);
	                if (!$return['error']) {
	                    $payments = $return['data'];

	                    // formata dados retornados para cada pagamento
	                    foreach ($payments as $payment) {
	                        $paymentData = array(
	                            'CDTIPORECE' => $payment['CDTIPORECE'],
	                            'NMTIPORECE' => $payment['NMTIPORECE'],
	                            'VALOR'      => $this->util->float($payment['VRMOVIVEND'])
	                        );

	                        array_push($lastSaleData['RECEBIMENTOS'], $paymentData);
	                    }
	                }
	                $orderDescription = &$lastSaleData['orderDescription'];
	                $orderDescription['totalValue'] = $orderDescription['deliveryFee'] + $orderDescription['productsValue'] - $orderDescription['orderDiscount'];

	                $data = array($lastSaleData);
	                return array(
	                	'error' => false,
	                	'data' => $data
	                );
	            } else {
	            	return array(
	                	'error' => true,
	                	'message' => 'Nenhuma venda foi encontrada.'
	                );
	            }
	        } else {
	        	return array(
                	'error' => true,
                	'message' => $return['message']
                );
	        }
	    } catch (\Exception $e) {
	    	return array(
            	'error' => true,
            	'message' => 'Erro ao buscar última venda do consumidor. ' . $e->getMessage()
            );
	    }
	}

	public function getLastSale($NRCOMANDAEXT) {
        $params = array(
            ':NRCOMANDAEXT'    => $NRCOMANDAEXT
        );

        $errorMessage = 'Erro ao buscar a venda.';
        try {
            $result = $this->entityManager->getConnection()->fetchAssoc("GET_LAST_SALE", $params);

            if($result) {
                $return = array(
                    'error' => false,
                    'data'  => $result
                );
            } else {
                $return = array(
                    'error'   => true,
                    'message' => 'Venda não encontrada.'
                );
            }
        } catch (\Exception $e) {
            $return = array(
                'error'   => true,
                'message' => $errorMessage.'. Erro: '.$e->getMessage()
            );
        }

        return $return;
    }

    // --- BUSCA PRODUTOS
    public function getLastSaleProducts($CDFILIAL, $NRVENDAREST, $NRCOMANDA) {
        $params = array(
            ':CDFILIAL'    => $CDFILIAL,
            ':NRVENDAREST' => $NRVENDAREST,
            ':NRCOMANDA'   => $NRCOMANDA
        );

        try {
            $products = $this->entityManager->getConnection()->fetchAll("GET_LASTSALE_PRODUCTS", $params);

            $return = array(
                'error' => false,
                'data'  => $products
            );
        } catch (\Exception $e) {
            $return = array(
                'error'   => true,
                'message' => 'Erro ao buscar produtos relacionados à venda '.$NRCOMANDA.'. Erro: '.$e->getMessage()
            );
        }

        return $return;
    }

    public function getLastSaleProductObservations($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRPRODCOMVEN){
        $params = array(
            ':CDFILIAL'    => $CDFILIAL,
            ':NRVENDAREST' => $NRVENDAREST,
            ':NRCOMANDA'   => $NRCOMANDA,
            ':NRPRODCOMVEN' => $NRPRODCOMVEN
        );

        try {
            if ($this->util->databaseIsOracle()) {
                $observations = $this->entityManager->getConnection()->fetchAll("GET_LASTSALE_PRODUCT_OBSERVATIONS", $params);
            } else {
                $observations = $this->entityManager->getConnection()->fetchAll("GET_POSITION_PRODUCT_OBSERVATIONS", $params);
            }

            $return = array(
                'error' => false,
                'data'  => $observations
            );

        } catch (\Exception $e) {
            $return = array(
                'error'   => true,
                'message' => 'Erro ao buscar observacoes. Erro: '.$e->getMessage()
            );
        }

        return $return;
	}

    // --- BUSCA COMBOS
    public function getLastSaleCombos($CDFILIAL, $NRVENDAREST, $NRCOMANDA) {
        $params = array(
            ':CDFILIAL'    => $CDFILIAL,
            ':NRVENDAREST' => $NRVENDAREST,
            ':NRCOMANDA'   => $NRCOMANDA
        );

        try {
        	$combos = $this->entityManager->getConnection()->fetchAll("GET_LASTSALE_COMBOS", $params);

			$return = array(
                'error' => false,
                'data'  => $combos
            );
        } catch (\Exception $e) {
            $return = array(
                'error'   => true,
                'message' => 'Erro ao buscar combos relacionados à venda. Erro: '.$e->getMessage()
            );
        }

        return $return;
    }

    // ---- BUSCA PRODUTOS DOS COMBOS
    public function getLastSaleComboProducts($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $CDPRODPROMOCAO, $NRSEQPRODCOM) {
        $params = array(
            ':CDFILIAL'       => $CDFILIAL,
            ':NRVENDAREST'    => $NRVENDAREST,
            ':NRCOMANDA'      => $NRCOMANDA,
            ':CDPRODPROMOCAO' => $CDPRODPROMOCAO,
            ':NRSEQPRODCOM'   => $NRSEQPRODCOM
        );

        try {
            $comboProducts = $this->entityManager->getConnection()->fetchAll("GET_LASTSALE_COMBO_PRODUCTS", $params);

			$return = array(
                'error' => false,
                'data'  => $comboProducts
            );
        } catch (\Exception $e) {
            $return = array(
                'error'   => true,
                'message' => 'Erro ao buscar produtos dos combos relacionados à venda'.$e->getMessage()
            );
        }

        return $return;
    }

    // --- BUSCA PAGAMENTOS
    public function getLastSalePayments($CDFILIAL, $NRVENDAREST) {
        $params = array(
            ':CDFILIAL'    => $CDFILIAL,
            ':NRVENDAREST' => $NRVENDAREST
        );

        try {
        	$payments = $this->entityManager->getConnection()->fetchAll("GET_LASTSALE_PAYMENTS", $params);

			$return = array(
                'error' => false,
                'data'  => $payments
            );
        } catch (\Exception $e) {
            $return = array(
                'error'   => true,
                'message' => 'Erro ao buscar formas de pagamento relacionadas à venda '.$NRSEQVENDA.'. Erro: '.$e->getMessage()
            );
        }

        return $return;
    }

	public function liberaComanda($CDFILIAL, $dadosMesa, $arrayPosicoes, $CDOPERADOR, $CDLOJA = NULL, $IDORIGEMVENDA = NULL) {
		foreach ($dadosMesa as $mesaAtual) {
			if (!empty($arrayPosicoes)){
				foreach ($arrayPosicoes as $posicaoAtual){
					self::apagaTabelasProduto($CDFILIAL, $mesaAtual, $posicaoAtual);
		        }
		        $params = array(
			        'CDFILIAL' => $CDFILIAL,
			        'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
			        'NRCOMANDA' => $mesaAtual['NRCOMANDA'],
			        'NRLUGARMESA' => $arrayPosicoes
				);
				$type = array(
        			'NRLUGARMESA' => \Doctrine\DBAL\Connection::PARAM_STR_ARRAY
        		);
		        // limpa cliente/consumidor para as posições selecionada
				$this->entityManager->getConnection()->executeQuery("DELETA_POSVENDAREST_POS", $params, $type);

				$itcomandaven = $this->entityManager->getConnection()->fetchAll("BUSCA_ITCOMANDAVEN", $params);
				if (empty($itcomandaven)) {
					self::apagaTabelasComanda($CDFILIAL, $mesaAtual);
				} else {
					// pagamento por posição limpa desconto setado para mesa para evitar bugs
					$this->entityManager->getConnection()->executeQuery("UPDATE_MESA_CREDFIDELITY", $params);
				}
			} else {
                // Limpa os dados das comandas agrupadas no odhenPOS modo comanda.
                if ($IDORIGEMVENDA == 'CMD_PKC') {
                    $params = array (
                        'CDFILIAL' => $CDFILIAL,
                        'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
                        'NRCOMANDA' => $mesaAtual['NRCOMANDA'],
                        'DSCOMANDA' => $mesaAtual['DSCOMANDA'],
                        'CDLOJA'    => $CDLOJA
                    );
                    $comandasAgrupadas = $this->entityManager->getConnection()->fetchAll("SQL_GET_COMANDAS_AGRUPADAS", $params);
                    foreach ($comandasAgrupadas as $comanda) {
                        self::apagaTabelasProduto($CDFILIAL, $comanda, 'T');
                        self::apagaTabelasComanda($CDFILIAL, $comanda);                    
                    }
                }

                self::apagaTabelasProduto($CDFILIAL, $mesaAtual, 'T');
				self::apagaTabelasComanda($CDFILIAL, $mesaAtual);
			}
			$params = array(
		        'CDFILIAL' => $CDFILIAL,
		        'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
		        'CDOPERADOR' => $CDOPERADOR
			);
			$this->entityManager->getConnection()->executeQuery("DELETA_CONTROLPOSVEN", $params);
	    }
	}

	public function apagaTabelasProduto($CDFILIAL, $mesaAtual, $posicao){
		$params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
            'NRCOMANDA' => $mesaAtual['NRCOMANDA'],
            'NRLUGARMESA' => $posicao
		);

        $this->entityManager->getConnection()->executeQuery("DELETA_OBSITCOMANDAEST", $params);
		$this->entityManager->getConnection()->executeQuery("DELETA_OBSITCOMANDAVEN", $params);
        $this->entityManager->getConnection()->executeQuery("DELETA_ITCOMANDAEST", $params);
        $this->entityManager->getConnection()->executeQuery("DELETA_ITCOMANDAVEN", $params);
        $this->entityManager->getConnection()->executeQuery("DELETA_ITCOMANDAVENDES", $params);
	}

	public function apagaTabelasComanda($CDFILIAL, $mesaAtual){
		$params = array(
            'CDFILIAL' => $CDFILIAL,
            'NRVENDAREST' => $mesaAtual['NRVENDAREST'],
            'NRCOMANDA' => $mesaAtual['NRCOMANDA']
		);

        $this->entityManager->getConnection()->executeQuery("LIBERA_MESA", $params);
        $this->entityManager->getConnection()->executeQuery("DELETA_COMANDAVEN", $params);
        $this->entityManager->getConnection()->executeQuery("DELETA_VENDAREST", $params);
        $this->entityManager->getConnection()->executeQuery("DELETA_POSVENDAREST", $params);

        $paramsJuncao = array(
    		'CDFILIAL' => $CDFILIAL,
    		'NRMESA'   => $mesaAtual['NRMESA']
    	);

    	$juncao = $this->entityManager->getConnection()->fetchAssoc("GET_JUNCAOMESA", $paramsJuncao);
        if (!empty($juncao)) {
        	$paramsMesaJuncao = array(
        		'CDFILIAL'  => $CDFILIAL,
        		'NRJUNMESA' => $juncao['NRJUNMESA']
        	);

            // Libera as mesas agrupadas com a principal.
            $mesasAgrupadas = $this->entityManager->getConnection()->fetchAll("GET_NRMESAJUNCAO", $paramsMesaJuncao);
            foreach ($mesasAgrupadas as $mesa) {
                if ($mesa['NRVENDAREST'] == $mesaAtual['NRVENDAREST']) {
                    continue;
                }
                $params = array(
                    'CDFILIAL' => $CDFILIAL,
                    'NRVENDAREST' => $mesa['NRVENDAREST'],
                    'NRCOMANDA' => $mesa['NRCOMANDA']
                );
                $this->entityManager->getConnection()->executeQuery("LIBERA_MESA", $params);
                $this->entityManager->getConnection()->executeQuery("DELETA_COMANDAVEN", $params);
                $this->entityManager->getConnection()->executeQuery("DELETA_VENDAREST", $params);
            }

            // Desagrupa as mesas.
        	$this->entityManager->getConnection()->executeQuery("DELETA_JUNCAOMESA", $paramsMesaJuncao);
        	$this->entityManager->getConnection()->executeQuery("DELETA_MESAJUNCAO", $paramsMesaJuncao);
    	}
	}

	public function mudarStatusComanda($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $STATUS){
		$params = array(
			"CDFILIAL" 		=> $CDFILIAL,
			"NRVENDAREST" 	=> $NRVENDAREST,
			"NRCOMANDA"		=> $NRCOMANDA,
			"IDSTCOMANDA"	=> $STATUS
		);

		$this->entityManager->getConnection()->executeQuery("CHANGE_STATUS_COMANDA", $params);
	}

	public function liberaComandaDelivery($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $CDLOJA, $NRSEQVENDA, $CDCAIXA){
		$paramBalcLoja = array(
			'NRSEQVENDA' => $NRSEQVENDA,
			'CDFILIAL'	 => $CDFILIAL,
			'CDLOJA'	 => $CDLOJA
		);
		$result = $this->entityManager->getConnection()->fetchAssoc("GET_RETIRA_BALCAO", $paramBalcLoja);
		$IDRETBALLOJA = $result['IDRETBALLOJA'];
		$IDFINPEDAUTDLV = $result['IDFINPEDAUTDLV'];
		//se for retira balcao o pedido pula a etapa de controle de entrega
		if($IDRETBALLOJA == 'S' || $IDFINPEDAUTDLV == 'S'){
			$this->mudarStatusComanda($CDFILIAL, $NRVENDAREST, $NRCOMANDA, 'X');
		}else{
			$this->mudarStatusComanda($CDFILIAL, $NRVENDAREST, $NRCOMANDA, 'P');
		}

		$order =  array(
			'CDFILIAL'	 	 => $CDFILIAL,
			'CDLOJA'	 	 => $CDLOJA,
			'NRVENDAREST'	 => $NRVENDAREST,
			'NRSEQVENDA'	 => $NRSEQVENDA,
			'CDCAIXA'		 => $CDCAIXA
		);
		//para imprimir relatorio de entrega delivery, deve-se mandar um array de order
		return array($order);
	}

}
