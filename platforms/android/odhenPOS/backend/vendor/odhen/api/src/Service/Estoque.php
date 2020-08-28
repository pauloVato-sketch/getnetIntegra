<?php
/*  
	author: Luis Philipe Fidelis 
	  date: March, 2016
    e-mail: luis.silva@teknisa.com

*/

use Zeedhi\Framework\DB\StoredProcedure\Param;
use Zeedhi\Framework\DB\StoredProcedure\StoredProcedure;

namespace Odhen\API\Service;

class Estoque {
	
	protected $entityManager;
	protected $util;

    public function __construct(
    	\Doctrine\ORM\EntityManager $entityManager,
    	\Odhen\API\Util\Util $util) {

	   	$this->entityManager = $entityManager;
	   	$this->util = $util;
	}

	const SYSTEM_USER = 'U';
    const ITEM_ABASTECIMENTO = 'A';
	const ITEM_OUTROS = 'O';

	public function updateProductStock(&$item, $nrorg, $cdfilial, $cdloja, $cdcaixa, $keys, $dtvenda, $saleAlmox, $saleLote, $saleSublote, $saleLcEstq, $cdoperador) {
	    if ($saleAlmox !== null) {
			if ($item['IDORIGESTQ'] == self::SYSTEM_USER) {
				//----- DADOS VINDO DO FRONTEND
			} else {
				self::buscaEstoqueDB($item, $nrorg, $cdfilial, $cdloja, $cdcaixa, $keys, $dtvenda, $cdoperador);
			}	
		} else {
			self::buscaEstoqueDB($item, $nrorg, $cdfilial, $cdloja, $cdcaixa, $keys, $dtvenda, $cdoperador);
		}
	}

	private function buscaEstoqueDB($item, $nrorg, $cdfilial, $cdloja, $cdcaixa, $keys, $dtvenda, $cdoperador) {
		$atualizaEstoque = false;
		$cdproduto 		 = $item['CDPRODESTO'];
		$nrlancestq 	 = $keys['NRLANCESTQ'];
		$stockData  	 = array(
			'CDALMOXARIFE' => null,
			'CDLOCALESTOQ' => ' ',
			'NRSUBLOTE'    => ' ',
			'NRLOTEESTQ'   => ' '
		);
		if (self::produtoControlaEstoque($cdproduto, $cdfilial, $nrorg)) {
			$paramsEstqFili = self::getParamsEstqFili($cdfilial, $nrorg);
			if ($paramsEstqFili['IDCTRLESTQ']) {
				$filiCtrAlmox = $paramsEstqFili['IDUTILALMOX'];
				$filiCtrLocal = $paramsEstqFili['IDUTILLCESTQ'];
				if($item['IDTIPOITEM'] == self::ITEM_ABASTECIMENTO){
					$nrbico = $item['NRBICO'];
					$dtabastec = $item['DATACONCLUSAOABASTECIMENTO'];
					$stockData['CDALMOXARIFE'] = self::getAlmoxAbastec($nrbico,$dtabastec);
				}
				if(($item['IDTIPOITEM'] == self::ITEM_OUTROS) || 
			       ($item['IDTIPOITEM'] == self::ITEM_ABASTECIMENTO && $stockData['CDALMOXARIFE'] == null)){
					$stockData['CDALMOXARIFE'] = self::getParamsEstqHierarq($nrorg,$cdfilial,$cdloja,$cdcaixa);
				}
				if($stockData['CDALMOXARIFE'] != null){
					if(!$filiCtrAlmox || ($filiCtrAlmox && $stockData['CDALMOXARIFE'] != ' ')){
						$nrlancestq = $keys['NRLANCESTQ'];
						$nrseqvenda = $keys['NRSEQVENDA'];
						$nrsequitvend = $keys['NRSEQUITVEND'];
						$vracritvend = isset($item['VRACRITVEND']) ? $item['VRACRITVEND'] : 0;
						$qtprodvend = $item['QTPRODVEND'];
						$vrunitvend = $item['VRUNITVEND'];
						$vrdescitem = $item['VRDESITVEND'];
						$vrlancto = ($qtprodvend * $vrunitvend) - $vrdescitem + $vracritvend;
						$vrbruto = $vrlancto;
						$idtipomovi = '4';
						self::geraItLanctoEstq($cdfilial, $cdfilial, $nrorg, $cdoperador, $nrlancestq, $dtvenda, $stockData, $cdproduto, $vrlancto, $vrbruto, $qtprodvend, $idtipomovi);
						$paramsUpdateItvenda = array(
							'NRSEQVENDA'   => $nrseqvenda,
							'NRSEQUITVEND' => $nrsequitvend,
							'NRORG'		   => $nrorg,
							'CDFILIAL'	   => $cdfilial,
							'CDCAIXA'	   => $cdcaixa,
							'NRLANCESTQ'   => $nrlancestq				
						);
						$this->entityManager->getConnection()->executeQuery("UPDATE_ITEMVENDA_ESTQ", $paramsUpdateItvenda);
					}
				}
			}
		}
	}

