<?php

namespace Odhen\API\Util;

use Doctrine\DBAL\Cache\QueryCacheProfile;

class MultipleDatabaseConnection extends \Doctrine\DBAL\Connection {

    protected $databaseDriver;
    protected $aditionalQueryClassMSDE = array();
    protected $aditionalQueryClassOracle = array();

    private function _getQuery($queryName, $databaseDriver) {
        switch($databaseDriver){
            case 'oci8':
                $queryMapping = OracleQuery::QUERY_MAPPING;
                if (!isset($queryMapping[$queryName])) {
                    $queryMapping = $this->aditionalQueryClassOracle;
                    if (!isset($queryMapping[$queryName])) {
                        $query = $this->_getQuery($queryName, 'sqlsrv');
                    } else {
                        $query = $queryMapping[$queryName];
                    }
                } else {
                    $query = $queryMapping[$queryName];
                }
                break;
            case 'sqlsrv':
                $queryMapping = MSDEQuery::QUERY_MAPPING;

                if (!isset($queryMapping[$queryName])) {
                    $queryMapping = $this->aditionalQueryClassMSDE;
                    if (!isset($queryMapping[$queryName])) {
                        $query = $queryName;
                    } else {
                        $query = $queryMapping[$queryName];
                    }
                } else {
                    $query = $queryMapping[$queryName];
                }
                break;
            default:
                throw Exception::invalidDataBase($databaseDriver);
        }

        return $query;
    }

	public function getQuery($queryName) {
		return $this->_getQuery($queryName, $this->databaseDriver);
	}

    public function executeQuery($queryName, array $params = array(), $types = array(), QueryCacheProfile $qcp = null) {
        $query = $this->getQuery($queryName);
        return parent::executeQuery($query, $params, $types);
	}

    // tratamento para log de querys demoradas
    // public function executeQuery($queryName, array $params = array(), $types = array(), QueryCacheProfile $qcp = null) {
    //     $query = $this->getQuery($queryName);
    //     $a = microtime(true);
    //     $ret = parent::executeQuery($query, $params, $types);
    //     $b = microtime(true);
    //     $tempo = round($b - $a);
    //     if ($tempo >= 2){
    //         $logText = "[" . date("d/m/Y H:i:s") . "]" . 
    //             PHP_EOL . 'tempo de execucao: ' . $tempo . PHP_EOL . "query: " . $query . PHP_EOL .
    //             PHP_EOL . '***************************************' . PHP_EOL;
    //         // executa esc
    //         file_put_contents('../../../LOG1912/' . date('H_i_s') . '.txt', $logText, FILE_APPEND);
    //     }
    //     return $ret;
    // }

    public function setDataBaseDriver($databaseDriver) {
        $this->databaseDriver = $databaseDriver;
    }

    public function setAditionalQueryClasses($aditionalQueryClassMSDE, $aditionalQueryClassOracle) {
        $classMSDE = new $aditionalQueryClassMSDE();
        $classOracle = new $aditionalQueryClassOracle();

        $this->aditionalQueryClassMSDE = $classMSDE->getArray();
        $this->aditionalQueryClassOracle = $classOracle->getArray();
    }

}