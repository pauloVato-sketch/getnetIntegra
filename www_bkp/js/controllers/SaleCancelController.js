function SaleCancelController(AccountService, OperatorRepository, PermissionService, ScreenService, UtilitiesService, PaymentService, IntegrationService, IntegrationCappta, PrinterService, templateManager){

	var self = this;

	this.saleCancel = function(widget){
		if (_.get(widget, 'currentRow.CODIGOCUPOM')){
			OperatorRepository.findOne().then(function(operatorData){
				widget.currentRow.CODIGOCUPOM = UtilitiesService.padLeft(widget.currentRow.CODIGOCUPOM, widget.getField('CODIGOCUPOM').maxlength, '0');
				AccountService.saleCancel(operatorData.chave, widget.currentRow.CODIGOCUPOM, widget.CDSUPERVISOR).then(function(saleCancelResult){
					saleCancelResult = saleCancelResult[0];
					if (!saleCancelResult.error) {
						self.clearScreen(widget);
						UtilitiesService.backMainScreen();		
						ScreenService.showMessage(saleCancelResult.message, 'success').then(function(){
							self.handleSaleCancel(saleCancelResult.data);
						}.bind(this));						
					} else {
						ScreenService.showMessage(saleCancelResult.message, 'alert');
					}
				});
			});
		}
	};

	this.openSaleCancel = function(windowName){
		PermissionService.checkAccess('cancelaCupom').then(function(CDSUPERVISOR){
			self.showSaleCancel(windowName, CDSUPERVISOR);
		}.bind(this));
	};

	this.showSaleCancel = function(windowName, CDSUPERVISOR) {
		ScreenService.openWindow(windowName).then(function(){
			templateManager.container.getWidget('saleCancelWidget').CDSUPERVISOR = CDSUPERVISOR;
			ScreenService.toggleSideMenu();
		}.bind(this));
	};

	this.clearScreen = function(widget){
		widget.currentRow = {};
		OperatorRepository.findOne().then(function(operatorData){
			widget.getField('CODIGOCUPOM').maxlength = operatorData.IDTPEMISSAOFOS === 'SAT' ? 6 : 9;
		});
	};

	this.handleSaleCancel = function(saleCancelResult){
		if (!_.isEmpty(saleCancelResult.dadosImpressao)){
			self.printSaleCancel(saleCancelResult.dadosImpressao).then(function(response){
				self.handleTransactionRefound(saleCancelResult);
			}.bind(this));
		} else {
			self.handleTransactionRefound(saleCancelResult);
		}
	};

	this.printSaleCancel = function(dadosImpressao){
		PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOCUPOM);
		PrinterService.printerCommand(PrinterService.BARCODE_COMMAND, dadosImpressao.TEXTOCODIGOBARRAS);
		PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOQRCODE);
		PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTORODAPE);
		PrinterService.printerSpaceCommand();
		
		return PrinterService.printerInit().then(function(result){
			if(result.error)
				ScreenService.alertNotification(result.message);
		});
	};

	this.handleTransactionRefound = function(saleCancelResult){
		var dataTEF = saleCancelResult.dataTEF;
		dataTEF = _.filter(dataTEF, function(tiporece) {
			return PaymentService.checkIfMustCallIntegration(tiporece);
		}.bind(this));

		OperatorRepository.findOne().then(function(operatorData){
			if (!_.isEmpty(dataTEF) && operatorData.IDUTILTEF === 'T' && (!!window.cordova || !!window.ZhCieloAutomation)){
				self.tefRefound(dataTEF, operatorData);
			}				
		});
	};

	this.tefRefound = function(dataTEF, operatorData){
		// monta dados para estorno
		dataTEF = _.map(dataTEF, function(tiporece){
			tiporece.IDTPTEF = operatorData.IDTPTEF;

			switch(tiporece.IDTPTEF) {
				case '2':
					tiporece.AUTHKEY = IntegrationCappta.getAUTHKEY(operatorData.AMBIENTEPRODUCAO);
					break;
				case '5':
					tiporece.DSENDIPSITEF = operatorData.DSENDIPSITEF; 
					tiporece.CDLOJATEF = operatorData.CDLOJATEF;
					tiporece.CDTERTEF = operatorData.CDTERTEF;
					var transactionDate = tiporece.DTHRINCMOV.split(" ")[0].replace('-', '').replace('-', '');
					tiporece.TRANSACTIONDATE = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
					break;
			}

			return tiporece;
		}.bind(self));

		IntegrationService.reversalIntegration(self.mochRemovePaymentSale, dataTEF).then(function(reversalIntegrationResult){
			// chama impressão do comprovante de cancelamento TEF
			if (!reversalIntegrationResult.error){
				ScreenService.showMessage("TEF estornado com sucesso.", 'success');

				if(operatorData.IDTPTEF !== '4') 
					PaymentService.printTEFVoucher(reversalIntegrationResult.data);
			} else {
				ScreenService.showMessage(reversalIntegrationResult.message).then(function(){
					PaymentService.handleRefoundTEFVoucher(reversalIntegrationResult.data);
				});
			}
		}.bind(this));
	};

	this.mochRemovePaymentSale = function(){
		return new Promise.resolve(true);
	};
}

Configuration(function(ContextRegister){
	ContextRegister.register('SaleCancelController', SaleCancelController);
});