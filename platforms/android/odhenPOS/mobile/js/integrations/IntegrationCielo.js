function IntegrationCielo(PaymentRepository){

    var self = this;
    var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';    

    var PAYMENTSTATUS_COMPLETED = '1';
    var PAYMENTSTATUS_REFUNDED  = '1';

    this.integrationPayment = function(operatorData, currentRow){
        if(!!window.ZhCieloAutomation) {
            PaymentRepository.findOne().then(function(payment){
                var newOrder = {
                    create: false,
                    accountValue: 0
                };
                // cria nova Ordem caso não existir pagamento por integração na venda
                if(_.isEmpty(_.find(payment.TIPORECE, Array('TRANSACTION.status', true)))){
                    newOrder.create = true;
                    newOrder.accountValue = (payment.DATASALE.TOTALVENDA) * 100;
                }
                newOrder = JSON.stringify(newOrder);
                
                var paymentValue = (currentRow.VRMOVIVEND.toFixed(2)) * 100;
                
                ZhCieloAutomation.payment(newOrder, paymentValue, currentRow.tiporece.IDTIPORECE);
            });
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };
    
    this.integrationPaymentResult = function(resolve, javaResult){
        var integrationResult = self.formatResponde();
        javaResult = self.handleJavaResult(javaResult);

        if (javaResult.statusCode === PAYMENTSTATUS_COMPLETED){
            integrationResult.error = false;
            integrationResult.data = {
                CDNSUHOSTTEF: javaResult.cieloCode,
                NRCONTROLTEF: javaResult.orderId,
                CDBANCARTCR: javaResult.brand,
                PAYMENTCONFIRMATION: PAYMENT_CONFIRMATION,
                REMOVEALLINTEGRATIONS: REMOVE_ALL_INTEGRATIONS
            };
        } else {
            integrationResult.message = javaResult.message;
        }

        resolve(integrationResult);
    };

    // o cancelamento da cielo é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

    this.reversalIntegration = function(tiporeceData){
        if(!!window.ZhCieloAutomation) {
            ZhCieloAutomation.reversalPayment(tiporeceData.CDNSUHOSTTEF, tiporeceData.NRCONTROLTEF);
        } else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
    };

    this.reversalIntegrationResult = function(resolve, javaResult){
        var integrationResult = self.formatResponde();
        javaResult = self.handleJavaResult(javaResult);

        if (javaResult.statusCode === PAYMENTSTATUS_REFUNDED){
            integrationResult.error = false;
        } else {
            integrationResult.message = javaResult.message;
        }

        resolve(integrationResult);
    };

    // cielo não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    this.formatResponde = function(){
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
}

Configuration(function(ContextRegister) {
    ContextRegister.register('IntegrationCielo', IntegrationCielo);
});