<?php

namespace Service;

use \Util\Exception;

class Params {

	protected $entityManager;
	protected $util;
	protected $date;
 	protected $parametrosAPI;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager,
	 	\Util\Util $util,
	 	\Util\Date $date,
 		\Odhen\API\Service\Parametros $parametrosAPI
	){
		$this->entityManager = $entityManager;
		$this->util          = $util;
		$this->date          = $date;
		$this->parametrosAPI = $parametrosAPI;
	}

	public function carregaDados($dataset){

		$session = $this->util->getSessionVars($dataset['chave']);

		$result = '';
		$dados = array();
		$dados['ambientes'] = array();
		$dados['clientes'] = array();
		$dados['consumidores'] = array();
		$dados['vendedores'] = array();
		$dados['grupos'] = array();
		$dados['impressoras'] = array();
		$dados['mensProducao'] = array();
		$dados['mensObservacao'] = array();
		$dados['mensCancelamento'] = array();
		$dados['recebimentos'] = array();
		$dados['grupoRecebimentos'] = array();
		$dados['mensDescontoObs'] = array();

		// carrega clientes para abertura de mesa/comanda
		$r_clientes = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_CLIENTES", array($session['CDFILIAL']));
		if (!empty($r_clientes)){
			foreach ($r_clientes as &$cliente){
				$dados['clientes'][$cliente['CDCLIENTE']] = array(
					'CDCLIENTE' => $cliente['CDCLIENTE'],
					'CDFILTABPREC' => $cliente['CDFILTABPREC'],
					'CDTABEPREC' => $cliente['CDTABEPREC'],
					'NMRAZSOCCLIE' => $cliente['NMRAZSOCCLIE'],
					'NRINSJURCLIE' => $cliente['NRINSJURCLIE'],
				);

				$dados['consumidores'][$cliente['CDCLIENTE']] = array();
			}
		}

		// carrega vendedores para abertura de mesa/comanda
		$r_vendedores = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_VENDEDORES", array($session['CDFILIAL']));
		if (!empty($r_vendedores)){
			foreach ($r_vendedores as &$vendedor){
				array_push($dados['vendedores'],
					array(
						'CDVENDEDOR' => $vendedor['CDVENDEDOR'],
						'NMFANVEN'    => $vendedor['NMFANVEN']
					)
				);
			}
		}

		$responseAPI = $this->parametrosAPI->carregaDados($session['CDFILIAL'], $session['CDCAIXA'], $session['CDVENDEDOR'], $session['CDOPERADOR']);
		if ($responseAPI['error'] == false) {
			$responseAPI = $responseAPI['dados'];

			$dados['ambientes'] = $responseAPI['ambientes'];
			$dados['grupos'] = $responseAPI['cardapio'];
			$dados['observacoes'] = $responseAPI['observacoes'];
			$dados['recebimentos'] = $responseAPI['recebimentos'];
			$dados['grupoRecebimentos'] = $responseAPI['grupoRecebimentos'];
            $dados['horarioDePrecos'] = $responseAPI['horarioDePrecos'];
            $dados['smartPromoProducts'] = $responseAPI['smartPromoProducts'];

			$dados['parametros'] = array(
				'couvert'         => $session['IDCOUVERART'],
				'controlaPos'     => $session['IDLUGARMESA'],
				'consumacao'      => $session['IDCONSUMAMIN'],
				'taxaServico'     => $session['IDCOMISVENDA'],
				'comandaAuto'     => $session['IDCOMANDAAUT'],
				'NRMESAPADRAO'    => $session['NRMESAPADRAO'],
				'CDVENDPADRAO'    => $session['CDVENDPADRAO'],
				'IDINFVENDCOM'    => $session['IDINFVENDCOM'],
				'NRATRAPADRAO'    => $session['NRATRAPADRAO'],
				'valorConsumacao' => $session['PRECOCONSUMA'],
				'VRCOMISVENDA'    => $session['VRCOMISVENDA'],
				'VRCOMISVENDA2'   => $session['VRCOMISVENDA2'],
				'VRCOMISVENDA3'   => $session['VRCOMISVENDA3'],
				'VRMAXDESCONTO'	  => $session['VRMAXDESCONTO'],
				'IDIMPPEDPROD'    => $session['IDIMPPEDPROD']
			);

			$dados['mensProducao'] =  $this->MensagemProducao($dataset['chave']);
			$dados['mensObservacao'] =  $this->BuscaObservacao($dataset['chave']);
			$dados['mensCancelamento'] =  $this->BuscaObservacaoCancelamento($dataset['chave']);
			$dados['impressoras'] =  $this->BuscaImpressoras($dataset['chave']);
			$dados['funcao'] = '1';
			$dados['mensDescontoObs'] =  $this->BuscaObservacaoDesc($dataset['chave']);

			return array(
				'error' => false,
				'dados' => $dados
			);
		} else {
			return $responseAPI;
		}
	}

	private function MensagemProducao($chave){
		$session = $this->util->getSessionVars($chave);
		$params = array($session['CDFILIAL'], $session['CDLOJA']);
		$r_MensProducao = $this->entityManager->getConnection()->fetchAll("SQL_MENS_PRODUCAO", $params);

		$mensagem = array();
		foreach ($r_MensProducao as &$msg){
			$mensagem[$msg['CDOCORR']] = array(
				'codigo' => $msg['CDOCORR'],
				'mensagem' => $msg['DSOCORR']
			);
		}
		return $mensagem;
	}

	private function BuscaObservacao($chave){
		$session = $this->util->getSessionVars($chave);
		$params = array($session['CDFILIAL'], $session['CDLOJA']);
		$r_Observacao = $this->entityManager->getConnection()->fetchAll("SQL_OBSERVACAO", $params);

		$observacao = array();
		foreach ($r_Observacao as &$obs){
			$observacao[$obs['CDOCORR']] = array(
				'codigo' => $obs['CDOCORR'],
				'mensagem' => $obs['DSOCORR']
			);
		}
	}

	private function BuscaObservacaoCancelamento ($chave) {
		$session = $this->util->getSessionVars($chave);
		$params = array($session['CDFILIAL'], $session['CDLOJA']);
		$r_Observacao = $this->entityManager->getConnection()->fetchAll("SQL_OBSERVACAO_CAN", $params);

		$observacao = array();
		foreach ($r_Observacao as &$obs){
			$observacao[$obs['CDOCORR']] = array(
				'grupo'    => $obs['CDGRPOCOR'],
				'codigo'   => $obs['CDOCORR'],
				'mensagem' => $obs['DSOCORR']
			);
		}
		return $observacao;
	}

	private function BuscaImpressoras($chave){
		$session = $this->util->getSessionVars($chave);
		$params = array($session['CDFILIAL'], $session['CDLOJA']);
		$r_Printers = $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_IMPRESSORAS", $params);

		$impressoras = array();
		foreach ($r_Printers as &$imprs){
			$impressoras[$imprs['NRSEQIMPRLOJA']] = array(
				'codigo' => $imprs['NRSEQIMPRLOJA'],
				'impressora' => $imprs['NMIMPRLOJA']
			);
		}

		return $impressoras;
	}

	public function getConsumersByClient($chave, $CDCLIENTE){
		try {
			$session = $this->entityManager->getConnection()->getSessionVars($chave);
			$CDFILIAL = $session['CDFILIAL'];

			$consumerStyle = $this->entityManager->getConnection()->fetchAll("SQL_GET_CONSUMER_PARAMS", array($CDFILIAL));

			if ($consumerStyle[0]['IDEXCONSATFIL'] == 'S' && $consumerStyle[0]['IDEXCONSATGER'] == 'S'){
				$params = array($CDFILIAL, $CDCLIENTE);
				$consumers = $this->entityManager->getConnection()->fetchAll("SQL_GET_CONSUMERS_BY_CLIENT", $params);
			}
			else {
				$params = array($CDFILIAL, $CDCLIENTE, $CDFILIAL, $CDCLIENTE);
				$consumers = $this->entityManager->getConnection()->fetchAll("SQL_GET_ALL_CONSUMERS_BY_CLIENT", $params);
			}

			return $consumers;
		} catch (\Exception $e) {
			Exception::logException($e);
			return null;
		}
	}

	public function getBalanceDetails($chave, $CDCLIENTE, $CDCONSUMIDOR){
		try {
			$session = $this->util->getSessionVars($chave);
			$CDFILIAL = $session['CDFILIAL'];
			$params = array($CDCLIENTE, $CDCONSUMIDOR);
			$balance = $this->entityManager->getConnection()->fetchAll("SQL_GET_CONSUMER_BALANCE", $params);
			$check = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_IDVERSALDCON", $params);
			return array(
				'BALANCE' => empty($balance) ? "0.00" : $balance[0]['SALDO'],
				'IDVERSALDCON' => $check['IDVERSALDCON']
			);
		} catch (\Exception $e) {
			Exception::logException($e);
			return null;
		}
	}

	private function runGetDelayedProducts($params) {
		return $this->entityManager->getConnection()->fetchAll("SQL_GET_DELAYED_PRODUCTS", $params);
    }

	public function getDelayedProducts($chave, $NRVENDAREST, $NRCOMANDA) {
        try {
            $session = $this->util->getSessionVars($chave);
            $CDFILIAL = $session['CDFILIAL'];

            $params = array(
                'CDFILIAL' => $CDFILIAL,
                'NRVENDAREST' => $NRVENDAREST,
                'NRCOMANDA' => $NRCOMANDA
            );

            $tries = 0;
            $succeeded = false;
            $delayedProducts = array();
            while (!$succeeded && $tries < 3) {
            	try {
	           		$delayedProducts = $this->runGetDelayedProducts($params);
            		$succeeded = true;
		    	} catch(\Exception $e) {
					sleep(2);
					$succeeded = false;
		    		$tries++;
		    	}
            }
            foreach ($delayedProducts as &$product) {
                $product['POSITION'] = "posição ".intval($product['POSITION']);
                if (!is_null($product['CDPRODPROMOCAO'])) {
                    $prodData = array(
                        'CDPRODUTO' => $product['CDPRODPROMOCAO']
                    );

                    $promoName = $this->entityManager->getConnection()->fetchAll("SQL_GET_SMARTPROMO", $prodData)[0]['NMPRODUTO'];
                    $product['NMPRODPROMOCAO'] = $promoName;
                } else {
                    $product['NMPRODPROMOCAO'] = '-';
                }
            }
            return $delayedProducts;
        } catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function getPagedConsumers($CDCLIENTE, $CDCONSUMIDOR, $FIRST, $LAST){
    	$session = $this->util->getSessionVars(null);

		$allConsumer = ($session['IDEXCONSATGER'] != 'S' || $session['IDEXCONSATFIL'] != 'S') ? 'T' : 'X';
		$CONSBLOBQ = 'N';
		if (!empty($CDCONSUMIDOR)){
			$CONSBLOBQ = $CDCONSUMIDOR[0] != '%' ? 'S' : 'N';
		}
		$params = array(
        	'CDFILIAL' => $session['CDFILIAL'],
        	'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => strtoupper($CDCONSUMIDOR),
            'FIRST' => $FIRST,
            'LAST' => $LAST,
            'ALLCONSU' => $allConsumer,
            'CONSBLOBQ' => $CONSBLOBQ
        );
        return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_CONSUMIDORES", $params);
    }

    public function getFamilies($CDFILIAL){
        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_FAMILIAS", $params);
    }

	public function dadosCaixa($chave){
		$session = $this->util->getSessionVars($chave);
		$cdfilial = $session["CDFILIAL"];
		$cdcaixa  = $session["CDCAIXA"];
		return $this->entityManager->getConnection()->fetchAll("SQL_DADOS_CAIXA", array($cdfilial, $cdcaixa));
	}

	public function getParamsVendaOnline(){
		return $this->entityManager->getConnection()->fetchAll("GET_ESITEF_DETAILS");
	}

	public function buscaCardapio($chave){
		$session = $this->util->getSessionVars('chave');

        $resultTabelaPadrao = $this->parametrosAPI->buscaTabelaPadrao($session['CDFILIAL'], $session['CDCLIENTE'], $session['CDLOJA'], array());
        $precosIndexadosPorProduto = $resultTabelaPadrao['precosIndexadosPorProduto'];
        $horarioDePrecos = $resultTabelaPadrao['horarioDePrecos'];

        $observacoesIndexadasPorProduto = $this->parametrosAPI->montaObservacoes($session['CDFILIAL'], $session['CDLOJA']);

        $cardapio = $this->parametrosAPI->montaCardapio($session['CDFILIAL'], $session['NRCONFTELA'], $session['CDLOJA'], $session['CDCLIENTE'], $precosIndexadosPorProduto, $observacoesIndexadasPorProduto);
        $smartPromoProducts = $this->parametrosAPI->montaPromocoes($session['CDFILIAL'], $session['NRCONFTELA'], $session['CDLOJA'], $precosIndexadosPorProduto, $observacoesIndexadasPorProduto);

        return array(
            'cardapio' => $cardapio['cardapio'],
            'horarioDePrecos' => $horarioDePrecos,
            'smartPromoProducts' => $smartPromoProducts
        );
    }

	private function BuscaObservacaoDesc($chave){
		$session = $this->util->getSessionVars($chave);
		$params = array($session['CDFILIAL'], $session['CDLOJA']);

		return $this->entityManager->getConnection()->fetchAll("GET_DISCOUNT_OBSERVATIONS", $params);
	}

}