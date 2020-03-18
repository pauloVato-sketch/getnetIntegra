<?php

namespace Service;


class Register{

	protected $entityManager;
	protected $util;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Util\Util $util) {
		$this->entityManager = $entityManager;
		$this->util = $util;
	}

	public function getRegisterOpeningDate($CDFILIAL, $CDCAIXA){
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA
		);
		return $this->entityManager->getConnection()->fetchAssoc("GET_REGISTER_OPENING_DATE", $params);
	}

	public function getRegisterClosingPayments($CDFILIAL, $CDCAIXA, $DTABERCAIX){
		$params = array(
			'CDFILIAL' => $CDFILIAL,
			'CDCAIXA' => $CDCAIXA,
			'DTABERCAIX' => new \DateTime($DTABERCAIX)
		);
		$type = array(
			'DTABERCAIX' => \Doctrine\DBAL\Types\Type::DATETIME
		);

		return $this->entityManager->getConnection()->fetchAll("GET_REGISTER_CLOSING_PAYMENTS", $params, $type);
	}
}