function AuthController(
	OperatorService,
	OperatorController,
	WindowService,
	templateManager,
	ScreenService,
	UtilitiesService,
	PerifericosService,
	RegisterService
) {
	var self = this;
	var modoMesa = "M";
	var modoComanda = "C";
	var modoBalcao = "B";
	var modoDelivery = "D";

	this.setAuthLogin = function (widget) {
		var email = widget.currentRow.EMAIL;
		widget.getField("PASSWORD").readOnly = widget.getField(
			"AUTH"
		).readOnly = !email;
		widget.activate();
	};

	this.auth = function (widget) {
		templateManager.updateURL(
			"https://odhenpos.teknisa.cloud/backend/index.php"
		);
		OperatorService.auth(
			widget.getField("EMAIL").value(),
			widget.getField("PASSWORD").value()
		).then(function (response) {
			WindowService.openWindow("LOGIN_FILIAL_SCREEN").then(function () {
				var widgetLogin = templateManager.container.getWidget(
					"loginAuthWidget"
				);
				widgetLogin.getField("OPERADOR").value(response[0].cdoperador);
				widgetLogin
					.getField("senha")
					.value(widget.getField("PASSWORD").value());
			});
		});
	};

	this.handleFilialChange = function (filialField) {
		var widget = filialField.widget;
		self.getCaixasLogin(widget, filialField.widget.currentRow.CDFILIAL);
	};

	this.getCaixasLogin = function (widget, filial) {
		var caixaField = widget.getField("CAIXA");
		if (filial) {
			OperatorService.getCaixasLogin(filial, caixaField).then(function (caixas) {
				caixas = caixas.dataset.CaixasLogin || [];
				if (_.isEmpty(caixas)) {
					caixaField.readOnly = true;
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
		}
	};

	this.authLogin = function (row) {
		var groupMenu = templateManager.project;
		var menus = groupMenu
			.getMenu("APLICACAO")
			.menus.concat(groupMenu.getMenu("CONSUMIDOR").menus);
		row.CDOPERADOR = row.OPERADOR;

		if (!row.CDFILIAL) throw "Informe a filial.";
		if (!row.CDCAIXA) throw "Informe o caixa.";
		if (!row.CDOPERADOR) throw "Informe o Operador.";
		if (!row.senha) throw "Informe a senha.";

		OperatorController.saveLoginData(row);

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
					var operatorData = data.OperatorRepository[0];
					if (operatorData.paramsImpressora) {
						PerifericosService.test(operatorData.paramsImpressora).then(function (result) {
							if (!result.error) {
								self.handleLogin(operatorData, data, menus);
							} else {
								ScreenService.showMessage(result.message);
							}
						});
					} else {
						self.handleLogin(operatorData, data, menus);
					}
				} else {
					ScreenService.showMessage(data[0]);
				}
			}.bind(this),
			function () {
				throw "Erro na tentativa de login, verifique a configuração de IP.";
			}
		);
	};

	this.handleLogin = function (operatorData, data, menus) {
		OperatorController.checkSSLConnectionId(operatorData).then(
			function (checkSSLConnectionIdResult) {
				if (!checkSSLConnectionIdResult.error) {
					if (operatorData.IDCOLETOR !== "C") {
						var estadoCaixa = operatorData.estadoCaixa;
						var IDPALFUTRABRCXA = operatorData.IDPALFUTRABRCXA;
						var VRABERCAIX = operatorData.VRABERCAIX;
						var obrigaFechamento = operatorData.obrigaFechamento;
						if (estadoCaixa === "fechado" && IDPALFUTRABRCXA === "S") {
							OperatorController.bindedDoLogin = _.bind(
								OperatorController.doLogin,
								OperatorController,
								data,
								menus
							);
							WindowService.openWindow("OPEN_REGISTER_SCREEN");
						} else if (
							estadoCaixa === "fechado" &&
							IDPALFUTRABRCXA === "N"
						) {
							RegisterService.openRegister(
								operatorData.chave,
								VRABERCAIX
							).then(function () {
								OperatorController.doLogin(data, menus);
							});
						} else if (estadoCaixa === "aberto" && obrigaFechamento) {
							RegisterService.setClosingOnLogin(true);
							OperatorController.bindedDoLogin = _.bind(
								OperatorController.doLogin,
								OperatorController,
								data,
								menus
							);
							WindowService.openWindow("CLOSE_REGISTER_SCREEN");
						} else {
							OperatorController.doLogin(data, menus);
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
							OperatorController.doLogin(data, menus);
						}
					}
				} else {
					ScreenService.showMessage(checkSSLConnectionIdResult.message);
				}
			}.bind(this)
		);
	};

}

Configuration(function (ContextRegister) {
	ContextRegister.register("AuthController", AuthController);
});
