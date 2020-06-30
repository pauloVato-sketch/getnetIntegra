function OrderController(ZHPromise, ApplicationContext, ScreenService, OrderService, OrderGetAccessRepository, OperatorRepository, OrderRequestLoginRepository, ParamsMenuRepository, OrderCurrentProductRepository, Query, OrderProductObservation, AccountCart, OrderCurrentUser, AccountController, AccountGetAccountItems, AccountService, templateManager, TotalCartRepository, OrderCallWaiterRepository, OrderGetCallRepository, OperatorController, TableRepository, OrderBlockedIps, ConfigIpRepository, UtilitiesService, ConsumerLoginRepository, SessionRepository, WindowService){

	this.inProccess = false;

	this.showTemp = function (){
		WindowService.openWindow('ORDER_TEMPORARY_SCREEN');
	};

	this.showOrderCart = function (cart){
		if (cart.length > 0){
			AccountCart.save(cart).then(function (){
				WindowService.openWindow('ORDER_CHECK_ORDER_SCREEN');
			});
		}
		else {
			ScreenService.showMessage("Não há produtos no carrinho.");
		}
	};

	this.confirmOrder = function (widget){
		ScreenService.confirmMessage(
			'Deseja transmitir o pedido?',
			'question',
			function (){
				AccountController.order(widget);
			},
			function (){}
		);
	};

	this.checkSession = function(args){
		SessionRepository.findOne().then(function (consumerDetails){
			args.owner.newRow();
			args.owner.moveToFirst();
			if (consumerDetails){
				args.owner.setCurrentRow(consumerDetails);
			}
		});
	};

	this.login = function(row, tablePopup, setIPPopup){
		if (!row.DSEMAILCONS || !UtilitiesService.checkEmail(row.DSEMAILCONS)){
			ScreenService.showMessage("Favor introduzir um e-mail válido.");
		}
		else if (!row.password || row.password.length === 0){
			ScreenService.showMessage("Favor introduzir a senha.");
		}
		else {
			ConfigIpRepository.findOne().then(function (ipInfo){
				if (ipInfo !== null){
					OrderService.login(row.DSEMAILCONS, row.password).then(function (consumerDetails){
						SessionRepository.save(consumerDetails).then(function (){
							tablePopup.setCurrentRow({});
							ScreenService.openPopup(tablePopup);
						});
					});
				}
				else {
					ScreenService.showMessage("Antes de efetuar o login, configure o IP do servidor.");
					ScreenService.openPopup(setIPPopup);
				}
			});
		}
	};

	//OBS: codigo lixao a frente
	//dava pra fazer uma função mas seria tanto callback dentro de callback que estou deixando assim
	//gabriel s2
	this.prepareOrderCloseAccountLabels = function (widget) {
		AccountController.prepareAccountDetails(widget,
			function(){
				//orderCloseAccount
				var labels = ["Valor dos produtos", "Valor do serviço", "Valor do couvert", "Valor da consumação", "Valor total"];
				var valores = [ 'vlrprodutos', 'vlrservico', 'vlrcouvert', 'vlrconsumacao', 'vlrtotal'];
				var gridData = widget.dataSource.data[0];
				var data = [];
				//monta array novo com base nos dados do data source e nas labels escritas ali em cima
			    valores.some(function(element, index, array){
			    	if(gridData[element] !== null && gridData[element] !== undefined ){
			    		dataObject ={
			    			"LABEL":labels[index]+' - '+UtilitiesService.toCurrency(gridData[element])
			    		};
			    		data.push(dataObject);
			    	}
			    });
				widget.dataSource.data = data;
				//orderAccountDetails
				var orderWidget = widget.container.getWidget('orderAccountDetails');
			    var orderLabels = ["Numero de produtos"];
				var orderValores = ['numeroProdutos'];
				var orderGridData = orderWidget.dataSource.data[0];
				var orderData = [];
				//monta array novo com base nos dados do data source e nas labels escritas ali em cima
			    orderValores.some(function(element, index, array){
			    	if(orderGridData[element] !== null && orderGridData[element] !== undefined ){
			    		dataObject ={
			    			"LABEL":orderLabels[index]+' - '+orderGridData[element]
			    		};
			    		orderData.push(dataObject);
			    	}
			    });
				orderWidget.dataSource.data = orderData;
			});
	};
	//FIM codigo lixao

	this.showOrderLogin = function () {
		WindowService.openWindow('ORDER_LOGIN_SCREEN');
	};

	this.showAccess = function () {
		WindowService.openWindow('ORDER_ACCESS_SCREEN');
	};

	this.showMenu = function () {
		WindowService.openWindow('ORDER_MENU_SCREEN');
	};

	// refaz o preço para ficar no formato quantidade x preço (2x R$5,00)
	this.accountPrice = function (itensData){
		var cont = 0;
		itensData.forEach(function(item){
			preco = item.preco;
			quantidade = item.quantidade;
			itensData[cont].preco = quantidade + ' x ' + preco;
			cont++;
		});
		return itensData;
	};

	this.prepareAccountRequest = function (widget){
		this.checkAccess(function (){
			delete widget.dataSource.data;
			widget.currentRow = {};

			OperatorRepository.findAll().then(function (operatorData){
				AccountController.getAccountData(function (accountData){
					// prepara parâmetros para chamar a getAccountDetails (traz consumação, serviço, total, couvert e produtos)
					var chave = operatorData[0].chave;
					var modoHabilitado = operatorData[0].modoHabilitado;
					// isso é necessário pois dentro da getAccountDetails (parcial), o Order tem que se comportar como modo mesa
					if (modoHabilitado === 'O'){
						modoHabilitado = 'M';
					}
					var nrComanda = accountData[0].NRCOMANDA;
					var nrVendaRest = accountData[0].NRVENDAREST;

					AccountService.getAccountDetails(chave, modoHabilitado, nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData) {
						// pega os produtos

						if (accountDetailsData.AccountGetAccountItems.length === 0){
							ScreenService.showMessage('Não foi realizado nenhum pedido.');
							WindowService.openWindow('ORDER_MENU_SCREEN');
						}
						else{
							var itensData = accountDetailsData.AccountGetAccountItems;
							// refaz o preço para ficar no formato quantidade x preço (2x R$5,00)
							itensData = this.accountPrice(itensData);

							itensData.forEach(function(item) {
							    var dt = item.DTHRINCOMVEN;
							    var hora = dt.substring(0, 5);
							    var dieMonth = dt.substring(8, 13);
							    var today = new Date();
							    var year = today.getFullYear();
							    item.DTHRINCOMVEN = hora + " - " + dieMonth + "/" + year;
							 });

							widget.dataSource.data = itensData;
							widget.moveToFirst();
						}
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.checkAccess = function (callBack) {
		//Verifica se a mesa não está fechada ou com a conta solicitada
		//Caso esteja, o usuário não pode continuar usando o Order
		OperatorRepository.findOne().then(function (operatorData) {
			//Só faz isso no modo order
			if (operatorData.modoHabilitado === 'O') {

				var chave = operatorData.chave;

				AccountController.getAccountData(function (accountData) {
					var NRCOMANDA = accountData[0].NRCOMANDA;
					var NRVENDAREST = accountData[0].NRVENDAREST;

					OrderService.checkAccess(chave, NRCOMANDA, NRVENDAREST).then(function (tableData) {
						if (!tableData[0].OK) {
							this.removeAccess();
							this.showLoading();
							WindowService.openWindow('ORDER_LOGIN_SCREEN');
						} else {
							callBack();
						}
					}.bind(this));
				}.bind(this));
			} else {
				callBack();
			}
		}.bind(this));
	};

	this.requestLogin = function (table){
		ConsumerLoginRepository.findOne().then(function (consumerDetails){
			this.checkBlockedUsers(function () {
				OrderCurrentUser.save(consumerDetails);
				AccountCart.remove(Query.build());
				ConfigIpRepository.findOne().then(function (ipInfo){
					OrderService.requestLogin(consumerDetails.NMCONSUMIDOR, table, projectConfig.frontVersion, ipInfo.ipForBack).then(function(requestLoginData){
						if (requestLoginData.OperatorRepository) {
							OperatorController.configureMenu();
							this.showMenu();
						} else {
							this.showAccess();
						}
					}.bind(this),
					function(){
						ScreenService.showMessage("Erro na tentativa de login, verifique sua conexão com a internet.");
					});
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.removeAccess = function (NRACESSOUSER) {
		OperatorRepository.findOne().then(function (operatorData) {
			//Caso eu informe o NRACESSOUSER, ele faz com o numero informado, senão pega do repositório
			var chave = null;
			if (!NRACESSOUSER) {
				NRACESSOUSER = operatorData.NRACESSOUSER;
				chave = operatorData.chave;
			}

			OrderService.controlUserAccess(NRACESSOUSER, 'I', chave);
		});
	};

	this.controlUserAccess = function(row, status){
		OperatorRepository.findOne().then(function (operatorData){
			OrderService.controlUserAccess(row.nracessouser, status, operatorData.chave).then(function(){
				if (status === 'B'){
					this.goToTablesPage('IP bloqueado com sucesso.');
				}
				else if (status === 'I') {
					var query = Query.build()
									.where('NRMESA').equals(row.mesa);

					TableRepository.findOne(query).then(function(activeTable){
						if (activeTable === null || (activeTable !== null && activeTable.IDSTMESAAUX === "S")){
							ScreenService.showMessage('Não é possível liberar acesso para uma mesa fechada.');
							this.goToTablesPage();
						}
						else {
							this.goToTablesPage('Solicitação desconsiderada com sucesso.');
						}
					}.bind(this));
				}
				else {
					this.goToTablesPage('Ação confirmada.');
				}
			}.bind(this));
		}.bind(this));
	};

	this.goToTablesPage = function (message){
		this.getNotifications().then(function (){
			ScreenService.closePopup();
			WindowService.openWindow('TABLES_SCREEN');
			if (message){
				ScreenService.successNotification(message);
			}
		});
	};

	this.checkBlockedUsers = function (callBack) {
		OrderService.checkBlockedUsers().then(function (dataBack) {
			if (dataBack[0].OK) {
				callBack();
			} else {
				ScreenService.showMessage("Seu IP está bloqueado. Para liberar, chame o garçom.");
			}
		});
	};

	this.getBlockedIps = function (callBack) {
		OperatorRepository.findOne().then(function (operatorData) {
			var chave = operatorData.chave;
			OrderService.getBlockedIps(chave).then(function (IPs) {
				callBack(IPs);
			});
		});
	};

	this.unblockUser = function (row) {
		OperatorRepository.findOne().then(function (operatorData) {
			var chave = operatorData.chave;
			var NRACESSOUSER = row.NRACESSOUSER;
			OrderService.controlUserAccess(NRACESSOUSER, 'I', chave).then(function () {
				ScreenService.showMessage("Desbloqueado com sucesso!");
				ScreenService.closePopup();
			});
		});
	};

	this.prepareOpeningBlock = function (widget, row) {
		var arrayRow = [];
		arrayRow.push(row);
		widget.dataSource.data = arrayRow;
		widget.setCurrentRow(row);
	};

	this.prepareBlockedIps = function (widget) {
		this.getBlockedIps(function (IPs) {
			widget.dataSource.data = IPs;
		});
	};

	//Esta função irá liberar o acesso do consumidor. Ela é chamada pela função TableController.open
	this.completeReleaseAccess = function (nracessouser, tablesWidget) {
		OperatorRepository.findOne().then(function(operatorData){
			OrderService.allowUserAccess(operatorData.chave, nracessouser).then(function(){
				ScreenService.closePopup();
				ScreenService.showMessage('Acesso liberado com sucesso.');
				ApplicationContext.TableController.refreshTables(tablesWidget);
			});
		});
	};

	//Função que monta cardápio no order
	this.loadMenu = function (menuWidget){
		this.checkAccess(function (){
			ParamsMenuRepository.findAll().then(function (products){
				AccountCart.findAll().then(function (cart){
					menuWidget.dataSource.data = products;

					var total = 0;
					for (var i in cart){
						total += cart[i].PRITEM * (cart[i].qtty || 1);
					}

					menuWidget.shoppingCart.items = cart;
					menuWidget.shoppingCart.deliveryFee = 0;
					menuWidget.shoppingCart.subtotal = total;
					menuWidget.shoppingCart.total = total;
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.loadProductDetails = function(productWidget){
		var obs = AccountController.getObservations(productWidget.productItem.OBSERVATIONS);
		if (productWidget.productItem.detailPages === undefined){
			productWidget.productItem.detailPages = [];
			productWidget.productItem.detailPages.push({
				title: "observações",
				constraints: {
					minSelection: 0
				},
				items: obs
			});
		}
	};

	this.updateTotal = function (widgetData, widget) {
		var QTDPRODUCT = widgetData.QTDPRODUCT;
		var PRITEM = widgetData.PRITEM;
		var TOTAL = parseFloat(QTDPRODUCT) * parseFloat(PRITEM);
		TOTAL = UtilitiesService.toCurrency(TOTAL);
		OrderCurrentProductRepository.findAll().then(function (product) {
			product[0].TOTAL = TOTAL;
			if(product[0].TOTAL === "NaN"){
				var total = parseFloat(product[0].PRITEM) * parseFloat(product[0].QTDPRODUCT);
				total = UtilitiesService.toCurrency(total);
				widget.currentRow.TOTAL = total;
			}else{
				widget.currentRow.TOTAL = TOTAL;
			}
		}.bind(this));
	};

	this.prepareProductDetail = function (productWidget, container) {
	    productWidget.newRow();
	    container.changeMode('lista');
		OrderCurrentProductRepository.findAll().then(function (product) {
	        if(productWidget.dataSource.data && productWidget.dataSource.data.length > 0) {
	            delete productWidget.dataSource.data;
	        }

	        product[0].QTDPRODUCT = 1;

	        var total = parseFloat(product[0].PRITEM) * parseFloat(product[0].QTDPRODUCT);
			total = UtilitiesService.toCurrency(total);
			product[0].TOTAL = total;


			productWidget.setCurrentRow(product[0]);

			// associando as observações ao datasource das observações do produto
			productWidget.getField('CDOCORR').dataSource.data = AccountController.getObservations(product[0].OBSERVATIONS);

			templateManager.updateTemplate();
		}.bind(this));
	};

	this.getData = function(){
		var today = new Date();
	    var dd = today.getDate();
	    var mm = today.getMonth()+1; //January is 0!

	    var yyyy = today.getFullYear();
	    if(dd<10){
	        dd='0'+dd;
	    }
	    if(mm<10){
	        mm='0'+mm;
	    }
	    var todayStr = dd+'/'+mm+'/'+yyyy + " " + new Date().toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
	    return todayStr;
	};

	// OBSOLETE
	// this.addToOrderCart = function(product){
	// 	AccountController.getOrderCodeProductID(function (id){
	// 		var position = '1';
	// 		var cartItems = {
	// 			ID: id,
	// 			UNIQUEID: id,
	// 			GRUPO: product.NMGRUPO,
	// 			CDPRODUTO: product.CDPRODUTO,
	// 			DSBUTTON: product.DSBUTTON,
	// 			POSITION: "posição " + position,
	// 			POS: position,
	// 			PRECO: product.PRECO,
	// 			PRITEM: product.PRITEM,
	// 			IMPRESSORAS: product.IMPRESSORAS,
	// 			CDOCORR: product.CDOCORR,
	// 			OBSERVATIONS: product.OBSERVATIONS,
	// 			QTDPRODUCT: product.QTDPRODUCT || 1,
	// 			DATA: this.getData()
	// 		};
	// 		AccountCart.save(cartItems);
	// 	}.bind(this));
	// };

	this.updateOrderCart = function(widget) {
		var cart = widget.dataSource.data;
		handleOneChoiceOnly(widget.currentRow, 'NRSEQIMPRLOJA');
		cart.forEach(function(product){
			if (!product.PRODUTOS){
				product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
				//product.ATRASOPROD = this.formatProductDelay(product.ATRASOPROD);
				product.NMIMPRLOJA = "";
				if (product.NRSEQIMPRLOJA) {
					product.NMIMPRLOJA = getPrinterName(product.NRSEQIMPRLOJA[0], product.IMPRESSORAS);
				}
			}
		}.bind(this));

		this.saveCart(cart);
	};

	this.hideLoading = function () {
		$('.zh-background-loading').removeClass('hideLoading');
		$('.zh-background-loading').addClass('hideLoading');
	};

	this.showLoading = function () {
		$('.zh-background-loading').removeClass('hideLoading');
	};

	this.checkPermission = function () {
		this.hideLoading();
		// além de verificar a pemissão do usuário, esta função recebe a field time regress para construir o cronômetro regressivo na tela
		var tempo = 720;

		// a validação de permissão e ocorrência dos segundos no relógio ocorrem dentro deste set interval
		var interval = setInterval(function() {
			if (!this.inProccess) {
				this.loginUser(interval);
			}
		}.bind(this), 5000);
	};

	this.cancelRequest = function () {
		OrderRequestLoginRepository.findOne().then(function (accessData) {
			var NRACESSOUSER = accessData.NRACESSOUSER;
			this.removeAccess(NRACESSOUSER);
			this.showLoading();
			WindowService.openWindow('ORDER_LOGIN_SCREEN');
		}.bind(this));
	};

	this.setDashboardHeader = function (container) {
		OrderRequestLoginRepository.findOne().then(function (userData) {
			container.label = userData.NMUSUARIO;
			templateManager.updateTemplate();
		});
	};

	this.loginUser = function (interval) {
		this.inProccess = true;
		OrderRequestLoginRepository.findOne().then(function(requestData){
			var userData = requestData;
			ConfigIpRepository.findOne().then(function (ipInfo) {
				OrderService.loginUser(requestData.NRACESSOUSER, ipInfo.ipForBack).then(function(loginResult){
					ScreenService.buildUserData(userData.NMUSUARIO, [userData.NMMESA]);
					if (!loginResult[0]){ // se retorna msg, retorna array, senão retorna objeto (por isso o if ta certo)
						if (loginResult.bloqueado) {
							this.showLoading();
							WindowService.openWindow('ORDER_LOGIN_SCREEN');
						} else {
							// se a requisição deu certo, preenche o repositório TableActiveTable
							OperatorController.configureMenu();
							$('.zh-background-loading').removeClass('hideLoading');
							AccountController.updateObservationsInner();
							this.showLoading();
							WindowService.openWindow('ORDER_MENU_SCREEN');
						}
						// para o interval que fica tentando fazer login
						clearInterval(interval);
					}
					this.inProccess = false;
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	/*
	this.startCountdown = function (time) {
		var retorno = '';
    	// Se o tempo não for zerado
    	if((time - 1) >= 0){
        	// Pega a parte inteira dos minutos
        	var min = parseInt(time/60);
        	// Calcula os segundos restantes
        	var seg = time%60;
        	// Formata o número menor que dez, ex: 08, 07, ...
    		if(seg <=9){
         		seg = "0"+seg;
    		}
        	// Cria a variável para formatar no estilo hora/cronômetro
     		var printableTime = min + ':' + seg;
        	// diminui o tempo
     		time--;

    		// Quando o contador chegar a zero faz esta ação
     		retorno = printableTime;
    	}
    	return retorno;
	};
	*/

	//Função que cancela um pedido (esvazia o carrinho)
	this.emptyCart = function(){
		ScreenService.confirmMessage(
			'Deseja cancelar o pedido e voltar para a página inicial?',
			'question',
			function(){
				AccountCart.remove(Query.build()).then(function (){
					WindowService.openWindow('ORDER_MENU_SCREEN');
				});
			},
			function(){}
		);
	};

	// Esta função serve para incrementar o dasource do form-without-scroller.html que é aberto para acessos pendentes
	this.prepareInnerWidgetAccess = function (row, innerWidget){
		var params = {
			'IDACESSOUSER' : row.IDACESSOUSER,
			'NMMESA' : row.NMMESA,
			'NMUSUARIO' : row.NMUSUARIO,
			'NRACESSOUSER' : row.NRACESSOUSER,
			'NRMESA' : row.NRMESA
		};

		innerWidget.dataSource.data = params;
	};

	// Esta função serve para incrementar o dasource do form-without-scroller.html que é aberto para chamadas
	this.prepareInnerWidgetCalls = function (row, widget){

		var params = {};
		params.labelMesa = row.labelMesa;
		params.tempo = row.tempo;
		params.mesa = row.mesa;
		params.NRACESSOUSER = row.nracessouser;
		widget.dataSource.data = params;

	};

	//Esta função carrega o valor total dos itens no carro de compras
	this.calculateCartTotal = function(widget){
		AccountController.buildOrderCode().then(function () {
			AccountCart.findAll().then(function (cart) {
				totalProd = 0;
				cart.forEach(function(product){
					totalProd += parseFloat(product.PRITEM) * parseFloat(product.QTDPRODUCT);
				});

				widget.getField('vrTotalPedido').label = UtilitiesService.toCurrency(totalProd);
			});
		});
	};

	//Função que chama o garçom
	this.callWaiter = function (callType){
		this.checkAccess(function () {
			OperatorRepository.findAll().then(function (dataOperator){
				var nracessouser = dataOperator[0].NRACESSOUSER;
				OrderService.callWaiter(nracessouser, callType);
				if (callType === 'F') {
					ScreenService.showMessage('Conta solicitada com sucesso.');
					WindowService.openWindow('ORDER_MENU_SCREEN');
				} else {
					ScreenService.showMessage('Chamada realizada com sucesso.');
					ScreenService.goBack();
				}
			});
		});
	};

	this.prepareListNotifications = function(widget){
		this.updateNotificationsLabel().then(function (allNotifications){
			widget.dataSource.data = allNotifications;
		});
	};


	this.mergeArrays = function (arrayA, arrayB) {
		arrayB.forEach(function (eachElement) {
			arrayA.push(eachElement);
		});
		return arrayA;
	};

	this.answerTable = function (row) {
		OrderService.answerTable(row.nracessouser).then(function(){
			ScreenService.closePopup();
		}.bind(this));
	};

	this.checkTableStatus = function(row, openTableWidget, tablesWidget){
		row.NRMESA = row.mesa;
		row.NRACESSOUSER = row.nracessouser;
		var query = Query.build().where('NRMESA').equals(row.NRMESA);
		TableRepository.findOne(query).then(function(activeTable){
			if (activeTable.IDSTMESAAUX === "D"){
				// If table is available, show open table popup.
				ApplicationContext.TableController.prepareOpening(row, openTableWidget);

			}
			else if (activeTable.IDSTMESAAUX === "S"){
				this.controlUserAccess(row, 'I');
			}
			else {
				// If table is already open, simply allow access.
				this.completeReleaseAccess(row.NRACESSOUSER, tablesWidget);
			}
		}.bind(this));
	};

	this.getNotifications = function () {
		var defer = ZHPromise.defer();
		OrderGetAccessRepository.clearAll().then(function (){
			OrderGetCallRepository.clearAll().then(function (){
				OrderService.getAccess().then(function (accessList) {
					OrderService.getCall().then(function (callsList) {
						this.updateNotificationsLabel(accessList, callsList).then(function (allNotifications) {
							defer.resolve(allNotifications);
						});
					}.bind(this), function () {
						defer.reject();
					});
				}.bind(this), function () {
					defer.reject();
				});
			}.bind(this));
		}.bind(this));
		return defer.promise;
	};

	this.updateNotificationsLabel = function (getAccess, getCall){
		var defer = ZHPromise.defer();
		var joinNotifications = function (getAccess, getCall){
			var allNotifications = [];

			getAccess.forEach(function (eachAccess){
				allNotifications.push(eachAccess);
			});
			getCall.forEach(function (eachCall){
				allNotifications.push(eachCall);
			});

			ScreenService.setNotificationHint('requests', allNotifications.length);
			defer.resolve(allNotifications);
		};

		if (getAccess && getCall){
			joinNotifications(getAccess, getCall);
		}
		else {
			OrderGetAccessRepository.findAll().then(function (getAccess){
				OrderGetCallRepository.findAll().then(
				function (getCall){
					joinNotifications(getAccess, getCall);
				},
				function () {
					defer.reject();
				});
			}, function () {
				defer.reject();
			});
		}

		return defer.promise;
	};

	this.showNotifications = function (){
		var notificationsWidget = templateManager.containers.tables.widgets[1].widgets[0];
		var blockedIpsWidget = templateManager.containers.tables.widgets[1].widgets[1];
		var widgetToShow = templateManager.containers.tables.widgets[1];

		this.getNotifications().then(function (allNotifications) {
			if (allNotifications.length === 0){
				notificationsWidget.dataSource.data = null;
			}
			else {
				this.prepareListNotifications(notificationsWidget);
			}
			this.prepareBlockedIps(blockedIpsWidget);
			ScreenService.openPopup(widgetToShow);
		}.bind(this));
	};

	this.controlInternalWidgetOpening = function (widget, row) {
		var widgets = widget.widgets;
		if (widgets[0].name !== row.widgetName) {
			var aux = widgets[0];
			widgets[0] = widgets[1];
			widgets[1] = aux;
		}
		widgets[0].currentRow = row;
	};

	this.confirmLogout = function(){

		ScreenService.confirmMessage(
			'Realmente deseja sair?',
			'quastion',
			function () {
				WindowService.openWindow('ORDER_LOGIN_SCREEN');
			},
			function (){
				ScreenService.goBack();
			}
		);

	}.bind(this);

	this.prepareDashboard = function(widget){
		OperatorRepository.findAll().then(function (operatorData){
			this.getAccountData(function (accountData) {

				// prepara parâmetros para chamar a getAccountDetails (traz desconto, consumação, serviço, total, couvert e produtos)
				var chave = operatorData[0].chave;
				var modoHabilitado = operatorData[0].modoHabilitado;
				if (modoHabilitado === 'O') {
					modoHabilitado = 'M';
				}
				var nrComanda = accountData[0].NRCOMANDA;
				var nrVendaRest = accountData[0].NRVENDAREST;

				AccountService.getAccountDetails(chave, modoHabilitado, nrComanda, nrVendaRest, 'M', '').then(function (accountDetailsData) {
						total = UtilitiesService.toCurrency(total);
						accountDetailsData.AccountGetAccountDetails[0].labeltotal = total;
						widget.dataSource.data = accountDetailsData.AccountGetAccountDetails;
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.toggleCart = function(widget){
		widget.showCart = !widget.showCart;
	};

	this.openNewConsumer = function(setIPPopup){
		ConfigIpRepository.findOne().then(function (ipInfo){
			if (ipInfo !== null){
				WindowService.openWindow('NEW_CONSUMER_SCREEN');
			}
			else {
				ScreenService.showMessage("Antes de efetuar seu cadastro, configure o IP do servidor.");
				ScreenService.openPopup(setIPPopup);
			}
		});
	};

	this.newConsumer = function(row, widget){
		var DSEMAILCONS = row.DSEMAILCONS;
		var NMCONSUMIDOR = row.NMCONSUMIDOR;
		var NRCELULARCONS = (row.NRCELULARCONS !== null) ? row.NRCELULARCONS.replace(/\(|\)|-|\s/g, '') : null; // Removes mask formatting.
		var CDSENHACONSMD5 = row.CDSENHACONSMD5;
		var CDIDCONSUMID = (row.CDIDCONSUMID !== null) ? row.CDIDCONSUMID : '';

		if (!widget.isValid()){
			ScreenService.showMessage("Preencha todos os campos obrigatórios.");
		}
		else if (!UtilitiesService.checkEmail(DSEMAILCONS)){
			ScreenService.showMessage("Favor introduzir um endereço de e-mail válido.");
		}
		else if (CDSENHACONSMD5 !== row.passwordCheck){
			ScreenService.showMessage("As duas senhas devem ser iguais. Favor digite-as novamente.");
		}
		else {
			OrderService.newConsumer(NMCONSUMIDOR, DSEMAILCONS, NRCELULARCONS, CDSENHACONSMD5, CDIDCONSUMID).then(function (){
				WindowService.openWindow('ORDER_LOGIN_SCREEN');
			});
		}
	};

}

Configuration(function(ContextRegister){
	ContextRegister.register('OrderController', OrderController);
});

