function MenuFunctionsController (PermissionService, AccountController, TableController, UtilitiesService, OperatorRepository, TableService, BillService, AccountService, ScreenService, templateManager, WindowService, BillController){
	var selectControl = [];

	this.showParcial = function(){
		PermissionService.checkAccess('parcialConta').then(function (){
			AccountController.showAccountDetails();
		});
	};

	this.showMsgProducao = function(){
		WindowService.openWindow('SEND_MESSAGE_SCREEN');
	};

	this.showFecharConta = function(){
		WindowService.openWindow('CLOSE_ACCOUNT_SCREEN');
	};

	this.showPagarConta = function(){
		OperatorRepository.findAll().then(function(operatorData){
			operatorData = operatorData[0];
			AccountController.getAccountData(function(accountData){
				accountData = accountData[0];

				var chave = operatorData.chave;
				var modoHabilitado = operatorData.modoHabilitado;
				var nrComanda = accountData.NRCOMANDA;
				var nrVendaRest = accountData.NRVENDAREST;
				var IDCOLETOR = operatorData.IDCOLETOR;

				AccountService.getAccountDetails(chave, ((modoHabilitado === 'O') ? 'M' : modoHabilitado), nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData){
					if (accountDetailsData.nothing[0].nothing === 'nothing') {
						if (!accountDetailsData.AccountGetAccountDetails[0].vlrtotal){
							if (modoHabilitado === 'O'){
								ScreenService.showMessage('Não foi realizado nenhum pedido para esta mesa, favor solicitar o fechamento ao garçom.');
								UtilitiesService.backMainScreen();
							} else if (modoHabilitado === 'M' || modoHabilitado === 'C'){
								if (accountDetailsData.AccountGetAccountDetails[0].vlrpago > 0) {
									ScreenService.showMessage('O adiantamento atingiu o valor máximo da conta.', 'alert');
								} else {
									if (modoHabilitado === 'C'){
										ScreenService.showMessage('Não houve pedido para realizar esta operação.', 'alert');
									} else {
										ScreenService.confirmMessage(
											'Não foi realizado nenhum pedido para esta mesa, deseja cancelar a abertura?',
											'question',
											function(){
												TableService.cancelOpen(chave, accountData.NRMESA).then(function(){
													UtilitiesService.backMainScreen();
												}.bind(this));
											}.bind(this),
											function(){}
										);
									}
								}
							}
						} else {
							// fecha mesa para receber os valores certos na pagar conta
							TableService.closeAccount(chave, nrComanda, nrVendaRest, 'M', true, true, true, 0, accountData.NRPESMESAVEN, null, null, 'N', null).then(function(response) {
								TableService.changeTableStatus(chave, nrVendaRest, nrComanda, 'R').then(function(response){
									TableController.openAccountPayment();
								}.bind(this));
								AccountController.handlePrintBill(response.dadosImpressao);
							}.bind(this));
						}
					} else {
						UtilitiesService.backMainScreen();
					}
				}.bind(this));
			}.bind(this));
		});
	};

	this.showDividirProdutos = function(){
		WindowService.openWindow('SPLIT_PRODUCTS_SCREEN');
	};

	this.showCancelarProduto = function(){
		PermissionService.checkAccess('cancelaItemGenerico').then(function (CDSUPERVISOR){
			TableController.showCancelProduct(CDSUPERVISOR);
		});
	};

	this.showAlterarQtPessoas = function(widget){
		PermissionService.checkAccess('alterarQtPessoas').then(function (){
			OperatorRepository.findOne().then(function(operatorData){
				widget.getField('NRPOSICAOMESA').maxValue = operatorData.NRMAXPESMES;
				TableController.showChangePositions(widget);
			});
		});
	};

	this.showAgrupamentos = function(){
		PermissionService.checkAccess('agruparMesas').then(function (){
			WindowService.openWindow('GROUP_TABLE_SCREEN');
		});
	};

	this.showTransferencias = function(){
		PermissionService.checkAccess('transferirProduto').then(function (CDSUPERVISOR){
			TableController.showTransfers(CDSUPERVISOR);
		});
	};

	this.showCancelarAbertura = function (container){
		PermissionService.checkAccess('cancelaMesaComanda').then(function (){
			TableController.cancelOpen(container);
		});
	};

	this.showChangeTable = function(widget){
		OperatorRepository.findAll().then(function(params){
			TableService.getTables(params[0].chave).then(function(result){
				result.TableRepository.forEach(function(res){
					res.mode = 'list';
				});
				widget.dataSource.data = result.TableRepository;
				ScreenService.openPopup(widget);
			});
		});
	};

	this.showReleaseProduct = function(){
		OperatorRepository.findOne().then(function (operatorData){
			AccountController.getAccountData(function (accountData){
				TableService.getDelayedProducts(operatorData.chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA).then(function (delayedProducts){
					if (delayedProducts.length > 0) {
						WindowService.openWindow('DELAYED_PRODUCTS_SCREEN');
					} else {
						ScreenService.showMessage('Não existem pedidos para liberar nesta mesa.');
					}
				});
			});
		});
	};

	this.showGenerateCode = function(widget){
		widget.fields[0].dataSource.data = widget.container.getWidget('positionsWidget').dataSource.data;
		widget.fields[0].position = null;
		ScreenService.openPopup(widget);
	};

	this.openTransferProduct = function(widget){
		PermissionService.checkAccess('transferirProduto').then(function(CDSUPERVISOR){
			widget.currentRow.CDSUPERVISOR = CDSUPERVISOR;
			ScreenService.openPopup(widget);
		});
	};

	this.prepareBillListSelect = function(billField) {
		billField.dataSource.data = Array();
		billField.clearValue();
		BillController.getBills(function(comandas) {
			AccountController.getAccountData(function(accountData) {
				_.remove(comandas, function(comanda) {
					return comanda.DSCOMANDA == accountData[0].DSCOMANDA;
				});
				billField.dataSource.data = comandas;
			});
		});
	};

	this.selectComandaProducts = function(productField) {
		productField.dataSource.data = Array();
		productField.clearValue();
		AccountController.getAccountData(function(accountData) {
			AccountService.selectComandaProducts(accountData[0].NRCOMANDA).then(function(result){
				productField.dataSource.data = result;
			}.bind(this));
		});
	};

	this.updateComandaProducts = function(widget) {
		if (!_.isEmpty(widget.currentRow.selectComandas)) {
			if (!_.isEmpty(widget.getField('selectComandaProducts').value())) {
				BillController.getBills(function(comandas) {
					var comanda = _.filter(comandas, function(c){ return (c.DSCOMANDA == widget.currentRow.DSCOMANDA);});
					AccountController.getAccountData(function(accountData) {
						AccountService.updateComandaProducts(accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, comanda[0].NRCOMANDA, comanda[0].NRVENDAREST, widget.currentRow.CDPRODUTO, widget.currentRow.NRPRODCOMVEN, widget.currentRow.CDSUPERVISOR).then(function(result){
							ScreenService.showMessage("Produtos Transferidos para comanda " + widget.currentRow.selectComandas + ".");
							widget.getField('selectComandas').clearValue();
							widget.getField('selectComandaProducts').clearValue();
							ScreenService.goBack();
						}.bind(this));
					}.bind(this));
				}.bind(this));
			} else {
				ScreenService.showMessage("Selecione ao menos um produto.", 'alert');
			}
		} else {
			ScreenService.showMessage("Selecione a comanda de destino.", 'alert');
		}
	};

	this.handleCheckedPromo = function(field, action) {
		_.forEach(field.dataSource, function(produto) {
			if (!!field.selectedRow.CDPRODPROMOCAO) {
			 	if (field.selectedRow.CDPRODPROMOCAO == produto.CDPRODPROMOCAO && field.selectedRow.NRSEQPRODCOM == produto.NRSEQPRODCOM) {
					produto.__isSelected = action;
				}
			}
		});
	};

	this.controlPromo = function(field) {
		selectControl = _.clone(field.dataSource);
		_.remove(selectControl, function(p) {
			return !p.__isSelected;
		});
	};

	this.removePromoItens = function(fieldRow, field) {
		if (!_.isEqual(fieldRow.row.selectComandaProducts, fieldRow.row.VALOR)) {
	        var removed = _.difference(fieldRow.row.VALOR, fieldRow.row.selectComandaProducts);

	        if (!_.isEmpty(removed)) {
				var removedRow = _.filter(selectControl, function(p){ return (p.CDPRODUTO+'-'+p.NRSEQPRODCOM == _.head(removed));});
				var toRemove = _.remove(selectControl, function(p){
					if (!_.isEmpty(p.CDPRODPROMOCAO) && (!_.isEmpty(removedRow)))
						return (p.CDPRODPROMOCAO == removedRow[0].CDPRODPROMOCAO && p.NRSEQPRODCOM == removedRow[0].NRSEQPRODCOM);
				});

				var valueField = _.clone(field.value());

				if (!_.isEmpty(toRemove)){
					_.forEach(toRemove, function(r){
						_.remove(fieldRow.row.VALOR, function(p){ return (p == r.VALOR);});
					});
				} else {
					_.remove(fieldRow.row.VALOR, function(p){ return (p == removed);});
				}

				_.forEach(toRemove, function(value) {
				  _.remove(valueField, function(p){
				  	return p == value.VALOR;
				  });
				});

				field.value(valueField);
	        }
		}
	};

	this.selectToGroupBills = function(mainBillField) {
		mainBillField.clearValue();
		mainBillField.dataSource.data = Array();
		BillController.getBills(function(comandas) {
		 	mainBillField.dataSource.data = comandas;
		}.bind(this));
	};

	this.handleSelectBills = function(mainBillField, selectBillsToGroupField){
		BillService.selectGroupBills().then(function (groupBills){
			if (!_.isEmpty(mainBillField.value())) {
				selectBillsToGroupField.readOnly = false;
				currentRow = mainBillField.getParent().currentRow;
				comandas = _.clone(mainBillField.dataSource.data);

				_.forEach(groupBills, function(value, key) {
				  	_.remove(comandas, function(c){
				  		return c.NRCOMANDA == value.NRCOMANDA && c.NRVENDAREST == value.NRVENDAREST;
				  	});
				});

				selectBillsToGroupField.dataSource.data = _.filter(comandas, function(datasetBills) {
					return datasetBills.NRCOMANDA != currentRow.MAINNRCOMANDA && datasetBills.NRVENDAREST != currentRow.MAINNRVENDAREST;
				});
			} else {
				selectBillsToGroupField.readOnly = true;
				selectBillsToGroupField.dataSource.data = Array();
			}
		}.bind(this));
	};

	this.selectToUngroupBills = function(ungroupBillsWidget) {
		var sortedGroupBills = Array();
		ungroupBillsWidget.dataSource.data = Array();
		BillService.selectGroupBills().then(function (groupBills){
			_.forEach(groupBills, function(value, key) {
				if (value.DSCOMANDAPRI == null) {
					value.DSCOMANDAPRI = 'PRINCIPAL';
					sortedGroupBills.push(value);
					groupedBills = _.filter(groupBills, function(gpBill) {
						return value.DSCOMANDA == gpBill.DSCOMANDAPRI;
					});
					sortedGroupBills.push(groupedBills);
				}
			});
		 	ungroupBillsWidget.dataSource.data = sortedGroupBills.flat();
		}.bind(this));
	};

	this.eraseGroupBillData = function (widget) {
		groupWidget = widget.container.getWidget('group');
		ungroupWidget = widget.container.getWidget('ungroup');
		groupWidget.getField('mainBill').clearValue();
		groupWidget.getField('mainBill').dataSource.data = Array();
		groupWidget.getField('selectBillsToGroup').clearValue();
		groupWidget.getField('selectBillsToGroup').dataSource.data = Array();
		groupWidget.getField('selectBillsToGroup').readOnly = true;
		ungroupWidget.dataSource.checkedRows = [];
		ungroupWidget.dataSource.data = Array();
	};

	this.groupDisgroupBills = function (widget) {
		if (widget.currentWidget.name == 'group') {
			// Realiza o agrupamento das comandas.
			groupWidget = widget.currentWidget;
			if (!_.isEmpty(groupWidget.currentRow.mainBill)) {
				if (!_.isEmpty(groupWidget.getField('selectBillsToGroup').value())) {
					currentRow = groupWidget.currentRow;
					
					mainBill = {
						"MAINBILL": {
							"DSCOMANDA": currentRow.MAINDSCOMANDA,
							"NRVENDAREST": currentRow.MAINNRVENDAREST,
							"NRCOMANDA": currentRow.MAINNRCOMANDA
						}
					};

					toGroupBills = Array();
					for (i = 0; i < currentRow.selectBillsToGroup.length; i++) {
						arr  = {
							"DSCOMANDA": currentRow.TOGROUPDSCOMANDA[i],
							"NRVENDAREST": currentRow.TOGROUPNRVENDAREST[i],
							"NRCOMANDA": currentRow.TOGROUPNRCOMANDA[i]
						};
						toGroupBills.push(arr);
					}

					BillService.groupBills(mainBill, toGroupBills).then(function(result){
						AccountController.getAccountData(function(accountData) {
							ScreenService.showMessage("Comandas agrupadas com sucesso. ");
							groupWidget.getField('mainBill').clearValue();
							groupWidget.getField('selectBillsToGroup').clearValue();
							groupWidget.getField('selectBillsToGroup').readOnly = true;
							ScreenService.closePopup();
							// Se a comanda atual estiver entre as comandas que serão agrupadas retorna para a tela principal.
							comandaAtual = _.filter(toGroupBills, function(c){
								return c.DSCOMANDA == accountData[0].DSCOMANDA && c.DSCOMANDA == accountData[0].DSCOMANDA && c.DSCOMANDA == accountData[0].DSCOMANDA;
							}.bind(this));
							if (!_.isEmpty(comandaAtual)) {
								UtilitiesService.backMainScreen();
							}
						}.bind(this));
					}.bind(this));

				} else {
					ScreenService.showMessage("Selecione as comandas a serem agrupadas.", 'alert');
				}
			} else {
				ScreenService.showMessage("Selecione a comanda principal.", 'alert');
			}
		} else {
			// Realiza o desagrupamento das comandas.
			ungroupWidget = widget.currentWidget;
			var selectedBills = ungroupWidget.getCheckedRows();
			if (!_.isEmpty(selectedBills)) {

				toUngroupBills = Array();
				for (i = 0; i < selectedBills.length; i++) {
					arr  = {
						"DSCOMANDA": selectedBills[i].DSCOMANDA,
						"NRVENDAREST": selectedBills[i].NRVENDAREST,
						"NRCOMANDA": selectedBills[i].NRCOMANDA,
						"DSCOMANDAPRI": selectedBills[i].DSCOMANDAPRI
					};
					toUngroupBills.push(arr);
				}
				
				BillService.ungroupBills(toUngroupBills).then(function(result){
					ScreenService.showMessage("Comandas desagrupadas com sucesso. ");
					ungroupWidget.dataSource.checkedRows = [];
					ScreenService.closePopup();
				}.bind(this));

			} else {
				ScreenService.showMessage("Selecione as comandas a serem desagrupadas.", 'alert');
			}
		}
	};

}

Configuration(function(ContextRegister){
	ContextRegister.register('MenuFunctionsController', MenuFunctionsController);
});