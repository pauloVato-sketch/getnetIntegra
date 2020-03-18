<?php

namespace Service;

use \Util\Exception;

class Bill{

	protected $entityManager;
	protected $util;
	protected $date;
	protected $waiterMessage;


	protected $parametrosCatraca;
	protected $comanda;

	const UTILIZA_INTEGRACAO_CATRACA = 'S';
	const EVENTO_ABRE_MESA = 'C';

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Util\Util $util, \Util\Date $date, \Util\WaiterMessage $waiterMessage) {
		$this->entityManager     = $entityManager;
		$this->util              = $util;
		$this->date 			 = $date;
		$this->waiterMessage     = $waiterMessage;
	}

	private function preparaComandaCatraca($comanda){
		$this->comanda = str_pad($comanda, $this->parametrosCatraca['NRTAMARQXML'], "0", STR_PAD_LEFT);
	}

	private function validaDiretorioCatraca(){
		if(!is_dir($this->parametrosCatraca['DSDIRARQXMLINT']))
			throw new \Exception('Diretório dos arquivos XML está inválido.');
	}

	private function validaUtilizaIntegracaoCatraca(){
		if($this->parametrosCatraca['IDGERAARQXMLINT'] !== self::UTILIZA_INTEGRACAO_CATRACA)
			throw new \Exception('Filial não parametrizada para utilizar integração com Catraca.');
	}

	private function iniciaParametrosCatraca($chave){
		$session = $this->util->getSessionVars($chave);
		$params = array(
			'CDFILIAL' => $session['CDFILIAL']
		);
		$this->parametrosCatraca = $this->entityManager->getConnection()->fetchAssoc('SQL_PARAMETROS_CATRACA', $params);
	}

	public function criaXMLCatraca($chave, $comanda){
		try{
			//retorna os parametros necessarios para geracao do xml.
			$this->iniciaParametrosCatraca($chave);
			//verifica se a filial utiliza a integracao com a catraca.
			$this->validaUtilizaIntegracaoCatraca();
			//verifica se foi informado um diretorio para geracao do xml.
			$this->validaDiretorioCatraca();
			//completa a comanda com zero de acordo com a quantidade parametrizada.
			$this->preparaComandaCatraca($comanda);
			// cria o xml com a comanda, tipo de evento de abertura e o caminho parametrizado.
			$this->util->criaXMLCatraca($this->comanda, self::EVENTO_ABRE_MESA, $this->parametrosCatraca['DSDIRARQXMLINT']);
		}catch(\Exception $e){
			Exception::logException($e);
			return false;
		}
	}

	public function abreComanda($dataset){ //Detaset Params: chave, dscomanda, vendedor, CDCLIENTE, DSCONSUMIDOR
		$connection = null;
		try{
			$session = $this->util->getSessionVars($dataset['chave']);
			$filial = $session['CDFILIAL'];

			// verifica o parâmetro do caixa de permissão de abertura de comandas
			if (!empty($session['IDPERABERCOMCXA'])) {
				if ($session['IDPERABERCOMCXA'] === 'N') {
					return array('funcao' => '0', 'error' => '042');
				}
			}

			if (empty($dataset['nrMesa'])) $stMesa = $session['NRMESAPADRAO'];
			else $stMesa = $dataset['nrMesa'];

			if (empty($stMesa)) {
				if ($session['IDINFMESACOM'] == 'S') {
					return array('funcao' => '0', 'error' => '433');
				} else {
					return array('funcao' => '0', 'error' => '028');
				}
			}

			$this->util->newCode('VENDAREST'.$filial);
			$stPrxVendaRest = $this->util->getNewCode('VENDAREST'.$filial, 10);
			$this->util->newCode('COMANDAVEN'.$filial);
			$stComanda = $this->util->getNewCode('COMANDAVEN'.$filial, 10);

			// verifica se existe registro na tabela COMANDAFOS - comandas permitidas
			$utilizaComandaFos = $this->entityManager->getConnection()->fetchAssoc("SQL_UTILIZA_COMANDA_FOS",array($session['CDFILIAL'], $session['CDLOJA']));

			if ($session['IDCOMANDAAUT'] === 'S') {
				if ($utilizaComandaFos['NRREGISTROS'] > 0) {
					$dsComanda = self::UltimaComanda($dataset['chave']);
					if ($dsComanda === 'LIMITE') {
						return array('funcao' => '0', 'error' => '034');
					}
				} else {
					$dsComanda = $stComanda;
				}
			} else {
				$dsComanda = substr($dataset['dscomanda'],0,10);
			}

			// verifica se a comanda pode ser utilizada e está ativa
			if ($utilizaComandaFos['NRREGISTROS'] > 0) {
				$params = array(
					":CDFILIAL"     => $session['CDFILIAL'],
					":CDLOJA"       => $session['CDLOJA'],
					":DSCOMANDAFOS" => $dsComanda
				);

				$comandaFos = $this->entityManager->getConnection()->fetchAssoc("SQL_COMANDA_FOS", $params);
				if (empty($comandaFos)) {
					return array('funcao' => '0', 'error' => '031');
				}else if ($comandaFos['IDSITCOMANDAFOS'] === 'I') {
					return array('funcao' => '0', 'error' => '032');
				}
			}

			// valida a comanda
			$params = array(
				":CDFILIAL"  => $session['CDFILIAL'],
				":DSCOMANDA" => $dsComanda,
				":CDLOJA" => $session['CDLOJA']
			);
			$dadosComanda = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_COMANDA_ABERTURA", $params);
			if (!empty($dadosComanda)) {
				return array('funcao' => '0', 'error' => '427');
			}

			$cdCliente = $dataset['CDCLIENTE'];
			if (empty($cdCliente) || $cdCliente == 'X-X') {
				$cdCliente = $session['CDCLIENTE'];
			}

			if ($cdCliente == "X-X"){
				$result = $this->entityManager->getConnection()->fetchAssoc("SQL_GETCLIENTEPADRAO", array($session['CDFILIAL']));
				$cdCliente = $result[0];
			}

			$cdConsumidor = null;
			if ($dataset['CDCONSUMIDOR'] != '' && !empty($dataset['CDCONSUMIDOR'])) {
				$cdConsumidor = $dataset['CDCONSUMIDOR'];
			}

			// valida se o cliente existe no banco, caso contrário, usa o cliente padrão
			$result = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_CLIENTE", array($cdCliente));
			if (empty($result)) {
				$result = $this->entityManager->getConnection()->fetchAssoc("SQL_GETCLIENTEPADRAO", array($session['CDFILIAL']));
				$cdCliente = $result[0];
			}

			//Busca o vendedor informado, caso não existir, pega o logado
			if (!Empty($dataset['CDVENDEDOR'])) {
				$cdVendedor = $dataset['CDVENDEDOR'];
			} else {
				$cdVendedor = $session['CDVENDEDOR'];
			}

			/* Open connection and begin transaction. */
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			$params = array(
				'CDFILIAL' => $filial,
				'NRVENDAREST' => $stPrxVendaRest,
				'CDLOJA' => $session['CDLOJA'],
				'NRMESA' => $stMesa,
				'CDVENDEDOR' => $cdVendedor,
				'CDOPERADOR' => str_pad($session['CDOPERADOR'], 12, '0', STR_PAD_LEFT),
				'NRPESMESAVEN' => 1,
				'CDCLIENTE' => $cdCliente,
				'CDCONSUMIDOR' => $cdConsumidor,
				'NRORG' => $session['NRORG']
			);
			$this->entityManager->getConnection()->executeQuery("SQL_INSERE_VENDA_REST", $params);

			$params = array(
				'CDFILIAL'    		=>	$filial,
				'NRVENDAREST'    	=>	$stPrxVendaRest,
				'NRCOMANDA'    		=>	$stComanda,
				'CDLOJA'    		=>	$session['CDLOJA'],
				'DSCOMANDA'    		=>	$dsComanda,
				'IDSTCOMANDA'    	=>	'1',
				'VRACRCOMANDA'    	=>	0,
				'IDORGCMDVENDA'     =>	'CMD_MOB',
				'DSCONSUMIDOR'      =>  $dataset['DSCONSUMIDOR']
			);
            if ($session['IDUTCXDRIVETHU'] === 'S'){
                $params['IDORGCMDVENDA'] = 'CMD_THR';
            }
			$this->entityManager->getConnection()->executeQuery("SQL_ABRE_COMANDA", $params);

			$NMCONSUMIDOR = $dataset['DSCONSUMIDOR'];
			if (!empty($cdCliente) && !empty($cdConsumidor) && !empty($dataset['DSCONSUMIDOR'])) {
				$params = array (
					'CDCLIENTE'	   => $cdCliente,
					'CDCONSUMIDOR' => $cdConsumidor,
					'NRORG' => $session['NRORG']
				);
				$consumidorObj = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_NMCONSUMIDOR", $params);
				if (!empty($consumidorObj['NMCONSUMIDOR'])) {
					$NMCONSUMIDOR = $consumidorObj['NMCONSUMIDOR'];
				}
			}

			$connection->commit();
			$dados = array(
				'DSCOMANDA' => $dsComanda,
				'NRVENDAREST' => $stPrxVendaRest,
				'NRCOMANDA' => $stComanda,
				'NRMESA' => $stMesa,
				'CDCLIENTE' => $cdCliente,
				'CDCONSUMIDOR' => $cdConsumidor,
				'CDVENDEDOR' => $cdVendedor,
				'NMCONSUMIDOR' => $NMCONSUMIDOR,
				'LABELDSCOMANDA' => $dsComanda . $NMCONSUMIDOR
			);
			return array('funcao' => '1', 'dados' => $dados);

		} catch(\Exception $e){
			Exception::logException($e);
			if ($connection != null) {
				$connection->rollback();
			}
			throw new \Exception ($e->getMessage(),1);
		}
	}

	public function UltimaComanda($chave) {
		$session = $this->util->getSessionVars($chave);
		$date = $this->date->getDataAtual()->format('Ymd');
		$stCdContador = 'ULTIMACOMANDA'.$session['CDFILIAL'].$date;

		$params = array($stCdContador);
		$NovoCodigo = $this->entityManager->getConnection()->fetchAssoc("SQL_NOVO_CODIGO", $params);

		if (Empty($NovoCodigo)) {
			$params = array($session['CDFILIAL'], $session['CDLOJA'], '0000000000', $session['CDFILIAL']);
			$maxComandaFos = $this->entityManager->getConnection()->fetchAssoc("SQL_MAX_COMANDA_FOS", $params);

			if (!Empty($maxComandaFos['DSCOMANDAFOS'])) {
				$params = array($stCdContador, str_pad($maxComandaFos['DSCOMANDAFOS'], 10, '0', STR_PAD_LEFT));
				$this->entityManager->getConnection()->executeQuery("INSERT_NOVO_CODIGO", $params);
			}

		} else {
			$params = array($session['CDFILIAL'], $session['CDLOJA'], $NovoCodigo['NRSEQUENCIAL'], $session['CDFILIAL']);
			$maxComandaFos = $this->entityManager->getConnection()->fetchAssoc("SQL_MAX_COMANDA_FOS", $params);

			if (!Empty($maxComandaFos['DSCOMANDAFOS'])) {
				$params = array(str_pad($maxComandaFos['DSCOMANDAFOS'], 10, '0', STR_PAD_LEFT), $stCdContador);
				$this->entityManager->getConnection()->executeQuery("UPDATE_NOVO_CODIGO", $params);
			}
		}

		if (!Empty($maxComandaFos['DSCOMANDAFOS'])) {
			return str_pad($maxComandaFos['DSCOMANDAFOS'], 10, '0', STR_PAD_LEFT);
		} else {
			$params = array($session['CDFILIAL'], $session['CDLOJA']);
			$verificaNumeros = $this->entityManager->getConnection()->fetchAssoc("SQL_VERIFICA_NUMEROS", $params);

			if (Empty($verificaNumeros['DSCOMANDAFOS'])) {
				return 'LIMITE';
			} else {
				return $verificaNumeros['DSCOMANDAFOS'];
			}
		}

	}

	public  function buscaComandas($chave, $mesaComanda){

		$session = $this->util->getSessionVars($chave);
		$params = array(
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$mesaComanda,
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$session['CDFILIAL'],
			$session['CDLOJA'],
			$mesaComanda
		);

		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_COMANDAS", $params);
	}

	public function consultaComandas($dataset){
		$session = $this->util->getSessionVars($dataset['chave']);

		$params = array($session['CDFILIAL'], $session['CDLOJA']);
		$r_listaComanda =$this->entityManager->getConnection()->fetchAll("SQL_LISTA_COMANDA", $params);
		if (Empty($r_listaComanda)){
			return array('funcao' => '0', 'error' => '003');
		}

		$result = array();
		foreach ($r_listaComanda as $comanda){
			$params = array($session['CDFILIAL'], $session['CDLOJA']);
			$r_mesaComanda = $this->entityManager->getConnection()->fetchAssoc("SQL_LISTA_COMANDA", $params);
			$result[$comanda['DSCOMANDA']] = $comanda;
		}
		return array('funcao' => '1', 'dados' => $result);
	}

	public  function dadosComanda($cdfilial, $NRCOMANDA, $NRVENDAREST, $CDLOJA){
		$params = array(
			$cdfilial,
			$NRCOMANDA,
			$NRVENDAREST,
			$CDLOJA

		);
		$dadosComanda = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_COMANDA", $params);
		if ($dadosComanda == false) {
			throw new \Exception($this->waiterMessage->getMessage('458'), 1);
		}
		return $dadosComanda;
	}

	public function validaComanda($dataset){
		try{
			$session = $this->util->getSessionVars($dataset['chave']);

			$params = array(
				":CDFILIAL"  => $session['CDFILIAL'],
				":DSCOMANDA" => $dataset['dscomanda'],
				":CDLOJA" => $session['CDLOJA']
			);
			$retorno = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_COMANDA_ABERTURA", $params);
			if ($retorno == false) {
				$result = array('funcao' => '1', 'vazio' => 'S', 'dados' => []);
			} else {
				$result = array('funcao' => '1', 'vazio' => 'N', 'dados' => $retorno);
			}


			return $result;
		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception($this->waiterMessage->getMessage('428'), 1);
		}
	}

}