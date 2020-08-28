function PaymentController(ScreenService, UtilitiesService, PaymentService, AccountController, PaymentRepository, IntegrationService, ParamsClientRepository, GetConsumerLimit, Query,
	PermissionService, OperatorRepository, PrinterService, ParamsGroupPriceChart, ParamsPriceChart, GroupPriceChart, PriceChart, AccountService, WindowService, TableController, CarrinhoDesistencia,
	PerifericosService, ProdSenhaPed) {

	// define por IDTIPORECE se pagamento tem valor máximo e se possibilita editar valor de pagamento
	var PAYMENT_TYPE = {
		'1': { max: true, repique: true },
		'2': { max: true, repique: true },
		'3': { max: false, repique: false },
		'4': { max: false, repique: true },
		'5': { max: false, repique: false },
		'6': { max: false, repique: false },
		'7': { max: false, repique: false },
		'8': { max: false, repique: false },
		'9': { max: true, repique: false },
		'A': { max: true, repique: false },
		'B': { max: false, repique: false },
		'C': { max: false, repique: false },
		'E': { max: true, repique: false },
		'F': { max: true, repique: true },
		'G': { max: true, repique: true },
		'H': { max: true, repique: true }
	};
	var MESSAGE = {
		VR_MIN: 'Valor inválido.',
		VR_MAX: 'Valor máximo excedido.',
		VR_MAX_DISCOUNT: 'Operação bloqueada. Valor máximo para desconto é de R$',
		END_PAYMENT: 'Deseja finalizar a venda?',
		ADD_PAYMENT: 'Recebimento adicionado.',
		ATT_PAYMENT: 'Recebimento alterado.',
		FAIL_PAYMENT: 'Operação bloqueada. Valor total da venda não atingido.',
		BLOCK_PAYMENT: 'Operação bloqueada. Valor total da venda já atingido.',
		REMOVE_PAYMENT: 'Deseja remover o pagamento?',
		PAYMENT_COMPLETED: 'Venda realizada. ',
		CANCEL_INTEGRATION: 'Transações eletrônicas pendentes. Deseja cancelá-las?',
		NFCE_CONTINGENCY: 'NFCE emitido em modo de contigência.',
		VR_MAX_SALDO: 'O cliente não possui saldo disponivel.',
		INFORM_CLIENT: 'Favor selecionar o consumidor antes de receber com crédito pessoal.',
		VR_SALDO_LIM: 'Operação bloqueada. O limite de crédito do consumidor será excedido. Consumo diário disponível: R$ ',
		LOW_CREDIT: 'O saldo restante deste consumidor é de: R$ ',
		NO_DISCOUNT: 'Não há desconto a ser removido.',
		NO_CREDIT: "Consumidor sem saldo disponível.",
		REMOVE_DISCOUNT: 'Deseja remover desconto já aplicado?',
		BLOCK_DISCOUNT: 'Operação bloqueada. Não é possível informar desconto enquanto existir recebimentos aplicados.',
		INATIVE_CONSUMER: 'Operação bloqueada. O Consumidor está inativo.',
		CONFIRM_PRINT: 'Deseja imprimir o cupom fiscal?',
		NO_ADDITION: 'Operação bloqueada. Esta venda não possui Gorjeta.',
		ALTER_ADDITION: 'Deseja ajustar a Gorjeta?',
		PRINT_VIA_CLI: 'Deseja imprimir a via do cliente?',
		NOT_ENOUGH_DEBIT: 'Saldo insuficiente para realizar a venda somente via débito consumidor. Favor complementar com outro tipo de recebimento.<br><br>Limite disponível: R$ ',
		NOT_ONLY_DEBIT: 'Operação bloqueada. Débito Consumidor não pode ser utilizado com outros tipos de recebimento.',
		BLOCK_PERSONAL_CREDIT: 'Operação bloqueada. Este tipo de recebimento não pode efetuar compra de crédito pessoal.',
		BLOCK_CREDIT_MULTIPLE: 'Crédito Fidelidade não pode ser utilizado junto com Crédito Pessoal.',
		DEBIT_WITH_DISCOUNT: 'Não é possível realizar uma venda de débito com desconto aplicado. Favor remover o desconto aplicado e tente novamente.'
	};

	var self = this;

	this.receivePayment = function (widget, tiporece) {
		PaymentRepository.findOne().then(function (paymentData) {
			var toPay = paymentData.DATASALE.FALTANTE;

			if (!toPay) {
				ScreenService.showMessage(MESSAGE.BLOCK_PAYMENT, 'alert');
			} else {
				if (!paymentData.CREDITOPESSOAL || !_.includes(["A", "9"], tiporece.IDTIPORECE)) {
					switch (tiporece.IDTIPORECE) {
						// debito pessoal
						case 'A':
							self.recievePersonalDebit(paymentData, widget, tiporece, toPay);
							break;
						// credito pessoal
						case '9':
							self.recievePersonalCredit(paymentData, widget, tiporece, toPay);
							break;
						default:
							self.openPaymentPopup(widget.container.getWidget('paymentPopup'), tiporece, toPay, false);
					}
				} else {
					ScreenService.showMessage(MESSAGE.BLOCK_PERSONAL_CREDIT);
				}
			}
		});
	};

	this.recievePersonalDebit = function (paymentData, widget, tiporece, toPay) {
		if (!paymentData.CDCLIENTE || !paymentData.CDCONSUMIDOR) {
			ScreenService.showMessage(MESSAGE.INFORM_CLIENT, 'alert');
			return;
		}
		if (!_.isEmpty(paymentData.TIPORECE)) {
			ScreenService.showMessage(MESSAGE.NOT_ONLY_DEBIT, 'alert');
			return;
		}
		if (paymentData.DATASALE.PCTDESCONTO > 0 || paymentData.DATASALE.VRDESCONTO > 0) {
			ScreenService.showMessage(MESSAGE.DEBIT_WITH_DISCOUNT, 'alert');
			return;
		}

		PaymentService.getConsumerLimit(paymentData.CDCLIENTE, paymentData.CDCONSUMIDOR, 'debito').then(function (consumerLimit) {
			var SALDO_ATUAL = consumerLimit[0].SALDO_ATUAL;
			var LIMITE_ATUAL = consumerLimit[0].LIMITE_ATUAL;
			var CONSUMO_DIA = consumerLimit[0].CONSUMO_DIA;
			var CONSUMO_MES = consumerLimit[0].CONSUMO_MES;
			var VRLIMDEBCONS = consumerLimit[0].VRLIMDEBCONS;
			var VRMAXDEBCONS = consumerLimit[0].VRMAXDEBCONS;
			var VRMAXCONSDIAD = consumerLimit[0].VRMAXCONSDIAD;
			var VRMAXCONSMESD = consumerLimit[0].VRMAXCONSMESD;
			var VRAVIDEBCONS = consumerLimit[0].VRAVIDEBCONS;
			var IDPERCOMVENCPDC = consumerLimit[0].IDPERCOMVENCPDC;

			var totalDebito = parseFloat((paymentData.DATASALE.TOTALVENDA - paymentData.DATASALE.REALSUBSIDY).toFixed(2));

			if (!IDPERCOMVENCPDC && VRMAXCONSMESD && totalDebito + CONSUMO_MES > VRMAXCONSMESD) {
				ScreenService.showMessage('Operação bloqueada. O consumidor ' + consumerLimit[0].CDCONSUMIDOR + ' - ' + consumerLimit[0].NMCONSUMIDOR + ' não pode exceder o valor máximo de consumo mensal por tipo de consumidor/loja - R$ ' + UtilitiesService.toCurrency(VRMAXCONSMESD) + '. Favor verificar parametrização no sistema.');
			}
			else if (!IDPERCOMVENCPDC && VRMAXCONSDIAD && totalDebito + CONSUMO_DIA > VRMAXCONSDIAD) {
				ScreenService.showMessage('Operação bloqueada. O consumidor ' + consumerLimit[0].CDCONSUMIDOR + ' - ' + consumerLimit[0].NMCONSUMIDOR + ' não pode exceder o valor máximo de consumo diário por tipo de consumidor/loja - R$ ' + UtilitiesService.toCurrency(VRMAXCONSDIAD) + '. Favor verificar parametrização no sistema.');
			}
			else if (!IDPERCOMVENCPDC && VRMAXDEBCONS && totalDebito + CONSUMO_DIA > VRMAXDEBCONS) {
				ScreenService.showMessage('Operação bloqueada. O consumidor ' + consumerLimit[0].CDCONSUMIDOR + ' - ' + consumerLimit[0].NMCONSUMIDOR + ' não pode exceder o valor de consumo diário - R$ ' + UtilitiesService.toCurrency(VRMAXDEBCONS) + '. Favor verificar parametrização no sistema.');
			}
			else if (!IDPERCOMVENCPDC && VRLIMDEBCONS && SALDO_ATUAL - totalDebito < VRLIMDEBCONS) {
				ScreenService.showMessage('Operação bloqueada. Saldo do consumidor não pode ficar inferior a R$ ' + UtilitiesService.toCurrency(VRLIMDEBCONS) + '. Saldo atual : R$ ' + UtilitiesService.toCurrency(SALDO_ATUAL) + '.', 'alert');
			}
			else {
				var toPay = paymentData.DATASALE.TOTALVENDA;
				if (LIMITE_ATUAL != null) {
					var paymentDiff = parseFloat((LIMITE_ATUAL - toPay + paymentData.DATASALE.REALSUBSIDY).toFixed(2));
					if (paymentDiff < 0) {
						toPay = LIMITE_ATUAL;
						ScreenService.showMessage(MESSAGE.NOT_ENOUGH_DEBIT + UtilitiesService.toCurrency(LIMITE_ATUAL), 'alert');
					}
				}

				if (toPay > 0) {
					self.openPaymentPopup(widget.container.getWidget('paymentPopup'), tiporece, toPay, true);
				}
			}
		});
	};

	this.recievePersonalCredit = function (paymentData, widget, tiporece, toPay) {
		if (paymentData.DATASALE.FIDELITYDISCOUNT > 0) {
			ScreenService.showMessage(MESSAGE.BLOCK_CREDIT_MULTIPLE, 'alert');
			return;
		}
		if (paymentData.CDCLIENTE && paymentData.CDCONSUMIDOR) {
			PaymentService.getConsumerLimit(paymentData.CDCLIENTE, paymentData.CDCONSUMIDOR, 'credito').then(function (consumerLimit) {
				var limiteDisponivel = consumerLimit[0].limiteDisponivel;
				if (limiteDisponivel && limiteDisponivel - paymentData.DATASALE.TOTALVENDA < 0) {
					ScreenService.showMessage(MESSAGE.VR_SALDO_LIM + UtilitiesService.toCurrency(limiteDisponivel), 'alert');
				}
				else if (consumerLimit[0].saldoDisponivel <= 0) {
					ScreenService.showMessage(MESSAGE.NO_CREDIT);
				}
				else {
					var saldoDisponivel = consumerLimit[0].saldoDisponivel;
					if (saldoDisponivel < paymentData.DATASALE.FALTANTE) {
						toPay = saldoDisponivel;
						ScreenService.showMessage(MESSAGE.LOW_CREDIT + UtilitiesService.toCurrency(saldoDisponivel), 'alert');
					}
					self.openPaymentPopup(widget.container.getWidget('paymentPopup'), tiporece, toPay, false);
				}
			});
		} else {
			ScreenService.showMessage(MESSAGE.INFORM_CLIENT);
		}
	};

	this.openPaymentPopup = function (paymentPopup, tiporece, toPay, locked) {
		self.setPaymentPopupProperty(paymentPopup, tiporece, toPay, locked).then(function () {
			ScreenService.openPopup(paymentPopup);
		}.bind(this));
	};

	this.setPaymentPopupProperty = function (openPopup, tiporece, toPay, locked) {
		return OperatorRepository.findOne().then(function (operatorData) {
			var fieldValue = openPopup.getField('VRMOVIVEND');
			var fieldNSU = openPopup.getField('CDNSUHOSTTEF');

			openPopup.label = tiporece.DSBUTTON;
			openPopup.currentRow = self.currentRowDefaultValue(tiporece);

			//Controle de valores máximos possíveis no pagamento do repique, respeitando a parametrização.
			if (operatorData.IDTPCONTRREPIQ !== 'N' && tiporece.IDUTCONTRREPIQ !== 'N' && PAYMENT_TYPE[tiporece.IDTIPORECE].repique) {
				fieldValue.range.max = null;
			} else {
				fieldValue.range.max = (PAYMENT_TYPE[tiporece.IDTIPORECE].max) ? toPay : null;
			}
			fieldValue.setValue(toPay);
			fieldValue.readOnly = locked;

			fieldNSU.maxlength = operatorData.QTDMAXDIGNSU || 10;

			if (!self.showFieldNSU(tiporece, operatorData)) {
				fieldValue.class = "6 center-align-field";
				fieldNSU.isVisible = false;
			} else {
				fieldValue.class = 6;
				fieldNSU.isVisible = true;
			}
		}.bind(this));
	};

	this.currentRowDefaultValue = function (tiporece) {
		return {
			tiporece: tiporece,
			eletronicTransacion: { status: false, data: IntegrationService.integrationData() }
		};
	};

	this.showFieldNSU = function (tiporece, operatorData) {
		// validação para mostrar field NSU nos recebimentos do tipo POS
		return tiporece.IDDESABTEF === 'S' && operatorData.IDSOLICITANSU === 'S' &&
			_.includes(PaymentService.PAYMENT_INTEGRATION, tiporece.IDTIPORECE);
	};

	this.setPayment = function (widget) {
		var currentRow = _.clone(widget.currentRow);
		var widgetPayment = widget.container.getWidget('paymentMenu');
		var valorRecebimento = UtilitiesService.getFloat(widget.getField('VRMOVIVEND').value());

		if (widget.isValid()) {
			currentRow.VRMOVIVEND = UtilitiesService.getFloat(currentRow.VRMOVIVEND);
			widget.getField('VRMOVIVEND').setValue(currentRow.VRMOVIVEND);
			currentRow.eletronicTransacion.data.CDNSUHOSTTEF = currentRow.CDNSUHOSTTEF;
			if (self.validValue(widget.getField('VRMOVIVEND'), '')) {
				self.trataRepique(currentRow, widgetPayment, valorRecebimento);
			}
		}
	};

	this.trataRepique = function (currentRow, widgetPayment, valorRecebimento) {
		PaymentRepository.findOne().then(function (paymentData) {
			OperatorRepository.findOne().then(function(operatorData){
				var valorRepique, valorMaximoRepique, VRPEMAXREPIQVND, fieldValorRepique;

				// Algumas das parametrizações do repique já são tratadas na variável showRepique no paymentService.js
				if (paymentData.showRepique && PAYMENT_TYPE[currentRow.tiporece.IDTIPORECE].repique && (valorRecebimento > paymentData.DATASALE.FALTANTE) && currentRow.tiporece.IDUTCONTRREPIQ !== 'N') {
					VRPEMAXREPIQVND = UtilitiesService.getFloat(operatorData.VRPEMAXREPIQVND);
					valorMaximoRepique = Math.trunc(((VRPEMAXREPIQVND / 100) * paymentData.DATASALE.TOTAL) * 100) / 100;
					valorRepique = Math.round((valorRecebimento - paymentData.DATASALE.FALTANTE) * 100) / 100;
					fieldValorRepique = widgetPayment.container.getWidget('Repique').getField('valorRepique');

					if (operatorData.IDTPCONTRREPIQ === 'V' && currentRow.tiporece.IDUTCONTRREPIQ === 'S') {
						ScreenService.confirmMessage("Deseja alterar o valor do repique? ", 'question',
							function () {
								ScreenService.openPopup(widgetPayment.container.getWidget('Repique')).then(function(){
									fieldValorRepique.label = "Valor: (Troco: " + UtilitiesService.toCurrency(valorRepique) + ")";
									fieldValorRepique.clearValue();
								});
							}, function () {
								if (VRPEMAXREPIQVND > 0 && valorRepique > valorMaximoRepique) {
									ScreenService.showMessage('Operação bloqueada. O valor do repique excede o valor máximo possível.');
								} else {
									currentRow.REPIQUE = valorRepique;
									self.handlePaymentData(currentRow, widgetPayment);
								}
							}
						);
					} else {
						if (VRPEMAXREPIQVND > 0 && valorRepique > valorMaximoRepique) {
							ScreenService.showMessage('Operação bloqueada. O valor do repique excede o valor máximo possível.');
						} else {
							currentRow.REPIQUE = valorRepique;
							self.handlePaymentData(currentRow, widgetPayment);
						}
					}
				} else {
					self.handlePaymentData(currentRow, widgetPayment);
				}
			}.bind(this));
		}.bind(this));
	};

	this.alteraRepique = function (widgetRepique) {
		PaymentRepository.findOne().then(function (paymentData) {
			OperatorRepository.findOne().then(function(operatorData){
				var VRPEMAXREPIQVND = UtilitiesService.getFloat(operatorData.VRPEMAXREPIQVND);
				var paymentPopup = widgetRepique.container.getWidget('paymentPopup');
				var currentRow = _.clone(paymentPopup.currentRow);
				var valorRecebimento = UtilitiesService.getFloat(paymentPopup.getField('VRMOVIVEND').value());
				var widgetPayment = widgetRepique.container.getWidget('paymentMenu');
				var valorRepique = UtilitiesService.getFloat(widgetRepique.getField('valorRepique').value());

				valorTroco = Math.round((valorRecebimento - paymentData.DATASALE.FALTANTE) * 100) / 100;

				if (valorRepique > 0){
					if (valorRepique <= valorTroco) {
						var valorMaximoRepique = Math.trunc(((VRPEMAXREPIQVND / 100) * paymentData.DATASALE.TOTAL) * 100) / 100;
						if (VRPEMAXREPIQVND > 0 && valorRepique > valorMaximoRepique) {
							ScreenService.showMessage('Operação bloqueada. O valor do repique excede o valor máximo possível.');
						} else {
							currentRow.REPIQUE = valorRepique;
							self.handlePaymentData(currentRow, widgetPayment);
						}
					} else {
						ScreenService.showMessage('Operação bloqueada. O valor do repique precisa ser menor ou igual ao valor do troco.');
					}
				} else {
					ScreenService.showMessage('Digite o valor do repique a ser alterado.');
				}
			}.bind(this));
		}.bind(this));
	};

	this.handlePaymentData = function (currentRow, widgetPayment) {
		ScreenService.closePopup();
		ScreenService.showLoader();
		PaymentService.handlePayment(currentRow).then(function (handlePaymentResult) {
			ScreenService.hideLoader();
			if (!handlePaymentResult.error) {
				self.paymentFinish(widgetPayment, handlePaymentResult.data);
			} else {
				self.handleSetPaymentError(handlePaymentResult);
			}
		}.bind(this));
	};

	this.handleSetPaymentError = function (handlePaymentResult) {
		ScreenService.showMessage(handlePaymentResult.message, 'alert').then(function () {
			handlePaymentResult = handlePaymentResult.data;

			if (!_.isEmpty(handlePaymentResult) && handlePaymentResult.IDTPTEF === '5' && handlePaymentResult.errorCode === -43) {
				GeneralFunctions.sendSitefLog();
			}
		}.bind(this));
	};

	this.handleConsumerDebit = function (widget) {
		if (widget.currentRow.tiporece.IDTIPORECE == 'A') {
			GetConsumerLimit.findOne().then(function (limits) {
				var leftover = parseFloat((limits.SALDO_ATUAL - widget.currentRow.VRMOVIVEND).toFixed(2));
				if (limits.VRAVIDEBCONS && leftover <= limits.VRAVIDEBCONS) {
					ScreenService.showMessage('Consumidor : ' + limits.NMCONSUMIDOR + '<br>Saldo Empresa : R$ ' + UtilitiesService.toCurrency(leftover) + '<br>Favor comprar novos créditos.', 'alert').then(function () {
						self.setPayment(widget);
					});
				}
				else {
					self.setPayment(widget);
				}
			});
		}
		else {
			self.setPayment(widget);
		}
	};

	this.validValue = function (field, customMessage) {
		var value = field.value();
		value = UtilitiesService.removeCurrency(value);
		var min = field.range.min;
		var max = field.range.max;

		if ((value < min) || isNaN(value)) {
			ScreenService.showMessage(MESSAGE.VR_MIN, 'alert');
			return false;
		} else if (typeof max === 'number') {
			if (value > max) {
				ScreenService.showMessage(_.isEmpty(customMessage) ? MESSAGE.VR_MAX : customMessage, 'alert');
				return false;
			}
		}

		return true;
	};

	this.paymentFinish = function (widgetPayment, DATASALE) {
		self.attStripeData(widgetPayment);
		ScreenService.closePopup();

		if (!DATASALE.FALTANTE && !DATASALE.TROCO && !DATASALE.REPIQUE) {
			self.verifyFinishPayment(widgetPayment.container.getWidget('consumerCPFPopup'));
		}
	};

	this.verifyFinishPayment = function (widget) {
		PaymentService.getPaymentValue().then(function (DATASALE) {
			if (!DATASALE.FALTANTE) {
				PaymentRepository.findOne().then(function (payment) {
					if (!payment.CREDITOPESSOAL) {
						self.payAccount();
					}
					else {
						PaymentService.chargePersonalCredit(payment).then(function (paymentResult) {
							if (paymentResult[0].dadosImpressao != null) {
								PrinterService.printerCommand(PrinterService.TEXT_COMMAND, paymentResult[0].dadosImpressao.RECEIPT);
								PrinterService.printerSpaceCommand();
								PrinterService.printerInit().then(function (result) {
									if (result.error)
										ScreenService.alertNotification(result.message);
								});
							}
							PaymentRepository.clearAll().then(function () {
								AccountController.finishPayAccount();
							});
						});
					}
				}.bind(this));
			} else {
				ScreenService.showMessage(MESSAGE.FAIL_PAYMENT, 'alert');
			}
		});
	};

	this.prepareCPFPopup = function (consumerCPFPopup) {
		OperatorRepository.findOne().then(function (operatorData) {
			PaymentRepository.findOne().then(function (paymentData) {
				if (operatorData.IDSOLICITACPF === 'S' || operatorData.IDCOLETOR !== 'C' || operatorData.IDSOLDIGCONS === 'S') {
					consumerCPFPopup.container.getWidget('paymentMenu').getAction('info').isVisible = true;
					consumerCPFPopup.getField('NRINSCRCONS').isVisible = operatorData.IDSOLICITACPF === 'S';
					consumerCPFPopup.getField('EMAIL').isVisible = operatorData.IDCOLETOR !== 'C';
					consumerCPFPopup.getField('NOMECONS').isVisible = operatorData.IDSOLDIGCONS === 'S';
					consumerCPFPopup.getField('NMFANVEN').isVisible = (operatorData.modoHabilitado === 'B' && operatorData.IDUTLSENHAOPER === 'C');
					consumerCPFPopup.getField("DSOBSFINVEN").isVisible = operatorData.IDSOLOBSFINVEN === 'S';

					consumerCPFPopup.currentRow.NRINSCRCONS = paymentData.NRINSCRCONS;
					consumerCPFPopup.currentRow.EMAIL = paymentData.EMAIL;
					consumerCPFPopup.currentRow.NOMECONS = paymentData.NOMECONS;
					consumerCPFPopup.currentRow.CDVENDEDOR = paymentData.CDVENDEDOR;
					consumerCPFPopup.currentRow.NMFANVEN = paymentData.NMFANVEN;
					consumerCPFPopup.currentRow.DSOBSFINVEN = paymentData.DSOBSFINVEN;

					ScreenService.openPopup(consumerCPFPopup);
				} else {
					consumerCPFPopup.container.getWidget('paymentMenu').getAction('info').isVisible = false;
				}
			});
		});
	};

	this.handleShowPaymentList = function (widget) {
		PaymentService.findAllPayment().then(function (arrTiporece) {
			if (!_.isEmpty(arrTiporece)) {
				self.handlePaymentScreen(widget, 'paymentList');
			} else {
				ScreenService.showMessage('Lista de recebimentos está vazia.', 'alert');
			}
		});
	};

	this.handlePaymentScreen = function (widget, nameWidgetToShow) {
		// alterna entre widgets do fluxo de pagamento
		widget.isVisible = false;

		var widgetToShow = widget.container.getWidget(nameWidgetToShow);
		widgetToShow.isVisible = true;
		widgetToShow.activate();
	};

	this.closeConsumerPopup = function (widget) {
		ScreenService.closePopup();
		widget.container.getWidget('paymentMenu').activate();
	};

	this.handleConsumerPopup = function (popup) {
		var clientField = popup.getField('NMRAZSOCCLIE');
		var searchField = popup.getField('consumerSearch');
		var consumerField = popup.getField('NMCONSUMIDOR');

		self.clearConsumerPopup(popup);

		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado == 'B') {
				popup.container.getWidget("paymentMenu").getAction("setconsumer").isVisible = true;
			}
			else {
				popup.container.getWidget("paymentMenu").getAction("setconsumer").isVisible = false;
			}

			var permiteDig = operatorData.IDPERDIGCONS == 'S';

			clientField.readOnly = permiteDig;
			searchField.readOnly = permiteDig;
			consumerField.readOnly = permiteDig;

			popup.getAction("qrcode").isVisible = !Util.isDesktop();
		});
	};

	this.attPaymentList = function (widget) {
		PaymentService.findAllPayment().then(function (arrTiporece) {
			widget.dataSource.data = _.map(arrTiporece, function (tiporece) {
				tiporece.VRMOVIVEND = UtilitiesService.toCurrency(tiporece.VRMOVIVEND);
				return tiporece;
			});

			if (_.isEmpty(widget.dataSource.data)) {
				self.handlePaymentScreen(widget, 'paymentMenu');
			}
		});
	};

	this.setPaymentRow = function (widget) {
		widget.container.getWidget('cancelPayment').currentRow = widget.currentRow;
	};

	this.removePayment = function (widget) {
		ScreenService.confirmMessage(
			MESSAGE.REMOVE_PAYMENT, 'question',
			function () {
				PaymentService.handleRemovePayment(widget.currentRow).then(function (handleRemovePaymentResult) {
					if (!handleRemovePaymentResult.error) {
						var widgetPaymentList = widget.container.getWidget('paymentList');
						self.attStripeData(widgetPaymentList);
						self.attPaymentList(widgetPaymentList);
					} else {
						ScreenService.showMessage(handleRemovePaymentResult.message, 'alert');
					}
				}.bind(this));
			},
			function () { }
		);
	};

	this.attScreen = function (widget) {
		self.attStripeData(widget.container.getWidget('paymentMenu'));
		ScreenService.closePopup();
		widget.container.getWidget('paymentMenu').activate();
	};

	this.setConsumerInfo = function (consumerCPFInfoWidget) {
		var isValid = true;

		var NRINSCRCONS = consumerCPFInfoWidget.currentRow.NRINSCRCONS || null;
		var EMAIL = consumerCPFInfoWidget.currentRow.EMAIL || null;
		var NOMECONS = consumerCPFInfoWidget.currentRow.NOMECONS || null;
		var DSOBSFINVEN = consumerCPFInfoWidget.currentRow.DSOBSFINVEN || null;
		var CDVENDEDOR = null;
		var NMFANVEN = null;
		var vendedores = _.filter(consumerCPFInfoWidget.getField('NMFANVEN').dataSource.data, function (o) { return o.__isSelected; });

		if (vendedores.length > 0) {
			CDVENDEDOR = vendedores[0].CDVENDEDOR;
			NMFANVEN = vendedores[0].NMFANVEN;
		}

		if (NRINSCRCONS) {
			NRINSCRCONS = NRINSCRCONS.replace(/[^0-9 ]/g, "");
			isValid = UtilitiesService.isValidCPForCNPJ(NRINSCRCONS);
			if (!isValid) {
				ScreenService.showMessage('CPF/CNPJ inválido.', 'alert');
				return;
			}
		}

		if (EMAIL) {
			isValid = UtilitiesService.checkEmail(EMAIL);
			if (!isValid) {
				ScreenService.showMessage('E-mail inválido.', 'alert');
				return;
			}
		}

		if (isValid) {
			PaymentRepository.findOne().then(function (paymentData) {
				paymentData.NRINSCRCONS = NRINSCRCONS;
				paymentData.EMAIL = EMAIL;
				paymentData.NOMECONS = NOMECONS;
				paymentData.CDVENDEDOR = CDVENDEDOR;
				paymentData.NMFANVEN = NMFANVEN;
				paymentData.DSOBSFINVEN = DSOBSFINVEN;
				PaymentRepository.save(paymentData);
				ScreenService.closePopup();
			});
		}
	};


	this.payAccount = function () {
		PaymentService.payAccount().then(function (payAccountResult) {
			if (!_.isEmpty(_.get(payAccountResult, 'data.paramsImpressora'))) {
				PerifericosService.print(payAccountResult.data.paramsImpressora).then(function (result) {
					if (!payAccountResult.error) {
						if (!_.isEmpty(result.message)) {
							self.handlePrintNote(payAccountResult);
						} else self.payAccountFinish(payAccountResult);
					} else {
						ScreenService.showMessage(payAccountResult.message, 'error');
						if (_.get(payAccountResult, 'data.resetSaleCode')) {
							PaymentService.updateSaleCode();
						}
					}
				});
			} else {
				if (!payAccountResult.error) {
					if (!_.isEmpty(payAccountResult.data.dadosImpressao)) {
						payAccountResult.data.dadosImpressao.TEFVOUCHER = [];
						self.handlePrintNote(payAccountResult);
					} else self.payAccountFinish(payAccountResult);
				} else {
					ScreenService.showMessage(payAccountResult.message, 'error');
					if (_.get(payAccountResult, 'data.resetSaleCode')) {
						PaymentService.updateSaleCode();
					}
				}
			}
			// Realiza impressão de pedidos na maquininha no modo balcão.
			if (!_.isEmpty(_.get(payAccountResult, 'data.impressaoPedidoSmart'))) {
				self.printOrderIntegration(payAccountResult.data.impressaoPedidoSmart);
			}
		}.bind(this));
	};

	this.payAccountFinish = function (payAccount) {
		CarrinhoDesistencia.remove(Query.build());
		ProdSenhaPed.remove(Query.build());
		var message = MESSAGE.PAYMENT_COMPLETED;

        if (_.get(payAccount, 'data.messageCurl')) {
            message += '<br><br>' + _.get(payAccount, 'data.messageCurl');
        }
		if (_.get(payAccount, 'data.IDSTATUSNFCE') === 'P') {
			message += '<br><br>' + MESSAGE.NFCE_CONTINGENCY;
		}
		if (_.get(payAccount, 'data.mensagemNfce')) {
			var retornoNfce = _.get(payAccount, 'data.mensagemNfce');
			if (!~retornoNfce.indexOf("100 - ")) {
				message += '<br><br>' + _.get(payAccount, 'data.mensagemNfce');
			}
		}
		if (_.get(payAccount, 'data.mensagemImpressao')) {
			message += '<br><br>' + _.get(payAccount, 'data.mensagemImpressao');
		}

		if (!_.isEmpty(_.get(payAccount, 'data.errPainelSenha'))) {
			ScreenService.notificationMessage(payAccount.data.errPainelSenha, 'error');
		}

		ScreenService.showMessage(message);

		if (_.get(payAccount, 'data.IDSTMESAAUX') === 'R') {
			TableController.openAccountPayment();
		} else {
			AccountController.finishPayAccount();
		}
	};

	this.attStripeData = function (widget) {
		var stripeWidget = widget.container.getWidget('paymentStripe');

		PaymentRepository.findOne().then(function (paymentData) {
			stripeWidget.getField('limitDebitoLabel').isVisible = _.get(paymentData, "limitDebito.LIMITE_ATUAL");
			stripeWidget.getField('limitDebito').isVisible = _.get(paymentData, "limitDebito.LIMITE_ATUAL");
			stripeWidget.getField('limitCreditoLabel').isVisible = _.get(paymentData, "limitCredito[0].VRLIMDEBCONS");
			stripeWidget.getField('limitCredito').isVisible = _.get(paymentData, "limitCredito[0].VRLIMDEBCONS");
			stripeWidget.getField('repiqueLabel').isVisible = _.get(paymentData, "showRepique");
			stripeWidget.getField('REPIQUE').isVisible = _.get(paymentData, "showRepique");
			stripeWidget.currentRow = {
				TOTALVENDA: UtilitiesService.toCurrency(paymentData.DATASALE.TOTALVENDA),
				VALORPAGO: UtilitiesService.toCurrency(paymentData.DATASALE.VALORPAGO),
				FALTANTE: UtilitiesService.toCurrency(paymentData.DATASALE.FALTANTE),
				TROCO: UtilitiesService.toCurrency(paymentData.DATASALE.TROCO),
				REPIQUE: UtilitiesService.toCurrency(UtilitiesService.removeCurrency((_.get(paymentData, "DATASALE.REPIQUE", 0)) || 0)),
				limitDebito: UtilitiesService.toCurrency(UtilitiesService.removeCurrency((_.get(paymentData, "limitDebito.LIMITE_ATUAL", 0)) || 0)),
				limitCredito: UtilitiesService.toCurrency(UtilitiesService.removeCurrency((_.get(paymentData, "limitCredito[0].VRLIMDEBCONS", 0)) || 0) / 100)
			};
		});

		widget.activate();
	};

	this.initButtons = function (container) {
		var paymentGroupType = container.getWidget('categories');

		PaymentRepository.findOne().then(function (paymentData) {
			if (_.isEmpty(paymentData.IDTPVENDACONS) || paymentData.CREDITOPESSOAL) {
				ParamsGroupPriceChart.findAll().then(function (paymentGroups) {
					ParamsPriceChart.findAll().then(function (paymentTypes) {
						GroupPriceChart.save(paymentGroups).then(function () {
							PriceChart.save(paymentTypes).then(function () {
								paymentGroupType.reload();
							}.bind(this));
						}.bind(this));
					}.bind(this));
				}.bind(this));
			}
			else {
				this.setPaymentTypes(paymentData.IDTPVENDACONS, container);
			}
		}.bind(this));
	};

	this.cancelForSale = function (widget) {
		// cancela venda
		PaymentService.findIntegrations().then(function (integrations) {
			if (integrations.error) {
				self.backPayment();
			} else {
				ScreenService.confirmMessage(
					MESSAGE.CANCEL_INTEGRATION, 'question',
					function () {
						PaymentService.handleCancelForSale(integrations.data).then(function (handleCancelForSaleResult) {
							if (!handleCancelForSaleResult.error) {
								self.backPayment();
							} else {
								self.attStripeData(widget.container.getWidget('paymentMenu'));
								ScreenService.showMessage(handleCancelForSaleResult.message, 'alert');
							}
						});
					}.bind(this),
					function () { }
				);
			}
		});
	};

	this.backPayment = function () {
		PaymentService.clearPayment();
		ScreenService.goBack();
	};

	this.openDiscount = function (paymentWidget) {
		PaymentService.handleOpenDiscount().then(function (handleOpenDiscountResult) {
			if (!handleOpenDiscountResult.error) {
				PermissionService.checkAccess('cupomDesconto').then(function (CDSUPERVISOR) {
					var discountPopup = paymentWidget.container.getWidget('discountPopup');
					discountPopup.currentRow.CDSUPERVISORd = CDSUPERVISOR;

					ScreenService.openPopup(discountPopup).then(function () {
						self.getDiscount(discountPopup);
					}.bind(this));
				}.bind(this));
			} else {
				ScreenService.showMessage(MESSAGE.BLOCK_DISCOUNT, 'alert');
			}
		});
	};

	this.clearConsumerPopup = function (popup) {
		popup.currentRow.CDCLIENTE = "";
		popup.currentRow.NMRAZSOCCLIE = "";
		popup.currentRow.CDCONSUMIDOR = "";
		popup.currentRow.NMCONSUMIDOR = "";
		popup.getField('NMRAZSOCCLIE').clearValue();
		popup.getField('consumerSearch').clearValue();
		popup.getField('NMCONSUMIDOR').clearValue();
		popup.getField('NMCONSUMIDOR').dataSourceFilter = [
			{
				"name": "CDCLIENTE",
				"operator": "=",
				"value": ""
			}
		];
	};

	this.openConsumerPopup = function (popup) {
		PaymentService.findAllPayment().then(function (payments) {
			if (_.isEmpty(payments)) {
				PaymentRepository.findOne().then(function (paymentData) {
					if (_.isEmpty(paymentData.CDCLIENTE)) {
						self.clearConsumerPopup(popup);
					}
					popup.getField('consumerSearch').clearValue();

					ParamsClientRepository.findAll().then(function (clients) {
						if (!_.isEmpty(paymentData.CDCLIENTE)) {
							popup.currentRow.CDCLIENTE = paymentData.CDCLIENTE;
							popup.currentRow.NMRAZSOCCLIE = paymentData.NMRAZSOCCLIE;
							popup.getField('NMRAZSOCCLIE').setValue(paymentData.NMRAZSOCCLIE);
						}
						else {
							if (clients.length == 1) {
								popup.currentRow.CDCLIENTE = clients[0].CDCLIENTE;
								popup.currentRow.NMRAZSOCCLIE = clients[0].NMRAZSOCCLIE;
								popup.getField('NMRAZSOCCLIE').setValue(clients[0].NMRAZSOCCLIE);
								popup.getField('NMCONSUMIDOR').dataSourceFilter = [
									{
										"name": "CDCLIENTE",
										"operator": "=",
										"value": ""
									}
								];
							}
						}
						if (!_.isEmpty(paymentData.CDCONSUMIDOR)) {
							popup.currentRow.CDCONSUMIDOR = paymentData.CDCONSUMIDOR;
							popup.currentRow.NMCONSUMIDOR = paymentData.NMCONSUMIDOR;
							popup.getField('NMCONSUMIDOR').setValue(paymentData.NMCONSUMIDOR);
						}
						ScreenService.openPopup(popup);
					});
				});
			}
			else {
				ScreenService.showMessage("Não é possível informar o consumidor com recebimentos lançados.");
			}
		});
	};

	this.prepareCustomers = function (currentRow, clientField) {
		var consumerField = clientField.widget.getField('NMCONSUMIDOR');
		consumerField.clearValue();
		if (!_.isEmpty(currentRow.CDCLIENTE)) {
			consumerField.dataSourceFilter[0].value = currentRow.CDCLIENTE;
			if (consumerField.dataSourceFilter[1]) {
				consumerField.dataSourceFilter[1].value = "";
			}
			var searchField = clientField.widget.getField('consumerSearch');
			if (searchField) searchField.clearValue();
		}
		else {
			currentRow.CDCLIENTE = "";
			consumerField.dataSourceFilter[0].value = "";
		}
	};

	var t;
	this.consumerSearch = function () {
		clearTimeout(t);
		var searchConsumer = function () {
			var consumerField = ApplicationContext.templateManager.container.getWidget('setConsumerPopUp').getField('NMCONSUMIDOR');
			var popup = ApplicationContext.templateManager.container.getWidget('setConsumerPopUp');

			consumerField.clearValue();

			consumerField.dataSourceFilter = [
				{
					name: 'CDCLIENTE',
					operator: '=',
					value: _.isEmpty(popup.currentRow.CDCLIENTE) ? "" : popup.currentRow.CDCLIENTE
				},
				{
					name: 'CDCONSUMIDOR',
					operator: '=',
					value: popup.currentRow.consumerSearch
				}
			];
			consumerField.reload().then(function (search) {
				search = search.dataset.ParamsCustomerRepository;
				if (!_.isEmpty(search)) {
					if (search.length == 1) {
						popup.currentRow.CDCLIENTE = search[0].CDCLIENTE;
						popup.currentRow.NMCONSUMIDOR = search[0].NMCONSUMIDOR;
						popup.currentRow.CDCONSUMIDOR = search[0].CDCONSUMIDOR;
						popup.currentRow.NMRAZSOCCLIE = search[0].NMRAZSOCCLIE;
						popup.currentRow.IDSITCONSUMI = search[0].IDSITCONSUMI;
						popup.getField('NMCONSUMIDOR').setValue(search[0].NMCONSUMIDOR);
					} else {
						self.modifyConsumerPopup(consumerField);
						consumerField.openField();
					}
				}
			}.bind(this));
		}.bind(this);
		t = setTimeout(searchConsumer, 1000);
	};

	this.modifyConsumerPopup = function (consumerField) {
		delete consumerField.selectWidget;
		if (consumerField.dataSourceFilter[0]) {
			consumerField.dataSourceFilter[0].value = consumerField.widget.currentRow.CDCLIENTE;
		}
	};

	this.setAccountConsumer = function (popup, clear) {
		if (_.isEmpty(popup.currentRow.CDCLIENTE) || clear) {
			popup.currentRow.CDCLIENTE = null;
			popup.currentRow.NMRAZSOCCLIE = null;
		}
		if (_.isEmpty(popup.currentRow.CDCONSUMIDOR) || clear) {
			popup.currentRow.CDCONSUMIDOR = null;
			popup.currentRow.NMCONSUMIDOR = null;
		}
		OperatorRepository.findOne().then(function (operatorData) {
			PaymentRepository.findOne().then(function (paymentData) {
				PaymentService.updateCartPrices(operatorData.chave, paymentData.ITEMVENDA, popup.currentRow.CDCLIENTE, popup.currentRow.CDCONSUMIDOR).then(function (result) {
					paymentData.ITEMVENDA = result.CartPricesRepository;
					paymentData.DATASALE.TOTALVENDA = result.nothing.valorVenda;
					paymentData.DATASALE.TOTAL = result.nothing.valorVenda;
					paymentData.DATASALE.TOTALSUBSIDY = result.nothing.subsidioTotal;
					paymentData.DATASALE.REALSUBSIDY = result.nothing.subsidioReal;
					paymentData.DATASALE.FALTANTE = result.nothing.valorVenda;
					paymentData.DATASALE.VRDESCONTO = 0;
					paymentData.DATASALE.PCTDESCONTO = '0';
					paymentData.DATASALE.TIPODESCONTO = 'P';
					paymentData.DATASALE.FIDELITYDISCOUNT = 0;
					paymentData.DATASALE.FIDELITYVALUE = 0;
					paymentData.numeroProdutos = result.nothing.numeroProdutos;
					paymentData.CDCLIENTE = popup.currentRow.CDCLIENTE;
					paymentData.NMRAZSOCCLIE = popup.currentRow.NMRAZSOCCLIE;
					paymentData.CDCONSUMIDOR = popup.currentRow.CDCONSUMIDOR;
					paymentData.NMCONSUMIDOR = popup.currentRow.NMCONSUMIDOR;
					PaymentRepository.save(paymentData).then(function () {
						if (operatorData.IDEXTCONSONLINE !== 'S' || _.isEmpty(paymentData.CDCLIENTE) || _.isEmpty(paymentData.CDCONSUMIDOR)) {
							self.attScreen(popup);
							self.setPaymentTypes(result.nothing.IDTPVENDACONS, popup.container);
						}
						else {
							ScreenService.confirmMessage("Deseja utilizar Crédito Fidelidade para este consumidor?", "question",
								function () {
									self.attStripeData(popup.container.getWidget('paymentMenu'));
									self.setPaymentTypes(result.nothing.IDTPVENDACONS, popup.container);
									AccountController.openBalconyFidelity(popup);
								},
								function () {
									self.attScreen(popup);
									self.setPaymentTypes(result.nothing.IDTPVENDACONS, popup.container);
								}
							);
						}
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.getDiscount = function (discountPopup) {
		var VRDESCONTO = 0;
		var TIPODESCONTO = '';
		// seta desconto já inserido
		PaymentService.getPaymentValue().then(function (DATASALE) {
			TIPODESCONTO = DATASALE.TIPODESCONTO;
			VRDESCONTO = TIPODESCONTO === 'P' ? DATASALE.PCTDESCONTO : DATASALE.VRDESCONTO;

			discountPopup.getField('TIPODESCONTO').setValue(TIPODESCONTO);
			discountPopup.getField('VRDESCONTO').setValue(VRDESCONTO);
			self.handleDiscountRadioChange(discountPopup);
		});
	};

	this.handleDiscountRadioChange = function (discountPopup) {
		PaymentService.getPaymentValue().then(function (DATASALE) {
			if (discountPopup.getField('TIPODESCONTO').value() === 'P') {
				discountPopup.getField('VRDESCONTO').label = 'Porcentagem';
				discountPopup.getField('VRDESCONTO').range.max = 100 * (DATASALE.FALTANTE / DATASALE.TOTALVENDA);
			} else if (discountPopup.getField('TIPODESCONTO').value() === 'V') {
				discountPopup.getField('VRDESCONTO').label = 'Valor';
				discountPopup.getField('VRDESCONTO').range.max = DATASALE.TOTAL;
			}
		});
	};

	this.setDiscount = function (discountPopup) {
		if (discountPopup.isValid()) {
			self.getMaxDiscountMessage(discountPopup).then(function (customMessage) {
				if (self.validValue(discountPopup.getField('VRDESCONTO'), customMessage)) {
					var currentRow = discountPopup.currentRow;

					currentRow.VRDESCONTO = UtilitiesService.getFloat(currentRow.VRDESCONTO);
					PaymentService.handleApplyDiscount(currentRow).then(function (handleApplyDiscountResult) {
						if (!handleApplyDiscountResult.error) {
							self.attScreen(discountPopup);
						} else {
							ScreenService.showMessage(customMessage, 'alert');
						}
					});
				}
			}.bind(this));
		}
	};

	this.getMaxDiscountMessage = function (discountPopup) {
		return PaymentRepository.findOne().then(function (paymentData) {
			return MESSAGE.VR_MAX_DISCOUNT + (paymentData.DATASALE.TOTAL - (0.01 * paymentData.numeroProdutos) - paymentData.DATASALE.FIDELITYVALUE).toFixed(2).replace('.', ',') + '.';
		});
	};

	this.cancelDiscount = function (discountPopup) {
		PaymentService.getPaymentValue().then(function (DATASALE) {
			if (!!DATASALE.VRDESCONTO) {
				ScreenService.confirmMessage(
					MESSAGE.REMOVE_DISCOUNT, 'question',
					function () {
						var currentRow = discountPopup.currentRow;
						// zera desconto
						currentRow.TIPODESCONTO = 'P';
						currentRow.VRDESCONTO = 0;
						currentRow.MOTIVODESCONTO = null;
						currentRow.CDOCORR = null;
						PaymentService.handleApplyDiscount(currentRow).then(function (handleApplyDiscountResult) {
							self.attScreen(discountPopup);
						});
					}.bind(this),
					function () { }
				);
			} else {
				ScreenService.showMessage(MESSAGE.NO_DISCOUNT, 'alert');
			}
		});
	};

	this.receivePersonalCredit = function (creditDetails) {
		if (this.checkCreditDetails(creditDetails)) {
			OperatorRepository.findOne().then(function (params) {
				var accountData = {
					"CDCLIENTE": creditDetails.CDCLIENTE,
					"CDCONSUMIDOR": creditDetails.CDCONSUMIDOR,
					"CDFAMILISALD": creditDetails.CDFAMILISALD,
					"VRRECARGA": creditDetails.VRRECARGA,
					"CREDITOPESSOAL": true
				};

				var accountDetails = {
					"vlrtotal": creditDetails.VRRECARGA,
					"vlrservico": 0,
					"vlrprodutos": creditDetails.VRRECARGA,
					"vlrdesconto": 0,
					"fidelityDiscount": 0,
					"numeroProdutos": 0
				};

				PaymentService.initializePayment(accountData, params, accountDetails, null, null, null, null, null).then(function () {
					WindowService.openWindow('PAYMENT_TYPES_SCREEN');
				});
			});
		}
	};

	this.checkCreditDetails = function (creditDetails) {
		if (creditDetails.CDCLIENTE == null || creditDetails.CDCLIENTE.length == 0 ||
			creditDetails.CDCONSUMIDOR == null || creditDetails.CDCONSUMIDOR.length == 0 ||
			creditDetails.CDFAMILISALD == null || creditDetails.CDFAMILISALD.length == 0 ||
			creditDetails.VRRECARGA == null) {
			ScreenService.showMessage("Favor preencher todos os campos.");
			return false;
		}
		else if (creditDetails.IDPERMCARGACRED == "N") {
			ScreenService.showMessage("Esta familia não está habilitada para receber carga de crédito.");
			return false;
		}
		else if (isNaN(creditDetails.VRRECARGA)) {
			ScreenService.showMessage("Favor informar um valor de recarga válido.");
			return false;
		}
		else if (creditDetails.VRRECARGA <= 0) {
			ScreenService.showMessage("Valor da recarga tem que ser maior que 0.");
			return false;
		}
		else {
			return true;
		}
	};

	this.setPaymentTypes = function (mode, container) {
		ParamsGroupPriceChart.findAll().then(function (paymentGroups) {
			ParamsPriceChart.findAll().then(function (paymentTypes) {
                OperatorRepository.findOne().then(function (operatorData){
    				var filteredPayments = self.paymentTypeFilter(mode, paymentGroups, paymentTypes, operatorData.IDCONSUBDESFOL);

                    GroupPriceChart.clearAll().then(function () {
                        PriceChart.clearAll().then(function () {
                            GroupPriceChart.save(filteredPayments.newPaymentGroups).then(function () {
                                PriceChart.save(filteredPayments.newPaymentTypes).then(function () {
                                    container.getWidget('categories').reload().then(function () {
                                        container.getWidget('pricechart').reload().then(function () {
                                            // Stops a bug where all the payment types get loaded in at the same time.
                                            if (!_.isEmpty(container.getWidget('categories').dataSource.data)) {
                                                var firstGroup = container.getWidget('categories').dataSource.data[0];
                                                var filter = [{
                                                    name: container.getWidget('categories').valueField,
                                                    operator: '=',
                                                    value: firstGroup[container.getWidget('categories').valueField]
                                                }];
                                                container.getWidget('pricechart').dataSource.filter(filter).then(function (filtered) {
                                                    container.getWidget('pricechart').dataSource.data = filtered;
                                                });
                                            }
                                        });
                                    });
                                });
                            });
                        });
                    });
                }.bind(this));
            }.bind(this));
        }.bind(this));
    };

	this.paymentTypeFilter = function (mode, paymentGroups, paymentTypes, IDCONSUBDESFOL) {
		var arrTypes = self.handlePaymentTypes(mode, IDCONSUBDESFOL);

		var newPaymentTypes = _.filter(paymentTypes, function (paymentType) {
			return _.includes(arrTypes, paymentType.IDTIPORECE);
		}.bind(this));

		var arrGroups = _.uniq(_.map(newPaymentTypes, function (newPaymentType) { return newPaymentType.CDGRUPO; }));
		var newPaymentGroups = _.filter(paymentGroups, function (newPaymentGroup) {
			return _.includes(arrGroups, newPaymentGroup.CDGRUPO);
		}.bind(this));

		// prepara novo dataSource (falha do zeedhi obriga a realizar este procedimento)
		newPaymentGroups = _.map(newPaymentGroups, function (newPaymentGroup) {
			newPaymentGroup.selected = false;
			newPaymentGroup.visible = true;
			return newPaymentGroup;
		}.bind(this));
		if (!_.isEmpty(newPaymentGroups)) {
			newPaymentGroups[0].selected = true;
		}

		return {
			'newPaymentTypes': newPaymentTypes,
			'newPaymentGroups': newPaymentGroups
		};
	};

    this.handlePaymentTypes = function (mode, IDCONSUBDESFOL){
        // À Vista
        var cash = Array('1', '2', '3', '4', '5', '6', '7', '8', 'B', 'C', 'E', 'F', 'G', 'H');
        // Débito Consumidor
        var consumer = Array('A');
        // Crédito Pessoal
        var personal = Array('9');

        var arrTypes = Array();
        mode = !_.isEmpty(mode) ? String(mode) : '7';
        switch (mode) {
            case '1': // Débito Consumidor (so utiliza A)
                arrTypes = consumer;
                break;
            case '2': // Crédito Pessoal (so utiliza 9)
                arrTypes = personal;
                break;
            case '3': // A Vista (todos os idtiporece menos 9 e A)
                if (IDCONSUBDESFOL === 'S'){
                    arrTypes = _.concat(consumer, cash);
                }
                else {
                    arrTypes = cash;
                }
                break;
            case '4': // Débito Consumidor/Crédito Pessoal (so utiliza 9 e A)
                arrTypes = _.concat(consumer, personal);
                break;
            case '5': // Débito Consumidor/A Vista (so nao utiliza o 9)
                arrTypes = _.concat(consumer, cash);
                break;
            case '6': // Crédito Pessoal/A Vista (so nao utiliza o A)
                arrTypes = _.concat(personal, cash);
                break;
            case '7': // Todos (tudo)
                arrTypes = _.concat(personal, consumer, cash);
                break;
            default:
                arrTypes = cash;
        }

        return arrTypes;
    };

	window.scanCodeResult = null;

	this.openQRScanner = function (widget) {
		if (_.isEmpty(widget.currentRow.CDCLIENTE)) widget.currentRow.CDCLIENTE = null;

		self.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				qrCode = qrCode.contents;

				if (_.isEmpty(qrCode)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				}
				else {
					self.clearConsumerPopup(widget);
					OperatorRepository.findOne().then(function (operatorData) {
						AccountService.searchConsumer(operatorData.chave, widget.currentRow.CDCLIENTE, qrCode).then(function (consumerData) {
							if (_.isEmpty(consumerData)) {
								ScreenService.showMessage("Não foi encontrado nenhum consumidor com este código.");
							} else {
								if (consumerData.length == 1) {
									widget.currentRow.CDCLIENTE = consumerData[0].CDCLIENTE;
									widget.currentRow.NMRAZSOCCLIE = consumerData[0].NMRAZSOCCLIE;
									widget.currentRow.CDCONSUMIDOR = consumerData[0].CDCONSUMIDOR;
									widget.currentRow.NMCONSUMIDOR = consumerData[0].NMCONSUMIDOR;
								} else {
									var consumerField = widget.getField('NMCONSUMIDOR');
									consumerField.dataSource.data = consumerData;
									consumerField.readOnly = false;
									self.modifyConsumerPopup(consumerField);
									consumerField.openField();
								}
							}
						});
					});
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.callQRScanner = function () {
		return new Promise(function (resolve) {
			if (!!window.ZhCodeScan) {
				window.scanCodeResult = _.bind(self.qrCodeResult, self, resolve);
				ZhCodeScan.scanCode();
			} else if (!!window.cordova) {
				cordova.plugins.barcodeScanner.scan(
					function (result) {
						result.error = false;
						result.contents = result.text;
						resolve(result);
					},
					function (error) {
						var result = {};
						result.error = true;
						result.message = error;
						resolve(result);
					}
				);
			} else {
				resolve({
					'error': true,
					'message': 'Não foi possível chamar a integração. Sua instância não existe.'
				});
			}
		}.bind(this));
	};

	this.qrCodeResult = function (resolve, result) {
		resolve(JSON.parse(result));
	};

	this.handleConsumerField = function (consumerField) {
		OperatorRepository.findOne().then(function (operatorData) {
			consumerField.selectWidget.floatingControl = false;
			if (operatorData.IDPERDIGCONS == 'S') {
				consumerField.readOnly = true;
			}
		});
	};

	this.handleConsumerChange = function (consumerPopup) {
		if (!_.isEmpty(consumerPopup.currentRow.CDCONSUMIDOR)) {
			if (consumerPopup.currentRow.IDSITCONSUMI === '2') {
				ScreenService.showMessage(MESSAGE.INATIVE_CONSUMER, 'alert');
				self.clearConsumerPopup(consumerPopup);
			}
			else {
				consumerPopup.currentRow.CDCLIENTE = consumerPopup.currentRow.CODCLIE;
				consumerPopup.currentRow.NMRAZSOCCLIE = consumerPopup.currentRow.NOMCLIE;
				consumerPopup.getField('NMRAZSOCCLIE').setValue(consumerPopup.currentRow.NOMCLIE);
				if (consumerPopup.currentRow.IDSOLSENHCONS === 'S' && consumerPopup.currentRow.CDSENHACONS !== null) {
					PermissionService.promptConsumerPassword(consumerPopup.currentRow.CDCLIENTE, consumerPopup.currentRow.CDCONSUMIDOR).then(
						function () {
							// ...
						},
						function () {
							consumerPopup.currentRow.NMCONSUMIDOR = null;
							consumerPopup.currentRow.CDCONSUMIDOR = null;
							consumerPopup.currentRow.IDSITCONSUMI = null;
						}
					);
				}
			}
		}
	};

	this.accountAddition = function (widget) {
		self.canHandleAccountAddition().then(function (canHandleAccountAddition) {
			if (!canHandleAccountAddition.error) {
				ScreenService.confirmMessage(
					MESSAGE.ALTER_ADDITION, 'question',
					function () {
						PermissionService.checkAccess('retirarTaxaServico').then(function (CDSUPERVISOR) {
							PaymentService.handleAccountAddition(CDSUPERVISOR).then(function () {
								self.attStripeData(widget);
							}.bind(this));
						}.bind(this));
					}.bind(this),
					function () { }
				);
			} else {
				ScreenService.showMessage(canHandleAccountAddition.message, 'alert');
			}
		}.bind(this));
	};

	this.canHandleAccountAddition = function () {
		var resultFormat = {
			'error': true,
			'message': ''
		};

		return PaymentService.getPaymentValue().then(function (DATASALE) {
			if (!DATASALE.VRTXSEVENDA) {
				resultFormat.message = MESSAGE.NO_ADDITION;
			} else if (!DATASALE.FALTANTE) {
				resultFormat.message = MESSAGE.BLOCK_PAYMENT;
			} else {
				resultFormat.error = false;
			}

			return resultFormat;
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 9 || keyCode === 13) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'discountPopup')
				this.setDiscount(widget);
			else if (widget.name === 'paymentPopup')
				this.setPayment(widget);
			else if (widget.name === 'consumerCPFPopup')
				this.setConsumerInfo(widget);
		}
	};

	this.handlePrintNote = function (payAccountResult) {
		ScreenService.confirmMessage(MESSAGE.CONFIRM_PRINT, 'question', function () {
			self.isCardSale(payAccountResult);
		}.bind(this), function () {
			payAccountResult.data.dadosImpressao.TEXTOCUPOM1VIA = null;
			self.isCardSale(payAccountResult);
		}.bind(this));
	};

	this.isCardSale = function (payAccountResult) {
		if (!_.isEmpty(payAccountResult.data.dadosImpressao.TEFVOUCHER)) {
			self.handleReceiptCustomer(payAccountResult);
		} else {
			PaymentService.handlePrintReceipt(payAccountResult.data.dadosImpressao);
			self.payAccountFinish(payAccountResult);
		}
	};

	this.handleReceiptCustomer = function (payAccountResult) {
		ScreenService.confirmMessage(
			MESSAGE.PRINT_VIA_CLI, 'question', function () {
				PaymentService.handlePrintReceipt(payAccountResult.data.dadosImpressao);
				self.payAccountFinish(payAccountResult);
			}.bind(this), function () {
				payAccountResult.data.dadosImpressao.TEFVOUCHER = _.map(payAccountResult.data.dadosImpressao.TEFVOUCHER, function (n) {
					n.STLPRIVIA = null;
					return n;
				});
				PaymentService.handlePrintReceipt(payAccountResult.data.dadosImpressao);
				self.payAccountFinish(payAccountResult);
			}.bind(this)
		);
	};

	this.printOrderIntegration = function (impressaoPedidoSmart) {
		var texto = '';
		impressaoPedidoSmart.forEach(function(pedidos){
			if (!pedidos.saas && ((pedidos.impressora.IDMODEIMPRES == '25' && !!window.cordova && !!cordova.plugins.GertecPrinter) || (pedidos.impressora.IDMODEIMPRES == '27' && !!window.ZhCieloAutomation) || pedidos.impressora.IDMODEIMPRES == '28')) {
				pedidos.comandos.forEach(function(comandos){
					texto += comandos.parameters.text;
					if (!!~comandos.parameters.text.search('SENHA')) {
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, texto);
						PrinterService.printerSpaceCommand(2);
						texto = '';
					}
				});
			}
		}.bind(this));
		PrinterService.printerInit().then(function (result) {
			if (result.error)
				ScreenService.alertNotification(result.message);
		});
	};

}

Configuration(function (ContextRegister) {
	ContextRegister.register('PaymentController', PaymentController);
});