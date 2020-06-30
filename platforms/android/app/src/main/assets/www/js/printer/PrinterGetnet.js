function PrinterGetnet(){

    var self = this;

    var INVALID_PRINTER_INSTANCE = 'Não foi possível chamar a impressora. Sua instância não existe.';

    this.printText = function(text){

        if (!!window.cordova.plugins.IntegrationService){

        	var params = JSON.stringify({"texto":text,
        	                              "flag":"printText"});
            //Chamada da função de impressão da integração
            //window.returnPrintResult contém a função printResult desse mesmo arquivo
            window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

        } else {
        	window.returnPrintResult(self.invalidPrinterInstance());
        }
    };

    this.printQRCode = function(qrCode){

        var params = JSON.stringify({"qrcode":qrCode,
                                     "flag":"qrCode"});
        window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

    };

    this.printBarCode = function(barCode){

        var params = JSON.stringify({"barcode":barCode,
                                     "flag":"barCode"});
        window.cordova.plugins.IntegrationService.print(params, window.returnPrintResult,null);

    };

    this.reprintTEFVoucher = function(){

        //window.returnPrintResult(self.invalidPrinterInstance());
    };


    this.printResult = function(resolve, javaResult){
        javaResult = self.codeToString(javaResult);
        resolve(javaResult);
    };

    this.printerDelay = function(){
        setTimeout(function(){
        	var returnObj = self.formatResponse();
        	returnObj.error = false;
        	window.returnPrintResult(JSON.stringify(returnObj));
        }.bind(this),5000);
    };

    this.invalidPrinterInstance = function(){
        return JSON.stringify({
            'error': true,
            'message': INVALID_PRINTER_INSTANCE
        });
    };

    this.formatResponse = null;

    this.codeToString = function(javaResult){
        switch(javaResult.message){
            case 0: javaResult.message = "OK"; break;
            case 1: javaResult.message = "Imprimindo"; break;
            case 2: javaResult.message = "Impressora não iniciada"; break;
            case 3: javaResult.message = "Impressora superaquecida"; break;
            case 4: javaResult.message = "Fila de impressão muito grande"; break;
            case 5: javaResult.message = "Parametros incorretos"; break;
            case 10: javaResult.message = "Porta da impressora aberta"; break;
            case 11: javaResult.message = "Temperatura baixa demais para impressão"; break;
            case 12: javaResult.message = "Sem bateria suficiente para impressão"; break;
            case 13: javaResult.message = "Motor de passo com problemas"; break;
            case 15: javaResult.message = "Sem bonina"; break;
            case 16: javaResult.message = "Bobina acabando"; break;
            case 17: javaResult.message = "Bobina travada"; break;
            case 1000:
            case null: javaResult.message = "Não foi possível definir o erro"; break;
        }
        return javaResult;
    };

}


Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterGetnet', PrinterGetnet);
});