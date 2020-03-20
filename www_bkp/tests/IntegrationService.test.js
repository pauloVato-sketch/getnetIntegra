chai.should();

describe("Unit Test - IntegrationService", function(){
	var integrations = integrationData();

	integrations.forEach(function(integration){
		describe("# " + integration.name, function(){
			it("Integration Success", function(done){
				var currentRow = defaultCurrentRow();
				setOperatorRepositoryIntegration(integration.IDTPTEF);
				setIntegrationPayment(integration.IDTPTEF, "SUCCESS");
				ApplicationContext.IntegrationService.integrationPayment(currentRow).then(function(integrationResult){
					this.defaultTestResultSuccess(integrationResult);
					// dados retornados da integração
					for(var i in integration.integrationResult){
						integration.integrationResult[i].should.equals(integrationResult.data.eletronicTransacion.data[i]);
					}
					done();
				});
			});
			it("Integration Error", function(done){
				setOperatorRepositoryIntegration(integration.IDTPTEF);
				setIntegrationPayment(integration.IDTPTEF, "ERROR");
				ApplicationContext.IntegrationService.integrationPayment(defaultCurrentRow()).then(function(integrationResult){
					this.defaultTestResultError(integrationResult);
					done();
				});
			});
			it("Cancel Integration Success", function(done){
				setCancelIntegration(integration.IDTPTEF, "SUCCESS");
				ApplicationContext.IntegrationService.cancelIntegration(integration).then(function(integrationResult){
					this.defaultTestResultSuccess(integrationResult);
					done();
				});
			});
			it("Cancel Integration Error", function(done){
				setCancelIntegration(integration.IDTPTEF, "ERROR");
				ApplicationContext.IntegrationService.cancelIntegration(integration).then(function(integrationResult){
					this.defaultTestResultError(integrationResult);
					done();
				});
			});
			it("Integration Confirmation Success", function(done){
				if (integration.integrationResult.PAYMENTCONFIRMATION){
					var arrCompleteIntegrations = getArrIntegrations(integration);
					setCompleteIntegration(integration.IDTPTEF, "SUCCESS");
					ApplicationContext.IntegrationService.completeIntegration(arrCompleteIntegrations).then(function(integrationResult){
						this.defaultTestResultSuccess(integrationResult);
						done();
					});
				} else {
					done();
				}
			});
			it("Integration Confirmation Error", function(done){
				if (integration.integrationResult.PAYMENTCONFIRMATION){
					var arrCompleteIntegrations = getArrIntegrations(integration);
					setCompleteIntegration(integration.IDTPTEF, "ERROR");
					ApplicationContext.IntegrationService.completeIntegration(arrCompleteIntegrations).then(function(integrationResult){
						this.defaultTestResultError(integrationResult);
						done();
					});
				} else {
					done();
				}
			});
			it("Refound Integration Success", function(done){
				// o estorno das integrações são testados pensando em que houve 2 transações por venda
				var arrReversalIntegrations = getArrIntegrations(integration);				
				setReversalIntegration(integration.IDTPTEF, "SUCCESS");
				ApplicationContext.IntegrationService.reversalIntegration(removePaymentSale, arrReversalIntegrations).then(function(integrationResult){
					this.defaultTestResultSuccess(integrationResult);
					done();
				});
			});
			it("Refound Integration Error", function(done){
				var arrReversalIntegrations = getArrIntegrations(integration);
				setReversalIntegration(integration.IDTPTEF, "ERROR");
				ApplicationContext.IntegrationService.reversalIntegration(removePaymentSale, arrReversalIntegrations).then(function(integrationResult){
					this.defaultTestResultError(integrationResult);
					done();
				});
			});
		});			
	});
});

