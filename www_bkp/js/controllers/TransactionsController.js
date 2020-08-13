function TransactionsController(AccountController, OperatorRepository, AccountService, ValidationEngine, TransactionsService, templateManager, ScreenService, UtilitiesService, TransactionsRepository, WindowService){

	var self = this;
	var NRSEQMOVMOB = "";
	var NRSEQMOVMOBToFind = "";

	this.setChaveOnDataSourceFilter = function(widget) {
		OperatorRepository.findAll().then(function (params) {

			widget.dataSourceFilter[3].value = params[0].chave;
			widget.reload();
		});
	};

	this.findTransaction = function(widget){

		var NRADMCODE =  widget.currentRow.NRADMCODE;
		var widgetTransaction = templateManager.container.getWidget('transaction');
		var DTHRFIMMOVini = widget.currentRow.DTHRMOVFIM;
		var DTHRFIMMOVfim = widget.currentRow.DTHRMOVFIM;
		OperatorRepository.findAll().then(function (params) {
			if(!NRADMCODE){
				DTHRFIMMOVini = !DTHRFIMMOVini ? moment(new Date()).format('DD/MM/YYYY') + " 00:00:00" : DTHRFIMMOVini + " 00:00:00";
				DTHRFIMMOVfim = !DTHRFIMMOVfim ? moment(new Date()).format('DD/MM/YYYY') + " 23:59:59" : DTHRFIMMOVfim + " 23:59:59";
				widgetTransaction.dataSourceFilter[0].value = DTHRFIMMOVini;
				widgetTransaction.dataSourceFilter[1].value = DTHRFIMMOVfim;
				widgetTransaction.dataSourceFilter[2].value = '';
				widgetTransaction.dataSourceFilter[3].value = params[0].chave;

			}else{
				widgetTransaction.dataSourceFilter[0].value = '';
				widgetTransaction.dataSourceFilter[1].value = '';
				widgetTransaction.dataSourceFilter[2].value = NRADMCODE;
				widgetTransaction.dataSourceFilter[3].value = params[0].chave;
			}
			templateManager.updateTemplate();
			widgetTransaction.activate();
			widgetTransaction.reload();
		});
	};

	this.sendTransactionEmail = function(widget, args){

		var NRSEQMOVMOB = (widget.currentRow.NRSEQMOVMOB = args.owner.widget.container.getWidget('transaction').currentRow.NRSEQMOVMOB);
		var DSEMAILCLI = typeof widget.currentRow.DSEMAILCLI == "string" ? widget.currentRow.DSEMAILCLI.toLowerCase() : widget.currentRow.DSEMAILCLI;
		var TRANSACTIONEMAIL = args.owner.widget.container.getWidget('transaction').currentRow.DSEMAILCLI;

		if(ValidationEngine.mail(DSEMAILCLI, "").valid){
			if(DSEMAILCLI != TRANSACTIONEMAIL){
				TRANSACTIONEMAIL = DSEMAILCLI;
				TransactionsService.updateTransactionEmail(DSEMAILCLI,NRSEQMOVMOB).then(function(success){
					args.owner.widget.container.getWidget('transaction').reload();
					TransactionsService.sendTransactionEmail(NRSEQMOVMOB, DSEMAILCLI).then(function(){
						ScreenService.showMessage("Email enviado com sucesso.");
					});
				});
			}else{
				TransactionsService.sendTransactionEmail(NRSEQMOVMOB, DSEMAILCLI).then(function(){
					ScreenService.showMessage("Email enviado com sucesso.");
				});
			}
		}else{
			ScreenService.showMessage(ValidationEngine.mail(DSEMAILCLI, "").message);
		}

		widget.isVisible = false;
		ScreenService.closePopup();

	};

	this.widgetEmailVisibility = function(widget){
		widget.isVisible = false;
		ScreenService.closePopup();
	};

	this.confirmTransactionEmail = function(widget,args){
		var popupEmail = args.owner.widget.container.getWidget('popupEmail');
		popupEmail.currentRow.DSEMAILCLI = typeof widget.currentRow.DSEMAILCLI == "string" ? widget.currentRow.DSEMAILCLI.toLowerCase() : widget.currentRow.DSEMAILCLI;
		popupEmail.isVisible = true;
		ScreenService.openPopup(popupEmail);
	};

	this.cancelTransaction = function(widget){

		var cancelPayment = 1;
		var NRSEQMOVMOB = widget.currentRow.NRSEQMOVMOB;
		self.NRSEQMOVMOBToFind = NRSEQMOVMOB;
		self.NRSEQMOVMOB = NRSEQMOVMOB;

		OperatorRepository.findAll().then(function (params) {
			TransactionsService.findRowToCancel(params[0].chave, NRSEQMOVMOB).then(function(rowToCancelData){

				var dataset = {
					chave : params[0].chave,
					CDVENDEDOR : params[0].CDVENDEDOR,
					NRVENDAREST : rowToCancelData[0].NRVENDAREST,
					NRMESA : rowToCancelData[0].NRMESA,
					NRLUGARMESA : rowToCancelData[0].NRLUGARMESA,
					VRMOV : (rowToCancelData[0].VRMOV *-1) ,
					NRADMCODE : rowToCancelData[0].NRADMCODE,
					DSBANDEIRA : rowToCancelData[0].DSBANDEIRA,
					CDTIPORECE : rowToCancelData[0].CDTIPORECE,
					IDTPTEF : rowToCancelData[0].IDTPTEF,
					NRCOMANDA : rowToCancelData[0].NRCOMANDA,
					IDTIPMOV : rowToCancelData[0].IDTIPMOV
				};
				AccountService.beginPaymentAccount(dataset.chave, dataset.CDVENDEDOR, dataset.NRVENDAREST, dataset.NRCOMANDA, dataset.NRMESA, dataset.NRLUGARMESA, dataset.CDTIPORECE, dataset.IDTIPMOV, dataset.VRMOV, dataset.DSBANDEIRA, dataset.IDTPTEF).then(function(response){

					self.NRSEQMOVMOB = response[0].NRSEQMOVMOB;

					if(widget.currentRow.IDTPTEF > 1){ // Se for TEF (não cancela caso for pagamento digitado (SITEF))
						if(window.ZhNativeInterface){
							var administrativeCode = widget.currentRow.NRADMCODE;
							var paymentId = response[0].NRSEQMOVMOB;
							var administrativeTask = "1"; // "1" para cancelamento
							var administrativePassword = "";
							var paymentType = widget.currentRow.IDTPTEF;
							ZhNativeInterface.tefAdministrativeTask(paymentType, administrativeTask, administrativeCode, administrativePassword, paymentId);
						} else {
							self.tefmock(); // Para teste no computador
							// ScreenService.showMessage('ZhNativeInterface não encontrada.');
						}
					} else {
						if(widget.currentRow.IDTPTEF == 1){
							// cancelamento SITEF
							ScreenService.showMessage("Esse método de pagamento não suporta o cancelamento da transação !");
						} else {

							var dataset = {
								NRSEQMOVMOB : self.NRSEQMOVMOB,
								NRSEQMOB : null,
								DSBANDEIRA : null,
								NRADMCODE :  null,
								IDADMTASK : '1',
								IDSTMOV : '1',
								TXMOVUSUARIO : null,
								TXMOVJSON : null,
								CDNSUTEFMOB : null,
								TXPRIMVIATEF : null,
								TXSEGVIATEF : null,
								transactionStatus : '1',
							};

							self.finishPayment(dataset);
						}
					}
				});
			});
		});
	};

	this.cancelTransactionCappta = function(widget){

		var cancelPayment = 1;
		var NRSEQMOVMOB = widget.currentRow.NRSEQMOVMOB;
        self.NRSEQMOVMOBToFind = NRSEQMOVMOB;
		self.NRSEQMOVMOB = NRSEQMOVMOB;

        OperatorRepository.findOne().then(function (params) {
            if (widget.currentRow.IDTPTEF > 1) { // Se for TEF (não cancela caso for pagamento digitado (SITEF))
                if (window.ZhNativeInterface && ZhNativeInterface.tefAdministrativeTask) {
                    var administrativeCode = widget.currentRow.NRADMCODE;
                    var paymentId = self.NRSEQMOVMOB;
                    var administrativeTask = "1"; // "1" para cancelamento
                    var administrativePassword = "";
                    var paymentType = widget.currentRow.IDTPTEF;
                    try {
                    	ZhNativeInterface.tefAdministrativeTask(paymentType, administrativeTask, administrativeCode, administrativePassword, paymentId);
                	} catch(error) {
                		ScreenService.showMessage('Falha na comunicação com o aplicativo Cappta. Verifique se o mesmo está instalado.');
                		console.log(error);
                	}
                } else {
                    self.tefmock(); // Para teste no computador
                    // ScreenService.showMessage('ZhNativeInterface não encontrada.');
                }
            } else {
                if(widget.currentRow.IDTPTEF == 1){
                    // cancelamento SITEF
                    ScreenService.showMessage("Esse método de pagamento não suporta o cancelamento da transação !");
                } else {

                    var dataset = {
                        NRSEQMOVMOB : self.NRSEQMOVMOB,
                        NRSEQMOB : null,
                        DSBANDEIRA : null,
                        NRADMCODE :  null,
                        IDADMTASK : '1',
                        IDSTMOV : '1',
                        TXMOVUSUARIO : null,
                        TXMOVJSON : null,
                        CDNSUTEFMOB : null,
                        TXPRIMVIATEF : null,
                        TXSEGVIATEF : null,
                        transactionStatus : '1',
                    };

                    self.finishPayment(dataset);
                }
            }
		});
	};

	this.finishPayment = function(dataset){
        AccountService.finishPaymentAccount(dataset).then(function(response){
            if(dataset.transactionStatus === 1 && dataset.JSONTEFDetails !== null){
                ScreenService.openWindow('transactions').then(function(){
                    var emailPopup = templateManager.container.getWidget("sendEmail");
                    emailPopup.currentRow = response[0];
                    emailPopup.currentRow.RECEIPT = dataset.JSONTEFDetails.customer_receipt;
                    emailPopup.currentRow.RECEIPT = emailPopup.currentRow.RECEIPT.replace(/'/g, '');
                    ScreenService.openPopup(emailPopup);
                });
                TransactionsService.updateCanceledTransaction(self.NRSEQMOVMOBToFind).then(function(){
                    self.findTransaction(templateManager.container.getWidget("transactionsFilter"));
                });
            } else if(dataset.transactionStatus === 1 && dataset.JSONTEFDetails === null) {
                ScreenService.showMessage("Cancelamento Feito com Sucesso!");
                TransactionsService.updateCanceledTransaction(self.NRSEQMOVMOBToFind).then(function(){
                    self.findTransaction(templateManager.container.getWidget("transactionsFilter"));
                });
            } else {
                ScreenService.showMessage(dataset.TXMOVUSUARIO);
            }
            if(templateManager.container.name == 'transactions'){
                templateManager.container.widgets[0].reload();
            }
        });
    };

	window.tefAdministrativeResult = function(result) {
    	var capptaErrors = {
			1: 'Não autenticado/Alguma das informações fornecidas para autenticação não é válida',
			2: 'Cappta Android está sendo inicializado',
			3: 'Formato da requisição recebida pelo Cappta Android é inválido',
			4: 'Operação cancelada pelo operador',
			5: 'Pagamento não autorizado/pendente/não encontrado',
			6: 'Pagamento ou cancelamento negados pela rede adquirente ou falta de conexão com internet',
			7: 'Erro interno no Cappta Android',
			8: 'Erro na comunicação com o Cappta Android'
    	};

		var JSONTEF = JSON.parse(result)[0];
		var userMessage = _.get(JSONTEF, 'tef_request_details.user_message');
    	if (userMessage == "Estorno realizado" ) {
			var dataset = self.createUpdateTransactionObject(JSONTEF);
        	self.finishPayment(dataset);
        } else {
        	var defaultMessage = _.get(capptaErrors, _.get(JSONTEF, 'tef_request_type'), 'Falha na comunicação com o aplicativo Cappta. Verifique se o mesmo está instalado.');
        	ScreenService.showMessage(userMessage || defaultMessage);
        }
	};

	this.isNotEmpty = function(value) {
		if (_.isString(value)) {
			return !_.isEmpty(value);
		} else {
			return !_.isNil(value);
		}
	};

	this.createUpdateTransactionObject = function(JSONTEF) {
		var JSONTEFDetails = JSONTEF.tef_request_details;
		var dataset = {};
		dataset.JSONTEFDetails = JSONTEFDetails;
		dataset.NRSEQMOVMOB = self.NRSEQMOVMOB;
		dataset.TXMOVJSON = JSON.stringify(JSONTEF);

		dataset.NRSEQMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.DSBANDEIRA = self.isNotEmpty(JSONTEFDetails.card_brand_name) ? JSONTEFDetails.card_brand_name : null;
		dataset.NRADMCODE = self.isNotEmpty(JSONTEFDetails.administrative_code) ? JSONTEFDetails.administrative_code : null;
		dataset.IDADMTASK = self.isNotEmpty(JSONTEFDetails.administrative_task) ? JSONTEFDetails.administrative_task : null;
		dataset.IDSTMOV = 0;//transação cancelada
		dataset.TXMOVUSUARIO = self.isNotEmpty(JSONTEFDetails.user_message) ? JSONTEFDetails.user_message : null;
		dataset.CDNSUTEFMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.TXPRIMVIATEF = self.isNotEmpty(JSONTEFDetails.merchant_receipt) ? JSONTEFDetails.merchant_receipt.replace(/'/g, '') : null;
		dataset.TXSEGVIATEF = self.isNotEmpty(JSONTEFDetails.customer_receipt) ? JSONTEFDetails.customer_receipt.replace(/'/g, '') : null;
		dataset.transactionStatus = self.isNotEmpty(JSONTEFDetails.payment_transaction_status) ? JSONTEFDetails.payment_transaction_status : null;

		return dataset;
	};

	this.tefmock = function() {
		// not mocking at the moment
    	if (false) {
			var result =
				[
				    {
				        "tef_request_type": 4,
				        "tef_request_details": {
				            "payment_transaction_status": 1,
				            "acquirer_affiliation_key": "0009448512329101",
				            "acquirer_name": "Elavon",
				            "card_brand_name": "MAESTRO",
				            "acquirer_authorization_code": "SIMULADOR",
				            "payment_product": 1,
				            "payment_installments": 1,
				            "payment_amount": 16,
				            "available_balance": null,
				            "unique_sequential_number": 21007,
				            "acquirer_unique_sequential_number": null,
				            "acquirer_authorization_datetime": "2016-07-15 11:25:42",
				            "administrative_code": "07520701019",
				            "administrative_task": 1,
				            "user_message": null,
				            "merchant_receipt": "''\r\n'**VIALOJISTA**'\r\n'ELAVON'\r\n'MAESTRO-DEBITOAVISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/1610: 25: 50'\r\n'AUT=SIMULADORDOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'CAPPTACARTOES'",
				            "customer_receipt": "''\r\n'HOMOLOGA'\r\n'40.841.182/0001-48'\r\n'**VIACLIENTE**'\r\n'ELAVON'\r\n'MAESTRO-DEBITOAVISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/1610: 25: 50'\r\n'AUT=SIMULADORDOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'CAPPTACARTOES'",
				            "reduced_receipt": "'ELAVON-NL000948512329101'\r\n'MAESTRO-************2679'\r\n'AUT=SIMULADORDOC=21007'\r\n'VALOR=1,50CONTROLE=07520701019'"
				        }
				    }
				];

			var resultString = JSON.stringify(result);

			window.tefAdministrativeResult(resultString);
    	} else {
    		ScreenService.showMessage('A Webview do Android não foi encontrada.');
    	}
	};

	this.openFilterTransactionsPopup = function(){

		var popupTransactionsFilter = templateManager.container.getWidget('transactionsFilter');
		popupTransactionsFilter.isVisible = true;
		ScreenService.openPopup(popupTransactionsFilter);

	};
	this.closeFilterTransactionsPopup = function(){

		var popupTransactionsFilter = templateManager.container.getWidget('transactionsFilter');
		popupTransactionsFilter.isVisible = false;

		ScreenService.closePopup();
	};

	this.clearField = function(widget, fieldID){

		var NRADMCODE = widget.currentRow.NRADMCODE;
		var DTHRMOVFIM = widget.currentRow.DTHRMOVFIM;

		if(fieldID == 1){
			// widget.currentRow.NRADMCODE = '';
			widget.getField('NRADMCODE').applyDefaultValue();
		}else{
			if(fieldID == 2)
			widget.getField('DTHRMOVFIM').applyDefaultValue();
		}

		templateManager.updateTemplate();
	};


}


Configuration(function(ContextRegister) {
	ContextRegister.register('TransactionsController', TransactionsController);
});