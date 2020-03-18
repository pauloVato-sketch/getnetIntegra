<?php
namespace Service;

class CallWaiter {

	protected $entityManager;


	public function __construct(\Doctrine\ORM\EntityManager $entityManager) {
		$this->entityManager     = $entityManager;
	}

	public function callWaiter($dataset) {
		$nracessouser = $dataset['NRACESSOUSER'];
		$tipoChamada  = $dataset['tipoChamada'];

		$params = array($nracessouser);
		// gera próximo código
		$nrchamada = $this->entityManager->getConnection()->fetchAssoc("SQL_MAX_CHAMADA", $params);

		$nrchamada = $nrchamada['NRCHAMADA'];

		if($nrchamada != 1){
			$nrchamada++;
		}

		// insere chamada na tabela CHAMADAWAITER
		$params = array($nracessouser, $nrchamada,  $tipoChamada);

		$this->entityManager->getConnection()->executeQuery("SQL_INSERE_CHAMADA", $params);
	}

	public function getCall() {
		$r_acessos = $this->entityManager->getConnection()->fetchAll("SQL_GET_CALL");

		return array(
			'funcao' => '1',
			'chamadas' => $r_acessos,
		);
	}


	public function answerTable($dataset){
			$nracessouser = $dataset["NRACESSOUSER"];
			$params = array($nracessouser);
			$this->entityManager->getConnection()->executeQuery("SQL_ATENDE_CHAMADA", $params);
	}

}