	private function produtoControlaEstoque($cdproduto, $cdfilial, $nrorg) {
		// --- PRODFILI -> IDCNTRESTOQ 
		$idcntrestoq = false;
		$params = array(
			'CDPRODUTO' => $cdproduto,
			'CDFILIAL'  => $cdfilial,
			'NRORG'		=> $nrorg
		);
		$result = $this->entityManager->getConnection()->fetchAll("GET_PRODUTO_CONTRESTOQ",$params);
		if (count($result) > 0) {
			$idcntrestoq = $result[0]['IDCNTRESTOQ'] == 'S';
		}
		return $idcntrestoq;
	}	

	private function getParamsEstqFili($cdfilial, $nrorg) {
		$paramsEstq = array();
		$params = array(
			'CDFILIAL' => $cdfilial,
			'NRORG'	   => $nrorg	
		);
		$paramsEstq = array(
			'IDUTILLOTE' => false,
			'IDUTILLCESTQ' => false,
			'IDCTRLESTQ' => false,
			'IDUTILALMOX' => false
		);
		$result = $this->entityManager->getConnection()->fetchAll("GET_PARAMSESTQ_FILIAL", $params);
		if (count($result) > 0) {
			$paramsEstq = $result[0];
			$paramsEstq['IDUTILLOTE']   = $paramsEstq['IDUTILLOTE']   == 'S';
			$paramsEstq['IDUTILLCESTQ'] = $paramsEstq['IDUTILLCESTQ'] == 'S';
			$paramsEstq['IDCTRLESTQ'] 	= $paramsEstq['IDCTRLESTQ']   == 'S';
			$paramsEstq['IDUTILALMOX']  = $paramsEstq['IDUTILALMOX']  == 'S';
		}
		return $paramsEstq;
	}
	 
	private function getAlmoxAbastec($nrbico, $dtabastec) {
		$cdalmoxarife = null;
		$params = array(
			'NRSEQBICO'		  => $nrbico,
            'DTABASTECIMENTO' => $dtabastec
		);
		$result = $this->entityManager->getConnection()->fetchAll("GET_TANQUEBICOH_BY_BICO",$params);
		if(count($result) > 0){
			$cdalmoxarife = $result[0]['CDALMOXARIFADO'];
		}
		return $cdalmoxarife;
	}	

