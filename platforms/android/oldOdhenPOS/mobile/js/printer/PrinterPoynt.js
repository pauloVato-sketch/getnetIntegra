function PrinterPoynt(){
	
	var self = this;

	var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

	this.printText = function(text){
		if (!!window.RedePoyntPrinterJSInterface){
			// tratamento específico para Poynt
			text = text.split("\\n").join('\n');

			RedePoyntPrinterJSInterface.printText(text);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printQRCode = function(qrCode){
		if (!!window.RedePoyntPrinterJSInterface){
			RedePoyntPrinterJSInterface.printQRCode(qrCode);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printBarCode = function(barCode){
		var response = self.formatResponse();

		response.error = false;
		window.returnPrintResult(JSON.stringify(response));
	};

	this.reprintTEFVoucher = function(){
		return new Promise(function(resolve){
			window.returnPrintResult = _.bind(self.printResult, this, resolve);

			if (!!window.RedePoyntJSInterface){
				RedePoyntJSInterface.onReprint();
			} else {
				window.returnPrintResult(self.invalidPrinterInstance());
			}
		}.bind(this));
	};

	this.printResult = function(resolve, javaResult){
		setTimeout(function(){
			resolve(JSON.parse(javaResult));
		}.bind(this), 1000);
	};

	this.printerDelay = function(time){
		var returnObj = self.formatResponse();
		returnObj.error = false;
		window.returnPrintResult(JSON.stringify(returnObj));
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
	ContextRegister.register('PrinterPoynt', PrinterPoynt);
});