function IntegrationStone(templateManager,ScreenService,OperatorRepository){

    var self = this;
    var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';

    this.integrationPayment = function(operatorData, currentRow){

        if(!!window.cordova.plugins) {
            var params = self.getPaymentFromCurrentRow(currentRow);
            window.cordova.plugins.IntegrationService.payment(params,window.returnIntegration,null);
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

//    this.setMessage = function(message) {
//        templateManager.containers.login.getWidget("pagsegpayment").getField("userInterfacePag").value(_.toUpper(message));
//    };
//
//    this.setLabel = function(label) {
//    	templateManager.containers.login.getWidget("pagsegpayment").label = label;
//    };
//
//    this.promptBoolean = function(message) {
//    	ScreenService.confirmMessage(message, 'question',
//    		function(){
//    			self.continueSitefProcess(SITEF_YES);
//    		},
//    		function(){
//    			self.continueSitefProcess(SITEF_NO);
//    		}
//    	);
//    };
//
//    this.promptCommand = function(message, minLength, maxLength, tipoCampo) {
//    	self.setMessage(message);
//        self.hideUserInterfaces();
//    	var widget = templateManager.containers.login.getWidget("pagsegpayment");
//    	widget.currentRow.tipoCampo = tipoCampo;
//    	widget.getAction("btnConfirm").isVisible = true;
//    	widget.getAction("btnBack").isVisible = true;
//
//    	var field = widget.getField("userInput");
//   		field.isVisible = true;
//   		field.minlength = minLength;
//   		field.maxlength = maxLength;
//   		field.setValue("");
//
//   		self.hideUserInterfaces();
//   	};
//
//    this.hideUserInterfaces = function() {
//    	var widget = templateManager.containers.login.getWidget("pagsegpayment");
//    	widget.getAction("btnBack").isVisible = widget.getAction("btnConfirm").isVisible =
//    		widget.getField("userInput").isVisible = false;
//    };
//
//   this.showCancelButton = function() {
//    	templateManager.containers.login.getWidget("pagsegpayment").getAction("btnBack").isVisible = true;
//   };

    this.integrationPaymentResult = function(resolve, javaResult){
         console.log("JAVARES");
         console.log(javaResult);
         //Realiza validação dos dados,e envia a resposta de volta com o resolve
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
                integrationResult.error = javaResult.error;
            	integrationResult.message = javaResult.message;
            }
         } else {
         	integrationResult.message = MESSAGE_NULL_RESPONSE;
         }
         resolve(integrationResult);
    };

    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        //Verificamos a existência do plugin no momento
        if(!!window.cordova.plugins) {
        	//Definimos os parametros como sendo os dados necessários para realizar o reembolso em forma de string JSON
        	var params = self.getRefundFromSaleCancelResult(tiporeceData);
            //Chama-se a função da integração(KT) com os parametros,a função que é pra onde o código seguirá caso sucesso,e null
            window.cordova.plugins.IntegrationService.refund(params, window.returnIntegration, null);
        } else {
        	window.returnIntegration(self.invalidIntegrationInstance());
        }

    };

    this.reversalIntegrationResult = function(resolve, javaResult){
        var integrationResult = self.formatResponse();
        javaResult = self.handleJavaResult(javaResult);
        console.log("java ESTORNO");
        console.log(javaResult);

        if (javaResult.errorCode == 0){
            integrationResult.error = false;
        } else {
            integrationResult.message = javaResult.message;
        }

        resolve(integrationResult);
    };

    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };

    this.formatResponse = function(){
        return {
            error: true,
            message: '',
            data: {}
        };
    };

    this.invalidIntegrationInstance = function(){
        return {
            statusCode: '2',
            message: MESSAGE_INTEGRATION_FAIL
        };
    };

    this.handleJavaResult = function(javaResult){
        if(typeof(javaResult) === 'string')
            javaResult = JSON.parse(javaResult);

        return javaResult;
    };

    this.getPaymentFromCurrentRow = function (currentRow){
       return JSON.stringify(
            {"paymentType": currentRow.tiporece.IDTIPORECE,
             "paymentValue": currentRow.VRMOVIVEND,
             "paymentNSU": "123"
            }
       );
    };

     this.getRefundFromSaleCancelResult = function (integrations){
        return JSON.stringify(
         	{"refundType" : integrations.IDTIPORECE,
           	 "refundValue": integrations.VRMOVIVEND,
        	 "refundDate" : integrations.TRANSACTIONDATE,
        	 "refundCV"   : integrations.NRCONTROLTEF,
        	 "TRANSACTIONCODE":integrations.TRANSACTIONCODE,
             "TRANSACTIONID":integrations.TRANSACTIONID
        	 }
        );
     };



}

Configuration(function(ContextRegister) {
    ContextRegister.register('IntegrationStone', IntegrationStone);
});



/*var NRCARTBANCO = integrationResponse.data.binCard + integrationResponse.data.lastNumbersCard;
    						var transactionDate = integrationResponse.data.date;
    						transactionDate = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
    						integrationResponse.data.eletronicTransacion = currentRow.eletronicTransacion;
    						integrationResponse.data.eletronicTransacion.data.CDBANCARTCR = integrationResponse.data.cardBrandName? integrationResponse.data.cardBrandName: '';
    						integrationResponse.data.eletronicTransacion.data.STLPRIVIA = '';
    						integrationResponse.data.eletronicTransacion.data.STLSEGVIA = '';
    						integrationResponse.data.eletronicTransacion.data.CDNSUHOSTTEF = integrationResponse.data.uniqueSequentialNumber;
    						integrationResponse.data.eletronicTransacion.data.TRANSACTIONDATE = transactionDate;
    						integrationResponse.data.eletronicTransacion.data.NRCONTROLTEF= integrationResponse.data.CV;
    						//alterar nome dos campos nas outras integrações
    						integrationResponse.data.eletronicTransacion.data.IDTIPORECE= integrationResponse.data.OperationType;
    						integrationResponse.data.eletronicTransacion.data.NRCARTBANCO = NRCARTBANCO;
    						integrationResponse.data.eletronicTransacion.data.VRMOVIVEND = currentRow.VRMOVIVEND;
    						integrationResponse.data.VRMOVIVEND = currentRow.VRMOVIVEND;
    						integrationResponse.data.tiporece = currentRow.tiporece;
    						*/