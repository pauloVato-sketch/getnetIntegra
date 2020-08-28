<?php

namespace Odhen\API\Service;

class Client {
	
	protected $entityManager;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager)  {
		$this->entityManager = $entityManager;
	}

	public function doPayment() {
		return $this->entityManager->getConnection()->fetchAll("GET_CLIENTS");
	}

}