	private function getParamsEstqHierarq($nrorg, $cdfilial, $cdloja, $cdcaixa) {
		$cdalmoxarife = null;
		$params = array(
			'NRORG'    => $nrorg,
			'CDFILIAL' => $cdfilial,
			'CDLOJA'   => $cdloja,
			'CDCAIXA'  => $cdcaixa		
		);
		$result = $this->entityManager->getConnection()->fetchAll("GET_RELACALMOXESTRUT_PDV", $params);
		if (count($result) > 0) {
			$cdalmoxarife = $result[0]['CDALMOXARIFE'];
		} else {
			unset($params['CDCAIXA']);
			$result = $this->entityManager->getConnection()->fetchAll("GET_RELACALMOXESTRUT_LOJA", $params);
			if (count($result) > 0) {
				$cdalmoxarife = $result[0]['CDALMOXARIFE'];
			} else {
				unset($params['CDLOJA']);
				$result = $this->entityManager->getConnection()->fetchAll("GET_RELACALMOXESTRUT_FILIAL", $params);
				if (count($result) > 0) {
					$cdalmoxarife = $result[0]['CDALMOXARIFE'];
				}
			}
		}
		return $cdalmoxarife;
	}

	private function prodsVendCntrlEst($nrorg, $cdfilial, $arrProdutos) {
		$idcntrestoq = false;
		$params = array($arrProdutos, $nrorg, $cdfilial);
		$paramsType = array(\Doctrine\DBAL\Connection::PARAM_STR_ARRAY,\PDO::PARAM_STR, \PDO::PARAM_STR);
		$query = "GET_ITEMS_VENDA_CTRLESTOQ";
		$statement = $this->entityManager->getConnection()->executeQuery($query, $params, $paramsType);
		$result = $statement->fetchAll();
		if(count($result) > 0 ){
			$idcntrestoq = $result[0]['IDCNTRESTOQ'] == 'S';
		}
		return $idcntrestoq;
	}

	private function geraLancamentoEstq($cdfilial, $nrorg, $cdoperador, $dtlancestq, $cdfilimovi, $dslancestq, $idtplancto) {
		$cdcontadorLanctEst = 'LANCTOEST' . $cdfilial;
		$conn      		    = $this->entityManager->getConnection();
		$nrlancestq 		= $this->util->geraCodigo($conn, $cdcontadorLanctEst, $nrorg, 1, 10);
		$paramsLanctoestoq  = array(
			'CDFILIAL'	 => $cdfilial,
			'NRORG'   	 => $nrorg,
			'CDFILIMOVI' => $cdfilimovi,
			'IDTPLANCTO' => $idtplancto,
			'DTLANCESTQ' => $dtlancestq,
			'CDOPERADOR' => $cdoperador,
			'NRLANCESTQ' => $nrlancestq,
			'DSLANCESTQ' => $dslancestq
		);
		
		$types = array (
			'DTLANCESTQ' => \Doctrine\DBAL\TypeS\Type::DATE
		);
		$fields = self::auditoryFields($nrorg, $cdoperador);
		$paramsLanctoestoq = array_merge($paramsLanctoestoq, $fields);
		$this->entityManager->getConnection()->executeQuery("INSERT_LANCTOESTOQ", $paramsLanctoestoq, $types);
		return $nrlancestq;
	}

	private function auditoryFields($nrorg, $cdoperador) {
		return $params = array(
			'NRORGINCLUSAO' => $nrorg,
			'NRORGULTATU' => $nrorg,
			'CDOPERINCLUSAO' => $cdoperador,
			'CDOPERULTATU' => $cdoperador,
			'IDATIVO' => 'S'
		);
	}

