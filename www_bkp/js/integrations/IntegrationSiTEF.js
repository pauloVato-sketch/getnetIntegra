function IntegrationSiTEF(FiliaisLogin, PaymentRepository, Query, HomologacaoSitef, SSLConnectionId, OperatorRepository, ScreenService, WindowService, templateManager){

	var self = this;
	var PAYMENT_CONFIRMATION = false;
    var REMOVE_ALL_INTEGRATIONS = false;

    var MESSAGE_INTEGRATION_FAIL = 'Não foi possível chamar a integração. Sua instância não existe.';

    var PAYMENT_STATUS_COMPLETED = 0;
    var PAYMENT_STATUS_REFUNDED  = 0;

    var SITEF_YES = "0";
    var SITEF_NO  = "1";

	var PAYMENT_INVOICE = "";
	
	var PAYMENT_TYPE = {
		pagamento: {
			debito: 2,
			credito: 3,
			mercadoPago: 122
		},
		estorno: {
			mercadoPago: 123,
			credito: 210,
			debito: 211
		},
		geral: {
			testeComunicacao: 111,
			reimpressaoEspecifica: 113,
			reimpressaoUltimo: 114,
			enviaLogs: 121,
			carregaTabelas: 772
		}
	};
	this.paymentTypeConstants = function(){
		return PAYMENT_TYPE;
	};

	this.integrationPayment = function(operatorData, currentRow, resolve) {
		if(!!window.cordova) {
			self.mustCreatePaymentInvoice().then(function (createPaymentInvoice) {
				if(createPaymentInvoice) {
					self.createPaymentInvoice(operatorData, currentRow);
				} else {
					self.callPayment(operatorData, currentRow);
				}
			}.bind(this));
		} else {
            window.returnIntegration(self.invalidIntegrationInstance());
        }
	};

	this.integrationPaymentResult = function(resolve, javaResult, isRefund) {
		var integrationResult = self.formatResponse();
		ScreenService.closePopup();
		javaResult = self.handleJavaResult(javaResult);
		if (!javaResult.error){
			javaResult = javaResult.data;
			var transactionDate = javaResult.transactionDate;
			transactionDate = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
			var NRCARTBANCO = javaResult.binCard + javaResult.lastNumbersCard;
			integrationResult.error = false;
			integrationResult.data = {
				CDBANCARTCR: javaResult.cardBrandName,
				STLPRIVIA: javaResult.customerReceipt,
				STLSEGVIA: javaResult.merchantReceipt,
				CDNSUHOSTTEF: javaResult.uniqueSequentialNumber,
				TRANSACTIONDATE: transactionDate,
				NRCONTROLTEF: javaResult.PAYMENTINVOICE,
				IDTIPORECE: javaResult.IDTIPORECE,
				NRCARTBANCO: NRCARTBANCO,
				VRMOVIVEND: javaResult.VRMOVIVEND,
				PAYMENTCONFIRMATION : PAYMENT_CONFIRMATION,
				REMOVEALLINTEGRATIONS : REMOVE_ALL_INTEGRATIONS
			};

			resolve(integrationResult);
		} else {
			integrationResult.message = javaResult.message;
			integrationResult.data.IDTPTEF = '5';
			integrationResult.data.errorCode = javaResult.errorCode;
			resolve(integrationResult);
		}
	};

	this.handleJavaResult = function(javaResult){
        if(typeof(javaResult) === 'string')
            javaResult = JSON.parse(javaResult);

        return javaResult;
    };

	// o cancelamento da sitef é o próprio estorno 
    this.cancelIntegration = function(tiporeceData){
        self.reversalIntegration(tiporeceData);     
    };

    this.cancelIntegrationResult = function(resolve, javaResult){
        self.reversalIntegrationResult(resolve, javaResult);
    };

	// sitef não completa integração
    this.completeIntegration = function(){
        return true;
    };

    this.completeIntegrationResult = function(resolve, javaResult){
        return true;
    };    

    this.reversalIntegration = function(tiporeceData){
    	if(tiporeceData.IDTIPORECE === 'H'){
    		self.callReversalIntegration(tiporeceData);
    	}else{
    		tiporeceData.NRCARTBANCO = self.formatCardNumber(tiporeceData.NRCARTBANCO);
	    	ScreenService.showMessage('O cartão <br>' + tiporeceData.NRCARTBANCO + '<br> será estornado')
	    		.then(function(){
	    			self.callReversalIntegration(tiporeceData);
	    		}.bind(this)
	    	);
    	}
	};

	this.callReversalIntegration = function(tiporeceData){
		if(!!window.cordova) {
			tiporeceData.VRMOVIVEND = parseFloat(tiporeceData.VRMOVIVEND);
			OperatorRepository.findOne().then(function(operatorData) {
				self.getParameters(operatorData, tiporeceData.NRCONTROLTEF).then(function(reversalParameters){
					
					var estornos = PAYMENT_TYPE.estorno;
					switch (tiporeceData.IDTIPORECE) {
						case '1': reversalParameters.paymentType = estornos.credito; break;
						case '2': reversalParameters.paymentType = estornos.debito; break;
						case 'H': reversalParameters.paymentType = estornos.mercadoPago; break;
					}

					reversalParameters.paymentValue = tiporeceData.VRMOVIVEND.toFixed(2);
					reversalParameters.paymentDate = tiporeceData.TRANSACTIONDATE;
					reversalParameters.paymentNSU = tiporeceData.CDNSUHOSTTEF;
					reversalParameters.paymentHour = tiporeceData.NRCONTROLTEF.slice(8);
					reversalParameters.paymentAuth = reversalParameters.paymentVia = "";

					self.initSitefProcess(reversalParameters);
				}.bind(this));
			}.bind(this));
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

  	this.reversalIntegrationResult = function(resolve, javaResult){
		self.integrationPaymentResult(resolve, javaResult, true);
	};

	this.formatResponse = null;

	this.invalidIntegrationInstance = function(){
		return JSON.stringify({
            paymentTransactionStatus: 1,
            userMessage: MESSAGE_INTEGRATION_FAIL
        });
	};

	this.mustCreatePaymentInvoice = function(){
		return PaymentRepository.findOne().then(function(payment) {
			var filtered = _.filter(payment.TIPORECE, function(tiporece){
				dataPayment = tiporece.TRANSACTION.data;
				return tiporece.TRANSACTION.data.IDTPTEF === '5';
			});

			if(filtered.length === 0) {
				return true;
			} else {
				self.PAYMENT_INVOICE = filtered[0].TRANSACTION.data.NRCONTROLTEF;
				return false;
			}
		});
	};

	this.createPaymentInvoice = function(operatorData, currentRow){
		var now = new Date();
		var year = now.getFullYear().toString();
		var month = self.leftZero(now.getMonth() + 1);
		var day = self.leftZero(now.getDate());
		var hour = self.leftZero(now.getHours());
		var minutes = self.leftZero(now.getMinutes());
		var seconds = self.leftZero(now.getSeconds());

		self.PAYMENT_INVOICE = year + month + day + hour + minutes + seconds;

		self.callPayment(operatorData, currentRow);
	};

	this.leftZero = function(leftZero){
		if(leftZero < 10)
			leftZero = "0" + leftZero;

		return leftZero;
	};

	this.callPayment = function(operatorData, currentRow) {
		currentRow.eletronicTransacion.data.DSENDIPSITEF = operatorData.DSENDIPSITEF;
		currentRow.eletronicTransacion.data.CDLOJATEF = operatorData.CDLOJATEF;
		currentRow.eletronicTransacion.data.CDTERTEF = operatorData.CDTERTEF;
		currentRow.eletronicTransacion.data.NRCARTBANCO = '';
		currentRow.eletronicTransacion.data.IDTIPORECE = '';

		self.getParameters(operatorData, self.PAYMENT_INVOICE).then(function(sitefParams){

			var pagamentos = PAYMENT_TYPE.pagamento;
			switch (currentRow.tiporece.IDTIPORECE) {
				case '1': sitefParams.paymentType = pagamentos.credito; break;
				case '2': sitefParams.paymentType = pagamentos.debito; break;
				case 'H': sitefParams.paymentType = pagamentos.mercadoPago; break;
			}
			
			sitefParams.paymentValue = currentRow.VRMOVIVEND.toFixed(2);
			sitefParams.paymentDate = self.PAYMENT_INVOICE.slice(0, 8);
			sitefParams.paymentHour = self.PAYMENT_INVOICE.slice(8, 14);
			sitefParams.paymentNSU = sitefParams.paymentAuth = sitefParams.paymentVia = "";

			self.initSitefProcess(sitefParams);
		}.bind(this));

		// Utilizado para homologação
		// HomologacaoSitef.download(Query.build()).then(function(dataTEF){
		// 	ZhSitefAutomation.payment(type, currentRow.VRMOVIVEND.toFixed(2), DSENDIPSITEF, CDLOJATEF, CDTERTEF, operatorData.NMOPERADOR, operatorData.NRINSJURFILI, self.PAYMENT_INVOICE, dataTEF[0]);
		// }.bind(this));
	};

	this.setReversal = function(){
		PaymentRepository.findOne().then(function(payment){
			payment.TIPORECE.forEach(function(tiporece){
				if(tiporece.TRANSACTION.data.IDTPTEF === '5') {
					tiporece.TRANSACTION.data.PAYMENTCONFIRMATION = tiporece.TRANSACTION.data.REMOVEALLINTEGRATIONS = false;
				}
			});

			PaymentRepository.save(payment);
		});
	};

	this.getParameters = function(operatorData, PAYMENTINVOICE){
		return SSLConnectionId.findOne().then(function(sSLConnectionIdResponse) {
			var params = {
				'paymentIp': operatorData.DSENDIPSITEF,
				'paymentTerminal': operatorData.CDTERTEF,
				'paymentStore': operatorData.CDLOJATEF,
				'paymentOperator': operatorData.NMOPERADOR,
				'paymentInvoice': PAYMENTINVOICE,
				'storeCnpj': operatorData.NRINSJURFILI,
				'IDUTLSSL': operatorData.IDUTLSSL,
				'IDCODSSL': ''
			};

			if(sSLConnectionIdResponse){
				params.IDCODSSL = sSLConnectionIdResponse.IDCODSSL;
			}

			return params;
		}.bind(this));
	};

	this.formatCardNumber = function(cardNumber){
		return cardNumber.slice(0, 4) + ' ' + cardNumber.slice(4, 6) + 
			'** **** ' + cardNumber.slice(6, 10);
	};

	this.initSitefProcess = function(params){
		ScreenService.hideLoader();
		var sitefWidget = templateManager.containers.login.getWidget("sitefPayment");
		sitefWidget.getField("userInput").isVisible = false;
		sitefWidget.getAction("btnBack").isVisible = false;
		sitefWidget.getAction("btnConfirm").isVisible = false;

		ScreenService.openPopup(sitefWidget).then(function() {
			window.setMessage = self.setMessage;
			window.setLabel = self.setLabel; 
			window.promptBoolean = self.promptBoolean; 
			window.promptCommand = self.promptCommand; 
			window.hideUserInterfaces = self.hideUserInterfaces;
			window.showCancelButton = self.showCancelButton;
			cordova.plugins.GertecSitef.payment(JSON.stringify(params), window.returnIntegration, null);
		}.bind(this));
	};

	this.continueSitefProcess = function(buffer){
		self.hideUserInterfaces();
		cordova.plugins.GertecSitef.continue(buffer, window.returnIntegration, null);
	};

	this.abortSitefProcess = function(){
		cordova.plugins.GertecSitef.abort(window.returnIntegration);
	};

	this.setMessage = function(message) {
 		templateManager.containers.login.getWidget("sitefPayment").getField("userInterface").value(_.toUpper(message));
	};

	this.setLabel = function(label) {
		templateManager.containers.login.getWidget("sitefPayment").label = label;
	};

	this.promptBoolean = function(message) {
		ScreenService.confirmMessage(message, 'question',
			function(){
				self.continueSitefProcess(SITEF_YES);
			},
			function(){
				self.continueSitefProcess(SITEF_NO);
			}
		);
	};

	this.promptCommand = function(message, minLength, maxLength, tipoCampo) {
		self.setMessage(message);
		var widget = templateManager.containers.login.getWidget("sitefPayment");
		widget.currentRow.tipoCampo = tipoCampo;
		widget.getAction("btnConfirm").isVisible = true;
		widget.getAction("btnBack").isVisible = true;

		var field = widget.getField("userInput");
		field.isVisible = true;
		field.minlength = minLength;
		field.maxlength = maxLength;
		field.setValue("");
	};

	this.hideUserInterfaces = function() {
		var widget = templateManager.containers.login.getWidget("sitefPayment");
		widget.getAction("btnBack").isVisible = widget.getAction("btnConfirm").isVisible =
			widget.getField("userInput").isVisible = false;
	};

	this.showCancelButton = function() {
		templateManager.containers.login.getWidget("sitefPayment").getAction("btnBack").isVisible = true;
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('IntegrationSiTEF', IntegrationSiTEF);
});