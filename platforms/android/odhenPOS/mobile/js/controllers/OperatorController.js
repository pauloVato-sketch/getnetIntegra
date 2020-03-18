function OperatorController(OperatorService, TableService, AccountController, UtilitiesService, templateManager, ScreenService,
	TableActiveTable, Query, ApplicationContext, RegisterService, WindowService, OperatorRepository, ParamsAreaRepository,
	ParamsGroupRepository, ParamsClientRepository, ParamsSellerRepository, AccountCart, ParamsMenuRepository, ParamsGroupPriceChart,
	ParamsPriceChart, ParamsPrinterRepository, ParamsProdMessageRepository, ParamsProdMessageCancelRepository, ParamsParameterRepository,
	ParamsObservationsRepository, ZHPromise, FiliaisLogin, CaixasLogin, VendedoresLogin, metaDataFactory, PrinterPoynt, IntegrationService,
	SaveLogin, SSLConnectionId, PermissionService, ParamsMensDescontoObs, CarrinhoDesistencia, AccountService, PaymentService,
	PerifericosService, ProdSenhaPed) {

	var modoMesa = 'M';
	var modoComanda = 'C';
	var modoBalcao = 'B';
	var modoDelivery = 'D';
	var self = this;

	this.showHome = function () {
		webViewInterface.redePoyntHideApp();
	};

	this.loadLoginData = function (widget) {
		UtilitiesService.validateIp().then(
			function () {
				self.setEditWidget(widget, false, ["servidor", "FILIAL"]);
				var filialField = widget.getField("FILIAL");
				OperatorService.getFiliaisLogin(filialField).then(function (filiais) {
					filiais = filiais.dataset.FiliaisLogin || [];
					filialField.dataSource.data = filiais;

					if (_.isEmpty(filiais)) {
						filialField.readOnly = true;
						FiliaisLogin.clearAll();
						ScreenService.showMessage("Nenhuma filial encontrada na base.");
					} else {
						filialField.readOnly = false;

						SaveLogin.findOne().then(function (loginData) {
							if (!_.isEmpty(loginData)) {
								widget.setCurrentRow(loginData);
								widget.fields.forEach(function (field) {
									field.readOnly = false;
								});
							} else {
								widget.setCurrentRow(filiais[0]);
								self.getCaixasLogin(widget, filiais[0].CDFILIAL);
								self.getVendedoresLogin(widget, filiais[0].CDFILIAL);
							}
						});
					}
				});
			},
			function (message) {
				self.setEditWidget(widget, false, ["servidor"]);
				ScreenService.showMessage(message).then(function () {
					UtilitiesService.prepareServerForm(
						widget.container.getWidget("serverIpWidget")
					);
				});
			}
		);
	};

	this.setEditWidget = function (widget, editable, exceptions) {
		exceptions = exceptions || [];
		widget.fields.forEach(function (field) {
			if (_.indexOf(exceptions, field.name) == -1) {
				field.readOnly = !editable;
			}
		});
	};

	this.getCaixasLogin = function (widget, filial) {
		var caixaField = widget.getField("CAIXA");
		if (filial) {
			OperatorService.getCaixasLogin(filial, caixaField).then(function (caixas) {
				caixas = caixas.dataset.CaixasLogin || [];
				if (_.isEmpty(caixas)) {
					caixaField.readOnly = true;
					CaixasLogin.clearAll();
					ScreenService.showMessage("Nenhum caixa encontrado na filial.");
				} else {
					caixaField.readOnly = false;
				}
				if (caixas.length == 1) {
					widget.setCurrentRow(_.merge(widget.currentRow, caixas[0]));
				} else {
					widget.currentRow.CAIXA = null;
					widget.currentRow.CDCAIXA = null;
				}
				caixaField.dataSource.data = caixas;
			});
		} else {
			caixaField.readOnly = true;
			CaixasLogin.clearAll();
		}
	};

	this.getVendedoresLogin = function (widget, filial) {
		var operadorField = widget.getField("OPERADOR");
		if (filial) {
			operadorField.readOnly = false;
		} else {
			operadorField.readOnly = true;
			VendedoresLogin.clearAll();
		}
	};

	this.setVendedorLogin = function (widget) {
		var operador = widget.currentRow.OPERADOR;
		widget.getField("senha").readOnly = widget.getField(
			"entrar"
		).readOnly = !operador;
		widget.activate();
	};

	this.handleFilialChange = function (filialField) {
		var widget = filialField.widget;

		if (!filialField.value()) {
			widget.setCurrentRow({});
			self.setVendedorLogin(widget);
		}

		self.getCaixasLogin(widget, widget.currentRow.CDFILIAL);
		self.getVendedoresLogin(widget, widget.currentRow.CDFILIAL);
	};

	this.login = function (row, errorPopup) {
		var groupMenu = templateManager.project;
		var menus = groupMenu
			.getMenu("APLICACAO")
			.menus.concat(groupMenu.getMenu("CONSUMIDOR").menus);
		row.CDOPERADOR = row.OPERADOR;

		UtilitiesService.validateIp()
			.then(
				function () {
					if (!row.CDFILIAL) throw "Informe a filial.";
					if (!row.CDCAIXA) throw "Informe o caixa.";
					if (!row.CDOPERADOR) throw "Informe o Operador.";
					if (!row.senha) throw "Informe a senha.";

					self.saveLoginData(row);

					OperatorService.login(
						row.CDFILIAL,
						row.CDCAIXA,
						row.CDOPERADOR,
						row.senha,
						projectConfig.frontVersion,
						projectConfig.currentMode
					).then(
						function (data) {
							if (data.OperatorRepository) {
								if (data.OperatorRepository[0].paramsImpressora) {
									PerifericosService.test(data.OperatorRepository[0].paramsImpressora).then(function (response) {
										if (!response.error) {
											self.handleLogin(data, menus);
										} else {
											ScreenService.showMessage(response.message);
										}
									});
								} else {
									self.handleLogin(data, menus);
								}
							} else {
								errorPopup.setCurrentRow({ erro: data[0] });
								ScreenService.openPopup(errorPopup);
							}
						}.bind(this),
						function () {
							throw "Erro na tentativa de login, verifique a configuração de IP.";
						}
					);
				}.bind(this)
			)
			.catch(function (err) {
				ScreenService.showMessage(err);
			});
	};

	this.handleLogin = function (data, menus) {
		var operatorData = data.OperatorRepository[0];
		self.checkSSLConnectionId(operatorData).then(
			function (checkSSLConnectionIdResult) {
				if (!checkSSLConnectionIdResult.error) {
					if (operatorData.IDCOLETOR !== "C") {
						var estadoCaixa = operatorData.estadoCaixa;
						var IDPALFUTRABRCXA = operatorData.IDPALFUTRABRCXA;
						var VRABERCAIX = operatorData.VRABERCAIX;
						var obrigaFechamento = operatorData.obrigaFechamento;
						if (estadoCaixa === "fechado" && IDPALFUTRABRCXA === "S") {
							this.bindedDoLogin = _.bind(this.doLogin, this, data, menus);
							WindowService.openWindow("OPEN_REGISTER_SCREEN");
						} else if (estadoCaixa === "fechado" && IDPALFUTRABRCXA === "N") {
							RegisterService.openRegister(
								operatorData.chave,
								VRABERCAIX
							).then(function () {
								this.doLogin(data, menus);
							});
						} else if (estadoCaixa === "aberto" && obrigaFechamento) {
							RegisterService.setClosingOnLogin(true);
							this.bindedDoLogin = _.bind(this.doLogin, this, data, menus);
							WindowService.openWindow("CLOSE_REGISTER_SCREEN");
						} else {
							this.doLogin(data, menus);
						}
					} else {
						if (operatorData.modoHabilitado === modoBalcao) {
							ScreenService.showMessage(
								"Modo balcão não pode ser coletor.",
								"alert"
							);
						} else if (operatorData.modoHabilitado === modoDelivery) {
							ScreenService.showMessage(
								"Modo delivery não pode ser coletor.",
								"alert"
							);
						} else {
							this.doLogin(data, menus);
						}
					}
				} else {
					ScreenService.showMessage(checkSSLConnectionIdResult.message);
				}
			}.bind(this)
		);
	};

	this.saveLoginData = function (row) {
		var saveRow = _.clone(row);
		saveRow.senha = "";
		SaveLogin.save(saveRow);
	};

	this.bindedDoLogin = this.doLogin;

	this.doLogin = function (data, menus) {
		OperatorRepository.save(data.OperatorRepository).then(function () {
			var operatorData = data.OperatorRepository[0];
			this.handleMenuOptions(operatorData.modoHabilitado, operatorData.IDCOLETOR, menus,
				operatorData.IDHABCAIXAVENDA, operatorData.NMFANVEN, operatorData.CDOPERADOR);

			TableActiveTable.remove(Query.build()).then(function () {
				UtilitiesService.backMainScreen();
				templateManager.project.notifications[0].isVisible = false;
			});

			self.checkPendingPayment(operatorData.IDTPTEF, null);
		}.bind(this));
	};

	this.handleMenuOptions = function (
		modo,
		IDCOLETOR,
		menus,
		IDHABCAIXAVENDA,
		NMOPERADOR,
		CDOPERADOR
	) {
		if (modo === modoMesa) {
			this.handleMesaMenu(menus, IDCOLETOR);
		} else if (modo === modoComanda) {
			this.handleComandaMenu(menus, IDCOLETOR);
		} else if (modo === modoBalcao) {
			this.handleBalcaoMenu(menus);
		} else if (modo === modoDelivery) {
			this.handleBalcaoMenu(menus);
		}

		SaveLogin.findOne().then(function (loginData) {
			buildUserMenuInfo(
				NMOPERADOR,
				CDOPERADOR,
				loginData.CAIXA,
				loginData.FILIAL
			);
		});

		var menusAdministracao = this.searchByName(
			"administracao",
			templateManager.project.groupMenu
		).menus;
		this.searchByName("trocaModo", menusAdministracao).isVisible =
			modosCaixa[IDHABCAIXAVENDA].modos.length > 1;

		if (!!window.cordova) {
			if (!!cordova.plugins.KioskPOS) {
				var toggleLockDevice = this.searchByName(
					"toggleLockDevice",
					menusAdministracao
				);
				toggleLockDevice.isVisible = true;

				cordova.plugins.KioskPOS.isInKiosk(function (isInKiosk) {
					toggleLockDevice.label = isInKiosk ? "Desbloquear Dispositivo" : "Bloquear Dispositivo";
				});
			} else if (!!cordova.plugins.GertecSitef) {
				this.searchByName("deviceSerial", menusAdministracao).isVisible = true;
			}
		}

		var modoVenda = IDCOLETOR !== "C";
		this.searchByName("Fechar Caixa", menus).isVisible = modoVenda;
		this.searchByName("Cancelar Venda", menus).isVisible = modoVenda;
		this.searchByName("Funções Gerais", menus).isVisible = modoVenda;
		this.searchByName("Crédito Pessoal", menus).isVisible = modoVenda;
	};

	this.handleMesaMenu = function (menus) {
		this.searchByName("Dashboard", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = false;
		this.searchByName("Mesas", menus).isVisible = true;
		this.searchByName("Mensagem Produção", menus).isVisible = true;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = false;
		this.searchByName("Solicitar Conta", menus).isVisible = false;
		this.searchByName("Chamar Garçom", menus).isVisible = false;
		this.searchByName("Pedidos Realizados", menus).isVisible = false;
	};

	this.handleComandaMenu = function (menus) {
		this.searchByName("Dashboard", menus).isVisible = false;
		this.searchByName("Mesas", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = true;
		this.searchByName("Mensagem Produção", menus).isVisible = true;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = false;
		this.searchByName("Solicitar Conta", menus).isVisible = false;
		this.searchByName("Chamar Garçom", menus).isVisible = false;
		this.searchByName("Pedidos Realizados", menus).isVisible = false;
	};

	this.handleBalcaoMenu = function (menus) {
		this.searchByName("Dashboard", menus).isVisible = false;
		this.searchByName("Mesas", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = false;
		this.searchByName("Mensagem Produção", menus).isVisible = false;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = false;
		this.searchByName("Solicitar Conta", menus).isVisible = false;
		this.searchByName("Chamar Garçom", menus).isVisible = false;
		this.searchByName("Pedidos Realizados", menus).isVisible = false;
	};

	var buildUserMenuInfo = function (NMOPERADOR, CDOPERADOR, CAIXA, FILIAL) {
		var info = [CDOPERADOR, FILIAL, CAIXA];
		var headerInfo = NMOPERADOR + " - " + CDOPERADOR + " | " + CAIXA;

		ScreenService.buildUserData(NMOPERADOR, info);
		ScreenService.setHeaderInfo(headerInfo);

		templateManager.hideUserData = true;
	};

	this.configureMenu = function () {
		templateManager.project.notifications = false;
		var menus = this.searchByName(
			"APLICACAO",
			templateManager.project.groupMenu
		);
		var menusAdministracao = this.searchByName(
			"administracao",
			templateManager.project.groupMenu
		);
		var menuslogin = menusAdministracao.menus;

		this.searchByName("Mesas", menus).isVisible = false;
		this.searchByName("Comandas", menus).isVisible = false;
		this.searchByName("Mensagem Produção", menus).isVisible = false;
		this.searchByName("Transações", menus).isVisible = false;
		this.searchByName("Fazer Pedido", menus).isVisible = true;
		this.searchByName("Solicitar Conta", menus).isVisible = true;
		this.searchByName("Chamar Garçom", menus).isVisible = true;
		this.searchByName("Pedidos Realizados", menus).isVisible = true;
		menusAdministracao.isVisible = true;
		this.searchByName("Logout", menuslogin).isVisible = false;
		this.searchByName("Sair", menuslogin).isVisible = true;
		this.searchByName("Dashboard", menus).isVisible = false;
	};

	this.searchByName = function (nome, menus) {
		return _.find(menus, { name: nome });
	};

	this.limitField = function (field, length) {
		var value = field.value();
		var modifier = Math.pow(10, length) - 1;
		while (value > modifier) value /= 10;
		field.value(parseInt(value));
	};

	this.logout = function (field) {
		field.windowName = templateManager.containers.zeedhi_project.mainWindow;
		ZHPromise.all([
			OperatorRepository.clearAll(),
			ParamsAreaRepository.clearAll(),
			ParamsGroupRepository.clearAll(),
			ParamsClientRepository.clearAll(),
			ParamsSellerRepository.clearAll(),
			ParamsMenuRepository.clearAll(),
			ParamsGroupPriceChart.clearAll(),
			ParamsPriceChart.clearAll(),
			ParamsPrinterRepository.clearAll(),
			ParamsProdMessageRepository.clearAll(),
			ParamsProdMessageCancelRepository.clearAll(),
			ParamsParameterRepository.clearAll(),
			ParamsObservationsRepository.clearAll(),
			ParamsMensDescontoObs.clearAll(),
			AccountCart.clearAll(),
			AccountService.logout(),
			CarrinhoDesistencia.clearAll(),
			ProdSenhaPed.clearAll()
		]).then(function () {
			templateManager.project.notifications[0].isVisible = false;
			ScreenService.openWindow(
				templateManager.containers.zeedhi_project.mainWindow
			);
		});
	};

	this.openChangeModePopup = function (menu) {
		OperatorRepository.findOne().then(function (operatorData) {
			var popupTrocaModo = self.searchByName("popupTrocaModo", menu.widgets);
			popupTrocaModo = metaDataFactory.widgetFactory(
				popupTrocaModo,
				templateManager.container
			);
			popupTrocaModo.currentRow = {};
			popupTrocaModo.currentRow.chaveSessao = operatorData.chave;
			popupTrocaModo.getField("nome").dataSource.data = _.filter(
				modosWaiter,
				function (modo) {
					return _.some(
						modosCaixa[operatorData.IDHABCAIXAVENDA].modos,
						function (caixaMode) {
							return caixaMode == modo.codigo;
						}
					);
				}
			);
			ScreenService.openPopup(popupTrocaModo);
		});
	};

	this.trocaModo = function (popupTrocaModo) {
		ScreenService.closePopup();
		ScreenService.toggleSideMenu();
		ScreenService.openWindow("login");
		OperatorService.trocaModoCaixa(
			popupTrocaModo.currentRow.chaveSessao,
			popupTrocaModo.currentRow.codigo
		).then(function (loginData) {
			projectConfig.currentMode = popupTrocaModo.currentRow.chaveSessao;
			var groupMenu = templateManager.project;
			var menus = groupMenu
				.getMenu("APLICACAO")
				.menus.concat(groupMenu.getMenu("CONSUMIDOR").menus);
			self.doLogin(loginData, menus);
			CarrinhoDesistencia.remove(Query.build());
			ProdSenhaPed.remove(Query.build());
		});
	};

	this.reprintTEFVoucher = function () {
		PrinterPoynt.reprintTEFVoucher().then(function (result) {
			if (!result.error) {
				ScreenService.toggleSideMenu();
			} else {
				ScreenService.showMessage(
					"Falha ao imprimir comprovante do TEF. " + result.message,
					"alert"
				);
			}
		});
	};

	this.checkPendingPayment = function (IDTPTEF, errorMessage) {
		OperatorService.findPendingPayments().then(function (payments) {
			payments = payments[0];
			if (payments.error) {
				if (!_.isEmpty(payments.message))
					ScreenService.showMessage(payments.message, 'alert');
				else if (_.isEmpty(payments.message) && errorMessage !== null)
					ScreenService.showMessage(errorMessage, 'alert');
			} else {
				payments = payments.data;
				payments.forEach(function (payment) {
					var transactionDate = payment.TRANSACTIONDATE;
					payment.TRANSACTIONDATE = transactionDate.slice(6, 8) + transactionDate.slice(4, 6) + transactionDate.substring(0, 4);
				});

				payments[0].IDTPTEF = IDTPTEF;
				ScreenService.showMessage("Há transações pendentes que serão canceladas.").then(function () {
					IntegrationService.reversalIntegration(self.mochRemovePaymentSale, payments).then(function (reversalIntegrationResult) {
						if (!reversalIntegrationResult.error) {
							PaymentService.removePayment(payments);
							PaymentService.handleRefoundTEFVoucher(reversalIntegrationResult.data);
						} else {
							if (reversalIntegrationResult.data.length > 1) {
								var reversed = _.map(reversalIntegrationResult.data, function (reversal) {
									return _.isUndefined(reversal.toRemove) ? null : reversal.toRemove.CDNSUHOSTTEF;
								});
								reversed = _.compact(reversed);

								payments = _.filter(payments, function (payment) {
									return _.indexOf(reversed, payment.CDNSUHOSTTEF) !== -1;
								}.bind(this));

								PaymentService.removePayment(payments);
							}

							ScreenService.showMessage(reversalIntegrationResult.message, 'alert');
						}
					}.bind(this));
				}.bind(this));
			}
		}.bind(this));
	};

	this.mochRemovePaymentSale = function () {
		return new Promise.resolve(true);
	};

	this.handlePrintText = function (printObject) {
		return Array({
			STLPRIVIA: printObject.customerReceipt,
			STLSEGVIA: printObject.merchantReceipt
		});
	};

	this.checkSSLConnectionId = function (data) {
		var result = {
			error: true,
			message: ""
		};

		return new Promise(function (resolve) {
			if (
				data.IDTPTEF === "5" &&
				(data.IDUTLSSL === "3" || data.IDUTLSSL === "4")
			) {
				OperatorService.buscaTefSSLConnectionId(device.serial).then(
					function (buscaTefSSLConnectionIdResult) {
						if (!_.isEmpty(buscaTefSSLConnectionIdResult)) {
							SSLConnectionId.save(buscaTefSSLConnectionIdResult);
							result.error = false;
							resolve(result);
						} else {
							result.message =
								"Operação bloqueada. Não há um código de conexão SSL parametrizado para este dispositivo.";
							resolve(result);
						}
					}.bind(this)
				);
			} else {
				result.error = false;
				resolve(result);
			}
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var field = args.owner.field;
			var widget = field.widget;

			if (widget.name === "serverIpWidget") {
				if (field.name === "ip") {
					if (!Util.isDesktop()) document.getElementById("porta").focus();
				} else if (field.name === "porta") {
					UtilitiesService.setServerIp(
						widget.currentRow,
						templateManager.container.getWidget("loginWidget")
					);
				}
			} else if (widget.name === "loginWidget" && !Util.isDesktop()) {
				this.login(
					widget.currentRow,
					widget.container.getWidget("errorConsole")
				);
			} else if (
				widget.name === "validateSupervisorWidget" ||
				widget.name === "unlockDeviceWidget"
			) {
				if (field.name === "supervisor") {
					if (!Util.isDesktop()) document.getElementById("pass").focus();
				} else if (field.name === "pass") {
					PermissionService.validateSupervisorPass(widget.currentRow);
				}
			} else if (widget.name === "consumerPasswordWidget") {
				PermissionService.checkConsumerPassword(widget.currentRow, widget);
			}
		}
	};

	this.validateSendMessageAccess = function () {
		var permissionName = "mensagemProducao";

		PermissionService.checkAccess(permissionName).then(
			function (CDSUPERVISOR) {
				self.openSendMessageWindow(CDSUPERVISOR);
			}.bind(this),
			function (rejectionStatus) {
				if (rejectionStatus === -1) {
					self.openSendMessageWindow();
				}
			}
		);
	};

	this.openSendMessageWindow = function (CDSUPERVISOR) {
		var windowName = "sendWaiterless";
		var sendMessageWidgetName = "sendMessage";

		ScreenService.openWindow(windowName).then(
			function () {
				ScreenService.toggleSideMenu();

				var sendMessageWidget = templateManager.container.getWidget(
					sendMessageWidgetName
				);
				if (sendMessageWidget) {
					sendMessageWidget.CDSUPERVISOR = CDSUPERVISOR;
				}
			}.bind(this)
		);
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register("OperatorController", OperatorController);
});
