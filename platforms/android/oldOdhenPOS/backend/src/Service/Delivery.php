<?php

namespace Service;

use \Util\Exception;

class Delivery{

	protected $entityManager;
	protected $util;
	protected $waiterMessage;


	public function __construct(
		\Doctrine\ORM\EntityManager $entityManager, 
		\Util\Util $util, 
		\Util\WaiterMessage $waiterMessage
	) {
		$this->entityManager     = $entityManager;
		$this->util              = $util;
		$this->waiterMessage     = $waiterMessage;
	}

	public function getDeliveryOrders($params){
		try{
			$paramsOrders = array(
				'CDFILIAL' => $params['CDFILIAL'],
				'CDLOJA' 	 => $params['CDLOJA']
			);
			$orders = $this->entityManager->getConnection()->fetchAll("GET_DELIVERY_ORDERS", $paramsOrders);
			
			//Busca tipos de recebimento e produtos de cada pedido
			foreach ($orders as $key => $order) {
				$paramsOrder = array(
					'CDFILIAL'		=> $params['CDFILIAL'],
					'NRVENDAREST' 	=> $order['NRVENDAREST']
				);
				$orders[$key]['DATASALE'] = $this->getMovcaixadlv($paramsOrder);
				$orders[$key]['PRODUTOS'] = $this->getProdutosDlv($paramsOrder);				
			}


			return $orders;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}

	public function getProdutosDlv($params){
		try{
			$params = array(
				'CDFILIAL'		=> $params['CDFILIAL'],
				'NRVENDAREST' 	=> $params['NRVENDAREST']
			);
			return $this->entityManager->getConnection()->fetchAll("GET_PRODUTOS_PEDIDODLV", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}

	public function getMovcaixadlv($params){
		try{
			$params = array(
				'CDFILIAL'		=> $params['CDFILIAL'],
				'NRVENDAREST' 	=> $params['NRVENDAREST']
			);
			return $this->entityManager->getConnection()->fetchAll("GET_TIPORECEBE_DELIVERY", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
		
		
	}

	public function getDeliveryOrdersControl($params){
		try{
			$params = array(
				'CDFILIAL'	 => $params['CDFILIAL'],
				'CDLOJA' 	 => $params['CDLOJA']
			);

			$orders = $this->entityManager->getConnection()->fetchAll("GET_DELIVERY_ORDERS_CONTROL", $params);

			return $orders;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}

	public function getInfoOrder($CDFILIAL, $NRVENDAREST){
		try{
			$params = array(
				'CDFILIAL' => $CDFILIAL,
				'NRVENDAREST' 	 => $NRVENDAREST
			);

			$orders = $this->entityManager->getConnection()->fetchAll("GET_INFO_DELIVERY_ORDER", $params);

			return $orders;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}

	public function getVendedores($params){
		try{
			$params = array(
				':CDFILIAL'	=>	$params['CDFILIAL']
			);
			$vendedores = $this->entityManager->getConnection()->fetchAll("GET_VENDEDORES", $params);
			return $vendedores;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function getVendedoresChegada($params){
		try{
			$params = array(
				':CDFILIAL'	=>	$params['CDFILIAL'],
				':CDLOJA'	=>	$params['CDLOJA']
			);
			$vendedores = $this->entityManager->getConnection()->fetchAll("GET_VENDEDORES_PEDIDOS_DLV", $params);
			return $vendedores;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}	

	public function saidaComandas($params){
		try{
			$params = $this->setNrEntrega($params);

			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();

			foreach ($params['ORDERS'] as $order) {

				$paramsEntregadorDlv = array(
					'DTALTOPER'			=> $params['DTALTOPER'],
					'CDVENDEDOR'		=> $params['ENTREGADOR'],
					'CDFILIAL'			=> $params['CDFILIAL'],
					'CDCAIXA'			=> $params['CDCAIXA'],
					'NRENTREGA'			=> $order['NRENTREGA'],
					'NRSEQVENDA'		=> $order['NRSEQVENDA']
				);

				$types = array(
					'DTALTOPER' => \Doctrine\DBAL\Types\Type::DATE
				);

				$connection->executeQuery("UPDATE_SAIDA_ENTREGADOR", $paramsEntregadorDlv, $types);
				$connection->executeQuery("UPDATE_IMP_VENDA", $paramsEntregadorDlv);
				
				$paramsOrder = array(
					'DTSAIDACMD'		=> $params['DTSAIDACMD'],
					'CDFILIAL'			=> $params['CDFILIAL'],
					'NRCOMANDA'			=> $order['DSCOMANDA'],
					'NRVENDAREST'  		=> $order['NRVENDAREST']
				);

				$types = array(
					'DTSAIDACMD' => \Doctrine\DBAL\Types\Type::DATE
				);

				$connection->executeQuery("UPDATE_SAIDA_COMANDA", $paramsOrder, $types);
				
				$paramsEntregadorVendaRest = array(
					'CDVENDEDOR'		=> $params['ENTREGADOR'],
					'CDFILIAL'			=> $params['CDFILIAL'],
					'NRVENDAREST'		=> $order['NRVENDAREST']
				);

				$connection->executeQuery("UPDATE_ENTREGADOR_VENDAREST", $paramsEntregadorVendaRest);


			}

			$connection->commit();
			return array('Pedidos entregues ao entregador.');
						
		} catch(\Exception $e){
			$connection->rollback();
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function setNrEntrega($params){
		foreach ($params['ORDERS'] as &$order) {
			$this->util->newCode('NRENTREGAPEDVND'.$params['CDFILIAL'].$params['CDCAIXA']);
			$order['NRENTREGA'] = $this->util->getNewCode('NRENTREGAPEDVND'.$params['CDFILIAL'].$params['CDCAIXA'], 10);
		}
		return $params;
	}

	public function checkOutOrders($params){
		try{
			$connection = $this->entityManager->getConnection();
			$connection->beginTransaction();
 			foreach ($params['ORDERS'] as $order) {
				$paramsEntregadorChegada = array(
					'DTCHEGAVENDA'  =>	$params['DTALTOPER'],
					'CDFILIAL'		=>	$params['CDFILIAL'],
					'CDCAIXA'		=>	$params['CDCAIXA'],
					'NRSEQVENDA'	=>	$order['NRSEQVENDA']
				);

				$types = array(
					'DTCHEGAVENDA' => \Doctrine\DBAL\Types\Type::DATE
				);

				$this->entityManager->getConnection()->executeQuery("UPDATE_ENTREGADOR_CHEGADA", $paramsEntregadorChegada, $types);

				$paramsChegadaComanda = array(
					'DTCHEGACMD'	=>	$params['DTCHEGACMD'],
					'CDFILIAL'		=>	$params['CDFILIAL'],
					'NRCOMANDA'	    =>	$order['NRCOMANDA']
				);

				$types = array(
					'DTCHEGACMD' => \Doctrine\DBAL\Types\Type::DATE
				);

				$this->entityManager->getConnection()->executeQuery("UPDATE_CHEGADA_COMANDA", $paramsChegadaComanda, $types);

			}

			$connection->commit();
			return array('Entregador chegou com sucesso.');

		} catch(\Exception $e){
			$connection->rollback();
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}

	public function checkOrdersEntregadorDlv($params){
		try{
			$params = array(
				'CDFILIAL'		=>	$params['CDFILIAL'],
				'CDLOJA'		=>	$params['CDLOJA'],
				'CDVENDEDOR'	=>	$params['ENTREGADOR']
			);

			$orders = $this->entityManager->getConnection()->fetchAll("GET_ORDERS_ENTREGADOR_DELIVERY", $params);

			return $orders;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function getTaxaEntrega($CDFILIAL, $NRVENDAREST){
		try{
			$params = array(
				':CDFILIAL'		=>	$CDFILIAL,
				':NRVENDAREST'	=>	$NRVENDAREST
			);

			$VRACRCOMANDA = $this->entityManager->getConnection()->fetchAssoc("GET_TAXA_ENTREGA", $params)['VRACRCOMANDA'];

			return $VRACRCOMANDA;

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function saveMovcaixadlv($params){
		try{
			$params = array(
				':CDFILIAL'			=>	$params['CDFILIAL'],
				':NRVENDAREST'		=>	$params['NRVENDAREST'],
				':NRSEQMOVDLV'		=>	$params['NRSEQMOVDLV'],
				':IDTIPOMOVIVEDLV'	=>	$params['IDTIPOMOVIVEDLV'],
				':CDCLIENTE'		=>	$params['CDCLIENTE'],
				':CDCONSUMIDOR'		=>	$params['CDCONSUMIDOR'],
				':CDTIPORECE'		=>	$params['CDTIPORECE'],
				':VRMOVIVENDDLV'	=>	$params['VRMOVIVENDDLV']
			);
			$this->entityManager->getConnection()->executeQuery("INSERT_MOVCAIXADLV_PEDIDO", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function deleteMovcaixadlv($params){
		try{
			$params = array(
				':CDFILIAL'			=>	$params['CDFILIAL'],
				':NRVENDAREST'		=>	$params['NRVENDAREST']
			);
			$this->entityManager->getConnection()->executeQuery("DELETE_MOVCAIXADLV", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function getClienteConsumidorDlv($params){
		try{
			$params = array(
				':CDFILIAL'			=>	$params['CDFILIAL'],
				':NRVENDAREST'		=>	$params['NRVENDAREST']
			);

			return $this->entityManager->getConnection()->fetchAssoc("GET_CLIENTE_CONSUMIDOR_DLV", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function updateComandaPendente($params){
		try{
			$params = array(
				':CDFILIAL'			=>	$params['CDFILIAL'],
				':NRCOMANDA'		=>	$params['NRCOMANDA']
			);
			$this->entityManager->getConnection()->executeQuery("UPDATE_COMANDA_PENDENTE", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}

	public function getNrNotaFiscal($params){
		try{
			$params = array(
				':CDFILIAL'			=>	$params['CDFILIAL'],
				':NRVENDAREST'		=>	$params['NRVENDAREST']
			);

			return $this->entityManager->getConnection()->fetchAssoc("GET_NRNOTAFISCALCE", $params);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}	
	}

	public function concludeOrderDlv($params){
		try{
			$DTHRATUAL = new \DateTime();
			$params = array(
				':CDFILIAL'			=>	$params['CDFILIAL'],
				':NRCOMANDA'		=>	$params['NRCOMANDA'],
				':DTCHEGACMD'		=>  $DTHRATUAL,
				':DTSAIDACMD'		=>	$DTHRATUAL
			);

			$types = array(
				'DTCHEGACMD' => \Doctrine\DBAL\Types\Type::DATE,
				'DTSAIDACMD' => \Doctrine\DBAL\Types\Type::DATE
			);

			$this->entityManager->getConnection()->executeQuery("CONCLUDE_ORDERDLV", $params, $types);

		} catch(\Exception $e){
			Exception::logException($e);
			throw new \Exception ($e->getMessage());
		}
	}
}