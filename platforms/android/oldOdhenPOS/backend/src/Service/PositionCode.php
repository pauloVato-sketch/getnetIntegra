<?php
namespace Service;

use \Util\Exception;

class PositionCode {

	protected $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager){
		$this->entityManager = $entityManager;
	}

	public function getCode($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRPOSICAO){
		try {
			$params = array(
				$CDFILIAL,
				$NRVENDAREST,
				$NRCOMANDA,
				$NRPOSICAO,
			);
			$result = $this->entityManager->getConnection()->fetchAll("SQL_GET_CODE", $params);
			if (empty($result)) return array();
			else return $result[0]['IDCHECKIN'];
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message('Erro ao buscar o código.'));
		}
	}

	public function codeExists($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRPOSICAO, $code){
		try {
			$params = array(
				$CDFILIAL,
				$NRVENDAREST,
				$NRCOMANDA,
				$NRPOSICAO,
				$code
			);
			$result = $this->entityManager->getConnection()->fetchAll("SQL_CHECK_CODE", $params);
			return !empty($result);
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message('Erro na verificação do código.'));
		}
	}

	public function insertCode($CDFILIAL, $NRVENDAREST, $NRCOMANDA, $NRPOSICAO, $code){
		try {
			$params = array(
				$CDFILIAL,
				$NRVENDAREST,
				$NRCOMANDA,
				$NRPOSICAO,
				$code
			);
			$result = $this->entityManager->getConnection()->executeQuery("SQL_INSERT_CODE", $params);
		} catch (\Exception $e){
			Exception::logException($e);
			$response->addMessage(new Message('Erro na inserção do código. Verifique se o sistema está configurado corretamente para utilizar este recurso.'));
		}
	}
}