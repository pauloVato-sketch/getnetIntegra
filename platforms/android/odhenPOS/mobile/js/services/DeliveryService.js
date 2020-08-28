function DeliveryService(DeliveryRepository, DeliveryControlRepository, PaymentPayAccount, OperatorRepository, Query, OperatorValidateSupervisor, FiliaisLogin, ValidateConsumerPass,
						 CaixasLogin, VendedoresLogin, TrocaModoCaixa, FindTefSSLConnectionId, DeliverySendOrders, PedidosEntreguesRepository,
						 DeliveryCheckOutOrders, DeliveryPrint, Movcaixadlv, DeliveryReprintCupomFiscal, CancelDeliveryProduct, CancelDeliveryOrder,
						 ConcludeOrderDlv ) {

	
	this.getDeliveryOrders = function(params) {
		var query = Query.build()
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('CDLOJA').equals(params.CDLOJA);
		return DeliveryRepository.download(query);
	};

	this.setDataSourceControl = function(params) {
		var query = Query.build()
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('CDLOJA').equals(params.CDLOJA);

		return DeliveryControlRepository.download(query);
	};

	this.generatePayment = function(cdfilial, nrvendarest, status, saleCode, datasale, nrcomanda, email){
		var saleCodeObj = {
			'saleCode': saleCode
		};
		if(datasale.TROCO == undefined){
			datasale.TROCO = 0;
		}
		var dataSale = {
			'DATASALE':{
	            'TOTALVENDA': 0,
	            'FALTANTE': 0,
	            'VALORPAGO': 0,
	            'TROCO': datasale.TROCO,
	            'TOTAL': datasale.TOTAL,
	            'TOTALSUBSIDY': 0,
	            'REALSUBSIDY': 0,
	            // taxa de serviço
	            'VRTXSEVENDA': 0,
	            // desconto
	            'VRDESCONTO': 0,
	            'PCTDESCONTO': 0,
	            'TIPODESCONTO': 'P',
	            'FIDELITYDISCOUNT': 0,
	            'FIDELITYVALUE': 0,
              	'VRCOUVERT': 0
			}
		};
		var query = Query.build()
						.where('DELIVERY').equals(true)
						.where('CDFILIAL').equals(cdfilial)
						.where('NRVENDAREST').equals(nrvendarest)
						.where('saleCode').equals(saleCodeObj.saleCode)
						.where('DATASALE').equals(dataSale)
						.where('IDSTCOMANDA').equals(status)
						.where('NRCOMANDA').equals(nrcomanda)
						.where('EMAIL').equals(email);
		return PaymentPayAccount.download(query);
	};

	this.getVendedoresLogin = function(filial, vendedoresField) {
		var query = Query.build()
						.where('CDFILIAL').equals(filial);
		return VendedoresLogin.downloadSome(query, 1, vendedoresField.itemsPerPage);
	};

	this.entregarPedidos = function(pedidos, entregador){
    	return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							 .where('ORDERS').equals(pedidos)
							 .where('ENTREGADOR').equals(entregador)
							 .where('CDFILIAL').equals(operatorData.CDFILIAL)
							 .where('CDCAIXA').equals(operatorData.CDCAIXA);
			return DeliverySendOrders.download(query);
		});
	};

	this.getPedidosEntregues = function(entregador){
    	return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							 .where('ENTREGADOR').equals(entregador)
							 .where('CDFILIAL').equals(operatorData.CDFILIAL)
							 .where('CDLOJA').equals(operatorData.CDLOJA);
			return PedidosEntreguesRepository.download(query);
		});
	};

	this.chegadaPedidos = function(pedidos, entregador){
		return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							 .where('ORDERS').equals(pedidos)
							 .where('ENTREGADOR').equals(entregador)
							 .where('CDFILIAL').equals(operatorData.CDFILIAL)
							 .where('CDLOJA').equals(operatorData.CDLOJA)
							 .where('CDCAIXA').equals(operatorData.CDCAIXA);
			return DeliveryCheckOutOrders.download(query);
		});
	};

	this.printDelivery = function(orders){
		return OperatorRepository.findOne().then(function (operatorData){
			orders = _.map(orders, function(order){
				order.CDCAIXA = operatorData.CDCAIXA;
				return order;
			});
			var query = Query.build()
							.where('ORDERS').equals(orders);

			return DeliveryPrint.download(query);
		});
	};

	this.saveMovcaixadlv = function(params){
		return OperatorRepository.findOne().then(function (operatorData){
			var query = Query.build()
							.where('CDFILIAL').equals(operatorData.CDFILIAL)
							.where('RECEBIMENTOS').equals(params.RECEBIMENTOS)
							.where('NRVENDAREST').equals(params.NRVENDAREST);
			return Movcaixadlv.download(query);
		});	
	};

	this.reprintDeliveryCupomFiscal = function(orders){
		var query = Query.build()
						.where('ORDERS').equals(orders);
		return DeliveryReprintCupomFiscal.download(query);
	};

	this.deletarProduto = function(params){
		params.product = JSON.stringify(params.product);
		var query = Query.build()
						.where('chave').equals(params.saleCode)
						.where('modo').equals(params.modo)
						.where('NRCOMANDA').equals(params.NRCOMANDA)
						.where('NRVENDAREST').equals(params.NRVENDAREST)
						.where('produto').equals(params.product)
						.where('motivo').equals(params.motivo)
						.where('CDSUPERVISOR').equals(params.CDOPERADOR)
						.where('IDPRODPRODUZ').equals(params.IDPRODPRODUZ)
						.where('CDFILIAL').equals(params.CDFILIAL);
		return CancelDeliveryProduct.download(query);
	};

	this.cancelarPedido = function(params){
		var dataSale = {
			'DATASALE':{
	            'TOTALVENDA': 0,
	            'FALTANTE': 0,
	            'VALORPAGO': 0,
	            'TROCO': 0,
	            'TOTAL': 0,
	            'TOTALSUBSIDY': 0,
	            'REALSUBSIDY': 0,
	            // taxa de serviço
	            'VRTXSEVENDA': 0,
	            // desconto
	            'VRDESCONTO': 0,
	            'PCTDESCONTO': 0,
	            'TIPODESCONTO': 'P',
	            'FIDELITYDISCOUNT': 0,
	            'FIDELITYVALUE': 0
			}
		};
		var query = Query.build()
						.where('DELIVERY').equals(true)
						.where('saleCode').equals(params.saleCode)
						.where('DATASALE').equals(dataSale)
						.where('modo').equals(params.modo)
						.where('NRCOMANDA').equals(params.NRCOMANDA)
						.where('NRVENDAREST').equals(params.NRVENDAREST)
						.where('motivo').equals(params.motivo)
						.where('CDSUPERVISOR').equals(params.CDOPERADOR)
						.where('IDPRODPRODUZ').equals(params.IDPRODPRODUZ)
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('IDSTCOMANDA').equals(params.IDSTCOMANDA);
		return CancelDeliveryOrder.download(query);
	};

	this.concludeOrderDlv = function(params){
		var dataSale = {
			'DATASALE':{
	            'TOTALVENDA': 0,
	            'FALTANTE': 0,
	            'VALORPAGO': 0,
	            'TROCO': 0,
	            'TOTAL': 0,
	            'TOTALSUBSIDY': 0,
	            'REALSUBSIDY': 0,
	            // taxa de serviço
	            'VRTXSEVENDA': 0,
	            // desconto
	            'VRDESCONTO': 0,
	            'PCTDESCONTO': 0,
	            'TIPODESCONTO': 'P',
	            'FIDELITYDISCOUNT': 0,
	            'FIDELITYVALUE': 0
			}
		};
		var query = Query.build()
						.where('DELIVERY').equals(true)
						.where('saleCode').equals(params.saleCode)
						.where('DATASALE').equals(dataSale)
						.where('modo').equals(params.modo)
						.where('NRCOMANDA').equals(params.NRCOMANDA)
						.where('NRVENDAREST').equals(params.NRVENDAREST)
						.where('motivo').equals(params.motivo)
						.where('CDSUPERVISOR').equals(params.CDOPERADOR)
						.where('IDPRODPRODUZ').equals(params.IDPRODPRODUZ)
						.where('CDFILIAL').equals(params.CDFILIAL)
						.where('IDSTCOMANDA').equals(params.IDSTCOMANDA);
		return ConcludeOrderDlv.download(query);
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('DeliveryService', DeliveryService);
});