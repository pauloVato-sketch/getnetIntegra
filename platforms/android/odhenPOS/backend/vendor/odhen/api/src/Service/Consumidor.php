<?php

namespace Odhen\API\Service;

use Odhen\API\Remote\Printer\Command;

class Consumidor {

    protected $entityManager;
    protected $util;
    protected $impressaoutil;
    protected $databaseUtil;
    protected $evoluiSaldo;
    protected $extratocons;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager,
                                \Odhen\API\Util\Util $util,
                                \Odhen\API\Lib\ImpressaoUtil $impressaoutil,
                                \Odhen\API\Util\Database $databaseUtil,
                                \Odhen\API\Util\EvoluiSaldo $evoluiSaldo,
                                \Odhen\API\Service\Extratocons $extratocons){
        $this->entityManager = $entityManager;
        $this->util          = $util;
        $this->impressaoutil = $impressaoutil;
        $this->databaseUtil  = $databaseUtil;
        $this->evoluiSaldo   = $evoluiSaldo;
        $this->extratocons   = $extratocons;
    }

    /**
     * [populaDadosConsumidor description]
     * @param  [array] $dadosConsumidor [description]
     * @return [array] retorna um array com os dados completo do consumidor [description]
     */
    public function populaDadosConsumidor($dadosConsumidor){
        $params = array(
            ':CDCONSUMIDOR' => $dadosConsumidor['CDCONSUMIDOR'],
            ':CDCLIENTE' => $dadosConsumidor['CDCLIENTE']
        );
        $dadosConsumidor = $this->entityManager->getConnection()->fetchAssoc("GET_CONSUMER_BY_ID", $params);
        return $dadosConsumidor;
    }

    /**
     * [populaDadosConsumidor description]
     * @param  [string] $COLUNA CDIDCONSUMID
     * @return [array] retorna um array com os dados completo do consumidor [description]
     */
    public function getCDIDCONSUMID($consumer){
        $params = array(
            ':CDIDCONSUMID' => $consumer['CDIDCONSUMID']
        );
        $resultConsumer = $this->entityManager->getConnection()->fetchAll("GET_CONSUMER_CDIDCONSUMID", $params);
        if(!$resultConsumer) {
            return array(
                'error' => true,
                'message' => 'Código do consumidor ou senha inválidos.'
            );
        } else {
            return array(
                'error' => false,
                'consumer' => $resultConsumer
            );
        }
    }

    public function validaCDSENHACONSMD5 ($CDSENHACONSPARAM, $CDSENHACONSBD){
        return md5($CDSENHACONSPARAM) === $CDSENHACONSBD;
    }

    public function getLimitDebConsumer($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );
        $saldoAtual = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_SALDO_DEBITO", $params);
        $saldoAtual = !empty($saldoAtual['VRSALDOCONS']) ? $saldoAtual['VRSALDOCONS'] : 0;
        // Busca todos os limites.
        $limites = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_LIMITE_DEBITO", $params);
        $limiteAtual = null;

        if ($limites['IDVERSALDCON'] == 'S'){
            // Calcula gasto dia.
            $params['DTMOVCLI'] = new \DateTime();
            $type = array(
                'DTMOVCLI' => \Doctrine\DBAL\Types\Type::DATE,
            );
            $gastoDia = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_GASTO_DIA_DEBITO_CONSUMIDOR", $params, $type);
            $gastoDia = !empty($gastoDia['CONSUMO']) ? floatval($gastoDia['CONSUMO']) : 0;

            // Calcula gasto mês.
            $params['DTMOVCLI'] = new \DateTime(date('01-m-Y'));
            $gastoMes = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_GASTO_MES_DEBITO_CONSUMIDOR", $params, $type);
            $gastoMes = !empty($gastoMes['CONSUMO']) ? floatval($gastoMes['CONSUMO']) : 0;

            // Formata os limites para que sejam floats.
            $limites['VRMAXDEBCONS'] = $limites['VRMAXDEBCONS'] ? floatval($limites['VRMAXDEBCONS']) : null;
            $limites['VRMAXCONSDIAD'] = $limites['VRMAXCONSDIAD'] ? floatval($limites['VRMAXCONSDIAD']) : null;
            $limites['VRMAXCONSMESD'] = $limites['VRMAXCONSMESD'] ? floatval($limites['VRMAXCONSMESD']) : null;
            $limites['VRAVIDEBCONS'] = $limites['VRAVIDEBCONS'] ? floatval($limites['VRAVIDEBCONS']) : null;

            // Calcula limite atual, baseado no valor limite e consumos.
            if ($limites['VRLIMDEBCONS'] !== null){
                $limites['VRLIMDEBCONS'] = floatval($limites['VRLIMDEBCONS']);
                $limiteAtual = round($saldoAtual - $limites['VRLIMDEBCONS'], 2);

                if ($limites['VRMAXDEBCONS'] !== null && ($limites['VRMAXDEBCONS'] - $gastoDia) < $limiteAtual){
                    $limiteAtual = round($limites['VRMAXDEBCONS'] - $gastoDia, 2);
                }

                if ($limites['VRMAXCONSDIAD'] !== null && ($limites['VRMAXCONSDIAD'] - $gastoDia) < $limiteAtual){
                    $limiteAtual = round($limites['VRMAXCONSDIAD'] - $gastoDia, 2);
                }

                if ($limites['VRMAXCONSMESD'] !== null && ($limites['VRMAXCONSMESD'] - $gastoMes) < $limiteAtual){
                    $limiteAtual = round($limites['VRMAXCONSMESD'] - $gastoMes, 2);
                }
            }
        }
        else {
            // Caso IDVERSALDCON for N, não é necessário validar os campos abaixo.
            $gastoDia = null;
            $gastoMes = null;
            $limites['VRLIMDEBCONS'] = null;
            $limites['VRMAXDEBCONS'] = null;
            $limites['VRMAXCONSDIAD'] = null;
            $limites['VRMAXCONSMESD'] = null;
            $limites['VRAVIDEBCONS'] = null;
        }

        return array(
            'CDCONSUMIDOR' => $limites['CDCONSUMIDOR'],
            'NMCONSUMIDOR' => $limites['NMCONSUMIDOR'],
            'SALDO_ATUAL'  => floatval($saldoAtual),
            'LIMITE_ATUAL' => $limiteAtual,
            'CONSUMO_DIA'  => $gastoDia,
            'CONSUMO_MES'  => $gastoMes,
            'VRLIMDEBCONS' => $limites['VRLIMDEBCONS'],
            'VRMAXDEBCONS' => $limites['VRMAXDEBCONS'],
            'VRMAXCONSDIAD' => $limites['VRMAXCONSDIAD'],
            'VRMAXCONSMESD' => $limites['VRMAXCONSMESD'],
            'VRAVIDEBCONS' => $limites['VRAVIDEBCONS']
        );
    }

    public function getSaldoConsumidor($CDCLIENTE, $CDCONSUMIDOR) {
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );
		$saldoConsumidor = null;
		$consumidor = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_SALDO_DEBITO", $params);
		if ($consumidor) {
			$saldoConsumidor = $consumidor['VRSALDOCONS'];
		} else {
			$saldoConsumidor = 0;
		}
		return $saldoConsumidor;
	}

    public function validaValorLimiteDebitoConsumidor($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $TOTALVENDA){
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );
        $result = array('error' => false);
        $valorLimite = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_LIMITE_DEBITO", $params);

        if ($valorLimite['IDVERSALDCON'] == 'S' && $valorLimite['VRLIMDEBCONS'] != null){
            $saldoAtual = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_SALDO_DEBITO", $params);
            $saldoAtual = !empty($saldoAtual['VRSALDOCONS']) ? $saldoAtual['VRSALDOCONS'] : 0;

            if ((floatval($saldoAtual) - $TOTALVENDA) < floatval($valorLimite['VRLIMDEBCONS'])){
                $result['error'] = true;
                $result['message'] = 'Não foi possível realizar a venda. Saldo limite ultrapassado.';
            }
        }
        return $result;
    }

    public function validaMaximoDiarioDebitoConsumidor($CDFILIAL, $CDLOJA, $CDCLIENTE, $CDCONSUMIDOR, $TOTALVENDA) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDLOJA' => $CDLOJA,
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );
        $result = array('error' => false);
        $valorMaximoDiario = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_CONSUMIDOR_LIMITE_DEBITO", $params);
        if ($valorMaximoDiario['VRMAXDEBCONS'] != null){
            $params['DTMOVCLI'] = new \DateTime();
            $type = array(
                'DTMOVCLI' => \Doctrine\DBAL\Types\Type::DATE,
            );
            $gastoDia = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_GASTO_DIA_DEBITO_CONSUMIDOR", $params, $type)['CONSUMO'];
            if (floatval($gastoDia) + floatval($TOTALVENDA) > floatval($valorMaximoDiario['VRMAXDEBCONS'])) {
                $result['error'] = true;
                $result['message'] = 'Não foi possível realizar a venda. Valor máximo diário excedido.';
            }
        }
        return $result;
    }

	public function getLimiteCredDisponivel($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR){
        $result = array();

        // Define a familia.
        $params = array(
            'CDFILIAL' => $CDFILIAL
        );
        $familyDetails = $this->entityManager->getConnection()->fetchAssoc("SQL_DETALHES_FAMILIA", $params);
        $CDFAMILISALD = $familyDetails['CDFAMILISALD'];

        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR
        );
        $limitesConsumo = $this->entityManager->getConnection()->fetchAssoc("SQL_BUSCA_LIMITES", $params);

        if ($limitesConsumo['IDVERSALDCON'] == 'S' && $limitesConsumo['VRMAXCREDCONS'] != null){
            $consumoDiario = $this->calculaConsumoDiario($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
            $limiteDisponivel = floatval($limitesConsumo['VRMAXCREDCONS']) - $consumoDiario;
        }
        else {
            $limiteDisponivel = null;
        }

        $result['limiteDisponivel'] = $limiteDisponivel;

        $saldoDisponivel = $this->buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL);

        if (empty($saldoDisponivel)) $result['saldoDisponivel'] = 0;
        else $result['saldoDisponivel'] = floatval($saldoDisponivel['VRSALDCONEXT']);

        return $result;
    }

	public function calculaConsumoDiario($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD){
        $date = new \DateTime();

        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'CDFAMILISALD' => $CDFAMILISALD,
                'DTMOVEXTCONS' => $date
            );
            $type = array(
                'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATE,
            );
            $consumoDiario = $this->entityManager->getConnection()->fetchAssoc("SQL_CONSUMO_DIARIO", $params, $type);
            return !empty($consumoDiario) ? floatval($consumoDiario['CONSUMO']) : floatval(0);
        }
        else {
            $consumoDiario = $this->extratocons->consultaConsumoFamilia($CDCLIENTE, $CDCONSUMIDOR, $date->format('d/m/Y'));
            if (!empty($consumoDiario)){
                $consumoDiario = $consumoDiario[0];
                if (!array_key_exists(0, $consumoDiario)){
                    $consumoDiario = array(0 => $consumoDiario);
                }
            }
            foreach ($consumoDiario as $family){
                if ($family['CDFAMILISALD'] == $CDFAMILISALD){
                    return floatval($family['SOMA']);
                }
            }
            return 0;
        }

    }

    public function creditaSaldo($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $NRSEQMOVEXT, $CDTIPORECE, $CDFILIAL, $CDCAIXA, $VRSALDCONFAM, $NRDEPOSICONS, $NRSEQMOVCAIXA, $DTABERCAIX, $DSOPEEXTCONS){
        $VRSALDCONEXT = $VRSALDCONFAM;

        $saldoExtrato = self::buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL);
        if (!empty($saldoExtrato)){
            $VRSALDCONEXT += floatval($saldoExtrato['VRSALDCONEXT']);
        }

        // Insere crédito na EXTRATOCONS.
        $params = array(
            'CDCLIENTE'     => $CDCLIENTE,
            'CDCONSUMIDOR'  => $CDCONSUMIDOR,
            'CDFAMILISALD'  => $CDFAMILISALD,
            'DTMOVEXTCONS'  => new \DateTime(),
            'NRSEQMOVEXT'   => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
            'CDTIPORECE'    => $CDTIPORECE,
            'CDFILIAL'      => $CDFILIAL,
            'CDCAIXA'       => $CDCAIXA,
            'DSOPEEXTCONS'  => $DSOPEEXTCONS,
            'VRMOVEXTCONS'  => $VRSALDCONFAM,
            'VRSALDCONEXT'  => $VRSALDCONEXT,
            'IDTPMOVEXT'    => 'C',
            'NRDEPOSICONS'  => $NRDEPOSICONS,
            'DTABERCAIX'    => $DTABERCAIX,
            'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA,
            'NRSEQVENDA'    => null
        );
        $type = array(
            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $this->entityManager->getConnection()->executeQuery("INSERT_EXTRATOCONS", $params, $type);

        // Insere ou atualiza saldo na SALDOCONS.
        self::atualizaSaldo($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $VRSALDCONEXT);

        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] === 'S'){
            // Insere crédito na EXTRATOCONS.
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'CDFAMILISALD' => $CDFAMILISALD,
                'DTMOVEXTCONS' => new \DateTime(),
                'NRSEQMOVEXT' => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
                'CDTIPORECE' => $CDTIPORECE,
                'IDTPMOVEXT' => 'C',
                'DSOPEEXTCONS' => $DSOPEEXTCONS,
                'VRMOVEXTCONS' => $VRSALDCONFAM,
                'VRSALDCONEXT' => $VRSALDCONEXT,
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA,
                'NRDEPOSICONS' => $NRDEPOSICONS,
                'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA,
                'IDIMPEXTRATO' => 'S'
            );
            $params['DTMOVEXTCONS'] = $params['DTMOVEXTCONS']->format('d/m/Y H:i:s');
            $this->extratocons->insereExtratocons($params);
        }

        // Configura array de retorno com os nomes do cliente/consumidor/familia para a impressão.
        $creditDetails = self::buscaNomes($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
        $creditDetails['VRSALDCONEXT'] = $VRSALDCONEXT;

        return $creditDetails;
    }

    public function debitaSaldo($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $NRSEQMOVEXT, $CDTIPORECE, $CDFILIAL, $CDCAIXA, $VRSALDCONFAM, $NRDEPOSICONS, $NRSEQMOVCAIXA, $DTABERCAIX, $DSOPEEXTCONS){
        $VRSALDCONEXT = $VRSALDCONFAM;

        $saldoExtrato = self::buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL);
        if (!empty($saldoExtrato)){
            $VRSALDCONEXT -= floatval($saldoExtrato['VRSALDCONEXT']);
        }

        // Insere crédito na EXTRATOCONS.
        $params = array(
            'CDCLIENTE'     => $CDCLIENTE,
            'CDCONSUMIDOR'  => $CDCONSUMIDOR,
            'CDFAMILISALD'  => $CDFAMILISALD,
            'DTMOVEXTCONS'  => new \DateTime(),
            'NRSEQMOVEXT'   => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
            'CDTIPORECE'    => $CDTIPORECE,
            'CDFILIAL'      => $CDFILIAL,
            'CDCAIXA'       => $CDCAIXA,
            'DSOPEEXTCONS'  => $DSOPEEXTCONS,
            'VRMOVEXTCONS'  => $VRSALDCONFAM,
            'VRSALDCONEXT'  => $VRSALDCONEXT,
            'IDTPMOVEXT'    => 'D',
            'NRDEPOSICONS'  => $NRDEPOSICONS,
            'DTABERCAIX'    => $DTABERCAIX,
            'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA,
            'NRSEQVENDA'    => null
        );
        $type = array(
            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        $this->entityManager->getConnection()->executeQuery("INSERT_EXTRATOCONS", $params, $type);

        // Insere ou atualiza saldo na SALDOCONS.
        self::atualizaSaldo($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $VRSALDCONEXT);

        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] === 'S'){
            // Insere crédito na EXTRATOCONS.
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'CDFAMILISALD' => $CDFAMILISALD,
                'DTMOVEXTCONS' => new \DateTime(),
                'NRSEQMOVEXT' => str_pad($NRSEQMOVEXT, 3, '0', STR_PAD_LEFT),
                'CDTIPORECE' => $CDTIPORECE,
                'IDTPMOVEXT' => 'D',
                'DSOPEEXTCONS' => $DSOPEEXTCONS,
                'VRMOVEXTCONS' => $VRSALDCONFAM,
                'VRSALDCONEXT' => $VRSALDCONEXT,
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA,
                'NRDEPOSICONS' => $NRDEPOSICONS,
                'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA,
                'IDIMPEXTRATO' => 'S'
            );
            $params['DTMOVEXTCONS'] = $params['DTMOVEXTCONS']->format('d/m/Y H:i:s');
            $this->extratocons->insereExtratocons($params);
        }

        // Configura array de retorno com os nomes do cliente/consumidor/familia para a impressão.
        $creditDetails = self::buscaNomes($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
        $creditDetails['VRSALDCONEXT'] = $VRSALDCONEXT;

        return $creditDetails;
    }

    // Verifica se o consumidor possui saldo anterior na extratocons.
    public function primeiraInsercao($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD = null){
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'CDFAMILISALD' => $CDFAMILISALD
        );
        if ($CDFAMILISALD){
            $result = $this->entityManager->getConnection()->fetchAssoc("IS_FIRST_INSERTION_FAMILY", $params);
        }
        else {
            $result = $this->entityManager->getConnection()->fetchAssoc("IS_FIRST_INSERTION", $params);
        }
        return $result ? $result : array();
    }

    public function buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL){
        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'CDFAMILISALD' => $CDFAMILISALD
            );
            $result = $this->entityManager->getConnection()->fetchAssoc("SQL_SALDO_EXTRATOCONS", $params);
            return $result ? $result : array();
        }
        else {
            $result = $this->extratocons->consultaSaldo('', $CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
            if (!empty($result)){
                $result = $result[0];
                $result['VRSALDCONEXT'] = $result['VRSALDCONFAM'];
            }
            return $result;
        }
    }

	public function buscaSaldoCons($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD){
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'CDFAMILISALD' => $CDFAMILISALD
        );
        $result = $this->entityManager->getConnection()->fetchAssoc("SQL_SALDO_SALDOCONS", $params);
        return $result ? $result : array();
    }

    public function atualizaSaldo($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $VRSALDCONEXT){
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'CDFAMILISALD' => $CDFAMILISALD,
            'VRSALDCONFAM' => $VRSALDCONEXT
        );
        $saldoConsumidor = self::buscaSaldoCons($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
        if (empty($saldoConsumidor)){
            $this->entityManager->getConnection()->executeQuery("CREATE_BALANCE", $params);
        }
        else {
            $this->entityManager->getConnection()->executeQuery("UPDATE_BALANCE", $params);
        }
    }

	public function buscaNomes($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD){
        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'CDFAMILISALD' => $CDFAMILISALD
        );
        $result = $this->entityManager->getConnection()->fetchAssoc("BUSCA_NOMES", $params);
        return $result ? $result : array();
    }

    public function restricaoSaldoNegativoConsumidor($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL) {
		$params = array(
			'CDCLIENTE' => $CDCLIENTE,
			'CDCONSUMIDOR' => $CDCONSUMIDOR,
			'CDFAMILISALD' => $CDFAMILISALD,
			'CDFILIAL' => $CDFILIAL
		);
		$result = $this->entityManager->getConnection()->fetchAssoc("PERMITE_SALDO_NEGATIVO_CONSUMIDOR", $params);
		return !isset($result['IDSALDNEGRAL']) || (isset($result['IDSALDNEGRAL']) ? $result['IDSALDNEGRAL'] == 'S' : false);
    }

    public function restricaoSaldoDiario($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL){
		$params = array(
			'CDCLIENTE' => $CDCLIENTE,
			'CDCONSUMIDOR' => $CDCONSUMIDOR,
			'CDFAMILISALD' => $CDFAMILISALD,
			'CDFILIAL' => $CDFILIAL
		);

		$limiteGasto = $this->entityManager->getConnection()->fetchAssoc("RESTRICAO_SALDO_DIARIO", $params);
		return isset($limiteGasto['VRCONSDIARAL']) ? $limiteGasto['VRCONSDIARAL'] : null;
    }

    public function restricaoGastoDiario($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL, $limiteGasto, $totalValorFamilia){
        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
    		$params = array(
    			'CDCLIENTE' => $CDCLIENTE,
    			'CDCONSUMIDOR' => $CDCONSUMIDOR,
    			'CDFAMILISALD' => $CDFAMILISALD,
    			'CDFILIAL' => $CDFILIAL
    		);
    		$gastoDia = $this->entityManager->getConnection()->fetchAssoc("GET_TOTAL_VENDA_CREDITO_PESSOAL", $params);
            return floatval($gastoDia['SUMEXTRATOCONS']) + $totalValorFamilia > floatval($limiteGasto);
        }
        else {
            $date = new \DateTime();
            $gastoFamilias = $this->extratocons->consultaConsumoFamilia($CDCLIENTE, $CDCONSUMIDOR, $date->format('d/m/Y'));
            if (!empty($gastoFamilias)){
                $gastoFamilias = $gastoFamilias[0];
                if (!array_key_exists(0, $gastoFamilias)){
                    $gastoFamilias = array(0 => $gastoFamilias);
                }
            }
            $gastoDia = 0;
            foreach ($gastoFamilias as &$family){
                if ($family['CDFAMILISALD'] == $CDFAMILISALD){
                    $gastoDia = $family['SOMA'];
                    break;
                }
            }
            return floatval($gastoDia) + $totalValorFamilia > floatval($limiteGasto);;
        }
    }

    public function restricaoProdutoDia($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL, $CDPRODUTO) {
		$params = array(
			'CDCLIENTE' => $CDCLIENTE,
			'CDCONSUMIDOR' => $CDCONSUMIDOR,
			'CDFAMILISALD' => $CDFAMILISALD,
			'CDFILIAL' => $CDFILIAL,
			'CDPRODUTO' => $CDPRODUTO
		);

		$limiteProduto = $this->entityManager->getConnection()->fetchAssoc("RESTRICAO_PRODUTO_DIA", $params);
		return isset($limiteProduto['QTCONSDIARAL']) ? $limiteProduto['QTCONSDIARAL'] : null;
    }

    public function validaQuantidadeProdutoDia($CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDCAIXA, $CDPRODUTO, $limiteProduto, $QTPRODVEND) {
		$params = array(
            'CDCLIENTE' =>	$CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'CDPRODUTO' => $CDPRODUTO
		);
		$produtoDia = $this->entityManager->getConnection()->fetchAssoc("PRODUTO_CONSUMIDO_DIA", $params);
		return floatval($produtoDia['PRODUTO_DIA']) + $QTPRODVEND > floatval($limiteProduto);
	}

    public function imprimeCupomCredPessoal($CDFILIAL, $CDCAIXA, $CDCONSUMIDOR, $CDCLIENTE) {
        $result = array();
        $dadosImpressora = $this->impressaoutil->getDadosImpressora($CDFILIAL, $CDCAIXA);
        $dadosConsumidor = $this->getSaldocons($CDCLIENTE, $CDCONSUMIDOR);
        if (!$dadosImpressora['error']){
            $dadosImpressora = $dadosImpressora['dadosImpressora'];
            $printerParams = $this->impressaoutil->buscaParametrosImpressora($dadosImpressora['IDMODEIMPRES']);
            $text = $this->impressaoutil->montaTextoCreditoPessoal($printerParams, $dadosConsumidor[0]);
            if (!$printerParams['impressaoFront']){
                $comandos = new Command();
                $comandos->text($text);
                $comandos->cutPaper();

                $respostaPonte = $this->impressaoutil->requisicaoPonte($dadosImpressora, $comandos);
                if ($respostaPonte['error']){
                    $result['error'] = true;
                    $result['message'] = $respostaPonte['message'];
                }
                else {
                    $result['error'] = false;
                    $result['message'] = null;
                }
            }
            else {
                $result['error'] = false;
                $result['message'] = array('RECEIPT' => $text);
            }
        }
        else {
            $result['error'] = true;
            $result['message'] = "Caixa sem impressora cadastrada.";
        }
        return $result;
    }

    public function getSaldocons($CDCLIENTE, $CDCONSUMIDOR){
         $params = array(
             'CDCLIENTE' => $CDCLIENTE,
             'CDCONSUMIDOR' => $CDCONSUMIDOR
         );
         return $this->entityManager->getConnection()->fetchAll("GET_CONSUMER_SALDOCONS_NMFAMILISALD", $params);
    }

    public function encryptDelphi ($senha){

        $argumentos = array(
            new \SoapVar($senha, XSD_STRING, 'string', "http://www.w3.org/2001/XMLSchema")
        );

        $parametros = array(
            'rdm'    => 'Bib_html.rdmDBGeralHtm',
            'method' => 'GetCriptoPass',
            'args'   => $argumentos,
        );
        return $this->callDelphiMethod($senha, $parametros);
    }

    public function decryptDelphi ($senha){
        $argumentos = array(
            new \SoapVar($senha, XSD_STRING, 'string', "http://www.w3.org/2001/XMLSchema")
        );

        $parametros = array(
            'rdm'    => 'Bib_html.rdmDBGeralHtm',
            'method' => 'GetUnCriptoPass',
            'args'   => $argumentos,
        );
        return $this->callDelphiMethod($senha, $parametros);
    }

    private function callDelphiMethod($senha, $parametros){
        //@todo rename to criptografaSenhaDelphi

		$url = 'http://security.teknisa.com/comwebservice/service.asmx?wsdl';

        $opcoes = array(
			'encoding'    => 'ISO-8859-1',
			'trace'       => true,
			'exceptions'  => true,
			'compression' => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        );

        try {

            $cliente = new \SoapClient($url, $opcoes);
            $retorno = $cliente->executeCOMMethod($parametros);
            $criptografia = $retorno->executeCOMMethodResult;

        }catch (\SoapFault $e) {
			throw new \Exception($e->getMessage());
        }

        return $criptografia;

    }

    public function saveCDSENHACONS ($CDIDCONSUMID, $CDSENHACONS) {
        try {
            $params = array(
                ':CDIDCONSUMID' => $CDIDCONSUMID,
                ':CDSENHACONS' => $CDSENHACONS
            );
            $this->entityManager->getConnection()->executeQuery("UPDATE_SENHA_DELPHI", $params);
            return array (
                'error' => false
            );
        } catch (\Exception $e) {
            return array (
                'error' => true,
                'message' => $e->getMessage()
            );
        }
    }

    public function verificaSaldoCanc($CDCLIENTE, $CDCONSUMIDOR, $CDFILIAL, $CDFAMILISALD){
        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'CDFILIAL' => $CDFILIAL
            );
            $result = $this->entityManager->getConnection()->fetchAll("VERIFICA_SALDO_CANCELAMENTO", $params);
        }
        else {
            $result = $this->extratocons->consultaSaldo($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, '');
        }

        $saldo = 0;
        foreach ($result as $family){
            if ($family['CDFAMILISALD'] == $CDFAMILISALD){
                $saldo = $family['VRSALDCONFAM'];
            }
        }
        return floatval($saldo);
    }

    public function cancelaSaldo($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDTIPORECE, $CDFILIAL, $CDCAIXA, $VRMOVEXTCONS, $NRDEPOSICONS, $NRSEQMOVCAIXA, $DTABERCAIX, $NRORG, $DSOPEEXTCONS){
        $params = array(
            'CDCLIENTE'     => $CDCLIENTE,
            'CDCONSUMIDOR'  => $CDCONSUMIDOR,
            'CDFAMILISALD'  => $CDFAMILISALD,
            'DTMOVEXTCONS'  => new \DateTime(),
            'NRSEQMOVEXT'   => '001',
            'CDTIPORECE'    => $CDTIPORECE,
            'CDFILIAL'      => $CDFILIAL,
            'CDCAIXA'       => $CDCAIXA,
            'DSOPEEXTCONS'  => $DSOPEEXTCONS,
            'VRMOVEXTCONS'  => $VRMOVEXTCONS,
            'VRSALDCONEXT'  => 0,
            'IDTPMOVEXT'    => 'D',
            'NRDEPOSICONS'  => $NRDEPOSICONS,
            'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA,
            'NRSEQVENDA'    => null
        );
        $type = array(
            'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME,
            'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
        );

        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] === 'S'){
            // Insere crédito na EXTRATOCONS.
            $paramsOnline = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'CDFAMILISALD' => $CDFAMILISALD,
                'DTMOVEXTCONS' => new \DateTime(),
                'NRSEQMOVEXT' => '001',
                'CDTIPORECE' => $CDTIPORECE,
                'IDTPMOVEXT' => 'D',
                'DSOPEEXTCONS' => $DSOPEEXTCONS,
                'VRMOVEXTCONS' => $VRMOVEXTCONS,
                'VRSALDCONEXT' => 0,
                'CDFILIAL' => $CDFILIAL,
                'CDCAIXA' => $CDCAIXA,
                'NRDEPOSICONS' => $NRDEPOSICONS,
                'NRSEQMOVCAIXA' => $NRSEQMOVCAIXA,
                'IDIMPEXTRATO' => 'S'
            );
            $paramsOnline['DTMOVEXTCONS'] = $paramsOnline['DTMOVEXTCONS']->format('d/m/Y H:i:s');
            $this->extratocons->insereExtratocons($paramsOnline);
        }

        // Apenas para evoluir o saldo do banco local.
        $primeiraInsercao = self::primeiraInsercao($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
        if ($this->databaseUtil->databaseIsOracle()){
            $params['DTABERCAIX'] = \DateTime::createFromFormat('Y-m-d H:i:s', $DTABERCAIX);
            $this->entityManager->getConnection()->executeQuery("INSERT_EXTRATOCONS", $params, $type);
            $primeiraInsercao['DTMOVEXTCONS'] = \DateTime::createFromFormat('Y-m-d H:i:s', $primeiraInsercao['DTMOVEXTCONS']);
        }
        else {
            $params['DTABERCAIX'] = \DateTime::createFromFormat('Y-m-d H:i:s.u', $DTABERCAIX);
            $this->entityManager->getConnection()->executeQuery("INSERT_EXTRATOCONS", $params, $type);
            $primeiraInsercao['DTMOVEXTCONS'] = \DateTime::createFromFormat('Y-m-d H:i:s.u', $primeiraInsercao['DTMOVEXTCONS']);
        }

        $params = array(
            'CDCLIENTE' => $CDCLIENTE,
            'CDCONSUMIDOR' => $CDCONSUMIDOR,
            'DTMOVEXTCONS' => $primeiraInsercao['DTMOVEXTCONS'],
            'NRORG' => $NRORG,
        );
        $this->evoluiSaldo->EvoluiSaldoFunction($params);

        // Configura array de retorno com os nomes do cliente/consumidor/familia para a impressão.
        $creditDetails = self::buscaNomes($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD);
        $creditDetails['VRSALDCONEXT'] = $this->buscaSaldoExtrato($CDCLIENTE, $CDCONSUMIDOR, $CDFAMILISALD, $CDFILIAL)['VRSALDCONEXT'];

        return $creditDetails;
    }

    public function imprimeCupomDebitoConsumidor($CDFILIAL, $CDCAIXA, $CDCLIENTE, $CDCONSUMIDOR, $NRSEQVENDA){
        try {
            $dadosConsumidor = $this->getConsumerParamsDebitoConsumidor($CDFILIAL, $CDCAIXA, $NRSEQVENDA);
            $text = $this->impressaoutil->montaTextoDebitoConsumidor($dadosConsumidor, $CDFILIAL, $CDCAIXA);
            return $text;
        } catch(\Exception $e) {

        }
    }

    private function getConsumerParamsDebitoConsumidor($CDFILIAL, $CDCAIXA, $NRSEQVENDA) {
        $params = array(
            'CDFILIAL' => $CDFILIAL,
            'CDCAIXA' => $CDCAIXA,
            'NRSEQVENDA' => $NRSEQVENDA
        );
        $dadosConsumidor = $this->entityManager->getConnection()->fetchAssoc("GET_CONSUMER_SALE", $params);
        return $dadosConsumidor;
    }

    public function consultaSaldo($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $NRORG){
        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
            $primeiraInsercao = self::primeiraInsercao($CDCLIENTE, $CDCONSUMIDOR);

            if (!empty($primeiraInsercao)){
                if ($this->databaseUtil->databaseIsOracle()){
                    $primeiraInsercao = \DateTime::createFromFormat('Y-m-d H:i:s', $primeiraInsercao['DTMOVEXTCONS']);
                }
                else {
                    $primeiraInsercao = \DateTime::createFromFormat('Y-m-d H:i:s.u', $primeiraInsercao['DTMOVEXTCONS']);
                }
            }
            else {
                $primeiraInsercao = null;
            }

            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR,
                'DTMOVEXTCONS' => $primeiraInsercao,
                'NRORG' => $NRORG,
            );
            $this->evoluiSaldo->EvoluiSaldoFunction($params);

            $params = array (
                'CDFILIAL' => $CDFILIAL,
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR
            );
            $saldo = $this->entityManager->getConnection()->fetchAll("GET_CONSULTA_SALDO", $params);
        }
        else {
            $saldo = $this->extratocons->consultaSaldo($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, '');
        }

        foreach ($saldo as &$entry){
            $entry['VRSALDCONFAM'] = number_format(floatval($entry['VRSALDCONFAM']), 2, ',', '');
        }

        return $saldo;
    }

    public function buscaCreditoFidelidade($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $NRORG){
        $dadosFilial = $this->entityManager->getConnection()->fetchAssoc("BUSCA_DADOS_FILIAL", array('CDFILIAL' => $CDFILIAL));
        if ($dadosFilial['IDEXTCONSONLINE'] !== 'S'){
            throw new \Exception('A filial não está configurada para acessar o sistema de crédito fidelidade.');
        }
        else {
            $params = array(
                'CDCLIENTE' => $CDCLIENTE,
                'CDCONSUMIDOR' => $CDCONSUMIDOR
            );
            $tipoCons = $this->entityManager->getConnection()->fetchAssoc("TIPO_CONSUMIDOR", $params);

            $saldo = $this->extratocons->consultaFidelidade($CDCLIENTE, $CDCONSUMIDOR);
            if (empty($saldo)) throw new \Exception('Consumidor não encontrado no sistema de Crédito Fidelidade.');
            else $VRSALDCONEXT = floatval($saldo[0]['VRSALDCONEXT']);

            return array(
                'IDPERALTDESCFID' => $tipoCons['IDPERALTDESCFID'],
                'VRSALDCONEXT' => $VRSALDCONEXT
            );
        }
    }

    public function atualizaSaldoMoviClie($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR){
        $this->util->updateSaldoCons($CDFILIAL, $CDCLIENTE, $CDCONSUMIDOR, $CDOPERADOR);
    }

}