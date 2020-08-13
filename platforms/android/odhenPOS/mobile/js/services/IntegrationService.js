function IntegrationService(IntegrationCappta, IntegrationNTK, IntegrationRede, IntegrationSiTEF, IntegrationCielo, IntegrationGetnet,OperatorRepository) {
	var INTEGRATION_TYPE = {
		'2': IntegrationCappta,
		'3': IntegrationNTK,
		'4': IntegrationRede,
		'5': IntegrationSiTEF,
		'7': IntegrationCielo,
		'9': IntegrationGetnet
		};

	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;
	var IntegrationClass = null;
	var VALUES_NOT_FOUND = 'Tipo do TEF não reconhecido pelo sistema.';
	var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';
    var MESSAGE_NULL_RESPONSE = 'Não foi possível obter o retorno da integração.';

	this.reversalWaiting = Array();

	this.integrationPayment = function(currentRow) {
		return OperatorRepository.findOne().then(function(operatorData) {
			currentRow.eletronicTransacion.data.IDTPTEF = operatorData.IDTPTEF;

			IntegrationClass = self.chooseIntegration(operatorData.IDTPTEF);
			if (IntegrationClass){
				return new Promise(function(resolve) {
					window.returnIntegration = _.bind(IntegrationClass.integrationPaymentResult, IntegrationClass, resolve);
					IntegrationClass.integrationPayment(operatorData, currentRow);
				}).then(self.buildIntegrationResponse.bind(currentRow));				
			} else {
				return self.invalidIntegrationValues();
			}
		}.bind(this));
	};

	this.buildIntegrationResponse = function(integrationResult) {
		if (!integrationResult.error) {
			for (var i in this.eletronicTransacion.data){
				if (!!integrationResult.data[i]){
					this.eletronicTransacion.data[i] = integrationResult.data[i];
				}
			}
			this.eletronicTransacion.status = true;
			integrationResult.data = this;
		}

		return integrationResult;
	};

	this.chooseIntegration = function(IDTPTEF){
		// seleciona serviço de integração
		return INTEGRATION_TYPE[IDTPTEF];
	};

	this.cancelIntegration = function(tiporeceData){
		return new Promise(function(resolve) {
			IntegrationClass = self.chooseIntegration(tiporeceData.IDTPTEF);
            window.returnIntegration = _.bind(IntegrationClass.cancelIntegrationResult, IntegrationClass, resolve);
            IntegrationClass.cancelIntegration(tiporeceData);
		}.bind(this));
	};

	this.completeIntegration = function(integrations){
    	// pega IDTPTEF da primeira posição pois será o mesmo para qualquer recebimento
    	return new Promise(function(resolve) {
    		IntegrationClass = self.chooseIntegration(integrations[0].IDTPTEF);
    		if (IntegrationClass){
    			window.returnIntegration = _.bind(IntegrationClass.completeIntegrationResult, IntegrationClass, resolve);
    			IntegrationClass.completeIntegration();
    		} else {
    			resolve(self.invalidIntegrationValues());
    		}
    	}.bind(this));
    };

	this.callRecursive = null;

	this.reversalIntegration = function(removePaymentSale, integrations){
        // pega IDTPTEF da primeira posição pois será o mesmo para qualquer recebimento
        return new Promise(function(reversalResolve) {
        	IntegrationClass = self.chooseIntegration(integrations[0].IDTPTEF);
        	if(IntegrationClass){
        		self.reversalWaiting = _.clone(integrations);
            	self.callRecursive = _.bind(this.recursiveReversalIntegration, self, reversalResolve, Array());
            	self.callRecursive(removePaymentSale, IntegrationClass);
            } else {
            	reversalResolve(self.invalidIntegrationValues());
            }
        }.bind(this));
	};

	this.recursiveReversalIntegration = function(reversalResolve, data, removePaymentSale){
		// função recursiva utilizada para estornar todas as transações realizadas na venda
		var integrationToReverse = self.reversalWaiting.shift();
		var toRemove = {
			'CDTIPORECE': null,
			'CDNSUHOSTTEF': null,
			'DTHRINCOMVEN': null
		};
        console.log("Ativa o cyberpunk");
		new Promise(function(resolve){
		    //definimos um parametro do objeto global window como uma função que é a reversalIntegrationResult
		    //com ambiente self e argumento resolve
			window.returnIntegration = _.bind(IntegrationClass.reversalIntegrationResult, IntegrationClass, resolve);
            IntegrationClass.reversalIntegration(integrationToReverse);
		}.bind(this)).then(function(resolved){
			// armazena todos os retornos
			if (!resolved.error) {
				toRemove.CDTIPORECE = integrationToReverse.CDTIPORECE;
				toRemove.CDNSUHOSTTEF = integrationToReverse.CDNSUHOSTTEF;
				toRemove.DTHRINCOMVEN = integrationToReverse.DTHRINCOMVEN;
				resolved.data.REVERSEDNRCONTROLTEF = integrationToReverse.NRCONTROLTEF;
				resolved.data.toRemove = toRemove;	 
				data.push(resolved.data);
				resolved.data = data;		
				if (self.reversalWaiting.length > 0){
					// realiza estorno da próxima transação
					self.callRecursive(removePaymentSale);

				} else {
					// estorno realizado com sucesso
					reversalResolve(resolved);
				}					
			} else {
				// erro ao realizar estorno
				data.push(resolved.data);
				resolved.data = data;
				reversalResolve(resolved);
			}
		}.bind(this));
	};

	this.formatResponse = function(){
        return {
            error: true,
            message: '',
            data: {}
        };
    };

    this.invalidIntegrationValues = function(){
		var result = self.formatResponse();

		result.message = VALUES_NOT_FOUND;
		return result;
	};
    IntegrationCappta.formatResponse = self.formatResponse;
	IntegrationNTK.formatResponse = self.formatResponse;
	IntegrationRede.formatResponse = self.formatResponse;
	IntegrationSiTEF.formatResponse = self.formatResponse;
	IntegrationGetnet.formatResponse = self.formatResponse;

	this.integrationData = function() {
		return {
			IDTPTEF: null,
			CDNSUHOSTTEF: null,
			CDBANCARTCR: null,
			STLPRIVIA: '',
			STLSEGVIA: '',
			PAYMENTCONFIRMATION: false,
			REMOVEALLINTEGRATIONS: false,
			// Cappta
			AUTHKEY: null,
			NRCONTROLTEF: null,
			// SiTEF
			DSENDIPSITEF: '',
			CDLOJATEF: null,
			CDTERTEF: null,
			TRANSACTIONDATE: '',
			NRCARTBANCO: '',
			//PagSeguro
			TRANSACTIONCODE:'',
			TRANSACTIONID:''
		};
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationService', IntegrationService);
});