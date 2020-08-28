<?php

namespace Service;

use \Util\Exception;

class KDS {

	protected $entityManager;
	protected $util;

	public function __construct(\Doctrine\ORM\EntityManager $entityManager, \Util\Util $util){
		$this->entityManager = $entityManager;
		$this->util = $util;
	}

	public function getProductComposition($CDFILIAL, $releaseList){
		try {
			// Items will be stored here.
			$compositionAndProducts = array();

			foreach ($releaseList as $product){
				/*
				// check if the main product is grouped or not
				$params = array(
					$CDFILIAL,
					$product['NRVENDAREST'],
					$product['NRCOMANDA'],
					$product['NRPRODCOMVEN']
				);
				$productDetails = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_ITCOMANDAVEN_QUANT", $params);
				*/

				// Gets details for the main product.
				$params = array(
					$CDFILIAL,
					$product['NRPEDIDOFOS'],
					$product['NRITPEDIDOFOS']
				);
				$productDetails = $this->entityManager->getConnection()->fetchAssoc("SQL_GET_MAIN_PRODUCT", $params);
				$productDetails['DTHREXIBATRASO'] = new \Datetime($productDetails['DTHREXIBATRASO'], new \DateTimeZone(date_default_timezone_get()));
				// Stores the main product into the array.
				array_push($compositionAndProducts, $productDetails);
				// Checks if the main product and any of its subproducts has subproducts and adds them to the array.
				$compositionAndProducts = $this->productCompositionTracer($CDFILIAL, $product['NRPEDIDOFOS'], $product['NRITPEDIDOFOS'], $compositionAndProducts);
			}

			return $compositionAndProducts;
		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception($e->getMessage());
		}
	}

	public function productCompositionTracer($CDFILIAL, $NRPEDIDOFOS, $NRITPEDIDOFOS, $compositionAndProducts){
		// Gets the product composition.
		$params = array(
			$CDFILIAL,
			$NRPEDIDOFOS,
			$NRITPEDIDOFOS
		);
		$result = $this->entityManager->getConnection()->fetchAll("SQL_GET_PRODUCT_COMPOSITION", $params);

		foreach ($result as $item){
			// Formats date.
			$item['DTHREXIBATRASO'] = new \Datetime($item['DTHREXIBATRASO'], new \DateTimeZone(date_default_timezone_get()));
			// Stores subproduct into the array.
			array_push($compositionAndProducts, $item);
			// Checks if subproduct has any further products.
			$compositionAndProducts = $this->productCompositionTracer($CDFILIAL, $item['NRPEDIDOFOS'], $item['NRITPEDIDOFOS'], $compositionAndProducts);
		}
		return $compositionAndProducts;
	}

	private function getNewKDSOPERACAOTEMP() {
		$this->util->newCode('KDSOPERACAOTEMP');
		return $this->util->getNewCode('KDSOPERACAOTEMP', 10);
	}

	public function insertKDSOPERACAOTEMP(array $operationParamsArray, $operationName, $NRORG) {
		$operation = array(
			'operation' => $operationName,
			'params'   => $operationParamsArray
		);
		$params = array(
			'NRSEQOPERACAO' => $this->getNewKDSOPERACAOTEMP(),
			'DSOPERACAO'    => json_encode($operation),
			'NRORG'			=> $NRORG
		);
		
		$this->entityManager->getConnection()->executeQuery("INSERT_KDSOPERACAOTEMP", $params);
	}
}