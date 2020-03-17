function IntegrationCappta(){

    var self = this;
    var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';    

    var PAYMENTSTATUS_COMPLETED = '0';
    var PAYMENTSTATUS_REFUNDED  = '0';

    var AUTHKEY_PRODUCTION = '0360DAC1E8FC41A3ABF9329866A7AA16';
    var AUTHKEY_HOMOLOGATION = '795180024C04479982560F61B3C2C06E';

    this.integrationPayment = function(operatorData, currentRow){
        if(!!window.ZhCapptaAutomation) {
            var paymentType = currentRow.tiporece.IDTIPORECE === '1' ? 'credit' : 'debit';
            var paymentValue = currentRow.VRMOVIVEND.toFixed(2).replace(',', '').replace('.', '');
            // define se chava utilizada será de produção ou homologação
            var AUTHKEY = self.getAUTHKEY(operatorData.AMBIENTEPRODUCAO);
            currentRow.eletronicTransacion.data.AUTHKEY = AUTHKEY;

            ZhCapptaAutomation.payment(AUTHKEY, paymentType, paymentValue, '1');            
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

    this.getAUTHKEY = function(AMBIENTEPRODUCAO){
        return AMBIENTEPRODUCAO ? AUTHKEY_PRODUCTION : AUTHKEY_HOMOLOGATION;
    };
    
    this.integrationPaymentResult = function(resolve, javaResult){
        javaResult = JSON.parse(javaResult);
        var integrationResult = self.formatResponse();
        var CDNSUHOSTTEF;

        if (javaResult.responseCode === PAYMENTSTATUS_COMPLETED){
            /* CDNSUHOSTTEF = !!javaResult.acquirerUniqueSequentialNumber ? 
                javaResult.acquirerUniqueSequentialNumber : javaResult.uniqueSequentialNumber;
            em hambiente de homologação, acquirerUniqueSequentialNumber sempre retornou '0' */
            CDNSUHOSTTEF = javaResult.uniqueSequentialNumber;
            integrationResult.error = false;
            integrationResult.data = {
                CDNSUHOSTTEF: CDNSUHOSTTEF,
                NRCONTROLTEF: javaResult.administrativeCode,
                CDBANCARTCR: javaResult.cardBrandId,
                STLPRIVIA: javaResult.customerReceipt,
                STLSEGVIA: javaResult.merchantReceipt,
                PAYMENTCONFIRMATION: PAYMENT_CONFIRMATION,
                REMOVEALLINTEGRATIONS: REMOVE_ALL_INTEGRATIONS
            };
        } else {
            integrationResult.message = javaResult.reason;
        }

        resolve(integrationResult);
    };

    // o cancelamento da cappta é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        if(!!window.ZhCapptaAutomation) {
            ZhCapptaAutomation.sendPaymentReversal(tiporeceData.AUTHKEY, tiporeceData.NRCONTROLTEF);
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

    this.reversalIntegrationResult = function(resolve, javaResult){
        javaResult = JSON.parse(javaResult);
        var integrationResult = self.formatResponse();

        if (javaResult.responseCode === PAYMENTSTATUS_REFUNDED){
            integrationResult.error = false;
            integrationResult.data = {
                STLPRIVIA: javaResult.customerReceipt,
                STLSEGVIA: javaResult.merchantReceipt
            };
        } else {
            integrationResult.message = javaResult.reason;
        }

        resolve(integrationResult);
    };

    // cappta não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    this.formatResponse = null;

    this.invalidIntegrationInstance = function(){
        return JSON.stringify({
            responseCode: '1',
            reason: MESSAGE_INTEGRATION_FAIL
        });
    };
    
}

Configuration(function(ContextRegister) {
    ContextRegister.register('IntegrationCappta', IntegrationCappta);
});