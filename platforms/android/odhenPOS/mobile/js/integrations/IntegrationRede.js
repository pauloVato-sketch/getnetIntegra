function IntegrationRede(){
	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi possível obter o retorno da integração.';

	this.integrationPayment = function(operatorData, currentRow) {
		if(!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {
				'paymentValue': currentRow.VRMOVIVEND * 100,
				'paymentType': currentRow.tiporece.IDTIPORECE
			};

			cordova.plugins.GertecRede.payment(JSON.stringify(params), window.returnIntegration, function(){});
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult) {
		var integrationResult = self.formatResponse();

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
      	if(!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {};
			cordova.plugins.GertecRede.reversal(JSON.stringify(params), window.returnIntegration, function(){});
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
		self.integrationPaymentResult(resolve, javaResult);
	};

	this.formatResponse = null;

	this.invalidIntegrationInstance = function(){
		return {
            error: true,
            message: MESSAGE_INTEGRATION_FAIL
        };
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationRede', IntegrationRede);
});