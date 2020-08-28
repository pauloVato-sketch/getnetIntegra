function RegisterController(OperatorController, OperatorRepository, ScreenService, UtilitiesService, RegisterService, RegisterClosingPayments, WindowService, PrinterService, RegisterOpen, RegisterClose, GeneralFunctions, CarrinhoDesistencia, Query, PerifericosService) {

	var self = this;

	this.openRegister = function (widget) {
		var rowChangeFunds = widget.currentRow;
		rowChangeFunds.VRMOVIVEND = UtilitiesService.getFloat(rowChangeFunds.VRMOVIVEND);
		if (rowChangeFunds.VRMOVIVEND == null || isNaN(rowChangeFunds.VRMOVIVEND)) {
			ScreenService.showMessage('Fundo de troco inválido.');
		} else if (rowChangeFunds.VRMOVIVEND < 0) {
			ScreenService.showMessage('Fundo de troco não pode ser menor que 0.');
		} else {
			OperatorRepository.findOne().then(function (operatorData) {
				RegisterService.openRegister(operatorData.chave, rowChangeFunds.VRMOVIVEND).then(function (registerOpen) {
					registerOpen = registerOpen[0];
					if (_.get(registerOpen, 'dadosImpressao.paramsImpressora')) {
						PerifericosService.print(registerOpen.dadosImpressao.paramsImpressora).then(function () {
							self.handleOpenRegister(false);
						});
					} else {
						if (!_.isEmpty(registerOpen.dadosImpressao)) {
							var openRegisterText = registerOpen.dadosImpressao.open;
							var registerWidget = widget.container.getWidget('report');

							if (operatorData.IDMODEIMPRES == '25') {
								openRegisterText = _.join(_.split(openRegisterText, ' | '), "\n");
							}
							registerWidget.setCurrentRow({ 'report': openRegisterText });
							ScreenService.openPopup(registerWidget);
						} else {
							self.handleOpenRegister(false);
						}
					}
				});
			});
		}
	};

	this.printOpenRegister = function () {
		RegisterOpen.findOne().then(function (registerOpen) {
			PrinterService.printerCommand(PrinterService.TEXT_COMMAND, registerOpen.dadosImpressao.open);
			self.printerSpaceCommand(2);
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});
			self.handleOpenRegister(true);
		});
	};

	this.printerSpaceCommand = function (max) {
		for (var i = 0; i < max; i++) {
			PrinterService.printerSpaceCommand();
		}
	};

	this.handleOpenRegister = function (closePopup) {
		if (closePopup) {
			ScreenService.closePopup();
		}

		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDTPTEF === '5' && operatorData.IDCOLETOR === 'N' && !Util.isDesktop()) {
				GeneralFunctions.sitefTableLoad();
				GeneralFunctions.exportLogs(false);
			}

			OperatorController.bindedDoLogin();
		}.bind(this));
	};

	this.closeRegister = function (paymentGrid) {
		OperatorRepository.findOne().then(function (operatorData) {
			var TIPORECE = paymentGrid.dataSource.data;
			if ((TIPORECE.length === 0) || (validPayments(TIPORECE))) {
				RegisterService.closeRegister(operatorData.chave, TIPORECE).then(function (registerClose) {
					CarrinhoDesistencia.remove(Query.build());
					registerClose = registerClose[0];
					if (_.get(registerClose, 'dadosImpressao.paramsImpressora')) {
						PerifericosService.print(registerClose.dadosImpressao.paramsImpressora).then(function () {
							self.handleCloseRegister(false);
						});
					} else {
						if (!_.isEmpty(registerClose.dadosImpressao)) {
							var registerWidget = paymentGrid.container.getWidget('report');
							if (operatorData.IDMODEIMPRES == '25') {
								_.forEach(registerClose.dadosImpressao, function (value, key) {
									registerClose.dadosImpressao[key] = _.join(_.split(value, ' | '), "\n");
								}.bind(this));
							}
							registerWidget.setCurrentRow({
								'report': _.join(_.values(registerClose.dadosImpressao),
									"\n\n******************************\n\n")
							});
							ScreenService.openPopup(registerWidget);
						} else {
							self.handleCloseRegister(false);
						}
					}
				});
			}
		});
	};

	this.printCloseRegister = function () {
		RegisterClose.findOne().then(function (registerClose) {
			for (var i in registerClose.dadosImpressao) {
				PrinterService.printerCommand(PrinterService.TEXT_COMMAND, registerClose.dadosImpressao[i]);
				PrinterService.printerCommand(PrinterService.TEXT_COMMAND, '******************************');
			}
			self.printerSpaceCommand(2);
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});
			self.handleCloseRegister(true);
		}.bind(this));
	};

	this.handleCloseRegister = function (closePopup) {
		if (closePopup) {
			ScreenService.closePopup();
		}
		if (RegisterService.getClosingOnLogin()) {
			RegisterService.setClosingOnLogin(false);
			WindowService.openWindow('OPEN_REGISTER_SCREEN');
		} else {
			UtilitiesService.backLoginScreen();
		}
	};

	function validPayments(payments) {
		var invalidPayment;
		return payments.every(function (payment) {
			var isValid = payment.IDSANGRIAAUTO == 'S' || (payment.IDSANGRIAAUTO == 'N' && payment.LABELVRMOVIVEND);
			if (!isValid) {
				ScreenService.showMessage('O tipo de recebimento ' + payment.NMTIPORECE + ' deve ter o valor preenchido.');
			}
			return isValid;
		});
	}
	this.getClosingPayments = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			RegisterService.getClosingPayments(operatorData.chave).then(function (data) {
				if (data.length === 0) {
					RegisterClosingPayments.clearAll();
				}
				if (RegisterService.getClosingOnLogin()) {
					ScreenService.showMessage("Existe movimentação para o dia anterior, o caixa deve ser fechado. Informe os valores movimentados para cada tipo de recebimento.");
				}
				widget.reload();
			});
		});
	};

	this.openPopupPaymentValue = function (widget) {
		var row = widget.currentRow;

		if (row.IDSANGRIAAUTO !== 'S') {
			var widgetPopup = widget.container.getWidget('paymentValue');

			widgetPopup.getField('LABELVRMOVIVEND').setValue(row.VRMOVIVEND);
			ScreenService.openPopup(widgetPopup);
		}
	};

	this.savePaymentValue = function (widgetEdit, paymentGrid) {
		var VRMOVIVEND = UtilitiesService.getFloat(widgetEdit.currentRow.LABELVRMOVIVEND);
		if ((typeof VRMOVIVEND !== 'number') || (isNaN(VRMOVIVEND)) || (VRMOVIVEND < 0)) {
			ScreenService.showMessage('Informe um valor válido.');
		} else {
			VRMOVIVEND = Math.abs(VRMOVIVEND);
			paymentGrid.currentRow.LABELVRMOVIVEND = UtilitiesService.toCurrency(VRMOVIVEND);
			paymentGrid.currentRow.VRMOVIVEND = VRMOVIVEND;
			ScreenService.closePopup();
            setTimeout(
                function(){
                    paymentGrid.redraw(true);
                }.bind(paymentGrid), 600);
		}
	};

	this.handleShowSideMenu = function (container) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (_.get(operatorData, 'obrigaFechamento', false) === true) {
				container.showMenu = false;
			} else {
				container.showMenu = true;
			}
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 9 || keyCode === 13) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'openRegisterWidget')
				this.openRegister(widget);
			else if (widget.name === 'paymentValue')
				this.savePaymentValue(widget, widget.container.widgets[0]);
		}
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('RegisterController', RegisterController);
});