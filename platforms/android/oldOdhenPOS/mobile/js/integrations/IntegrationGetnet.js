function IntegrationGetnet(){
	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi possível obter o retorno da integração.';

	this.integrationPayment = function(operatorData, currentRow) {

		if(!!window.cordova.plugins.IntegrationService) {
			var params = self.getPaymentFromCurrentRow(currentRow);
			window.cordova.plugins.IntegrationService.payment(params, window.returnIntegration, null);
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult) {

		var integrationResult = self.formatResponse();

        console.log("JavaResult:");
        console.log(javaResult);

		if(javaResult !== null) {
			if (!javaResult.error){
				integrationResult.error = false;
				javaResult = javaResult.data;
				var NRCARTBANCO = javaResult.binCard + javaResult.lastNumbersCard;
                var transactionDate = javaResult.date;
                transactionDate = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
				integrationResult.data = {
					CDBANCARTCR: javaResult.cardBrandName ? javaResult.cardBrandName : '',
					CDNSUHOSTTEF: javaResult.nsu,
					tiporece: javaResult.OperationType,
					VRMOVIVEND: javaResult.Value,
                    STLPRIVIA : "",
                    STLSEGVIA : "",
                    TRANSACTIONDATE : transactionDate,
                    NRCONTROLTEF: javaResult.CV,
                    IDTIPORECE: javaResult.OperationType,
                    NRCARTBANCO : NRCARTBANCO,
					PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
					REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
				};
			} else {
				integrationResult.message = javaResult.message;
			}
		} else {
			integrationResult.message = MESSAGE_NULL_RESPONSE;
		}
		console.log("resolving integration");
		console.log(integrationResult);
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
        console.log("Flamengooo");
        console.log(tiporeceData);
      	if(!!window.cordova.plugins.IntegrationService) {
			var params = self.getRefundFromSaleCancelResult(tiporeceData);
			window.cordova.plugins.IntegrationService.refund(params, window.returnIntegration,null);
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
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

	this.getRefundFromSaleCancelResult = function(tiporeceData){
	    console.log(tiporeceData.TRANSACTIONDATE);
		return JSON.stringify(
		{"refundType": tiporeceData.IDTIPORECE,
		 "refundValue": tiporeceData.VRMOVIVEND,
		 "refundDate" : tiporeceData.TRANSACTIONDATE,
		 "refundCV" : tiporeceData.NRCONTROLTEF
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