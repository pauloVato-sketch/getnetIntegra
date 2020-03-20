chai.should();

var PrinterServiceClass = null;

describe("Unit Test - PrinterService\n", function(){
	var text;

	before(function(){
		PrinterServiceClass = ApplicationContext.PrinterService;
	});

	beforeEach(function(){
		PrinterServiceClass.printerCommands = Array();
	});

	context("Isolated tests in the functions\n", function(){
		describe("# Function printerCommand", function(){
			it("should add command when text parameter passed", function(){
				text = 'unit test';
				PrinterServiceClass.printerCommand(PrinterServiceClass.TEXT_COMMAND, text);
				
				PrinterServiceClass.printerCommands.should.have.lengthOf(1);
				PrinterServiceClass.printerCommands[0].type.should.equal(PrinterServiceClass.TEXT_COMMAND);
				PrinterServiceClass.printerCommands[0].parameter.should.equal(text);
			});
			it("should not add command when no parameter passed", function(){
				text = null;

				PrinterServiceClass.printerCommand(PrinterServiceClass.TEXT_COMMAND, text);
				PrinterServiceClass.printerCommands.should.have.lengthOf(0);
			});
		});
		describe("# Function printerSpaceCommand", function(){
			it("should add command '\\n' when function is called", function(){
				PrinterServiceClass.printerSpaceCommand();

				PrinterServiceClass.printerCommands.should.have.lengthOf(1);
				PrinterServiceClass.printerCommands[0].type.should.equal(PrinterServiceClass.TEXT_COMMAND);
				PrinterServiceClass.printerCommands[0].parameter.should.be.a('string');
			});
		});
		describe("# Function printerInit", function(){
			it("should error when commands aren't added", function(done){
				setOperatorRepositoryPrinter(null);
				PrinterServiceClass.printerInit().then(function(result){
					result.error.should.equal(true);
					result.message.should.equal('Comandos de impressora não foram adicionados.');
					done();
				});
			});
			it("should error when printer param it's wrong", function(done){
				setOperatorRepositoryPrinter('1');
				PrinterServiceClass.printerSpaceCommand();
				
				PrinterServiceClass.printerInit().then(function(result){
					result.error.should.equal(true);
					result.message.should.equal('Impressora não parametrizada ou inválida para o tipo de Caixa.');
					done();
				});
			});
		});
		describe("# Function choosePrinter", function(){
			it("should return class when right params is passed", function(){
				// 26 = Poynt
				var printerClass = PrinterServiceClass.choosePrinter('26');

				printerClass.should.be.a('object');
			});
		});
		describe("# Function endPrint", function(){
			it("should remove commands when it's called", function(){
				// add command
				PrinterServiceClass.printerSpaceCommand();

				PrinterServiceClass.endPrint();
				PrinterServiceClass.printerCommands.should.have.lengthOf(0);
			});
		});
		describe("# Function formatResponse", function(){
			it("should return a response object when it's called\n", function(){
				var formatResponse = PrinterServiceClass.formatResponse();

				formatResponse.should.be.a('object');
				formatResponse.should.have.property('error');
				formatResponse.should.have.property('message');
			});
		});
	});
	
	context("Calling print function\n", function(){
		var printers = PrintData();
		var response;
		var qrCode = 'qrCode';
		var barCode = 'barCode';

		printers.forEach(function(printer){
			describe("# " + printer.name + " print", function(){
				it("print success", function(done){
					text = 'unit test';
					response = getResponsePrint('SUCCESS');
					setOperatorRepositoryPrinter(printer.IDMODEIMPRES);
					PrinterServiceClass.printerCommand(PrinterServiceClass.TEXT_COMMAND, text);
					PrinterServiceClass.printerCommand(PrinterServiceClass.QRCODE_COMMAND, qrCode);
					PrinterServiceClass.printerCommand(PrinterServiceClass.BARCODE_COMMAND, barCode);
					PrinterServiceClass.printerSpaceCommand();
					mockPrinter(printer.IDMODEIMPRES, response);

					PrinterServiceClass.printerInit().then(function(result){
						result.error.should.equal(false);
						result.message.should.be.a('string').with.lengthOf(0);
						done();
					});
				});
				it("print error", function(done){
					text = 'unit test';
					response = getResponsePrint('ERROR');
					setOperatorRepositoryPrinter(printer.IDMODEIMPRES);
					PrinterServiceClass.printerCommand(PrinterServiceClass.TEXT_COMMAND, text);
					PrinterServiceClass.printerCommand(PrinterServiceClass.QRCODE_COMMAND, qrCode);
					PrinterServiceClass.printerCommand(PrinterServiceClass.BARCODE_COMMAND, barCode);
					PrinterServiceClass.printerSpaceCommand();
					mockPrinter(printer.IDMODEIMPRES, response);

					PrinterServiceClass.printerInit().then(function(result){
						result.error.should.equal(true);
						result.message.should.equal('Falha na impressão. unit test');
						done();
					});
				});
			});
		});
	});
});


function setOperatorRepositoryPrinter(IDMODEIMPRES){
	ApplicationContext.OperatorRepository.findOne = function(){
		return new Promise(function(resolve){
			resolve({
				'IDMODEIMPRES': IDMODEIMPRES
			});
		});
	};
}

function PrintData(){
	return Array(
		{
			'name': 'Gertec',
			'IDMODEIMPRES': '25'
		},
		{
			'name': 'Poynt',
			'IDMODEIMPRES': '26'
		},
		{
			'name': 'CieloLio',
			'IDMODEIMPRES': '27'
		}
	);
}

function mockPrinter(IDMODEIMPRES, response){
	var printerClass = PrinterServiceClass.choosePrinter(IDMODEIMPRES);

	// apenas se mocka funções existentes
	if (printerClass.printText){
		printerClass.printText = function(text){
			window.returnPrintResult(response);
		};		
	}

	if (printerClass.printQRCode){
		printerClass.printQRCode = function(text){
			window.returnPrintResult(response);
		};
	}

	if (printerClass.printBarCode){
		printerClass.printBarCode = function(text){
			window.returnPrintResult(response);
		};
	}

	printerClass.printResult = function(resolve, javaResult){
		resolve(JSON.parse(javaResult));
	};
}

function getResponsePrint(type){
	if (type === 'SUCCESS'){
		return JSON.stringify({
			'error': false,
			'message': ''
	    });		
	} else {
		return JSON.stringify({
			'error': true,
			'message': 'unit test'
	    });
	}
};

