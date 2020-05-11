function PrinterService(OperatorRepository, PrinterPoynt, PrinterCieloLio, PrinterGertec,PrinterGetnet,WindowService) {

	var self = this;

	var PrinterClass = null;
	var PRINTER_TYPE = {
		'25': PrinterGertec,
		'26': PrinterPoynt,
		'27': PrinterCieloLio,
		'28': PrinterGetnet
	};

	var COMMANDS_NOT_FOUND = 'Comandos de impressora não foram adicionados.';
	var NO_PARAMETERIZED_PRINTER = 'Impressora não parametrizada ou inválida para o tipo de Caixa.';
	var PRINT_ERROR = 'Falha na impressão. ';

	this.TEXT_COMMAND = 'printText';
	this.QRCODE_COMMAND = 'printQRCode';
	this.BARCODE_COMMAND = 'printBarCode';
	this.DELAY_COMMAND = 'printerDelay';

	this.printerCommands = Array();

	this.printerCommand = function(type, parameter){
		if (_.isString(parameter) && !_.isEmpty(parameter)){
			self.printerCommands.push({
				'type': type,
				'parameter': parameter
			});
		}
	};

	this.printerSpaceCommand = function(){
		self.printerCommands.push({
			'type': self.TEXT_COMMAND,
			'parameter': '\n         ' +
						 '\n         '
		});
	};

	this.callRecursivePrintCommands = null;

	this.printerInit = function() {
		return OperatorRepository.findOne().then(function(operatorData) {
			if (!_.isEmpty(self.printerCommands)){
				PrinterClass = self.choosePrinter(operatorData.IDMODEIMPRES);
				if (PrinterClass){
					return new Promise(function(resolve){
						self.callRecursivePrintCommands = _.bind(self.printCommands, self, resolve, PrinterClass);
						self.callRecursivePrintCommands();
					}.bind(this));
				} else {
					return self.invalidPrinterParam();
				}				
			} else {
				return self.invalidPrinterCommands();
			}
		}.bind(this)).then(self.endPrint);
	};

	this.choosePrinter = function(IDMODEIMPRES){
		// seleciona serviço de impressão
		return _.get(PRINTER_TYPE, IDMODEIMPRES);
	};

	window.returnPrintResult = null;

	this.printCommands = function(impressionResolved, PrinterClass){
		// função recursiva utilizada para chamar as funções de impressão
		var currentPrinterCommand = self.printerCommands.shift();		
		new Promise(function(resolve){
			window.returnPrintResult = _.bind(PrinterClass.printResult, PrinterClass, resolve);
			PrinterClass[currentPrinterCommand.type](currentPrinterCommand.parameter);
		}.bind(this)).then(function(resolved){
			if (!resolved.error) {
				if (!_.isEmpty(self.printerCommands)){
					// realiza impressão do próximo comando
					self.callRecursivePrintCommands();
				} else {
					// impressão realizada com sucesso
					impressionResolved(resolved);		
				}
			} else {
				// erro ao realizar impressão
				resolved.message = PRINT_ERROR + resolved.message;
				impressionResolved(resolved);

			}
		}.bind(this));
	};

	this.invalidPrinterCommands = function(){
		var result = self.formatResponse();

		result.message = COMMANDS_NOT_FOUND;
		return result;
	};

	this.invalidPrinterParam = function(){
		var result = self.formatResponse();

		result.message = NO_PARAMETERIZED_PRINTER;
		return result;
	};

    this.endPrint = function(result){
		// para qualquer resultado da impressão, se reseta os comandos de impressão e retorna seu resultado
		self.printerCommands = Array();

		return result;
	};

	this.formatResponse = function(){
        return {
            'error': true,
            'message': ''
        };
    };

    PrinterPoynt.formatResponse = self.formatResponse;
    PrinterCieloLio.formatResponse = self.formatResponse;
    PrinterGertec.formatResponse = self.formatResponse;
    PrinterGetnet.formatResponse = self.formatResponse;
}

Configuration(function(ContextRegister) {
	ContextRegister.register('PrinterService', PrinterService);
});