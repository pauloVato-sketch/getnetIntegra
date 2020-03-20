function PrinterCieloLio(){
	
	var self = this;

	var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

	this.printText = function(text){
		if (!!window.ZhCieloAutomation){
			ZhCieloAutomation.printText(text);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printQRCode = function(qrCode){
		self.printImage(qrCode, 1);
	};

	this.printBarCode = function(barCode){
		self.printImage(barCode, 2);
	};

	this.printImage = function(stringCode, type){
		// impressão de imagem utilizada pelo QRcode e Código de Barras
		if (!!window.ZhCieloAutomation){
			ZhCieloAutomation.printImage(stringCode, type);
		} else {
			window.returnPrintResult(self.invalidPrinterInstance());
		}
	};

	this.printerDelay = function(time){
		var returnObj = self.formatResponse();
		returnObj.error = false;
		window.returnPrintResult(JSON.stringify(returnObj));
	};

	this.reprintTEFVoucher = function(){
		window.returnPrintResult(self.invalidPrinterInstance());
	};

	this.printResult = function(resolve, javaResult){
		var response = self.formatResponse();
		javaResult = JSON.parse(javaResult);

		response.error = javaResult.statusPrinter !== "1";
		response.message = javaResult.message;

		resolve(response);
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
	ContextRegister.register('PrinterCieloLio', PrinterCieloLio);
});