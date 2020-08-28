function UtilitiesService(OperatorRepository, ScreenService, templateManager, ApplicationContext, eventAggregator, ConfigIpRepository, ZHPromise, PermissionService, WindowService, UtilitiesTest, UtilitiesRequestsRepository, Query, ValidationEngine, SaveLogin, AccountCart) {

	var self = this;

	window.ApplicationContext = ApplicationContext;

	var memoryStorageAsync = new MemoryStorageAsync(ZHPromise);
	var originalClosePopup = ScreenService.closePopup;

	ScreenService.closePopup = function (all) {
		originalClosePopup(all);
		self.onPopUpClose();
	};

	var flow = {
		"menu": function () {
			var menuWidget = templateManager.container.getWidget("menu");
			if (menuWidget) {
				menuWidget.activate();
				templateManager.container.restoreDefaultMode();
			}
		}
	};

	// localforage.setItem = memoryStorageAsync.setLocalVar;
	// localforage.getItem = memoryStorageAsync.getLocalVar;
	// localforage.removeItem = memoryStorageAsync.removeItem;

	this.onPopUpClose = function () {
		var containerName = "";
		if (templateManager.container) {
			containerName = templateManager.container.name;
		}
		var toDo = flow[containerName] || false;
		if (toDo) {
			toDo();
		}
	};


	/* Habilite para começar a fazer o log do backEnd. */
	this.debugEnabled = false;

	/* *************************** */

	/* ****** LOG DE REQUISIÇÕES ****** */
	var pendingRequests = {};

	eventAggregator.onRequestRetry(function (data) {
		ScreenService.changeLoadingMessage("Tentando novamente... (" + data.requestCount + " de " + data.retryCount + ")");
	});

	eventAggregator.onRequestSuccess(function (data) {
		ScreenService.changeLoadingMessage("Aguarde...");
	});

	eventAggregator.onRequestError(function (data) {
		ScreenService.changeLoadingMessage("Aguarde...");
		$('.zh-container-alert').addClass('alert-red');

		$('.zh-footer-alert').click(function () {
			$('.zh-container-alert').removeClass('alert-red');
		});

		// Validação para evitar mensagem durante a configuração de IP.
		if ((templateManager.container.name !== "login") && (templateManager.container.name !== "billLogin") && (data.data.config.data.origin.widgetName !== "serverIpWidget")) {
			var message = '';
			var consoleMessage = _.get(data, 'data.data.error');
			if (consoleMessage) {
				console.log(consoleMessage);
			}
			if (templateManager.container.name == "loginContainer") {
				message = 'Ocorreu um erro ao estabelecer a conexão com o servidor. Certifique-se que o IP foi corretamente configurado e tente novamente.';
				ScreenService.showMessage(message, 'error');
			} else {
				message = 'Ocorreu um erro ao estabelecer a conexão com o servidor. Caso esteja demorando muito para exibir esta mensagem, verifique o sinal da sua rede.';
				if (consoleMessage) {
					message += '<br><br>' + consoleMessage;
				}
				ScreenService.showMessage(message, 'error');
			}
		}
	});

	if (this.debugEnabled) {
		eventAggregator.onRequestStart(function (data) {
			if (data.data.service !== '/UtilitiesRequestsRepository') {
				pendingRequests[data.index] = {
					"start": new Date().getTime()
				};
			}
		});

		eventAggregator.onRequestEnd(function (data) {
			if (data.data.method && pendingRequests[data.index]) {
				var currentRequest = pendingRequests[data.index];
				currentRequest.end = new Date().getTime();
				currentRequest.totalTime = currentRequest.end - currentRequest.start;
				currentRequest.backEndProcess = data.data.method[0].parameters[0] * 1000;
				currentRequest.latencyTime = currentRequest.totalTime - currentRequest.backEndProcess;
				currentRequest.index = data.index;
				self.sendRequestsToBack(pendingRequests);
			}
			delete pendingRequests[data.index];
		});
	}
	/* *************************** */

	/* CPF validation. */
	this.isValidCPF = function (cpf) {
		/* CPF validation. */
		var isValid = false;
		if (cpf) {
			cpf = cpf.replace('.', '').replace('.', '').replace('-', '');
			var total;
			var first, second;

			var invalidCpfs = [
				"00000000000",
				"11111111111",
				"22222222222",
				"33333333333",
				"44444444444",
				"55555555555",
				"66666666666",
				"77777777777",
				"88888888888",
				"99999999999"
			];

			if (invalidCpfs.indexOf(cpf) == -1) {
				total = 0;
				for (i = 1; i <= 9; i++) {
					total += parseInt(cpf.substring(i - 1, i)) * (11 - i);
				}
				first = (total * 10) % 11;
				if (first == 10 || first == 11) first = 0;

				total = 0;
				for (i = 1; i <= 10; i++) {
					total += parseInt(cpf.substring(i - 1, i)) * (12 - i);
				}
				second = (total * 10) % 11;

				if ((second == 10) || (second == 11)) second = 0;
				isValid = !(first != parseInt(cpf.substring(9, 10)) || second != parseInt(cpf.substring(10, 11)));
			} else {
				isValid = false;
			}
		}
		return isValid;
	};

	this.isValidCPForCNPJ = function (code) {
		if (code.length > 11) {
			return self.isValidCNPJ(code);
		} else {
			return self.isValidCPF(code);
		}
	};

	this.isValidCNPJ = function (cnpj) {
		cnpj = cnpj.replace(/[^\d]+/g, '');

		if (cnpj == '') return false;

		if (cnpj.length != 14)
			return false;

		// Elimina CNPJs invalidos conhecidos
		if (cnpj == "00000000000000" ||
			cnpj == "11111111111111" ||
			cnpj == "22222222222222" ||
			cnpj == "33333333333333" ||
			cnpj == "44444444444444" ||
			cnpj == "55555555555555" ||
			cnpj == "66666666666666" ||
			cnpj == "77777777777777" ||
			cnpj == "88888888888888" ||
			cnpj == "99999999999999")
			return false;

		// Valida DVs
		tamanho = cnpj.length - 2;
		numeros = cnpj.substring(0, tamanho);
		digitos = cnpj.substring(tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(0))
			return false;

		tamanho = tamanho + 1;
		numeros = cnpj.substring(0, tamanho);
		soma = 0;
		pos = tamanho - 7;
		for (i = tamanho; i >= 1; i--) {
			soma += numeros.charAt(tamanho - i) * pos--;
			if (pos < 2)
				pos = 9;
		}
		resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
		if (resultado != digitos.charAt(1))
			return false;

		return true;
	};

	ValidationEngine.registerValidation('CPF', function (cpfValue) {
		return {
			'valid': self.isValidCPF(cpfValue) || !cpfValue,
			'message': 'CPF inválido'
		};
	});

	this.setServerIp = function (row, widget) {
		if (!(row.ip)) {
			ScreenService.showMessage('Informe o IP.');
		} else {
			var ip = row.ip.split('://').pop();
			var port = (row.porta) ? (":" + row.porta) : "";
			var ipForTest = "http://" + ip + port + self.getBackendPath(ip);

			// URL para testar (HTTP).
			templateManager.updateURL(ipForTest);
			self.testConnection().then(
				function () {
					setIp('http', ip, port).then(function () {
						SaveLogin.clearAll();
						widget.reload();
						ScreenService.closePopup();
					});
				},
				function () {
					// URL para testar (HTTPS).
					ipForTest = "https://" + ip + port + self.getBackendPath(ip);
					templateManager.updateURL(ipForTest);
					self.testConnection().then(
						function () {
							setIp('https', ip, port).then(function () {
								SaveLogin.clearAll();
								widget.reload();
								ScreenService.closePopup();
							});
						},
						function () {
							// Nenhum dos protocolos funcionaram.
							ScreenService.confirmMessage(
								"Não foi possível conectar ao servidor. Deseja manter o ip informado?",
								"question",
								function () {
									setIp('http', ip, port).then(function () {
										SaveLogin.clearAll();
										widget.reload();
										ScreenService.closePopup();
									});
								},
								function () { }
							);
						});
				});
		}
	};

	var setIp = function (protocol, ip, port) {

		var treatedProtocol = protocol + "://";
		var treatedPort = port.replace(":", "");
		var ipCompleto = treatedProtocol + ip + port + self.getBackendPath(ip);
		templateManager.updateURL(ipCompleto);

		var configIp = {
			ipCompleto: ipCompleto,
			ipSemPorta: ip,
			protocol: protocol,
			port: treatedPort,
			ipForBack: treatedProtocol + ip + port
		};

		var defer = ZHPromise.defer();
		ConfigIpRepository.clearAll().then(function () {
			ConfigIpRepository.save(configIp).then(function () {
				defer.resolve();
			});
		});
		return defer.promise;
	};

	this.validateIp = function () {
		var defer = ZHPromise.defer();

		this.checkSetLocalVar().then(function () {
			defer.resolve();
		}, function () {
			defer.reject("Configure o IP do servidor da aplicação.");
		});

		return defer.promise;
	};

	this.checkSetLocalVar = function () {
		var defer = ZHPromise.defer();

		ConfigIpRepository.findOne().then(function (configIp) {
			if (configIp) {
				templateManager.updateURL(configIp.ipCompleto);
				defer.resolve();
			} else {
				defer.reject();
			}
		}, function () {
			defer.reject();
		});

		return defer.promise;
	};

	var __init = function () {
		window.__back__ = function () { };
	};

	this.changeIndex = function (widget) {
		__init(); //remove zeedhi behavior.
		var image = widget.getField('logo-waiter');
		if (projectConfig.currentMode === modosWaiter.order.codigo) {
			ApplicationContext.OrderController.showOrderLogin();
		} else if (projectConfig.currentMode === modosWaiter.comanda.codigo) {
			image.source = image.sourceFastPass;
		} else {
			image.source = image.sourceWaiter;
		}
	};

	this.toCurrency = function (number) {
		if (typeof number == 'string') {
			number = parseInt(number);
		}
		return number.toFixed(2).replace('.', ',');
	};

	this.removeCurrency = function (value) {
		if (typeof value == 'string') {
			value = parseFloat(value.replace(',', '.').replace('R$', ''));
		}
		return value;
	};

	this.formatFloat = function (floatNumber) {
		return parseFloat(floatNumber).toFixed(2).replace(".", ",");
	};

	this.showServidor = function (widgetToShow) {
		ScreenService.changeFilter(widgetToShow);
	};

	this.backMainScreen = function () {
		OperatorRepository.findAll().then(function (operatorData) {
			modoHabilitado = operatorData[0].modoHabilitado;

			if (modoHabilitado === 'M') {
				WindowService.openWindow('TABLES_SCREEN');
			} else if (modoHabilitado === 'C') {
				WindowService.openWindow('BILLS_SCREEN');
			} else if (modoHabilitado === 'O') {
				WindowService.openWindow('ORDER_MENU_SCREEN');
			} else if (modoHabilitado === 'B') {
				WindowService.openWindow('MENU_SCREEN');
			} else if (modoHabilitado === 'D') {
				WindowService.openWindow('DELIVERY_ORDERS_SCREEN');
			}
		});
	};

	this.backLoginScreen = function () {
		OperatorRepository.findAll().then(function (operatorData) {
			if (operatorData[0].modoHabilitado !== 'O') {
				ScreenService.openWindow(templateManager.containers.zeedhi_project.mainWindow);
			} else {
				WindowService.openWindow('ORDER_LOGIN_SCREEN');
			}
		});
	};

	this.handleBack = function (activePage) {
		if (activePage === 'menu' || activePage === 'sendWaiterless') {
			this.backMainScreen();
		} else if (activePage === 'checkPromo') {
			WindowService.openWindow('PROMO_SCREEN');
		} else if (activePage === 'orderCheckOrder') {
			WindowService.openWindow('ORDER_MENU_SCREEN');
		} else if (activePage === 'orderProduct') {
			WindowService.openWindow('ORDER_MENU_SCREEN');
		} else if (activePage === 'orderCloseAccount') {
			WindowService.openWindow('ORDER_MENU_SCREEN');
		} else {
			WindowService.openWindow('MENU_SCREEN');
		}
	};

	this.showAccessControl = function (widget) {
		ScreenService.changeFilter(widget);
	};

	this.showFunctions = function (widgetToShow) {
		ScreenService.openPopup(widgetToShow);
	};

	this.openPopup = function (widgetToShow) {
		ScreenService.openPopup(widgetToShow);
	};

	this.prepareServerForm = function (widgetToShow) {
		ConfigIpRepository.findOne().then(function (configIp) {
			var data = [];
			if (configIp) {
				data.porta = configIp.port;
				data.ip = configIp.ipSemPorta;
			}

			widgetToShow.setCurrentRow(data);

			ScreenService.openPopup(widgetToShow);
		});
	};

	var waiterFunctionsByMode = {
		'M': [
			'btnAccountDetails',
			'btnSendMessage',
			'btnCloseAccount',
			'btnCancelProduct',
			'btnChangePositions',
			'btnGroupTables',
			'btnTransfer',
			'btnCancelTableOpening',
			'btnReleaseProduct',
			'btnPositionCode',
			'btnAccountPayment',
			'btnAnticipatePayment',
			'btnSplitProducts'
		],
		'C': [
			'btnChangeTable',
			'btnAccountDetails',
			'btnSendMessage',
			'btnCancelProduct',
			'btnTransferProductComanda',
			'btnCloseAccount',
			'btnBillGrouping'
		],
		'B': [
			'btnSendMessage',
			'btnChangeConsumer'
		]
	};

	function getWaiterFunctions(widgetFuntions) {
		var waiterFunctions = [];
		_.each(widgetFuntions.fields, function (currentFunction) {
			waiterFunctions.push(currentFunction.name);
		});
		return waiterFunctions;
	}

	function hideFunctionsByMode(widgetFunctions, currentMode, waiterFunctions) {
		_.each(waiterFunctions, function (currentFunction) {
			var mustShow = _.indexOf(waiterFunctionsByMode[currentMode], currentFunction) >= 0;
			widgetFunctions.getField(currentFunction).isVisible = mustShow;
			widgetFunctions.getField(currentFunction).showOnForm = mustShow;
		});
	}

	function setActionsVisibilityByWaiterMode(widget, currentMode) {
		var actions = widget.actions;
		actions.forEach(function (action) {
			if (action.activeOnMode && !Util.isArray(action.activeOnMode)) {
				action.activeOnMode = Util.parseToArray(action.activeOnMode);
			}
			action.isVisible = !action.activeOnMode || ~action.activeOnMode.indexOf(currentMode);
		});
	}

	this.hideFunctions = function (widget) {
		var widgetFunctions = widget.container.getWidget('functions');
		var waiterFunctions = getWaiterFunctions(widgetFunctions);
		var btnCloseAccount = widgetFunctions.getField('btnCloseAccount');

		OperatorRepository.findOne().then(function (params) {

			var currentMode = params.modoHabilitado;

			setActionsVisibilityByWaiterMode(widget, currentMode);
			hideFunctionsByMode(widgetFunctions, currentMode, waiterFunctions);

			if (currentMode !== 'B') {
				var mustShowPayment = params.IDCOLETOR == 'C';

				//o campo abaixo foi temporariamente fixado em isVisible = false por estar causando confusão nos clientes
				widgetFunctions.getField('btnAnticipatePayment').isVisible = false;
				widgetFunctions.getField('btnAnticipatePayment').showOnForm = mustShowPayment;

				if (currentMode === 'C') {
					if (params.bloqComandaParcial === 'N') {
						btnCloseAccount.isVisible = false;
						btnCloseAccount.showOnForm = false;
					}

					btnCloseAccount.isVisible = !mustShowPayment;
					btnCloseAccount.showOnForm = !mustShowPayment;
					btnCloseAccount.label = 'Receber Comanda';
				} else if (currentMode === 'M') {
					widgetFunctions.getField('btnSplitProducts').isVisible = (params.IDLUGARMESA === 'S');
					widgetFunctions.getField('btnAccountPayment').isVisible = !mustShowPayment;
					widgetFunctions.getField('btnAccountPayment').showOnForm = !mustShowPayment;
					btnCloseAccount.label = 'Fechar Conta';
					if (params.NRATRAPADRAO > 0) {
						widgetFunctions.getField('btnReleaseProduct').isVisible = true;
						widgetFunctions.getField('btnReleaseProduct').showOnForm = true;
					} else {
						widgetFunctions.getField('btnReleaseProduct').isVisible = false;
						widgetFunctions.getField('btnReleaseProduct').showOnForm = false;
					}
				}
			}
			widgetFunctions.container.restoreDefaultMode();
		});
	};

	this.setHeader = function (container) {
		OperatorRepository.findAll().then(function (operatorData) {
			ApplicationContext.AccountController.getAccountData(function (accountData) {
				var inicio = container.label.substr(0, container.label.indexOf('<span')) || container.label;
				if (!_.isEmpty(accountData)) {
					if (operatorData[0].modoHabilitado === 'M') {
						inicio += '<span class="waiter-header-right">' + accountData[0].NMMESA + '</span>';
					} else if (operatorData[0].modoHabilitado === 'C') {
						inicio += '<span class="waiter-header-right"> Comanda ' + accountData[0].DSCOMANDA + ' - Mesa ' + accountData[0].NRMESA + '</span>';
					}
				}
				container.label = inicio;
			});
		});
	};

	this.checkEmail = function (email) {
		/* This regular expression is said to not work in some very specific cases, but it appears to be safe to use in our case. */
		return Boolean(email.match(/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/));
	};

	this.numberLock = function (numberField) {
		if (numberField.length > 4) numberField = numberField.substr(0, 4);
	};

	this.setFirstPositionButtonAsActive = function (widget) {
		var positionsField = widget.getField('positionswidget');
		if (positionsField.template.split("/")[6] == "waiter_position.html") {
			positionsField.position = 0;
		} else {
			positionsField._buttons = [];
			positionsField.position = undefined;
		}
		widget.reload();
	};

	this.setupPoynt = function (loginWidget) {
		templateManager.project.notifications[0].isVisible = false;

		if (self.isPoyntDevice()) {
			templateManager.project.notifications[1].isVisible = true;
			templateManager.container.showHeader = true;
			loginWidget.getField('poynt_email').isVisible = true;
			loginWidget.getField('poynt_phone').isVisible = true;
			loginWidget.getField('poynt_website').isVisible = true;
		} else {
			templateManager.container.showHeader = false;
		}
	};

	this.isPoyntDevice = function () {
		return window.navigator.userAgent.indexOf('Poynt') > -1;
	};

	this.getBackendPath = function (ip) {
		if (ip.indexOf("waiterpoynt.teknisa.com") > - 1) {
			return '/backend/index.php';
		}
		return projectConfig.serviceUrl;
	};

	this.setVersionLabel = function (widget) {
		var versionField = widget.getField('version');
		var version = projectConfig.frontVersion || '1.0.0';
		versionField.label = 'Versão ' + version;
	};

	this.testConnection = function () {
		var query = Query.build();
		return UtilitiesTest.download(query);
	};

	this.sendRequestsToBack = function (requests) {
		var query = Query.build()
			.where('requests').equals(requests);
		return UtilitiesRequestsRepository.download(query);
	};

	this.truncValue = function (value) {
		// trunca float para 2 casas decimais
		return parseFloat((String(value * 100).split('.')[0]) / 100);
	};

	this.floatFormat = function (value, size, toString) {
		if (size == null) size = 2;
		var a = Math.pow(10, size);
		var f = parseFloat(parseInt(value * a) / a);
		return toString ? f.toFixed(size).replace('.', ',') : f;
	};

	this.padLeft = function (value, pad, chr) {
		value = (typeof value) != 'string' ? String(value) : value;

		if (value.length < pad) {
			for (var i = pad - value.length; i > 0; i--) {
				value = chr + value;
			}
		}

		return value;
	};

	this.toNumber = function (field) {
		field.setValue(field.value().replace(/[^0-9 ]/g, ""));
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

	this.getFloat = function (value) {
		return (typeof value) == 'string' ? parseFloat(value.replace(',', '.')) : value;
	};

	this.loginOnEnter = function (args) {
		if (!!window.cordova) {
			var logo = args.owner.getField('logo-waiter');
			logo.source = logo.source.replace('mobile', 'www');
			logo.sourceWaiter = logo.sourceWaiter.replace('mobile', 'www');
			logo.sourceFastPass = logo.sourceFastPass.replace('mobile', 'www');

			var GertecPrinter = cordova.plugins.GertecPrinter;
			if (!!GertecPrinter)
				GertecPrinter.printerInit();

			var KioskPOS = cordova.plugins.KioskPOS;
			if (!!KioskPOS) {
				self.kioskConfig(KioskPOS);
			} else {
				document.addEventListener('backbutton', function (e) { }, false);
			}
		}
	};

	this.kioskConfig = function (kiosk) {
		kiosk.isSetAsLauncher(function (isSetAsLauncher) {
			kiosk.setLockFlag(isSetAsLauncher);
		});

		var lastTimeBackPress = 0;
		document.addEventListener('backbutton', function (e) {
			e.preventDefault();
			e.stopPropagation();
			if (new Date().getTime() - lastTimeBackPress < 650) {
				kiosk.isInKiosk(function (isInKiosk) {
					var screenName = document.getElementsByClassName("zh-application-content")[0];
					screenName = screenName.baseURI.split("index.html#/")[1];

					if (isInKiosk && screenName === 'login') {
						self.openUnlockPopup();
					}
				});
			} else {
				lastTimeBackPress = new Date().getTime();
			}
		}, false);
	};

	this.openUnlockPopup = function () {
		var unlockDeviceWidget = templateManager.containers.login.getWidget("loginWidget").widgets[4];
		unlockDeviceWidget.newRow();
		unlockDeviceWidget.isVisible = true;
		ScreenService.hideLoader();
		ScreenService.openPopup(unlockDeviceWidget);
	};

	this.handleCloseKeyboard = function () {
		if (!!window.ZhNativeInterface) {
			ZhNativeInterface.closeKeyboard();
		} else if (!!window.cordova && !!window.Keyboard) {
			Keyboard.hide();
		}
	};

	this.validateDate = function (date) {
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

	this.validateName = function (field) {
		field.setValue(field.value().replace(/[^A-Za-zÀ-ú'\s]/g, ""));
	};

	this.switchTemplateGroupProp = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' || operatorData.modoHabilitado === 'B' || operatorData.IDLUGARMESA === 'N') {
				widget.template = '../../../../templates/widget/list-grouped-default.html';
				widget.groupProp = 'GRUPO';
			}
			else {
				widget.template = '../../../../templates/widget/list-grouped-position.html';
				widget.groupProp = 'posicao';
			}
		});
	};

	this.backAfterFinish = function () {
    	OperatorRepository.findOne().then(function (operatorData) {
    		if (operatorData.modoHabilitado === 'B') {
    			AccountCart.remove(Query.build());
    		}

           	self.backMainScreen();
    	});
    };
    
}

Configuration(function (ContextRegister) {
	ContextRegister.register('UtilitiesService', UtilitiesService);
});

function WaiterOrdersCatCtrl($scope, templateManager, ScreenService, $rootScope, ApplicationContext) {
	var paging = new checkRows();
	$scope.openRow = function (row) {
		if (!row.DISABLED) {
			if (row && row[$scope.widget.categoryProperty] !== "next" && row[$scope.widget.categoryProperty] !== "prev") {
				$scope.widget.setCurrentRow(row);
				$scope.widget.dataSource.data.forEach(function (object) {
					object.selected = false;
				});
				row.selected = true;
			} else {
				paging[row[$scope.widget.categoryProperty]]();
			}
		}
	};

	function checkRows() {

		var clearVisibility = function (data) {
			data.forEach(function (object) {
				object.visible = false;
			});
			return data;
		},
			currentPage;

		this.init = function () {
			currentPage = 0;
			var init = false;
			var widget = $scope.widget;
			$scope.listener = $scope.$watch('widget.dataSource.data.length', function (data) {
				if (data) {
					widget.dataSource.data = clearVisibility(widget.dataSource.data);
					$scope.listener();
					that.update();
				}
			});
		};

		this.update = function () {

			var checkColumns = function () {
				//iPad: 4 columns, regardless of orientation.
				//return 4;
				//Computer:
				var width = $(window).width();
				return 4;
				/*
				if (width < 335) return 1;
				if (width < 462) return 2;
				if (width < 606) return 3;
				if (width < 750) return 4;
				if (width < 769) return 5;
				if (width < 950) return 4;
				if (width < 1134) return 5;
				return 6;*/
			},
				columns = checkColumns(),
				lines = $scope.widget.lines,
				perPage = columns * lines - 2,
				nextObj = {
					"CDGRUPO": "next",
					"class": "icon-waiter-next",
					"COLOR": "#969696",
					"color-active": "#ffffff",
					"visible": true
				},
				prevObj = {
					"CDGRUPO": "prev",
					"class": "icon-waiter-prev",
					"COLOR": "#969696",
					"color-active": "#ffffff",
					"visible": true
				},
				scopeData,
				firstPosition = currentPage * perPage,
				lastPosition = firstPosition + perPage + (currentPage === 0 ? 2 : 1);

			var i;
			if ($scope.widget.parent.name === 'smartPromo' || $scope.widget.parent.name === 'subPromo') {
				var selection = 0;
				for (i in $scope.widget.dataSource.data) {
					// Controls group visibility.
					$scope.widget.dataSource.data[i].visible = true;
					// Checks to see which group to highlight, based on the initialization function.
					if ($scope.widget.dataSource.data[i].SELECTED) selection = i;
				}
				// Highlights the appropriate group.
				$scope.widget.dataSource.data[selection].selected = true;
			}
			else {
				if ($scope.widget.dataSource.data) {
					scopeData = $scope.widget.dataSource.data.filter(function (object) {
						return object[$scope.widget.categoryProperty] !== "next" && object[$scope.widget.categoryProperty] !== "prev";
					});
					scopeData = clearVisibility(scopeData);
					for (i = firstPosition + (currentPage === 0 ? 0 : 1); i < lastPosition - (currentPage === 0 ? 1 : 0) && i < scopeData.length; i++) {
						scopeData[i].visible = true;
					}
					$scope.openRow(scopeData[firstPosition + (currentPage === 0 ? 0 : 1)]);
					if (lastPosition <= scopeData.length) {
						scopeData.splice(lastPosition, 0, nextObj);
					}
					if (currentPage !== 0) {
						scopeData.splice(firstPosition, 0, prevObj);
					}
					$scope.widget.dataSource.data = scopeData;
				}
			}
		};

		this.next = function () {
			currentPage++;
			that.update();
		};
		this.prev = function () {
			currentPage--;
			that.update();
		};

		var that = this;
	}

	angular.element(document).ready(function () {
		$scope.$watch('$rootScope.searchList', function (newData) {
			ScreenService.filterWidget($scope.widget, $scope.widget.parent.widgets, newData);
		});
		paging.init();
		angular.element(window).bind('resize', function () {
			//paging.update();
		});
	});
	$scope.idealTextColor = idealTextColor;
}

function idealTextColor(bgColor) {
	var nThreshold = 105;
	var bgDelta = 0;
	if (bgColor) {
		var components = getRGBComponents(bgColor);
		bgDelta = (components.R * 0.299) + (components.G * 0.587) + (components.B * 0.114);
	} else {
		bgDelta = 0;
	}
	return ((255 - bgDelta) < nThreshold) ? "#000" : "#fff";
}

function getRGBComponents(color) {
	var r = color.substring(1, 3);
	var g = color.substring(3, 5);
	var b = color.substring(5, 7);

	return {
		R: parseInt(r, 16),
		G: parseInt(g, 16),
		B: parseInt(b, 16)
	};
}

function WaiterFieldListGroupedController($scope, ApplicationContext) {
	$scope.lineClick = function () {
		var fn = $scope.field.click || $scope.field.touchstart;
		if (fn) fn({ data: $scope.row });
	};
}

function WaiterGroupController($scope, ApplicationContext) {
	var imagePath = "bower_components/zeedhi-frontend/assets/images/icons/{0}.svg";
	$scope.selectTable = function (table, positions, abreComanda) {
		if (table.mode === 'list') {
			ApplicationContext.TableController.selectTable(table, positions, abreComanda);
		} else {
			ApplicationContext.TableController.checkTable(table);
		}
	};
	$scope.getImage = function (src) {
		src = !!src ? src : 'empty';
		return imagePath.replace("{0}", src);
	};
	$scope.__getFilter = function (table) {
		var filter = {};
		if ($scope.searchList) {
			filter[$scope.tableField.filterProp] = $scope.searchList;
			$scope._strict_ = false;
		}
		return filter;
	};
	$scope.setTheTable = function (selectedTableData, container) {
		ApplicationContext.TableController.setTheTable(selectedTableData.NRMESA, container);
	};
}

