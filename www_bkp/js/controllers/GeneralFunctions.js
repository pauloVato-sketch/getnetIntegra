function GeneralFunctions(OperatorRepository, GeneralFunctionsService, ScreenService, UtilitiesService, PrinterService, 
	PermissionService, PaymentService, ImpressaoLeituraX, IntegrationService, SSLConnectionId, ParamsMenuRepository, 
	FilterProducts, ItemSangria, Query, templateManager, IntegrationSiTEF, OperatorController){

	var self = this;
	var MESSAGE_ADMINISTRATIVE_OK;
	var MESSAGE_NULL_RESPONSE = 'Não foi pissível obter o retorno da integração.';

	var sitefConsts = IntegrationSiTEF.paymentTypeConstants().geral;

	// Reimpressão - Cupom Fiscal

	this.handleReprintPopup = function (widget) {
		widget.getField('radioReprintSaleCoupon').setValue('U');

		self.setSaleCodeLength(widget);
		self.handleReprintType(widget);
	};

	this.setSaleCodeLength = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			widget.getField('saleCode').maxlength = operatorData.IDTPEMISSAOFOS === 'SAT' ? 6 : 9;
		});
	};

	this.handleReprintType = function (widget) {
		var saleCodeField = widget.getField('saleCode');
		var searchSalesField = widget.getField('searchSales');
		var radio = widget.getField('radioReprintSaleCoupon').value();

		saleCodeField.value('');
		searchSalesField.value('');

		if (radio === 'C') {
			saleCodeField.isVisible = true;
			searchSalesField.isVisible = false;
		} else if (radio === 'V') {
			saleCodeField.isVisible = false;
			searchSalesField.isVisible = true;
		} else {
			saleCodeField.isVisible = false;
			searchSalesField.isVisible = false;
		}
	};

	this.reprintSaleCoupon = function (widget) {
		var reprintType = widget.getField('radioReprintSaleCoupon').value();
		var saleCode = widget.getField('saleCode').value();
		var searchSales = widget.getField('searchSales').value();

		saleCode = _.isEmpty(saleCode) ? searchSales : saleCode;


		if ((reprintType === 'U') || (!_.isEmpty(saleCode))) {
			saleCode = UtilitiesService.padLeft(saleCode, widget.getField('saleCode').maxlength, '0');
			GeneralFunctionsService.reprintSaleCoupon(reprintType, saleCode).then(function (result) {
				result = result[0];

				if (!result.error) {
					if (result.paramsImpressora) {
						PerifericosService.print(result.paramsImpressora).then(function () {
							PaymentService.handlePrintReceipt(result.dadosImpressao);
							ScreenService.closePopup();
						});
					} else {
						PaymentService.handlePrintReceipt(result.dadosImpressao);
						ScreenService.closePopup();
					}
				} else {
					ScreenService.showMessage(result.message, 'alert');
				}
			});
		} else {
			ScreenService.showMessage('Código do cupom fiscal inválido.', 'alert');
		}
	};

	// Reimpressão - Cupom TEF

	this.openPopupReprintTef = function (widget) {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			ScreenService.openPopup(widget);
		}.bind(this));
	};

	this.handleReprintTefType = function (widget, setOnEnter) {
		var nsuSitefField = widget.getField('nsuSitef');
		var transactionDateField = widget.getField('transactionDate');
		var transactionAuthField = widget.getField('transactionAuth');
		var transactionViaField = widget.getField('transactionVia');
		var radioReprintTefCoupon = widget.getField('radioReprintTefCoupon');

		if (setOnEnter)
			radioReprintTefCoupon.setValue('U');

		nsuSitefField.clearValue();
		transactionDateField.clearValue();
		transactionViaField.clearValue();

		transactionAuthField.isVisible = radioReprintTefCoupon.value() === 'P' || radioReprintTefCoupon.value() === 'C';
		transactionViaField.isVisible = transactionAuthField.isVisible;
		widget.getField("searchPayments").isVisible = radioReprintTefCoupon.value() === 'P';
		nsuSitefField.isVisible = radioReprintTefCoupon.value() === 'C';
		transactionDateField.isVisible = nsuSitefField.isVisible;
		self.transactionAuthOnChange(widget);

		if (!setOnEnter)
			self.updateFields(widget.fields);
	};

	this.updateFields = function (fields) {
		fields.forEach(function (field) {
			if (field.isVisible && !field.readOnly) {
				field.validations = { "required": {} };
			} else {
				field.validations = "";
			}

			if (field.name !== 'radioReprintTefCoupon') {
				field.clearValue();
				field.reload();
			}
		});
	};

	this.reprintTEFVoucher = function (widget) {
		if (widget.isValid()) {
			window.returnIntegration = _.bind(self.getReprintTextResult, this);

			if (!!window.cordova && cordova.plugins.GertecSitef) {
				self.getSitefParameters().then(function (paymentParams) {
					paymentParams.paymentType = widget.getField('radioReprintTefCoupon').value() === 'U' ? sitefConsts.reimpressaoUltimo : sitefConsts.reimpressaoEspecifica;
					paymentParams = self.handleReprintTef(widget, paymentParams);

					if (paymentParams.paymentType === sitefConsts.reimpressaoEspecifica) {
						IntegrationSiTEF.initSitefProcess(paymentParams);
					} else {
						cordova.plugins.GertecSitef.payment(JSON.stringify(paymentParams), window.returnIntegration, null);
					}
				}.bind(this));
			} else {
				window.returnIntegration(self.invalidPrinterInstance());
			}
		}
	};

	this.handleReprintTef = function (widget, paymentParams) {
		if (widget.getField('radioReprintTefCoupon').value() !== 'U') {
			var row = widget.currentRow;
			paymentParams.paymentDate = row.transactionDate.split(" ")[0].replace('/', '').replace('/', '');
			paymentParams.paymentNSU = row.nsuSitef;
			paymentParams.paymentAuth = _.isEmpty(row.transactionAuth) ? "1" : row.transactionAuth;
			paymentParams.paymentVia = _.isEmpty(row.transactionVia) ? "1" : row.transactionVia;
		} else {
			paymentParams.paymentDate = paymentParams.paymentNSU = paymentParams.paymentAuth = paymentParams.paymentVia = "";
		}

		return paymentParams;
	};

	this.getReprintTextResult = function (javaResult) {
		ScreenService.closePopup();

		if (!javaResult.error) {
			javaResult = javaResult.data;
			javaResult.merchantReceipt = _.isUndefined(javaResult.merchantReceipt) ? "" : javaResult.merchantReceipt;
			javaResult.customerReceipt = _.isUndefined(javaResult.customerReceipt) ? "" : javaResult.customerReceipt;
			PaymentService.printTEFVoucher(self.handlePrintText(javaResult));
		} else {
			ScreenService.showMessage(javaResult.message);
		}
	};

	this.handlePrintText = function (reprintObject) {
		return Array({
			'STLPRIVIA': reprintObject.customerReceipt,
			'STLSEGVIA': reprintObject.merchantReceipt
		});
	};

	this.transactionAuthOnChange = function (widget) {
		var transactionViaField = widget.getField("transactionVia");
		transactionViaField.readOnly = widget.currentRow.transactionAuth !== "2";
		transactionViaField.validations = transactionViaField.readOnly ? "" : { "required": {} };
		widget.currentRow.transactionVia = transactionViaField.readOnly ? "" : widget.currentRow.transactionVia;
		transactionViaField.reload();
	};

	// Teste de Comunicação

	this.sitefComunicateTest = function () {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			ScreenService.showLoader();
			window.returnIntegration = _.bind(self.returnAdministrativeMenu, this, false);

			if (!!window.cordova && cordova.plugins.GertecSitef) {
				MESSAGE_ADMINISTRATIVE_OK = "Teste de Comunicação OK.";

				self.getSitefParameters().then(function (sitefParams) {
					sitefParams.paymentType = sitefConsts.testeComunicacao;
					cordova.plugins.GertecSitef.payment(JSON.stringify(sitefParams), window.returnIntegration, null);
				}.bind(this));
			} else {
				window.returnIntegration(self.invalidIntegrationInstance());
			}
		}.bind(this));
	};

	// Recarga de Tabelas

	this.sitefTableLoad = function () {
		ScreenService.showLoader();
		window.returnIntegration = _.bind(self.returnAdministrativeMenu, this, true);

		if (!!window.cordova && cordova.plugins.GertecSitef) {
			MESSAGE_ADMINISTRATIVE_OK = "Tabelas Carregadas com Sucesso.";

			self.getSitefParameters().then(function (sitefParams) {
				sitefParams.paymentType = sitefConsts.carregaTabelas;
				IntegrationSiTEF.initSitefProcess(sitefParams);
			}.bind(this));
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

	// Envio de Logs para servidor SiTef

	this.sendSitefLog = function () {
		ScreenService.showLoader();
		window.returnIntegration = _.bind(self.returnAdministrativeMenu, this, true);

		if (!!window.cordova && cordova.plugins.GertecSitef) {
			MESSAGE_ADMINISTRATIVE_OK = "Logs Enviados com Sucesso.";

			self.getSitefParameters().then(function (sitefParams) {
				sitefParams.paymentType = sitefConsts.enviaLogs;
				IntegrationSiTEF.initSitefProcess(sitefParams);
			}.bind(this));
		} else {
			window.returnIntegration(self.invalidIntegrationInstance());
		}
	};

	// Estorno

	this.reversalPayment = function (widget) {
		if (widget.isValid()) {
			if (self.validateValue(widget.getField('VRMOVIVEND'))) {
				if (self.validateDate(widget)) {
					ScreenService.showLoader();
					widget.currentRow.CDNSUHOSTTEF = self.validateNSU(widget);
					var row = widget.currentRow;
					var date = _.clone(row.TRANSACTIONDATE);

					while (date.search('/') != -1) {
						date = date.replace('/', '');
					}

					OperatorRepository.findOne().then(function (operatorData) {
						GeneralFunctionsService.getNrControlTef(row.CDNSUHOSTTEF).then(function (result) {
							result = result[0];

							if (result.error) {
								ScreenService.showMessage(result.message);
								ScreenService.hideLoader();
							} else {
								IntegrationService.reversalIntegration(self.mochRemovePaymentSale,
									Array({
										'IDTIPORECE': row.IDTIPORECE,
										'VRMOVIVEND': parseFloat(row.VRMOVIVEND.split('.').join('').replace(',', '.')),
										'DSENDIPSITEF': operatorData.DSENDIPSITEF,
										'CDLOJATEF': operatorData.CDLOJATEF,
										'CDTERTEF': operatorData.CDTERTEF,
										'TRANSACTIONDATE': date,
										'CDNSUHOSTTEF': row.CDNSUHOSTTEF,
										'NRCONTROLTEF': result.data.NRCONTROLTEF,
										'IDTPTEF': '5',
										'NRCARTBANCO': result.data.NRCARTBANCO
									})
								).then(self.reversalPaymentResult);
							}
						}.bind(this),
							function (err) {
								ScreenService.hideLoader();
							});
					}.bind(this));
				}
			}
		}
	};

	this.mochRemovePaymentSale = function () {
		return new Promise.resolve(true);
	};

	this.reversalPaymentResult = function (javaResult) {
		ScreenService.hideLoader();

		if (!javaResult.error) {
			PaymentService.printTEFVoucher(javaResult.data);
			ScreenService.closePopup();
			ScreenService.showMessage("TEF estornado com sucesso.", 'success');
		} else {
			ScreenService.showMessage(javaResult.userMessage || javaResult.message);
		}
	};

	this.openPopupReversalTef = function (widget) {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			var date = new Date().toLocaleDateString('pt-BR');
			widget.getField("TRANSACTIONDATE").setValue(date);
			widget.getField("IDTIPORECE").setValue("1");
			widget.getField("VRMOVIVEND").setValue("");
			widget.getField("CDNSUHOSTTEF").setValue("");

			ScreenService.openPopup(widget);
		}.bind(this));
	};

	this.validateNSU = function (widget) {
		var CDNSUHOSTTEF = widget.currentRow.CDNSUHOSTTEF;

		while (CDNSUHOSTTEF.length < 9) {
			CDNSUHOSTTEF = '0' + CDNSUHOSTTEF;
		}

		return CDNSUHOSTTEF;
	};

	this.validateValue = function (field) {
		var value = parseFloat(field.value());

		if (isNaN(value)) {
			ScreenService.showMessage('Valor inválido.', 'alert');
			return false;
		}

		return true;
	};

	this.returnAdministrativeMenu = function (closeUI, javaResult) {
		ScreenService.hideLoader();

		if (closeUI)
			ScreenService.closePopup();

		if (!!javaResult.error) {
			ScreenService.showMessage(javaResult.message);
		} else {
			ScreenService.showMessage(MESSAGE_ADMINISTRATIVE_OK, 'SUCCESS');
		}
	};

	this.validateDate = function (widget) {
		var date = _.clone(widget.currentRow.TRANSACTIONDATE);
		date = date.split('/');

		var day = parseInt(date[0]);
		var month = parseInt(date[1]);
		var year = parseInt(date[2]);

		if (month >= 1 && month <= 12) {
			var february = self.leapYear(year) ? 29 : 28;
			var monthLength = [31, february, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

			if (day >= 1 && day <= monthLength[month - 1]) {
				if (year <= (new Date()).getFullYear())
					return true;
			}
		}

		ScreenService.showMessage("Data inválida");
		widget.currentRow.TRANSACTIONDATE = '';
		return false;
	};

	this.leapYear = function (year) {
		if (year % 4 === 0) {
			if (year % 100 === 0) {
				if (year % 400 === 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return true;
			}
		} else {
			return false;
		}
	};

	this.maskDate = function (field) {
		var date = field.value();
		date = date.replace(/\D/g, '');
		var m = date.match(/(\d{0,2})(\d{0,2})(\d{0,4})/);

		if (m[1] > 0) {
			if (m[2] > 0) {
				if (m[3] > 0) {
					date = m[1] + '/' + m[2] + '/' + m[3];
				} else {
					date = m[1] + '/' + m[2];
				}
			}
		}

		field.setValue(date);
	};

	this.getSitefParameters = function () {
		return SSLConnectionId.findOne().then(function (sSLConnectionIdResponse) {
			return OperatorRepository.findOne().then(function (operatorData) {
				var paymentParams = {
					'paymentIp': operatorData.DSENDIPSITEF,
					'paymentTerminal': operatorData.CDTERTEF,
					'paymentStore': operatorData.CDLOJATEF,
					'storeCnpj': operatorData.NRINSJURFILI,
					'IDUTLSSL': operatorData.IDUTLSSL,
					'IDCODSSL': '',
					'paymentValue': '',
					'paymentInvoice': '',
					'paymentHour': '',
					'paymentOperator': '',
					'paymentDate': '',
					'paymentNSU': '',
					'paymentAuth': '',
					'paymentVia': ''
				};

				if (sSLConnectionIdResponse) {
					paymentParams.IDCODSSL = sSLConnectionIdResponse.IDCODSSL;
				}

				return paymentParams;
			}.bind(this));
		}.bind(this));
	};

	this.invalidPrinterInstance = function () {
		return {
			'error': true,
			'message': 'Não foi possível chamar a impressora. Sua instância não existe.'
		};
	};

	this.invalidIntegrationInstance = function () {
		return {
			'error': true,
			'message': 'Não foi possível chamar a integração. Sua instância não existe.'
		};
	};

	this.generalFunctionsOnEnter = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			var showSitefFunctions = operatorData.IDTPTEF === '5';

			widget.getField("reprintOfTEFCoupon").isVisible = showSitefFunctions;
			widget.getField("comunicateTest").isVisible = showSitefFunctions;
			widget.getField("tableLoad").isVisible = showSitefFunctions;
			widget.getField("sendSitefLog").isVisible = showSitefFunctions;

			widget.getField("qrCodeSale").isVisible = operatorData.modoHabilitado === 'B' && operatorData.IDLEITURAQRCODE === 'S';
			widget.getField("reversalPayment").isVisible = showSitefFunctions;
			widget.getField("sendLogs").isVisible = showSitefFunctions;

			var showRedeFunctions = operatorData.IDTPTEF === '4';
			widget.getField("reprintOfRedeTEFCoupon").isVisible = showRedeFunctions;
			widget.getField("redeReversalPayment").isVisible = showRedeFunctions;

			widget.getField("checkPendindPayments").isVisible = operatorData.modoHabilitado === 'B';
		});
	};

	this.blockProducts = function (widget) {
		if (widget.isValid()) {
			if (!_.isEmpty(widget.currentRow.selectProducts)) {
				GeneralFunctionsService.blockProducts(widget).then(function (blockProductResult) {
					if (!blockProductResult[0].error) {
						self.setBlockUnblock(widget.currentRow.selectProducts);
						ScreenService.showMessage("Produto(s) bloqueado(s).");
						widget.getField("selectProducts").clearValue();
						ScreenService.closePopup(widget);
					} else {
						ScreenService.showMessage(blockProductResult[0].message);
					}
				}.bind(this));
			}
			else {
				ScreenService.showMessage("Favor escolher pelo menos um produto.", "alert");
			}
		}
	};

	this.setBlockUnblock = function (products) {
		ParamsMenuRepository.findAll().then(function (menuProducts) {
			menuProducts.forEach(function (menuProduct) {
				products.forEach(function (product) {
					if (menuProduct.CDPRODUTO == product) {
						menuProduct.IDPRODBLOQ = menuProduct.IDPRODBLOQ == 'N' ? 'S' : 'N';
					}
				});
			});

			ParamsMenuRepository.save(menuProducts);
		}.bind(this));
	};

	this.unblockProducts = function (widget) {
		if (widget.isValid()) {
			if (!_.isEmpty(widget.currentRow.selectBlockedProducts)) {
				GeneralFunctionsService.unblockProducts(widget).then(function (unblockProductResult) {
					if (!unblockProductResult[0].error) {
						self.setBlockUnblock(widget.currentRow.selectBlockedProducts);
						ScreenService.showMessage("Produto(s) desbloqueado(s).");
						widget.getField("selectBlockedProducts").clearValue();
						ScreenService.closePopup(widget);
					} else {
						ScreenService.showMessage(unblockProductResult[0].message);
					}
				}.bind(this));
			}
			else {
				ScreenService.showMessage("Favor escolher pelo menos um produto.", "alert");
			}
		}
	};

	// Funcao para verificar acesso de supervisor a função de carregamento de tabelas da SITEF
	this.supervisorCarregamentoTabSitef = function (widget) {
		PermissionService.checkAccess('administracaoTEF').then(function () {
			self.sitefTableLoad();
		});
	};

	// Funcao Generica para verificar acesso de supervisor para determinado nivel de acesso
	this.verificaAcessoSupervisor = function (widget, acesso) {
		PermissionService.checkAccess(acesso).then(function () {
			ScreenService.openPopup(widget);
		}.bind(this));
	};

	// Funcao Generica para limpar Field informada
	this.clearField = function (widget, field) {
		widget.getField(field).clearValue();
	};

	this.impressaoLeituraX = function (widget) {
		PermissionService.checkAccess('leituraX').then(function () {
			ScreenService.confirmMessage('Deseja imprimir o relatório da Leitura X?', 'question', function () {
				if (widget.isValid()) {
					GeneralFunctionsService.impressaoLeituraX().then(function (impressaoLeituraX) {
						impressaoLeituraX = impressaoLeituraX[0];
						if (impressaoLeituraX.error) {
							ScreenService.showMessage(impressaoLeituraX.message);
						} else {
							if (impressaoLeituraX.saas) {
								PerifericosService.print(impressaoLeituraX).then(function () {
									if (!_.isEmpty(impressaoLeituraX.dadosImpressao)) {
										self.openPopupXReport(widget, impressaoLeituraX.dadosImpressao.parcial);
									}
								});
							} else {
								if (!_.isEmpty(impressaoLeituraX.dadosImpressao)) {
									self.openPopupXReport(widget, impressaoLeituraX.dadosImpressao.parcial);
								}
							}
						}
					});
				}
			});
		}.bind(this));
	};

	this.scanSaleCode = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			UtilitiesService.callQRScanner().then(function (qrCode) {
				if (!qrCode.error) {
					qrCode = qrCode.contents;

					if (_.isEmpty(qrCode)) {
						ScreenService.showMessage("Não foi possível obter os dados do leitor.");
					}
					else {
						widget.currentRow.qrCode = qrCode;
						widget.currentRow.chave = operatorData.chave;
						var cpfPopup = widget.container.getWidget('cpfPopup');
						cpfPopup.getField('CPF').clearValue();
						PaymentService.updateSaleCode();
						ScreenService.openPopup(cpfPopup);
					}
				} else {
					ScreenService.showMessage(qrCode.message, 'alert');
				}
			}.bind(this));
		}.bind(this));
	};

	this.qrCodeSale = function (row, generalWidget) {
		var CPF = row.CPF.replace(/[^0-9 ]/g, "");

		if (_.isEmpty(CPF) || UtilitiesService.isValidCPForCNPJ(CPF)) {
			PaymentService.qrCodeSale(generalWidget.currentRow.chave, generalWidget.currentRow.qrCode, CPF).then(function (saleResult) {
				saleResult = saleResult[0];
				if (saleResult.error) {
					ScreenService.showMessage(saleResult.message, 'alert');
					if (_.get(saleResult, 'resetSaleCode')) {
						PaymentService.updateSaleCode();
					}
				}
				else {
					PaymentService.handlePrintReceipt(saleResult.dadosImpressao);
					self.payAccountFinish(saleResult);
					ScreenService.closePopup();
				}
			});
		}
		else {
			ScreenService.showMessage('CPF inválido.');
		}
	};

	this.payAccountFinish = function (payAccount) {
		var message = 'Venda realizada. ';

		if (_.get(payAccount, 'IDSTATUSNFCE') === 'P') {
			message += '<br><br>' + 'NFCE emitido em modo de contigência.';
		}
		if (_.get(payAccount, 'mensagemNfce')) {
			message += '<br>' + _.get(payAccount, 'mensagemNfce');
		}
		if (_.get(payAccount, 'mensagemImpressao')) {
			message += '<br><br>' + _.get(payAccount, 'mensagemImpressao');
		}
		ScreenService.showMessage(message);
	};

	this.openPopupXReport = function (widget, parcial) {
		var widgetReport = widget.container.getWidget('report');
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDMODEIMPRES == '25') {
				parcial = _.join(_.split(parcial, ' | '), "\n");
			}
			widgetReport.setCurrentRow({ 'report': parcial });
			ScreenService.openPopup(widgetReport);
		}.bind(this));
	};

	this.printXReport = function () {
		ImpressaoLeituraX.findOne().then(function (impressaoLeituraX) {
			PrinterService.printerCommand(PrinterService.TEXT_COMMAND, impressaoLeituraX.dadosImpressao.parcial);
			PrinterService.printerSpaceCommand();
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});

			ScreenService.closePopup();
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var field = args.owner.field;
			var widget = field.widget;

			if (widget.name === 'reprintSaleCouponPopup') {
				self.reprintSaleCoupon(widget);
			} else if (widget.name === 'unlockDeviceWidget') {
				if (field.name === 'supervisor') {
					if (!Util.isDesktop()) document.getElementById('pass').focus();
				} else if (field.name === 'pass') {
					self.handleUnlockDevice(widget.currentRow);
				}
			} else if (widget.name == 'sitefPayment') {
				IntegrationSiTEF.continueSitefProcess(widget.currentRow.userInput);
			}
		}
	};

	this.setSale = function (row, widget) {
		widget.getParent().getWidget('reprintSaleCouponPopup').getField('searchSales').value(row.NRNOTAFISCALCE);
	};

	this.getChecked = function (widget) {
		if (!_.isEmpty(FilterProducts.checkedRows)) {
			_.forEach(widget.dataSource.data, function (dataRow) {
				_.forEach(FilterProducts.checkedRows, function (checkedRow) {
					if (dataRow.CDPRODUTO == checkedRow.CDPRODUTO) {
						dataRow.__isSelected = checkedRow.__isSelected;
					}
				});
			});

			widget.setCurrentRow(FilterProducts.checkedRows);
		}
	};

	this.clearChecked = function () {
		FilterProducts.checkedRows = Array();
	};

	this.checkControl = function (widget) {
		row = widget.selectedRow;

		if (row.__index == undefined) {
			self.gridCheck(row);
		} else {
			if (row.__index >= 0) {
				self.gridCheck(row);
			}
		}
	};

	this.gridCheck = function (row) {
		if (row.__isSelected) {
			FilterProducts.checkedRows = _.concat(FilterProducts.checkedRows, row);
		} else {
			_.remove(FilterProducts.checkedRows, function (n) {
				return n.CDPRODUTO == row.CDPRODUTO;
			});
		}
	};

	this.updateSelectField = function (field) {
		var data = Array();
		field.dataSource.data = FilterProducts.checkedRows;
		_.forEach(FilterProducts.checkedRows, function (row) {
			data = _.concat(data, row.CDPRODUTO);
		}.bind(this));
		field.setValue(data);
	};

	this.clearFilter = function (widget) {
		widget.setCurrentRow({});
		widget.getField('selectProducts').dataSourceFilter = [];
	};

	this.handleCancelReprintTef = function (widget) {
		widget.fields.forEach(function (field) {
			if (field.name === 'radioReprintTefCoupon') {
				field.setValue('U');
			} else {
				field.clearValue();
				field.isVisible = false;
			}
		});

		ScreenService.closePopup();
	};

	this.hideSearchButton = function (selectWidget) {
		selectWidget.floatingControl.searchAction = false;
		selectWidget.reload();
	};

	this.addSangria = function (widget) {
		ItemSangria.findAll().then(function (item) {
			if (widget.isValid()) {
				var tipoRecebimento = widget.getField('tipoRecebimento').value();
				var valorSangria = widget.getField('valorSangria').value();
				var tipoSangria = !!widget.getField('tipoSangria').value() ? widget.getField('tipoSangria').value() : null;
				var obsSangria = !!widget.getField('obsSangria').value() ? widget.getField('obsSangria').value() : null;
				var CDTIPORECE = widget.currentRow.CDTIPORECE;
				var CDTPSANGRIA = widget.currentRow.CDTPSANGRIA;
				var IDENTIFICADOR = item.length > 0 ? _.maxBy(item, function (a) { return a.IDENTIFICADOR; }).IDENTIFICADOR + 1 : 0;

				var itemAtual = {
					tipoRecebimento: tipoRecebimento,
					valorSangria: valorSangria,
					tipoSangria: tipoSangria,
					obsSangria: obsSangria,
					CDTIPORECE: CDTIPORECE,
					CDTPSANGRIA: CDTPSANGRIA,
					IDENTIFICADOR: IDENTIFICADOR
				};

				item = _.concat(item, itemAtual);
				self.clearPopUpSangria(widget, false);

				ItemSangria.save(item).then(function (a) {
					widget.widgets[0].dataSource.data = a;
				});
			} else {
				ScreenService.showMessage('Nem todos os campos requeridos foram informados.', 'alert');
			}
		});
	};

	this.handleRequires = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDSOLTPSANGRIACX === 'N') {
				widget.getField('tipoSangria').validations = null;
			}
		});
	};

	this.removeItemSangria = function (row) {
		ItemSangria.findAll().then(function (item) {
			ScreenService.confirmMessage(
				'Deseja remover o item?', 'question',
				function () {
					_.remove(item, function (a) {
						return a.IDENTIFICADOR == row.selectedRow.IDENTIFICADOR;
					}.bind(this));
					row.owner.field.getParent().getParent().dataSource.data = item;
					ItemSangria.remove(Query.build()).then(function () {
						return ItemSangria.save(item);
					});
				},
				function () { }
			);
		});
	};

	this.clearPopUpSangria = function (widget, clearItems) {
		widget.getField('tipoRecebimento').clearValue();
		widget.getField('tipoSangria').clearValue();
		widget.getField('obsSangria').clearValue();
		widget.getField('valorSangria').clearValue();

		if (clearItems) {
			var item = [];
			widget.widgets[0].dataSource.data = item;
			ItemSangria.remove(Query.build()).then(function () {
				return ItemSangria.save(item);
			});
		}
	};

	this.saveSangria = function (widget) {
		ItemSangria.findAll().then(function (item) {
			if (widget.widgets[0].dataSource.data.length > 0) {
				ScreenService.confirmMessage(
					'Deseja imprimir o relatório?', 'question',
					function () {
						self.handleReturnSangria(item, true, widget);
					},
					function () {
						self.handleReturnSangria(item, false, widget);
					}
				);
			} else {
				ScreenService.showMessage('Nenhuma sangria foi selecionada.', 'alert');
			}
		});
	};

	this.handleReturnSangria = function (item, imprimeSangria, widget) {
		GeneralFunctionsService.saveSangria(item, imprimeSangria).then(function (retorno) {
			if (!_.isEmpty(retorno)) {
				if (!retorno[0].error) {
					if (!_.isEmpty(retorno[0].dadosImpressao)) {
						self.printSangriaSmartPos(retorno[0].dadosImpressao.sangria);
					} else if (!_.isEmpty(retorno[0].mensagemImpressao)) {
						ScreenService.showMessage(retorno[0].mensagemImpressao, 'alert');
					}
					ScreenService.goBack();
				} else {
					ScreenService.showMessage(retorno[0].message, 'error');
				}
			}
		});
	};

	this.saveRow = function (widget) {
		row = widget.widgets[0];
		row.selectedRow = widget.currentRow;
	};

	this.printSangriaSmartPos = function (sangria) {
		PrinterService.printerCommand(PrinterService.TEXT_COMMAND, sangria);
		PrinterService.printerSpaceCommand();
		PrinterService.printerInit().then(function (result) {
			if (result.error) {
				ScreenService.alertNotification(result.message);
			}
		});
	};

	this.handleToggleLock = function () {
		PermissionService.checkAccess('bloqueiaDispositivo').then(function () {
			self.toggleLock();
		}.bind(this));
	};

	this.handleUnlockDevice = function (row) {
		cordova.plugins.KioskPOS.validateMasterSupervisor(row.supervisor, row.pass,
			function (unlock) {
				if (unlock)
					self.toggleLock();
				else
					ScreenService.showMessage("Senha incorreta.");
			}
		);
	};

	this.toggleLock = function () {
		ScreenService.showCustomDialog("A aplicação será reiniciada.", "alert", "exclamation", [
			{
				label: "OK",
				"default": true,
				code: function () {
					var kiosk = cordova.plugins.KioskPOS;
					kiosk.isInKiosk(function (isInKiosk) {
						if (isInKiosk)
							kiosk.unlockDevice(self.toggleLockResult, self.toggleLockResult);
						else
							kiosk.lockDevice(self.toggleLockResult, self.toggleLockResult);
					});
				}
			}, {
				label: "Cancelar",
				code: null
			}
		]);
	};

	this.toggleLockResult = function (result) {
		result = JSON.parse(result);

		if (result.error) {
			ScreenService.showMessage(result.message);
		}
	};

	this.closeUnlockPopup = function () {
		var unlockDeviceWidget = templateManager.containers.login.getWidget("loginWidget").widgets[4];
		ScreenService.closePopup(true);
		unlockDeviceWidget.isVisible = false;
	};

	this.exportLogs = function (userInteration) {
		if (!!window.cordova && !!cordova.plugins.GertecSitef) {
			userInteration = userInteration === undefined ? true : userInteration;
			if (userInteration) {
				ScreenService.showLoader();
			}

			cordova.plugins.GertecSitef.exportLogs(function (javaResult) {
				if (javaResult.error) {
					if (userInteration) {
						ScreenService.hideLoader();
						ScreenService.showMessage(javaResult.message);
					}
				} else {
					GeneralFunctionsService.exportLogs(javaResult.content, device.serial).then(function (exportLogsResult) {
						if (userInteration) {
							ScreenService.hideLoader();
						}

						exportLogsResult = exportLogsResult[0];
						if (exportLogsResult.error) {
							if (userInteration) {
								ScreenService.showMessage(exportLogsResult.message);
							}
						} else {
							cordova.plugins.GertecSitef.deleteLogs();
							if (userInteration) {
								ScreenService.showMessage("Logs exportados com sucesso.", 'success');
							}
						}
					}.bind(this));
				}
			});
		}
	};

	this.reprintRedeCoupom = function () {
		if (!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {};
			cordova.plugins.GertecRede.reprint(JSON.stringify(params), self.reprintRedeCoupomResult, function () { });
		} else {
			self.reprintRedeCoupomResult(self.invalidIntegrationInstance());
		}
	};

	this.reprintRedeCoupomResult = function (javaResult) {
		if (javaResult === null) {
			ScreenService.showMessage(MESSAGE_NULL_RESPONSE);
		} else if (javaResult.error) {
			ScreenService.showMessage(javaResult.message);
		}
	};

	this.redeReversalPayment = function () {
		if (!!window.cordova && !!cordova.plugins.GertecRede) {
			var params = {};
			IntegrationService.reversalIntegration(self.mochRemovePaymentSale, Array({ IDTPTEF: '4' })).then(self.redeReversalPaymentResult);
		} else {
			self.redeReversalPaymentResult(self.invalidIntegrationInstance());
		}
	};

	this.redeReversalPaymentResult = function (javaResult) {
		if (javaResult === null) {
			ScreenService.showMessage(MESSAGE_NULL_RESPONSE);
		} else if (javaResult.error) {
			ScreenService.showMessage(javaResult.message);
		} else {
			ScreenService.showMessage("TEF estornado com sucesso.", 'success');
		}
	};

	this.showDeviceSerial = function () {
		ScreenService.showMessage("O serial deste dispositivo é: " + device.serial, 'success');
	};
	
	this.checkPendingPayment = function() {
		var errorMessage = "Não foram encontrados pagamentos pendentes.";

		OperatorRepository.findOne().then(function(operatorData){
			OperatorController.checkPendingPayment(operatorData.IDTPTEF, errorMessage);
		}.bind(this));
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('GeneralFunctions', GeneralFunctions);
});