function TableController($rootScope, PermissionService, TableService, TableRepository, OperatorService, OperatorRepository, AccountController, AccountService, ParamsAreaRepository, ScreenService, AccountCart, Query, AccountGetAccountItems, ParamsClientRepository, TableActiveTable, TableSelectedTable, templateManager, AccountSavedCarts, UtilitiesService, ParamsCustomerRepository, BillService, ParamsSellerRepository, ParamsParameterRepository, OperatorController, OrderController, DelayedProductsRepository, TimestampRepository, ApplicationContext, WaiterNamedPositionsState, WindowService, AccountGetAccountDetails, SellerControl, PerifericosService) {

	var fieldCopy;
	var enterTable;
	var self = this;

	this.checkTable = function (table) {
		if (table.checked === 'selecao') {
			table.checked = '';
		} else {
			table.checked = 'selecao';
		}
	};

	this.selectItem = function (item, widget) {
		if (!item.__isSelected) {
			widget.dataSource.addCheckedRows(item);
		} else {
			widget.dataSource.removeCheckedRows(item);
		}
	};

	this.setMaxPosition = function (positionsField, maxPosition) {
		positionsField.dataSource.data[0].NRPOSICAOMESA = maxPosition;
	};

	this.buildPositionsObject = function (positionsField) {
		var positionsObject = [];
		var NRPOSICAOMESA = positionsField.dataSource.data[0].NRPOSICAOMESA;
		var clientMapping = positionsField.dataSource.data[0].clientMapping;
		var consumerMapping = positionsField.dataSource.data[0].consumerMapping;
		var positionNamedMapping = positionsField.dataSource.data[0].positionNamedMapping;
		var currentPosition;
		for (var idx = 0; idx < NRPOSICAOMESA; idx++) {
			currentPosition = idx + 1;
			positionsObject.push({
				'NRLUGARMESA': currentPosition,
				'CDCLIENTE': _.get(clientMapping[currentPosition], 'CDCLIENTE', null),
				'CDCONSUMIDOR': _.get(consumerMapping[currentPosition], 'CDCONSUMIDOR', null),
				'DSCONSUMIDOR': _.get(positionNamedMapping[currentPosition], 'DSCONSUMIDOR', null)
			});
		}
		return positionsObject;
	};

	this.buildClientMapping = function (positionsObject) {
		var clientMapping = {};
		_.each(positionsObject, function (currentPosition) {
			if (_.get(currentPosition, 'CDCLIENTE')) {
				clientMapping[parseInt(currentPosition.NRLUGARMESA)] = {
					'CDCLIENTE': currentPosition.CDCLIENTE,
					'NMRAZSOCCLIE': currentPosition.NMRAZSOCCLIE
				};
			}
		});
		return clientMapping;
	};

	this.buildConsumerMapping = function (positionsObject) {
		var consumerMapping = {};
		_.each(positionsObject, function (currentPosition) {
			if (_.get(currentPosition, 'CDCONSUMIDOR')) {
				consumerMapping[parseInt(currentPosition.NRLUGARMESA)] = {
					'CDCONSUMIDOR': currentPosition.CDCONSUMIDOR,
					'NMCONSUMIDOR': currentPosition.NMCONSUMIDOR
				};
			}
		});
		return consumerMapping;
	};

	this.buildPositionNamedMapping = function (positionsObject) {
		var positionNamedMapping = {};
		_.each(positionsObject, function (currentPosition) {
			if (_.get(currentPosition, 'DSCONSUMIDOR')) {
				positionNamedMapping[parseInt(currentPosition.NRLUGARMESA)] = {
					'DSCONSUMIDOR': currentPosition.DSCONSUMIDOR
				};
			}
		});
		return positionNamedMapping;
	};

	this.open = function (row, tablesWidget, callBack, positionsField) {
		if (row.NRPOSICAOMESA > 0) {
			OperatorRepository.findAll().then(function (operatorData) {
				var chave = operatorData[0].chave;
				var radioFieldValue = positionsField.widget.getField('radioTablePositions').value();
				var positionsObject = [];
				if (radioFieldValue === 'P') {
					positionsObject = self.buildPositionsObject(positionsField);
					row.CDCLIENTE = null;
					row.CDCONSUMIDOR = null;
				}
				if (operatorData[0].IDUTLSENHAOPER == 'C' && !_.isEmpty(row.CDVENDEDOR) && operatorData[0].IDCAIXAEXCLUSIVO === 'N') SellerControl.save(row.CDVENDEDOR);
				TableService.open(chave, row.NRMESA, row.NRPOSICAOMESA, row.CDCLIENTE, row.CDCONSUMIDOR, row.CDVENDEDOR, positionsObject).then(function (openData) {
					ScreenService.closePopup();
					if (row.NRACESSOUSER) {
						// Libera o acesso pendente (Order).
						OrderController.completeReleaseAccess(row.NRACESSOUSER, tablesWidget);
					} else {
						if (!openData.ERROR) {
							this.validateOpening(row.NRMESA, 'O', 'M', function () {
								if (!callBack) {
									WindowService.openWindow('MENU_SCREEN');
								} else {
									callBack();
								}
							});
						} else {
							ScreenService.closePopup(true);
							if (tablesWidget) {
								this.refreshTables(tablesWidget);
							}
						}
					}
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage('Quantidade de pessoas inválida.');
		}
	};

	this.cancelOpen = function (menuContainer) {
		ScreenService.confirmMessage(
			'Deseja cancelar a abertura da mesa?',
			'question',
			function () {
				OperatorRepository.findAll().then(function (operatorData) {
					var chave = operatorData[0].chave;
					TableActiveTable.findAll().then(function (tableData) {
						TableService.cancelOpen(chave, tableData[0].NRMESA).then(function () {
							UtilitiesService.backMainScreen();
						});
					}.bind(this));
				}.bind(this));
			}.bind(this),
			function () {
				menuContainer.restoreDefaultMode();
			}
		);
	};

	this.handleTransferWidget = function (widget) {
		OperatorRepository.findOne().then(function (data) {
			var productField = widget.getField('product');
			if (productField) {
				self.prepareTransferList(productField);
			}
			if (widget.getField('positions')) {
				if (data.IDLUGARMESA === 'S') {
					self.positionsTransferControl(widget.getField('positions'));
					widget.getField('lblPos').isVisible = true;
					widget.getField('positions').isVisible = true;
				} else {
					widget.getField('lblPos').isVisible = false;
					widget.getField('positions').isVisible = false;
				}
			}
			if (widget.getField('NRPOSICAOMESA')) {
				widget.getField('NRPOSICAOMESA').isVisible = false;
			}
			if (widget.getField('btnTableListProduto')) {
				widget.getField('btnTableListProduto').label = widget.getField('btnTableListProduto')._label;
			}
		});
	};

	this.positionsTransferControl = function (target) {
		TableSelectedTable.clearAll().then(function () {
			if (target.dataSource && target.dataSource.data && target.dataSource.data[0]) {
				target.dataSource.data[0].NRPOSICAOMESA = "0";
			}
		});
	};

	this.refreshTables = function (tablesWidget) {
		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			tablesWidget.getAction('quickProductRelease').isVisible = data[0].NRATRAPADRAO > 0;
			AccountCart.remove(Query.build()).then(function () {
				TableService.getTables(chave).then(function (requestData) {
					OrderController.updateNotificationsLabel(requestData.OrderGetAccessRepository, requestData.OrderGetCallRepository).then(function (allNotifications) {
						tablesWidget.fields[1].dataSource.data = requestData.TableRepository;
						tablesWidget.reload();
						tablesWidget.activate();
					});
				});
			});
		});
	};

	var getMillissecondsTime = function (qtMinutes) {
		return qtMinutes * 60 * 1000;
	};

	this.prepareAreas = function (select, filter, refreshTablesData) {
		ParamsAreaRepository.findAll().then(function (data) {
			TableActiveTable.findAll().then(function (active) {
				var myArea = [];
				if (active.length > 0) {
					myArea = data.filter(function (i) {
						return i.CDSALA === active[0].CDSALA;
					})[0];
				} else {
					myArea = data[0];
				}

				select.dataSource.data = data;
				select.setCurrentRow(myArea);
				filter.dataSource.data = data;
				filter.currentRow = myArea;

				refreshTablesData.activate();
				this.refreshTables(refreshTablesData);
			}.bind(this));
		}.bind(this));
	};

	this.changeArea = function (areaWidget, currentRow) {
		areaWidget.setCurrentRow(currentRow);
		ScreenService.closePopup();
	};

	this.validateOpening = function (nrMesa, status, modo, callBack) {
		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			TableService.validateOpening(chave, nrMesa, status, modo).then(function (comeBack) {
				if (callBack) callBack(comeBack[0]);
			});
		});
	};

	var last = 0;

	this.openTable = Util.buildDebounceMethod(function (row, widgetToShow, clearCart) {
		AccountController.buildOrderCode().then(function () {
			var _openTable = (function () {
				var nrMesa = row.NRMESA;
				var status = '';
				OperatorRepository.findAll().then(function (operatorData) {
					var chave = operatorData[0].chave;
					var query = Query.build()
						.where('NRMESA').equals(nrMesa);
					widgetToShow.getField('NRPOSICAOMESA').maxValue = operatorData[0].NRMAXPESMES;
					TableRepository.find(query).then(function (data) {
						var status = data[0].IDSTMESAAUX;
						var modo = 'M'; // "M" de mesa

						if (status !== 'D') { // diferente de disponível
							this.validateOpening(nrMesa, status, modo, function (back) {
								if ((back.IDSTMESAAUX === 'D') || (back.STATUS === 'DISPONIVEL')) {
									this.prepareOpening(row, widgetToShow);
								} else if (back.STATUS === 'SOLICITADA') {
									if (data[0].IDCOLETOR !== 'C') {
										this.showRequestedTableDialog(chave);
									} else {
										ScreenService.confirmMessage(
											'A conta já foi solicitada. Deseja reabrir a mesa?',
											'question',
											function () {
												PermissionService.checkAccess('liberarMesa').then(function () {
													this.handleFilterParameters();
												}.bind(this));
											}.bind(this),
											function () { }
										);
									}
								} else if (back.STATUS === 'RECEBIMENTO') {
									if (data[0].IDCOLETOR !== 'C') {
										if (back.POSITIONCONTROL) {
											self.openAccountPayment();
										}
										else {
											ScreenService.showMessage('Todas as posições da mesa já estão sendo recebidas.');
										}
									}
									else {
										ScreenService.showMessage('Mesa está em recebimento.');
									}
								} else if (back.STATUS === 'PAGA') {
									ScreenService.showMessage('Mesa paga.');
								} else {
									self.handleFilterParameters();
								}
							}.bind(this));
						} else if (status === 'D') {
							this.prepareOpening(row, widgetToShow);
						}
					}.bind(this));
				}.bind(this));
			}).bind(this);
			if (clearCart) {
				AccountCart.remove(Query.build()).then(function () {
					_openTable();
				});
			} else {
				_openTable();
			}
			AccountCart.remove(Query.build()).then(function () { }.bind(this));
		}.bind(this));
	}, 200, true);

	this.handleFilterParameters = function () {
		OperatorRepository.findAll().then(function (data) {
			TableActiveTable.findAll().then(function (activeTable) {
				if (data[0].IDUTLSENHAOPER == 'C' && data[0].IDCAIXAEXCLUSIVO === 'N') {
					ScreenService.openPopup(templateManager.container.getWidget('setCurrentWaiterPopUp'));
				} else if (data[0].IDUTLSENHAOPER == 'S' && data[0].IDCAIXAEXCLUSIVO === 'N') {
					ScreenService.openPopup(templateManager.container.getWidget('setPassWaiterPopUp'));
				} else if (activeTable[0].STATUS === 'SOLICITADA') {
					TableService.reopen(data[0].chave, activeTable[0].NRMESA).then(function () {
						WindowService.openWindow('MENU_SCREEN');
					});
				} else {
					WindowService.openWindow('MENU_SCREEN');
				}
			});
		});
	};

	this.tableClick = function (row, widgetToShow) {
		var time = new Date();
		enterTable = true;
		this.openTable(row, widgetToShow, true);
	};

	this.validateWaiter = function (widget) {
		var field = widget.getField('currentWaiterField');
		if (!_.isEmpty(field.value())) {
			SellerControl.save(widget.currentRow.CDVENDEDOR);
			self.handleOpenTable(field);
		} else {
			ScreenService.showMessage('Nenhum vendedor selecionado.');
		}
	};

	this.validatePassword = function (widget) {
		field = widget.getField('passwordField');
		if (!_.isEmpty(field.value())) {
			AccountService.validatePassword(field.value()).then(function (result) {
				if (!_.isEmpty(result)) {
					self.handleOpenTable(field);
				}
			});
		} else {
			ScreenService.showMessage('Informe a senha.');
		}
	};

	this.handleOpenTable = function (field) {
		TableActiveTable.findAll().then(function (activeTable) {
			if (activeTable[0].STATUS === 'SOLICITADA') {
				OperatorRepository.findAll().then(function (operatorData) {
					TableService.reopen(operatorData[0].chave, activeTable[0].NRMESA).then(function () {
						field.value('');
						WindowService.openWindow('MENU_SCREEN');
					});
				});
			} else {
				field.value('');
				WindowService.openWindow('MENU_SCREEN');
			}
		}.bind(this));
	};

	this.clearAndClose = function (field) {
		field.value('');
		ScreenService.closePopup();
	};

	this.prepareOpening = function (row, widgetToShow) {
		if (widgetToShow.dataSource.data && widgetToShow.dataSource.data.length > 0) {
			delete widgetToShow.dataSource.data;
		}
		widgetToShow.newRow();
		widgetToShow.container.restoreDefaultMode();
		widgetToShow.moveToFirst();

		var data = {
			NRMESA: row.NRMESA,
			NRPESMESAVEN: 2,
			NRPOSICAOMESA: 2,
			__createdLocal: true,
			btnOpenTable: null,
			lblNMMESA: null,
			lblNRPESMESAVEN: null,
			CDCLIENTE: null,
			CDCONSUMIDOR: null,
			CDVENDEDOR: null,
			NMRAZSOCCLIE: null,
			NRACESSOUSER: row.NRACESSOUSER
		};

		widgetToShow.setCurrentRow(data);
		widgetToShow.label = 'Abrir Mesa - ' + row.NMMESA;

		ScreenService.openPopup(widgetToShow);
	};

	this.scanConsumerQrCode = function (widget) {
		if (_.isEmpty(widget.currentRow.CDCLIENTE)) widget.currentRow.CDCLIENTE = null;

		self.callQRScanner().then(function (qrCode) {
			if (!qrCode.error) {
				qrCode = qrCode.contents;

				if (_.isEmpty(qrCode)) {
					ScreenService.showMessage("Não foi possível obter os dados do leitor.");
				}
				else {
					widget.currentRow.NMCONSUMIDOR = "";
					widget.currentRow.CDCLIENTE = "";
					widget.currentRow.CDCONSUMIDOR = "";
					widget.getField('NMCONSUMIDOR').clearValue();
					widget.getField('NMRAZSOCCLIE').clearValue();

					OperatorRepository.findOne().then(function (operatorData) {
						AccountService.searchConsumer(operatorData.chave, widget.currentRow.CDCLIENTE, qrCode).then(function (consumerData) {
							if (_.isEmpty(consumerData)) {
								ScreenService.showMessage("Não foi encontrado nenhum consumidor com este código.");
							} else {
								var clientField = widget.getField('NMRAZSOCCLIE');
								var consumerField = widget.getField('NMCONSUMIDOR');
								clientField.readOnly = false;
								consumerField.readOnly = false;
								consumerField.dataSourceFilter[0].value = consumerData[0].CDCLIENTE;
								widget.currentRow.CDCLIENTE = consumerData[0].CDCLIENTE;
								widget.currentRow.NMRAZSOCCLIE = consumerData[0].NMRAZSOCCLIE;
								widget.currentRow.CDCONSUMIDOR = consumerData[0].CDCONSUMIDOR;
								widget.currentRow.NMCONSUMIDOR = consumerData[0].NMCONSUMIDOR;
								this.updatePositionLabel(consumerField);
							}
						}.bind(this));
					}.bind(this));
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

	this.prepareClients = function (clientWidget) {
		ParamsClientRepository.findAll().then(function (data) {
			clientWidget.dataSource.data = data;
			ScreenService.openPopup(clientWidget);
		});
	};

	this.prepareCustomers = function (customersSelect, CDCLIENTE, mustUpdateField, positionsField) {
		customersSelect.clearValue();
		customersSelect.widget.currentRow.CDCONSUMIDOR = "";
		ParamsCustomerRepository.clearAll().then(function () {
			CDCLIENTE = !_.isEmpty(CDCLIENTE) ? CDCLIENTE : "";
			customersSelect.dataSourceFilter[0].value = CDCLIENTE;
			if (CDCLIENTE) {
				customersSelect.reload();
				if (mustUpdateField) {
					updateConsumerField(positionsField, customersSelect);
				}
			}
			this.updatePositionLabel(customersSelect);
		}.bind(this));
	};

	this.sendMessage = function (row) {
		var mensagens = "";
		mensagens = row.DSOCORR || [];
		var impressoras = row.NRSEQIMPRLOJA || [];

		var mensagem = "";
		if (row.mensagem !== "") mensagem += row.mensagem + "; ";
		mensagem += mensagens.join("; ");

		if (mensagem === "")
			ScreenService.showMessage("Selecione uma mensagem.");
		else if (impressoras.length === 0)
			ScreenService.showMessage("Selecione uma impressora.");
		else {
			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				AccountController.getAccountData(function (accountData) {
					TableService.sendMessage(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, impressoras, mensagem, row.TXMOTIVCANCE, data[0].modoHabilitado).then(function (result) {
						result = result[0];
						if (_.get(result, '[0].saas')) {
							PerifericosService.print(result);
							UtilitiesService.backMainScreen();
						} else {
							UtilitiesService.backMainScreen();
						}
					});
				});
			}.bind(this));
		}
	};

	this.sendWaiterlessMessage = function (row) {
		var mensagens = "";
		mensagens = row.DSOCORR || [];
		var impressoras = row.NRSEQIMPRLOJA || [];
		mensagens.push(row.mensagem);

		var mensagem = mensagens.join('; ');

		if (mensagem === "")
			ScreenService.showMessage("Selecione uma mensagem.");
		else if (impressoras.length === 0)
			ScreenService.showMessage("Selecione uma impressora.");
		else {
			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				TableService.sendMessage(chave, data[0].NMFANVEN, "waiterless", impressoras, mensagem, row.TXMOTIVCANCE, data[0].modoHabilitado).then(function (response) {
					response = response[0];
					if (_.get(response, '[0].saas')) {
						PerifericosService.print(response[0]).then(function (result) {
							UtilitiesService.backMainScreen();
						});
					} else {
						UtilitiesService.backMainScreen();
					}
				});
			}.bind(this));
		}
	};

	this.quickProductRelease = function (event, row, tablesWidget) {
		event.stopPropagation();
		OperatorRepository.findOne().then(function (operatorData) {
			this.validateOpening(row.NRMESA, row.IDSTMESAAUX, 'M', function (back) {
				TableService.getDelayedProducts(operatorData.chave, back.NRVENDAREST, back.NRCOMANDA).then(function (delayedProducts) {
					if (delayedProducts.length > 0) {
						WindowService.openWindow('DELAYED_PRODUCTS_SCREEN');
					} else {
						ScreenService.showMessage('Não existem pedidos para liberar nesta mesa.');
					}
				});
			}.bind(this));
		}.bind(this));
	};

	this.quickPrintAccount = function (event, row, tablesWidget) {
		event.stopPropagation();
		OperatorRepository.findOne().then(function (operatorData) {
			this.validateOpening(row.NRMESA, row.IDSTMESAAUX, 'M', function (back) {
				if (back.IDSTMESAAUX === 'D' || back.STATUS === 'DISPONIVEL') {
					ScreenService.showMessage("Mesa ainda não foi aberta.");
				} else if (back.STATUS === 'SOLICITADA' || back.STATUS === 'RECEBIMENTO') {
					ScreenService.showMessage("A conta já foi solicitada.");
				} else {
					AccountService.getAccountDetails(operatorData.chave, "M", row.NRCOMANDA, row.NRVENDAREST, 'I', "").then(function (backData) {
						this.refreshTables(tablesWidget);
						AccountController.handlePrintBill(backData.dadosImpressao);
						//backend already send it
						//ScreenService.successNotification("Parcial da conta impressa com sucesso.");
					}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.quickCloseAccount = function (event, row, tablesWidget) {
		event.stopPropagation();
		OperatorRepository.findOne().then(function (operatorData) {
			this.validateOpening(row.NRMESA, row.IDSTMESAAUX, 'M', function (back) {
				if (back.IDSTMESAAUX === 'D' || back.STATUS === 'DISPONIVEL') {
					ScreenService.showMessage("Mesa ainda não foi aberta.");
				} else if (back.STATUS === 'SOLICITADA' || back.STATUS === 'RECEBIMENTO') {
					ScreenService.showMessage("A conta já foi solicitada.");
				} else {
					AccountService.getAccountDetails(operatorData.chave, 'M', row.NRCOMANDA, row.NRVENDAREST, 'M', '').then(function (accountDetails) {
						if (accountDetails.AccountGetAccountDetails[0].vlrtotal === 0) {
							ScreenService.confirmMessage(
								'Não foi realizado nenhum pedido para esta mesa, deseja cancelar a abertura?',
								'question',
								function () {
									TableService.cancelOpen(operatorData.chave, row.NRMESA).then(function () {
										this.refreshTables(tablesWidget);
									}.bind(this));
								}.bind(this),
								function () { }
							);
						} else {
							TableService.closeAccount(operatorData.chave, row.NRCOMANDA, row.NRVENDAREST, 'M', true, true, true, 0, accountDetails.AccountGetAccountDetails[0].NRPESMESAVEN, null, null, 'I', null).then(function (closeAccountReturn) {
								this.refreshTables(tablesWidget);
								AccountController.handlePrintBill(closeAccountReturn.dadosImpressao);
								//backend already send it
								//ScreenService.successNotification("Mesa fechada com sucesso.");
							}.bind(this));
						}
					}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.closeAccount = function (widget, txporcentservico) {
		OperatorRepository.findOne().then(function (data) {
			AccountController.getAccountData(function (accountData) {
				accountData = accountData[0];
				var total = widget.getField('total').value();

				total = UtilitiesService.removeCurrency(total);
				if (total === 0 && (data.modoHabilitado === 'M' || data.modoHabilitado === 'C')) {
					var mode = data.modoHabilitado === 'M' ? 'mesa' : 'comanda';
					ScreenService.confirmMessage(
						'Não foi realizado nenhum pedido para esta ' + mode + ', deseja cancelar a abertura da ' + mode + '?',
						'question',
						function () {
							if (data.modoHabilitado === 'M') {
								TableService.cancelOpen(data.chave, accountData.NRMESA).then(function () {
									UtilitiesService.backMainScreen();
								});
							} else {
								BillService.cancelOpen(data.chave, accountData.NRMESA, accountData.NRVENDAREST, accountData.NRCOMANDA).then(function () {
									UtilitiesService.backMainScreen();
								});
							}
						},
						function () { }
					);
				} else {
					var modo, consumacao, servico, couvert, imprimeParcial;
					if (data.modoHabilitado === 'O') {
						//Mesmo sendo modo order, o parametro passado será 'M'
						modo = 'M';
						consumacao = true;
						servico = true;
						couvert = true;
					} else {
						modo = data.modoHabilitado;
						consumacao = widget.getField('swiconsumacao').value();
						servico = widget.getField('swiservico').value();
						couvert = widget.getField('swicouvert').value();
						imprimeParcial = data.modoHabilitado === 'M' ? 'I' : null;
					}

					TableService.closeAccount(data.chave, accountData.NRCOMANDA, accountData.NRVENDAREST, modo, consumacao, servico,
						couvert, 0, accountData.NRPESMESAVEN, widget.currentRow.CDSUPERVISOR, accountData.NRMESA, imprimeParcial, txporcentservico).then(function (response) {
							if (response.nothing[0].nothing === 'nothing') {
								if (data.modoHabilitado === 'M') {
									if (_.get(response, 'paramsImpressora.saas')) {
										PerifericosService.print(response.paramsImpressora).then(function () {
											self.receivePayment(data.chave, accountData.NRCOMANDA, accountData.NRVENDAREST, data.IDCOLETOR);
											AccountController.handlePrintBill(response.dadosImpressao);
										});
									} else {
										this.receivePayment(data.chave, accountData.NRCOMANDA, accountData.NRVENDAREST, data.IDCOLETOR);
										AccountController.handlePrintBill(response.dadosImpressao);
									}
								} else {
									AccountController.prepareAccountDetails(widget, function () {
										AccountController.openPayment(true);
									});
								}
							} else {
								UtilitiesService.backMainScreen();
							}
						}.bind(this));
				}
			}.bind(this));
		}.bind(this));
	};

	this.receivePayment = function (chave, NRCOMANDA, NRVENDAREST, IDCOLETOR) {
		if (IDCOLETOR !== 'C') {
			ScreenService.confirmMessage(
				'Deseja ir para tela de pagamento?',
				'question',
				function () {
					TableService.changeTableStatus(chave, NRVENDAREST, NRCOMANDA, 'R').then(function (response) {
						this.openAccountPayment();
					}.bind(this));
				}.bind(this),
				function () {
					UtilitiesService.backMainScreen();
				}.bind(this)
			);
		} else
			UtilitiesService.backMainScreen();
	};

	this.openAccountPayment = function () {
		TableActiveTable.findOne().then(function (activeTable) {
			TableService.positionControl(activeTable.NRVENDAREST, null, null, null).then(function (result) {
				WindowService.openWindow('PAYMENT_SCREEN').then(function () {
					var accountPaymentWidget = templateManager.container.getWidget('accountDetails');
					// Métodos onEnter da AccountPaymentNamed.json.
					AccountController.handlePositionsFieldInit(accountPaymentWidget);
					AccountController.prepareAccountClosingWidget(accountPaymentWidget, null, null, null);
					if (result[0].message == null) {
						AccountController.showPositionActions(accountPaymentWidget.container.getWidget('accountDetails').getField('positionsField'));
						AccountController.refreshAccountDetails(accountPaymentWidget.widgets, '');
					}
					else {
						AccountController.hidePositionActions(accountPaymentWidget.container.getWidget('accountDetails').getField('positionsField'));
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.getMessageHistory = function (widget) {
		if (widget.dataSource.data && widget.dataSource.data.length > 0) {
			delete widget.dataSource.data;
		}

		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			AccountController.getAccountData(function (accountData) {
				TableService.getMessageHistory(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST).then(function (data) {
					var placeHolder = '';
					if (!(data[0].TXMOTIVCANCE)) {
						placeHolder = 'nenhuma mensagem';
					}
					var items = [{
						mensagem: '',
						TXMOTIVCANCE: data[0].TXMOTIVCANCE,
						TXMOTIVCANCENADA: placeHolder,
						NMIMPRLOJA: [],
						DSOCORR: []
					}];

					widget.dataSource.data = items;
					widget.setCurrentRow(items[0]);
				});
			}.bind(this));
		}.bind(this));
	};

	this.prepareWaiterlessData = function (widget) {
		widget.setCurrentRow({});
		/*
		if(widget.dataSource.data && widget.dataSource.data.length > 0) {
			delete widget.dataSource.data;
		}

		OperatorRepository.findAll().then(function (data) {
			var chave = data[0].chave;
			var items = [];
			items.push({
				mensagem: '',
				TXMOTIVCANCENADA: data[0].TXMOTIVCANCE,
				TXMOTIVCANCENADA: '',
				NMIMPRLOJA: [],
				DSOCORR: []
			});

			widget.dataSource.data = items;
			widget.setCurrentRow(items[0]);
		}.bind(this));
		*/
	};

	this.showCancelProduct = function (CDSUPERVISOR) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountItems(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, '').then(function (dataReturn) {
					if (params[0].modoHabilitado === 'C') {
						WindowService.openWindow('CANCEL_PRODUCT_SCREEN').then(function () {
							templateManager.container.getWidget('cancelProductComanda')._supervisor = CDSUPERVISOR;
						}.bind(this));
					}
					else {
						WindowService.openWindow('CANCEL_PRODUCT_SCREEN2').then(function () {
							templateManager.container.getWidget('cancelProductMesa')._supervisor = CDSUPERVISOR;
						}.bind(this));
					}
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	/* ******************** */
	/* GROUPING & SPLITTING */
	/* ******************** */

	this.prepareGrouping = function (selectedWidget) {
		selectedWidget.activate();
		templateManager.updateTemplate();

		if (selectedWidget.dataSource.data && selectedWidget.dataSource.data.length > 0) {
			delete selectedWidget.dataSource.data;
		}

		TableActiveTable.findAll().then(function (activeTable) {
			var query = Query.build()
				.where('CDSALA').equals(activeTable[0].CDSALA)
				.where('IDSTMESAAUX').equals('O')
				.where('agrupada').equals('N')
				.where('NRMESA').notEquals(activeTable[0].NRMESA);

			TableRepository.find(query).then(function (result) {
				selectedWidget.dataSource.data = result;
			});
		});
	};

	this.prepareSplitting = function (selectedWidget) {
		templateManager.updateTemplate();

		if (selectedWidget.dataSource.data && selectedWidget.dataSource.data.length > 0) {
			delete selectedWidget.dataSource.data;
		}

		TableActiveTable.findAll().then(function (activeTable) {
			// find the active table in the table repository
			var query = Query.build()
				.where('NRMESA').equals(activeTable[0].NRMESA);
			TableRepository.find(query).then(function (activeTableInRepo) {

				// find all the tables that are grouped with the active table
				TableRepository.findAll().then(function (tables) {
					// get the tables in the table repository
					tablesToShow = tables.filter(function (currentTable) {
						for (var groupedTable in activeTableInRepo[0].mesasAgrupadas) {
							if (currentTable.NRMESA === activeTableInRepo[0].mesasAgrupadas[groupedTable]) {
								if (currentTable.NRMESA !== activeTable[0].NRMESA) {
									// and put them in a array
									return currentTable;
								}
							}
						}
					});
				}).then(function () {
					selectedWidget.dataSource.data = tablesToShow;
				});
			});
		});
	};

	this.prepareTableList = function (tablesWidget) {
		OperatorRepository.findAll().then(function (params) {
			TableService.getTables(params[0].chave).then(function (result) {
				result.TableRepository.forEach(function (res) {
					res.mode = 'list';
				});
				tablesWidget.dataSource.data = result.TableRepository;
				ScreenService.openPopup(tablesWidget);
			});
		});
	};

	this.selectTable = function (table, positionsField, abreComanda) {
		/* Updates the positions widget with the number of positions on the selected table. */
		OperatorRepository.findAll().then(function (params) {
			if (params[0].modoHabilitado === 'M') {
				positionsField.reload().then(function (data) {
					var fieldMaxPosicoes = positionsField.widget.getField('NRPOSICAOMESA');
					positionsFieldData = positionsField.dataSource.data[0];
					if (parseInt(table.NRPOSICAOMESA) > 0) {
						if (fieldMaxPosicoes) {
							fieldMaxPosicoes.isVisible = false;
						}
						positionsFieldData.NRPOSICAOMESA = table.NRPOSICAOMESA;
					} else {
						if (params[0].IDLUGARMESA === 'S') {
							if (fieldMaxPosicoes) {
								fieldMaxPosicoes.isVisible = true;
								fieldMaxPosicoes.applyDefaultValue();
								positionsFieldData.NRPOSICAOMESA = fieldMaxPosicoes.value();
							} else {
								positionsFieldData.NRPOSICAOMESA = "2";
							}
						} else {
							fieldMaxPosicoes.isVisible = false;
							fieldMaxPosicoes.setValue("1");
							positionsFieldData.NRPOSICAOMESA = positionsFieldData.NRPESMESAVEN = "1";
						}
					}
					positionsField.position = null;

					TableSelectedTable.clearAll().then(function () {
						TableSelectedTable.save(table).then(function () {
							var container = positionsField.widget.container;

							var productWidget = container.getWidget('product');
							if (productWidget.getField('btnTableListProduto')) {
								productWidget.getField('btnTableListProduto').label = table.NMMESA;
							}

							var tableWidget = container.getWidget('table');
							if (tableWidget.getField('btnTableListMesa')) {
								tableWidget.getField('btnTableListMesa').label = table.NMMESA;
							}

							ScreenService.closePopup();
							positionsField.forceReload = true;
							templateManager.updateTemplate();
						});
					});
				});
			} else {
				TableSelectedTable.clearAll().then(function () {
					TableSelectedTable.save(table).then(function () {
						abreComanda.getField('btnTableList').label = table.NMMESA;
						ScreenService.closePopup();
					});
				});
			}
		});
	};

	this.groupTables = function (widget) {
		list = widget.dataSource.data.filter(function (array) {
			return array.checked === 'selecao';
		});

		if (list.length !== 0) {

			var listaMesas = [];
			for (var i in list) {
				listaMesas.push(list[i].NRMESA);
			}

			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				TableActiveTable.findAll().then(function (mesa) {
					TableService.groupTables(chave, mesa[0].NRMESA, listaMesas).then(function () {
						UtilitiesService.backMainScreen();
					});
				}.bind(this));
			}.bind(this));

		} else {
			ScreenService.showMessage('Nenhuma mesa foi selecionada.');
		}
	};

	this.splitTables = function (widget) {
		/* Gets the selected tables from the list. */
		list = widget.dataSource.data.filter(function (array) {
			return array.checked === 'selecao';
		});

		if (list.length !== 0) {
			/* Inserts the selected table into an array. */
			var listaMesas = [];
			for (var i in list)
				listaMesas.push(list[i].NRMESA);

			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				AccountController.getAccountData(function (accountData) {
					/* Splits the selected tables from the currently active table. */
					TableService.splitTables(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, listaMesas).then(function () {
						UtilitiesService.backMainScreen();
					});
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage('Nenhuma mesa foi selecionada.');
		}
	};


	/* ********** */
	/* TRANSFERS */
	/* ********* */

	this.showTransfers = function (CDSUPERVISOR) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountItems(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, '').then(function (dataReturn) {
					WindowService.openWindow('TRANSFERS_SCREEN').then(function () {
						templateManager.container.getWidget('product')._supervisor = CDSUPERVISOR;
						templateManager.container.getWidget('table')._supervisor = CDSUPERVISOR;
					});
				});
			});
		});
	};

	this.prepareTransferList = function (transferList, items) {
		transferList.widget.activate();
		if (items) {
			transferList.dataSource.data = items;
		} else {
			AccountGetAccountItems.findAll().then(function (data) {
				transferList.dataSource.checkedRows = [];
				for (var i in data) {
					if (data[i].quantidade != 1) {
						data[i].DSBUTTON = data[i].quantidade + " x " + data[i].DSBUTTON;
					}
				}
				transferList.dataSource.data = data;
			});
		}
	};

	this.transferItemActionEvent = function (widget) {
		var productField = widget.getField('product');
		var positionsField = widget.getField('positions');
		if (productField) {
			self.transferItem(productField.dataSource.checkedRows, positionsField.position + 1, productField, widget);
		}
	};

	this.transferItem = function (rows, position, listGroupedField, widget) {
		TableSelectedTable.findAll().then(function (mesa) {
			if (mesa.length > 0) {
				if (rows.length > 0) {
					ScreenService.confirmMessage(
						'Deseja transferir o(s) produto(s) selecionado(s) para a ' + mesa[0].NMMESA + '?',
						'question',
						function () {
							OperatorRepository.findAll().then(function (params) {
								/* Stores selected items into an array. */
								var produtos = {};
								rows.forEach(function (row) {
									var produto;
									if (row.CDPRODPROMOCAO === null || row.composicao.length > 0) {
										produto = {
											NRVENDAREST: row.NRVENDAREST,
											NRCOMANDA: row.nrcomanda,
											NRPRODCOMVEN: row.NRPRODCOMVEN,
											quantidade: row.quantidade
										};
										produtos[row.NRPRODCOMVEN] = produto;
									}
									else {
										for (var i in listGroupedField.dataSource.data) {
											if (listGroupedField.dataSource.data[i].NRSEQPRODCOM == row.NRSEQPRODCOM) {
												produto = {
													NRVENDAREST: listGroupedField.dataSource.data[i].NRVENDAREST,
													NRCOMANDA: listGroupedField.dataSource.data[i].nrcomanda,
													NRPRODCOMVEN: listGroupedField.dataSource.data[i].NRPRODCOMVEN,
													quantidade: "1.000"
												};
												produtos[listGroupedField.dataSource.data[i].NRPRODCOMVEN] = produto;
											}
										}
									}
								});
								var transfer = [];
								for (var i in produtos) {
									transfer.push(produtos[i]);
								}
								AccountController.getAccountData(function (accountData) {
									var CDSUPERVISOR = params[0].CDOPERADOR !== widget._supervisor ? widget._supervisor : null;
									var maxPosicoes = widget.currentRow.NRPOSICAOMESA;
									TableService.transferItem(params[0].chave, mesa[0].NRMESA, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, transfer, position, CDSUPERVISOR, maxPosicoes).then(function () {
										var fieldMaxPosicoes = widget.getField('NRPOSICAOMESA');
										fieldMaxPosicoes.isVisible = false;
										if (accountData[0].NRMESA !== mesa[0].NRMESA) {
											/* Remove the transfered items from the list. */
											transfer.forEach(function (product) {
												listGroupedField.dataSource.data = listGroupedField.dataSource.data.filter(function (item) {
													return item.NRPRODCOMVEN !== product.NRPRODCOMVEN;
												});
											});
											if (listGroupedField.dataSource.data.length === 0) {
												WindowService.openWindow('TABLES_SCREEN');
											}
										} else {
											/* Change the position of the items. */
											rows.forEach(function (row) {
												row.posicao = "posição " + position;
											});
											listGroupedField.dataSource.data = listGroupedField.dataSource.data.filter(function (item) {
												return item !== null;
											});
										}

										//Cleans the selection
										listGroupedField.dataSource.data.map(function (item) {
											item.__isSelected = false;
											return item;
										});
										listGroupedField.dataSource.checkedRows = [];
									});
								}.bind(this));
							}.bind(this));
						}.bind(this),
						function () {
							/* Do nothing. */
						}
					);
				} else {
					ScreenService.showMessage('Nenhum produto foi selecionado.');
				}
			} else {
				ScreenService.showMessage('Mesa não selecionada.');
			}
		}.bind(this));
	};

	this.transferTable = function (widget) {
		TableSelectedTable.findAll().then(function (destinyTable) {
			if (destinyTable.length > 0) {
				ScreenService.confirmMessage(
					'Transferir para a ' + destinyTable[0].NMMESA + '?',
					'question',
					function () {
						OperatorRepository.findAll().then(function (operatorData) {
							var chave = operatorData[0].chave;
							AccountController.getAccountData(function (accountData) {
								if (accountData[0].NRMESA === destinyTable[0].NRMESA) {
									ScreenService.showMessage('Não é possível transferir para a mesa origem.');
								} else {
									var CDSUPERVISOR = operatorData[0].CDOPERADOR !== widget._supervisor ? widget._supervisor : null;
									TableService.transferTable(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, destinyTable[0].NRMESA, CDSUPERVISOR).then(function (data) {
										UtilitiesService.backMainScreen();
									});
								}
							});
						});
					}.bind(this),
					function () { }
				);
			} else {
				ScreenService.showMessage('Selecione uma mesa.');
			}
		});
	};


	/* ********* */
	/* POSITIONS */
	/* ********* */

	/* Updates the positions widget (top of page). */
	this.preparePositions = function (positions) {
		OperatorRepository.findAll().then(function (params) {
			// está no modo mesa e utiliza rotina de posições
			if (params[0].modoHabilitado === 'M' && params[0].IDLUGARMESA === 'S') {
				positions.isVisible = true;
				TableActiveTable.findAll().then(function (tableData) {
					positions.dataSource.data = tableData;

					if (enterTable) {
						positions.position = 0;
						enterTable = false;
					}
				});
			} else {
				positions.isVisible = false;
			}
		});
	};

	/* Changes the number of people on the table. */
	this.setPositions = function (popupWidget, positionsField, radioTablePositions, menuPositionsWidget) {
		if (popupWidget.currentRow.NRPOSICAOMESA > 0) {
			OperatorRepository.findAll().then(function (data) {
				var chave = data[0].chave;
				/* Gets the currently active table. */
				AccountController.getAccountData(function (accountData) {
					/* Makes changes (back end). */
					TableService.setPositions(chave, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, popupWidget.currentRow.NRPOSICAOMESA).then(function () {
						// recalculates prices
						AccountController.changeClientConsumer(popupWidget, positionsField, radioTablePositions, false).then(function () {
							self.preparePositions(menuPositionsWidget);
							ScreenService.closePopup();
						});
					}.bind(this));
				}.bind(this));
			}.bind(this));
		} else {
			ScreenService.showMessage('Quantidade de pessoas inválida.');
		}
	};

	/* Shows a widget that allows the user to change the number of people. */
	this.showChangePositions = function (widget) {
		ScreenService.openPopup(widget).then(function () {
			self.blockPopupOnEnterEvent = false;
			self.handleShowPositions(widget, true);
		});
	};

	/* Changes the table associated to the bill - only used in Bill Mode. */
	this.setTheTable = function (NRMESA, container) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (data) {
				BillService.setTheTable(data[0].chave, NRMESA, accountData[0].NRVENDAREST).then(function (result) {
					var currentLabel = container.label.substr(0, container.label.indexOf('<span')) || container.label;
					container.label = currentLabel + '<span class="waiter-header-right"> Comanda ' + accountData[0].DSCOMANDA + ' - Mesa ' + NRMESA + '</span>';
					ScreenService.closePopup(true);
				});
			});
		});
	};

	/* Delayed Products functions. */
	this.setDelayedProducts = function (widget) {
		OperatorRepository.findOne().then(function (operatorData) {
			DelayedProductsRepository.findAll().then(function (delayedProducts) {
				widget.dataSource.data = delayedProducts;
				widget.selectAll();
				templateManager.updateTemplate();
			});
		});
	};

	this.showPrinters = function (selectedProducts, popup) {
		if (selectedProducts.length > 0) {
			popup.setCurrentRow({});
			ScreenService.openPopup(popup);
		}
		else ScreenService.showMessage('Favor escolher pelo menos 1 produto para liberar.');
	};

	this.releaseMultipleProducts = function (selectedProducts, printer, widget) {
		if (printer && printer[0]) {
			printer = printer[0];
		} else {
			printer = '';
		}
		var selection = [];
		for (var i in selectedProducts) {
			selection.push({
				'CDFILIAL': selectedProducts[i].CDFILIAL,
				'NRPEDIDOFOS': selectedProducts[i].NRPEDIDOFOS,
				'NRITPEDIDOFOS': selectedProducts[i].NRITPEDIDOFOS,
				'NRVENDAREST': selectedProducts[i].NRVENDAREST,
				'NRCOMANDA': selectedProducts[i].NRCOMANDA,
				'NRPRODCOMVEN': selectedProducts[i].NRPRODCOMVEN
			});
		}
		OperatorRepository.findOne().then(function (operatorData) {
			TableService.releaseTheProduct(operatorData.chave, selectedProducts[0].CDFILIAL, selectedProducts[0].NRVENDAREST, selectedProducts[0].NRCOMANDA, selection, printer).then(function (delayedProducts) {
				ScreenService.closePopup();
				if (delayedProducts.length > 0) {
					widget.dataSource.data = delayedProducts;
					templateManager.updateTemplate();
				} else {
					ScreenService.goBack();
				}
			});
		});
	};

	this.selectReleasePrinter = function (widget) {
		if (widget.currentRow.NRSEQIMPRLOJA && !Util.isEmptyOrBlank(widget.currentRow.NRSEQIMPRLOJA)) {
			widget.currentRow.NRSEQIMPRLOJA = Array(widget.currentRow.NRSEQIMPRLOJA.pop());
		}
	};

	this.groupSmartPromo = function (product, widget) {
		for (var i in widget.dataSource.data) {
			if (product.NRSEQPRODCOM !== null && product.NRSEQPRODCOM === widget.dataSource.data[i].NRSEQPRODCOM) {
				if (widget.dataSource.checkedRows.indexOf(widget.dataSource.data[i]) < 0) {
					widget.dataSource.checkedRows.push(widget.dataSource.data[i]);
					widget.dataSource.data[i].__isSelected = true;
					widget.dataSource.updateCheckedRows();
				}
			}
		}
	};

	this.ungroupSmartPromo = function (product, widget) {
		for (var i in widget.dataSource.data) {
			if (product.NRSEQPRODCOM !== null && product.NRSEQPRODCOM === widget.dataSource.data[i].NRSEQPRODCOM) {
				widget.dataSource.checkedRows.splice(widget.dataSource.data.indexOf(widget.dataSource.data[i]), 1);
				widget.dataSource.data[i].__isSelected = false;
				widget.dataSource.updateCheckedRows();
			}
		}
	};

	this.loadProducts = function () {
		var widgetProducts = templateManager.container.getWidget("widgetProducts");
		widgetProducts.dataSource.data = [];
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountItemsWithoutCombo(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, "").then(function (databack) {
					// pageDetails.currentRow = databack.AccountGetAccountDetails[0];

					if (widgetProducts.dataSource.data && widgetProducts.dataSource.data.length > 0) {
						widgetProducts.dataSource.data = [];
					}

					widgetProducts.dataSource.data = databack;
					templateManager.updateTemplate();
				});
			});
		});
	};

	this.loadOriginalProducts = function () {
		var widgetCancel = templateManager.container.getWidget("widgetCancel");
		widgetCancel.dataSource.data = [];
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findAll().then(function (params) {
				AccountService.getAccountOriginalItems(params[0].chave, params[0].modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, "").then(function (databack) {
					// pageDetails.currentRow = databack.AccountGetAccountDetails[0];
					if (databack.length > 0) {
						self.widgetCancelVisibility(true);
						if (widgetCancel.dataSource.data && widgetCancel.dataSource.data.length > 0) {
							widgetCancel.dataSource.data = [];
						}

						widgetCancel.dataSource.data = databack;
						templateManager.updateTemplate();
					} else {

						self.widgetCancelVisibility(false);
					}
				});
			});
		});
	};

	this.widgetCancelVisibility = function (visibility) {

		var widgetCancel = templateManager.container.getWidget("widgetCancel");
		widgetCancel.isVisible = visibility;

	};

    this.splitProductsValidation = function (){

        var widgetProducts = templateManager.container.getWidget("widgetProducts");
        var field = templateManager.container.getWidget("widgetSplit").getField("positionswidget");
        var selectedProducts = templateManager.container.getWidget("widgetProducts").getCheckedRows();

        if (!field.position || field.position.length <= 1 || selectedProducts.length === 0)
            widgetProducts.getAction("dividir").isVisible = false;
        else
            widgetProducts.getAction("dividir").isVisible = true;

        field._isStatusChanged = false;

    };

    this.splitProductPromoIntegrity = function(widget, selectedItem){
        widget.dataSource.data.forEach(function (item){
            if (selectedItem && !_.isEmpty(item.NRSEQPRODCOM)){
                if (item.NRSEQPRODCOM == selectedItem.NRSEQPRODCOM && !_.isEqual(item, selectedItem)){
                    item.__isSelected = !selectedItem.__isSelected;
                }
            }
        });
    };

	this.cancelSplitedProductsValidation = function () {

		var widgetCancel = templateManager.container.getWidget("widgetCancel");
		var selectedProducts = widgetCancel.getCheckedRows();

		if (selectedProducts.length === 0) {
			widgetCancel.getAction("cancelar").isVisible = false;
		} else {
			widgetCancel.getAction("cancelar").isVisible = true;
		}
	};

	this.positionVisibility = function (widget) {
		switch (widget.name) {

			case 'widgetProducts':
				widget.parent.getField("positionswidget").isVisible = true;
				break;

			case 'widgetCancel':
				widget.parent.getField("positionswidget").isVisible = false;
				break;
		}
	};

	this.splitProducts = function (container) {
		var widgetPositions = container.getWidget("widgetSplit");
		var widgetProducts = container.getWidget("widgetProducts");
		var positions = widgetPositions.getField("positionswidget").position;
		var selectedProducts = widgetProducts.getCheckedRows();
		positions = positions.map(function (pos) {
			return ++pos;
		});

		var NRVENDAREST = [];
		var NRCOMANDA = [];
		var NRPRODCOMVEN = [];

		var isValid = true;
		selectedProducts.forEach(function (product) {
			if (positions.length > UtilitiesService.removeCurrency(product.preco) * 100) {
				isValid = false;
			}
			NRVENDAREST.push(product.NRVENDAREST);
			NRCOMANDA.push(product.nrcomanda);
			NRPRODCOMVEN.push(product.NRPRODCOMVEN);
		});

		if (isValid) {
			OperatorRepository.findAll().then(function (operatorData) {
				TableService.splitProducts(operatorData[0].chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, positions).then(function (data) {
					self.loadProducts();
					self.loadOriginalProducts();
				});

			});
			this.splitProductsValidation();
		} else {
			ScreenService.showMessage('Não é possível realizar a divisão de um ou mais produtos para esta seleção de posições, pois o preço total do mesmo irá ficar menor que 1 centavo.');
		}
	};

	this.cancelSplitedProducts = function (container) {
		var widgetProducts = container.getWidget("widgetProducts");
		var widgetCancel = container.getWidget("widgetCancel");
		var selectedProducts = widgetCancel.getCheckedRows();

		var NRVENDAREST = [];
		var NRCOMANDA = [];
		var NRPRODCOMVEN = [];

		selectedProducts.forEach(function (product) {
			NRVENDAREST.push(product.NRVENDAREST);
			NRCOMANDA.push(product.nrcomanda);
			NRPRODCOMVEN.push(product.NRPRODCOMVEN);
		});

		OperatorRepository.findAll().then(function (operatorData) {
			TableService.cancelSplitedProducts(operatorData[0].chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN).then(function (data) {
				self.loadOriginalProducts();
				self.loadProducts();
				widgetProducts.activate();
			});
		});
	};

	this.generatePositionCode = function (args) {
		var selectedPosition = args.owner.data('position') + 1;
		OperatorRepository.findAll().then(function (operatorData) {
			TableActiveTable.findAll().then(function (table) {
				TableService.generatePositionCode(operatorData[0].chave, table[0].NRVENDAREST, table[0].NRCOMANDA, selectedPosition);
			});
		});
	};

	this.showRequestedTableDialog = function (chave) {
		ScreenService.showCustomDialog(
			"A conta para esta mesa já foi solicitada.",
			"alert", false,
			[{
				label: "Reabrir Mesa",
				code: function (e, deferred) {
					PermissionService.checkAccess('liberarMesa').then(function () {
						self.handleFilterParameters();
					}.bind(this));
					deferred.resolve(e);
				}
			},
			{
				label: "Receber Mesa",
				code: function (e, deferred) {
					AccountController.getAccountData(function (accountData) {
						TableService.changeTableStatus(chave, accountData[0].NRVENDAREST, accountData[0].NRCOMANDA, 'R').then(function (response) {
							self.openAccountPayment();
						}.bind(this));
					}.bind(this));
					deferred.resolve(e);
				}
			},
			{
				label: "Cancelar",
				code: function (e, deferred) {
					deferred.reject(e);
				}
			}]
		);
	};

	this.handleShowPositions = function (popupWidget, tableAlreadyOpen) {
		OperatorRepository.findOne().then(function (operatorData) {
			// this is to avoid running this function when closing the clients/consumers select component
			if (!this.blockPopupOnEnterEvent) {
				// get fields
				var positionsField = popupWidget.getField('positionsField');
				var spinPositionControl = popupWidget.getField('NRPOSICAOMESA');
				var radioTablePositions = popupWidget.getField('radioTablePositions');
				var NMCONSUMIDOR = popupWidget.getField('NMCONSUMIDOR');
				var consumerSearch = popupWidget.getField('consumerSearch');
				var NMRAZSOCCLIE = popupWidget.getField('NMRAZSOCCLIE');
				var DSCONSUMIDOR = popupWidget.getField('DSCONSUMIDOR');
				var QRCODEACTION = popupWidget.getAction('qrcode');
				var controlPos = 2;

				if (operatorData.IDLUGARMESA === 'N') {
					radioTablePositions.isVisible = false;
					spinPositionControl.isVisible = false;
					controlPos = 1;
				}

				// reset popup
				popupWidget.currentRow.CDCLIENTE = null;
				popupWidget.currentRow.NMRAZSOCCLIE = null;
				popupWidget.currentRow.CDCONSUMIDOR = null;
				popupWidget.currentRow.NMCONSUMIDOR = null;
				popupWidget.currentRow.DSCONSUMIDOR = null;
				popupWidget.currentRow.consumerSearch = null;
				popupWidget.currentRow.IDSITCONSUMI = null;
				popupWidget.currentRow.NRPOSICAOMESA = controlPos;
				popupWidget.currentRow.NRPESMESAVEN = controlPos;
				NMRAZSOCCLIE.readOnly = false;
				NMCONSUMIDOR.readOnly = false;
				if (consumerSearch) consumerSearch.readOnly = false;
				if (DSCONSUMIDOR) {
					DSCONSUMIDOR.isVisible = false;
				}
				positionsField.isVisible = false;
				radioTablePositions.setValue('M');
				positionsField.dataSource.data[0].NRPOSICAOMESA = 2;
				positionsField.dataSource.data[0].clientMapping = {};
				positionsField.dataSource.data[0].consumerMapping = {};
				positionsField.dataSource.data[0].positionNamedMapping = {};

				TableActiveTable.findOne().then(function (tableData) {

					popupWidget.getField('NRPOSICAOMESA').maxValue = operatorData.NRMAXPESMES;

					var permiteDig = operatorData.IDPERDIGCONS == 'S';
					NMRAZSOCCLIE.readOnly = permiteDig;
					NMCONSUMIDOR.readOnly = permiteDig;
					if (QRCODEACTION) {
						QRCODEACTION.isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();
						QRCODEACTION.readOnly = false;
					}

					if (tableAlreadyOpen && tableData) {
						positionsField.dataSource.data[0].NRPOSICAOMESA = tableData.NRPOSICAOMESA;
						popupWidget.currentRow.NRPOSICAOMESA = tableData.NRPOSICAOMESA;

						if (tableData.posicoes.length > 0) {
							radioTablePositions.setValue('P');
							positionsField.isVisible = true;
							NMCONSUMIDOR.readOnly = true;
							NMRAZSOCCLIE.readOnly = true;
							if (consumerSearch) consumerSearch.readOnly = true;
							if (DSCONSUMIDOR) {
								DSCONSUMIDOR.isVisible = operatorData.IDUTLNMCONSMESA === 'S';
								DSCONSUMIDOR.readOnly = true;
							}
							if (QRCODEACTION) {
								QRCODEACTION.readOnly = true;
							}
							var positionsObject = _.get(tableData, 'posicoes', {});
							positionsField.dataSource.data[0].clientMapping = self.buildClientMapping(positionsObject);
							positionsField.dataSource.data[0].consumerMapping = self.buildConsumerMapping(positionsObject);
							positionsField.dataSource.data[0].positionNamedMapping = self.buildPositionNamedMapping(positionsObject);
							templateManager.updateTemplate();
						} else {
							if (tableData.CDCLIENTE) {
								NMCONSUMIDOR.readOnly = false;
								self.prepareCustomers(NMCONSUMIDOR, tableData.CDCLIENTE);
							}
							if (consumerSearch) consumerSearch.readOnly = false;
							if (DSCONSUMIDOR) {
								DSCONSUMIDOR.isVisible = false;
							}
							popupWidget.currentRow.CDCLIENTE = tableData.CDCLIENTE;
							popupWidget.currentRow.NMRAZSOCCLIE = tableData.NMRAZSOCCLIE;
							popupWidget.currentRow.CDCONSUMIDOR = tableData.CDCONSUMIDOR;
							popupWidget.currentRow.NMCONSUMIDOR = tableData.NMCONSUMIDOR;
						}
					}
					if ((operatorData.modoHabilitado === 'M') && (operatorData.IDLUGARMESA == 'S')) {
						radioTablePositions.isVisible = true;
						spinPositionControl.isVisible = true;
						WaiterNamedPositionsState.initializeTemplate();
					}
				});
			}
			this.blockPopupOnEnterEvent = false;
		}.bind(this));
	};

	this.initConsumerPopup = function (popupWidget, modeWidget) {

		fieldCopy = angular.copy(modeWidget.fields[1].dataSource.data[0]);

		if (!this.blockPopupOnEnterEvent) {
			if (modeWidget.fields[0].value() === "M" || !_.isEmpty(modeWidget.fields[1].position)) {
				var NMCONSUMIDOR = popupWidget.getField('NMCONSUMIDOR');
				var NMRAZSOCCLIE = popupWidget.getField('NMRAZSOCCLIE');
				var QRCODEACTION = popupWidget.getAction('qrcode');
				var consumerSearch = popupWidget.getField('consumerSearch');

				popupWidget.currentRow.CDCLIENTE = null;
				popupWidget.currentRow.NMRAZSOCCLIE = null;
				popupWidget.currentRow.CDCONSUMIDOR = null;
				popupWidget.currentRow.NMCONSUMIDOR = null;
				popupWidget.currentRow.consumerSearch = null;
				popupWidget.currentRow.IDSITCONSUMI = null;
				popupWidget.currentRow.NRPOSICAOMESA = 2;
				NMRAZSOCCLIE.readOnly = false;
				NMCONSUMIDOR.readOnly = false;
				consumerSearch.readOnly = false;

				TableActiveTable.findOne().then(function (tableData) {
					OperatorRepository.findOne().then(function (operatorData) {

						var permiteDig = operatorData.IDPERDIGCONS == 'S';
						NMRAZSOCCLIE.readOnly = permiteDig;
						NMCONSUMIDOR.readOnly = permiteDig;
						consumerSearch.readOnly = permiteDig;

						QRCODEACTION.isVisible = !Util.isDesktop() && !UtilitiesService.isPoyntDevice();

						var positionsObject = _.get(tableData, 'posicoes', {});
						modeWidget.fields[1].dataSource.data[0].clientMapping = self.buildClientMapping(positionsObject);
						modeWidget.fields[1].dataSource.data[0].consumerMapping = self.buildConsumerMapping(positionsObject);
						modeWidget.fields[1].dataSource.data[0].positionNamedMapping = self.buildPositionNamedMapping(positionsObject);

						var positionsData = AccountController.handleConsumerPositionsOnPayment(false, Array());

						if (tableData) {
							popupWidget.currentRow.NRPOSICAOMESA = tableData.NRPOSICAOMESA;

							if (modeWidget.fields[0].value() === "P") {
								popupWidget.currentRow.CDCLIENTE = positionsData.CDCLIENTE;
								popupWidget.currentRow.NMRAZSOCCLIE = positionsData.NMRAZSOCCLIE;
								popupWidget.currentRow.CDCONSUMIDOR = positionsData.CDCONSUMIDOR;
								popupWidget.currentRow.NMCONSUMIDOR = positionsData.NMCONSUMIDOR;
							}
							else if (tableData.CDCLIENTE) {
								popupWidget.currentRow.CDCLIENTE = tableData.CDCLIENTE;
								popupWidget.currentRow.NMRAZSOCCLIE = tableData.NMRAZSOCCLIE;
								popupWidget.currentRow.CDCONSUMIDOR = tableData.CDCONSUMIDOR;
								popupWidget.currentRow.NMCONSUMIDOR = tableData.NMCONSUMIDOR;
							}
						}

						ScreenService.openPopup(popupWidget);
					});
				});
			}
			else {
				ScreenService.showMessage("Escolha uma posição para informar um consumidor.");
			}
		}
		this.blockPopupOnEnterEvent = false;
	};

	this.restorePositions = function (positionsField) {
		positionsField.dataSource.data[0] = fieldCopy;
	};

	this.restorePositionsCopy = function (positionsField) {
		positionsField.dataSource.data[0] = angular.copy(fieldCopy);
	};

	this.updatePositionsCopy = function (positionsField) {
		fieldCopy = angular.copy(positionsField.dataSource.data[0]);
	};

	this.doBlockPopupOnEnterEvent = function () {
		this.blockPopupOnEnterEvent = true;
	};

	this.handlePositionsRadioChange = function (radioTablePositions, positionsField, NMRAZSOCCLIE, NMCONSUMIDOR, DSCONSUMIDOR, quantityField, tableAlreadyOpen) {
		OperatorRepository.findOne().then(function (operatorData) {
			TableActiveTable.findOne().then(function (tableData) {
				var NRPOSICAOMESA;
				if (tableAlreadyOpen && !quantityField) {
					NRPOSICAOMESA = tableData.NRPOSICAOMESA;
				} else {
					NRPOSICAOMESA = quantityField.value();
				}

				NMRAZSOCCLIE.clearValue();
				NMCONSUMIDOR.clearValue();
				NMRAZSOCCLIE.readOnly = true;
				NMCONSUMIDOR.readOnly = true;
				NMCONSUMIDOR.dataSourceFilter[0].value = "";
				if (!_.isEmpty(NMCONSUMIDOR.dataSourceFilter[1])) {
					NMCONSUMIDOR.dataSourceFilter[1].value = "%%";
				}
				self.clearConsumerRow(NMCONSUMIDOR.widget.currentRow);
				var qrCodeButton = positionsField.widget.getAction('qrcode');
				var consumerSearch = positionsField.widget.getField('consumerSearch');
				if (consumerSearch) consumerSearch.clearValue();

				if (radioTablePositions.value() === 'P') {
					positionsField.isVisible = true;
					positionsField.dataSource.data[0].NRPOSICAOMESA = NRPOSICAOMESA;
					WaiterNamedPositionsState.unselectAllPositions();
					if (operatorData.IDUTLNMCONSMESA === 'S' && DSCONSUMIDOR) {
						DSCONSUMIDOR.isVisible = true;
						DSCONSUMIDOR.readOnly = true;
						DSCONSUMIDOR.clearValue();
					}
					if (consumerSearch) consumerSearch.readOnly = true;
					if (qrCodeButton) qrCodeButton.readOnly = true;
				}
				else {
					positionsField.isVisible = false;
					NMRAZSOCCLIE.readOnly = false;
					NMCONSUMIDOR.readOnly = false;
					if (DSCONSUMIDOR) {
						DSCONSUMIDOR.isVisible = false;
					}
					if (consumerSearch) consumerSearch.readOnly = false;
					if (qrCodeButton) qrCodeButton.readOnly = false;
				}
			});
		});
	};

	this.updatePositionsField = function (quantityField, positionsField) {
		if (positionsField.isVisible === true) {
			positionsField.dataSource.data[0].NRPOSICAOMESA = quantityField.value();
		}
	};

	this.handleOpenTablePositionChange = function (positionsField) {
		var qrCodeButton = positionsField.widget.getAction('qrcode');
		var NMRAZSOCCLIE = positionsField.widget.getField('NMRAZSOCCLIE');
		var NMCONSUMIDOR = positionsField.widget.getField('NMCONSUMIDOR');
		var consumerSearch = positionsField.widget.getField('consumerSearch');
		var DSCONSUMIDOR = positionsField.widget.getField('DSCONSUMIDOR');

		if (positionsField.position.length === 0) {
			NMRAZSOCCLIE.clearValue();
			NMCONSUMIDOR.clearValue();
			NMRAZSOCCLIE.readOnly = true;
			NMCONSUMIDOR.readOnly = true;
			if (consumerSearch) {
				consumerSearch.clearValue();
				consumerSearch.readOnly = true;
			}
			if (DSCONSUMIDOR) {
				DSCONSUMIDOR.clearValue();
				DSCONSUMIDOR.readOnly = true;
			}
			if (qrCodeButton) qrCodeButton.readOnly = true;
		} else {
			if (NMRAZSOCCLIE.readOnly === true) {
				NMRAZSOCCLIE.readOnly = false;
				NMCONSUMIDOR.readOnly = false;
			} else {
				if (mustUnselectPositions(positionsField.dataSource.data[0], positionsField.position)) {
					unselectPositions(positionsField, positionsField.newPosition);
				}
			}
			if (consumerSearch) {
				consumerSearch.clearValue();
				consumerSearch.readOnly = false;
			}
			if (DSCONSUMIDOR) {
				DSCONSUMIDOR.readOnly = false;
			}
			if (qrCodeButton) qrCodeButton.readOnly = false;
			updateClientField(positionsField, NMRAZSOCCLIE, NMCONSUMIDOR, DSCONSUMIDOR);
		}
	};

	function updateClientField(positionsField, NMRAZSOCCLIE, NMCONSUMIDOR, DSCONSUMIDOR) {
		var currentRow = positionsField.widget.currentRow;
		var clientMapping = positionsField.dataSource.data[0].clientMapping;
		var positionNamedMapping = positionsField.dataSource.data[0].positionNamedMapping;
		var position = positionsField.position[0] + 1;

		if (clientMapping[position]) {
			currentRow.CDCLIENTE = clientMapping[position].CDCLIENTE;
			currentRow.NMRAZSOCCLIE = clientMapping[position].NMRAZSOCCLIE;
			self.prepareCustomers(NMCONSUMIDOR, clientMapping[position].CDCLIENTE, true, positionsField);
		} else {
			NMRAZSOCCLIE.clearValue();
			NMCONSUMIDOR.clearValue();
			NMCONSUMIDOR.dataSourceFilter[0].value = "";
		}
		if (DSCONSUMIDOR) {
			if (positionNamedMapping[position]) {
				currentRow.DSCONSUMIDOR = positionNamedMapping[position].DSCONSUMIDOR;
			} else {
				DSCONSUMIDOR.clearValue();
			}
		}
	}

	function updateConsumerField(positionsField, NMCONSUMIDOR) {
		var consumerMapping = positionsField.dataSource.data[0].consumerMapping;
		var position = positionsField.position[0] + 1;

		if (consumerMapping[position]) {
			NMCONSUMIDOR.widget.currentRow.CDCONSUMIDOR = consumerMapping[position].CDCONSUMIDOR;
			NMCONSUMIDOR.widget.currentRow.NMCONSUMIDOR = consumerMapping[position].NMCONSUMIDOR;
		} else {
			NMCONSUMIDOR.clearValue();
		}
	}

	function unselectPositions(positionsField, newPosition) {
		positionsField.position.forEach(function (currentPosition) {
			if (currentPosition !== newPosition) {
				positionsField.toggleButtonSelectedStatus(positionsField, currentPosition, true);
			}
		});
	}

	function mustUnselectPositions(data, selectedPositions) {
		var clientMapping = data.clientMapping;
		var consumerMapping = data.consumerMapping;
		var positionNamedMapping = data.positionNamedMapping;
		var mustUnselectPositions = false;
		var clientSelected = null;
		var currentClient = null;
		var currentPosition;

		for (var idx in selectedPositions) {
			currentPosition = selectedPositions[idx] + 1;
			currentClient = {
				'CLIENTE': _.get(clientMapping[currentPosition], 'CDCLIENTE', null),
				'CONSUMIDOR': _.get(consumerMapping[currentPosition], 'CDCONSUMIDOR', null),
				'POSITIONAMED': _.get(positionNamedMapping[currentPosition], 'DSCONSUMIDOR', null)
			};

			if (!clientSelected) {
				clientSelected = currentClient;
			} else if (!_.isMatch(clientSelected, currentClient)) {
				mustUnselectPositions = true;
				break;
			}
		}

		return mustUnselectPositions;
	}

	this.blockPopupOnEnterEvent = false;

	this.updatePositionLabel = function (fieldOnWidget) {
		var positionsField = fieldOnWidget.widget.getField('positionsField');
		if (fieldOnWidget.widget.container.name === "shortAccount") {
			positionsField = fieldOnWidget.widget.container.getWidget('accountDetails').getField('positionsField');
		}
		if (positionsField && positionsField.isVisible === true && positionsField.position.length > 0) {
			var NMRAZSOCCLIE = fieldOnWidget.widget.getField('NMRAZSOCCLIE');
			var NMCONSUMIDOR = fieldOnWidget.widget.getField('NMCONSUMIDOR');
			var DSCONSUMIDOR = fieldOnWidget.widget.getField('DSCONSUMIDOR');

			var textNMRAZSOCCLIE = NMRAZSOCCLIE.value();
			var textNMCONSUMIDOR = NMCONSUMIDOR.value();
			var textDSCONSUMIDOR = DSCONSUMIDOR ? DSCONSUMIDOR.value() : null;

			positionsField.position.forEach(function (currentPosition) {
				currentPosition = currentPosition + 1;

				var newClientMapping = null;
				if (textNMRAZSOCCLIE) {
					newClientMapping = {
						'CDCLIENTE': NMRAZSOCCLIE.widget.currentRow.CDCLIENTE,
						'NMRAZSOCCLIE': textNMRAZSOCCLIE
					};
				}
				positionsField.dataSource.data[0].clientMapping[currentPosition] = newClientMapping;

				var newConsumerMapping = null;
				if (textNMCONSUMIDOR) {
					newConsumerMapping = {
						'CDCONSUMIDOR': NMCONSUMIDOR.widget.currentRow.CDCONSUMIDOR,
						'NMCONSUMIDOR': textNMCONSUMIDOR
					};
				}
				positionsField.dataSource.data[0].consumerMapping[currentPosition] = newConsumerMapping;

				var newpositionNamedMapping = null;
				if (textDSCONSUMIDOR) {
					newpositionNamedMapping = {
						'DSCONSUMIDOR': textDSCONSUMIDOR
					};
				}
				positionsField.dataSource.data[0].positionNamedMapping[currentPosition] = newpositionNamedMapping;
			});

			positionsField.dataSource.data[0].clientChanged = true;
		}
	};

	this.toggleDelayedProduct = function (widget) {
		var selectedItem = widget.selectedRow;
		var itemsToToggle = widget.dataSource.data.forEach(function (item) {
			if (!_.isEmpty(item.NRSEQPRODCOM)) {
				if (item.NRSEQPRODCOM == selectedItem.NRSEQPRODCOM && !_.isEqual(item, selectedItem)) {
					item.__isSelected = !selectedItem.__isSelected;
				}
			}
		});
	};

	var t;
	this.consumerSearch = function (widget) {
		clearTimeout(t);
		var searchConsumer = function () {
			var consumerField = widget.getField('NMCONSUMIDOR');

			consumerField.clearValue();
			consumerField.dataSourceFilter = [
				{
					name: 'CDCLIENTE',
					operator: '=',
					value: _.isEmpty(widget.currentRow.CDCLIENTE) ? "" : widget.currentRow.CDCLIENTE
				},
				{
					name: 'CDCONSUMIDOR',
					operator: '=',
					value: widget.currentRow.consumerSearch
				}
			];
			consumerField.reload().then(function (search) {
				search = search.dataset.ParamsCustomerRepository;
				if (!_.isEmpty(search)) {
					if (search.length == 1) {
						search = search[0];
						widget.currentRow.CDCLIENTE = search.CDCLIENTE;
						widget.currentRow.NMCONSUMIDOR = search.NMCONSUMIDOR;
						widget.currentRow.CDCONSUMIDOR = search.CDCONSUMIDOR;
						widget.currentRow.NMRAZSOCCLIE = search.NMRAZSOCCLIE;
						widget.currentRow.IDSITCONSUMI = search.IDSITCONSUMI;

						consumerField.setValue(search.NMCONSUMIDOR);
						if (consumerField.change) {
							consumerField.change();
						}
					} else {
						self.handleConsumerField(consumerField);
						consumerField.openField();
					}
				}
			}.bind(this));
		}.bind(this);
		t = setTimeout(searchConsumer, 1000);
	};

	this.handleSetConsumer = function (field) {
		var currentRow = field.widget.currentRow;

		if (currentRow.IDSITCONSUMI == '2') {
			ScreenService.showMessage('Operação bloqueada. O consumidor está inativo.', 'alert');
			self.clearConsumerRow(currentRow);
		}
		else if (currentRow.IDSOLSENHCONS === 'S' && currentRow.CDSENHACONS !== null) {
			PermissionService.promptConsumerPassword(currentRow.CDCLIENTE, currentRow.CDCONSUMIDOR).then(
				function () {
					self.updatePositionLabel(field);
				},
				function () {
					currentRow.NMCONSUMIDOR = null;
					currentRow.CDCONSUMIDOR = null;
					currentRow.IDSITCONSUMI = null;
					self.updatePositionLabel(field);
				}
			);
		}
		else {
			self.updatePositionLabel(field);
		}
	};

	this.clearConsumerRow = function (currentRow) {
		currentRow.CDCLIENTE = null;
		currentRow.NMCONSUMIDOR = null;
		currentRow.CDCONSUMIDOR = null;
		currentRow.NMRAZSOCCLIE = null;
		currentRow.IDSITCONSUMI = null;
	};

	this.handleConsumerField = function (consumerField) {
		OperatorRepository.findOne().then(function (operatorData) {
			consumerField.selectWidget.floatingControl = false;
		});
	};

	this.handleEnterButton = function (args) {
		var keyCode = args.e.keyCode;
		if (keyCode === 13 || keyCode === 9) {
			UtilitiesService.handleCloseKeyboard();
			var widget = args.owner.field.widget;

			if (widget.name === 'setPassWaiterPopUp') {
				this.validatePassword(widget, widget.container.getWidget('setPassWaiterPopUp').returnParam);
			}
		}
	};

	this.partialPrint = function (widget) {
		AccountController.getAccountData(function (accountData) {
			OperatorRepository.findOne().then(function (operatorData) {
				AccountGetAccountDetails.findOne().then(function (accountDetails) {
					AccountService.getAccountDetails(operatorData.chave, operatorData.modoHabilitado, accountData[0].NRCOMANDA, accountData[0].NRVENDAREST, 'I', accountDetails.posicao).then(function (data) {
						AccountController.handlePrintBill(data.dadosImpressao);
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	/* Atualiza a informação do vendedor que abriu a mesa */
	this.handleVendedorAbertura = function (tableInfoStripe) {
		OperatorRepository.findOne().then(function (params) {
			// Verifica se está no modo mesa
			if (params.modoHabilitado === 'M') {
				TableActiveTable.findOne().then(function (tableData) {
					tableInfoStripe.NMVENDEDORABERT = tableData.NMVENDEDORABERT;
				});
			}
		});
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register('TableController', TableController);
});