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

}

Configuration(function(ContextRegister){
	ContextRegister.register('MenuFunctionsController', MenuFunctionsController);
});