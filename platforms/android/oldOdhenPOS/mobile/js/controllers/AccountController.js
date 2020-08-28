function AccountController(ZHPromise, OperatorRepository, AccountCart, AccountGetAccountItems, AccountGetAccountDetails,
	ParamsMenuRepository, TableActiveTable, ParamsObservationsRepository, AccountService, ParamsGroupRepository, SmartPromoRepository,
	ScreenService, Query, $rootScope, AccountSavedCarts, ApplicationContext, templateManager, ParamsProdMessageCancelRepository,
	TableService, BillActiveBill, UtilitiesService, SmartPromoGroups, SmartPromoProds, SmartPromoTray, ParamsParameterRepository,
	OperatorService, TotalCartRepository, AccountLastOrders, TimestampRepository, TransactionsService, PaymentService,
	WindowService, WaiterNamedPositionsState, PermissionService, CartPool, ParamsPriceTimeRepository, metaDataFactory, PrinterService,
	SubPromoGroups, SubPromoProds, SubPromoTray, PaymentRepository, ParamsMensDescontoObs, SellerControl, CarrinhoDesistencia, ProdutosDesistencia,
	BillService, PerifericosService, ProdSenhaPed) {

	var self = this;
	var allObservations = [];
	var observationMap = [];
	var NRSEQMOVMOB = ""; // PK da tabela de pagamento
    var selectControl = [];
    var trayClone;

	var Mode = {
		TABLE: 'M',
		ORDER: 'O',
		BILL: 'C',
		BALCONY: 'B'
	};

	var Param = {
		YES: 'S',
		NO: 'N'
	};

	this.updateObservationsInner = function (callback) {
		ParamsObservationsRepository.findAll().then(function (obs) {
			allObservations = obs;
			if (callback)
				callback();
		});
	};

	// always do this the first time
	this.updateObservationsInner();

	this.loadCartData = function (widget) {
		//widget.container.restoreDefaultMode();
		this.updateObservationsInner();
	};

	var orderCode = "";
	var userKey = null;

	this.buildOrderCode = function () {
		var defer = ZHPromise.defer();
		var resolver = function (operatorData) {
			var millisec = new Date().getTime();
			orderCode = millisec + "K" + operatorData.chave;
			userKey = operatorData.chave;
			defer.resolve();
		};
		if (userKey) {
			resolver({ chave: userKey });
		} else {
			OperatorRepository.findOne().then(resolver, function () {
				defer.reject();
			});
		}
		return defer.promise;
	};

	var cancelObservations = [];

	var updateCancelObservationsInner = function (callback) {
		ParamsProdMessageCancelRepository.findAll().then(function (obs) {
			cancelObservations = obs;
			if (callback)
				callback();
		});
	};

	updateCancelObservationsInner();

	this.log = function (stuff) {
		console.log(stuff);
	};

	this.getAccountData = function (callback) {
		var defer = ZHPromise.defer();
		OperatorRepository.findOne().then(function (operatorData) {
			var accountData;
			if ((operatorData.modoHabilitado === 'M') || (operatorData.modoHabilitado === 'O')) {
				TableActiveTable.findAll().then(function (tableData) {
					defer.resolve(callback(tableData));
				});
			} else if (operatorData.modoHabilitado === 'C') {
				BillActiveBill.findAll().then(function (billData) {
					defer.resolve(callback(billData));
				});
			} else if (operatorData.modoHabilitado === 'B') {
				defer.resolve(callback([]));
			}
		});
		return defer;
	};

	this.validateCart = function (args) {
		AccountCart.findAll().then(function (items) {
			if (items.length > 0) {
				ScreenService.confirmMessage(
					'Deseja cancelar o pedido e voltar para a página principal?',
					'question',
					function () {
						self.accountCartClear();
					},
					function () { }
				);
			} else {
				UtilitiesService.backMainScreen();
			}
		});
	};

	this.setGroupHeader = function (args) {
		var str = "Selecione ";
		var quantMIN = args.row.QTPRGRUPROMIN || 0;
		var quantMAX = args.row.QTPRGRUPPROMOC || 0;
		if (quantMIN >= quantMAX) {
			if (quantMAX <= 0)
				str += " os Produtos";
			else if (quantMAX > 1)
				str += parseInt(quantMAX).toString() + " Produtos";
			else
				str += parseInt(quantMAX).toString() + " Produto";
		} else {
			if (quantMAX > 1)
				str += "entre " + parseInt(quantMIN).toString() + " e " + parseInt(quantMAX).toString() + " Produtos";
			else
				str += "entre " + parseInt(quantMIN).toString() + " e " + parseInt(quantMAX).toString() + " Produto";
		}

		args.owner.container.label = str;
		UtilitiesService.setHeader(args.owner.container);
	};

	this.isVisibleAccountItems = function (container, modoHabilitado, IDLUGARMESA) {
		AccountGetAccountItems.findAll().then(function (accountItems) {
			var closeAccountItemsTable = container.getWidget('closeAccountItemsTable');
			var closeAccountItemsBill = container.getWidget('closeAccountItemsBill');
			//Verificação adicional se é feito o controle de posições no modo modo para troca de template dos itens na parcial de conta do fechamento de mesa.
			closeAccountItemsTable.isVisible = !Util.isEmptyOrBlank(accountItems) && modoHabilitado === 'M' && IDLUGARMESA === 'S';
			closeAccountItemsBill.isVisible = !Util.isEmptyOrBlank(accountItems) && (modoHabilitado === 'C' || (IDLUGARMESA === 'N' && modoHabilitado === 'M'));
			if (closeAccountItemsTable.isVisible) {
				self.refreshItems(closeAccountItemsTable);
			} else if (closeAccountItemsBill.isVisible) {
				self.refreshItems(closeAccountItemsBill);
			}
		});
	};

	this.switchTemplate = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' || operatorData.modoHabilitado === 'B' || operatorData.IDLUGARMESA === 'N') {
				widget.template = '../../../../templates/widget/list-grouped-default.html';
			}
			else {
				widget.template = '../../../../templates/widget/list-grouped-position.html';
			}
		});
	};

	this.showSmartProds = function (owner) {
		if (owner.currentRow.composicao !== null && owner.currentRow.composicao.length > 0) {
			if (owner.widgetsBackup) owner.widgets = owner.widgetsBackup;
			owner.widgets[0].fields[0].dataSource.data = owner.currentRow.composicao;
			owner.widgets[0].isVisible = true;
		}
		else {
			if (owner.widgets) {
				owner.widgetsBackup = owner.widgets;
				delete owner.widgets;
			}
		}
	};

	this.refreshItems = function (widget) {
		delete widget.dataSource.data;
		widget.currentRow = {};
		AccountGetAccountItems.findAll().then(function (data) {
			for (var i in data) {
				if (parseFloat(data[i].quantidade.replace(',', '.')) != 1) {
					data[i].DSBUTTON = data[i].quantidade + " x " + data[i].DSBUTTON;
				}
			}
			widget.dataSource.data = data;
		});
	};

	this.resetCartLabel = function (cartAction) {
		AccountCart.findAll().then(function (products) {
			if (products.length > 0) {
				cartAction.hint = products.length;
			} else {
				cartAction.hint = '';
			}
		});
	};

	/* Checks to see if the cart is empty, preventing the user from needlessly entering the checkOrder page. */
	this.showCart = function () {
		AccountCart.findAll().then(function (cartData) {
			CartPool.findAll().then(function (cartPool) {
				// valida se tem produtos no carrinho
				if (!(Util.isArray(cartData) && Util.isEmptyOrBlank(cartData)) || !(Util.isArray(cartPool) && Util.isEmptyOrBlank(cartPool))) {
					OperatorRepository.findOne().then(function (operatorData) {
						self.showCheckOrderScreen(operatorData);
					}.bind(this));
				} else {
					ScreenService.showMessage('Não há produtos no carrinho.');
				}
			}.bind(this));
		}.bind(this));
	};

	this.showCheckOrderScreen = function (operatorData) {
		if (operatorData.modoHabilitado === 'O') {
			return WindowService.openWindow('ORDER_CHECK_ORDER_SCREEN');
		} else {
			if (operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' || operatorData.modoHabilitado === 'B' || operatorData.IDLUGARMESA === 'N') {
				return WindowService.openWindow('CHECK_ORDER_SCREEN2');
			}
			else {
				return WindowService.openWindow('CHECK_ORDER_SCREEN');
			}
		}
	};

	this.controlVisible = function (widget) {
		ParamsParameterRepository.findAll().then(function (params) {
			OperatorRepository.findOne().then(function (operatorData) {
				var serviceAction = widget.getAction('alterservico');
				var swiservico = widget.getField('swiservico');
				var servico = widget.getField('servico');
				var lblservico = widget.getField('lblservico');
				var lblVendedorAbert = widget.getField('lblVendedorAbert');
				var nmVendedorAbert = widget.getField('NMVENDEDORABERT');
				var btncloseAccount = widget.getAction('BtncloseAccount');

				if (params[0].IDCOMISVENDA === 'N') {
					if (servico) {
						lblservico.disabled = true;
						servico.value = '0,00';
						servico.disabled = true;
						swiservico.isVisible = false;
						swiservico.value(false);
					}
					else {
						widget.getField('vlrservico').value = '0,00';
					}
					serviceAction.isVisible = false;
				}
				else {
					var changeCharge = widget.container.getWidget('changeCharge');

					lblservico.disabled = false;
					servico.disabled = false;
					swiservico.isVisible = true;
					serviceAction.isVisible = true;

					changeCharge.getField('radioChargeChange').applyDefaultValue();
					changeCharge.getField('radioCharge').applyDefaultValue();
					changeCharge.getField('radioCharge').isVisible = true;
					changeCharge.getField('TIPOGORJETA').applyDefaultValue();
					changeCharge.getField('TIPOGORJETA').isVisible = false;
					changeCharge.getField('vlrservico').applyDefaultValue();
					changeCharge.getField('vlrservico').isVisible = false;
					changeCharge.dataSource.data[0].value = null;
				}

				if (params[0].IDCONSUMAMIN === 'N') {
					var consumacao = widget.getField('consumacao');
					widget.getField('lblconsumacao').disabled = true;
					consumacao.disabled = true;
					widget.getField('swiconsumacao').isVisible = false;
					widget.getField('swiconsumacao').value(false);
					consumacao.value = '0,00';
				}
				else {
					widget.getField('lblconsumacao').disabled = false;
					widget.getField('consumacao').disabled = false;

					if (operatorData.modoHabilitado !== 'O') {
						widget.getField('swiconsumacao').isVisible = true;
					}
				}

				if (params[0].IDCOUVERART === 'N') {
					var couvert = widget.getField('couvert');
					widget.getField('lblcouvert').disabled = true;
					couvert.disabled = true;
					widget.getField('swicouvert').isVisible = false;
					widget.getField('swicouvert').value(false);
					couvert.value = '0,00';
				} else {
					widget.getField('lblcouvert').disabled = false;
					widget.getField('couvert').disabled = false;


					if (operatorData.modoHabilitado === 'O') {
						//Modo order não trabalha com estas switches
					} else {
						widget.getField('swicouvert').isVisible = true;
					}
				}

				if (operatorData.modoHabilitado === 'M') {
					lblVendedorAbert.isVisible = true;
					nmVendedorAbert.isVisible = true;
					swiservico.isVisible = true;
				} else if (operatorData.modoHabilitado === 'C') {
					lblVendedorAbert.isVisible = false;
					nmVendedorAbert.isVisible = false;
					swiservico.isVisible = false;
				}
			}.bind(this));
		});
	};

	this.recalcPrice = function (row) {
		var total = row.vlrprodutos;

		if (!row.vlrdesconto) {
			row.desconto = '0,00';
		} else {
			row.desconto = '' + UtilitiesService.formatFloat(row.vlrdesconto);
			total = Math.round((total - row.vlrdesconto) * 100) / 100;
		}

		if (!row.swiservico) {
			row.servico = '0,00';
		} else {
			row.servico = '' + UtilitiesService.formatFloat(row.vlrservico);
			total += row.vlrservico;
		}

		if (!row.swiconsumacao) {
			row.consumacao = '0,00';
		} else {
			row.consumacao = '' + UtilitiesService.formatFloat(row.vlrconsumacao);
			total = total + row.vlrconsumacao;
		}

		if (!row.swicouvert) {
			row.couvert = '0,00';
		} else {
			row.couvert = '' + UtilitiesService.formatFloat(row.vlrcouvert);
			total = total + row.vlrcouvert;
		}

		row.total = '' + UtilitiesService.formatFloat(total);
	};

	this.changeSwitch = function (widget, fieldName, accessName) {
		setTimeout(function () {
			OperatorRepository.findAll().then(function (operatorData) {
				if (operatorData[0][accessName] !== 'S') {
					if (operatorData[0][accessName] === 'C') {

						// bloqueia a mudança pra mudar manualmente caso o supervisor autorize
						widget.currentRow['swi' + fieldName] = !widget.currentRow['swi' + fieldName];

						PermissionService.checkAccess(accessName).then(function (CDSUPERVISOR) {
							widget.currentRow['swi' + fieldName] = !widget.currentRow['swi' + fieldName];
							widget.currentRow.CDSUPERVISOR = CDSUPERVISOR;
							this.recalcPrice(widget.currentRow);
						}.bind(this));
					}
					else if (operatorData[0][accessName] === 'N') {
						// não tem permissão
						ScreenService.showMessage("Você não possui permissão para realizar esta ação.");
						// bloqueia a mudança no switch
						widget.currentRow['swi' + fieldName] = !widget.currentRow['swi' + fieldName];
					}
				}
				else {
					widget.currentRow.CDSUPERVISOR = operatorData[0].CDOPERADOR;
					this.recalcPrice(widget.currentRow);
				}
				templateManager.onUpdate();
			}.bind(this));
		}.bind(this), 1);
	};

	this.openTableAndOrder = function (row, positionsField, container) {
		ApplicationContext.TableController.open(row, null, function () {
			self.order(container.getWidget('checkOrder'), null, null);
		}, positionsField);
	};

	this.orderPRODUTOS = function (PRODUTOS) {
		if (_.isNil(PRODUTOS)) {
			return null;
		} else {
			return PRODUTOS.sort(sortPRODUTOS);
		}
	};

	function sortPRODUTOS(a, b) {
		return a.ID > b.ID ? 1 : -1;
	}

	this.order = Util.buildDebounceMethod(function (widget, returnParam, saleProdPass) {
		SellerControl.findOne().then(function (CDVENDEDOR) {
			CDVENDEDOR = !_.isEmpty(CDVENDEDOR) ? CDVENDEDOR : null;
			OperatorRepository.findOne().then(function (operatorData) {
				if (operatorData.IDCAIXAEXCLUSIVO === 'N' && returnParam === true) SellerControl.clearAll();
				self.updateCart(widget, null).then(function () {
					self.returnRepository(operatorData).then(function (products) {
						isValidPrinterChoice(widget.dataSource.data).then(function () {
							ApplicationContext.OrderController.checkAccess(function () {
								if (widget.dataSource.data.length > 0) {
									var cartProducts = self.recoverSubPromos(products);
									var produtos = [];

									cartProducts.forEach(function (produto) {
										var qtd = ((produto.QTPRODCOMVEN === undefined) ? '1' : produto.QTPRODCOMVEN);

										if (produto.NRSEQIMPRLOJA === null) {
											produto.NRSEQIMPRLOJA = [];
											produto.NRSEQIMPRLOJA[0] = null;
										}

										/* Remove observações e atraso do produto pai antes de enviar. */
										if (!_.isEmpty(produto.PRODUTOS) && produto.IDIMPPRODUTO != '1') {
											produto.ATRASOPROD = 'N';
											produto.TOGO = 'N';
											produto.TXPRODCOMVEN = '';
										}
										else {
											produto.TXPRODCOMVEN = this.obsToText(produto.CDOCORR, produto.DSOCORR_CUSTOM);
										}

										var produtoMontado = {
											CDPRODUTO: produto.CDPRODUTO || null,
											DSBUTTON: produto.DSBUTTON || null,
											QTPRODCOMVEN: qtd || null,
											NRLUGARMESA: produto.POS || null,
											TXPRODCOMVEN: produto.TXPRODCOMVEN || null,
											CDOCORR: produto.CDOCORR || [],
											CUSTOMOBS: produto.DSOCORR_CUSTOM || null,
											VRPRECCOMVEN: produto.PRITEM || null,
											IDIMPPRODUTO: produto.IDIMPPRODUTO || null,
											IDTIPCOBRA: produto.IDTIPCOBRA || null,
											PRODUTOS: this.orderPRODUTOS(produto.PRODUTOS),
											DATA: produto.DATA || null,
											ID: produto.ID || null,
											UNIQUEID: produto.UNIQUEID || null,
											ATRASOPROD: produto.ATRASOPROD || null,
											TOGO: produto.TOGO || null,
											PRINTER: produto.NRSEQIMPRLOJA[0] || null,
											REFIL: produto.refilSet || false,
											NRCOMANDA: produto.NRCOMANDA || null,
											NRVENDAREST: produto.NRVENDAREST || null,
											VRPRECCLCOMVEN: produto.VRPRECITEMCL || null
										};

										produtos.push(produtoMontado);
									}.bind(this));

									this.getAccountData(function (accountData) {
										CartPool.findAll().then(function (cartPool) {
											AccountService.order(operatorData.chave, operatorData.modoHabilitado, cartPool, accountData[0].NRVENDAREST, produtos, orderCode, CDVENDEDOR, saleProdPass).then(function (orderResponse) {
												if (orderResponse[0].erro == '004') {
													TableActiveTable.findAll().then(function (activeTable) {
														ScreenService.confirmMessage("A " + activeTable[0].NMMESA + " não está aberta. Deseja reabrir a mesa e enviar o pedido?").then(
															function () {
																ApplicationContext.TableController.openTable(activeTable[0], widget.container.getWidget('openTable'), false);
															}
														);
													});
												}
												else {
													PerifericosService.print(orderResponse[0].paramsImpressora).then(function () {
														AccountCart.remove(Query.build()).then(function () {
															if (operatorData.continueOrdering) {
																CartPool.clearAll();
																operatorData.continueOrdering = false;
																OperatorRepository.save(operatorData);
																widget.groupProp = 'POSITION';
															}
															widget.dataSource.data = [];
															if (returnParam) {
																UtilitiesService.backMainScreen();
															}
															else {
																self.buildOrderCode().then(function () {
																	self.checkOrderReset(widget);
																}.bind(this));
															}
														}.bind(this));
													});
												}
											}.bind(this));
										}.bind(this));
									}.bind(this));
								} else {
									ScreenService.showMessage("Erro na transmissão do pedido. <br>Tente novamente.", "error");
									WindowService.openWindow('MENU_SCREEN');
								}
							}.bind(this));
						}.bind(this), function (err) {
							ScreenService.showMessage(err);
						});
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	}, 1000, true);


	// Função responsável por verificar parametrização (IDSENHACUP) de senhas de produção nos pedidos.
	// S - Sequencial (Diário) ->Sequencial por filial zerado diariamente. / D - Digitada  -> Abre uma tela para operador digitar senha/pager.
	// A - Aleatória (5 dígitos) - Aleatório com cinco digitos. / L - Aleatória (3 dígitos) - Aleatório com três digitos.
	this.verificaSenhaProd = function (widget, returnParam) {
		OperatorRepository.findOne().then(function (operatorData) {
			AccountCart.findAll().then(function (accountCart) {
				modoHabilitado = operatorData.modoHabilitado;
				if (operatorData.IDSENHACUP === 'D') {
					accountCart = self.recoverSubPromos(accountCart);
					if (modoHabilitado === 'B' && Util.isArray(accountCart) && Util.isEmptyOrBlank(accountCart)) {
						ScreenService.showMessage('Não há produtos no carrinho.');
					}
					else {
						ScreenService.openPopup(widget.container.getWidget('setSaleNumber')).then(function () {
							self.handleShowProdPass(widget.container.getWidget('setSaleNumber'), returnParam, modoHabilitado);
						}.bind(this));
					}
				} else if (modoHabilitado !== 'B') {
					this.order(widget, returnParam, saleProdPass = null);
				} else {
					this.openPayment(true);
				}
			}.bind(this));
		}.bind(this));
	};

	this.handleShowProdPass = function (widgetSaleNumber, returnParam, modoHabilitado) {
		if (modoHabilitado !== 'B') {
			widgetSaleNumber.getAction('confirm').label = (returnParam === true) ? 'Concluir' : 'Transmitir';
			widgetSaleNumber.getField('prodSaleNumber').dataSource.data = [{ 'value': returnParam }];
		} else {
			widgetSaleNumber.getAction('confirm').label = 'Receber';
		}

		widgetSaleNumber.getField('prodSaleNumber').clearValue();
	};

	this.applySalePass = function(widget){
		OperatorRepository.findOne().then(function (operatorData) {
			field = widget.container.getWidget('setSaleNumber').getField('prodSaleNumber');
			if (!_.isEmpty(field.value())){
				saleProdPass = field.value();
				if (operatorData.modoHabilitado !== 'B') {
					returnParam = field.dataSource.data[0].value;
					this.order(widget, returnParam, saleProdPass);
				} else {
					ProdSenhaPed.remove(Query.build()).then(function () {
						ProdSenhaPed.save(saleProdPass).then(function () {
							this.openPayment(true);
						}.bind(this));
					}.bind(this));
				}
			} else {
				ScreenService.showMessage('Digite o número da Senha/Pager do pedido para continuar.');
			}
		}.bind(this));
	};

	this.returnRepository = function (operatorData) {
		return AccountCart.findAll().then(function (accountCart) {
			if (operatorData.modoHabilitado !== 'C') return accountCart;
			else {
				return CartPool.findAll().then(function (cart) {
					var products = !_.isEmpty(cart) ? cart : accountCart;
					return products;
				}.bind(this));
			}
		}.bind(this));
	};

	var isValidPrinterChoice = function (products) {
		var prodFunc = products.map(function (product) {
			return handlePrintersProductForRoom(product).then(function (printers) {
				var defer = ZHPromise.defer();

				if (printers.length > 1) {
					if (!product.NMIMPRLOJA || !product.NRSEQIMPRLOJA[0]) {
						defer.reject("Informe a impressora do produto " + product.DSBUTTON + "!");
					} else {
						defer.resolve();
					}
				} else {
					defer.resolve();
				}

				return defer.promise;
			});
		});

		return ZHPromise.all(prodFunc);
	};

	this.getOrder = function (nrmesa, nrcomanda, nrvendarest, callBack) {
		var query = Query.build()
			.where('NRMESA').equals(nrmesa)
			.where('NRCOMANDA').equals(nrcomanda)
			.where('NRVENDAREST').equals(nrvendarest);

		AccountLastOrders.findOne(query).then(function (order) {
			if (callBack) {
				callBack(order);
			}
		});
	};

	this.sortPositions = function (a, b) {
		if (a.NRLUGARMESA < b.NRLUGARMESA)
			return -1;
		if (a.NRLUGARMESA > b.NRLUGARMESA)
			return 1;
		return 0;
	};

	this.prepareCart = function (widget, stripe) {
		OperatorRepository.findOne().then(function (operatorData) {

			var totalProd = 0;

			var __prepareCart = (function () {
				var totalOrderPrice = 0;
				var orderedCart = [];
				widget.dataSource.data.reverse();
				widget.dataSource.data.forEach(function (product) {
					product.QTPRODCOMVEN = product.QTPRODCOMVEN ? product.QTPRODCOMVEN : 1;
					product.QTPRODCOMVEN = parseFloat(String(product.QTPRODCOMVEN).replace(',', '.'));
					var quantidade = product.qtty || product.QTPRODCOMVEN;

					if (operatorData.IDUTLQTDPED || product.IDPESAPROD === 'S') {
						product.DSBUTTONSHOW = quantidade.toFixed(product.IDPESAPROD === 'S' ? 3 : 2).replace('.', ',') +
							" x " + product.DSBUTTON;
					}
					// Transforma array de observação em texto para mostrar ao usuário.
					product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);

					// Trata produtos da promoção inteligente e produto combinado.
					if (!_.isEmpty(product.PRODUTOS)) {
						if (product.IDIMPPRODUTO !== '1') {
							product.TXPRODCOMVEN = "";
						}
						product.PRODUTOS.reverse();
						product.PRODUTOS.forEach(function (comboProduct) {
							// Agrupa as observações dos filhos e coloca elas como se fossem do pai.
							var obs = this.obsToText(comboProduct.CDOCORR, comboProduct.DSOCORR_CUSTOM);
							if (obs.length > 0) {
								comboProduct.TXPRODCOMVEN = obs;
								product.TXPRODCOMVEN += " " + obs;
								product.TXPRODCOMVEN = product.TXPRODCOMVEN.trimLeft(); // Lazy fix for the line above.
							}
							else {
								comboProduct.TXPRODCOMVEN = "";
							}
							// Marca se o produto tem atraso.
							if (comboProduct.ATRASOPROD === 'Y') {
								product.ATRASOPROD = 'Y';
							}

							if (comboProduct.TOGO === 'Y') {
								product.TOGO = 'Y';
							}
							comboProduct.STRPRICE = (comboProduct.TOTPRICE).toFixed(2).replace('.', ',');
						}.bind(this));

						product.PRECO = UtilitiesService.formatFloat(product.PRITOTITEM);
					}

					totalOrderPrice += quantidade * product.PRITOTITEM;
					product.PRECO = parseFloat(product.PRITOTITEM).toFixed(2).replace('.', ',');
					// Coloca a quantidade na frente do preço.
					if (!(operatorData.modoHabilitado === 'O' || (product.QTPRODCOMVEN !== 1))) {
						product.POSITION_TO_SHOW = parseInt(product.POSITION.split(" ")[1]);
					}

					// Marca se o produto tem atraso.
					if (product.ATRASOPROD === 'Y') {
						product.holdText = 'SEGURA';
					} else {
						product.holdText = '';
					}

					if (product.TOGO === 'Y') {
						product.toGoText = 'PARA VIAGEM';
					} else {
						product.toGoText = '';
					}

					// Insere os produtos num array para que fiquem ordenados.
					orderedCart.splice(0, 0, product);
				}.bind(this));
				templateManager.updateTemplate();

				// STRIPE.
				this.getAccountData(function (accountData) {
					if (!_.isEmpty(accountData)) {
						if (accountData[0].CDCONSUMIDOR && _.get(accountData[0], 'DETALHES.BALANCE')) {
							// Coloca o valor do saldo no stripe.
							stripe.fields[2].label = UtilitiesService.toCurrency(parseFloat(accountData[0].DETALHES.BALANCE));
							stripe.fields[1].isVisible = true;
							stripe.fields[2].isVisible = true;
						}
						else {
							// Se não tiver consumidor associado à mesa/comanda, não mostra o saldo e esconde os labels.
							stripe.fields[1].isVisible = false;
							stripe.fields[2].isVisible = false;
						}
					} else {
						stripe.fields[1].isVisible = false;
						stripe.fields[2].isVisible = false;
					}
					// Coloca o valor total no stripe.
					stripe.fields[4].label = UtilitiesService.toCurrency(totalOrderPrice);
				}.bind(this));
			}).bind(this);

			if (allObservations.length === 0) this.updateObservationsInner(__prepareCart);
			else __prepareCart();

		}.bind(this));
	};

	this.obsToText = function (observations, custom) {
		var obss = [];
		if (observations) {
			obss = observations.map(function (observation) {
				return allObservations.filter(function (obs) {
					return obs.CDOCORR === observation;
				})[0].DSOCORR;
			}) || [];
		}
		if (custom) {
			obss.push(custom);
		}

		if (obss.length > 0)
			return obss.join("; ") + ";";
		else
			return obss.join("; ");
	};

	this.calcProductValue = function (product) {
		var comboProdValue = 0;
		var comboProdSubsidy = 0;
		var prodValue = 0;
		var prodSubsidy = 0;
		var qntProd = 0;
		var obsWithValue = Array();
		var currentObsWithValue = Array();

        try {
            if (_.isEmpty(product.IDTIPCOBRA)){
                // Promoção Inteligente.
                product.PRODUTOS.forEach(function(comboProduct){
                    comboProduct.TOTPRICE = comboProduct.PRICE;

                    currentObsWithValue = self.calcObsValue(comboProduct);
                    if (!_.isEmpty(currentObsWithValue)){
                        comboProduct.TOTPRICE += _.sum(currentObsWithValue);
                        obsWithValue = obsWithValue.concat(currentObsWithValue);
                    }

                    var total = parseFloat((comboProduct.QTPRODCOMVEN * (comboProduct.PRECO + comboProduct.VRPRECITEMCL + comboProduct.VRACRITVEND - comboProduct.VRDESITVEND)).toFixed(2));
                    if (total < 0.01){
                        throw comboProduct;
                    }

                    comboProdValue += total;
                    comboProdSubsidy += parseFloat(comboProduct.VRPRECITEMCL);
                });

                if (product.IDIMPPRODUTO === '1') {
                    prodValue = parseFloat((product.PRITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2));
                    prodSubsidy = product.VRPRECITEMCL;
                    qntProd = 1;
                } else {
                    prodValue = comboProdValue;
                    prodSubsidy = comboProdSubsidy;
                    qntProd = product.PRODUTOS.length;
                }
            }
            else {
                // Produto Combinado.
                var highestPrice = (_.maxBy(product.PRODUTOS, 'PRICE')).PRICE || 0;

                // Quantity adjust.
                var tamanho = product.PRODUTOS.length;
                var specialQuant = 0;
                var quantity = UtilitiesService.floatFormat(1 / tamanho);
                if (quantity * tamanho != 1){
                    specialQuant = quantity + UtilitiesService.floatFormat(1 - quantity * tamanho);
                }

                product.PRODUTOS.forEach(function(comboProduct){
                    comboProduct.VRPRECITEMCL = 0;

                    // Quantity set.
                    if (specialQuant > 0){
                        comboProduct.QTPRODCOMVEN = specialQuant;
                        specialQuant = 0;
                    }
                    else {
                        comboProduct.QTPRODCOMVEN = quantity;
                    }

                    // Price set.
                    if (product.IDTIPCOBRA === 'C'){
                        comboProduct.PRICE = highestPrice;
                        comboProduct.PRECO = highestPrice.toFixed(3);
                        comboProduct.STRPICE = highestPrice.toFixed(2).replace('.', ',');
                    }
                    comboProduct.TOTPRICE = UtilitiesService.floatFormat(comboProduct.PRICE * comboProduct.QTPRODCOMVEN);
                    if (comboProduct.TOTPRICE < 0.01){
                        throw comboProduct;
                    }
                    comboProdValue += comboProduct.TOTPRICE;

                    // Additionals.
                    currentObsWithValue = self.calcObsValue(comboProduct);
                    if (!_.isEmpty(currentObsWithValue)){
                        comboProduct.TOTPRICE += _.sum(currentObsWithValue);
                        obsWithValue = obsWithValue.concat(currentObsWithValue);
                    }
                });

                prodValue = parseFloat(comboProdValue.toFixed(2));
                qntProd = product.PRODUTOS.length;
            }
        }
        catch (errProduct){
            return errProduct;
        }

		currentObsWithValue = self.calcObsValue(product);
		obsWithValue = _.isEmpty(currentObsWithValue) ? obsWithValue : obsWithValue.concat(currentObsWithValue);

		product.EXTRAS = _.sum(obsWithValue);
		product.PRITOTITEM = parseFloat((prodValue + product.EXTRAS).toFixed(2));
		product.VRPRECITEMCL = parseFloat(prodSubsidy.toFixed(2));
		product.REALSUBSIDY = 0;
		product.numeroProdutos = qntProd + obsWithValue.length;
        return null;
	};

	this.calcObsValue = function (product) {
		var obsWithValue = [];
		// seleciona as observações selecionadas que são cobradas
		var obsSelect = _.filter(this.getObservations(product.OBSERVATIONS), function (obsProd) {
			return _.includes(product.CDOCORR, obsProd.CDOCORR) && obsProd.IDCONTROLAOBS === 'A';
		});

		obsSelect.forEach(function (obs) {
			obsWithValue.push(parseFloat(obs.VRPRECITEM) + parseFloat(obs.VRPRECITEMCL));
		});

		return obsWithValue;
	};

	this.showFunctions = function (widgetToShow) {
		ScreenService.closePopup();
		ScreenService.openPopup(widgetToShow);
	};

	this.openPayment = function (allPositions) {
		var position = Array();
		WaiterNamedPositionsState.mustUnselect = false;
		OperatorRepository.findOne().then(function (params) {
			if (params.modoHabilitado !== 'B') {
				self.getAccountData(function (accountData) {
					accountData = accountData[0];
					AccountGetAccountDetails.findOne().then(function(accountDetails) {
						var validateAndPerformOpenPayment = function(positions) {
							AccountService.getPayments(accountData).then(function(payments){
								var consumer = self.getConsumerForPositions(positions, params.modoHabilitado);
								position = consumer.position.sort();

								if(parseInt(accountDetails.NRPESMESAVEN) === position.length)
									allPositions = true;

								if(!_.isEmpty(payments)) {
									var permission = self.validatePositionsPayment(position, payments, allPositions);

									if(!permission.continuePayment) {
										var firstPart = position.length > 1 ? "estas posições. " : "esta posição. ";
										var secondPart = '';

										if(permission.positionsInPayment[0] === 0 && !allPositions) {
											secondPart = 'Todas as posições estão';
										} else {
											secondPart = permission.positionsInPayment.length > 1;
											var positionsInPayment = _.join(permission.positionsInPayment, ', ');
											secondPart = secondPart ? "As posições " + positionsInPayment + " estão" :
												"A posição " + positionsInPayment + " está";
										}

										ScreenService.showMessage("Impossível receber " + firstPart + secondPart + " em recebimento.");
										return;
									} else {
										payments = _.isEmpty(permission.positionsInPayment) ? Array() : _.uniqBy(payments, 'CDNSUTEFMOB');
									}
								}

								if (accountDetails.length === 0 || accountDetails.vlrtotal <= 0) {
									ScreenService.showMessage('A conta já foi paga.');
									return;
								}
								if (!consumer.unique) {
									ScreenService.showMessage('Não é possível realizar o pagamento simultaneamente para posições com cliente/consumidor diferentes.', 'alert');
									return;
								}
								if (params.IDCOLETOR === 'C' && position.length > 1) {
									ScreenService.showMessage('Não é possível realizar o adiantamento para mais de uma posição simultaneamente.', 'alert');
									return;
								}
							    accountData.CREDITOPESSOAL = false;
							    if (!_.isNull(consumer.CDCLIENTE)) {
									accountData.CDCLIENTE = consumer.CDCLIENTE;
									accountData.NMRAZSOCCLIE = consumer.NMRAZSOCCLIE;
									accountData.CDCONSUMIDOR = consumer.CDCONSUMIDOR;
									accountData.NMCONSUMIDOR = consumer.NMCONSUMIDOR;
								}

								if(allPositions)
									position = Array();

								PaymentService.initializePayment(accountData, params, accountDetails, [], position, '', payments, null).then(function() {
									WindowService.openWindow('PAYMENT_TYPES_SCREEN');
								});
							});
						};

						if (allPositions) {
							AccountGetAccountItems.findAll().then(function (accountItems) {
								if (!_.isUndefined(accountItems[0]) && _.isArray(accountItems[0]))
									accountItems = accountItems[0];

								var positionFromItem = function (item) {
									return item.POS;
								};
								ParamsParameterRepository.findOne().then(function (params) {
									var positionsWithItems = params.IDCOUVERART === 'S' ? Array() : _.uniq(accountItems.map(positionFromItem));
									validateAndPerformOpenPayment(positionsWithItems);
								}.bind(this));
							}.bind(this));
						} else {
							var selectedPositions = accountDetails.posicao.map(function (p) { return parseInt(p); });
							validateAndPerformOpenPayment(selectedPositions);
						}
					});
				});
			} else {
				CarrinhoDesistencia.findAll().then(function (carrinhoDesistencia) {
					ProdSenhaPed.findOne().then(function (prodSenhaPed) {
						AccountCart.findAll().then(function (accountCart) {

							accountCart = self.recoverSubPromos(accountCart);

							if (Util.isArray(accountCart) && Util.isEmptyOrBlank(accountCart)) {
								ScreenService.showMessage('Não há produtos no carrinho.');
							} else {
								var vlrtotal = 0;
								var totalSubsidy = 0;
								var numeroProdutos = 0;
								var nomeProduto = '';
								var produtosBloqueados = '';
								var contador = 0;

								var promises = [];

								promises.push(AccountService.verificaProdutosBloqueados(accountCart));
								promises.push(AccountService.calculaDescontoSubgrupo(accountCart));

								Promise.all(promises).then(function (result) {
									var produtosBloqueados = result[0];
									if (!_.isEmpty(produtosBloqueados)) {
										produtosBloqueados.forEach(function (produto) {
											nomeProduto = produto.NMPRODUTO;
											produtosBloqueados += produtosBloqueados == '' ? nomeProduto : ', ' + nomeProduto;
											contador += 1;
										});

										if (contador > 1) {
											ScreenService.showMessage('Produtos ' + produtosBloqueados + ' bloqueados.');
										}
										else {
											ScreenService.showMessage('Produto ' + produtosBloqueados + ' bloqueado.');
										}
									} else {
										accountCart = result[1];
										accountCart.forEach(function (product) {
											var productValue = product.PRITOTITEM * product.QTPRODCOMVEN;
											if (productValue != parseFloat(productValue.toFixed(2))) {
												if (!_.isEmpty(product.IDPESAPROD) && product.IDPESAPROD === 'S') {
													productValue = Math.trunc(productValue * 100) / 100;
												} else {
													productValue = parseFloat(productValue.toFixed(2));
												}
											}
											vlrtotal += productValue;
											totalSubsidy += Math.trunc(product.VRPRECITEMCL * product.QTPRODCOMVEN * 100) / 100;
											numeroProdutos += product.numeroProdutos;
										});

										var accountData = {
											"CREDITOPESSOAL": false
										};

										var accountDetails = {
											'vlrtotal': vlrtotal,
											'totalSubsidy': totalSubsidy,
											'realSubsidy': 0,
											'vlrservico': 0,
											'vlrcouvert': 0,
											'vlrdesconto': 0,
											'fidelityDiscount': 0,
											'fidelityValue': 0,
											'vlrprodutos': vlrtotal,
											'numeroProdutos': numeroProdutos
										};

										PaymentService.initializePayment(accountData, params, accountDetails, accountCart, position, carrinhoDesistencia, null, prodSenhaPed).then(function () {
											WindowService.openWindow('PAYMENT_TYPES_SCREEN');
										});
									}
								});
							}
						});
					});
				});
			}
		});
	};

	this.validatePositionsPayment = function(position, payments, allPositions) {
		var payingPositions = Array();
		var groupedPayments = _.map(_.groupBy(payments, 'CDNSUTEFMOB'), function(value, key) { return value;});
		groupedPayments.forEach(function(groupedPayment){
			groupedPayment = _.map(groupedPayments[0], function(groupedPaymentAux){
			   return parseInt(groupedPaymentAux.NRLUGARMESA);
			});

			payingPositions.push(_.uniq(groupedPayment));
		});

		var permissionPayPosition = false;
		var positionsInPayment = Array();

		if(payingPositions[0] == 0) {
			permissionPayPosition = allPositions;
			positionsInPayment = payingPositions[0];
		} else {
			payingPositions.forEach(function(payingPosition) {
				if(_.isEqual(payingPosition, position) || _.isEmpty(_.intersection(payingPosition, position)))
					permissionPayPosition = true;
				else
					permissionPayPosition = false;

				if(!_.isEmpty(_.intersection(position, payingPosition)))
					positionsInPayment = payingPosition;
			}.bind(this));
		}

		return {
			"continuePayment": permissionPayPosition,
			"positionsInPayment": positionsInPayment
		};
	};

    this.recoverSubPromos = function(cartData){
        var products = [];
        for (var x in cartData){
            if (cartData[x].PRODUTOS.length > 0){
                for (var i in cartData[x].PRODUTOS){
                    if (cartData[x].PRODUTOS[i].PRODUTOS.length > 0){
                        var subProduct = cartData[x].PRODUTOS.splice(i, 1);

                        for (var f in subProduct[0].PRODUTOS){
                            var obs = this.obsToText(subProduct[0].PRODUTOS[f].CDOCORR, subProduct[0].DSOCORR_CUSTOM);
                            if (obs.length > 0) {
                                subProduct[0].PRODUTOS[f].TXPRODCOMVEN = obs;
                                subProduct[0].PRODUTOS[f].TXPRODCOMVEN += " " + obs;
                                subProduct[0].PRODUTOS[f].TXPRODCOMVEN = subProduct[0].PRODUTOS[f].TXPRODCOMVEN.trimLeft();
                            }
                            else subProduct[0].PRODUTOS[f].TXPRODCOMVEN = "";
                        }

						subProduct[0].QTPRODCOMVEN = cartData[x].QTPRODCOMVEN;
						subProduct[0].NRCOMANDA = cartData[x].NRCOMANDA;
						subProduct[0].NRVENDAREST = cartData[x].NRVENDAREST;
						subProduct[0].NRSEQIMPRLOJA = cartData[x].NRSEQIMPRLOJA;
						subProduct[0].IDTIPOCOMPPROD = '3';
						products.push(subProduct[0]);

						cartData[x].PRITOTITEM -= subProduct[0].PRITOTITEM;
						cartData[x].numeroProdutos--;
					}
				}
			}
			products.push(cartData[x]);
		}

		return products;
	};

	this.handleConsumerPositionsOnPayment = function (getPositionFromData, accountDetails) {
		var fieldPosition = templateManager.container.getWidget('accountDetails').getField("positionsField");
		var CDCLIENTE = null;
		var NMRAZSOCCLIE = null;
		var CDCONSUMIDOR = null;
		var NMCONSUMIDOR = null;

		// seleciona pagamento para posição selecionada
		var selectedPositions = getPositionFromData ? accountDetails.posicao.map(function (p) { return parseInt(p); }) :
			fieldPosition.position.map(function (p) { return ++p; });

		if (!_.isEmpty(selectedPositions)) {
			var consumerPositionsData = fieldPosition.dataSource.data[0];

			if (!_.isEmpty(consumerPositionsData.clientMapping)) {
				var allPositions = _.keys(consumerPositionsData.clientMapping);
				var selectedPositionsIndex = _.filter(selectedPositions, function (position) {
					return _.includes(allPositions, position.toString());
				}.bind(this));

				if (selectedPositions.length == selectedPositionsIndex.length) {
					CDCLIENTE = consumerPositionsData.clientMapping[selectedPositions[0]].CDCLIENTE;
					NMRAZSOCCLIE = consumerPositionsData.clientMapping[selectedPositions[0]].NMRAZSOCCLIE;
					if (consumerPositionsData.consumerMapping[selectedPositions[0]]) {
						CDCONSUMIDOR = consumerPositionsData.consumerMapping[selectedPositions[0]].CDCONSUMIDOR;
						NMCONSUMIDOR = consumerPositionsData.consumerMapping[selectedPositions[0]].NMCONSUMIDOR;
					}

					for (var i = 1; i < selectedPositions.length; i++) {
						if (consumerPositionsData.clientMapping[selectedPositions[1]].CDCLIENTE != CDCLIENTE ||
							(consumerPositionsData.consumerMapping[selectedPositions[1]] &&
								consumerPositionsData.consumerMapping[selectedPositions[1]].CDCONSUMIDOR != CDCONSUMIDOR)) {
							CDCLIENTE = null;
							NMRAZSOCCLIE = null;
							CDCONSUMIDOR = null;
							NMCONSUMIDOR = null;
							i = selectedPositions.length;
						}
					}
				}
			}
		}

		return {
			'position': selectedPositions,
			'CDCLIENTE': CDCLIENTE,
			'NMRAZSOCCLIE': NMRAZSOCCLIE,
			'CDCONSUMIDOR': CDCONSUMIDOR,
			'NMCONSUMIDOR': NMCONSUMIDOR
		};
	};

	this.getConsumerForPositions = function (selectedPositions, modoHabilitado) {
		var consumer = {
			position: selectedPositions,
			unique: true,
			CDCLIENTE: null,
			NMRAZSOCCLIE: null,
			CDCONSUMIDOR: null,
			NMCONSUMIDOR: null
		};
		if (modoHabilitado === 'M') {
			var fieldPosition = templateManager.container.getWidget('accountDetails').getField("positionsField");
			var consumerPositionsData = fieldPosition.dataSource.data[0];
			var clientMapping = (consumerPositionsData.clientMapping && !_.isEmpty(consumerPositionsData.clientMapping)) ? consumerPositionsData.clientMapping : [];
			var consumerMapping = (consumerPositionsData.consumerMapping && !_.isEmpty(consumerPositionsData.consumerMapping)) ? consumerPositionsData.consumerMapping : [];
			var validateUniqueValueForKey = function (key, mapping, position) {
				var valueForPosition = mapping[position] ? mapping[position][key] : null;
				return consumer[key] === valueForPosition;
			};
			for (var i = 0; i < selectedPositions.length; i++) {
				var selectedPosition = selectedPositions[i];
				if (i == 0) {
					if (clientMapping[selectedPosition]) {
						consumer.CDCLIENTE = clientMapping[selectedPosition].CDCLIENTE;
						consumer.NMRAZSOCCLIE = clientMapping[selectedPosition].NMRAZSOCCLIE;
					}
					if (consumerMapping[selectedPosition]) {
						consumer.CDCONSUMIDOR = consumerMapping[selectedPosition].CDCONSUMIDOR;
						consumer.NMCONSUMIDOR = consumerMapping[selectedPosition].NMCONSUMIDOR;
					}
				} else {
					var clientIsUnique = validateUniqueValueForKey('CDCLIENTE', clientMapping, selectedPosition);
					var consumerIsUnique = validateUniqueValueForKey('CDCONSUMIDOR', consumerMapping, selectedPosition);
					if (!clientIsUnique || !consumerIsUnique) {
						consumer.unique = false;
						break;
					}
				}
			}
		}
		return consumer;
	};

	this.backPayment = function () {
		OperatorRepository.findOne().then(function (operatorData) {
			var message = '';
			if (operatorData.IDCOLETOR !== 'C') {
				message = 'Pagamento não finalizado. Deseja continuar?';
			}
			else {
				message = 'Deseja sair do adiantamento?';
			}

			ScreenService.confirmMessage(
				message,
				'question',
				function () {
					self.getAccountData(function (accountData) {
						TableService.changeTableStatus(operatorData.chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, 'S').then(function (response) {
							UtilitiesService.backMainScreen();
						}.bind(this));
					}.bind(this));
				}.bind(this),
				function () { }
			);
		}.bind(this));
	};

	var isSmartPromo = function (product) {
		return product.IDTIPOCOMPPROD == '3';
	};

	var buildCartItem = function (product, position, refilSet) {
		return self.getAccountData(function (accountData) {
			return self.getOrderCodeProductID().then(function (id) {
				var time = new Date();
				var dscomanda = '';

				if (!_.isEmpty(accountData)) {
					dscomanda = accountData[0].LABELDSCOMANDA;
				}

				var cartItem = {
					ID: id,
					UNIQUEID: id,
					GRUPO: product.NMGRUPO,
					CDPRODUTO: product.CDPRODUTO,
					DSBUTTON: product.DSBUTTON,
					DSBUTTONSHOW: product.DSBUTTON,
					POSITION: "posição " + position,
					POS: position,
					PRECO: product.PRECO,
					PRITEM: product.PRITEM,
					PRITOTITEM: parseFloat((product.PRITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2)),
					VRPRECITEMCL: product.VRPRECITEMCL,
					REALSUBSIDY: 0,
					VRDESITVEND: product.VRDESITVEND,
					VRACRITVEND: product.VRACRITVEND,
					CDOCORR: [],
					IDIMPPRODUTO: product.IDIMPPRODUTO,
					IDTIPOCOMPPROD: product.IDTIPOCOMPPROD,
					IDTIPCOBRA: product.IDTIPCOBRA,
					IDPESAPROD: product.IDPESAPROD,
					OBSERVATIONS: product.OBSERVATIONS,
					IMPRESSORAS: product.IMPRESSORAS,
					ATRASOPROD: "N",
					TOGO: "N",
					holdText: '',
					toGoText: '',
					PRODUTOS: Array(),
					refilSet: refilSet,
					NRQTDMINOBS: product.NRQTDMINOBS,
					NRCOMANDA: _.get(accountData, '[0].NRCOMANDA') || null,
					NRVENDAREST: _.get(accountData, '[0].NRVENDAREST') || null,
					DSCOMANDA: dscomanda,
					numeroProdutos: 1,
					AGRUPAMENTO: '',
					IDENTIFYKEY: time.getTime(),
                    QTPRODCOMVEN: 1
				};
				if (refilSet) {
					cartItem.PRECO = '0,00';
					cartItem.PRITEM = 0;
					cartItem.PRITOTITEM = 0;
					cartItem.VRACRITVEND = 0;
					cartItem.VRDESITVEND = 0;
					cartItem.VRPRECITEMCL = 0;
				}
				return cartItem;
			});
		});
	};

	this.getOrderCodeProductID = function () {
		return AccountCart.findAll().then(function (cartItems) {
			var nextID = 0;
			cartItems.forEach(function (item) {
				if (item.ID > nextID) {
					nextID = item.ID;
				}
			});
			return nextID + 1;
		});
	};

	var restartDataSourceWidget = function (widget) {
		if (widget.dataSource.data && widget.dataSource.data.length > 0) {
			delete widget.dataSource.data;
		}
		widget.newRow();
		widget.moveToFirst();
	};

	var prepareProductWidget = function (productWidget, cartItem) {

		return handlePrintersProductForRoom(cartItem).then(function (printers) {
			var data = {
				product: cartItem.DSBUTTON,
				position: cartItem.POSITION,
				CDPRODUTO: cartItem.CDPRODUTO,
				CDOCORR: [],
				ATRASOPROD: "N",
				TOGO: "N",
				holdText: '',
				toGoText: '',
				DSOCORR_CUSTOM: '',
				ID: cartItem.ID,
				IDPESAPROD: cartItem.IDPESAPROD,
				NRSEQIMPRLOJA: [],
				NMIMPRLOJA: ""
			};

			var printersField = productWidget.getField('NRSEQIMPRLOJA');
			printersField.dataSource.data = printers;
			printersField.isVisible = printers.length > 1;

			if (printers.length > 0) {
				data.NRSEQIMPRLOJA.push(printers[0].NRSEQIMPRLOJA);
				if (printers.length > 1) {
					data.NMIMPRLOJA = getPrinterName(printers[0].NRSEQIMPRLOJA, printers);
				}
			}

			productWidget.setCurrentRow(data);
			productWidget.container.restoreDefaultMode();

			return data;
		});
	};

	var handlePrintersProductForRoom = function (product) {
		var promiseResult = ZHPromise.when([]);
		if (product.IMPRESSORAS && product.IMPRESSORAS.length > 0) {
			promiseResult = TableActiveTable.findOne().then(function (activeTable) {
				var printersRoom = product.IMPRESSORAS.filter(function (param) {
					if (activeTable) {
						return param.CDAMBIENTE === activeTable.CDSALA;
					}
					else {
						return false;
					}
				});
				return printersRoom;
			});
		}
		return promiseResult;
	};

	var isUsingPositions = function (operatorData) {
		return operatorData.IDLUGARMESA === Param.YES;
	};

	var isTableMode = function (operatorData) {
		return operatorData.modoHabilitado === Mode.TABLE;
	};

	var isBillMode = function (operatorData) {
		return operatorData.modoHabilitado === Mode.BILL;
	};

	var isBalconyMode = function (operatorData) {
		return operatorData.modoHabilitado === Mode.BALCONY;
	};

	var updateWidgetLabel = function (operatorData, cartItem, productWidget) {
		var labelText = '';
		var article = 'a';

		if (isTableMode(operatorData)) {
			if (isUsingPositions(operatorData)) {
				labelText = cartItem.POSITION;
			} else {
				labelText = 'mesa';
			}
		} else if (isBillMode(operatorData)) {
			labelText = 'comanda';
		} else if (isBalconyMode(operatorData)) {
			labelText = 'carrinho';
			article = 'o';
		}

		productWidget.label = '<span class="font-bold">' + cartItem.DSBUTTON + '</span> para ' + article + ' <span class="font-bold">' + labelText + '</span>';
	};

	var updateFieldObservationsDataSource = function (observationField, product) {
		observationField.dataSource.data = this.getObservations(product.OBSERVATIONS);
	}.bind(this);

    this.addToCart = function (productWidget, product, position, actionQtCart, innerCart, refilSet, refilBypass){
        OperatorRepository.findOne().then(function (operatorData){
            //caixa recebedor não coleta
            if (operatorData.IDCOLETOR != 'R'){
                if (product.PRITEM > 0 || product.IDIMPPRODUTO === '2'){
                    /* REFIL MECHANICS */
                    if (product.REFIL === 'S' && !refilBypass){
                        if (operatorData.modoHabilitado !== 'B'){
                        	this.getAccountData(function(accountData){
                        		if (accountData && accountData.length > 0){
	                                AccountService.checkRefil(operatorData.chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, product.CDPRODUTO, position).then(function (refilData){
	                                    if (refilData.length === 0){
	                                        this.addToCart(productWidget, product, position, actionQtCart, innerCart, false, true);
	                                    }
	                                    else {
	                                        ScreenService.confirmMessage(
	                                            'Este produto é um refil?',
	                                            'question',
	                                            function (){
	                                                this.addToCart(productWidget, product, position, actionQtCart, innerCart, true, true);
	                                            }.bind(this),
	                                            function (){
	                                                this.addToCart(productWidget, product, position, actionQtCart, innerCart, false, true);
	                                            }.bind(this)
	                                        );
	                                    }
	                                }.bind(this));
	                            }
                        	}.bind(this));
                        } else {
                            ScreenService.showMessage("Produto refil não pode ser realizado no modo balcão.", "alert");
                        }
                    } else {
                        addItemToCart(productWidget, product, position, actionQtCart, innerCart, refilSet);
                    }
                } else {
                    ScreenService.showMessage("Produto sem preço.", 'alert');
                }
            } else{
                ScreenService.showMessage("Caixa habilitado apenas para modo recebedor.");
            }
        }.bind(this));
    };

    var addItemToCart = function(productWidget, product, position, actionQtCart, innerCart, refilSet){
        if (product.IDPRODBLOQ === 'N'){
            AccountCart.findAll().then(function(cart){
                actionQtCart.hint = cart.length+1;
                innerCart.hint = cart.length+1;
                buildCartItem(product, position, refilSet).then(function (cartItem){
                    restartDataSourceWidget(productWidget);
                    prepareProductWidget(productWidget, cartItem).then(function (dataSource){
                        productWidget.dataSource.data[0].IDPESAPROD = dataSource.IDPESAPROD;
                        cartItem.IDPESAPROD    = dataSource.IDPESAPROD;
                        cartItem.NRSEQIMPRLOJA = dataSource.NRSEQIMPRLOJA;
                        cartItem.NMIMPRLOJA    = dataSource.NMIMPRLOJA;
                        if (dataSource.IDPESAPROD === 'S') cartItem.QTPRODCOMVEN = null;
                        else cartItem.QTPRODCOMVEN = 1;
                        AccountCart.save(cartItem).then(function(){
                            OperatorRepository.findOneInMemory().then(function (operatorData){
                                updateWidgetLabel(operatorData, cartItem, productWidget);
                            });
                            updateFieldObservationsDataSource(productWidget.getField('CDOCORR'), product);
                            productWidget.currentRow.QTPRODCOMVEN = "1";
                            openProductPopUp(productWidget);
                        });
                    });
                });
            });
        } else {
            ScreenService.showMessage("Produto bloqueado.");
        }
    };

    var openProductPopUp = function(productWidget){
        OperatorRepository.findOneInMemory().then(function (operatorData){
            var parentWidget = productWidget.container.getWidget('menu') || productWidget.container.getWidget('smartPromo') || productWidget.container.getWidget('subPromo');

            productWidget.getField('ATRASOPROD').isVisible = operatorData.NRATRAPADRAO > 0;
            productWidget.getField('TOGO').isVisible = operatorData.IDCTRLPEDVIAGEM === 'S';
            if (parentWidget.container.name !== 'menu' || operatorData.IDUTLQTDPED === 'S'){
                productWidget.getField('QTPRODCOMVEN').isVisible = true;
                productWidget.getField('QTPRODCOMVEN').spin = true;
                productWidget.getField('QTPRODCOMVEN').label = "Quantidade (un)";
                productWidget.getField('QTPRODCOMVEN').blockInputEdit = true;
            }
            else {
                productWidget.getField('QTPRODCOMVEN').isVisible = false;
            }
            if (productWidget.currentRow.IDPESAPROD === 'S'){
                productWidget.getField('QTPRODCOMVEN').isVisible = true;
                productWidget.getField('QTPRODCOMVEN').spin = false;
                productWidget.getField('QTPRODCOMVEN').label = "Quantidade (kg)";
                productWidget.currentRow.QTPRODCOMVEN = "";
                productWidget.getField('QTPRODCOMVEN').blockInputEdit = false;
            }
			ScreenService.openPopup(productWidget);

			parentWidget.activate(); //To show the correct action on the button bar.
			parentWidget.container.restoreDefaultMode();
		});
	};

    /* - SMART PROMO MECHANICS - */

    this.defineRepository = function(widget){
        if (widget.container.name === "smartPromo") return SmartPromoTray;
        else return SubPromoTray;
    };

    this.definePreselection = function(products){
        var newTray = [];
        var preselection = _.filter(products, function (product){
            return product.IDPRODPRESELEC === "S" && product.IDPESAPROD === "N";
        });

        preselection.forEach(function (product){
            var currentGroupProducts = _.reduce(newTray, function(count, trayItem){
                if (trayItem.CDGRUPO === product.CDGRUPO){
                    return count + 1;
                }
                else {
                    return count;
                }
            }, 0);

            if (currentGroupProducts < product.QTPRGRUPPROMOC){
                product.quantity = 1;
                newTray.push(product);
            }
        });
        return newTray;
    };

    this.buildAllTrayItems = function(products, tray, firstCicle){
        return new Promise(function(resolve, reject) {
            if (firstCicle) {
                products = self.definePreselection(products);
            }

            if (!_.isEmpty(products)){
                var product = products.shift();
                var promoValues = self.processPromoValues(tray, product);
                self.buildTrayItem(product, promoValues).then(function (trayItem){
                    tray.push(trayItem);
                    if (products.length > 0) {
                        self.buildAllTrayItems(products, tray, false).then(function(resolvedTray){
                            resolve(resolvedTray);
                        }, reject);
                    } else {
                        resolve(tray);
                    }
                });
            }
            else {
                resolve([]);
            }
        });
    };

    this.openPromoScreen = function(product, widget, subPromo){
        self.preparePromoRepositories(product).then(function (repos){

            if (repos == null){
                // Error handling.
                return AccountCart.findAll().then(function (cart){
                    return AccountCart.remove(Query.build()).then(function (){
                        var newCart = cart.filter(function (item){
                            return item.ID !== cart[0].ID;
                        });
                    });
                });
            }

            var PromoGroups;
            var PromoProds;
            var PromoTray;
            var PromoWindow;

            if (!subPromo){
                PromoGroups = SmartPromoGroups;
                PromoProds = SmartPromoProds;
                PromoTray = SmartPromoTray;
                PromoWindow = 'PROMO_SCREEN';
            }
            else {
                PromoGroups = SubPromoGroups;
                PromoProds = SubPromoProds;
                PromoTray = SubPromoTray;
                PromoWindow = 'SUBPROMO_SCREEN';
            }

            var promises = [];

            // GROUPS.
            var PromoGroupsPromise = PromoGroups.remove(Query.build()).then(function (){
                return PromoGroups.save(repos.groups);
            });

            // PRODUCTS.
            var PromoProdsPromise = PromoProds.remove(Query.build()).then(function (){
                return PromoProds.save(repos.products);
            });

            // Clears the tray.
            var PromoTrayPromise = PromoTray.remove(Query.build());

            // Add to tray.
            var PromoTrayAddPromise = self.buildAllTrayItems(repos.products, [], true).then(function(trayItensToAdd){
                return PromoTray.save(trayItensToAdd);
            });

            promises.push(PromoGroupsPromise);
            promises.push(PromoProdsPromise);
            promises.push(PromoTrayPromise);
            promises.push(PromoTrayAddPromise);
            // Resolves all promises.
            ZHPromise.all(promises).then(function (promisesResults){
                // Opens the Smart Promo page.
                WindowService.openWindow(PromoWindow).then(function (){
                    var widgetCategories = templateManager.container.getWidget('categories');
                    widgetCategories.currentRow.DISPLAY = widget.currentRow.DSBUTTON;
                    widgetCategories.currentRow.IDTIPOCOMPPROD = product.IDTIPOCOMPPROD;
                    if (widgetCategories.dataSource.data.length > 0){
                        self.resetGroupHighlight(widgetCategories);
                    }
                }.bind(this));
            }.bind(this));
        });
    };

    this.preparePromoRepositories = function(product){
        return self.getSmartPromoInfo(product).then(function (product){
            return OperatorRepository.findOne().then(function (operatorData){

                if (!product) return; // Error handling.

                var groups = [];
                var products = [];
                /* Builds the groups. */
                for (var idGroup in product.GRUPOS){

                    var currentGroup = product.GRUPOS[idGroup];
                    var currentCategory = currentGroup.grupo;

                    var group = buildSmartGroup(currentCategory);

                    /* Inserts this group into the group array. */
                    groups.push(group);

                    /* Builds the products. */
                    for (var idProd in currentGroup.produtos){
                        var currentProduct = currentGroup.produtos[idProd];
                        var item = buildSmartProduct(currentCategory, currentProduct, operatorData.IDCOLETOR);
                        if (item == null) return null;
                        /* Inserts this product into the group product. */
                        products.push(item);
                    }
                }

                return {
                    groups: groups,
                    products: products,
                };
            });
        });
    };

    this.getSmartPromoInfo = function(product){
        return SmartPromoRepository.findAll().then(function (allProducts){
             // Gets the groups/products.
            var products = JSON.parse(allProducts[0]);
            var groups = products[product.CDPRODUTO];

            // Puts the keys back.
            var productKeys = ["CDPRODUTO", "IDIMPPRODUTO", "IDAPLICADESCPR", "IDPERVALORDES", "NMPRODUTO", "VRDESPRODPROMOC", "IDDESCACRPROMO", "VRPRECITEM", "OBSERVACOES", "IDPRODBLOQ", "IMPRESSORAS", "VRALIQCOFINS", "VRALIQPIS", "VRPEALIMPFIS", "CDIMPOSTO", "CDCSTICMS", "CDCSTPISCOF", "CDCFOPPFIS", "DSPRODVENDA", "DSADICPROD", "DSENDEIMGPROMO", "NRORDPROMOPR", "IDPRODPRESELEC", "IDOBRPRODSELEC", "NRQTDMINOBS", "CDPROTECLADO", "IDTIPOCOMPPROD", "HRINIVENPROD", "HRFIMVENPROD", "CDCLASFISC", "REFIL", "CDPRODPROMOCAO", "VRPRECITEMCL", "VRDESITVEND", "VRACRITVEND", "IDPESAPROD"];
            for (var i in groups){
                for (var p in groups[i].produtos){
                    groups[i].produtos[p] = _.zipObject(productKeys, groups[i].produtos[p]);
                }
            }

            if (product.IDTIPOCOMPPROD === 'C') {
            	return product;
            }
            else if (groups == null || Object.keys(groups).length == 0){
                AccountCart.findAll().then(function (cart){
                    AccountCart.remove(Query.build()).then(function (){
                        var newCart = cart.filter(function (item){
                            return item.ID !== cart[0].ID;
                        });
                        AccountCart.save(newCart).then(function (){
                            ScreenService.showMessage("Promoção não possui composição.", "alert");
                        });
                    });
                });
            }
            else {
                product.GRUPOS = groups;
                return product;
            }
        });
    };

    var buildSmartGroup = function(currentCategory){
        return {
            COLOR:          '#660000',
            CDGRUPO:        currentCategory.CDGRUPROMOC,
            NMGRUPO:        currentCategory.NMGRUPROMOC,
            DISPLAY:        currentCategory.NMGRUPROMOC,
            QTPRGRUPPROMOC: currentCategory.QTPRGRUPPROMOC,
            QTPRGRUPROMIN:  currentCategory.QTPRGRUPROMIN,
            CDGRUPMUTEX:    currentCategory.CDGRUPMUTEX,
            SELECTED:       false,
            DISABLED:       false
        };
    };

    var buildSmartProduct = function(currentCategory, currentProduct, idImpProduto, IDCOLETOR){
    	// Caso o produto seja pre-selecionado, temos que validar ele aqui pois a tela adiciona eles automaticamente.
    	if (currentProduct.IDPRODPRESELEC === 'S'){
    		validaProduto = self.validateProducts(currentProduct, IDCOLETOR);
            if (!_.isEmpty(validaProduto)){
                if (currentProduct.IDOBRPRODSELEC === 'S'){
                    // Se um produto obrigatório estiver inválido, a promoção não pode ser montada.
                    ScreenService.showMessage("Um ou mais produtos obrigatórios dentro da promoção não podem ser pedidos: " + validaProduto);
                    return null;
                }
                // Caso o produto não seja válido, remove a flag de pre-seleção.
                currentProduct.IDPRODPRESELEC = 'N';
            }
    	}

        return {
            COLOR:           '#000066',
            CDGRUPO:         currentCategory.CDGRUPROMOC,
            NMGRUPO:         currentCategory.NMGRUPROMOC,
            CDPRODUTO:       currentProduct.CDPRODUTO,
            IDIMPPRODUTO:    currentProduct.IDIMPPRODUTO,
            IDAPLICADESCPR:  currentProduct.IDAPLICADESCPR,
            IDPERVALORDES:   currentProduct.IDPERVALORDES,
            IDPESAPROD:      currentProduct.IDPESAPROD,
            DSBUTTON:        currentProduct.NMPRODUTO,
            VRDESPRODPROMOC: currentProduct.VRDESPRODPROMOC,
            IDDESCACRPROMO:  currentProduct.IDDESCACRPROMO,
            VRPRECITEM:      currentProduct.VRPRECITEM,
            OBSERVATIONS:    currentProduct.OBSERVACOES,
            IDPRODBLOQ:      currentProduct.IDPRODBLOQ,
            QTPRGRUPPROMOC:  currentCategory.QTPRGRUPPROMOC,
            QTPRGRUPROMIN:   currentCategory.QTPRGRUPROMIN,
            CDGRUPMUTEX:     currentCategory.CDGRUPMUTEX,
            IMPRESSORAS:     currentProduct.IMPRESSORAS,
            IDPRODPRESELEC:  currentProduct.IDPRODPRESELEC,
            IDOBRPRODSELEC:  currentProduct.IDOBRPRODSELEC,
            IDTIPOCOMPPROD:  currentProduct.IDTIPOCOMPPROD,
            HRINIVENPROD:  	 currentProduct.HRINIVENPROD,
            HRFIMVENPROD:  	 currentProduct.HRFIMVENPROD,
            CDCLASFISC:  	 currentProduct.CDCLASFISC,
            CDCFOPPFIS:  	 currentProduct.CDCFOPPFIS,
            CDCSTICMS:  	 currentProduct.CDCSTICMS,
            CDCSTPISCOF:  	 currentProduct.CDCSTPISCOF,
            VRALIQPIS:  	 currentProduct.VRALIQPIS,
            VRALIQCOFINS:  	 currentProduct.VRALIQCOFINS,
            VRDESITVEND:     currentProduct.VRDESITVEND,
            VRACRITVEND:     currentProduct.VRACRITVEND,
            VRPRECITEMCL:    currentProduct.VRPRECITEMCL,
            quantity:        0
        };
    };

    this.addToTray = function (productWidget, product){
        if (product.VRPRECITEM !== null || parseFloat(product.VRPRECITEM) > 0 || product.IDIMPPRODUTO === '1'){
            var PromoTray = self.defineRepository(productWidget);
            PromoTray.findAll().then(function (tray){

                var groupCount = self.promoGroupCount(tray, product.CDGRUPO);

                var handle = _.find(tray, function(item){
                    return item.CDPRODUTO === product.CDPRODUTO;
                });

                productWidget.getAction('clearProducts').isVisible = product.IDPESAPROD === "S";

                if (_.isEmpty(handle) || product.IDPESAPROD === "S"){
                    // Produto não existe na bandeja.
                    var promoValues = self.processPromoValues(tray, product);
                    if (groupCount < product.QTPRGRUPPROMOC){
                        trayClone = null;
                        groupCount++;
                        self.buildTrayItem(product, promoValues).then(function (trayItem){
                            PromoTray.save(trayItem).then(function (){
                                AccountCart.findAll().then(function (cart){
                                    if (productWidget.container.name === "smartPromo" && product.IDTIPOCOMPPROD == '3' && cart[0].CDPRODUTO != product.CDPRODUTO && product.IDIMPPRODUTO != '1'){
                                        self.openPromoScreen(product, productWidget.container.getWidget('products'), true);
                                    }
                                    else {
                                        product.quantity++;
                                        self.updateGroupQuantityHeader(productWidget, groupCount);
                                        if (!_.isEmpty(product.OBSERVATIONS)){
                                            // Se não tiver observações, não abre o popup.
                                            self.openPromoPopup(productWidget, product, promoValues);
                                        }
                                        else {
                                            PromoTray.findAll().then(function (newTray){
                                                self.handleMutex(productWidget.container.getWidget('categories').dataSource.data, product.CDGRUPMUTEX, product.CDGRUPO, newTray);
                                                self.advanceGroup(productWidget.container.getWidget('categories'), newTray);
                                            });
                                        }
                                    }
                                });
                            });
                        });
                    }
                    else {
                        ScreenService.showMessage("Quantidade excedida.");
                    }
                }
                else {
                    // Produto já existe na bandeja.
                    trayClone = angular.copy(tray);

                    if (productWidget.dataSource.data && productWidget.dataSource.data.length > 0) {
                        delete productWidget.dataSource.data;
                    }
                    productWidget.newRow();
                    productWidget.container.restoreDefaultMode();
                    productWidget.moveToFirst();

                    productWidget.newRow();
                    productWidget.setCurrentRow(handle);
                    productWidget.label = '<span class="font-bold">'+product.DSBUTTON+'</span> para a <span class="font-bold">bandeja</span>';

                    productWidget.getField('CDOCORR').dataSource.data = self.getObservations(product.OBSERVATIONS);
                    openProductPopUp(productWidget);
                }
            });
        }
        else ScreenService.showMessage("Produto sem preço.");
    };

    this.clearTrayProduct = function(widget){
        var PromoTray = this.defineRepository(widget);
        PromoTray.findAll().then(function (tray){
            PromoTray.remove(Query.build()).then(function (){
                var newTray = tray.filter(function (item){
                    return item.CDPRODUTO !== widget.currentRow.CDPRODUTO;
                });
                PromoTray.save(newTray).then(function (){
                    var handle = _.find(widget.container.getWidget('products').dataSource.data, function (prod){
                        return prod.CDPRODUTO === widget.currentRow.CDPRODUTO;
                    });
                    handle.quantity = 0;
                    var count = self.promoGroupCount(newTray, widget.currentRow.GDGRUPO);
                    self.updateGroupQuantityHeader(widget, count);
                    ScreenService.closePopup();
                });
            });
        });
    };

    this.promoGroupCount = function(tray, CDGRUPO){
        /* Counts the number of products are in the group. */
        var cont = 0;
        for (var i in tray){
            if (tray[i].CDGRUPO === CDGRUPO){
                if (tray[i].IDPESAPROD === "N"){
                    cont += tray[i].QTPRODCOMVEN;
                }
                else {
                    cont++;
                }
            }
        }
        return cont;
    };

    this.processPromoValues = function(tray, product){
        /* Works out the next ID. */
        var nextID = 0;
        tray.forEach(function (item) {
            if (item.ID > nextID) nextID = item.ID;
        });
        nextID++;

        /* Checks if the delay has been set or not. */
        var setDelay = 'N';
        var delayString = '';
        if (tray.length > 0 && tray[0].ATRASOPROD === 'Y'){
            setDelay = 'Y';
            delayString = 'SEGURA';
        }

        var setToGo = 'N';
        var toGoString = '';
        if (tray.length > 0 && tray[0].TOGO === 'Y'){
            setToGo = 'Y';
            toGoString = 'PARA VIAGEM';
        }

        /* Calculates the discount. */
        var price = parseFloat((product.VRPRECITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2));
        var discount = product.IDDESCACRPROMO == 'D' ? parseFloat(product.VRDESPRODPROMOC) : 0;
        var addition = product.IDDESCACRPROMO == 'A' ? parseFloat(product.VRDESPRODPROMOC) : 0;
        if (product.IDAPLICADESCPR === 'I'){ // I => Only applies discount on the first product of that type.
            for (var j in tray){
                if (tray[j].CDPRODUTO === product.CDPRODUTO){
                    // If the product is already on the tray, we remove its discount.
                    discount = 0;
                    break;
                }
            }
        }

        var strDesconto = '';
        if (discount > 0){
            if (product.IDPERVALORDES === 'P'){
                strDesconto = '-' + discount + '%';
                discount = parseInt(price*discount)/100;
            }
            else if (product.IDPERVALORDES === 'V'){
                strDesconto = '-R$' + discount;
            }
        }

        if (addition > 0 && product.IDPERVALORDES === 'P'){
            addition = parseInt(price*addition)/100;
        }

        price = parseFloat((price - discount + addition).toFixed(2));

        var strPrice = '';
        if (product.IDIMPPRODUTO !== '1')
            strPrice = '' + UtilitiesService.formatFloat(price);

        var originalDiscount = product.VRDESITVEND;
        var fullAddition = parseFloat((product.VRACRITVEND + addition).toFixed(2));
        var fullDiscount = parseFloat((product.VRDESITVEND + discount).toFixed(2));
        var realPrice = parseFloat((product.VRPRECITEM + product.VRPRECITEMCL + product.VRACRITVEND - product.VRDESITVEND).toFixed(2));

        return {
            ID: nextID,
            setDelay: setDelay,
            setToGo: setToGo,
            delayString: delayString,
            toGoString: toGoString,
            price: price,
            strPrice: strPrice,
            discount: discount,
			addition: addition,
            strDesconto: strDesconto,
            originalDiscount: originalDiscount,
            fullDiscount: fullDiscount,
            fullAddition: fullAddition,
            realPrice: realPrice
        };
    };

    this.buildTrayItem = function(product, promoValues){
        return handlePrintersProductForRoom(product).then(function (printers){
            return {
                ID: promoValues.ID,
                CDGRUPO: product.CDGRUPO,
                CDPRODUTO: product.CDPRODUTO,
                IDIMPPRODUTO: product.IDIMPPRODUTO,
                NMGRUPO: product.NMGRUPO,
                DSBUTTON: product.DSBUTTON,
                IDAPLICADESCPR: product.IDAPLICADESCPR,
                IDOBRPRODSELEC: product.IDOBRPRODSELEC,
                IDPERVALORDES: product.IDPERVALORDES,
                VRDESPRODPROMOC: promoValues.discount,
                PRECO: product.VRPRECITEM,
                STRPRICE: promoValues.strPrice,
                STRDESCONTO: promoValues.strDesconto,
                PRITEM: product.VRPRECITEM,
                VRPRECITEMCL: product.VRPRECITEMCL,
                REALSUBSIDY: 0,
                VRDESITVEND: promoValues.fullDiscount,
                VRACRITVEND: promoValues.fullAddition,
                PRICE: promoValues.price,
                TOTPRICE: promoValues.price,
                DISCOUNT: promoValues.discount,
                ADDITION: promoValues.addition,
                ATRASOPROD: promoValues.setDelay,
                holdText: promoValues.delayString,
                TOGO: promoValues.setToGo,
                toGoText: promoValues.toGoString,
                PRODUTOS: [],
                CDOCORR: [],
                DSOCORR_CUSTOM: '',
                TXPRODCOMVEN: null,
                OBSERVATIONS: product.OBSERVATIONS,
                IMPRESSORA: printers.length > 0 ? printers[0].NRSEQIMPRLOJA : null,
                NRQTDMINOBS: product.NRQTDMINOBS,
                CDGRUPMUTEX: product.CDGRUPMUTEX,
                REALPRICE: promoValues.realPrice,
                VRDESCONTO: promoValues.originalDiscount,
                IDPESAPROD: product.IDPESAPROD,
                QTPRGRUPPROMOC: product.QTPRGRUPPROMOC,
                QTPRODCOMVEN: 1
            };
        });
    };

    this.updateGroupQuantityHeader = function(widget, groupCount){
        // Changes the group name to reflect the newly added product.
        var widgetCategories = widget.container.getWidget('categories');
        var oldDisplay = "";
        for (var i in widgetCategories.dataSource.data){
            if (widgetCategories.dataSource.data[i].selected){
                oldDisplay = widgetCategories.dataSource.data[i].DISPLAY;
            }
            widgetCategories.currentRow.oldDisplay = !!oldDisplay.substring(0, oldDisplay.indexOf("(")) ? oldDisplay.substring(0, oldDisplay.indexOf("(")) : oldDisplay;
            if (widgetCategories.dataSource.data[i].selected || widgetCategories.dataSource.data[i].SELECTED){
                widgetCategories.dataSource.data[i].DISPLAY = groupCount > 0 ? widgetCategories.currentRow.oldDisplay + ' (' + groupCount + ')' : widgetCategories.currentRow.oldDisplay;
            }
        }
    };

    this.openPromoPopup = function(productWidget, product, promoValues){
        if (productWidget.dataSource.data && productWidget.dataSource.data.length > 0) {
            delete productWidget.dataSource.data;
        }
        productWidget.newRow();
        productWidget.container.restoreDefaultMode();
        productWidget.moveToFirst();

        product.QTPRODCOMVEN = 1;
        product.ID = promoValues.ID;
        product.ATRASOPROD = promoValues.setDelay;
        product.holdText = promoValues.delayString;
        product.TOGO = promoValues.setToGo;
        product.toGoText = promoValues.toGoString;
        product.CDOCORR = [];
        product.DSOCORR_CUSTOM = '';

        productWidget.newRow();
        productWidget.setCurrentRow(product);
        productWidget.label = '<span class="font-bold">'+product.DSBUTTON+'</span> para a <span class="font-bold">bandeja</span>';

        productWidget.getField('CDOCORR').dataSource.data = this.getObservations(product.OBSERVATIONS);
        openProductPopUp(productWidget);
    };

    this.initSmartPromo = function(widget){
        this.updateObservationsInner(function (){
            SmartPromoGroups.findAll().then(function (groups){
                SmartPromoTray.findAll().then(function (tray){

                    var master = {};
                    var exclusiveGroups = {};
                    var i;

                    // Counts the number of selected products in each group.
                    for (i in tray){
                        if (master[tray[i].CDGRUPO] == null) master[tray[i].CDGRUPO] = 0;

                        if (tray[i].IDPESAPROD === "S") master[tray[i].CDGRUPO]++;
                        else master[tray[i].CDGRUPO] += tray[i].QTPRODCOMVEN;

                        if (tray[i].CDGRUPMUTEX){
                            exclusiveGroups[tray[i].CDGRUPMUTEX] = tray[i].CDGRUPO;
                        }
                    }

                    // Sets the number of products on group names.
                    var selection = 0;
                    for (i in groups){
                    	groups[i].visible = true;

                        if (master[groups[i].CDGRUPO] != null){
                            groups[i].DISPLAY = groups[i].NMGRUPO + " (" + parseInt(master[groups[i].CDGRUPO]).toString() + ")";
                        }
                        else {
                            groups[i].DISPLAY = groups[i].NMGRUPO;
                        }
                        // Controls the highlighted group, to be determined later on.
                        if (widget.dataSource.data[i] && widget.dataSource.data[i].selected) selection = i;

                        if (groups[i].CDGRUPMUTEX && exclusiveGroups[groups[i].CDGRUPMUTEX] && exclusiveGroups[groups[i].CDGRUPMUTEX] !== groups[i].CDGRUPO){
                            groups[i].DISABLED = true;
                        }
                        groups[i].DISPLAY = widget.currentRow.IDTIPOCOMPPROD === 'C' ? widget.currentRow.DISPLAY : groups[i].DISPLAY;
                    }

                    widget.setCurrentRow(groups[selection]); // Determines which group will be selected.
                    groups[selection].SELECTED = true; // Controls the highlighted group, to be determined later on.
                    groups[selection].selected = true;
                    widget.dataSource.data = groups; // Sets the page's datasource with the correct groups.
                });
            });
        });
    };

    this.initSubPromo = function(widget){
        this.updateObservationsInner(function (){
            SubPromoGroups.findAll().then(function (groups){
                groups = _.map(groups, function(group){
                	group.visible = true;
                	return group;
                });
                widget.setCurrentRow(groups[0]); // Determines which group will be selected.
                groups[0].SELECTED = true; // Always highlights the first group.
                groups[0].selected = true;
                widget.dataSource.data = groups; // Sets the page's datasource with the correct groups.
            });
        });
    };

    this.backSmartPromo = function(widget){
        AccountCart.findAll().then(function (cart){
            AccountCart.remove(Query.build()).then(function (){
                var newCart = cart.filter(function (item){
                    return item.ID !== cart[0].ID;
                });

                AccountCart.save(newCart).then(function (){
                    self.resetGroupHighlight(widget);
                    WindowService.openWindow('MENU_SCREEN');
                });
            });
        });
    };

    this.backSubPromo = function(widget){
        SmartPromoTray.findAll().then(function (tray){
            SmartPromoTray.remove(Query.build()).then(function (){
                var newTray = tray.filter(function (item){
                    return item.ID !== tray[0].ID;
                });

                SmartPromoTray.save(newTray).then(function (){
                    self.resetGroupHighlight(widget);
                    WindowService.openWindow('PROMO_SCREEN');
                });
            });
        });
    };

    this.resetGroupHighlight = function(widget){
        // Ensures the first group will be highlighted next time.
        for (var i in widget.dataSource.data){
            widget.dataSource.data[i].selected = false;
        }
        widget.dataSource.data[0].selected = true;
    };

    this.undoPromoAdd = function(args){
        var PromoTray = this.defineRepository(args.owner.widget);
        if (_.isEmpty(trayClone)){
            var product = args.row;
            var widgetCategories = args.owner.widget.container.getWidget('categories');
            var handle = _.find(args.owner.widget.container.getWidget('products').dataSource.data, function (prod){
                return prod.CDPRODUTO == product.CDPRODUTO;
            });
            handle.quantity--;
            PromoTray.findAll().then(function (tray) {
                var cont = self.promoGroupCount(tray, product.CDGRUPO);
                PromoTray.remove(Query.build()).then(function () {
                    var newTray = tray.filter(function (item) {
                        return item.ID !== product.ID;
                    });
                    PromoTray.save(newTray).then(function () {
                        for (var i in widgetCategories.dataSource.data){
                            if (widgetCategories.dataSource.data[i].selected){
                                var apnd = (parseInt(cont)-1 > 0) ? ' (' + (parseInt(cont)-1).toString() + ')' : '';
                                widgetCategories.dataSource.data[i].DISPLAY = widgetCategories.dataSource.data[i].oldDisplay + apnd;
                            }
                        }
                        ScreenService.closePopup();
                    });
                });
            });
        }
        else {
            PromoTray.save(trayClone).then(function (){
                ScreenService.closePopup();
            });
        }
    };

    this.closePromoPopup = function(widget){
        var PromoTray = this.defineRepository(widget);
        self.updatePromoObservations(widget, null).then(function (){
            PromoTray.findAll().then(function (data){
                var obsReturn = self.handleObservations(data, widget);
                if (obsReturn.error){
                    ScreenService.showMessage(obsReturn.message);
                }
                else if (widget.currentRow.IDPESAPROD === 'S' && (_.isEmpty(widget.currentRow.QTPRODCOMVEN) || widget.currentRow.QTPRODCOMVEN == 0)){
                    ScreenService.showMessage("Favor inserir uma quantidade válida para o produto.");
                }
                else if (widget.currentRow.IDPESAPROD === 'S' && widget.currentRow.QTPRODCOMVEN > 999999999){
                    ScreenService.showMessage("Quantidade do produto não pode exceder o limite máximo de 999999999kg.");
                }
                else {
                    if (widget.currentRow.IDPESAPROD === "N"){
                        var groupCount = self.promoGroupCount(data, widget.currentRow.CDGRUPO);
                        self.updateGroupQuantityHeader(widget, groupCount);

                        var handle = _.find(widget.container.getWidget('products').dataSource.data, function (prod){
                            return prod.CDPRODUTO === widget.currentRow.CDPRODUTO;
                        });
                        handle.quantity = widget.currentRow.QTPRODCOMVEN;
                        if (widget.currentRow.QTPRODCOMVEN == 0){
                            PromoTray.remove(Query.build()).then(function (){
                                var newTray = data.filter(function (item) {
                                    return item.ID !== widget.currentRow.ID;
                                });
                                PromoTray.save(newTray);
                                this.handleMutex(widget.container.getWidget('categories').dataSource.data, data[0].CDGRUPMUTEX, data[0].CDGRUPO, newTray);
                            }.bind(this));
                        }
                        else {
                            this.handleMutex(widget.container.getWidget('categories').dataSource.data, widget.currentRow.CDGRUPMUTEX, widget.currentRow.CDGRUPO, data);
                            this.advanceGroup(widget.container.getWidget('categories'), data);
                        }
                        ScreenService.closePopup();
                    }
                    else {
                        ScreenService.closePopup();
                        this.handleMutex(widget.container.getWidget('categories').dataSource.data, widget.currentRow.CDGRUPMUTEX, widget.currentRow.CDGRUPO, data);
                        this.advanceGroup(widget.container.getWidget('categories'), data);
                    }
                }
            }.bind(this));
        }.bind(this));
    };

    this.handleMutex = function(groupData, mutex, currentGroup, tray){
        if (mutex != null){
            // Checks if there are any products from the current group inside the tray.
            var groupProducts = _.filter(tray, function(products){
                return products.CDGRUPO === currentGroup;
            });
            // Gets OTHER groups that have the same mutex.
            var mutexGroups = _.filter(groupData, function(group){
                return group.CDGRUPMUTEX === mutex && group.CDGRUPO !== currentGroup;
            });
            // Blocks or unblocks the group.
            _.forEach(mutexGroups, function(group){
                group.DISABLED = groupProducts.length > 0;
            });
        }
    };

    this.advanceGroup = function(groupWidget, tray){
        var count = this.promoGroupCount(tray, groupWidget.currentRow.CDGRUPO);
        var grupoAtual = groupWidget.currentRow.CDGRUPO;

        if (count == groupWidget.currentRow.QTPRGRUPPROMOC){
            var indexGrupoAtual = _.findIndex(groupWidget.dataSource.data, {'CDGRUPO': grupoAtual});

            if ((groupWidget.dataSource.data.length - 1) != indexGrupoAtual){
                groupWidget.dataSource.data = _.map(groupWidget.dataSource.data, function(a){
                    a.selected = false;
                    return a;
                });

                indexGrupoAtual = this.getNextGroupIndex(groupWidget.dataSource.data, indexGrupoAtual);

                groupWidget.dataSource.data[indexGrupoAtual].selected = true;
                groupWidget.setCurrentRow(groupWidget.dataSource.data[indexGrupoAtual]);
            }
        }
    };

    this.getNextGroupIndex = function(groups, currentIndex){
        for (var i = currentIndex; i < groups.length - 1; i++){
            if (!groups[i+1].DISABLED) return i+1;
        }
        return currentIndex;
    };

    this.confirmSmartPromo = function(widget){
        AccountCart.findAll().then(function (cart){
            SmartPromoTray.findAll().then(function (tray){
                if (validateGroupRequirements(widget.dataSource.data, tray, cart[0].IDTIPCOBRA)){
                    handlePrintersProductForRoom(cart[0]).then(function (printers){
                        cart[0].NRSEQIMPRLOJA = [];
                        if (printers.length > 0){
                            cart[0].NRSEQIMPRLOJA.push(printers[0].NRSEQIMPRLOJA);
                        }
                        self.trataCampanha(tray).then(function (tray){
                            cart[0].PRODUTOS = tray;
                            var calcResult = self.calcProductValue(cart[0]);
                            if (calcResult == null){
                                AccountCart.save(cart[0]).then(function (){
                                    if (cart[0].IDIMPPRODUTO == '1'){
                                        WindowService.openWindow('CHECK_PROMO_SCREEN');
                                    }
                                    else {
                                        self.resetGroupHighlight(widget);
                                        WindowService.openWindow('MENU_SCREEN');
                                    }
                                });
                            }
                            else {
                                ScreenService.showMessage("O valor calculado para o produto " + calcResult.DSBUTTON + " ficou abaixo de R$0,01. Verifique a parametrização.");
                            }
                        });
                    });
                }
            }.bind(this));
        }.bind(this));
    };

    this.confirmSubPromo = function(widget){
        SmartPromoTray.findAll().then(function (smartTray){
            SubPromoTray.findAll().then(function (subTray){
                if (validateGroupRequirements(widget.dataSource.data, subTray, null)){
                    smartTray[0].PRODUTOS = subTray;
                    self.calcProductValue(smartTray[0]);
                    smartTray[0].PRECO = smartTray[0].PRITOTITEM;
                    smartTray[0].PRICE = smartTray[0].PRITOTITEM;
                    smartTray[0].PRITEM = smartTray[0].PRITOTITEM;
                    smartTray[0].TOTPRICE = smartTray[0].PRITOTITEM;
                    smartTray[0].TXPRODCOMVEN = self.obsToText(smartTray[0].CDOCORR, smartTray[0].DSOCORR_CUSTOM);
                    SmartPromoTray.save(smartTray[0]).then(function (){
                        self.resetGroupHighlight(widget);
                        WindowService.openWindow('PROMO_SCREEN');
                    });
                }
            });
        });
    };

    var validateGroupRequirements = function(groups, tray, IDTIPCOBRA){
        if (IDTIPCOBRA === null){
        	if (tray.length == 0){
                ScreenService.showMessage('Favor escolher pelo menos uma opção.');
                return false;
            } else {
	            for (var i in groups){
	                var count = self.promoGroupCount(tray, groups[i].CDGRUPO);
	                var quant = groups[i].QTPRGRUPROMIN;

	                if (count < quant && !groups[i].DISABLED){
	                    var alert = 'Favor escolher mais ' + (quant - count);
	                    if (quant - count > 1) alert += ' produtos do grupo ' + groups[i].NMGRUPO + '.';
	                    else alert += ' produto do grupo ' + groups[i].NMGRUPO + '.';

	                    ScreenService.showMessage(alert);
	                    return false;
	                }
	            }
            }
        }
        else {
            if (tray.length == 0){
                ScreenService.showMessage('Favor escolher pelo menos uma opção.');
                return false;
            }
        }
        return true;
    };

    this.updatePromoProductQuantity = function(quantity){
        if (quantity == null || quantity === ""){
            return null;
        }
        else {
            quantity = String(quantity).replace(',','.');
            if (isNaN(quantity) || quantity <= 0){
                return null;
            }
            else {
                quantity = parseFloat(quantity).toFixed(3);
                return parseFloat(quantity);
            }
        }
    };

    this.updatePromoObservations = function(widget, row){
        var PromoTray = this.defineRepository(widget);
        return PromoTray.findAll().then(function (tray){
            var handle = _.find(tray, function(item){
                return item.CDPRODUTO === widget.currentRow.CDPRODUTO;
            });

            if (widget.currentRow.IDPESAPROD === "N"){
                var originalQuantity = handle.QTPRODCOMVEN;
                handle.QTPRODCOMVEN = parseInt(widget.currentRow.QTPRODCOMVEN);
                var groupCount = self.promoGroupCount(tray, widget.currentRow.CDGRUPO);

                if (groupCount > widget.currentRow.QTPRGRUPPROMOC){
                    widget.currentRow.QTPRODCOMVEN = originalQuantity;
                    return ScreenService.showMessage("Quantidade excedida.");
                }
                if (widget.currentRow.QTPRODCOMVEN == 0 && widget.currentRow.IDOBRPRODSELEC === "S"){
                    widget.currentRow.QTPRODCOMVEN = originalQuantity;
                    return ScreenService.showMessage("Produtos obrigatórios não podem ser retirados da seleção.");
                }
            }

            handle.CDOCORR        = widget.currentRow.CDOCORR || [];
            handle.DSOCORR_CUSTOM = widget.currentRow.DSOCORR_CUSTOM || null;
            handle.TXPRODCOMVEN   = this.obsToText(widget.currentRow.CDOCORR, widget.currentRow.DSOCORR_CUSTOM);
            handle.ATRASOPROD     = widget.currentRow.ATRASOPROD;
            handle.TOGO           = widget.currentRow.TOGO;
            handle.holdText       = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
            handle.toGoText       = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
            handle.QTPRODCOMVEN   = this.updatePromoProductQuantity(widget.currentRow.QTPRODCOMVEN);

            return PromoTray.save(tray);
        }.bind(this));
    };

    this.togglePromoDelay = function(widget){
        var PromoTray = this.defineRepository(widget);
        PromoTray.findAll().then(function (tray){
            for (var i in tray){
                tray[i].ATRASOPROD = widget.currentRow.ATRASOPROD;
                tray[i].TOGO       = widget.currentRow.TOGO;
                tray[i].holdText   = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
                tray[i].toGoText   = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
            }
            PromoTray.save(tray);
        }.bind(this));
    };

    this.filterPromoProducts = function(args){
        if (!args.row.DISABLED){
            var PromoTray = this.defineRepository(args.owner);
            this._filterPromoProducts(args).then(function (widget){
                PromoTray.findAll().then(function (tray){
                    tray.forEach(function (trayItem){
                        var handle = _.find(widget.dataSource.data, function (prod){
                            return prod.CDPRODUTO === trayItem.CDPRODUTO;
                        });
                        if (!_.isEmpty(handle)){
                            handle.quantity = 0;
                            if (handle.CDGRUPO == widget.dataSource.data[0].CDGRUPO){
                                if (handle.IDPESAPROD === "S"){
                                    handle.quantity++;
                                }
                                else {
                                    handle.quantity = trayItem.QTPRODCOMVEN;
                                }
                            }
                        }
                    });
                });
            });
        }
    };

    this._filterPromoProducts = function (args){
        for (var i in args.owner.dataSource.data){
            args.owner.dataSource.data[i].SELECTED = false;
        }
        var defer = ZHPromise.defer();
        ScreenService.filterWidget(args.owner, args.owner.parent.widgets);
        this.setGroupHeader(args);
        args.owner.container.getWidget('products').reload().then(function(){
            defer.resolve(args.owner.container.getWidget('products'));
        });
        return defer.promise;
    };

    this.trataCampanha = function(tray){
        return OperatorRepository.findOne().then(function (operatorData){
            var defer = ZHPromise.defer();
            if (operatorData.modoHabilitado === 'B' && operatorData.UTLCAMPANHA){
                return AccountService.getCampanha(tray).then(function (campanha){
                    try {
                        if (!_.isEmpty(campanha)){
                            campanha = campanha[0];
                            if (campanha.IDAPLICADESACR == '1'){
                                tray = self.aplicaDescontoCampanha(tray, campanha, campanha.CDPRODPRIN);
                            }
                            else if (campanha.IDAPLICADESACR == '2'){
                                tray = self.aplicaDescontoCampanha(tray, campanha, campanha.CDPRODCOMB);
                            }
                            else if (campanha.IDAPLICADESACR == '3'){
                                tray = self.aplicaDescontoCampanha(tray, campanha, campanha.CDPRODCOMB2);
                            }
                            else if (campanha.IDAPLICADESACR == '4'){
                                tray = self.rateiaDescontoCampanha(tray, campanha);
                            }
                        }
                        defer.resolve(tray);

                    } catch (err){
                        ScreenService.showMessage(err);
                        defer.reject();
                    } finally {
                        return defer.promise;
                    }
                });
            }
            else {
                defer.resolve(tray);
            }
            return defer.promise;
        });
    };

    this.aplicaDescontoCampanha = function(tray, campanha, produto){
        var valor = null;
        for (var i in tray){
            if (tray[i].CDPRODUTO == produto){
                if (campanha.IDPERCVALOR == 'V'){
                    valor = parseFloat(campanha.VRDESCACRE);
                }
                else {
                    valor = tray[i].REALPRICE * parseFloat(campanha.VRDESCACRE) / 100;
                }

                if (campanha.IDDESCACRE == 'D'){
                    tray[i].DISCOUNT = Math.floor(valor * 100) / 100;
                }
                else {
                    tray[i].ADDITION = Math.floor(valor * 100) / 100;
                }
                var newPrice = parseFloat((tray[i].REALPRICE + tray[i].ADDITION - tray[i].DISCOUNT).toFixed(2));
                tray[i].PRICE = newPrice;
                tray[i].TOTPRICE = newPrice;
                tray[i].VRDESITVEND = parseFloat((tray[i].VRDESCONTO + tray[i].DISCOUNT).toFixed(2));
                tray[i].REALSUBSIDY = 0;
                break;
            }
        }
        return tray;
    };

    this.rateiaDescontoCampanha = function(tray, campanha){
        // Preco total de TOTOS os produtos, considerando descontos e acrescimos da ITEMPRECODIA.
        var totalPrice = tray.reduce(function (total, item){
            return total + item.REALPRICE;
        }, 0);
        // Preco final de TODOS os produtos, considerando o desconto ou acrescimo da campanha.
        var modo = null;
        var finalPrice = null;
        if (campanha.IDDESCACRE == 'D'){
            modo = 'DISCOUNT';
            if (campanha.IDPERCVALOR == 'V'){
                finalPrice = totalPrice - parseFloat(campanha.VRDESCACRE);
            }
            else {
                finalPrice = totalPrice * (1 - parseFloat(campanha.VRDESCACRE) / 100);
            }
        }
        else {
            modo = 'ADDITION';
            if (campanha.IDPERCVALOR == 'V'){
                finalPrice = totalPrice + parseFloat(campanha.VRDESCACRE);
            }
            else {
                finalPrice = totalPrice * (1 + parseFloat(campanha.VRDESCACRE) / 100);
            }
        }
        // Aplica o desconto/acrescimo parcial nos produtos.
        tray = tray.map(function (item){
            var valor = null;
            if (campanha.IDPERCVALOR == 'V'){
                valor = (item.REALPRICE / totalPrice) * parseFloat(campanha.VRDESCACRE);
            }
            else {
                valor = item.REALPRICE * parseFloat(campanha.VRDESCACRE) / 100;
            }
            item[modo] = Math.floor(valor * 100) / 100;
            var newPrice = parseFloat((item.REALPRICE + item.ADDITION - item.DISCOUNT).toFixed(2));
            item.PRICE = newPrice;
            item.TOTPRICE = newPrice;
            item.VRDESITVEND = parseFloat((item.VRDESCONTO + item.DISCOUNT).toFixed(2));
            item.REALSUBSIDY = 0;
            return item;
        });
        // Total do desconto/acrescimo implementado.
        var totalDescAcre = tray.reduce(function (total, item){
            return total + item[modo];
        }, 0);
        // Rateia a diferença entre os descontos/acrescimos, caso exista.
        var diferenca = (totalPrice - finalPrice) - totalDescAcre;
        if (diferenca > 0.01){
            var qtdRateio = parseInt(diferenca/0.01);
            var c = 0;
            var totalProduto = null;
            var newPrice = null;
            while (qtdRateio > 0){
                for (var i in tray){
                    totalProduto = Math.floor((tray[i].REALPRICE + tray[i].ADDITION - tray[i].DISCOUNT) * 100) / 100;
                    if (tray[i][modo] >= 0.01 && totalProduto > 0.01){
                        tray[i][modo] += 0.01;
                        qtdRateio--;
                    }
                    newPrice = parseFloat((tray[i].REALPRICE + tray[i].ADDITION - tray[i].DISCOUNT).toFixed(2));
                    tray[i].PRICE = newPrice;
                    tray[i].TOTPRICE = newPrice;
                    tray[i].VRDESITVEND = parseFloat((tray[i].VRDESCONTO + tray[i].DISCOUNT).toFixed(2));

                    if (qtdRateio == 0) break;
                }

                c++;
                if (c == 1000) throw "Erro no rateio do desconto.";
            }
        }

        return tray;
    };

	this.storeParentObservations = function(widget){
		AccountCart.findAll().then(function (cart){
			cart[0].CDOCORR = widget.currentRow.CDOCORR || [];
			cart[0].DSOCORR_CUSTOM = widget.currentRow.DSOCORR_CUSTOM || null;
			AccountCart.save(cart[0]).then(function (){
				widget.setCurrentRow({'CDOCORR': [], 'DSOCORR_CUSTOM': null});
				WindowService.openWindow('MENU_SCREEN');
			});
		}.bind(this));
	};

	this.checkPromoDatasourceHandler = function (widget) {
		delete widget.dataSource.data;
		AccountCart.findAll().then(function (cart) {
			SmartPromoTray.findAll().then(function (tray) {
				tray.forEach(function (product) {
					product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
					if (product.ATRASOPROD === 'Y') product.holdText = 'SEGURA';
					else product.holdText = '';
					if (product.TOGO === 'Y') product.toGoText = 'PARA VIAGEM';
					else product.toGoText = '';
				}.bind(this));
				widget.dataSource.data = tray;

				// Prepares the parent product observation popup.
				widget.widgets[1].getField('CDOCORR').dataSource.data = this.getObservations(cart[0].OBSERVATIONS);
				widget.widgets[1].label = "Observações Adicionais - " + cart[0].DSBUTTON;

				ScreenService.openPopup(widget.widgets[1]);
			}.bind(this));
		}.bind(this));
	};

	this.getObservations = function (arrayCDOCORR) {
		var result = [];
		if (arrayCDOCORR) {
			if (!observationMap[arrayCDOCORR]) {
				observationMap[arrayCDOCORR] = this.cutRepeatValues(arrayCDOCORR).map((function (eachCDOCORR) {
					return this.findFirst(allObservations, function (obs) {
						return obs.CDOCORR === eachCDOCORR;
					});
				}).bind(this));
			}
			result = observationMap[arrayCDOCORR];
		}
		return result;
	};

	this.findFirst = function (arr, test) {
		var result = null;
		arr.some(function (element, i) {
			var testResult = test(element, i, arr);
			if (testResult) {
				result = element;
			}
			return testResult;
		});
		return result;
	};

	this.cutRepeatValues = function (array) {
		return array.filter(function (este, i) {
			return array.indexOf(este) === i;
		});
	};

	this.updateCart = function (widget, stripe) {
		return new Promise(function (resolve) {
			handleOneChoiceOnly(widget.currentRow, 'NRSEQIMPRLOJA');
			widget.dataSource.data.forEach(function (product) {
				if (_.isEmpty(product.PRODUTOS)) {
					product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
					product.NMIMPRLOJA = "";
					if (product.NRSEQIMPRLOJA) {
						product.NMIMPRLOJA = getPrinterName(product.NRSEQIMPRLOJA[0], product.IMPRESSORAS);
					}
				}
				else {
					for (var i in product.PRODUTOS) {
						if (product.ATRASOPROD === 'Y') product.PRODUTOS[i].ATRASOPROD = 'Y';
						else product.PRODUTOS[i].ATRASOPROD = 'N';
						if (product.TOGO === 'Y') product.PRODUTOS[i].TOGO = 'Y';
						else product.PRODUTOS[i].TOGO = 'N';
					}
				}
				if (product.ATRASOPROD === 'Y') {
					product.holdText = 'SEGURA';
				} else {
					product.holdText = '';
				}

				if (product.TOGO === 'Y') {
					product.toGoText = 'PARA VIAGEM';
				} else {
					product.toGoText = '';
				}

				self.calcProductValue(product);
			}.bind(this));

			var row = _.clone(widget.currentRow);
			if (stripe) {
				widget.dataSource.data.reverse();
				self.prepareCart(widget, stripe);
			}
			if (!_.isEmpty(widget.currentRow)) {
				var promiseCart = function () {
					return AccountCart.findAll().then(function (cart) {
						cart = _.map(cart, function (itemCart) {
							if (itemCart.IDENTIFYKEY == row.IDENTIFYKEY) {
								itemCart = row;
							}
							return itemCart;
						});
						if (stripe) {
							cart.reverse();
						}
						return AccountCart.remove(Query.build()).then(function () {
							return AccountCart.save(cart);
						}.bind(this));
					});
				};
				var promisePool = function () {
					return CartPool.findAll().then(function (cartPool) {
						cartPool = _.map(cartPool, function (itemCart) {
							if (itemCart.IDENTIFYKEY == row.IDENTIFYKEY) {
								itemCart = row;
							}
							return itemCart;
						});
						if (stripe) {
							cartPool.reverse();
						}
						return CartPool.remove(Query.build()).then(function () {
							return CartPool.save(cartPool);
						}.bind(this));
					});
				};

				Promise.all([promiseCart(), promisePool()]).then(function () {
					resolve();
				}.bind(this));
			} else {
				resolve();
			}
		}.bind(this));
	};

	var getPrinterName = function (NRSEQIMPRLOJA, arrayPrinters) {
		if (arrayPrinters && NRSEQIMPRLOJA) {
			return arrayPrinters.filter(function (printer) {
				return printer.NRSEQIMPRLOJA == NRSEQIMPRLOJA;
			})[0].NMIMPRLOJA || "";
		} else {
			return "";
		}
	};

	this.updatePromoItem = function (widget) {
		var defer = ZHPromise.defer();
		var cart = widget.dataSource.data;
		cart.forEach(function (product) {
			product.TXPRODCOMVEN = this.obsToText(product.CDOCORR, product.DSOCORR_CUSTOM);
		}.bind(this));
		this.savePromo(cart);
		defer.resolve();
		return defer.promise;
	};

	this.togglePromoDelayCheck = function (widget) {
		for (var i in widget.dataSource.data) {
			widget.dataSource.data[i].ATRASOPROD = widget.currentRow.ATRASOPROD;
			widget.dataSource.data[i].holdText = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
			widget.dataSource.data[i].TOGO = widget.currentRow.TOGO;
			widget.dataSource.data[i].toGoText = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
		}
		this.savePromo(widget.dataSource.data);
	};

	this.updateObservations = function (widget) {
		return AccountCart.findAll().then(function (data) {
			widget.currentRow.QTPRODCOMVEN = parseFloat(String(widget.currentRow.QTPRODCOMVEN).replace(',', '.'));
			handleOneChoiceOnly(widget.currentRow, 'NRSEQIMPRLOJA');
			data[0].CDOCORR = widget.currentRow.CDOCORR || [];
			data[0].DSOCORR_CUSTOM = widget.currentRow.DSOCORR_CUSTOM || null;
			data[0].ATRASOPROD = widget.currentRow.ATRASOPROD;
			data[0].TOGO = widget.currentRow.TOGO;
			data[0].holdText = (widget.currentRow.ATRASOPROD === 'Y') ? 'SEGURA' : '';
			data[0].toGoText = (widget.currentRow.TOGO === 'Y') ? 'PARA VIAGEM' : '';
			data[0].NRSEQIMPRLOJA = widget.currentRow.NRSEQIMPRLOJA || [];
			data[0].TXPRODCOMVEN = self.obsToText(widget.currentRow.CDOCORR, widget.currentRow.DSOCORR_CUSTOM);
			data[0].NMIMPRLOJA = getPrinterName(widget.currentRow.NRSEQIMPRLOJA[0], data[0].IMPRESSORAS);
			if (widget.currentRow.IDPESAPROD === 'S') {
				if (widget.currentRow.QTPRODCOMVEN !== null && !isNaN(widget.currentRow.QTPRODCOMVEN)) {
					data[0].QTPRODCOMVEN = parseFloat(widget.currentRow.QTPRODCOMVEN.toFixed(3));
				}
				else {
					widget.currentRow.QTPRODCOMVEN = "";
					data[0].QTPRODCOMVEN = 1;
				}
			}
			else {
				data[0].QTPRODCOMVEN = parseInt(widget.currentRow.QTPRODCOMVEN);
			}
			self.calcProductValue(data[0]);
			return AccountCart.save(data[0]);
		}.bind(this));
	};

	this.handleObservations = function (data, widget) {
		//@TODO SOME HARD STUFF
		var obsOnScreen = widget.getField('CDOCORR').dataSource.data || [];
		//pega os dados das obervaçoes ja selecionadas
		var ocorrencias = [];
		if (widget.currentRow.CDOCORR.length > 0) {
			ocorrencias = _.filter(allObservations, function (obs) {
				return _.some(widget.currentRow.CDOCORR, function (value) { return value == obs.CDOCORR; });
			});
		}

		//trata grupos
		var gruposOnScreen = obsOnScreen.map(function (grupo) {
			if (grupo.CDGRUPOOBRIG) {
				return grupo.CDGRUPOOBRIG;
			}
		}).filter(function (grupo, index, grupos) {
			return grupo && grupos.indexOf(grupo) === index;
		});

		var grupoInvalido;
		var checando = gruposOnScreen.every(function (grupo) {
			if (!ocorrencias.length) {
				grupoInvalido = grupo;
				return false;
			} else {
				return ocorrencias.some(function (validating, index, ocorrencias) {
					var valid = validating.CDGRUPOOBRIG && validating.CDGRUPOOBRIG == grupo;
					if (!valid) {
						grupoInvalido = grupo;
					}
					return !!valid;
				});
			}
		});
		if (!checando && grupoInvalido) {
			grupoInvalido = _.find(allObservations, { 'CDGRUPOOBRIG': grupoInvalido }).NMGRUPOBRIG;
			return { error: true, message: "Quantidade mínima de observações do grupo " + grupoInvalido + " não foi atingida." };
		}

		var qtdObsFaltando = data[0].NRQTDMINOBS - (_.get(widget.currentRow, "CDOCORR.length") || 0);
		if (!data[0].NRQTDMINOBS || (data[0].NRQTDMINOBS && qtdObsFaltando < 1)) {
			return { error: false };
		} else {
			return { error: true, message: "Quantidade mínima de observação não atingida. Por favor selecione mais " + qtdObsFaltando + ' ' + (qtdObsFaltando > 1 ? "observações" : "observação") + '.' };
		}
	};

	var handleOneChoiceOnly = function (row, field) {
		if (row && row[field]) {
			var fieldLength = row[field].length;
			if (fieldLength > 1) {
				var lastChoice = row[field][fieldLength - 1];
				row[field] = [];
				row[field].push(lastChoice);
			}
		}
	};

	this.undoOrder = function (product, cartAction) {
		AccountCart.findAll().then(function (Cart) {
			AccountCart.remove(Query.build()).then(function () {
				var newCart = Cart.filter(function (item) {
					return item.ID !== product.ID;
				});

				AccountCart.save(newCart).then(function () {
					ScreenService.closePopup();
				});

				cartAction.hint = cartAction.hint - 1;
			});
		});
	};

	this.prepareMenu = function (widgets) {
		ParamsGroupRepository.findAll().then(function (data) {
			widgets[0].dataSource.data = data;
			widgets[0].setCurrentRow(data[0]);
			ParamsMenuRepository.findAll().then(function (data) {
				widgets[1].dataSource.data = data;
				widgets[1].setCurrentRow(data[0]);
			});
		});

	};

	this.updateCancelObservations = function (row, callback) {
		var CDOCORR = row.CDOCORR;
		var custom = row.cancelMotive || "";
		var strObs = '';
		var __processObservations = function () {
			if (CDOCORR) {
				strObs = CDOCORR.map(function (obs) {
					return cancelObservations.filter(function (eachObs) {
						return eachObs.CDOCORR === obs;
					})[0].DSOCORR;
				}).join("; ");
			}
			if (custom) {
				if (strObs) {
					strObs += "; " + custom;
				} else {
					strObs = custom;
				}
			}
			row.TXPRODCOMVENCAN = strObs;
			if (callback) {
				callback();
			}
		};
		if (cancelObservations.length === 0) {
			updateCancelObservationsInner(__processObservations);
		} else {
			__processObservations();
		}
	};

	this.prepareCancelProduct = function (owner) {
		owner.widgets[0].currentRow = owner.currentRow;

		if (owner.currentRow.composicao)
			owner.widgets[0].fields[1].dataSource.data = owner.currentRow.composicao;
		else
			owner.widgets[0].fields[1].dataSource.data = [];
	};

	this.filterProductsFromPosition = function (position, selectProducts, popup, itemsWidget) {
		popup.setCurrentRow({});

		if (position != 1) position = position.owner.data('position') + 1;
		var productData = angular.copy(itemsWidget.dataSource.data);
		selectProducts.dataSource.data = _.sortBy(productData, 'POS').filter(function (p) {
			return p.POS != position;
		});
	};

	this.initTransferPopup = function (popup, itemsWidget) {
		popup.getField('positionsField').position = 0;
		popup.getField('positionsField').dataSource.data[0] = {
			NRPOSICAOMESA: itemsWidget.container.getWidget('accountDetailsTable').getField('NRPESMESAVEN').value()
		};
		this.filterProductsFromPosition(1, popup.getField('selectProducts'), popup, itemsWidget);
		ScreenService.openPopup(popup);
	};

	this.integrityControl = function (widget, popup) {
		selectControl = [];
		var selectedItem = widget.selectedRow;
		if (!selectedItem.__isSelected) selectControl.push(selectedItem.NRPRODCOMVEN);
		widget.dataSource.data.forEach(function (item) {
			if (!_.isEmpty(item.NRSEQPRODCOM)) {
				if (item.NRSEQPRODCOM == selectedItem.NRSEQPRODCOM && !_.isEqual(item, selectedItem)) {
					item.__isSelected = !selectedItem.__isSelected;
				}
			}
			if (item.__isSelected) selectControl.push(item.NRPRODCOMVEN);
		});
	};

	this.handleSelectButtons = function (widget, popup) {
		var removedProduct = null;
		var selectedProducts = popup.fields[1].value();
		selectControl.forEach(function (nrprodcomven) {
			if (!~selectedProducts.indexOf(nrprodcomven)) {
				removedProduct = nrprodcomven;
			}
		});

		if (removedProduct) {
			selectControl.splice(selectControl.indexOf(removedProduct), 1);
			removedProduct = widget.dataSource.data.filter(function (item) {
				return item.NRPRODCOMVEN == removedProduct;
			});
			var smartPromoCode = removedProduct[0].NRSEQPRODCOM;
			if (smartPromoCode) {
				var smartPromoProds = widget.dataSource.data.filter(function (item) {
					return item.NRSEQPRODCOM == smartPromoCode;
				});
				smartPromoProds = smartPromoProds.map(function (item) {
					return item.NRPRODCOMVEN;
				});
				selectedProducts = selectedProducts.filter(function (nrprodcomven) {
					return !~smartPromoProds.indexOf(nrprodcomven);
				});
				popup.fields[1].value(selectedProducts);
				var difference = _.difference(selectControl, selectedProducts);
				difference.forEach(function (diff) {
					selectControl.splice(selectControl.indexOf(diff), 1);
				});
			}
		}
	};

	this.transferPositions = function (widget, position, globalPositions) {
		if (widget.currentRow.selectProducts && widget.currentRow.selectProducts.length > 0) {
			var CDCLIENTE = null;
			var CDCONSUMIDOR = null;
			if (globalPositions.dataSource.data[0].clientMapping[position + 1]) {
				CDCLIENTE = globalPositions.dataSource.data[0].clientMapping[position + 1].CDCLIENTE;
			}
			if (globalPositions.dataSource.data[0].consumerMapping[position + 1]) {
				CDCONSUMIDOR = globalPositions.dataSource.data[0].consumerMapping[position + 1].CDCONSUMIDOR;
			}
			TableActiveTable.findOne().then(function (activeTable) {
				AccountService.transferPositions(widget.currentRow.selectProducts, position + 1, activeTable.NRVENDAREST, activeTable.NRCOMANDA, CDCLIENTE, CDCONSUMIDOR).then(function () {
					ScreenService.closePopup();
					AccountGetAccountDetails.findOne().then(function (accountDetails) {
						self.refreshAccountDetails(widget.container.getWidget('accountDetails').widgets, accountDetails.posicao, true);
					});
				});
			});
		}
		else {
			ScreenService.showMessage("Favor escolher pelo menos 1 produto para ser transferido.");
		}
	};

	this.cancelProduct = function (row, widget, IDPRODPRODUZ) {
		ParamsParameterRepository.findOne().then(function (params) {
			this.updateCancelObservations(row, function () {
				if (!(row.TXPRODCOMVENCAN) && params.IDSOLOBSCAN === 'S') {
					ScreenService.showMessage("Informe um motivo para o cancelamento.");
				} else {
					OperatorRepository.findAll().then(function (data) {
						var chave = data[0].chave;
						var selectedObs = _.get(row, 'CDOCORR', []);
						var motivo = [{
							'CDGRPOCOR': _.get(cancelObservations[0], 'CDGRPOCOR', []),
							'CDOCORR': selectedObs[selectedObs.length - 1] || [], // salva CDOCORR da última observação clicada
							'TXPRODCOMVEN': row.TXPRODCOMVENCAN
						}];
						var produto = [];
						produto.push(row.NRVENDAREST);
						produto.push(row.nrcomanda);
						produto.push(row.NRPRODCOMVEN);
						produto.push(row.CDPRODPROMOCAO);
						produto.push(row.NRSEQPRODCOM);
						produto.push(row.NRSEQPRODCUP);
						produto.push(row.codigo);
						produto.push(row.quantidade);
						produto.push(row.composicao);

						this.getAccountData(function (accountData) {
							AccountService.cancelProduct(chave, data[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, produto, motivo, widget._supervisor, IDPRODPRODUZ).then(function (response) {
								if (_.get(response, 'paramsImpressora')) {
									PerifericosService.print(response.paramsImpressora).then(function () {
										self.handleProducts(widget, row);
									});
								} else {
									self.handleProducts(widget, row);
								}
							});
						});
					}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.handleProducts = function (widget, row) {
		widget.dataSource.data = widget.dataSource.data.filter(function (item) {

			if (row.CDPRODPROMOCAO === null) {
				// Delete splited products
				if (item.IDDIVIDECONTA == 'S') {
					return item.NRVENDAREST !== row.NRVENDAREST ||
						item.nrcomanda !== row.nrcomanda ||
						item.codigo !== row.codigo ||
						item.NRPRODORIG !== row.NRPRODORIG;

				} else {
					return item.NRVENDAREST !== row.NRVENDAREST ||
						item.nrcomanda !== row.nrcomanda ||
						item.NRPRODCOMVEN !== row.NRPRODCOMVEN ||
						item.codigo !== row.codigo;
				}
			}
			/* If the removed product was a Smart Promo, removes all other products associated with it. */
			else {
				return (item.NRVENDAREST !== row.NRVENDAREST ||
					item.nrcomanda !== row.nrcomanda ||
					item.NRPRODCOMVEN !== row.NRPRODCOMVEN ||
					item.codigo !== row.codigo) &&
					item.NRSEQPRODCOM !== row.NRSEQPRODCOM;
			}
		});

		if (widget.dataSource.data.length === 0) {
			WindowService.openWindow('MENU_SCREEN');
		}
	};

	this.removeFromCart = function (row, widget, stripe) {
		ScreenService.confirmMessage(
			'Remover produto ' + row.DSBUTTON + '?',
			'question',
			function () {
				AccountCart.findAll().then(function (cart) {
					cart = _.filter(cart, function (itemCart) {
						return itemCart.IDENTIFYKEY !== row.IDENTIFYKEY;
					}.bind(this));
					return AccountCart.remove(Query.build()).then(function () {
						return AccountCart.save(cart);
					});
				}.bind(this)).then(function () {
					return CartPool.findAll().then(function (cartPool) {
						cartPool = _.filter(cartPool, function (itemCart) {
							return itemCart.IDENTIFYKEY !== row.IDENTIFYKEY;
						}.bind(this));
						return CartPool.remove(Query.build()).then(function () {
							return CartPool.save(cartPool);
						});
					}.bind(this));
				}).then(function () {
					widget.dataSource.data = _.reverse(_.filter(widget.dataSource.data, function (currentItem) {
						return currentItem.IDENTIFYKEY !== row.IDENTIFYKEY;
					}.bind(this)));
					self.produtosDesistencia([row]);
					if (widget.dataSource.data.length === 0) {
						OperatorRepository.findOne().then(function (operatorData) {
							if (operatorData.modoHabilitado === 'B') {
								CarrinhoDesistencia.remove(Query.build());
							}
							if (operatorData.modoHabilitado === 'O') {
								WindowService.openWindow('ORDER_MENU_SCREEN');
							} else {
								WindowService.openWindow('MENU_SCREEN');
							}
						}.bind(this));
					} else {
						self.prepareCart(widget, stripe);
					}
				}.bind(this));
			}.bind(this),
			function () { }
		);
	};

	this.removePromoItem = function (row, widget) {
		ScreenService.confirmMessage(
			'Remover o item ' + row.DSBUTTON + '?',
			'question',
			function () {
				widget.dataSource.data = widget.dataSource.data.filter(function (itemCart) {
					return itemCart.ID !== row.ID;
				});

				if (row.IDAPLICADESCPR === 'I' && row.DISCOUNT !== 0) {
					for (var i in widget.dataSource.data) {
						if (widget.dataSource.data[i].CDPRODUTO === row.CDPRODUTO) {
							widget.dataSource.data[i].DISCOUNT = row.DISCOUNT;
							widget.dataSource.data[i].ADDITION = row.ADDITION;
							widget.dataSource.data[i].PRICE = row.PRICE;
							widget.dataSource.data[i].STRDESCONTO = row.STRDESCONTO;
							widget.dataSource.data[i].STRPRICE = row.STRPRICE;
							widget.dataSource.data[i].VRDESPRODPROMOC = row.VRDESPRODPROMOC;
							widget.dataSource.data[i].IDDESCACRPROMO = row.IDDESCACRPROMO;

							break;
						}
					}
				}

				this.savePromo(widget.dataSource.data, function () {
					if (widget.dataSource.data.length === 0) {
						WindowService.openWindow('PROMO_SCREEN');
					}
				});
			}.bind(this),
			function () { }
		);
	};

	this.saveCart = function (widgetData, callback) {
		AccountCart.remove(Query.build()).then(function () {
			AccountCart.save(widgetData).then(function () {
				if (callback) {
					callback();
				}
			}.bind(this));
		});
	};

	this.savePromo = function (widgetData, callback) {
		SmartPromoTray.remove(Query.build()).then(function () {
			SmartPromoTray.save(widgetData);
			if (callback) {
				callback();
			}
		});
	};

	this.showAccountDetails = function () {
		OperatorRepository.findAll().then(function (operatorData) {
			var screenToOpen = '';
			if ((operatorData[0].modoHabilitado === 'C') || operatorData[0].modoHabilitado === 'O' || (operatorData[0].IDLUGARMESA === 'N')) {
				screenToOpen = 'ACCOUNT_DETAILS_SCREEN_BILL';
			} else {
				screenToOpen = 'ACCOUNT_DETAILS_SCREEN';
			}
			WindowService.openWindow(screenToOpen);
		});
	};

	// esta função é utilizada na fechar conta
	this.prepareAccountDetails = function (widget, callback) {
		ApplicationContext.OrderController.checkAccess(function () {

			widget.activate();
			OperatorRepository.findAll().then(function (operatorData) {
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
						if (accountDetailsData.nothing[0].nothing === 'nothing') {
							var total = accountDetailsData.AccountGetAccountDetails[0].vlrtotal;

							if (total !== 0 && operatorData[0].modoHabilitado === 'O') {
								accountDetailsData.AccountGetAccountDetails[0].total = UtilitiesService.toCurrency(total);
								total = "total: " + UtilitiesService.toCurrency(total);
								accountDetailsData.AccountGetAccountDetails[0].labeltotal = total;
							}

							widget.dataSource.data = accountDetailsData.AccountGetAccountDetails;
							widget.moveToFirst();
							self.isVisibleAccountItems(widget.container, modoHabilitado, operatorData[0].IDLUGARMESA);

							// caso não tenha nenhum pedido realizado, pergunta se deseja cancelar a abertura da mesa
							if (total === 0 && (modoHabilitado === 'M' || modoHabilitado === 'C')) {
								var mode = modoHabilitado === 'M' ? 'mesa' : 'comanda';
								ScreenService.confirmMessage(
									'Não foi realizado nenhum pedido para esta ' + mode + ', deseja cancelar a abertura?',
									'question',
									function () {
										if (modoHabilitado === 'M') {
											TableService.cancelOpen(operatorData[0].chave, accountData[0].NRMESA).then(function () {
												UtilitiesService.backMainScreen();
											});
										} else {
											BillService.cancelOpen(operatorData[0].chave, accountData[0].NRMESA, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA).then(function () {
												UtilitiesService.backMainScreen();
											});
										}
									},
									function () {
										WindowService.openWindow('MENU_SCREEN');
									}
								);
							} else if (modoHabilitado === 'O') { // modo order
								if (total === 0) {
									ScreenService.showMessage('Não foi realizado nenhum pedido para esta mesa, favor solicitar o fechamento ao garçom.');
									UtilitiesService.backMainScreen();
								}
							} else { // caso tenha pedidos, prepara a tela
								ParamsParameterRepository.findOne().then(function (params) {
									accountDetailsData.AccountGetAccountDetails[0].swicouvert = accountDetailsData.AccountGetAccountDetails[0].swiconsumacao = true;
									var currentRow;
									//Verificação de couvert para preparação de tela no modo comanda.
									if (params.IDCOUVERART != "N" && modoHabilitado !== 'M') {
										if (widget.getField('couvert').value() == '0,00') {
											accountDetailsData.AccountGetAccountDetails[0].swicouvert = false;
											currentRow = widget.container.getWidget('closeAccount').currentRow;
											currentRow.vlrcouvert = UtilitiesService.truncValue(parseFloat(accountDetailsData.AccountGetAccountDetails[0].NRPESMESAVEN) * operatorData[0].PRECOCOUVERT);
										}
									} else if (params.IDCOUVERART === "N") {
										accountDetailsData.AccountGetAccountDetails[0].swicouvert = false;
									}
									if (operatorData[0].IDCOMISVENDA != "N") {
										if (widget.getField('servico').value() == '0,00') {
											accountDetailsData.AccountGetAccountDetails[0].swiservico = false;
											currentRow = widget.container.getWidget('closeAccount').currentRow;
											currentRow.vlrservico = Math.trunc(params.VRCOMISVENDA * currentRow.vlrprodcobtaxa) / 100;
											self.recalcPrice(currentRow);
										} else accountDetailsData.AccountGetAccountDetails[0].swiservico = true;
									} else accountDetailsData.AccountGetAccountDetails[0].swiservico = false;
								});
								if (modoHabilitado === 'M') {
									TableActiveTable.findAll().then(function (activeTable) {
										widget.getField('positions').dataSource.data = activeTable;
									});
								}
							}
							if (jQuery.isFunction(callback)) {
								callback();
							}
						} else {
							UtilitiesService.backMainScreen();
						}
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.refreshAccountDetailsPositionClick = function (widgetsFilhos, args) {
		var p = args.owner.data('position') + 1;
		if (p < 10) p = "0" + p;
		this.refreshAccountDetails(widgetsFilhos, p);
	};

	this.resetAccountScreen = function () {
		this.oldPageDetailsName = '';
		this.oldPosition = null;
	};

	this.resetAccountScreen();

	this.isClosingBill = false;
	this.setCloseBill = function setCloseBill(flag) {
		this.isClosingBill = flag;
	};

	this.setBillAction = function setCloseBillAction(widget) {
		var actionReceber = widget.getAction('receber');
		var actionImprimir = widget.getAction('imprimir');
		if (actionReceber && actionImprimir) {
			if (this.isClosingBill) {
				widget.label = 'Receber Comanda';
				widget.container.label = 'Receber Comanda';
				actionReceber.isVisible = true;
				actionImprimir.isVisible = false;
			} else {
				widget.label = 'Parcial da Conta';
				widget.container.label = 'Parcial da Conta';
				actionReceber.isVisible = false;
				actionImprimir.isVisible = true;
			}
		}
		widget.activate();
	};

	// esta função é utilizada na parcial do waiter
	// usar quando o template for "waiter_position"
	this.refreshAccountDetails = function (widgetsFilhos, position, forceRefresh) {
		// pega as duas tabs
		var pageDetails = widgetsFilhos[0];
		var pageItems = widgetsFilhos[1];

		this.setBillAction(pageDetails);

		// validação para evitar disparar múltiplos eventos
		if ((pageDetails.name !== this.oldPageDetailsName) || (position !== this.oldPosition) || forceRefresh) {
			this.oldPageDetailsName = pageDetails.name;
			this.oldPosition = position;

			if (pageDetails.dataSource.data && pageDetails.dataSource.data.length > 0) {
				delete pageDetails.dataSource.data;
			}
			pageDetails.container.restoreDefaultMode();
			pageDetails.setCurrentRow({});

			this.getAccountData(function (accountData) {
				OperatorRepository.findAll().then(function (params) {
					AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'M', position).then(function (databack) {
						var accountDetails = databack.AccountGetAccountDetails[0];

						var dataset = {
							NRPESMESAVEN: accountDetails.NRPESMESAVEN,
							consumacao: accountDetails.consumacao,
							vlrconsumacao: accountDetails.vlrconsumacao,
							couvert: accountDetails.couvert,
							vlrcouvert: accountDetails.vlrcouvert,
							permanencia: accountDetails.permanencia,
							produtos: accountDetails.produtos,
							vlrprodutos: accountDetails.vlrprodutos,
							vlrprodcobtaxa: accountDetails.vlrprodcobtaxa,
							desconto: accountDetails.desconto,
							vlrdesconto: accountDetails.vlrdesconto,
							servico: accountDetails.servico,
							vlrservico: accountDetails.vlrservico,
							total: accountDetails.total,
							vlrtotal: accountDetails.vlrtotal,
							valorPago: accountDetails.valorPago,
							totalSubsidy: accountDetails.totalSubsidy,
							swiconsumacao: accountDetails.swiconsumacao,
							swicouvert: accountDetails.swicouvert,
							swiservico: accountDetails.swiservico,
							realSubsidy: accountDetails.realSubsidy,
							fidelityValue: accountDetails.fidelityValue,
							fidelityDiscount: accountDetails.fidelityDiscount,
							numeroProdutos: accountDetails.numeroProdutos,
							posicao: accountDetails.posicao,
							NMVENDEDORABERT: accountDetails.NMVENDEDORABERT
						};
						pageDetails.setCurrentRow(dataset);

						if (pageItems.dataSource.data && pageItems.dataSource.data.length > 0) {
							delete pageItems.dataSource.data;
						}

						AccountService.getTableTransactions(accountData[0].NRMESA, "T", accountData[0].NRVENDAREST, params[0].chave).then(function (tableTransactions) {
							if (!position && templateManager.container.getWidget('accountShort')) {
								if (tableTransactions[0].PAGAMENTOMESA !== 0) {
									templateManager.container.getWidget('accountShort').isVisible = false;
								}
								else {
									templateManager.container.getWidget('accountShort').isVisible = true;
								}
							}
						});

						if (Util.isArray(databack.AccountGetAccountItems) && Util.isEmptyOrBlank(databack.AccountGetAccountItems[0])) {
							databack.AccountGetAccountItems = [];
						} else {
							for (var i in databack.AccountGetAccountItems) {
								if (parseFloat(databack.AccountGetAccountItems[i].quantidade.replace(',', '.')) != 1) {
									databack.AccountGetAccountItems[i].DSBUTTON = databack.AccountGetAccountItems[i].quantidade.toString().replace('.', ',') + " x " + databack.AccountGetAccountItems[i].DSBUTTON;
								}
							}
						}

						if (pageDetails.getField('servicoBtn')) {
							pageDetails.getField('servicoBtn').isVisible = params[0].IDCOMISVENDA == "S";
						}

						pageItems.dataSource.data = databack.AccountGetAccountItems;
						templateManager.updateTemplate();
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}
	};

	// esta função é utilizada no pagamento do waiter
	//Usar quando o template for "waiter_position_multiple"
	this.refreshAccountDetailsMultiplePositions = function (widgetsFilhos, position, positionsField) {
		// pega as duas tabs
		var pageDetails = widgetsFilhos[0];
		var pageItems = widgetsFilhos[1];

		if (Number.isInteger(position)) {
			position = [position];
		}

		var messageWidget = positionsField.widget.widgets[2];
		if (messageWidget && (position === undefined || position.length === 0)) {
			messageWidget.activate();
		}

		if (!positionsField._isStatusChanged && (position !== undefined)) {
			return false;
		}

		if (position === undefined || (Array.isArray(position) && position.length === 0)) {
			pageDetails.isVisible = false;
			pageItems.isVisible = false;
			if (messageWidget) {
				messageWidget.isVisible = true;
				messageWidget.activate();
			}
		} else {
			pageDetails.isVisible = true;
			pageItems.isVisible = true;
			if (messageWidget) {
				messageWidget.isVisible = false;
			}
			pageDetails.activate();
		}

		if (Array.isArray(position)) {
			position = position.length === 0 ? "" : position;
		}
		if (Array.isArray(position)) {
			position = position.map(function (p) {
				return ++p;
			});
		}
		if (Number.isInteger(position)) {
			++position;
			if (position < 10) {
				position = "0" + position;
			}
		}

		if (pageDetails.dataSource.data && pageDetails.dataSource.data.length > 0) {
			pageDetails.dataSource.data = [];
		}
		pageDetails.container.restoreDefaultMode();

		// validação para evitar disparar múltiplos eventos
		if ((pageDetails.name !== this.oldPageDetailsName) || (position !== this.oldPosition)) {
			this.oldPageDetailsName = pageDetails.name;
			this.oldPosition = position;

			if (position !== undefined && position.length > 0) {
				this.getAccountData(function (accountData) {
					OperatorRepository.findAll().then(function (params) {
						AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'M', position).then(function (databack) {
							pageDetails.currentRow = databack.AccountGetAccountDetails[0];

							if (pageItems.dataSource.data && pageItems.dataSource.data.length > 0) {
								pageItems.dataSource.data = [];
							}
							if (Util.isArray(databack.AccountGetAccountItems) && Util.isEmptyOrBlank(databack.AccountGetAccountItems[0])) {
								databack.AccountGetAccountItems = [];
							}
							pageItems.dataSource.data = databack.AccountGetAccountItems;
							templateManager.updateTemplate();
							positionsField._isStatusChanged = false;
						}.bind(this));
					}.bind(this));
				}.bind(this));
			}
		}
	};

	this.printAccount = function (position) {
		if (!(position)) {
			position = "";
		}
		this.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'I', position).then(function (data) {
					if (!position) {
						UtilitiesService.backMainScreen();
					}
					if (_.get(data, 'dadosImpressao.dadosImpressao.paramsImpressora.saas')) {
						PerifericosService.print(data.dadosImpressao.dadosImpressao.paramsImpressora).then(function (response) {
							if (response.error) {
								ScreenService.showMessage(response.message);
							}
						});
					} else {
						self.handlePrintBill(data.dadosImpressao);
					}
				});
			});
		});
	};

	this.handlePrintBill = function (dadosImpressao) {
		if (!_.isEmpty(dadosImpressao)) {
			PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.dadosImpressao);
			PrinterService.printerSpaceCommand();
			PrinterService.printerInit().then(function (result) {
				if (result.error)
					ScreenService.alertNotification(result.message);
			});
		}
	};

	this.paymentAccount = function (widget, positions) {
		var CDTIPORECE = widget.currentRow.CDTIPORECE;
		var DSBANDEIRA = widget.currentRow.CDBANCARTCR;
		var IDDESABTEF = widget.currentRow.IDDESABTEF;
		var paymentValue = widget.currentRow.lblValorTotal.replace(',', '.');
		var IDTIPORECE = widget.currentRow.IDTIPORECE;
		positions = positions ? positions : "";

		self.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				if (self.validatePaymentFields(widget, params[0].IDTPTEF)) {
					AccountService.getAccountDetails(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'P', positions).then(function (data) {
						if (self.isTEF(widget, params[0].IDTPTEF) || self.isSITEF(widget, params[0].IDTPTEF))
							IDTPTEF = params[0].IDTPTEF;
						else
							IDTPTEF = null;

						var dataset = {
							chave: params[0].chave,
							CDVENDEDOR: params[0].CDVENDEDOR,
							NRVENDAREST: accountData[0].NRVENDAREST,
							NRMESA: accountData[0].NRMESA,
							NRLUGARMESA: (data.AccountGetAccountDetails[0].posicao === "" ? "T" : data.AccountGetAccountDetails[0].posicao[0]),
							CDTIPORECE: CDTIPORECE,
							IDTIPMOV: IDTIPORECE,
							VRMOV: parseFloat(paymentValue),
							NRCOMANDA: accountData[0].NRCOMANDA,
							DSBANDEIRA: DSBANDEIRA,
							IDTPTEF: IDTPTEF,
							PRODUCTS: data.AccountGetAccountItems
						};

						if (dataset.NRLUGARMESA != "T" && positions.length > 1) {
							var TRANSFER = [];

							var nrmesa = dataset.NRMESA;
							var nrcomanda = dataset.NRCOMANDA;
							var nrvendarest = dataset.NRVENDAREST;

							dataset.PRODUCTS.forEach(function (product) {
								var produto = {
									NRVENDAREST: product.NRVENDAREST,
									NRCOMANDA: product.nrcomanda,
									NRPRODCOMVEN: product.NRPRODCOMVEN,
									quantidade: product.quantidade
								};

								if (product.POS == parseInt(dataset.NRLUGARMESA)) {
									nrmesa = product.NRMESA;
									nrcomanda = product.nrcomanda;
									nrvendarest = product.NRVENDAREST;
								}

								TRANSFER.push(produto);
							});

							// Transfere os itens das posicoes selecionadas para a primeira posiçao selecionada
							TableService.transferItem(dataset.chave, nrmesa, nrcomanda, nrvendarest, TRANSFER, dataset.NRLUGARMESA, null, positions.length).then(function (response) {

								if (response[0].error) {
									ScreenService.showMessage(response[0].error);
								}
								else {
									TransactionsService.moveTransactions(dataset.chave, dataset.NRVENDAREST, dataset.NRCOMANDA, dataset.NRLUGARMESA, positions).then(function (response) {
										if (response[0].error) {
											ScreenService.showMessage(response[0].error);
										}
										else {
											self.pay(widget, dataset, IDTIPORECE, paymentValue);
										}
									});
								}
							});
						}
						else {
							self.pay(widget, dataset, IDTIPORECE, paymentValue);
						}
					});
				}
			});
		});
	};

	this.pay = function (widget, dataset, IDTIPORECE, paymentValue) {
		AccountService.beginPaymentAccount(dataset).then(function (response) {
			self.NRSEQMOVMOB = response[0].NRSEQMOVMOB;
			// Verifica se a opção marcada é Crédito Digitado.
			if (self.isSITEF(widget, dataset.IDTPTEF)) {
				self.typedCredit(widget, self.NRSEQMOVMOB);
			}
			else if (self.isTEF(widget, dataset.IDTPTEF)) {
				if (window.ZhNativeInterface && ZhNativeInterface.tefPayment) {
					ZhNativeInterface.tefPayment(dataset.IDTPTEF, IDTIPORECE, paymentValue, self.NRSEQMOVMOB);
				}
				else {
					self.tefmock(); // Para teste no computador
					// ScreenService.showMessage('ZhNativeInterface não encontrada.');
				}
			}
			else {
				dataset = {
					NRSEQMOVMOB: self.NRSEQMOVMOB,
					NRSEQMOB: null,
					DSBANDEIRA: null,
					NRADMCODE: null,
					IDADMTASK: '0',
					IDSTMOV: '1',
					TXMOVUSUARIO: null,
					TXMOVJSON: null,
					CDNSUTEFMOB: null,
					TXPRIMVIATEF: null,
					TXSEGVIATEF: null,
					transactionStatus: '1'
				};
				self.finishPayment(dataset);
			}
		});
	};

	this.typedCredit = function (widget, NRSEQMOVMOB) {
		var row = widget.currentRow;
		var paymentValue = widget.currentRow.lblValorTotal.replace(',', '.');

		var dataset = {
			amount: paymentValue,
			idAutorizadora: row.CDBANCARTCR,
			dtVencimento: row.cardExpiration.replace('/', ''),
			numCartao: row.cardNumber.replace(/ /g, ''),
			codSeguranca: row.securityCode
		};

		AccountService.typedCreditPayment(dataset, NRSEQMOVMOB).then(function (response) {
			window.tefResult(response);
		});
	};

	window.tefResult = function (result) {
		var capptaErrors = {
			1: 'Não autenticado/Alguma das informações fornecidas para autenticação não é válida',
			2: 'Cappta Android está sendo inicializado',
			3: 'Formato da requisição recebida pelo Cappta Android é inválido',
			4: 'Operação cancelada pelo operador',
			5: 'Pagamento não autorizado/pendente/não encontrado',
			6: 'Pagamento ou cancelamento negados pela rede adquirente ou falta de conexão com internet',
			7: 'Erro interno no Cappta Android',
			8: 'Erro na comunicação com o Cappta Android'
		};

		var JSONTEF = JSON.parse(result)[0];
		var userMessage = _.get(JSONTEF, 'tef_request_details.user_message');
		if (userMessage == "Transação aceita") {
			var dataset = self.createUpdateTransactionObject(JSONTEF);
			self.finishPaymentCappta(dataset);
		} else {
			var defaultMessage = _.get(capptaErrors, _.get(JSONTEF, 'tef_request_type'), 'Falha na comunicação com o aplicativo Cappta. Verifique se o mesmo está instalado.');
			ScreenService.showMessage(userMessage || defaultMessage);
		}
	};

	this.isNotEmpty = function (value) {
		if (_.isString(value)) {
			return !_.isEmpty(value);
		} else {
			return !_.isNil(value);
		}
	};

	this.createUpdateTransactionObject = function (JSONTEF) {
		var JSONTEFDetails = JSONTEF.tef_request_details;
		var dataset = {};
		dataset.JSONTEFDetails = JSONTEFDetails;
		dataset.NRSEQMOVMOB = self.NRSEQMOVMOB;
		dataset.TXMOVJSON = JSON.stringify(JSONTEF);

		dataset.NRSEQMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.DSBANDEIRA = self.isNotEmpty(JSONTEFDetails.card_brand_name) ? JSONTEFDetails.card_brand_name : null;
		dataset.NRADMCODE = self.isNotEmpty(JSONTEFDetails.administrative_code) ? JSONTEFDetails.administrative_code : null;
		dataset.IDADMTASK = self.isNotEmpty(JSONTEFDetails.administrative_task) ? JSONTEFDetails.administrative_task : null;
		dataset.IDSTMOV = self.isNotEmpty(JSONTEFDetails.payment_transaction_status) ? JSONTEFDetails.payment_transaction_status : null;
		dataset.TXMOVUSUARIO = self.isNotEmpty(JSONTEFDetails.user_message) ? JSONTEFDetails.user_message : null;
		dataset.CDNSUTEFMOB = self.isNotEmpty(JSONTEFDetails.unique_sequential_number) ? JSONTEFDetails.unique_sequential_number : null;
		dataset.TXPRIMVIATEF = self.isNotEmpty(JSONTEFDetails.merchant_receipt) ? JSONTEFDetails.merchant_receipt.replace(/'/g, '') : null;
		dataset.TXSEGVIATEF = self.isNotEmpty(JSONTEFDetails.customer_receipt) ? JSONTEFDetails.customer_receipt.replace(/'/g, '') : null;
		dataset.transactionStatus = self.isNotEmpty(JSONTEFDetails.payment_transaction_status) ? JSONTEFDetails.payment_transaction_status : null;

		return dataset;
	};

	this.tefmock = function () {
		// not mocking at the moment
		if (false) {
			var result = [
				{
					"tef_request_type": 4,
					"tef_request_details":
					{
						"payment_transaction_status": 1,
						"acquirer_affiliation_key": "0009448512329101",
						"acquirer_name": "Elavon",
						"card_brand_name": "MAESTRO",
						"acquirer_authorization_code": "SIMULADOR",
						"payment_product": 1,
						"payment_installments": 1,
						"payment_amount": 16,
						"available_balance": null,
						"unique_sequential_number": 21007,
						"acquirer_unique_sequential_number": null,
						"acquirer_authorization_datetime": "2016-07-15 11:25:42",
						"administrative_code": "07520701019",
						"administrative_task": 0,
						"user_message": null,
						"merchant_receipt": "''\r\n'**VIA LOJISTA**'\r\n'ELAVON'\r\n'MAESTRO-DEBITO A VISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/16 10:25:50'\r\n'AUT=SIMULADOR DOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'SIMULADO'",
						"customer_receipt": "''\r\n'HOMOLOGA'\r\n'40.841.182/0001-48'\r\n'**VIA CLIENTE**'\r\n'ELAVON'\r\n'MAESTRO-DEBITO A VISTA'\r\n'************2979'\r\n'ESTAB000948512329101'\r\n'15/07/16 10:25:50'\r\n'AUT=SIMULADOR DOC=21007'\r\n'VALOR=1,50'\r\n'CONTROLE=07520701019'\r\n'SIMULADO'",
						"reduced_receipt": "'ELAVON-NL000948512329101'\r\n'MAESTRO-************2679'\r\n'AUT=SIMULADOR DOC=21007'\r\n'VALOR=1,50 CONTROLE=07520701019'"
					}
				}
			];
			var resultString = JSON.stringify(result);
			window.tefResult(resultString);
		} else {
			ScreenService.showMessage('A Webview do Android não foi encontrada.');
		}
	};

	this.finishPaymentCappta = function (dataset) {
		this.getAccountData(function (accountData, operatorData) {
			dataset.NRVENDAREST = accountData[0].NRVENDAREST;
			dataset.NRCOMANDA = accountData[0].NRCOMANDA;
			dataset.chave = operatorData.chave;
			AccountService.finishPaymentAccount(dataset).then(function (response) {
				if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails !== null) {
					ScreenService.openWindow('accountPayment').then(function () {
						var emailPopup = templateManager.container.getWidget("popupEmail");
						emailPopup.currentRow = response[0].payments[0];
						emailPopup.currentRow.RECEIPT = dataset.JSONTEFDetails.customer_receipt;
						emailPopup.currentRow.RECEIPT = emailPopup.currentRow.RECEIPT.replace(/'/g, '');
						ScreenService.openPopup(emailPopup);
					});
				} else if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails === null) {
					ScreenService.showMessage("Pagamento realizado com sucesso.");
					ScreenService.closePopup();
				} else {
					ScreenService.showMessage(dataset.TXMOVUSUARIO);
				}

				var position;
				var currentWidget;

				if (response[0].tableClosed) {
					UtilitiesService.backMainScreen();
				} else {
					if (response[0].NRLUGARMESA == "T") {
						position = "";
						currentWidget = templateManager.container.getWidget('accountDetails');
						this.refreshAccountDetails(currentWidget.widgets, position);
					} else {
						// position = response[0].NRLUGARMESA;
						// position = templateManager.container.getWidget('accountShort').getField("positionswidget").position;
						templateManager.container.getWidget('accountShort').getField("positionswidget")._isStatusChanged = true;
						currentWidget = templateManager.container.getWidget('accountShort');
						//Carrega com nenhuma posição selecionada
						this.refreshAccountDetailsMultiplePositions(currentWidget.widgets, undefined);
					}
				}
			}.bind(self));
		});
	};

	this.finishPayment = function (dataset) {
		AccountService.finishPaymentAccount(dataset).then(function (response) {
			if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails !== null) {
				WindowService.openWindow('PAYMENT_SCREEN').then(function () {
					var emailPopup = templateManager.container.getWidget("popupEmail");
					emailPopup.currentRow = response[0];
					emailPopup.currentRow.RECEIPT = dataset.JSONTEFDetails.customer_receipt;
					emailPopup.currentRow.RECEIPT = emailPopup.currentRow.RECEIPT.replace(/'/g, '');
					ScreenService.openPopup(emailPopup);
				});
			} else if (dataset.transactionStatus === 1 && dataset.JSONTEFDetails === null) {
				ScreenService.showMessage("Transação aceita!");
			} else {
				ScreenService.showMessage(dataset.TXMOVUSUARIO);
			}

			var position;
			var currentWidget;

			if (response[0].NRLUGARMESA === "T") {
				position = "";
				currentWidget = templateManager.container.getWidget('accountDetails');
				this.refreshAccountDetails(currentWidget.widgets, position);
			}
			else {
				templateManager.container.getWidget('accountShort').getField("positionswidget")._isStatusChanged = true;
				currentWidget = templateManager.container.getWidget('accountShort');
				// carrega com nenhuma posição selecionada
				this.refreshAccountDetailsMultiplePositions(currentWidget.widgets, undefined, templateManager.container.getWidget('accountShort').getField("positionswidget"));
			}
		}.bind(self));
	};

	this.validatePaymentValue = function (widget) {
		if (parseFloat(widget.currentRow.lblValorTotal) > parseFloat(widget.getField('lblValorTotal').validations.range.max)) {

			widget.currentRow.lblValorTotal = widget.getField('lblValorTotal').validations.range.max;
			ScreenService.showMessage('Digite um Valor Válido!');
		}
	};

	this.validatePaymentMethod = function (widget) {
		OperatorRepository.findAll().then(function (operatorData) {
			widget.fieldGroups[1].isVisible = self.isSITEF(widget, operatorData[0].IDTPTEF);
		});
		return true;
	};

	this.validatePaymentFields = function (widget, IDTPTEF) {
		if (this.isSITEF(widget, IDTPTEF)) return widget.isValid();
		return !!widget.currentRow.lblValorTotal && !!widget.currentRow.paymentType;
	};

	this.isSITEF = function (widget, IDTPTEF) {
		return (parseFloat(widget.currentRow.CDBANCARTCR || 0) > 0 && widget.currentRow.IDTIPORECE == 1 && IDTPTEF == 1);
	};

	this.isTEF = function (widget, IDTPTEF) {
		return (widget.currentRow.IDDESABTEF == 'N' && (widget.currentRow.IDTIPORECE == 1 || widget.currentRow.IDTIPORECE == 2) && IDTPTEF == 2);
	};

	this.sendTransactionEmail = function (args) {
		var DSEMAILCLI = args.currentRow.DSEMAILCLI;
		var NRSEQMOVMOB = args.currentRow.NRSEQMOVMOB;

		TransactionsService.updateTransactionEmail(DSEMAILCLI, NRSEQMOVMOB);
		TransactionsService.sendTransactionEmail(NRSEQMOVMOB, DSEMAILCLI).then(function (response) {
			ScreenService.closePopup();
			//IMPLEMENTAR RETORNO DE MENSAGEM.
			ScreenService.showMessage("E-mail enviado com sucesso!");
		});
	};

	this.selectedProduct = {};

	this.prepareCheckOrder = function (product, field, widget, listaFilhos, stripe) {
		OperatorRepository.findAll().then(function (operatorData) {
			if (this.selectedProduct !== product) {
				field.dataSource.data = this.getObservations(product.OBSERVATIONS);
				widget.currentRow = product;
				this.selectedProduct = product;
				if (!_.isEmpty(product.PRODUTOS)) {
					listaFilhos.dataSource.data = product.PRODUTOS;
					widget.getField('CDOCORR').isVisible = false;
					widget.getField('DSOCORR_CUSTOM').isVisible = false;
				}
				else {
					listaFilhos.dataSource.data = [];
					widget.getField('CDOCORR').isVisible = true;
					widget.getField('DSOCORR_CUSTOM').isVisible = true;
				}

				/* Define se será mostrado o checkbox de atraso de produtos. */
				if (operatorData[0].modoHabilitado !== 'O') {
					widget.getField('ATRASOPROD').isVisible = (operatorData[0].NRATRAPADRAO > 0);
				}

				widget.getField('TOGO').isVisible = operatorData[0].IDCTRLPEDVIAGEM === 'S';

				if (operatorData[0].IDUTLQTDPED === 'S') {
					widget.getField('QTPRODCOMVEN').isVisible = true;
					widget.getField('QTPRODCOMVEN').spin = true;
					widget.getField('QTPRODCOMVEN').label = "Quantidade (un)";
					widget.getField('QTPRODCOMVEN').blockInputEdit = true;
				} else {
					widget.getField('QTPRODCOMVEN').isVisible = false;
				}
				if (product.IDPESAPROD === 'S') {
					widget.getField('QTPRODCOMVEN').isVisible = true;
					widget.getField('QTPRODCOMVEN').spin = false;
					widget.getField('QTPRODCOMVEN').label = "Quantidade (kg)";
					widget.getField('QTPRODCOMVEN').blockInputEdit = false;
				}

				handlePrintersProductForRoom(product).then(function (printers) {
					var printersField = widget.getField('NRSEQIMPRLOJA');
					printersField.isVisible = printers.length > 1;
					printersField.dataSource.data = printers;
				});
				templateManager.updateTemplate();
			}
		}.bind(this));
	};

	this.openSmartPromoObservationChangePopup = function (product, popup) {
		OperatorRepository.findAll().then(function (operatorData) {
			if (product) {
				popup.fields[0].dataSource.data = this.getObservations(product.OBSERVATIONS);
				popup.currentRow = product;
				ScreenService.openPopup(popup);
			}
		}.bind(this));
	};

	this.saveSmartPromoObservationsChanges = function (widget) {
		widget.dataSource.data.forEach(function (cart) {
			if (!_.isEmpty(cart.PRODUTOS)) {
				var obs = self.obsToText(cart.CDOCORR, cart.DSOCORR_CUSTOM) + " ";
				for (var j in cart.PRODUTOS) {
					obs += cart.PRODUTOS[j].TXPRODCOMVEN + " ";
				}
				cart.TXPRODCOMVEN = obs.replace(/^ +/, ''); // Remove os espaços do inicio.
			}
			self.calcProductValue(cart);
		}.bind(this));

		AccountCart.findAll().then(function (cart) {
			cart = _.map(cart, function (itemCart) {
				if (itemCart.IDENTIFYKEY == widget.currentRow.IDENTIFYKEY) {
					itemCart = widget.currentRow;
				}
				return itemCart;
			});
			cart.reverse();
			AccountCart.remove(Query.build()).then(function () {
				AccountCart.save(cart);
			}.bind(this));
		});

		CartPool.findAll().then(function (cartPool) {
			cartPool = _.map(cartPool, function (itemCart) {
				if (itemCart.IDENTIFYKEY == widget.currentRow.IDENTIFYKEY) {
					itemCart = widget.currentRow;
				}
				return itemCart;
			});
			cartPool.reverse();
			CartPool.remove(Query.build()).then(function () {
				CartPool.save(cartPool);
			}.bind(this));
		});
		widget.dataSource.data.reverse();
		self.prepareCart(widget, widget.container.getWidget('checkOrderStripe'));
		ScreenService.closePopup();

	};

	// Gets the observations from the child product.
	this.prepareUpdatePromo = function (product, field, widget) {
		OperatorRepository.findAll().then(function (operatorData) {
			if (this.selectedProduct !== product) {
				field.dataSource.data = this.getObservations(product.OBSERVATIONS);
				widget.currentRow = product;
				this.selectedProduct = product;
				/* Define se será mostrado o checkbox de atraso de produtos. */
				if (operatorData[0].NRATRAPADRAO > 0) {
					widget.fields[2].isVisible = true;
					widget.fields[1].class = 8;
				}
				else {
					widget.fields[2].isVisible = false;
					widget.fields[1].class = 12;
				}
			}
		}.bind(this));
	};

	// Resets the data present in the widget to prevent duplicate data from causing errors.
	this.checkOrderReset = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.continueOrdering && !operatorData.newOrders) {
				AccountCart.clearAll();
			}
			widget.dataSource.data = [];
			ScreenService.goBack();
		});
	};

	this.setNewOrders = function (value) {
		return OperatorRepository.findOne().then(function (operatorData) {
			operatorData.newOrders = value;
			return OperatorRepository.save(operatorData);
		});
	};

	this.storeCPF = function (widget) {

	};

	this.setCPF = function (widget) {
		OperatorRepository.findAll().then(function (params) {
			AccountService.setCPF(widget.currentRow.cpfField, widget.currentRow.theCheckBox, widget.getField('positions').position + 1).then(function (result) {

			});
		});
	};

	this.togglePositions = function (widget) {
		widget.getField('positions').isVisible = widget.getField('theCheckBox').value();
		widget.getField('positionProducts').isVisible = widget.getField('theCheckBox').value();
	};

	this.loadPositionOrders = function (widget, args) {
		var position = args.owner.data('position') + 1;
		AccountGetAccountItems.findAll().then(function (items) {
			var positionItems = items.filter(function (item) {
				return item.POS == position;
			});
			widget.getField('positionProducts').value('');
			if (positionItems.length > 0) {
				widget.getField('cpfField').readOnly = false;
				for (var i in positionItems) {
					widget.getField('positionProducts').value(widget.getField('positionProducts').value() + positionItems[i].DSBUTTON + '\n');
				}
			}
			else {
				widget.getField('cpfField').readOnly = true;
			}
		});
	};

	this.closeProductPopup = function (widget) {
		self.updateObservations(widget).then(function () {
			widget.currentRow.QTPRODCOMVEN = parseFloat(widget.currentRow.QTPRODCOMVEN);
			if (widget.currentRow.QTPRODCOMVEN == null || isNaN(widget.currentRow.QTPRODCOMVEN) || widget.currentRow.QTPRODCOMVEN <= 0) {
				ScreenService.showMessage('Favor inserir uma quantidade válida para o produto.');
				return;
			} else {
				AccountCart.findAll().then(function (data) {
					var obsReturn = self.handleObservations(data, widget);

					if (obsReturn.error) {
						ScreenService.showMessage(obsReturn.message);
					} else {
						ScreenService.closePopup();
					}
				}.bind(this));
			}
		});
	};

	this.handlePositionsFieldInit = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');
		widgetCloseAccount.setCurrentRow({});
		radioTablePositions.applyDefaultValue();
		self.setActionLabel(positionsField);
	};

	this.prepareAccountClosingWidget = function (widgetCloseAccount, formWidget, openFidelityPopup, fidelitySearch) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');

		OperatorRepository.findOne().then(function (operatorData) {
			if ((operatorData.modoHabilitado === 'M') && (operatorData.IDLUGARMESA === 'S')) {
				TableActiveTable.findOne().then(function (activeTable) {
					var positionsObject = _.get(activeTable, 'posicoes', {});

					WaiterNamedPositionsState.initializeTemplate();

					positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
					positionsField.dataSource.data[0].clientMapping = ApplicationContext.TableController.buildClientMapping(positionsObject);
					positionsField.dataSource.data[0].consumerMapping = ApplicationContext.TableController.buildConsumerMapping(positionsObject);
					positionsField.dataSource.data[0].positionNamedMapping = ApplicationContext.TableController.buildPositionNamedMapping(positionsObject);
					ApplicationContext.TableController.updatePositionsCopy(positionsField);

					if (openFidelityPopup) {
						self.openTableFidelity(formWidget, positionsField, radioTablePositions, fidelitySearch);
					}
				});
			}
		});
	};

	this.handlePositionsRadioChangeAccount = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');

		var topMargin = parseInt($('.zh-widget-accountItemsTable').css('top'));

		TableActiveTable.findOne().then(function (activeTable) {
			if (radioTablePositions.value() === 'P') {
				positionsField.isVisible = true;
				positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
				$('.zh-widget-accountItemsTable').css('top', topMargin + 55 + 'px');
			} else {
				positionsField.isVisible = false;
				$('.zh-widget-accountItemsTable').css('top', topMargin - 55 + 'px');
				WaiterNamedPositionsState.unselectAllPositions();
				this.refreshAccountDetails(widgetCloseAccount.widgets, '', positionsField, true);
			}
			self.setActionLabel(positionsField);
		}.bind(self));
	};

	this.handleCloseTablePositionChange = function (positionsField) {
		TableActiveTable.findOne().then(function (activeTable) {
			TableService.positionControl(activeTable.NRVENDAREST, positionsField.newPosition + 1, !~positionsField.position.indexOf(positionsField.newPosition), positionsField.position).then(function (result) {
				if (result[0].message == null) {
					self.showPositionActions(positionsField);
					if (positionsField.position.length > 0) {
						positionsField.widget.fields[0].setValue('P');
						self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
					}
					else {
						positionsField.widget.fields[0].setValue('M');
						self.refreshAccountDetails(positionsField.widget.widgets, '', positionsField, true);
					}
					self.setActionLabel(positionsField);
				}
				else {
					positionsField._buttons[positionsField.newPosition].selected = false;
					positionsField.position.pop(positionsField.newPosition);
					if (positionsField.position.length == 0) {
						self.hidePositionActions(positionsField);
					}
				}
			});
		});
	};

	this.showPositionActions = function (positionsField) {
		OperatorRepository.findOne().then(function (operatorData) {
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('changePositions').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('transfer').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('partialPrint').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getField('servicoBtn').isVisible = operatorData.IDCOMISVENDA == "S";
		});
	};

	this.handlePositionsFieldInit = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');
		widgetCloseAccount.setCurrentRow({});
		radioTablePositions.applyDefaultValue();
		self.setActionLabel(positionsField);
	};

	this.prepareAccountClosingWidget = function (widgetCloseAccount, formWidget, openFidelityPopup, fidelitySearch) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');
		positionsField.isVisible = true;

		OperatorRepository.findOne().then(function (operatorData) {
			if ((operatorData.modoHabilitado === 'M') && (operatorData.IDLUGARMESA === 'S')) {
				TableActiveTable.findOne().then(function (activeTable) {
					var positionsObject = _.get(activeTable, 'posicoes', {});

					WaiterNamedPositionsState.initializeTemplate();

					positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
					positionsField.dataSource.data[0].clientMapping = ApplicationContext.TableController.buildClientMapping(positionsObject);
					positionsField.dataSource.data[0].consumerMapping = ApplicationContext.TableController.buildConsumerMapping(positionsObject);
					positionsField.dataSource.data[0].positionNamedMapping = ApplicationContext.TableController.buildPositionNamedMapping(positionsObject);
					ApplicationContext.TableController.updatePositionsCopy(positionsField);

					if (openFidelityPopup) {
						self.openTableFidelity(formWidget, positionsField, radioTablePositions, fidelitySearch);
					}
				});
			} else {
				positionsField.isVisible = false;
			}
		});
	};

	this.handlePositionsRadioChangeAccount = function (widgetCloseAccount) {
		var positionsField = widgetCloseAccount.getField('positionsField');
		var radioTablePositions = widgetCloseAccount.getField('radioTablePositions');

		var topMargin = parseInt($('.zh-widget-accountItemsTable').css('top'));

		TableActiveTable.findOne().then(function (activeTable) {
			if (radioTablePositions.value() === 'P') {
				positionsField.isVisible = true;
				positionsField.dataSource.data[0].NRPOSICAOMESA = activeTable.NRPOSICAOMESA;
				$('.zh-widget-accountItemsTable').css('top', topMargin + 55 + 'px');
			} else {
				positionsField.isVisible = false;
				$('.zh-widget-accountItemsTable').css('top', topMargin - 55 + 'px');
				WaiterNamedPositionsState.unselectAllPositions();
				this.refreshAccountDetails(widgetCloseAccount.widgets, '', positionsField, true);
			}
			self.setActionLabel(positionsField);
		}.bind(self));
	};

	this.handleCloseTablePositionChange = function (positionsField) {
		TableActiveTable.findOne().then(function (activeTable) {
			TableService.positionControl(activeTable.NRVENDAREST, positionsField.newPosition + 1, !~positionsField.position.indexOf(positionsField.newPosition), positionsField.position).then(function (result) {
				if (result[0].message == null) {
					self.showPositionActions(positionsField);
					if (positionsField.position.length > 0) {
						positionsField.widget.fields[0].setValue('P');
						self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
					}
					else {
						positionsField.widget.fields[0].setValue('M');
						self.refreshAccountDetails(positionsField.widget.widgets, '', positionsField, true);
					}
					self.setActionLabel(positionsField);
				}
				else {
					positionsField._buttons[positionsField.newPosition].selected = false;
					positionsField.position.pop(positionsField.newPosition);
					if (positionsField.position.length == 0) {
						self.hidePositionActions(positionsField);
					}
				}
			});
		});
	};

	this.showPositionActions = function (positionsField) {
		OperatorRepository.findOne().then(function (operatorData) {
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('changePositions').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('transfer').isVisible = true;
			positionsField.widget.container.getWidget('accountItemsTable').getAction('pagar').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getAction('partialPrint').isVisible = true;
			positionsField.widget.container.getWidget('accountDetailsTable').getField('servicoBtn').isVisible = operatorData.IDCOMISVENDA == "S";
		});
	};

	this.hidePositionActions = function (positionsField) {
		positionsField.widget.container.getWidget('accountDetailsTable').setCurrentRow({});
		positionsField.widget.container.getWidget('accountDetailsTable').getAction('changePositions').isVisible = false;
		positionsField.widget.container.getWidget('accountDetailsTable').getAction('pagar').isVisible = false;
		positionsField.widget.container.getWidget('accountDetailsTable').getAction('partialPrint').isVisible = false;
		positionsField.widget.container.getWidget('accountItemsTable').dataSource.data = [];
		positionsField.widget.container.getWidget('accountItemsTable').getAction('transfer').isVisible = false;
		positionsField.widget.container.getWidget('accountItemsTable').getAction('pagar').isVisible = false;
		positionsField.widget.container.getWidget('accountDetailsTable').getField('servicoBtn').isVisible = false;
	};

	this.setActionLabel = function (positionsField) {
		var isMesa = positionsField.widget.currentRow.radioTablePositions === 'M';
		var newLabel;
		var actionPagar1 = positionsField.widget.widgets[0].getAction('pagar');
		var actionPagar2 = positionsField.widget.widgets[1].getAction('pagar');
		var fullTable;

		OperatorRepository.findOne().then(function (operatorData) {
			newLabel = operatorData.IDCOLETOR === 'C' ? 'Adiantar' : 'Receber';

			actionPagar1.label = newLabel;
			actionPagar2.label = newLabel;
			// se true, pagamento é inicializado com todas as posições
			fullTable = isMesa ? "true" : "false";
			actionPagar1.events[0].code = "AccountController.openPayment(" + fullTable + ");";
			actionPagar2.events[0].code = "AccountController.openPayment(" + fullTable + ");";
		});
	};

	this.accountChangeClientConsumer = function (formWidget, positionsField, radioTablePositions) {
		self.changeClientConsumer(formWidget, positionsField, radioTablePositions, true).then(function (changeClientConsumerResponse) {
			fidelitySearch = changeClientConsumerResponse.AccountChangeClientConsumer.fidelitySearch;
			var openFidelityPopup = false;
			if (_.isEmpty(fidelitySearch)) {
				self.finishChangeClientConsumer(formWidget, positionsField, openFidelityPopup, fidelitySearch);
				ScreenService.closePopup();
			}
			else {
				ScreenService.confirmMessage("Deseja utilizar Crédito Fidelidade para este consumidor?", "question",
					function () {
						openFidelityPopup = true;
						self.finishChangeClientConsumer(formWidget, positionsField, openFidelityPopup, fidelitySearch);
					},
					function () {
						self.finishChangeClientConsumer(formWidget, positionsField, openFidelityPopup, fidelitySearch);
						ScreenService.closePopup();
					}
				);
			}
		});
	};

	this.finishChangeClientConsumer = function (formWidget, positionsField, openFidelityPopup, fidelitySearch) {
		var accountDetailsWidget = formWidget.container.getWidget('accountDetails');
		if (accountDetailsWidget) {
			accountDetailsWidget.activate();
			ApplicationContext.TableController.buildPositionsObject(positionsField);
			if (positionsField.widget.fields[0].value() === "M") {
				self.refreshAccountDetails(accountDetailsWidget.widgets, '', true);
			}
			else {
				positionsField._isStatusChanged = true;
				self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
			}
			self.prepareAccountClosingWidget(accountDetailsWidget, formWidget, openFidelityPopup, fidelitySearch);
		}
	};

	this.changeClientConsumer = function (formWidget, positionsField, radioTablePositions, fidelitySearch) {
		var positionsObject = [];
		var currentRow = formWidget.currentRow;

		if (radioTablePositions.value() === 'P') {
			positionsObject = ApplicationContext.TableController.buildPositionsObject(positionsField);
		}
		if (currentRow.CDCLIENTE === '') {
			currentRow.CDCLIENTE = null;
			currentRow.NMRAZSOCCLIE = null;
			currentRow.CDCONSUMIDOR = null;
			currentRow.NMCONSUMIDOR = null;
		}
		if (currentRow.CDCONSUMIDOR === '') {
			currentRow.CDCONSUMIDOR = null;
			currentRow.NMCONSUMIDOR = null;
		}
		return OperatorRepository.findOne().then(function (operatorData) {
			return TableActiveTable.findOne().then(function (activeTable) {
				return AccountService.changeClientConsumer(
					operatorData.chave,
					activeTable.NRVENDAREST,
					activeTable.NRCOMANDA,
					positionsObject,
					currentRow.CDCLIENTE,
					currentRow.CDCONSUMIDOR,
					fidelitySearch
				);
			});
		});
	};

	this.handleModeOrder = function (stripe) {
		// altera actions dependendo do modo habilitado
		OperatorRepository.findOne().then(function (operatorData) {
			var transmitirAction = stripe.getAction('transmitir');
			var pagamentoAction = stripe.getAction('pagamento');
			var cancelarAction = stripe.getAction('cancelar');
			var concluirAction = stripe.getAction('concluir');
			var conferirAction = stripe.getAction('conferir');
			var continueAction = stripe.getAction('continue');

			if (operatorData.modoHabilitado !== 'B') {
				var showContinue = operatorData.IDAGRUPAPEDCOM == 'S' && operatorData.modoHabilitado == 'C';
				continueAction.isVisible = showContinue;

				/* Esconde o botão de transmitir em celulares. */
				pagamentoAction.isVisible = false;
				cancelarAction.isVisible = !operatorData.continueOrdering && !showContinue && operatorData.modoHabilitado == 'C';
				// Essa é a menos importante das actions do meio.
				transmitirAction.isVisible = !showContinue && !cancelarAction.isVisible;
				concluirAction.isVisible = !operatorData.continueOrdering;
				conferirAction.isVisible = operatorData.continueOrdering && operatorData.modoHabilitado == 'C';

				if (!(operatorData.modoHabilitado === 'C' || operatorData.modoHabilitado === 'O' ||
					operatorData.IDLUGARMESA === 'N' || operatorData.modoHabilitado === 'B')) {
					stripe.container.getWidget('checkOrder').groupProp = 'POSITION';
				}
			} else {
				pagamentoAction.label = "Receber";
				transmitirAction.isVisible = false;
				pagamentoAction.isVisible = true;
				cancelarAction.isVisible = true;
				concluirAction.isVisible = false;
				conferirAction.isVisible = false;
			}
		});
	};

	this.cancelOrder = function () {
		ScreenService.confirmMessage(
			'Deseja cancelar o pedido?',
			'question',
			function () {
				self.accountCartClear();
			},
			function () { }
		);
	};

	this.finishPayAccount = function () {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.modoHabilitado === Mode.BALCONY) {
				AccountCart.remove(Query.build());
			}

			UtilitiesService.backMainScreen();
		});
	};

	this.openAddition = function (accountPaymentNamedWidget) {
		if (accountPaymentNamedWidget.currentRow.vlrprodcobtaxa > 0) {
			PermissionService.checkAccess('retirarTaxaServico').then(function (CDSUPERVISOR) {
				accountPaymentNamedWidget.currentRow.CDSUPERVISOR = CDSUPERVISOR;
				var additionPopup = accountPaymentNamedWidget.container.getWidget('additionPopup');
				ScreenService.openPopup(additionPopup).then(function () {
					accountPaymentNamedWidget.currentRow.TIPOGORJETA = 'V';
					additionPopup.setCurrentRow(accountPaymentNamedWidget.currentRow);
				});
			}.bind(this));
		} else {
			ScreenService.showMessage("Não é possível aplicar taxa de serviço para uma mesa sem produtos pedidos.");
		}
	};

	this.applyAddition = function (additionPopup) {
		if (additionPopup.isValid()) {
			if (ApplicationContext.PaymentController.validValue(additionPopup.getField('vlrservico'), '')) {
				AccountGetAccountDetails.findOne().then(function (accountDetails) {
					var vlrProdLiq = parseFloat((accountDetails.vlrprodutos - accountDetails.vlrdesconto - accountDetails.fidelityDiscount).toFixed(2));
					var vlrservico = UtilitiesService.removeCurrency(additionPopup.getField('vlrservico').value());
					var TIPOGORJETA = additionPopup.getField('TIPOGORJETA').value();
					var VRCOUVERT = parseFloat(accountDetails.vlrcouvert);

					var VRACRESCIMO = TIPOGORJETA === 'V' ? vlrservico : parseFloat((vlrProdLiq * (vlrservico / 100)).toFixed(2));
					if (accountDetails.vlrservico != VRACRESCIMO) {
						accountDetails.CDSUPERVISORs = additionPopup.currentRow.CDSUPERVISOR;
						if (VRACRESCIMO == 0) {
							accountDetails.logServico = 'RET_TAX';
						} else if (VRACRESCIMO > accountDetails.vlrservico) {
							accountDetails.logServico = 'ADD_TAX';
						} else {
							accountDetails.logServico = 'ALT_TAX';
						}
						accountDetails.vlrservico = VRACRESCIMO;
						accountDetails.vlrtotal = VRACRESCIMO + vlrProdLiq + VRCOUVERT;
						// valores a mostra para o usuário
						accountDetails.servico = UtilitiesService.formatFloat(VRACRESCIMO);
						accountDetails.total = UtilitiesService.formatFloat(accountDetails.vlrtotal);

						AccountGetAccountDetails.save(accountDetails);
					}
					self.getAccountData(function (accountData) {
						AccountService.updateServiceTax(accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, vlrProdLiq, vlrservico, TIPOGORJETA).then(function () {
							// atualiza as informações na tela de finaliza a alteração
							additionPopup.container.getWidget('accountDetailsTable').currentRow = accountDetails;
							self.closeAddition(additionPopup);
						});
					});
				});
			}
		}
	};

	this.closeAddition = function (widget) {
		ScreenService.closePopup();
		widget.container.getWidget('accountDetailsTable').activate();
	};

	this.showCheckOrderScreenCartPool = function (operatorData) {
		this.showCheckOrderScreen(operatorData).then(function (something) {
			var updateScreen = function updateScreen() {
				return templateManager.updateTemplate().then(function (something) {
					self.checkCartPool(templateManager.container);
				});
			};
			setTimeout(updateScreen, 800);
		});
	};

	this.checkCartPool = function (checkOrderContainer) {
		var checkOrderWidget = checkOrderContainer.getWidget('checkOrder');
		var stripe = checkOrderContainer.getWidget('checkOrderStripe');

		stripe.getAction('transmitir').isVisible = false;
		stripe.getAction('cancelar').isVisible = true;
		stripe.getAction('concluir').isVisible = true;
		stripe.getAction('conferir').isVisible = false;
		stripe.getAction('continue').isVisible = false;

		AccountCart.findAll().then(function (cart) {
			CartPool.findAll().then(function (cartPool) {
				cart = self.filterCartPool(cart, cartPool);
				cartPool = cartPool.concat(cart);
				CartPool.save(cartPool);
				checkOrderWidget.groupProp = 'DSCOMANDA';
				checkOrderWidget.dataSource.data = cartPool;
				self.prepareCart(checkOrderWidget, stripe);
			});
		});
	};

	this.accountCartClear = function () {
		AccountCart.findAll().then(function (cartAccount) {
			CartPool.findAll().then(function (cartPool) {
				OperatorRepository.findOne().then(function (operatorData) {
					var filteredCart = self.filterCartPool(cartAccount, cartPool);
					self.produtosDesistencia(filteredCart);
					var promises = [];
					var AccountCartClear = AccountCart.remove(Query.build());
					promises.push(AccountCartClear);
					if (operatorData.continueOrdering) {
						self.produtosDesistencia(cartPool);
						var CartPoolClear = CartPool.remove(Query.build());
						promises.push(CartPoolClear);
						operatorData.continueOrdering = false;
						var OperatorRepositorySave = OperatorRepository.save(operatorData);
						promises.push(OperatorRepositorySave);
					}
					ZHPromise.all(promises).then(function () {
						UtilitiesService.backMainScreen();
					});
				});
			});
		});
	};

	this.filterCartPool = function (cart, cartPool) {
		var filteredCart = [];
		cart.forEach(function (cartItem) {
			var founded = false;
			cartPool.forEach(function (cartPoolItem) {
				if (cartPoolItem.IDENTIFYKEY == cartItem.IDENTIFYKEY) {
					founded = true;
				}
			});

			if (!founded)
				filteredCart.push(cartItem);
		});

		return filteredCart;
	};

	this.openDiscount = function (widget) {
		PermissionService.checkAccess('cupomDesconto').then(function (CDSUPERVISOR) {
			var discountPopup = widget.container.getWidget('discountPopup');
			discountPopup.currentRow.CDSUPERVISOR = CDSUPERVISOR;

			ScreenService.openPopup(discountPopup).then(function () {
				self.getDiscount(discountPopup);
			}.bind(this));
		}.bind(this));
	};

	this.getDiscount = function (discountPopup) {
		AccountGetAccountDetails.findOne().then(function (accountDetails) {
			var productField = discountPopup.getField('PRODUCTSONACCOUNT');

			// para este desconto, o valor setado nunca será em porcentagem
			discountPopup.getField('TIPODESCONTO').setValue('V');
			discountPopup.getField('VRDESCONTO').setValue(accountDetails.vlrdesconto);
			productField.reload();
			productField.clearValue();
			self.handleDiscountRadioChange(discountPopup);
		});
	};

	this.handleDiscountRadioChange = function (discountPopup) {
		var valueDiscountField = discountPopup.getField('VRDESCONTO');

		AccountGetAccountDetails.findOne().then(function (accountDetails) {
			if (discountPopup.getField('TIPODESCONTO').value() === 'P') {
				valueDiscountField.label = 'Porcentagem';
				valueDiscountField.range.max = 99.99;
			} else {
				valueDiscountField.label = 'Valor';
				valueDiscountField.range.max = accountDetails.vlrprodutos;
			}
		});
	};

	this.cancelDiscount = function (discountPopup) {
		var valueProductField = discountPopup.getField('PRODUCTSONACCOUNT').value();
		discountPopup.getField('MOTIVODESCONTO').clearValue();
		discountPopup.getField('CDOCORR').clearValue();

		if (!_.isEmpty(valueProductField)) {
			ScreenService.confirmMessage(
				'Deseja limpar o desconto dos produtos selecionados?', 'question',
				function () {
					// chama função que aplica desconto com o valor 0
					self.changeProductDiscount(discountPopup, 0, 'V', valueProductField);
				}.bind(this),
				function () { }
			);
		} else {
			ScreenService.showMessage('Selecione um ou mais produtos para limpar seu desconto.', 'alert');
		}
	};

	this.changeProductDiscount = function (discountPopup, VRDESCONTO, TIPODESCONTO, NRPRODCOMVEN) {
		self.getAccountData(function (accountData) {
			CDGRPOCORDESC = !_.isEmpty(discountPopup.currentRow.CDOCORR) ? discountPopup.currentRow.CDOCORR[0] : null;
			AccountService.changeProductDiscount(accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, VRDESCONTO, TIPODESCONTO, NRPRODCOMVEN, discountPopup.currentRow.CDSUPERVISOR, discountPopup.currentRow.MOTIVODESCONTO, CDGRPOCORDESC).then(function (changeProductDiscountResult) {
				ScreenService.closePopup();
				self.prepareAccountDetails(discountPopup.container.getWidget('closeAccount'));
			});
		});
	};

	this.applyDiscount = function (discountPopup) {
		var valueDiscountField = discountPopup.getField('VRDESCONTO');

		if (discountPopup.isValid()) {
			// valida valor de entrada do desconto
			if (ApplicationContext.PaymentController.validValue(valueDiscountField, '')) {
				discountPopup.currentRow.VRDESCONTO = UtilitiesService.removeCurrency(discountPopup.currentRow.VRDESCONTO);

				// define se será possível aplicar desconto para os produtos selecionados
				self.handleSetDiscount(discountPopup).then(function (_) {
					self.changeProductDiscount(discountPopup, discountPopup.currentRow.VRDESCONTO, discountPopup.currentRow.TIPODESCONTO, discountPopup.currentRow.PRODUCTSONACCOUNT);
				}, function (errorMessage) {
					ScreenService.showMessage(errorMessage, 'alert');
				});
			}
		}
	};

	this.handleSetDiscount = function (discountPopup) {
		return new Promise(function (resolve, reject) {
			ParamsParameterRepository.findOne().then(function (params) {
				var products = discountPopup.getField('PRODUCTSONACCOUNT').dataSource.data;
				var TIPODESCONTO = discountPopup.currentRow.TIPODESCONTO;
				var VRDESCONTO = TIPODESCONTO === 'P' ? parseFloat((discountPopup.currentRow.VRDESCONTO / 100).toFixed(4)) : discountPopup.currentRow.VRDESCONTO;

				// filtra produtos escolhidos no desconto
				products = _.filter(products, function (product) {
					return _.includes(discountPopup.currentRow.PRODUCTSONACCOUNT, product.NRPRODCOMVEN);
				});

				for (var i = 0; i < products.length; i++) {
					var preco = UtilitiesService.truncValue(products[i].VRPRECCOMVEN * products[i].QTPRODCOMVEN);
					var currentDiscount = 0;

					if (TIPODESCONTO === 'P') {
						currentDiscount = UtilitiesService.truncValue(VRDESCONTO * preco);
						percentdesc = VRDESCONTO;
					} else {
						currentDiscount = VRDESCONTO;
						percentdesc = VRDESCONTO / preco * 100;
					}

					if (parseFloat((preco - currentDiscount).toFixed(2)) <= 0) {
						reject('O valor do desconto não pode ser igual ou maior que o valor total da venda.');
						return;
					} else if (percentdesc > params.VRMAXDESCONTO && params.VRMAXDESCONTO > 0) {
						reject('Operação bloqueada. Valor de desconto maior que percentual máximo permitido.');
						return;
					}
				}

				resolve();
			}.bind(this), reject);
		}.bind(this));
	};

	this.changeLabelContainer = function (container) {
		OperatorRepository.findOne().then(function (operatorData) {
			container.label = operatorData.modoHabilitado === 'C' ? "Receber Comanda" : "Receber Mesa";
		});
	};

	this.getConsumerBalance = function (widget) {
		if (widget.currentRow.CDCLIENTE == null || widget.currentRow.CDCONSUMIDOR == null) {
			ScreenService.showMessage("Informe o consumidor.");
			return;
		}

		OperatorRepository.findOne().then(function (params) {
			AccountService.getConsumerBalance(params.chave, widget.currentRow.CDCLIENTE, widget.currentRow.CDCONSUMIDOR).then(function () {
				widget.widgets[0].reload().then(function () {
					ScreenService.openPopup(widget.widgets[0]);
				});
			}.bind(this));
		}.bind(this));
	};

	this.clientCustomerVisibility = function (widget) {
		widget.currentRow.CDCLIENTE = "";
		widget.currentRow.CDCONSUMIDOR = "";
		widget.getField('NMRAZSOCCLIE').clearValue();
		widget.getField('NMCONSUMIDOR').clearValue();

		widget.getAction('qrcode').isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();
	};

	this.prepareCreditCharge = function (widget) {
		widget.currentRow.CDFAMILISALD = "";
		widget.currentRow.VRRECARGA = null;

		widget.getField('NMFAMILISALD').clearValue();
		widget.getField('VRRECARGA').clearValue();

		var familiesSelect = widget.getField('NMFAMILISALD');
		if (familiesSelect.dataSource.data.length == 1) {
			familiesSelect.widget.currentRow.CDFAMILISALD = familiesSelect.dataSource.data[0].CDFAMILISALD;
			familiesSelect.widget.currentRow.NMFAMILISALD = familiesSelect.dataSource.data[0].NMFAMILISALD;
			familiesSelect.widget.currentRow.IDPERMCARGACRED = familiesSelect.dataSource.data[0].IDPERMCARGACRED;
			familiesSelect.setValue(familiesSelect.dataSource.data[0].NMFAMILISALD);
		}
		self.clientCustomerVisibility(widget);
	};

	this.prepareCancelCredit = function (widget) {
		widget.currentRow.NRDEPOSICONS = "";
		widget.getField('NRDEPOSICONS').clearValue();
		self.clientCustomerVisibility(widget);
	};

	this.cancelPersonalCredit = function (cancelDetails, depositPopup, NRSEQMOVCAIXA) {
		if (this.checkCancelDetails(cancelDetails)) {
			OperatorRepository.findOne().then(function (params) {
				AccountService.cancelPersonalCredit(params.chave, cancelDetails.CDCLIENTE, cancelDetails.CDCONSUMIDOR, cancelDetails.NRDEPOSICONS, NRSEQMOVCAIXA, null).then(function (cancelResult) {
					if (cancelResult.nothing) {
						depositPopup.currentRow.CDCLIENTE = cancelDetails.CDCLIENTE;
						depositPopup.currentRow.CDCONSUMIDOR = cancelDetails.CDCONSUMIDOR;
						depositPopup.currentRow.NRDEPOSICONS = cancelDetails.NRDEPOSICONS;
						depositPopup.currentRow.NRSEQMOVCAIXA = null;
						depositPopup.getField('NMTIPORECE').clearValue();
						depositPopup.getField('NMTIPORECE').dataSource.data = cancelResult.CancelCreditRepository;
						ScreenService.openPopup(depositPopup);
					}
					else {
						if (cancelResult.length == 0) {
							ScreenService.confirmMessage(
								'O consumidor informado não possui saldo suficiente para efetuar este cancelamento. Sendo que se a resposta for sim o saldo do consumidor ficará negativo. Deseja continuar?',
								'question',
								function () {
									AccountService.cancelPersonalCredit(params.chave, cancelDetails.CDCLIENTE, cancelDetails.CDCONSUMIDOR, cancelDetails.NRDEPOSICONS, NRSEQMOVCAIXA, true).then(function (cancelResult) {
										self.handleCancelResult(cancelResult);
									});
								},
								function () { }
							);
						}
						else {
							self.handleCancelResult(cancelResult);
						}
					}
				});
			});
		}
	};

	this.checkCancelDetails = function (cancelDetails) {
		if (cancelDetails.CDCLIENTE == null || cancelDetails.CDCLIENTE.length == 0 ||
			cancelDetails.CDCONSUMIDOR == null || cancelDetails.CDCONSUMIDOR.length == 0 ||
			cancelDetails.NRDEPOSICONS == null) {
			ScreenService.showMessage("Favor preencher todos os campos.");
			return false;
		}
		else if (isNaN(cancelDetails.NRDEPOSICONS) || cancelDetails.VRRECARGA <= 0) {
			ScreenService.showMessage("Favor informar um número de depósito válido.");
			return false;
		}
		else {
			return true;
		}
	};

	this.handleCancelResult = function (cancelResult) {
		if (cancelResult[0].dadosImpressao != null) {
			// Impressão front-end.
		}
		UtilitiesService.backMainScreen();
	};

	this.priceUpdate = function (product, callback) {
		var defer = ZHPromise.defer();
		OperatorRepository.findOne().then(function (operatorData) {
			AccountCart.findAll().then(function (cart) {
				if (cart.length > 0 || operatorData.modoHabilitado == 'M') {
					defer.resolve(callback(false));
				}
				else {
					ParamsPriceTimeRepository.findOne().then(
						function (nextUpdateTime) {
							var d = new Date();
							var currentTime = parseInt(d.getTime() / 1000);
							if (currentTime >= nextUpdateTime.nextUpdateTime) {
								ScreenService.changeLoadingMessage("Atualizando preços. Aguarde...");
								AccountService.updatePrices(operatorData.chave).then(function (updateResult) {
									ScreenService.restoreDefaultLoadingMessage();
									defer.resolve(callback(updateResult.ParamsMenuRepository.filter(function (p) {
										return p.CDPRODUTO == product.CDPRODUTO;
									})));
								});
							}
							else {
								defer.resolve(callback(false));
							}
						},
						function () {
							ScreenService.restoreDefaultLoadingMessage();
						}
					);
				}
			});
		});
		return defer.promise;
	};

	/* ****************************************** */
	/* **  PERSONAL CREDIT TRANSFER FUNCTIONS  ** */
	/* ****************************************** */

	this.prepareTransferCredit = function (widget) {
		widget.currentRow.CDCLIENTE = [];
		widget.currentRow.CDCONSUMIDOR = [];
		widget.currentRow.CDFAMILISALD = [];
		widget.currentRow.CDIDCONSUMID = [];
		widget.currentRow.VRSALDCONEXT = [];
		widget.currentRow.NMCONSUMIDOR = [];
		widget.currentRow.selectedCards = [];
		widget.currentRow.transferClient = null;
		widget.currentRow.transferType = null;

		widget.getField('cardSearchOri').clearValue();
		widget.getField('cardSearchDest').clearValue();
		widget.getField('selectedCards').dataSource.data = [];

		widget.getField('destConsumer').clearValue();
		widget.getField('destType').clearValue();

		self.cleanDestinationValues(widget);
		widget.getField('transferValue').setValue("R$ 0,00");

		if (Util.isDesktop()) {
			widget.getField('qrcodeOrig').isVisible = false;
			widget.getField('qrcodeDest').isVisible = false;
			widget.getField('cardSearchOri').class = 9;
			widget.getField('cardSearchDest').class = 9;
		}
	};

	this.cardSearch = function (widget, searchValue, mode) {
		if (!searchValue) {
			ScreenService.showMessage("Informe um cartão.");
			return;
		}

		AccountService.cardSearch(searchValue).then(function (searchResult) {
			if (!searchResult.length) {
				ScreenService.showMessage("Cartão não encontrado.");
				return;
			}

			if (mode === "ORIG") {
				widget.widgets[0].reload().then(function () {
					if (searchResult.length == 1) {
						widget.widgets[0].dataSource.data[0].__isSelected = true;
					}
					ScreenService.openPopup(widget.widgets[0]);
				});
			}
			else {
				if (searchResult.length == 1) {
					widget.currentRow.destID = searchResult[0].ID;
					widget.currentRow.destConsumer = searchResult[0].NMCONSUMIDOR;
					widget.currentRow.destType = searchResult[0].NMTIPOCONS;
					widget.currentRow.destCDCLIENTE = searchResult[0].CDCLIENTE;
					widget.currentRow.destCDCONSUMIDOR = searchResult[0].CDCONSUMIDOR;
					widget.currentRow.destCDFAMILISALD = searchResult[0].CDFAMILISALD;
					widget.currentRow.destCDIDCONSUMID = searchResult[0].CDIDCONSUMID;
					widget.currentRow.destVRSALDCONEXT = searchResult[0].VRSALDCONEXT;
					widget.currentRow.destCDTIPOCONS = searchResult[0].CDTIPOCONS;
					widget.currentRow.destIDSITCONSUMI = searchResult[0].IDSITCONSUMI;
					widget.currentRow.destIDPERTRANSALD = searchResult[0].IDPERTRANSALD;
					this.selectDestCard(widget);
				}
				else if (searchResult.length > 1) {
					widget.getField('familyDest').reload();
					widget.getField('familyDest').openField();
				}
			}
		}.bind(this));
	};

	this.selectOrigCard = function (selectionWidget, widget, selectedCards) {
		if (_.isEmpty(widget.currentRow.selectedCards)) {
			widget.currentRow.transferClient = null;
			widget.currentRow.transferType = null;
		}

		var selectedFamilies = selectionWidget.dataSource.data.filter(function (selection) {
			return selection.__isSelected;
		}.bind(selectedCards));

		for (var i in selectedFamilies) {
			if (!self.validateOriginCard(widget, selectedFamilies[i])) {
				return;
			}
		}

		var exists;
		selectedFamilies.forEach(function (family) {
			exists = selectedCards.dataSource.data.some(function (card) {
				return card.ID == family.ID;
			});
			if (!exists) {
				family.VRSALDCONEXT = UtilitiesService.formatFloat(family.VRSALDCONEXT);
				selectedCards.dataSource.data.push(family);
				widget.currentRow.CDCLIENTE.push(family.CDCLIENTE);
				widget.currentRow.CDCONSUMIDOR.push(family.CDCONSUMIDOR);
				widget.currentRow.CDFAMILISALD.push(family.CDFAMILISALD);
				widget.currentRow.CDIDCONSUMID.push(family.CDIDCONSUMID);
				widget.currentRow.VRSALDCONEXT.push(family.VRSALDCONEXT);
				widget.currentRow.NMCONSUMIDOR.push(family.NMCONSUMIDOR);
				widget.currentRow.selectedCards.push(family.ID);
			}
			else {
				selectedCards.dataSource.data.forEach(function (card) {
					if (card.ID == family.ID) {
						card.__isSelected = true;
					}
				});
			}
		});

		if (selectedFamilies.length > 0) {
			self.handleOriginChange(widget, true);
			widget.getField('cardSearchOri').clearValue();
			ScreenService.closePopup();
		}
		else {
			ScreenService.showMessage("Marque pelo menos uma opção.");
		}
	};

	this.validateOriginCard = function (widget, family) {
		if (widget.currentRow.transferClient == null) {
			widget.currentRow.transferClient = family.CDCLIENTE;
		}
		else {
			if (widget.currentRow.transferClient != family.CDCLIENTE) {
				ScreenService.showMessage("Só é possível realizar transferências entre cartões de cliente iguais.");
				return false;
			}
		}

		if (widget.currentRow.transferType == null) {
			widget.currentRow.transferType = family.CDTIPOCONS;
		}
		else {
			if (widget.currentRow.transferType != family.CDTIPOCONS) {
				ScreenService.showMessage("Só é possível realizar transferências entre o mesmo tipo de cliente.");
				return false;
			}
		}

		if (family.VRSALDCONEXT <= 0) {
			ScreenService.showMessage("Não é possível transferir de cartões que não possuem saldo.");
			return false;
		}

		if (family.IDSITCONSUMI != '1') {
			ScreenService.showMessage("Um ou mais dos cartões selecionados encontra-se inativo.");
			return false;
		}

		if (family.IDPERTRANSALD != 'S') {
			ScreenService.showMessage("Não é permitido transferir saldo deste tipo de consumidor.");
			return false;
		}

		if (widget.currentRow.destCDCLIENTE != null) {
			if (widget.currentRow.destID == family.ID) {
				ScreenService.showMessage("Cartão/familia de origem não pode ser igual ao de destino.");
				return false;
			}
			if (widget.currentRow.destCDCLIENTE != family.CDCLIENTE) {
				ScreenService.showMessage("Cliente do cartão de origem difere ao do cartão de destino escolhido.");
				return false;
			}
			if (widget.currentRow.destCDTIPOCONS != family.CDTIPOCONS) {
				ScreenService.showMessage("Tipo de consumidor do cartão de origem difere ao do cartão de destino escolhido.");
				return false;
			}
		}

		return true;
	};

	this.handleOriginChange = function (widget, selectControl) {
		var selectedCardsField = widget.getField('selectedCards');
		var newCards = Array();

		selectedCardsField.dataSource.data.forEach(function (card) {
			if (card.__isSelected) {
				newCards.push(card);
			}
		});
		if (selectControl && !!selectedCardsField.selectWidget) {
			selectedCardsField.selectWidget.setSelected(newCards, selectedCardsField);
		} else {
			selectedCardsField.dataSource.data = newCards;
		}

		self.calculateTransferValues(widget);
	};

	this.selectDestCard = function (widget) {
		if (self.validateDestinationCard(widget.currentRow, widget.getField('selectedCards').dataSource.data)) {
			self.calculateTransferValues(widget);
		}
		else {
			self.cleanDestinationValues(widget);
		}
	};

	this.cleanDestinationValues = function (widget) {
		widget.currentRow.destID = null;
		widget.currentRow.destConsumer = null;
		widget.currentRow.destType = null;
		widget.currentRow.destCDCLIENTE = null;
		widget.currentRow.destCDCONSUMIDOR = null;
		widget.currentRow.destCDFAMILISALD = null;
		widget.currentRow.destCDIDCONSUMID = null;
		widget.currentRow.destVRSALDCONEXT = null;
		widget.currentRow.destCDTIPOCONS = null;
		widget.currentRow.destIDSITCONSUMI = null;
		widget.currentRow.destIDPERTRANSALD = null;
		widget.getField('cardSearchDest').clearValue();
		widget.getField('currentBalance').clearValue();
		widget.getField('finalBalance').clearValue();
		self.calculateTransferValues(widget);
	};

	this.validateDestinationCard = function (row, selectedCards) {
		if (_.isEmpty(selectedCards)) {
			row.transferClient = null;
			row.transferType = null;
		}

		for (var i in selectedCards) {
			if (selectedCards[i].ID == row.destID) {
				ScreenService.showMessage("Cartão/familia de destino não pode ser igual ao de origem.");
				return false;
			}
		}

		if (row.transferClient && row.transferClient != row.destCDCLIENTE) {
			ScreenService.showMessage("Cliente do cartão/familia de destino difere do de origem.");
			return false;
		}

		if (row.transferType && row.transferType != row.destCDTIPOCONS) {
			ScreenService.showMessage("Tipo de consumidor do cartão/familia de destino difere do de origem.");
			return false;
		}

		if (row.destIDSITCONSUMI != '1') {
			ScreenService.showMessage("Este cartão encontra-se inativo.");
			return false;
		}

		if (row.destIDPERTRANSALD != 'S') {
			ScreenService.showMessage("Não é permitido transferir saldo para este tipo de consumidor.");
			return false;
		}

		return true;
	};

	this.calculateTransferValues = function (widget) {
		var transferValue = widget.getField('selectedCards').dataSource.data.reduce(function (total, card) {
			return total + parseFloat(card.VRSALDCONEXT.replace(',', '.'));
		}, 0);
		widget.getField('transferValue').setValue("R$ " + UtilitiesService.formatFloat(transferValue));

		if (widget.currentRow.destVRSALDCONEXT != null) {
			var currentBalance = parseFloat(widget.currentRow.destVRSALDCONEXT);
			var finalBalance = parseFloat(currentBalance + transferValue);
			widget.getField('currentBalance').setValue("R$ " + UtilitiesService.formatFloat(currentBalance));
			widget.getField('finalBalance').setValue("R$ " + UtilitiesService.formatFloat(finalBalance));
		}
		else {
			widget.getField('currentBalance').clearValue();
			widget.getField('finalBalance').clearValue();
		}
	};

	this.transferPersonalCredit = function (widget) {
		if (_.isEmpty(widget.currentRow.selectedCards)) {
			ScreenService.showMessage("Escolha o cartão de origem.");
			return;
		}

		if (_.isEmpty(widget.currentRow.destCDCLIENTE)) {
			ScreenService.showMessage("Escolha o cartão de destino.");
			return;
		}

		OperatorRepository.findOne().then(function (params) {
			AccountService.transferPersonalCredit(params.chave, widget.currentRow).then(function () {
				self.prepareTransferCredit(widget);
			}.bind(this));
		}.bind(this));
	};

	this.clearSelectedCards = function (widget) {
		widget.currentRow.CDCLIENTE = [];
		widget.currentRow.CDCONSUMIDOR = [];
		widget.currentRow.CDFAMILISALD = [];
		widget.currentRow.CDIDCONSUMID = [];
		widget.currentRow.VRSALDCONEXT = [];
		widget.currentRow.NMCONSUMIDOR = [];
		widget.currentRow.selectedCards = [];
		widget.currentRow.transferClient = null;
		widget.currentRow.transferType = null;
		widget.getField('selectedCards').dataSource.data = [];
		self.calculateTransferValues(widget);
	};

	this.scanTransferCard = function (widget, mode) {
		UtilitiesService.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				if (_.isEmpty(qrCode.contents)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				}
				else {
					qrCode = qrCode.contents.replace(/[^A-Z0-9-\/. ]/gi, "");
					if (mode == "ORIG") {
						widget.getField('cardSearchOri').value(qrCode);
					}
					else {
						widget.getField('cardSearchDest').value(qrCode);
					}
					self.cardSearch(widget, qrCode, mode);
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.reloadConsumers = function (field) {
		field.reload();
	};

	this.scanProductQrCode = function (widget) {
		UtilitiesService.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				if (_.isEmpty(qrCode.contents)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				} else {
					OperatorRepository.findOne().then(function (operatorData) {
						var qrCodeScan = (operatorData.IDLCDBARBALATOL == 'S') ?
							qrCode.contents.substr(operatorData.NRPOSINICODBARR, operatorData.NRPOSFINCODBARR - operatorData.NRPOSINICODBARR) : qrCode.contents;
						self.filterProducts(widget, qrCodeScan).then(function (products) {
							if (!_.isEmpty(products)) {
								if (products.length == 1) {
									widget.dataSource.data = products;
									self.handleSelectedProduct(widget, products[0], widget.container.getWidget('positionsWidget').position + 1);
								} else {
									widget.getField('selectProducts').dataSourceFilter = [
										{
											name: 'CDARVPROD|DSBUTTON',
											operator: '=',
											value: qrCodeScan
										}
									];
									widget.getField('selectProducts').reload();
									widget.getField('selectProducts').openField();
								}
							} else {
								ScreenService.showMessage("Produto não encontrado.");
							}
						}.bind(this));
					}.bind(this));
				}
			} else {
				ScreenService.showMessage(qrCode.message, 'alert');
			}
		}.bind(this));
	};

	this.rowSelectedOnProductFilter = function (args) {
		ScreenService.closePopup();
		var widget = args.owner.container.getWidget('products');
		widget.currentRow = args.row;
		self.handleSelectedProduct(widget, args.row, widget.container.getWidget('positionsWidget').position + 1);
	};

    this.handleSelectedProduct = function (widget, product, position){
        OperatorRepository.findOne().then(function (operatorData){
            self.priceUpdate(product, function (result){
                if (result){
                    widget.reload();
                    product = result[0];
                }

                product.HRINIVENPROD = !product.HRINIVENPROD ? 0 : product.HRINIVENPROD;
                product.HRFIMVENPROD = !product.HRFIMVENPROD ? 0 : product.HRFIMVENPROD;

                var validaProduto = self.validateProducts(product, operatorData.IDCOLETOR);
                if (_.isEmpty(validaProduto)){
                    if (product.GRUPOS){ // Produto do cardápio principal.
                        if (!isSmartPromo(product) && product.IDTIPOCOMPPROD !== 'C'){
                            /* - Produto Normal - */
                            self.addToCart(widget.container.getWidget("addProduct"), product, position, widget.container.getWidget("addProduct").getAction("cart"), widget.container.getWidget("menu").getAction("cart"), false, false);
                            ScreenService.closePopup();
                        }
                        else {
                            /* - Promoção Inteligente - */
                            self.buildPromoItem(product, position, false, false, function (refil){
                                buildCartItem(product, position, refil).then(function (cartItem){
                                    // Fixa a quantidade do produto.
                                    cartItem.QTPRODCOMVEN = 1;
                                    // Adiciona o produto pai no carrinho.
                                    AccountCart.save(cartItem).then(function (){
                                        self.openPromoScreen(product, widget, false);
                                        ScreenService.closePopup();
                                    });
                                });
                            });
                        }
                    }
                    else { // Produto dentro de uma promoção.
                        self.addToTray(widget.container.getWidget("addProduct"), product);
                        ScreenService.closePopup();
                    }
                } else {
                    ScreenService.showMessage(validaProduto);
                }
            });
        });
    };

    this.validateProducts = function(product, IDCOLETOR){
        if (product.IDTIPOCOMPPROD !== 'C') {
            if (product.IDPRODBLOQ == 'S') return "Produto bloqueado.";

            var hora = self.getHour();
            if (!((product.HRINIVENPROD == 0) && (product.HRFIMVENPROD == 0)) && ((hora <= product.HRINIVENPROD) || (hora >= product.HRFIMVENPROD)))
                return "Operação Bloqueada. Produto fora do horário permitido para venda.";

            if (product.VRPRECITEM == 0)
                return "Produto sem preço.";

            if (IDCOLETOR !== 'C'){
                var message = 'Produto não pode ser vendido, pois não possui ';
                var validate = {
                    'CDCLASFISC': "NCM",
                    'CDCFOPPFIS': "CFOP",
                    'CDCSTICMS': "CST do ICMS",
                    'VRALIQPIS': "Aliquota do PIS",
                    'CDCSTPISCOF': "CST do PIS/COFINS",
                    'VRALIQCOFINS': "Aliquota do COFINS"
                };

                for (var indexVaL in validate){
                    if (_.isEmpty(product[indexVaL])) {
                        return message + validate[indexVaL] + ' parametrizado.';
                    }
                }
            }
        }

        return null;
    };

    this.getHour = function(){
        var now = new Date();

        return now.getHours() * 100 + now.getMinutes();
    };

    this.prepareCheckBalance = function(widget){
        widget.getField('NMRAZSOCCLIE').clearValue();
        widget.getField('NMCONSUMIDOR').clearValue();
        widget.getAction('qrcode').isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();
    };

	this.filterProducts = function(widget, pesquisa){
		if (widget.isValid()) {
			return AccountService.filterProducts({ 0: pesquisa }).then(function (filterProductResult) {
				if (!_.isEmpty(filterProductResult)) {
					return filterProductResult;
				} else {
					ScreenService.showMessage("Produto não encontrado.");
				}
			}.bind(this));
		}
	};

	var t;
	this.timerSearch = function (widget, pesquisa, timer) {
		clearTimeout(t);
		var timerSearch = function () {
			var field = widget.getField('selectProducts');
			var popup = widget;

			field.clearValue();

			field.dataSourceFilter = [
				{
					name: 'CDARVPROD|DSBUTTON',
					operator: '=',
					value: _.isEmpty(popup.currentRow.filterProducts) ? "%%" : "%" + popup.currentRow.filterProducts + "%"
				}
			];
			field.reload().then(function (search) {
				if (!_.isEmpty(search)) {
					if (widget.name === "BlockProductPopup") {
						if (!_.isEmpty(popup.currentRow.filterProducts)) {
							delete field.selectWidget;
							field.openField();
						}
					}
					else {
						var products = search.dataset.FilterProducts;
						if (products.length == 1) {
							popup.currentRow = products[0];
							popup.getField('selectProducts').setValue(products[0].DSBUTTON);
						} else if (products.length > 1) {
							delete field.selectWidget;
							field.openField();
						}
					}
				}
			}.bind(this));
		}.bind(this);
		t = setTimeout(timerSearch, timer);
	};

	this.setMask = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			widget.fields[0].mask.params.mask = operatorData.CDPICTPROD;
			widget.floatingControl = false;
		});
	};

	this.resetValues = function (widget) {
		widget.getField('filterProducts').clearValue();
		widget.getField('selectProducts').clearValue();
		widget.getField('selectProducts').dataSourceFilter = [
			{
				name: 'CDARVPROD|DSBUTTON',
				operator: '=',
				value: "%%"
			}
		];
		widget.getField('selectProducts').reload();
	};

	this.checkAdd = function (widget, product, position) {
		if (!_.isEmpty(widget.getField('selectProducts').value())) {
			this.handleSelectedProduct(widget, product, position);
		} else {
			ScreenService.showMessage('Nenhum produto selecionado.');
		}
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'discountPopup') {
				this.applyDiscount(widget);
			} else if (widget.name === 'additionPopup') {
				this.applyAddition(widget);
			}
		}
	};

	this.fidelityReturn = function (consumerPopup, modeWidget) {
		TableActiveTable.findOne().then(function (activeTable) {
			if (modeWidget.fields[0].value() === "P") {
				var positionsData = self.handleConsumerPositionsOnPayment(false, Array());
				consumerPopup.currentRow.CDCLIENTE = positionsData.CDCLIENTE;
				consumerPopup.currentRow.NMRAZSOCCLIE = positionsData.NMRAZSOCCLIE;
				consumerPopup.currentRow.CDCONSUMIDOR = positionsData.CDCONSUMIDOR;
				consumerPopup.currentRow.NMCONSUMIDOR = positionsData.NMCONSUMIDOR;
			}
			else if (activeTable.CDCLIENTE) {
				consumerPopup.currentRow.CDCLIENTE = activeTable.CDCLIENTE;
				consumerPopup.currentRow.NMRAZSOCCLIE = activeTable.NMRAZSOCCLIE;
				consumerPopup.currentRow.CDCONSUMIDOR = activeTable.CDCONSUMIDOR;
				consumerPopup.currentRow.NMCONSUMIDOR = activeTable.NMCONSUMIDOR;
			}
			ScreenService.closePopup();
		});
	};

	this.openTableFidelity = function (widget, positionsField, radioTablePositions, fidelitySearch) {
		ApplicationContext.TableController.restorePositionsCopy(positionsField);
		TableActiveTable.findOne().then(function (activeTable) {
			if (radioTablePositions.value() === 'P') {
				var positionsData = self.handleConsumerPositionsOnPayment(false, Array());
				if (_.isEmpty(positionsData.CDCLIENTE) || _.isEmpty(positionsData.CDCONSUMIDOR)) {
					ApplicationContext.TableController.clearConsumerRow(widget.currentRow);
					ScreenService.showMessage('Favor defina um consumidor para a posição antes de continuar.');
					return;
				}
			}
			else {
				if (_.isEmpty(activeTable.CDCLIENTE) || _.isEmpty(activeTable.CDCONSUMIDOR)) {
					ScreenService.showMessage('Favor defina um consumidor para a mesa antes de continuar.');
					return;
				}
			}

			var applyFidelityPopup = function (fidelityDetails) {
				var fidelityWidget = widget.container.getWidget('fidelityPopup');
				fidelityWidget.setCurrentRow({ 'VSALDODISP': fidelityDetails.VRSALDCONEXT, 'IDPERALTDESCFID': fidelityDetails.IDPERALTDESCFID });
				AccountGetAccountDetails.findOne().then(function (accountDetails) {
					var totalCost = Math.round((accountDetails.vlrprodutos - accountDetails.vlrdesconto) * 100) / 100;
					var fieldFidelityValue = fidelityWidget.getField('SALDOAPLICADO');
					var maxValue = (totalCost < fidelityWidget.currentRow.VSALDODISP) ? totalCost : fidelityWidget.currentRow.VSALDODISP;

					fidelityWidget.currentRow.SALDOAPLICADO = (accountDetails.fidelityDiscount > 0 && accountDetails.fidelityDiscount < maxValue) ?
						accountDetails.fidelityDiscount : maxValue;
					fidelityWidget.currentRow.IDCOMISVENDA = fidelityDetails.IDCOMISVENDA;
					fidelityWidget.currentRow.VRCOMISVENDA = fidelityDetails.VRCOMISVENDA;

					fieldFidelityValue.readOnly = fidelityDetails.IDPERALTDESCFID == 'N';
					fieldFidelityValue.range.max = maxValue;
					ScreenService.openPopup(fidelityWidget);
				});

			}.bind(this);
			if (!_.isEmpty(fidelitySearch)) {
				applyFidelityPopup(fidelitySearch);
			} else {
				AccountService.getFidelityDetails(widget.currentRow.CDCLIENTE, widget.currentRow.CDCONSUMIDOR).then(function (fidelityDetails) {
					applyFidelityPopup(fidelityDetails[0]);
				}.bind(this));
			}
		});
	};

	this.confirmTableFidelity = function (widget) {
		if (widget.isValid()) {
			if (ApplicationContext.PaymentController.validValue(widget.getField('SALDOAPLICADO'), '')) {
				self.getAccountData(function (accountData) {
					AccountGetAccountDetails.findOne().then(function (accountDetails) {
						var SALDOAPLICADO = Math.round(UtilitiesService.removeCurrency(widget.getField('SALDOAPLICADO').value()) * 100) / 100;
						// salva desconto na POSVENDAREST para parcial
						AccountService.setDiscountFidelity(accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, accountDetails.posicao, SALDOAPLICADO).then(function () {
							positionsField = widget.container.getWidget('accountDetails').getField('positionsField');
							if (positionsField.position.length > 0) {
								positionsField._isStatusChanged = true;
								positionsField.widget.fields[0].setValue('P');
								self.refreshAccountDetailsMultiplePositions(positionsField.widget.widgets, positionsField.position, positionsField);
							} else {
								positionsField.widget.fields[0].setValue('M');
								self.refreshAccountDetails(positionsField.widget.widgets, '', positionsField, true);
							}
							ScreenService.closePopup(true);
							widget.container.getWidget('accountDetailsTable').activate();
						}.bind(this));
					}.bind(this));
				}.bind(this));
			}
		}
	};

	this.openBalconyFidelity = function (widget) {
		PaymentRepository.findOne().then(function (paymentData) {
			if (_.isEmpty(paymentData.CDCLIENTE) || _.isEmpty(paymentData.CDCONSUMIDOR)) {
				ScreenService.showMessage('Favor defina um consumidor antes de continuar.');
				return;
			}

			AccountService.getFidelityDetails(paymentData.CDCLIENTE, paymentData.CDCONSUMIDOR).then(function (fidelityDetails) {
				var fidelityWidget = widget.container.getWidget('fidelityPopup');
				fidelityWidget.setCurrentRow({ 'VSALDODISP': fidelityDetails[0].VRSALDCONEXT, 'IDPERALTDESCFID': fidelityDetails[0].IDPERALTDESCFID });

				if (paymentData.DATASALE.FIDELITYDISCOUNT > 0) {
					fidelityWidget.currentRow.SALDOAPLICADO = paymentData.DATASALE.FIDELITYDISCOUNT;
				}
				else {
					if (paymentData.DATASALE.TOTALVENDA < fidelityWidget.currentRow.VSALDODISP) {
						fidelityWidget.currentRow.SALDOAPLICADO = paymentData.DATASALE.TOTALVENDA;
					}
					else {
						fidelityWidget.currentRow.SALDOAPLICADO = fidelityWidget.currentRow.VSALDODISP;
					}
				}

				fidelityWidget.getField('SALDOAPLICADO').readOnly = fidelityDetails[0].IDPERALTDESCFID == 'N';
				ScreenService.openPopup(fidelityWidget);
			});
		});
	};

	this.confirmBalconyFidelity = function (widget) {
		if (widget.isValid()) {
			if (ApplicationContext.PaymentController.validValue(widget.getField('SALDOAPLICADO'), '')) {
				PaymentRepository.findOne().then(function (paymentData) {
					var SALDOAPLICADO = UtilitiesService.removeCurrency(widget.getField('SALDOAPLICADO').value());
					SALDOAPLICADO = Math.round(SALDOAPLICADO * 100) / 100;

					var totalCost = paymentData.DATASALE.TOTAL - paymentData.DATASALE.VRDESCONTO;
					totalCost = Math.round(totalCost * 100) / 100;

					if (SALDOAPLICADO > totalCost) SALDOAPLICADO = totalCost;

					var minCost = 0.01 * paymentData.numeroProdutos;
					var maxDiscount = Math.round((totalCost - minCost) * 100) / 100;

					paymentData.DATASALE.FIDELITYVALUE = SALDOAPLICADO;
					if (SALDOAPLICADO >= maxDiscount) {
						paymentData.DATASALE.TOTALVENDA = minCost;
						paymentData.DATASALE.FALTANTE = minCost;
						paymentData.DATASALE.FIDELITYVALUE = maxDiscount;
					}
					else {
						paymentData.DATASALE.TOTALVENDA = paymentData.DATASALE.TOTAL - paymentData.DATASALE.VRDESCONTO - SALDOAPLICADO;
						paymentData.DATASALE.TOTALVENDA = Math.round(paymentData.DATASALE.TOTALVENDA * 100) / 100;
						paymentData.DATASALE.FALTANTE = paymentData.DATASALE.TOTALVENDA;
					}

					paymentData.DATASALE.FIDELITYDISCOUNT = SALDOAPLICADO;

					PaymentRepository.save(paymentData).then(function () {
						ApplicationContext.PaymentController.attScreen(widget);
						ScreenService.closePopup(true);
					});
				});
			}
		}
	};

	this.buildPromoItem = function (product, position, refil, refilBypass, callback) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDCOLETOR != 'R') {
				if (product.PRITEM > 0 || product.IDIMPPRODUTO === '2') {
					/* REFIL MECHANICS */
					if (product.REFIL === 'S' && !refilBypass) {
						if (operatorData.modoHabilitado !== 'B') {
							TableActiveTable.findOne().then(function (table) {
								AccountService.checkRefil(operatorData.chave, table.NRVENDAREST, table.NRCOMANDA, product.CDPRODUTO, position).then(function (refilData) {
									if (refilData.length === 0) {
										self.buildPromoItem(product, position, false, true, callback);
									}
									else {
										ScreenService.confirmMessage(
											'Este produto é um refil?',
											'question',
											function () {
												self.buildPromoItem(product, position, true, true, callback);
											}.bind(this),
											function () {
												self.buildPromoItem(product, position, false, true, callback);
											}.bind(this)
										);
									}
								}.bind(this));
							}.bind(this));
						} else {
							ScreenService.showMessage("Produto refil não pode ser realizado no modo balcão.", "alert");
						}
					} else {
						callback(refil);
					}
				} else {
					ScreenService.showMessage("Produto sem preço.", 'alert');
				}
			} else {
				ScreenService.showMessage("Caixa habilitado apenas para modo recebedor.");
			}
		}.bind(this));
	};

	this.checkProducedProduct = function (row, widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			if (operatorData.IDINFPRODPRODUZ == 'S') {
				ScreenService.confirmMessage('O produto ' + row.DSBUTTON + ' já foi produzido?', 'question', function () {
					self.cancelProduct(row, widget, 'S');
				}.bind(this), function () {
					self.cancelProduct(row, widget, null);
				}.bind(this));
			} else {
				self.cancelProduct(row, widget, null);
			}
		});
	};

	this.handleOpenCharge = function (widget) {
		if (widget.currentRow.vlrprodcobtaxa > 0) {
			PermissionService.checkAccess('retirarTaxaServico').then(function (CDSUPERVISOR) {
				var changeCharge = widget.container.getWidget('changeCharge');
				changeCharge.currentRow.CDSUPERVISOR = CDSUPERVISOR;
				ScreenService.openPopup(changeCharge).then(function () {
					self.handleShowChangeCharge(changeCharge);
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage("Não é possível aplicar taxa de serviço para uma mesa sem produtos pedidos.");
		}
	};

	this.applyCharge = function (widget) {
		if (widget.isValid()) {
			var currentRow = widget.getParent().currentRow;
			var VRCOMISPOR = 0;

			if (widget.getField('radioChargeChange').value() === 'P') {
				VRCOMISPOR = UtilitiesService.getFloat(widget.getField('radioCharge').value());
			} else {
				var vlrservico = UtilitiesService.getFloat(widget.getField('vlrservico').value());

				if (widget.getField('TIPOGORJETA').value() === 'P') {
					VRCOMISPOR = vlrservico;
				} else {
					VRCOMISPOR = Math.trunc((vlrservico / currentRow.vlrprodcobtaxa) * 10000) / 100;
					VRCOMISPOR = self.roundServiceCharge(VRCOMISPOR, vlrservico, currentRow.vlrprodcobtaxa);
				}
			}
			currentRow.vlrservico = Math.trunc(VRCOMISPOR * currentRow.vlrprodcobtaxa) / 100;
			currentRow.swiservico = currentRow.vlrservico == 0 ? false : true;

			widget.dataSource.data[0].value = VRCOMISPOR;

			self.recalcPrice(currentRow);
			ScreenService.closePopup();
		}
	};

	this.roundServiceCharge = function (VRCOMISPOR, vlrservico, totalProd) {
		var aux = 0.01;
		var newVRCOMISPOR = Math.trunc(VRCOMISPOR * totalProd) / 100;

		if (newVRCOMISPOR >= vlrservico) {
			if (newVRCOMISPOR == vlrservico) {
				return VRCOMISPOR;
			}
			else {
				VRCOMISPOR = Math.round((VRCOMISPOR - aux) * 100) / 100;
				return VRCOMISPOR;
			}
		} else {
			return self.roundServiceCharge(Math.round((VRCOMISPOR + aux) * 100) / 100, vlrservico, totalProd);
		}
	};

	this.handleShowChangeCharge = function (widget) {
		var field = widget.getField('radioCharge');
		var newData = Array();

		ParamsParameterRepository.findOne().then(function (paramsRepoReturn) {
			var vrconsu1 = paramsRepoReturn.VRCOMISVENDA;
			var vrconsu2 = paramsRepoReturn.VRCOMISVENDA2;
			var vrconsu3 = paramsRepoReturn.VRCOMISVENDA3;

			if (vrconsu1 == 0 && vrconsu2 == 0) {
				vrconsu2 = null;
			} else if (vrconsu1 == 0 && vrconsu3 == 0) {
				vrconsu3 = null;
			} else if (vrconsu2 == 0 && vrconsu3 == 0) {
				vrconsu3 = null;
			}

			if (vrconsu1 != null) {
				newData.push({
					'value': vrconsu1,
					'name': UtilitiesService.formatFloat(vrconsu1) + '%'
				});
			}
			if (vrconsu2 != null) {
				newData.push({
					'value': vrconsu2,
					'name': UtilitiesService.formatFloat(vrconsu2) + '%'
				});
			}
			if (vrconsu3 != null) {
				newData.push({
					'value': vrconsu3,
					'name': UtilitiesService.formatFloat(vrconsu3) + '%'
				});
			}

			field.dataSource.data = newData;
			field.defaultOption = 0;
		});
	};

	this.radioChargeChange = function (opcao, field1, field2, field3) {
		if (opcao.value() === 'M') {
			field2.isVisible = field3.isVisible = true;
			field1.isVisible = false;
		} else {
			field1.isVisible = true;
			field2.isVisible = field3.isVisible = false;
		}
	};

	this.limpaDesconto = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			// Limpar campo de observacoes de desconto
			widget.getField('MOTIVODESCONTO').clearValue();
			widget.getField('CDOCORR').clearValue();
			// Controla o fieldGroup FIELDS_OBSERVATION de acordo com parametrizacao
			widget.fieldGroups[1].opened = (operatorData.IDSOLOBSDESC === 'S');
		});
	};

	this.handleShowObsDesc = function (widget) {
		if (!_.isEmpty(widget.row.CDOCORR)) {
			widget.row.CDOCORR = [_.last(widget.row.CDOCORR)];
		}
	};

	this.produtosDesistencia = function (cart) {
		OperatorRepository.findOne().then(function (params) {
			var produtos = [];
			cart.forEach(function (cartItems) {
				if (_.isEmpty(cartItems.PRODUTOS) || cartItems.IDIMPPRODUTO === '1') {
					var produto = {};
					produto.NRVENDA = cartItems.NRVENDAREST || null;
					produto.QTPRODITCOMVENDES = cartItems.QTPRODCOMVEN;
					produto.CDPRODUTO = cartItems.CDPRODUTO;
					produto.VRPRECCOMVEN = cartItems.PRITEM;
					produto.VRDESCCOMVEN = cartItems.VRDESITVEND;
					produto.VRACRCOMVEN = cartItems.VRACRITVEND;
					produto.CDOCORR = cartItems.CDOCORR;
					if (cartItems.IDIMPPRODUTO === '1' && !_.isEmpty(cartItems.PRODUTOS)) {
						produto.CDOCORR = produto.CDOCORR.concat(cartItems.PRODUTOS[0].CDOCORR);
					}
					produtos.push(produto);
				} else {
					cartItems.PRODUTOS.forEach(function (cartItemsProducts) {
						if (_.isEmpty(cartItemsProducts.PRODUTOS)) {
							var produto = {};
							produto.NRVENDA = cartItems.NRVENDAREST || null;
							produto.QTPRODITCOMVENDES = cartItems.QTPRODCOMVEN;
							produto.CDPRODUTO = cartItemsProducts.CDPRODUTO;
							produto.VRPRECCOMVEN = cartItemsProducts.PRITEM;
							produto.VRDESCCOMVEN = cartItemsProducts.VRDESITVEND;
							produto.VRACRCOMVEN = cartItemsProducts.VRACRITVEND;
							produto.CDOCORR = cartItemsProducts.CDOCORR;
							produtos.push(produto);
						} else {
							cartItemsProducts.PRODUTOS.forEach(function (cartItemsProdProducts) {
								var produto = {};
								produto.NRVENDA = cartItems.NRVENDAREST || null;
								produto.QTPRODITCOMVENDES = cartItems.QTPRODCOMVEN;
								produto.CDPRODUTO = cartItemsProdProducts.CDPRODUTO;
								produto.VRPRECCOMVEN = cartItemsProdProducts.PRITEM;
								produto.VRDESCCOMVEN = cartItemsProdProducts.VRDESITVEND;
								produto.VRACRCOMVEN = cartItemsProdProducts.VRACRITVEND;
								produto.CDOCORR = cartItemsProdProducts.CDOCORR;
								produtos.push(produto);
							});
						}
					});
				}
			});
			if (params.modoHabilitado !== 'B') {
				AccountService.produtosDesistencia(produtos);
			} else {
				CarrinhoDesistencia.findAll().then(function (carrinhoDesistencia) {
					if (!carrinhoDesistencia) {
						carrinhoDesistencia = [];
					}
					CarrinhoDesistencia.save(carrinhoDesistencia.concat(produtos));
				});
			}
		});
	};

}

Configuration(function (ContextRegister) {
	ContextRegister.register('AccountController', AccountController);
});