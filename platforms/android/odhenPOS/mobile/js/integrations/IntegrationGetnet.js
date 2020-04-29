function IntegrationGetnet(){
	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi pissível obter o retorno da integração.';

	this.integrationPayment = function(operatorData, currentRow) {

		if(!!window.cordova && !!window.cordova.plugins.IntegrationService) {
			var params = self.getPaymentFromCurrentRow(currentRow);

			window.cordova.plugins.IntegrationService.payment(params, window.returnIntegration, null);
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult) {
		console.log(resolve);
		console.log(javaResult);
		var integrationResult = {};
		
		if(javaResult !== null) {
			if (!javaResult.error){
				integrationResult.error = false;
				javaResult = javaResult.data;
				integrationResult.data = {
					CDBANCARTCR: javaResult.flag,
					CDNSUHOSTTEF: javaResult.nsu,
					NRCONTROLTEF: javaResult.AUTO,
					PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
					REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
				};
			} else {
				integrationResult.message = javaResult.message;
			}
		} else {
			integrationResult.message = MESSAGE_NULL_RESPONSE;
		}

		resolve(integrationResult);
	};

	// rede não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    // o cancelamento da rede é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
    	console.log(tiporeceData);
      	if(!!window.cordova && !!window.cordova.plugins.IntegrationService) {
			var params = self.getRefundFromSaleCancelResult(tiporeceData);
			console.log("refund");
			window.cordova.plugins.IntegrationService.refund(params, window.returnIntegration,null);
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
  		console.log("refund RESULT");
		self.integrationPaymentResult(resolve, javaResult);
	};

	this.formatResponse = function(){
		return{
			error:true,
			message:'',
			data:{}
		};
	};

	this.invalidIntegrationInstance = function(){
		return {
            error: true,
            message: MESSAGE_INTEGRATION_FAIL
        };
	};

	this.getPaymentFromCurrentRow = function(currentRow){
		return JSON.stringify(
		{"paymentType": currentRow.tiporece.IDTIPORECE,
		 "paymentValue": currentRow.VRMOVIVEND,
		 "paymentNSU" : "123"
		});
	};

	this.getRefundFromSaleCancelResult = function(integrations){
		return JSON.stringify(
		{"refundType": integrations.IDTIPORECE,
		 "refundValue": integrations.VRMOVIVEND,
		 "refundDate" : integrations.TRANSACTIONDATE,
		 "refundCV" : integrations.NRCONTROLTEF
		});
	};

	this.handleJavaResult = function(javaResult){
		if(typeof(javaResult) === 'string'){
			javaResult = JSON.parse(javaResult);
			return javaResult;
		}
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationGetnet', IntegrationGetnet);
});