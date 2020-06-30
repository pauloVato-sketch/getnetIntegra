function IntegrationService(IntegrationCappta, IntegrationNTK, IntegrationRede, IntegrationSiTEF, IntegrationCielo,OperatorRepository) {
	var INTEGRATION_TYPE = {
		'2': IntegrationCappta,
		'3': IntegrationNTK,
		'4': IntegrationRede,
		'5': IntegrationSiTEF,
		'7': IntegrationCielo
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

			//[remove]
			IntegrationClass = self.chooseIntegration(operatorData.IDTPTEF);
			if (IntegrationClass){
				return new Promise(function(resolve) {
					window.returnIntegration = _.bind(IntegrationClass.integrationPaymentResult, IntegrationClass, resolve);
					//[remove]
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

    //[remove]
	this.chooseIntegration = function(IDTPTEF){
		// seleciona serviço de integração
		return INTEGRATION_TYPE[IDTPTEF];
	};

	this.cancelIntegration = function(tiporeceData){
		return new Promise(function(resolve) {
		    //[remove]
			IntegrationClass = self.chooseIntegration(tiporeceData.IDTPTEF);
            window.returnIntegration = _.bind(IntegrationClass.cancelIntegrationResult, IntegrationClass, resolve);
            //[remove]
            IntegrationClass.cancelIntegration(tiporeceData);
		}.bind(this));
	};

	this.completeIntegration = function(integrations){
		// pega IDTPTEF da primeira posição pois será o mesmo para qualquer recebimento
		return new Promise(function(resolve) {
			if (window.cordova.plugins.IntegrationService){
				window.returnIntegration = _.bind(self.completeIntegrationResult, self, resolve);
				return true;
			} else {
				resolve(self.invalidIntegrationValues());
				return false;
			}
		}.bind(this));
	};	

	this.callRecursive = null;

    this.getRefundFromSaleCancelResult = function (integrations){
        return JSON.stringify(
        	{"refundType" : integrations.IDTIPORECE,
        	 "refundValue": integrations.VRMOVIVEND,
    		 "refundDate" : integrations.TRANSACTIONDATE,
    		 "refundCV"   : integrations.NRCONTROLTEF
    		 }
    	);
    };

	this.reversalIntegration = function(removePaymentSale, integrations){
        //chamada para iniciar processo de estorno pela integração
    	return new Promise(function(reversalResolve) {
			//verifica-se o tamanho do array de transações
			if(integrations.length>0){
				self.reversalWaiting = _.clone(integrations);	
				//callRecursive se torna recursiveReversalIntegration com ambiente self com argumentos fixos reversalResolve
				//e o array de transações
				self.callRecursive = _.bind(self.recursiveReversalIntegration, self, reversalResolve,integrations);
				//chama-se a função com o último parametro faltante
				self.callRecursive(removePaymentSale);
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

		new Promise(function(resolve){
		    //definimos um parametro do objeto global window como uma função que é a reversalIntegrationResult
		    //com ambiente self e argumento resolve
			window.returnIntegration = _.bind(self.reversalIntegrationResult, self, resolve);
			//chama-se a função que executa a chamada direta da integração
			self.reversalIntegrationOnly(integrationToReverse);
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
    //[remove]
    IntegrationCappta.formatResponse = self.formatResponse;
	IntegrationNTK.formatResponse = self.formatResponse;
	IntegrationRede.formatResponse = self.formatResponse;
	IntegrationSiTEF.formatResponse = self.formatResponse;

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
			NRCARTBANCO: ''
		};
	};














    ////////////////CODIGO ALTERADO DA INTEGRAÇÃO REDE 208~259//////////////

	this.reversalIntegrationResult = function(resolve, javaResult){
	    //Função alocada no window.returnIntegration() e que faz a chamada abaixo

    	self.integrationPaymentResult(resolve, javaResult);
    };

    this.integrationPaymentResult = function(resolve, javaResult) {
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

    this.reversalIntegrationOnly = function(tiporeceData){
            //Verificamos a existência do plugin no momento
          	if(!!window.cordova && !!window.cordova.plugins.IntegrationService) {
    			//Definimos os parametros como sendo os dados necessários para realizar o reembolso em forma de string JSON
    			var params = self.getRefundFromSaleCancelResult(tiporeceData);
                //Chama-se a função da integração(KT) com os parametros,a função que é pra onde o código seguirá caso sucesso,e null
    			window.cordova.plugins.IntegrationService.refund(params, window.returnIntegration, null);
    		} else {
    			window.returnIntegration(self.invalidIntegrationInstance());
    		}
    };

    this.completeIntegrationResult = function(){
          return true;
    };

    this.invalidIntegrationInstance = function(){
    	return {
           error: true,
           message: MESSAGE_INTEGRATION_FAIL
        };
    };



    ////////////////CÓDIGO PROVENIENTE DE PAYMENTSERVICE.JS     263~283//////////////////////////////

    this.getPaymentFromCurrentRow = function (currentRow){
        return JSON.stringify(
       		{"paymentType": currentRow.tiporece.IDTIPORECE,
       		 "paymentValue": currentRow.VRMOVIVEND,
       		 "paymentNSU": "123"}
       	);
    };

    this.callPayment = function(currentRow){
        var payment = self.getPaymentFromCurrentRow(currentRow);
        OperatorRepository.findOne().then(function(operatorData) {
        	currentRow.eletronicTransacion.data.DSENDIPSITEF = operatorData.DSENDIPSITEF;
        	currentRow.eletronicTransacion.data.CDLOJATEF = operatorData.CDLOJATEF;
           	currentRow.eletronicTransacion.data.CDTERTEF = operatorData.CDTERTEF;
        	currentRow.eletronicTransacion.data.NRCARTBANCO = '';
        	currentRow.eletronicTransacion.data.IDTIPORECE = '';
        	window.cordova.plugins.IntegrationService.payment(payment, window.returnIntegration, null);
       	});
    };
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationService', IntegrationService);
});