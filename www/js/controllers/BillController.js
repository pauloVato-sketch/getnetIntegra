function BillController(AccountController, OperatorRepository, BillService, ScreenService, AccountCart, Query,
 TableSelectedTable, templateManager, TableController, ParamsParameterRepository, PermissionService, TimestampRepository,
  WindowService, UtilitiesService, CartPool, ZHPromise){

	var self = this;

	this.getBills = function(callBack) {
		OperatorRepository.findAll().then(function(params) {
			BillService.getBills(params[0].chave).then(function(data) {
				if (callBack) {
					callBack(data);
				}
			});
		});
	};

	/* Makes it so that the products are organized by groups instead of by position in Bill Mode. */
	this.formatGroups = function(widget) {
		OperatorRepository.findOne().then(function(data) {
			if (data.modoHabilitado === 'C' || data.IDLUGARMESA === 'N' || data.modoHabilitado === 'B') {
				widget.groupProp = 'GRUPO';
			} else {
				widget.groupProp = 'posicao';
			}
		});
	};

	this.controlPriceVisibility = function(container) {
		var checkOrderWidget = container.getWidget('checkOrder');
		var checkOrderStripeWidget = container.getWidget('checkOrderStripe');
		OperatorRepository.findOne().then(function(data) {
			if (data.modoHabilitado === 'B') {
				checkOrderWidget.detailPriceProp = 'PRECO';
				checkOrderStripeWidget.isVisible = true;
			}else{
				checkOrderWidget.detailPriceProp = '';
				checkOrderStripeWidget.isVisible = false;
			}
		});
	};

	this.continueOrdering = function(container) {
		var promises = [];
		var operatorPromise = OperatorRepository.findOne().then(function(operatorData) {
			operatorData.continueOrdering = true;
			operatorData.newOrders = false;
			return OperatorRepository.save(operatorData);
		});

		var accountCartPromise = AccountCart.findAll().then(function(cart){
			return AccountCart.clearAll().then(function(){
				return CartPool.findAll().then(function(cartPool){
					cart = AccountController.filterCartPool(cart, cartPool);
					return CartPool.save(cartPool.concat(cart));
				});
			});
		});

		promises.push(operatorPromise);
		promises.push(accountCartPromise);
		ZHPromise.all(promises).then(function (){
			UtilitiesService.backMainScreen();
		});
	};

	this.checkOrderOnInit = function(container){
		this.formatGroups(container.getWidget('checkOrder'));
		this.controlPriceVisibility(container);
	};

	this.validateBill = function(row, widgets) {
		AccountController.buildOrderCode().then(function() {
			AccountCart.remove(Query.build()).then(function() {
				OperatorRepository.findOne().then(function(operatorData) {
					if (row.DSCOMANDA) {
						row.DSCOMANDA = this.validateCode(row.DSCOMANDA);
						BillService.validateBill(operatorData.chave, row.DSCOMANDA, false).then(function(data) {
							if (data[0].VAZIO == 'N') { // comanda existe
								openMenu();
							} else { // comanda não existe
								if (operatorData.geraNrComandaAut == 'N') {
									PermissionService.checkAccess('abrirComanda').then(function() {
										this.prepareOpenBill(widgets[0], row);
									}.bind(this));
								} else {
									ScreenService.showMessage('Comanda não encontrada.');
								}
							}
						}.bind(this));
					} else {
						ScreenService.showMessage('Comanda pode ser somente números.');
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.addBillClick = function(widgetToShow, row) {
		AccountController.buildOrderCode().then(function() {
			PermissionService.checkAccess('abrirComanda').then(function() {
				this.prepareOpenBill(widgetToShow, row);
			}.bind(this));
		}.bind(this));
	};

	this.prepareBills = function(widget) {
		this.checkBarCodeButton(widget.getField('DSCOMANDA'), widget.getField('BTNBARCODE'));
		widget.setCurrentRow({});
		widget.container.restoreDefaultMode();

		OperatorRepository.findOne().then(function(operatorData) {
			widget.getAction('conferir').isVisible = operatorData.continueOrdering;
			widget.getAction('addBill').isVisible = operatorData.geraNrComandaAut == 'S';
		});
	};

	this.prepareBillList = function(widgetToShow) {
		this.getBills(function(data) {
			widgetToShow.dataSource.data = data;
			ScreenService.openPopup(widgetToShow);
		});
	};

	this.selectBill = function(row) {
		AccountController.buildOrderCode().then(function() {
			AccountCart.remove(Query.build()).then(function() {
				OperatorRepository.findOne().then(function(operatorData) {
					BillService.validateBill(operatorData.chave, row.DSCOMANDA, false).then(function(data) {
						ScreenService.closePopup();
						openMenu();
					});
				});
			});
		});
	};

	this.prepareOpenBill = function(billOpeningWidget, row) {
		OperatorRepository.findOne().then(function(operatorData) {
			ParamsParameterRepository.findAll().then(function(dataParams) {

				if (billOpeningWidget.dataSource.data && billOpeningWidget.dataSource.data.length > 0){
					delete billOpeningWidget.dataSource.data;
				}
				billOpeningWidget.newRow();
				billOpeningWidget.moveToFirst();

				var data = {
					__createdLocal: true,
					DSCOMANDA: row.DSCOMANDA,
					CDCLIENTE: null,
					CDCONSUMIDOR: null,
					CDVENDEDOR: null
				};

				billOpeningWidget.setCurrentRow(data);

				if (dataParams[0].NRMESAPADRAO) {
					billOpeningWidget.getField('btnTableList').label = "Mesa (opcional)";
				} else {
					billOpeningWidget.getField('btnTableList').label = "Mesa";
				}

				var messageToShow = '';
				if (operatorData.geraNrComandaAut == 'N'){
					billOpeningWidget.label = "Abrir Comanda - " + row.DSCOMANDA;
				} else {
					billOpeningWidget.label = "Abrir Comanda";
				}

				// Informar consumidor.
				var consumerSearch = billOpeningWidget.getField('consumerSearch');
				var consumidorField = billOpeningWidget.getField('NMCONSUMIDOR');
				var btnReadConsumerQRCode = billOpeningWidget.getAction('btnReadConsumerQRCode');
				
				if (operatorData.infConsAbrComanda == 'N'){
					consumerSearch.isVisible = consumidorField.isVisible = btnReadConsumerQRCode.isVisible = false;
				} else {
					consumerSearch.isVisible = consumidorField.isVisible = btnReadConsumerQRCode.isVisible = true;
				}

				var dsconsumidorField = billOpeningWidget.getField('DSCONSUMIDOR');
				dsconsumidorField.isVisible = operatorData.IDSOLDIGCONS == 'S';

				// Informar mesa.
				if (operatorData.infoMesAbrComanda == 'N'){
					billOpeningWidget.getField('btnTableList').isVisible = false;
				}

				// Informar vendedor.
				billOpeningWidget.getField('VENDEDOR').isVisible = dataParams[0].IDINFVENDCOM == 'S';

				TableSelectedTable.clearAll().then(function() {
					ScreenService.openPopup(billOpeningWidget);
				});

			}.bind(this));
		}.bind(this));
	};

	this.openBill = function(billData) {
		AccountCart.remove(Query.build()).then(function() {
			ParamsParameterRepository.findOne().then(function(dataParams) {
				OperatorRepository.findOne().then(function(operatorData){
					var chave = operatorData.chave;
					TableSelectedTable.findOne().then(function(selectedTable) {
						if(operatorData.infoMesAbrComanda == 'S' && !selectedTable){
							if(dataParams.NRMESAPADRAO){
								self.confirmMesaPadrao(chave, billData, dataParams.NRMESAPADRAO);
							}else{
								ScreenService.showMessage('Selecione uma mesa.');
							}
						}else{
							var NRMESA = selectedTable ? selectedTable.NRMESA: '';
							self.checkAndOpenBill(chave, billData, NRMESA);
						}
					});
				});
			});
		});
	};

	this.confirmMesaPadrao = function(chave, billData, NRMESA){
		return ScreenService.confirmMessage(
		'Mesa não selecionada. Deseja continuar com a mesa padrão?','question',
		function(){self.checkAndOpenBill(chave, billData, NRMESA);}, function(){});
	};

	this.checkAndOpenBill = function(chave, billData, NRMESA){
		if (billData.DSCOMANDA === null) billData.DSCOMANDA = "";
		if (billData.CDCLIENTE === null) billData.CDCLIENTE = "";
		if (billData.CDCONSUMIDOR === null) billData.CDCONSUMIDOR = "";
		if (billData.DSCONSUMIDOR === null) billData.DSCONSUMIDOR = "";
		if (billData.CDVENDEDOR === null) billData.CDVENDEDOR = "";
		BillService.openBill(chave, billData.DSCOMANDA, billData.CDCLIENTE, billData.CDCONSUMIDOR, NRMESA, billData.CDVENDEDOR, billData.DSCONSUMIDOR).then(function(){
			ScreenService.closePopup();
			openMenu();
		});
	};

	this.scanBarCode = function(widget){
		if(!!window.ZhCodeScan) {
			window.scanCodeResult = _.bind(self.scanCodeResultFunction, self, widget);
			ZhCodeScan.scanCode();
		} else if(!!window.cordova && !!cordova.plugins.barcodeScanner) {
			UtilitiesService.callQRScanner().then(function(result){
				self.scanCodeResultFunction(widget, JSON.stringify(result));
			}.bind(this));
		} else {
			ScreenService.showMessage('Não foi possível chamar a integração. Sua instância não existe');
		}
	};

	this.validateCode = function(code){
		if(code.length < 10) {
			var fullStrPad = "0000000000";
			var strPad = fullStrPad.substring(0, 10 - code.length);
			code = strPad + code;
		} else if (code.length > 10) {
			code = code.substring(code.length-10);
		}
		
		return code;
	};

	this.checkBarCodeButton = function (fieldDSCOMANDA, fieldBTNBARCODE) {
		if((!!window.ZhCodeScan) || (!!window.cordova && !!cordova.plugins.barcodeScanner)) {
			fieldDSCOMANDA.class = 10;
			fieldBTNBARCODE.isVisible = true;
		}
	};

	this.scanCodeResultFunction = function(widget, result){
		result = JSON.parse(result);
		if(!result.error) {
			if (result.contents){
				widget.getField('DSCOMANDA').setValue(self.validateCode(result.contents));
				self.validateBill(widget.currentRow, widget.widgets);
			} else {
				ScreenService.showMessage('Operação bloqueada. Comanda inválida.');
			}
		} else {
			ScreenService.showMessage(result.message);
		}
	};

	var openMenu = function(){
		WindowService.openWindow('MENU_SCREEN');
	};

	var t;
    this.consumerSearch = function(){
        clearTimeout(t);
        var searchConsumer = function(){
            var consumerField = ApplicationContext.templateManager.container.getWidget('billOpeningWidget').getField('NMCONSUMIDOR');
            var popup = ApplicationContext.templateManager.container.getWidget('billOpeningWidget');

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
            consumerField.reload().then(function (search){
                search = search.dataset.ParamsCustomerRepository;
                if (!_.isEmpty(search)){
	                if (search.length == 1){
	                    popup.currentRow.CDCLIENTE = search[0].CDCLIENTE;
	                    popup.currentRow.NMCONSUMIDOR = search[0].NMCONSUMIDOR;
	                    popup.currentRow.CDCONSUMIDOR = search[0].CDCONSUMIDOR;
	                    popup.currentRow.NMRAZSOCCLIE = search[0].NMRAZSOCCLIE;
	                    popup.currentRow.IDSITCONSUMI = search[0].IDSITCONSUMI;
	                    popup.getField('NMCONSUMIDOR').setValue(search[0].NMCONSUMIDOR);
	                } else {
	                	self.applyClientFilter(consumerField);
		                consumerField.openField();
	                }
                }
            }.bind(this));
        }.bind(this);
        t = setTimeout(searchConsumer, 1000);
	};

    this.applyClientFilter = function(consumerField){
    	if (consumerField.dataSourceFilter[0]){
    		consumerField.dataSourceFilter[0].value = consumerField.widget.currentRow.CDCLIENTE;
    	}
    };

    this.handleConsumerField = function(consumerField){
		if (consumerField.selectWidget) {
			consumerField.selectWidget.floatingControl = false;
		}
    };

    this.handleConsumerChange = function(consumerPopup){
    	if (!_.isEmpty(consumerPopup.currentRow.CDCONSUMIDOR)){
    		if (consumerPopup.currentRow.IDSITCONSUMI === '2'){
    			ScreenService.showMessage(MESSAGE.INATIVE_CONSUMER, 'alert');
    			self.clearConsumerPopup(consumerPopup);
    		}
            else {
                consumerPopup.currentRow.CDCLIENTE = consumerPopup.currentRow.CODCLIE;
                consumerPopup.currentRow.NMRAZSOCCLIE = consumerPopup.currentRow.NOMCLIE;
                consumerPopup.getField('NMRAZSOCCLIE').setValue(consumerPopup.currentRow.NOMCLIE);
                if (consumerPopup.currentRow.IDSOLSENHCONS === 'S' && consumerPopup.currentRow.CDSENHACONS !== null){
                    PermissionService.promptConsumerPassword(consumerPopup.currentRow.CDCLIENTE, consumerPopup.currentRow.CDCONSUMIDOR).then(
                        function (){
                            // ...
                        },
                        function (){
                            consumerPopup.currentRow.NMCONSUMIDOR = null;
                            consumerPopup.currentRow.CDCONSUMIDOR = null;
                            consumerPopup.currentRow.IDSITCONSUMI = null;
                        }
                    );
                }
            }
    	}
    };

    this.clearConsumerPopup = function(popup){
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

    this.handleEnterButton = function(args) {
		var keyCode = args.e.keyCode;
		if(keyCode === 9 || keyCode === 13) {
			UtilitiesService.handleCloseKeyboard();
		}
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('BillController', BillController);
});