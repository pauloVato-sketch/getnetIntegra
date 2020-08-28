<?php
namespace Service;

class Consumer {

	protected $entityManager;
	protected $util;

	const SALE_TYPES = array(
		array("CDTIPOVENDA" => "1", "NMTIPOVENDA" => "Débito Consumidor"),
		array("CDTIPOVENDA" => "2", "NMTIPOVENDA" => "Crédito Pessoal"),
		array("CDTIPOVENDA" => "3", "NMTIPOVENDA" => "A Vista"),
		array("CDTIPOVENDA" => "4", "NMTIPOVENDA" => "Débito Consumidor/Crédito Pessoal"),
		array("CDTIPOVENDA" => "5", "NMTIPOVENDA" => "Débito Consumidor/A Vista"),
		array("CDTIPOVENDA" => "6", "NMTIPOVENDA" => "Crédito Pessoal/A Vista"),
		array("CDTIPOVENDA" => "7", "NMTIPOVENDA" => "Todos")
	);

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Util\Util $util) {
		$this->entityManager = $entityManager;
		$this->util = $util;
	}

	public function getClientData() {
        $session = $this->util->getSessionVars(null);
        $params = array(
            'CDFILIAL' => $session['CDFILIAL']
        );
		return $this->entityManager->getConnection()->fetchAll("SQL_GET_CLIENT_DATA");
	}

	public function getConsumerByMail($CDCLIENTE, $DSEMAILCONS){
		$params = array (
			$CDCLIENTE,
			$DSEMAILCONS
		);
		return $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_BY_EMAIL", $params);
	}

	public function getConsumerDetails($CDCLIENTE, $CDCONSUMIDOR){
		$params = array (
			'CDCLIENTE' => $CDCLIENTE,
			'code' => $CDCONSUMIDOR
		);
		return $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMER_DETAILS", $params);
	}

	public function registerConsumer($CDCLIENTE, $CDCONSUMIDOR, $CDSENHACONSMD5, $CDIDCONSUMID, $NMCONSUMIDOR, $DSEMAILCONS, $NRCELULARCONS, $IDSITCONSUMI, $CDCCUSCLIE, $IDCONSUMIDOR, $IDATUCONSUMI, $IDTPVENDACONS, $IDIMPCPFCUPOM, $IDCADCONFLIBCON, $IDPERCONSPRODEX, $IDTPSELMANHA, $IDTPSEALMOCO, $IDTPSELTARDE, $IDCRACHAMESTRE){
		$params = array (
			':CDCLIENTE' => $CDCLIENTE,
			':CDCONSUMIDOR' => $CDCONSUMIDOR,
			':CDSENHACONSMD5' => $CDSENHACONSMD5,
			':CDIDCONSUMID' => $CDIDCONSUMID,
			':NMCONSUMIDOR' => $NMCONSUMIDOR,
			':DSEMAILCONS' => $DSEMAILCONS,
			':NRCELULARCONS' => $NRCELULARCONS,
			':IDSITCONSUMI' => $IDSITCONSUMI,
			':CDCCUSCLIE' => $CDCCUSCLIE,
			':IDCONSUMIDOR' => $IDCONSUMIDOR,
			':IDATUCONSUMI' => $IDATUCONSUMI,
			':IDTPVENDACONS' => $IDTPVENDACONS,
			':IDIMPCPFCUPOM' => $IDIMPCPFCUPOM,
			':IDCADCONFLIBCON' => $IDCADCONFLIBCON,
			':IDPERCONSPRODEX' => $IDPERCONSPRODEX,
			':IDTPSELMANHA' => $IDTPSELMANHA,
			':IDTPSEALMOCO' => $IDTPSEALMOCO,
			':IDTPSELTARDE' => $IDTPSELTARDE,
			':IDCRACHAMESTRE' => $IDCRACHAMESTRE
		);
		$this->entityManager->getConnection()->executeQuery("SQL_INSERT_CONSUMER", $params);
	}

	public function allowUserAccess($dataset) {
		$session  = $this->util->getSessionVars($dataset['chave']);
		$cdFilial = $session['CDFILIAL'];
		$cdLoja   = $session['CDLOJA'];

		$params = array($cdFilial, $cdLoja, $dataset['nracessouser']);

		$validaMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_MESA_BYNRACESSOUSER", $params);

		 if($validaMesa['CHAVEGARCOM'] == NULL || $validaMesa['IDACESSOUSER'] == 'P'){
			if (empty($validaMesa)) {
				return array('funcao' => '0', 'error' => '005'); //005 - Mesa não cadastrada para a filial/loja.
			} else {
				if($validaMesa['IDSTMESAAUX'] != 'S'){
					$params = array($cdFilial, $cdLoja, $dataset['chave'], $dataset['nracessouser']);
					$this->entityManager->getConnection()->executeQuery("SQL_PERMITE_ACESSO", $params);
					return array('funcao' => '1');
				}
			}
		}else{
			return array('funcao' => '0', 'error' => '054'); //Solicitação de acesso foi atendida por um outro garçom
		}
	}

	public function checkAcess($dataset) {
		$nmusuario = $dataset['nmusuario'];
		$params = array($nmusuario);

		$acess = $this->entityManager->getConnection()->fetchAssoc("SQL_CHECK_ACCESS", $params);


		return $acess;
	}

	public function checkBlockedUsers() {
		$params = array(
			$_SERVER['REMOTE_ADDR']
		);

		$retorno = $this->entityManager->getConnection()->fetchAssoc("SQL_VERIFICA_IP_BLOQUEADO", $params);
		return array('funcao' => '1', 'retorno' => $retorno);
	}

	public function getAllBlockedIps($chave) {
		$session = $this->util->getSessionVars($chave);

		$params = array(
			$session['CDFILIAL']
		);

		return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_IPS_BLOQUEADOS", $params);
	}

	public function controlUserAccess($dataset) {
		$nracessouser = $dataset['nracessouser'];
		$status       = $dataset['status'];
		$session = $this->util->getSessionVars($dataset['chave']);

		$params = array(
				$nracessouser
			);

		$idacessouser = $this->entityManager->getConnection()->fetchAssoc("SQL_IDACESSOUSER", $params);

		if($idacessouser['IDACESSOUSER'] != 'A'){

			$params = array(
				$status,
				$session['CDFILIAL'],
				$session['CDLOJA'],
				$nracessouser
			);

			//Bloqueia o acesso
			$this->entityManager->getConnection()->executeQuery("SQL_ALTERA_STATUS_ACESSOFM", $params);
			return array('funcao' => '1');
		}else{
		   return array('funcao' => '0', 'error' => '436'); //436 - Não é possível realizar está operação. Este acesso está autorizado.
		}
	}

	public function listUserAccess() {
			// busca a lista de acessos pendentes
			$r_acessos = $this->entityManager->getConnection()->fetchAll("SQL_ACESSOS");

			return array(
				'funcao' => '1',
				'solicitacoes' => $r_acessos,
			);
	}

	//Esta função retorna todas as mesas em uso
	public function findAllTables() {
			// busca a lista de acessos pendentes
			$r_acessos = $this->entityManager->getConnection()->fetchAll("SQL_ACESSOS_AUT");

			return array(
				'funcao' => '1',
				'solicitacoes' => $r_acessos,
			);
	}

	public function requestAccess($dataset) {
			$BDversion = $this->entityManager->getConnection()->fetchAssoc("SQL_BD_VERSION"); //Se houver conexão com o banco ira apresentar um retorno

			if($BDversion == null){ //Se não houver retorno no select acima, é porque não existe conexão com banco de dados
				return array('funcao' => '0', 'error' => '265'); //265 - 'Não foi possível conectar no banco de dados, verifique o arquivo "app.json".'
			}

			$nmUsuario = strtoupper($dataset['nome']);
			$nrMesa = $dataset['mesa'];

			// valida se o acesso já foi autorizado
			$params = array($nmUsuario, $nrMesa);

			$r_acesso_aut = $this->entityManager->getConnection()->fetchAssoc("SQL_ACESSO_AUT", $params);

			if ((empty($r_acesso_aut)) || ($r_acesso_aut['IDACESSOUSER'] === 'B') || ($r_acesso_aut['IDACESSOUSER'] === 'I')) {
				// se não existir solicitação de acesso ou se o acesso dele foi bloqueado/ignorado
				// insere pedido de acesso como pendente
				$this->util->newCode('ACESSOFM');
				$nrAcessoUser = $this->util->getNewCode('ACESSOFM', 10);

				$params = array(
					$nrAcessoUser,
					$nrMesa,
					$nmUsuario,
					$_SERVER['REMOTE_ADDR']
				);

				$this->entityManager->getConnection()->executeQuery("SQL_INSERE_ACESSO", $params);

				$nmmesa = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_NOME_MESA", array($nrMesa));
				$nmmesa = $nmmesa['NMMESA'];

				$retorno = array(
								'funcao'       => '1',
								'NRACESSOUSER' => $nrAcessoUser,
								'NMUSUARIO'    => $nmUsuario,
								'NMMESA'       => $nmmesa
							);
			} else if ($r_acesso_aut['IDACESSOUSER'] === 'P') { //Se o acesso estiver pendente, usuário continuará esperando

				$idacessouser = 'P';

				$params = array(
					$nrMesa,
					$nmUsuario,
					$idacessouser
				);

				$nrAcessoUser = $this->entityManager->getConnection()->fetchAssoc("SQL_NRACESSOUSER", $params);
				$nmmesa = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_NOME_MESA", array($nrMesa));
				$nmmesa = $nmmesa['NMMESA'];

				$retorno = array(
								'funcao'       => '1',
								'NRACESSOUSER' => $nrAcessoUser["NRACESSOUSER"],
								'NMUSUARIO'    => $nmUsuario,
								'NMMESA'       => $nmmesa
							);

			} else if ($r_acesso_aut['IDACESSOUSER'] === 'A') {

				$idacessouser = 'A';

				$params = array(
					$nrMesa,
					$nmUsuario,
					$idacessouser
				);

				$nrAcessoUser = $this->entityManager->getConnection()->fetchAssoc("SQL_NRACESSOUSER", $params);
				$nmmesa = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_NOME_MESA", array($nrMesa));
				$nmmesa = $nmmesa['NMMESA'];


				$params = array(
					$nrAcessoUser["NRACESSOUSER"],
					$nrMesa,
					$nmUsuario
				);

				$this->entityManager->getConnection()->executeQuery("SQL_UPDATE_ACESSO", $params);
				$retorno = array(
								'funcao'       => '1',
								'NRACESSOUSER' => $nrAcessoUser["NRACESSOUSER"],
								'NMUSUARIO'    => $nmUsuario,
								'NMMESA'       => $nmmesa
							);

			}
			return $retorno;
	}

	public function userLogin($dataset) {
		$nracessouser = $dataset['nracessouser'];

		// busca os dados que foram inseridos pelo garçom
		$params = array($nracessouser);
		$r_dados_usuario = $this->entityManager->getConnection()->fetchAssoc("SQL_DADOS_USUARIO", $params);

		$params = array($r_dados_usuario['CDFILIAL'], $r_dados_usuario['CDLOJA'],  $r_dados_usuario['NRACESSOUSER']);


		$validaMesa = $this->entityManager->getConnection()->fetchAssoc("SQL_VALIDA_MESA_BYNRACESSOUSER", $params);

		if ($r_dados_usuario['IDACESSOUSER'] == 'P') {
			return array('funcao' => '0', 'error' => '053'); // Seu acesso ainda não foi autorizado, aguarde a autorização do garçom.
		} else if ($r_dados_usuario['IDACESSOUSER'] == 'B' || $r_dados_usuario['IDACESSOUSER'] == 'I') {

			if($validaMesa['IDSTMESAAUX'] == 'S'){
				return array('funcao' => '0', 'error' => '439'); // Sua conta já foi solicitada. Para reabrir a mesa chame o garçon
			}else{
			   return array('funcao' => '0', 'error' => '057'); // Seu acesso foi inativado. Para continuar realizando pedidos, solicite acesso novamente.
			}

		} else if ($r_dados_usuario['IDACESSOUSER'] == 'A') {
			$controleAcesso = array('modoHabilitado' => 'U');
			$session = $this->util->getSessionVars($r_dados_usuario['CHAVEGARCOM']);

			$params = array($session['CDFILIAL'], $r_dados_usuario['NRMESA']);
			$r_dados_vendarest = $this->entityManager->getConnection()->fetchAssoc("SQL_NRVENDAREST", $params);

			$params = array($session['CDFILIAL'], $r_dados_vendarest['NRVENDAREST']);
			$r_dados_comandaven = $this->entityManager->getConnection()->fetchAssoc("SQL_NRCOMANDA", $params);

			return array(
				'funcao'         => '1',
				'chave'          => $r_dados_usuario['CHAVEGARCOM'],
				'supervisor'     => 'N',
				'CONTROLEACESSO' => $controleAcesso,
				'NRMESA'         => $r_dados_usuario['NRMESA'],
				'NRACESSOUSER'   => $r_dados_usuario['NRACESSOUSER'],
				'NMUSUARIO'      => $r_dados_usuario['NMUSUARIO'],
				'IDACESSOUSER'   => $r_dados_usuario['IDACESSOUSER'],
				'NRCOMANDA'      => $r_dados_comandaven['NRCOMANDA'],
				'NRVENDAREST'    => $r_dados_vendarest['NRVENDAREST']
			);
		}
	}

    public function getCountries($NMPAIS, $FIRST, $LAST){
        $params = array(
            'NMPAIS' => strtoupper($NMPAIS),
            'FIRST' => $FIRST,
            'LAST' => $LAST
        );
        return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_PAIS", $params);
    }

    public function getStates($CDPAIS, $NMESTADO, $FIRST, $LAST){
        $params = array(
            'CDPAIS' => $CDPAIS,
            'NMESTADO' => strtoupper($NMESTADO),
            'FIRST' => $FIRST,
            'LAST' => $LAST
        );
        return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_ESTADO", $params);
    }

    public function getCities($CDPAIS, $SGESTADO, $NMMUNICIPIO, $FIRST, $LAST){
        $params = array(
            'CDPAIS' => $CDPAIS,
            'SGESTADO' => $SGESTADO,
            'NMMUNICIPIO' => strtoupper($NMMUNICIPIO),
            'FIRST' => $FIRST,
            'LAST' => $LAST
        );
        return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_MUNICIPIO", $params);
    }

    public function getNeighborhoods($CDPAIS, $SGESTADO, $CDMUNICIPIO, $NMBAIRRO, $FIRST, $LAST){
        $params = array(
            'CDPAIS' => $CDPAIS,
            'SGESTADO' => $SGESTADO,
            'CDMUNICIPIO' => $CDMUNICIPIO,
            'NMBAIRRO' => strtoupper($NMBAIRRO),
            'FIRST' => $FIRST,
            'LAST' => $LAST
        );
        return $this->entityManager->getConnection()->fetchAll("SQL_BUSCA_BAIRRO", $params);
    }

    public function getSaleTypes(){
        return self::SALE_TYPES;
    }

    public function getConsumerTypes(){
        $params = array();
        return $this->entityManager->getConnection()->fetchAll("SQL_TIPO_CONS", $params);
    }

    public function addConsumer($data){
        $session = $this->util->getSessionVars(null);

        $NRCPFRESPCON = preg_replace('/\.|-/', '', $data['consumerCPF']);

        $consumer = $this->getConsumerDetails($data['CDCLIENTE'], $data['consumerCod']);
        if (!empty($consumer)){
            throw new \Exception("Já existe um consumidor cadastrado com este código.");
        }

        $params = array(
            'CDFILIAL' => $session['CDFILIAL']
        );
        $CDCCUSCLIE = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CLIENT_DATA", $params)['CDCCUSCLIE'];
        if (empty($CDCCUSCLIE)){
            throw new \Exception("Centro de Custo Padrão não está cadastrado.");
        }

        $CDSENHACONSMD5 = $this->util->encrypt($data['consumerPassAcess']);

        $params = array(
            // Basic
            'CDCLIENTE'       => $data['CDCLIENTE'],
            'CDCONSUMIDOR'    => $data['consumerCod'],
            'NMCONSUMIDOR'    => $data['consumerName'],
            'IDSEXOCONS'      => $data['consumerGender'],
            'DTNASCCONS'      => $data['consumerBirth'],
            'NRRGCONSUMID'    => $data['consumerRG'],
            'NRCPFRESPCON'    => $NRCPFRESPCON,
            // Address
            'DSENDECONS'      => $data['consumerAdress'],
            'NRENDECONS'      => $data['consumerAdressNum'],
            'CDPAIS'          => $data['CDPAIS'],
            'SGESTADO'        => $data['SGESTADO'],
            'CDMUNICIPIO'     => $data['CDMUNICIPIO'],
            'CDBAIRRO'        => $data['CDBAIRRO'],
            'NMBAIRCONS'      => $data['NMBAIRRO'],
            'NRCEPCONS'       => $data['consumerCEP'],
            // Contact
            'NRTELECONS'      => $data['consumerResPhone'],
            'NRTELE2CONS'     => $data['consumerComPhone'],
            'NRCELULARCONS'   => $data['consumerCellPhone'],
            'DSEMAILCONS'     => $data['consumerEmail'],
            // Types
            'CDTIPOCONS'      => $data['CDTIPOCONS'],
            'IDTPVENDACONS'   => $data['CDTIPOVENDA'],
            // Access
            'CDIDCONSUMID'    => $data['consumerId'],
            'CDEXCONSUMID'    => $data['consumerCodAcessExt'],
            'NMLOGINCONS'     => $data['consumerCodAcess'],
            'CDSENHACONSMD5'  => $data['consumerPassAcess'],
            // Toggles
            'IDATUCPFCONS'    => $data['consumerUpdateCPF'],
            'IDPERCONSPRODEX' => $data['consumerReleasedConsum'],
            'IDIMPCPFCUPOM'   => $data['consumerPrint'],
            'IDVERSALDCON'    => $data['consumerVerifyBalance'],
            // Other not-null fields.
            'CDCCUSCLIE'      => $CDCCUSCLIE,
            'IDSITCONSUMI'    => '1',
            'IDCONSUMIDOR'    => 'C',
            'IDATUCONSUMI'    => 'S',
            'IDCADCONFLIBCON' => 'S',
            'IDTPSELMANHA'    => 'N',
            'IDTPSEALMOCO'    => 'N',
            'IDTPSELTARDE'    => 'N',
            'IDCRACHAMESTRE'  => 'N',
            'NRORG'           => $session['NRORG']
        );
        $this->entityManager->getConnection()->executeQuery("SQL_ADD_CONSUMER", $params);
    }

}