	private function geraItLanctoEstq($cdfilimovi, $cdfilial, $nrorg, $cdoperador, $nrlancestq, $dtlancto, $stockData, $cdproduto, $vrlancto, $vrbruto, $qtlancto, $idtipomovi) {
		$vrlanctobrut = $vrbruto;
		$vrtotlancto  = $vrbruto;
		$vrlanctoest  = $vrlancto;
		$qttotlancto  = $qtlancto;
		$qtlanctoest  = $qtlancto;
		$vrunilancto  = $vrtotlancto / $qttotlancto;
		$cdalmoxarife = $stockData['CDALMOXARIFE'];
		$cdlocalestoq = $stockData['CDLOCALESTOQ'];
		$nrloteestq   = $stockData['NRLOTEESTQ'];
		$nrsublote    = $stockData['NRSUBLOTE']; 
		$nrsequitem   = self::getSequencialItlancto($nrorg, $cdfilial, $nrlancestq);
		$paramsItlanctoest = array(
			'CDFILIAL'     => $cdfilial,
			'NRSEQUITEM'   => $nrsequitem,
			'NRORG'		   => $nrorg,
			'NRLANCESTQ'   => $nrlancestq,
			'CDPRODUTO'    => $cdproduto,
			'QTTOTLANCTO'  => self::parseFloatToSave($qttotlancto),
			'VRTOTLANCTO'  => self::parseFloatToSave($vrtotlancto), 
			'VRUNILANCTO'  => self::parseFloatToSave($vrunilancto),
			'IDTIPOMOVI'   => $idtipomovi,
			'CDFILIMOVI'   => $cdfilimovi,
			'CDALMOXARIFE' => $cdalmoxarife,
			'CDLOCALESTOQ' => $cdlocalestoq,
			'NRLOTEESTQ'   => $nrloteestq,
			'NRSUBLOTE'	   => $nrsublote,
			'DTLANCMOVI'   => $dtlancto,
			'CDPRODMOVI'   => $cdproduto,
			'QTLANCTOEST'  => self::parseFloatToSave($qtlanctoest),
			'VRLANCTOBRUT' => self::parseFloatToSave($vrlanctobrut),
			'VRLANCTOEST'  => self::parseFloatToSave($vrlanctoest)
		);
		
		$types = array (
			'DTLANCMOVI' => \Doctrine\DBAL\TypeS\Type::DATE
		);
		$fields = self::auditoryFields($nrorg, $cdoperador);
		$paramsItlanctoest = array_merge($paramsItlanctoest, $fields);
		
		$this->entityManager->getConnection()->executeQuery("INSERT_ITLANCTOEST", $paramsItlanctoest, $types);
	}

	private function parseFloatToSave($campo) {
		return str_replace('.', ',', strval($campo));
	}

	private function getSequencialItlancto($nrorg, $cdfilial, $nrlancestq) {
		$params = array(
			'CDFILIAL'   => $cdfilial,
			'NRORG'      => $nrorg,
			'NRLANCESTQ' => $nrlancestq 
		);
		$resp = $this->entityManager->getConnection()->fetchAll("GET_MAX_ITLANCTOEST", $params);
		$nrsequitem = '0001';
		if(count($resp) > 0){
			if($resp[0]['PROXNRSEQUITEM'] !== null){
				$nrsequitem = $resp[0]['PROXNRSEQUITEM'];
			}	
		}
		return $nrsequitem;
	}

	private function atualizaEstoque($cdfilimovi, $cdfilial, $nrlancestq, $dtini, $dtfin) {
		$connection = $this->entityManager->getConnection();
		$procedure = new StoredProcedure($connection, 'ATUALIZA_ESTOQUE');
		$procedure->addParam(new Param('P_FILIMOVI'  , Param::PARAM_INPUT, $cdfilimovi, Param::PARAM_TYPE_STR));
        $procedure->addParam(new Param('P_DTINICIAL' , Param::PARAM_INPUT, $dtini     , Param::PARAM_TYPE_STR));
        $procedure->addParam(new Param('P_DTFINAL'   , Param::PARAM_INPUT, $dtfin     , Param::PARAM_TYPE_STR));
        $procedure->addParam(new Param('P_FILILANCTO', Param::PARAM_INPUT, $cdfilial  , Param::PARAM_TYPE_STR));
       	$procedure->addParam(new Param('P_NRLANCTO'  , Param::PARAM_INPUT, $nrlancestq, Param::PARAM_TYPE_STR));
        $procedure->execute();
    }

}