function integrationData(){
	return Array(
		{
			"name": "Cappta",
			"IDTPTEF": "2",
			"integrationResult": {
				"IDTPTEF": "2",
				"CDNSUHOSTTEF": 1,
				"NRCONTROLTEF": "1",
				"CDBANCARTCR": "122",
				"STLPRIVIA": "1 VIA",
				"STLSEGVIA": "2 VIA",
				"PAYMENTCONFIRMATION": false,
				"REMOVEALLINTEGRATIONS": false
			}
		},
		{
			"name": "Rede",
			"IDTPTEF": "4",
			"integrationResult": {
				"IDTPTEF": "4",
				"CDNSUHOSTTEF": 2,
				"CDBANCARTCR": "VISA",
				"PAYMENTCONFIRMATION": false,
				"REMOVEALLINTEGRATIONS": false
			}
		},
		{
			"name": "SiTEF",
			"IDTPTEF": "5",
			"integrationResult": {
				"IDTPTEF": "5",
				"CDNSUHOSTTEF": 2,
				"CDBANCARTCR": "VISA",
				"STLPRIVIA": "1 VIA",
				"STLSEGVIA": "2 VIA",
				"PAYMENTCONFIRMATION": false,
				"REMOVEALLINTEGRATIONS": false
			}
		},
		{
			"name": "Cielo LIO",
			"IDTPTEF": "7",
			"integrationResult": {
				"IDTPTEF": "7",
				"CDNSUHOSTTEF": 2,
				"CDBANCARTCR": "VISA",
				"PAYMENTCONFIRMATION": false,
				"REMOVEALLINTEGRATIONS": false
			}
		}
	);
}

function defaultCurrentRow(){
	return {
		tiporece: {},
		editValue: false,
		eletronicTransacion: {status: false, data: ApplicationContext.IntegrationService.integrationData()}
	};	
}

function defaultTestResultSuccess(integrationResult){
	// resultado é um objeto
	integrationResult.should.be.a("object");
	// resultado sem erro
	integrationResult.error.should.equals(false);
	// string de retorno vazia
	integrationResult.message.should.have.lengthOf(0);
}

function defaultTestResultError(integrationResult){
	// resultado é um objeto
	integrationResult.should.be.a("object");
	// resultado com erro
	integrationResult.error.should.equals(true);
	// string de retorno preenchida
	integrationResult.message.should.be.a('string');
}

function removePaymentSale(arrTiporece){
	return new Promise.resolve(true);
}

function setOperatorRepositoryIntegration(IDTPTEF){
	ApplicationContext.OperatorRepository.findOne = function(){
		return new Promise(function(resolve){
			resolve({
				"IDTPTEF": IDTPTEF
			});
		});
	};	
}

function setIntegrationPayment(IDTPTEF, type){
	var IntegrationClass = ApplicationContext.IntegrationService.chooseIntegration(IDTPTEF);
	var javaResult = defaultJavaResultIntegration(IDTPTEF, type);

	IntegrationClass.integrationPayment = function(operatorData, currentRow){
		window.returnIntegration(javaResult);
	};

	// moch específico para Rede - Poynt
	if (IDTPTEF === "4"){
		IntegrationClass.getTransactioncode = function(NRCOMANDA, NRVENDAREST){
			return new Promise.resolve([{
				"SEQTEF": "1"
			}]);
		};
	}
}

