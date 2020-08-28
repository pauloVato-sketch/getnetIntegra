<?php

namespace Odhen\API\Util;

class EvoluiSaldo {

    protected $connection;
    protected $databaseUtil;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Odhen\API\Util\Database $databaseUtil){
        $this->connection = $entityManager->getConnection();
        $this->databaseUtil  = $databaseUtil;
    }

    /*0*/
    public function EvoluiSaldoFunction(&$conditions){
        if (isset($conditions['CDCLIENTE'])){
            if (isset($conditions['CDCONSUMIDOR'])){
                if (isset($conditions['DTMOVEXTCONS'])){
                    if (isset($conditions['NRORG'])){
                        $allFamilies = $this->getSaldoAllFamilies($conditions);  //adqUltMovExtrato
                        foreach ($allFamilies as &$familie) {
                            $familie['DTMOVEXTCONS'] = $conditions['DTMOVEXTCONS'];
                            $forFamilie = $this->getSaldoForFamilie($familie);   //adqMovExtrato
                            foreach ($forFamilie as $key => &$valuesFamilie){
                                if ($key == 0){
                                    $this->CalculoSaldo($forFamilie[$key], $familie); //adqUltMovExtrato --> VRSALDCONEXT
                                }
                                $this->AtualizaMovimetacaoExtrato($forFamilie[$key], $key);
                                $SALDOFINAL = $forFamilie[$key]['SALDOFINAL'];
                                if (isset($forFamilie[$key+1])){
                                    $forFamilie[$key+1]['SALDOFINAL'] = $SALDOFINAL;
                                    $this->CalculoSaldoFinal($forFamilie[$key+1]);
                                }
                                $familie['SALDOFINAL'] = $SALDOFINAL;
                                if (!empty($familie)){
                                    $test = $this->GetVerificaSaldo($familie);
                                    if (empty($test)) {
                                       $this->InsertSaldoCons($familie);
                                    }
                                    else{
                                        $familie['NRORG'] = $conditions['NRORG'];
                                        $this->AtualizaSaldoCons($familie);
                                    }
                                }
                                else{
                                   return $arrayName = array();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /*1*/
    private function getSaldoAllFamilies($conditions){
        if ($conditions['CDCLIENTE'] == '')
            $conditions['TCDCLIENTE'] = 'T';
        else
            $conditions['TCDCLIENTE'] = ' ';
        if ($conditions['CDCONSUMIDOR'] == '')
            $conditions['TCDCONSUMIDOR'] = 'T';
        else
            $conditions['TCDCONSUMIDOR'] = ' ';
        $params = array(
            ':CDCLIENTE'    => $conditions['CDCLIENTE'],
            ':TCDCLIENTE'   => $conditions['TCDCLIENTE'],
            ':CDCONSUMIDOR' => $conditions['CDCONSUMIDOR'],
            ':TCDCONSUMIDOR'=> $conditions['TCDCONSUMIDOR'],
            ':DTMOVEXTCONS' => $conditions['DTMOVEXTCONS'],
            ':NRORG'        => $conditions['NRORG']
        );
        $type = array(
            'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        return $this->connection->fetchAll("GET_SALDO_ALL_FAMILIES", $params, $type);
    }

    /*2*/
    private function getSaldoForFamilie($conditions){
        $params = array(
            ':CDCLIENTE'    => $conditions['CDCLIENTE'],
            ':CDCONSUMIDOR' => $conditions['CDCONSUMIDOR'],
            ':CDFAMILISALD' => $conditions['CDFAMILISALD'],
            ':DTMOVEXTCONS' => $conditions['DTMOVEXTCONS'],
            ':NRORG'        => $conditions['NRORG']
        );
        $type = array(
            'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
        );
        return $this->connection->fetchAll("GET_SALDO_FOR_FAMILIES", $params, $type);
    }

    /*calculo 1 /*3*/
    private function CalculoSaldo(&$conditions, $familie){
        if ($conditions['IDTPMOVEXT'] == 'C'){
            $conditions['SALDOFINAL'] = floatval($familie['VRSALDCONEXT']) + floatval($conditions['VRMOVEXTCONS']);
        }
        else{
            $conditions['SALDOFINAL'] = floatval($familie['VRSALDCONEXT']) - floatval($conditions['VRMOVEXTCONS']);
        }
        return $conditions;
    }

    /*4*/
    private function AtualizaMovimetacaoExtrato($conditions, $key){
        $params = array(
            'SALDOFINAL'   =>  $conditions['SALDOFINAL'],
            'CDCLIENTE'    =>  $conditions['CDCLIENTE'],
            'CDCONSUMIDOR' =>  $conditions['CDCONSUMIDOR'],
            'CDFAMILISALD' =>  $conditions['CDFAMILISALD'],
            'DTMOVEXTCONS' =>  $conditions['DTMOVEXTCONS'],
            'NRSEQMOVEXT'  =>  $conditions['NRSEQMOVEXT'],
            'NRORG'        =>  $conditions['NRORG']
        );

        if ($this->databaseUtil->databaseIsOracle()){
            $params['DTMOVEXTCONS'] = \DateTime::createFromFormat('Y-m-d H:i:s', $params['DTMOVEXTCONS']);
            $type = array(
                'DTMOVEXTCONS' => \Doctrine\DBAL\Types\Type::DATETIME
            );
            return $this->connection->executeQuery("ATUALIZA_MOVIMENTACAO_EXTRATO", $params, $type);
        }
        else {
            $s = $params['DTMOVEXTCONS'];
            $s = substr($s, 8, 2).'/'.substr($s, 5, 2).'/'.substr($s, 0, 4).' '.substr($s, 11);
            $params['DTMOVEXTCONS'] = $s;
            return $this->connection->executeQuery("ATUALIZA_MOVIMENTACAO_EXTRATO", $params);
        }
    }

    /*calculo 2 /*5*/
    private function CalculoSaldoFinal(&$conditions){
        if ($conditions['IDTPMOVEXT'] == 'C'){
            $conditions['SALDOFINAL'] = floatval($conditions['SALDOFINAL']) + floatval($conditions['VRMOVEXTCONS']);
        } else {
            $conditions['SALDOFINAL'] = floatval($conditions['SALDOFINAL']) - floatval($conditions['VRMOVEXTCONS']);
        }
        return $conditions;
    }

    /*6*/
    private function GetVerificaSaldo($conditions) {
        $params = array(
            ':CDCLIENTE'    => $conditions['CDCLIENTE'],
            ':CDCONSUMIDOR' => $conditions['CDCONSUMIDOR'],
            ':CDFAMILISALD' => $conditions['CDFAMILISALD'],
            ':NRORG'        => $conditions['NRORG']
        );
        return $this->connection->fetchAll("GET_VERIFICA_SALDO", $params);
    }

    /*7*/
    private function InsertSaldoCons($conditions) {
        $params = array(
            ':CDCLIENTE'    => $conditions['CDCLIENTE'],
            ':CDCONSUMIDOR' => $conditions['CDCONSUMIDOR'],
            ':CDFAMILISALD' => $conditions['CDFAMILISALD'],
            ':VRSALDCONFAM' => $conditions['SALDOFINAL'],
            ':NRORG'        => $conditions['NRORG']
        );
        return $this->connection->executeQuery("INSERT_SALDO_CONS", $params);
    }

    /*8*/
    private function AtualizaSaldoCons($conditions) {
        $params = array(
            ':CDCLIENTE'     => $conditions['CDCLIENTE'],
            ':CDCONSUMIDOR'  => $conditions['CDCONSUMIDOR'],
            ':CDFAMILISALD'  => $conditions['CDFAMILISALD'],
            ':VRSALDCONFAM'  => $conditions['SALDOFINAL'],
            ':NRORG'         => $conditions['NRORG']
        );
        return $this->connection->executeQuery("ATUALIZA_SALDO_CONS", $params);
    }

}