<?php

namespace Odhen\API\Test;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Odhen\API\Util\MultipleDatabaseConnection;

class ConnectionMock extends MultipleDatabaseConnection {

    protected $recording = array();
    private $isMocking = false;
    private $isIgnoringChangesToDatabase = false;
    private $mocks = array();
    private $classMock;
    private $functionMock;
    private $folderMocks = "../tests/mocks";

    public function ignoreChangesToDatabase() {
        $this->isIgnoringChangesToDatabase = true;
    }

    public function beginTransaction() {
        if ($this->isMocking) {
            return true;
        } else {
            return parent::beginTransaction();
        }
    }

    public function getIsMocking(){
        return $this->isMocking;
    }

    public function commit() {
        if ($this->isMocking || $this->isIgnoringChangesToDatabase) {
            return true;
        } else {
            return parent::commit();
        }
    }

    public function rollback() {
        if ($this->isMocking) {
            return true;
        } else {
            return parent::rollback();
        }
    }

    public function setClassMock($classMock) {
        $classMock = preg_replace('/.*\\\\/', '', $classMock);
        $this->classMock = $classMock;
    }

    public function getClassMock() {
        if (!empty($this->classMock)) {
            return $this->classMock;
        } else {
            return 'Geral';
        }
    }

    public function setFunctionMock($classMock, $functionMock, $isMocking = false) {
        $functionMock = preg_replace('/.*\\\\/', '', $functionMock);
        $this->setClassMock($classMock);
        $this->functionMock = $functionMock;
        $this->isMocking = $isMocking;
        $this->recording = array();
        $this->mocks = array();
    }

    public function getFunctionMock() {
        if (!empty($this->functionMock)) {
            return $this->functionMock;
        } else {
            return 'Geral';
        }
    }

    public function executeQuery($query, array $params = array(), $types = array(), QueryCacheProfile $qcp = null) {
        if ($this->isMocking) {
            return true;
        } else {
            $result = parent::executeQuery($query, $params, $types, $qcp);
            return $result;
        }
    }

    public function fetchAssoc($statement, array $params = array(), array $types = array()) {
        if ($this->isMocking) {
            return $this->getMock();
        } else {
            $result = parent::fetchAssoc($statement, $params, $types);
            array_push($this->recording, $result);
            return $result;
        }
    }

    public function fetchAll($sql, array $params = array(), $types = array()) {
        if ($this->isMocking) {
            return $this->getMock();
        } else {
            $result = parent::fetchAll($sql, $params, $types);
            array_push($this->recording, $result);
            return $result;
        }
    }

    public function getMockJsonFileName() {
        return $this->folderMocks . "/" . $this->getClassMock() . '.' . $this->functionMock . ".mock.json";
    }

    public function saveRecording() {
        if (!$this->isMocking) {
            $text = json_encode($this->recording);
            if (!file_exists($this->folderMocks)) {
                mkdir($this->folderMocks, 0777, true);
            }
            file_put_contents($this->folderMocks . "/" . $this->getClassMock() . '.' . $this->functionMock . ".mock.json", $text);
        }
    }

    private function getMock() {
        if (empty($this->mocks)) {
            if (file_exists($this->folderMocks)) {
                $this->mocks = file_get_contents($this->folderMocks  . '/' . $this->getClassMock() . '.' . $this->functionMock . ".mock.json");
                $this->mocks = json_decode($this->mocks, true);
            }else{
                throw new \Exception("Arquivo de mocks". $this->folderMocks  . '/' . $this->getClassMock() . '.' . $this->functionMock . ".mock.json não encontrado, verifique seus testes.", 1);
            }
        }
        return array_shift($this->mocks);
    }



}