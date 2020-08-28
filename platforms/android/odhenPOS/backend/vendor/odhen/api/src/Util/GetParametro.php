<?php

namespace Odhen\API\Util;

class GetParametro { 

	protected $entityManager;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager){
	   	$this->entityManager = $entityManager;
	}

	public function getParametroByHierarquia($codigo, $filter){
    	$array = array('CODPARAMETRO' =>$codigo);
		$ParamValue = null;
		// busca hierarquia definida para o parametro
		$getHierarquiaParam = $this->entityManager->getConnection()->fetchAll("GET_HIERARQUIA_BYCODPARAM", $array);
		if(empty($getHierarquiaParam)){ 

			// caso não encontre, busca hierarquia do parent para encontrar parametro
			$getParent = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYCODPARAMETRO", $array);
			
			if($getParent){
				$nrparent = $getParent[0]['NRPARENT'];
				$array = array('NRPARAMETRO' => $nrparent);
				$getHierarquiaParam = $this->entityManager->getConnection()->fetchAll("GET_HIERARQUIA_BYNRPARAMETRO", $array);
				$buildHierarquia =  self::buildHierarquia($getHierarquiaParam);
				$ParamValue      =  self::getParamValueByHierarquia($buildHierarquia, $codigo, $filter);
			}
		}
   		return $ParamValue;
    }

    public function getParamValueByHierarquia($buildHierarquia, $codigo, $filter){
    	$valorParam = null;
    	for ($i=count($buildHierarquia)-1; $i>=0; $i--){ 
    		$hierarquia = $buildHierarquia[$i];
    		$paramValue = self::getParametro($codigo, $filter, $hierarquia);
    		if(!empty($paramValue)){
    			$valorParam = isset($paramValue[0]) ? $paramValue[0]['VALORPARAMETRO'] : $paramValue['VALORPARAMETRO'];
    			if($valorParam != NULL){
    				return $valorParam;	
    			}
    		}
    	}
    	return $valorParam;
    }


	public function getParametro($codigo, $filter, $hierarquia){
    	$filter = self::preparaFilter($filter);
		if($hierarquia == 'GERAL'){
			$parametros= self::getParametroGeral($codigo, $filter);
		}elseif($hierarquia == 'FILIAL'){
			$parametros= self::getParametroFilial($codigo, $filter);
		}elseif($hierarquia == 'FILIAL/LOJA'){
			$parametros= self::getParametroFilialLoja($codigo, $filter);
		}elseif($hierarquia == 'FILIAL/LOJA/PDV'){
			$parametros= self::getParametroFilialLojaPDV($codigo, $filter);
		}elseif($hierarquia == 'TIPORECE'){
			$parametros= self::getParametroTiporece($codigo, $filter);
		}elseif($hierarquia == 'FILIAL/LOJA/TIPORECE'){
			$parametros= self::getParametroFilialLojaTiporece($codigo, $filter);
		}else{
			$parametros= self::getParametroGeral($codigo, $filter);
		}
		return $parametros;
	}

	public function preparaFilter($filter){
		$filter['CDFILIAL']= isset($filter['CDFILIAL']) ? $filter['CDFILIAL'] : '0000';
		$filter['CDLOJA']  = isset($filter['CDLOJA']) ? $filter['CDLOJA'] : '00';
		$filter['CDCAIXA'] = isset($filter['CDCAIXA']) ? $filter['CDCAIXA'] : '000';
		$filter['NRORG']   = isset($filter['NRORG']) ? $filter['NRORG'] : '0000';
		$filter['CDTIPORECE']   = isset($filter['CDTIPORECE']) ? $filter['CDTIPORECE'] : '0000';
		return $filter;
	}

    public function getParametroGeral($codigo, $filter){
    	$valorParam = array();
    	
    	$arrayParam = array('NRORG'=>$filter['NRORG'], 'CODPARAMETRO'=>$codigo);
    	
    	try {
			$valorParam = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYRELACIONAMENTOGERAL", $arrayParam);
		}catch (\Exception $e){
			var_dump($e);
		}	
		return $valorParam;
    }

    public function getParametroFilial($codigo, $filter){
    	$valorParam = array();
    	$arrayParam = array('CODPARAMETRO'=>$codigo, 'NRORG'=>$filter['NRORG'], 'DESCRELACIONAMENTO'=>'CDFILIAL', 'VALOR'=>$filter['CDFILIAL']);
    	try {
			$valorParam = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYDESCRELAC_VALOR", $arrayParam);
			if($valorParam){
				for($i=0; $i<count($valorParam); $i++){
					$nrparametrovalor = $valorParam[$i]['NRPARAMETROVALOR'];
					$arrayParam = array('NRPARAMETROVALOR'=>$nrparametrovalor, 'CODPARAMETRO'=>$codigo,
				                                   'NRORG'=>$filter['NRORG'],  'DESCRELACIONAMENTO'=>'CDFILIAL');
					/*Busca outros relacionamentos para o nrparametrovalor.
					  Caso não encontre, esse nrprametrovalor esta associado só a nível de filial. Caso encontre outros
					  relacionamentos continua a busca.
					*/
					$result = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR", $arrayParam);
					if(empty($result)){ //
						return $retorno[] = $valorParam[$i];
					}
				}
			}
			return $valorParam;
		}catch (\Exception $e){
			var_dump($e);
		}	
    }
    
	public function getIDHABCAIXAVENDAFromCaixa($CDFILIAL, $CDLOJA, $CDCAIXA){
    	$params = array(
    		':CDFILIAL' => $CDFILIAL,
    		':CDLOJA' => $CDLOJA,
    		':CDCAIXA' => $CDCAIXA
    	);
    	try{
    		$tipocaixa = $this->entityManager->getConnection()->fetchAll(\Util\self::GET_TIPO_CAIXA, $params);
    	}
    	catch(\Exception $e){
    		throw $e;
    	}
    	return $tipocaixa;
    }

    public function getParametroFilialLoja($codigo, $filter){
       	$arrayResult = array();
       
       	$arrayParam = array('CODPARAMETRO'=>$codigo, 'NRORG'=>$filter['NRORG'], 'DESCRELACIONAMENTO'=>'CDFILIAL', 'VALOR'=>$filter['CDFILIAL']);
    	try {
    		// Busca todas os relacionamentos para a filial informada, em siguida busca qual filial se relaciona com a loja informada.
			$valorParam = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYDESCRELAC_VALOR", $arrayParam);
			if($valorParam){
				
				for($i=0; $i<count($valorParam); $i++){
					$nrparametrovalor = $valorParam[$i]['NRPARAMETROVALOR'];
					/*
					Busca relacionamento de loja para o nrparametrovalor. Caso encontre relacionamento de loja e não encontre
					de PDV retorna o array pois se trata de um relacionamento a nível de loja.
					*/
					$arrayParamLoja = array('CODPARAMETRO'  =>$codigo, 
						                'NRORG'         =>$filter['NRORG'],
					                    'NRPARAMETROVALOR'=>$nrparametrovalor, 
					                    'DESCRELACIONAMENTO'=>'CDLOJA',
					                    'VALOR'=>$filter['CDLOJA']
					);

					$resultLoja        = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR", $arrayParamLoja);

					$arrayParamPDV = array('CODPARAMETRO'=>$codigo,
					                       'NRORG'=>$filter['NRORG'],
					                       'NRPARAMETROVALOR'=>$nrparametrovalor,
					                       'DESCRELACIONAMENTO'=>'CDCAIXA');
					$resultPDV =  $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC", $arrayParamPDV);	
					if($resultLoja && empty($resultPDV)){
						$i = count($valorParam);
						return $resultLoja;
					}
				}
				
			}
			return $arrayResult;
		}catch (\Exception $e){
			var_dump($e);
		}		
    }

    public function getParametroFilialLojaPDV($codigo, $filter){

    	$arrayResult = array();
    	$arrayParam = array('CODPARAMETRO'=>$codigo, 'NRORG'=>$filter['NRORG'], 'DESCRELACIONAMENTO'=>'CDFILIAL', 'VALOR'=>$filter['CDFILIAL']);
    	try {
    		// Busca todas os relacionamentos para a filial informada, em siguida busca qual filial se relaciona com a loja informada.
			$valorParam = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYDESCRELAC_VALOR", $arrayParam);
		    if($valorParam){
				for($i=0; $i<count($valorParam); $i++){
					$nrparametrovalor = $valorParam[$i]['NRPARAMETROVALOR'];
					/*
					Busca relacionamento de loja e pdv para o nrparametrovalor. Caso encontre relacionamento de loja e não encontre
					de PDV continua a busca pois se trata de um relacionamento a nível de loja. Caso entre um relacionamento de loja e pdv
					para os valores informados retorna o array.
					*/
					$arrayParamLoja = array('CODPARAMETRO'  =>$codigo, 
						                'NRORG'         =>$filter['NRORG'],
					                    'NRPARAMETROVALOR'=>$nrparametrovalor, 
					                    'DESCRELACIONAMENTO'=>'CDLOJA',
					                    'VALOR'=>$filter['CDLOJA']
					);
					$resultLoja= $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR", $arrayParamLoja);
					

					$arrayParamPDV = array('CODPARAMETRO'  =>$codigo, 
						                'NRORG'         =>$filter['NRORG'],
					                    'NRPARAMETROVALOR'=>$nrparametrovalor, 
					                    'DESCRELACIONAMENTO'=>'CDCAIXA',
					                    'VALOR'=>$filter['CDCAIXA']);
					
					$resultPDV =  $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR", $arrayParamPDV);	
					if($resultLoja && $resultPDV){
						$i = count($valorParam);
						return $resultPDV;
					}
				}
			}
			
			return $arrayResult;
		}catch (\Exception $e){
			var_dump($e);
		}		
    }

    public function getParametroTiporece($codigo, $filter){
    	$valorParam = array();	
    	$arrayParam = array('CODPARAMETRO'=>$codigo, 'NRORG'=>$filter['NRORG'], 'DESCRELACIONAMENTO'=>'TIPORECE', 'VALOR'=>$filter['CDTIPORECE']);
    	try {
			$valorParam = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYDESCRELAC_VALOR", $arrayParam);
    	}catch (\Exception $e){
			var_dump($e);
		}	
		return $valorParam;
    }

    public function getParametroFilialLojaTiporece($codigo, $filter){
    	$RETORNO = array();
    	$arrayParam = array('CODPARAMETRO'=>$codigo, 'NRORG'=>$filter['NRORG'], 'DESCRELACIONAMENTO'=>'CDFILIAL', 'VALOR'=>$filter['CDFILIAL']);
    	try {
    		// Busca todas os relacionamentos para a filial informada, em siguida busca qual filial se relaciona com a loja informada.
			$valorParam = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYDESCRELAC_VALOR", $arrayParam);
			if($valorParam){
				for($i=0; $i<count($valorParam); $i++){
					$nrparametrovalor = $valorParam[$i]['NRPARAMETROVALOR'];
					/*
					Busca relacionamento de loja e pdv para o nrparametrovalor. Caso encontre relacionamento de loja e não encontre
					de PDV continua a busca pois se trata de um relacionamento a nível de loja. Caso entre um relacionamento de loja e pdv
					para os valores informados retorna o array.
					*/
					$arrayParamLoja = array('CODPARAMETRO'  =>$codigo, 
						                'NRORG'         =>$filter['NRORG'],
					                    'NRPARAMETROVALOR'=>$nrparametrovalor, 
					                    'DESCRELACIONAMENTO'=>'CDLOJA',
					                    'VALOR'=>$filter['CDLOJA']);
					$resultLoja = $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR", $arrayParamLoja);
					if(!empty($resultLoja)){
						$arrayParamTiporece = array('CODPARAMETRO'  =>$codigo, 
						                'NRORG'         =>$filter['NRORG'],
					                    'NRPARAMETROVALOR'=>$nrparametrovalor, 
					                    'DESCRELACIONAMENTO'=>'CDTIPORECE',
					                    'VALOR'=>$filter['CDTIPORECE']);
						$resultTiporece =  $this->entityManager->getConnection()->fetchAll("GET_PARAMETRO_BYNRPARAMETROVALOR_BYDESC_BYVALOR", $arrayParamTiporece);	
						if($resultLoja && $resultTiporece){
							$i = count($valorParam);
							$RETORNO = $resultTiporece; 
						}else{
							continue;
						}
					}
				}
			}
			return $RETORNO;
		}catch (\Exception $e){
			var_dump($e);
		}	
    }

    public function buildHierarquia($arrayHierarquia){
    	$arrayValidate = array();
    	for($i=(count($arrayHierarquia)-1); $i>=0; $i--){
    		$hierarquiaValue = $arrayHierarquia[$i]['HIERARQUIAVALUE'];
    		if($hierarquiaComposta = strrchr($hierarquiaValue,'_')){
    			$arrayhierarquiaComposta = self::getArrayHierarquiaComposta($hierarquiaValue);
    			for($j=0; $j<count($arrayhierarquiaComposta); $j++){
    				$arrayValidate[] = $arrayhierarquiaComposta[$j];
    			}
    		}else{
    			array_unshift($arrayValidate, $arrayHierarquia[$i]['HIERARQUIAVALUE']);
    		}
    	}
    	return $arrayValidate;
    }
    
    public function getArrayHierarquiaComposta($hierarquiaValue){
    	$array = explode('_', $hierarquiaValue);
		return $array;    
	}
	
}