function defaultJavaResultIntegration(IDTPTEF, type){
	if (IDTPTEF === "2"){
		if (type === "SUCCESS"){
			return JSON.stringify(
				{
					"responseCode": "0",
					"acquirerUniqueSequentialNumber": 1,
					"uniqueSequentialNumber": 1,
					"administrativeCode": "1",
					"cardBrandId": "122",  
					"customerReceipt": "1 VIA", 
					"merchantReceipt": "2 VIA" 
			    }
			);		
		} else {
			return JSON.stringify(
				{
			        "responseCode": "1",
			        "reason": "error"
			    }
			);		
		}		
	} else if (IDTPTEF === "4"){
		if (type === "SUCCESS"){
			return JSON.stringify(
				{
					"STATUS": "AUTHORIZED",
					"AUTE_NSU": 2,
					"FLAG": "VISA",
					"AUTO": "1",
					"customerReceipt": "1 VIA", 
					"merchantReceipt": "2 VIA" 
			    }
			);		
		} else {
			return JSON.stringify(
				{
			        "STATUS": "CANCELED"
			    }
			);		
		}		
	} else if (IDTPTEF === "5"){
		if (type === "SUCCESS"){
			return JSON.stringify(
				{
					"error": false,
					"data": {
						"uniqueSequentialNumber": 2,
						"cardBrandName": "VISA",  
						"customerReceipt": "1 VIA", 
						"merchantReceipt": "2 VIA",
						"transactionDate": "201801191433"
					}
			    }		
			);
		} else {
			return JSON.stringify(
				{
					"error": true,
					"message": "error"
			    }	
			);
		}		
	} else if (IDTPTEF === "7"){
		if (type === "SUCCESS"){
			return JSON.stringify(
				{
					"statusCode": '1',
					"cieloCode": 2,
					"orderId": "20190709161213",  
					"brand": "VISA"
			    }
			);		
		} else {
			return JSON.stringify(
				{
			        "statusCode": '2',
			        "message": "error"
			    }
			);		
		}		
	} 
}

function setCancelIntegration(IDTPTEF, type){
	var IntegrationClass = ApplicationContext.IntegrationService.chooseIntegration(IDTPTEF);
	var javaResult = defaultJavaResultCancel(IDTPTEF, type);

	IntegrationClass.cancelIntegration = function(tiporeceData){
		window.returnIntegration(javaResult);
	};
}

function defaultJavaResultCancel(IDTPTEF, type){
	if (IDTPTEF === "2"){
		return this.defaultJavaResultReversal(IDTPTEF, type);
	} else if (IDTPTEF === "4"){
		return this.defaultJavaResultReversal(IDTPTEF, type);
	} else if (IDTPTEF === "5"){
		return this.defaultJavaResultReversal(IDTPTEF, type);
	} else if (IDTPTEF === "7"){
		return this.defaultJavaResultReversal(IDTPTEF, type);
	}
}

function setCompleteIntegration(IDTPTEF, type){
	var IntegrationClass = ApplicationContext.IntegrationService.chooseIntegration(IDTPTEF);
	var javaResult = defaultJavaResultComplete(IDTPTEF, type);

	IntegrationClass.completeIntegration = function(tiporeceData){
		window.returnIntegration(javaResult);
	};
}

function getArrIntegrations(integration){
	return Array(integration.integrationResult, integration.integrationResult);
}

function setReversalIntegration(IDTPTEF, type){
	var IntegrationClass = ApplicationContext.IntegrationService.chooseIntegration(IDTPTEF);
	var javaResult = defaultJavaResultReversal(IDTPTEF, type);

	IntegrationClass.reversalWaiting = Array();
	IntegrationClass.reversalIntegration = function(tiporeceData){
		window.returnIntegration(javaResult);
	};
}

function defaultJavaResultReversal(IDTPTEF, type){
	if (IDTPTEF === "2"){
		if (type === "SUCCESS"){
			return JSON.stringify(
				{
			        "responseCode": "0",
			        "customerReceipt": "1 VIA", 
					"merchantReceipt": "2 VIA"
			    }
			);	
		} else {
			return JSON.stringify(
				{
			        "responseCode": "1",
			        "reason": "error"
			    }
			);	
		}		
	} else if (IDTPTEF === "4"){
		if (type === "SUCCESS"){
			return "AUTHORIZED"
		} else {
			return "CANCELED"
		}
	} else if (IDTPTEF === "5"){
		return this.defaultJavaResultIntegration(IDTPTEF, type);
	} else if (IDTPTEF === "7"){
		if (type === "SUCCESS"){
			return JSON.stringify(
				{
			        "statusCode": "1"
			    }
			);	
		} else {
			return JSON.stringify(
				{
			        "statusCode": "2",
			        "message": "error"
			    }
			);	
		}
	}
}