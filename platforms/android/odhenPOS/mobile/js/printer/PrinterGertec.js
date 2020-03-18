function PrinterGertec(ScreenService){
	
	var self = this;

	var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

	this.printText = function(text){
		if (!!window.ZhGertecPrinter){
			ZhGertecPrinter.printText(text);
		} else if(!!window.cordova && !!cordova.plugins.GertecPrinter) {
			cordova.plugins.GertecPrinter.printString(text, window.returnPrintResult, function(){});
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printQRCode = function(qrCode){
		if (!!window.ZhGertecPrinter){
			ZhGertecPrinter.printQrCode(qrCode);
		} else if(!!window.cordova && !!cordova.plugins.GertecPrinter) {
			cordova.plugins.GertecPrinter.printQrCode(qrCode, window.returnPrintResult, function(){});
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printBarCode = function(barCode){
		if (!!window.ZhGertecPrinter){
			ZhGertecPrinter.printBarCode(barCode);
		} else if(!!window.cordova && !!cordova.plugins.GertecPrinter) {
			cordova.plugins.GertecPrinter.printBarCode(barCode, window.returnPrintResult, function(){});
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printerDelay = function(time){
		setTimeout(function(){
			var returnObj = self.formatResponse();
			returnObj.error = false;
			window.returnPrintResult(JSON.stringify(returnObj));
		}.bind(this), parseInt(time));	
	};

	this.reprintTEFVoucher = function(){
		window.returnIntegration = _.bind(self.getReprintTextResult, this);

		if (!!window.ZhSitefAutomation){
			ZhSitefAutomation.showAdministrativeMenu();
		} else {
			window.returnIntegration(self.invalidPrinterInstance());
		}
	};

	this.getReprintTextResult = function(javaResult){
		window.returnPrintResult = _.bind(self.reprintResult, this);
		javaResult = JSON.parse(javaResult);
		var comandos = [javaResult.customerReceipt, "", ""];

		if(javaResult.paymentTransactionStatus === 0) {
			comandos.forEach(function(text) {
				self.printText(text);
			}.bind(this));
		} else {
			ScreenService.showMessage("Erro ao tentar encontrar o último comprovante TEF");
		}
	};

	this.reprintResult = function (javaResult) {
		javaResult = JSON.parse(javaResult);

		if(javaResult.paymentTransactionStatus === 1) {
			ScreenService.showMessage("Erro ao imprimir o último comprovante TEF");
		}
	};

	this.printResult = function(resolve, javaResult){
		if (!!window.ZhGertecPrinter)
			javaResult = JSON.parse(javaResult);

		resolve(javaResult);
	};

	this.invalidPrinterInstance = function(){
		return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

	this.formatResponse = null;
	
}

Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterGertec', PrinterGertec);
});