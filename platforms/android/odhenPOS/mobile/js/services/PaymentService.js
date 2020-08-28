function PaymentService(ApplicationContext, PaymentRepository, Query, PaymentPayAccount, OperatorRepository, IntegrationService, PrintTEFVoucher, GetConsumerLimit, ZHPromise, AccountSaleCode, ChargePersonalCredit, CartPricesRepository, PrinterService, QRCodeSaleRepository, ScreenService, SavePayment, RemovePayment, ParamsParameterRepository){

	var self = this;

	/* Tipos de pagamento que realiza transação eletrônica por integração
		1 -> Credito
		2 -> Debito
		F/G -> Voucher
		H -> Mercado Pago
	*/
	this.PAYMENT_INTEGRATION = ['1', '2', 'F', 'G', 'H'];

	this.PAYMENT_CREDIT_DEBIT = ['9', 'A'];

	this.isOnSale = false;

	this.initializePayment = function (accountData, params, accountDetails, ITEMVENDA, NRLUGARMESA, ITVENDADES, payments, PRODSENHAPED) {
		return self.formatPaymentData(accountData, params, accountDetails, ITEMVENDA, NRLUGARMESA, ITVENDADES, payments, PRODSENHAPED).then(function (paramsPayment) {
			IntegrationService.waitingIntegration = Array();
			return PaymentRepository.save(paramsPayment);
		});
	};


    this.formatPaymentData = function(accountData, params, accountDetails, ITEMVENDA, NRLUGARMESA, ITVENDADES, payments, PRODSENHAPED){
        return new Promise(function(resolve){
            return OperatorRepository.findOne().then(function(operatorData){
	            var CDCLIENTE      = !!accountData.CDCLIENTE ? accountData.CDCLIENTE : '';
	            var NMRAZSOCCLIE   = !!accountData.NMRAZSOCCLIE ? accountData.NMRAZSOCCLIE : '';
	            var CDCONSUMIDOR   = !!accountData.CDCONSUMIDOR ? accountData.CDCONSUMIDOR : '';
	            var NMCONSUMIDOR   = !!accountData.NMCONSUMIDOR ? accountData.NMCONSUMIDOR : '';
	            var CDVENDEDOR     = null;
	            var NMFANVEN       = null;
	            var NRMESA         = !!accountData.NRMESA ? accountData.NRMESA : '';
	            var NRPESMESAVEN   = !!accountData.NRPESMESAVEN ? accountData.NRPESMESAVEN : '';
	            var NRVENDAREST    = !!accountData.NRVENDAREST ? accountData.NRVENDAREST : '';
	            var NRCOMANDA      = !!accountData.NRCOMANDA ? accountData.NRCOMANDA : '';
	            var CREDITOPESSOAL = !!accountData.CREDITOPESSOAL ? accountData.CREDITOPESSOAL : '';
	            var CDFAMILISALD   = !!accountData.CDFAMILISALD ? accountData.CDFAMILISALD : '';
	            var VRRECARGA      = !!accountData.VRRECARGA ? accountData.VRRECARGA : '';
	            var numeroProdutos = !!accountDetails.numeroProdutos ? accountDetails.numeroProdutos : 1;
	            var TOTALVENDA	   = accountDetails.vlrtotal;
	            var VRTXSEVENDA    = accountDetails.vlrservico;
	            var VRCOUVERT      = accountDetails.vlrcouvert;
	            var TOTALSUBSIDY   = accountDetails.totalSubsidy;
	            var REALSUBSIDY    = accountDetails.realSubsidy;
	            var TOTAL          = parseFloat((accountDetails.vlrprodutos - accountDetails.vlrdesconto).toFixed(2));
	            var IDTPVENDACONS  = null;
	            var CDSUPERVISORs  = !!accountDetails.CDSUPERVISORs ? accountDetails.CDSUPERVISORs : '';
	            var logServico 	   = !!accountDetails.logServico ? accountDetails.logServico : '';
	            var CDSUPERVISORd  = !!accountDetails.CDSUPERVISORd ? accountDetails.CDSUPERVISORd : '';
	            var logDesconto    = !!accountDetails.logDesconto ? accountDetails.logDesconto : '';

	            if(!_.isEmpty(payments)) {
					var bindedGetCustomReceObject = _.bind(self.getCustomReceObject, this, operatorData);
					payments = _.map(payments, bindedGetCustomReceObject);
				}

	            var result = {
	                TIPORECE: payments == null ? Array() : payments,
	                ITEMVENDA: ITEMVENDA,
	                ITVENDADES: ITVENDADES,
	                PRODSENHAPED: PRODSENHAPED,
	                DATASALE: {
	                    // valores da venda
	                    TOTALVENDA: TOTALVENDA,
	                    FALTANTE: TOTALVENDA,
	                    VALORPAGO: 0,
	                    TROCO: 0,
	                    REPIQUE: 0,
	                    TOTAL: TOTAL,
	                    TOTALSUBSIDY: TOTALSUBSIDY,
	                    REALSUBSIDY: REALSUBSIDY,
	                    // taxa de serviço
	                    VRTXSEVENDA: VRTXSEVENDA,
	                    // couvert
	                    VRCOUVERT: VRCOUVERT,
	                    // desconto
	                    VRDESCONTO: 0,
	                    PCTDESCONTO: 0,
	                    TIPODESCONTO: 'P',
	                    FIDELITYDISCOUNT: accountDetails.fidelityDiscount,
	                    FIDELITYVALUE: accountDetails.fidelityValue
	                },
	                CDCLIENTE: CDCLIENTE,
	                NMRAZSOCCLIE: NMRAZSOCCLIE,
	                CDCONSUMIDOR: CDCONSUMIDOR,
	                NMCONSUMIDOR: NMCONSUMIDOR,
	                CDVENDEDOR: CDVENDEDOR,
	                NMFANVEN: NMFANVEN,
	                NRMESA: NRMESA,
	                NRPESMESAVEN: NRPESMESAVEN,
	                NRVENDAREST: NRVENDAREST,
	                NRCOMANDA: NRCOMANDA,
	                CREDITOPESSOAL: CREDITOPESSOAL,
	                CDFAMILISALD: CDFAMILISALD,
	                VRRECARGA: VRRECARGA,
	                chave: params.chave,
	                NRLUGARMESA: NRLUGARMESA,
	                numeroProdutos: numeroProdutos,
	                servico: {
	                    CDSUPERVISOR: CDSUPERVISORs,
	                    logServico: logServico
	                },
	                desconto: {
	                    CDSUPERVISOR: CDSUPERVISORd,
	                    logDesconto: logDesconto
	                },
	                DELIVERY: false
	            };


	            self.calcTotalSale(result);
	            // Verifica parametros e se houve alteração da taxa de serviço para mostrar o label do repique na tela de recebimento.
	            if (operatorData.IDTPCONTRREPIQ !== 'N' && !CREDITOPESSOAL) {
	            	ParamsParameterRepository.findOne().then(function (params) {
	            		// Se houve alteração no valor da taxa de serviço o repique é desabilitado.
	            		if (operatorData.modoHabilitado !== 'B' && operatorData.IDCOMISVENDA !== 'N') {
	            			result.showRepique = (accountDetails.vlrservoriginal == accountDetails.vlrservico) ? true : false;
	            		} else {
	            			result.showRepique = true;
	            		}
	            	}.bind(this));
	            } else {
	            	result.showRepique = false;
	            }

	            if (CDCLIENTE != '' && CDCONSUMIDOR != ''){
	                self.getConsumerLimit(CDCLIENTE, CDCONSUMIDOR, 'all').then(function(limits){
	                    result.limitDebito = limits[0].debito;
	                    result.limitCredito = limits[0].credito;
	                    result.IDTPVENDACONS = limits[0].IDTPVENDACONS;
	                    resolve(result);
	                });
	            }
	            else {
	                resolve(result);
	            }
            }.bind(this));
        });
	};

	this.getCustomReceObject = function(operatorData, payment) {
		var date = new Date(payment.DTHRINCMOV);
		date = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();

		return {
			'VRMOVIVEND': parseFloat(payment.VRMOV),
			'TRANSACTION': {
				'data': {
					'AUTHKEY': null,
					'CDLOJATEF': operatorData.CDLOJATEF,
					'CDTERTEF': operatorData.CDTERTEF,
					'DSENDIPSITEF': operatorData.DSENDIPSITEF,
					'CDBANCARTCR': payment.DSBANDEIRA,
					'CDNSUHOSTTEF': payment.CDNSUTEFMOB,
					'IDTIPORECE': payment.IDTIPORECE,
					'IDTPTEF': payment.IDTPTEF,
					'NRCARTBANCO': payment.NRCARTBANCO,
					'NRCONTROLTEF': payment.NRADMCODE,
					'PAYMENTCONFIRMATION': false,
					'REMOVEALLINTEGRATIONS': false,
					'STLPRIVIA': payment.TXPRIMVIATEF,
					'STLSEGVIA': payment.TXSEGVIATEF,
					'NRLUGARMESA': payment.NRLUGARMESA,
					'TRANSACTIONDATE': date.split(' ')[0].replace(/\//g, '')
				},
				'status': true
			},
			'CDTIPORECE': payment.CDTIPORECE,
			'DSBUTTON': payment.DSBUTTON,
			'IDDESABTEF': "N",
			'IDTIPORECE': payment.IDTIPORECE,
			'IDTPTEF': payment.IDTPTEF,
			'NRBUTTON': "",
			'DTHRINCOMVEN': date
		};
	};

	this.promiseFormatPaymentData = function(paymentData){
		var defer = ZHPromise.defer();
		defer.resolve(paymentData);
		return defer.promise;
	};

	this.getPaymentValue = function () {
		return PaymentRepository.findOne().then(function (payment) {
			return payment.DATASALE;
		});
	};

	this.findAllPayment = function () {
		return PaymentRepository.findOne().then(function (payment) {
			return payment.TIPORECE;
		});
	};

	this.handlePayment = function (currentRow) {
		// verifica se caixa e pagamento selecionado faz integração
		return self.checkIfMustCallIntegration(currentRow.tiporece).then(function (mustCallIntegration) {
			if (mustCallIntegration) {
				// chama integração
				return IntegrationService.integrationPayment(currentRow).then(function (integrationResult) {
					 if (!integrationResult.error) {
				        return self.savePayment(integrationResult.data).then(function(){
					        //self.handlePrintPayment(integrationResult.data.eletronicTransacion.data).then(function(){
						    	return self.setPaymentSale(integrationResult.data);
					        //}.bind(this));
				        }.bind(this));
				    } else {
                        ApplicationContext.UtilitiesService.backAfterFinish();
					    return integrationResult;
				    }
				});
			} else {
				// pagamento sem integração
				return self.setPaymentSale(currentRow);
			}
		}.bind(this));
	};

	this.checkIfMustCallIntegration = function (tiporece) {
		return OperatorRepository.findOne().then(function (operatorData) {
			return tiporece.IDDESABTEF !== 'S' && _.includes(self.PAYMENT_INTEGRATION, tiporece.IDTIPORECE) && operatorData.IDUTILTEF === 'T';
		}.bind(this));
	};


	this.handlePrintPayment = function(dataPrinter) {
		return new Promise(function(resolve) {
			var tefObject = {
				TEFVOUCHER: [{
					STLPRIVIA: dataPrinter.STLPRIVIA,
					STLSEGVIA: ''
				}]
			};

			self.handlePrintReceipt(tefObject, false);
			
			ScreenService.confirmMessage(
				'Deseja imprimir a via do cliente?', 'question', 
				function(){
					var tefPrintVoucher = tefObject.TEFVOUCHER[0];
					tefPrintVoucher.STLPRIVIA = '';
					tefPrintVoucher.STLSEGVIA = dataPrinter.STLSEGVIA;
					self.handlePrintReceipt(tefObject);
					resolve();
				}.bind(this),
				function(){
					resolve();
				}.bind(this)
			);
		});
	};

	this.savePayment = function(currentPayment) {
		return new Promise(function(resolve) {
			return PaymentRepository.findOne().then(function(paymentData) {
				var query = Query.build()
					.where('paymentData').equals(paymentData)
					.where('currentPayment').equals(currentPayment);

				return SavePayment.download(query).then(function(paymentData){
					resolve();
				});
			}.bind(this));
		});
	};

	this.checkIfMustCallIntegration = function(tiporece) {
		return OperatorRepository.findOne().then(function(operatorData) {
			return tiporece.IDDESABTEF !== 'S' 	&& _.includes(self.PAYMENT_INTEGRATION, tiporece.IDTIPORECE) && operatorData.IDUTILTEF === 'T';
		}.bind(this));
	};

	this.setPaymentSale = function (currentRow) {
		return PaymentRepository.findOne().then(function (payment) {
			// seta recebimento
			self.formatPriceChart(payment.TIPORECE, currentRow);
			// calcula valor pago no total da venda
			self.calcTotalSale(payment);
			// salva modificações do recebimento
			return PaymentRepository.save(payment).then(function () {
				return self.statusPaymentReturn(false, '', payment.DATASALE);
			}.bind(this));
		}.bind(this));
	};

	this.formatPriceChart = function (tiporece, currentRow) {
		var button = currentRow.tiporece;
		var onPaymentType = _.find(tiporece, function (editing) {
			return editing.CDTIPORECE === button.CDTIPORECE;
		});

		// pagamentos que realizam integração nunca se sobrescrevem
		if (_.includes(self.PAYMENT_INTEGRATION, button.IDTIPORECE) || _.isEmpty(onPaymentType)) {
			tiporece.push(self.defaultPaymentType(button, currentRow));
		}
		else if (_.includes(self.PAYMENT_CREDIT_DEBIT, button.IDTIPORECE)) {
			onPaymentType.VRMOVIVEND = currentRow.VRMOVIVEND;
			onPaymentType.DTHRINCOMVEN = self.dateTime();
		}
		else {
			onPaymentType.VRMOVIVEND += currentRow.VRMOVIVEND;
			onPaymentType.DTHRINCOMVEN = self.dateTime();
		}
	};

	this.defaultPaymentType = function (button, currentRow) {
		return {
			CDTIPORECE: button.CDTIPORECE,
			IDTIPORECE: button.IDTIPORECE,
			DSBUTTON: button.DSBUTTON,
			VRMOVIVEND: currentRow.VRMOVIVEND,
			REPIQUE: currentRow.REPIQUE > 0 ? currentRow.REPIQUE : 0,
			TRANSACTION: currentRow.eletronicTransacion,
			DTHRINCOMVEN: self.dateTime()
		};
	};

	this.dateTime = function () {
		var dateTime = new Date();

		return dateTime.getDateBr() +
			' ' + ApplicationContext.UtilitiesService.padLeft(dateTime.getHours(), 2, '0') +
			':' + ApplicationContext.UtilitiesService.padLeft(dateTime.getMinutes(), 2, '0') +
			':' + ApplicationContext.UtilitiesService.padLeft(dateTime.getSeconds(), 2, '0');
	};

	this.calcTotalSale = function (payment) {
		var DATASALE = payment.DATASALE;
		var amountPaid = 0;
		var repique = 0;

		payment.TIPORECE.forEach(function (tiporece) {
			amountPaid += tiporece.VRMOVIVEND;
			repique += tiporece.REPIQUE;
		});

		DATASALE.VALORPAGO = amountPaid;
		if (DATASALE.TOTALVENDA < amountPaid) {
		
			DATASALE.FALTANTE = 0;
			DATASALE.REPIQUE = repique;
			DATASALE.TROCO = parseFloat((amountPaid - DATASALE.TOTALVENDA).toFixed(2));
		} else {
			DATASALE.FALTANTE = parseFloat((DATASALE.TOTALVENDA - amountPaid).toFixed(2));
			DATASALE.TROCO = 0;
			DATASALE.REPIQUE = 0;
		}
	};

	this.handleRemovePayment = function (tiporece) {
		return new Promise(function (resolve) {
			self.updateSetReversal(tiporece.TRANSACTION.data, function (dataTransaction) {
				var arrTiporece = Array();

				if (!tiporece.TRANSACTION.status) {
					arrTiporece.push({
						'CDTIPORECE': tiporece.CDTIPORECE,
						'DTHRINCOMVEN': tiporece.DTHRINCOMVEN
					});

					resolve(self.removePaymentSale(arrTiporece));
				} else {
					self.findIntegrations().then(function (integrations) {
						if (!dataTransaction.REMOVEALLINTEGRATIONS) {
							arrTiporece.push({
								'CDTIPORECE': tiporece.CDTIPORECE,
								'CDNSUHOSTTEF': dataTransaction.CDNSUHOSTTEF,
								'DTHRINCOMVEN': tiporece.DTHRINCOMVEN
							});
						} else {
							arrTiporece = _.map(integrations.data, function (integration) {
								return {
									'CDTIPORECE': integration.CDTIPORECE
								};
							});
						}

						resolve(self.handleCancelIntegration(dataTransaction, arrTiporece, integrations.data));
					});
				}
			});
		});
	};

	this.updateSetReversal = function (dataTransaction, callback) {
		PaymentRepository.findAll().then(function (payments) {
			payments = payments[0].TIPORECE;
			payments = _.filter(payments, function (payment) {
				return payment.TRANSACTION.data.CDNSUHOSTTEF === dataTransaction.CDNSUHOSTTEF;
			});

			payments = _.map(payments, function (payment) {
				return payment.TRANSACTION.data;
			});

			callback(payments[0]);
		});
	};

	this.handleCancelIntegration = function (dataTransaction, arrTiporece, integrations) {
		if (!dataTransaction.REMOVEALLINTEGRATIONS && dataTransaction.IDTPTEF === '5') {
			integrations = _.filter(integrations, function (integration) {
				return integration.CDNSUHOSTTEF === dataTransaction.CDNSUHOSTTEF;
			}.bind(this));

			return IntegrationService.reversalIntegration(self.removePaymentSale, integrations).then(function(reversalIntegrationResult){
				if(!reversalIntegrationResult.error) {
					return self.removePayment(Array(dataTransaction)).then(function(){
						return self.handleIntegrationResult(reversalIntegrationResult, arrTiporece);
					}.bind(this));
				} else {
					return self.handleIntegrationResult(reversalIntegrationResult, arrTiporece);
				}
			}.bind(this));
		} else {
			return IntegrationService.cancelIntegration(dataTransaction).then(function (cancelIntegrationResult) {
				return self.handleIntegrationResult(cancelIntegrationResult, arrTiporece);
			}.bind(this));
		}
	};

	this.handleIntegrationResult = function (integrationResult, arrTiporece) {
		if (!integrationResult.error) {
			self.handleRefoundTEFVoucher(integrationResult.data);
			return self.removePaymentSale(arrTiporece);
		} else {
			return integrationResult;
		}
	};

	this.removePaymentSale = function (arrTiporece) {
		return PaymentRepository.findOne().then(function (payment) {
			// remove recebimento
			payment.TIPORECE = _.filter(payment.TIPORECE, function (tiporece) {
				var noMatch = true;
				var toMatch = {
					'CDTIPORECE': tiporece.CDTIPORECE,
					'DTHRINCOMVEN': tiporece.DTHRINCOMVEN,
					'CDNSUHOSTTEF': tiporece.TRANSACTION.data.CDNSUHOSTTEF
				};

				_.forEach(arrTiporece, function (toExclude) {
					if (_.isMatch(toMatch, toExclude)) {
						noMatch = false;
					}
				});

				return noMatch;
			});
			// recalcula valor
			self.calcTotalSale(payment);
			// salva alterações
			return PaymentRepository.save(payment).then(function () {
				return self.statusPaymentReturn(false, '', null);
			});
		});
	};

	this.handleRefoundTEFVoucher = function (arrRefoundIntegration) {
		if (!_.isEmpty(arrRefoundIntegration)) {
			var arrRefoundTEFVoucher = Array();
			if (!_.isArray(arrRefoundIntegration) && _.isObject(arrRefoundIntegration))
				arrRefoundIntegration = [arrRefoundIntegration];

			arrRefoundIntegration.forEach(function (refoundIntegration) {
				if (_.get(refoundIntegration, 'STLPRIVIA')) {
					arrRefoundTEFVoucher.push(refoundIntegration);
				}
			});

			if (!_.isEmpty(arrRefoundTEFVoucher)) {
				self.printTEFVoucher(arrRefoundTEFVoucher);
			}
		}
	};

	this.printTEFVoucher = function (arrTEFVoucher) {
		OperatorRepository.findOne().then(function (operatorData) {
			var query = Query.build();
			query = query.where('DATA').equals({
				'chave': operatorData.chave,
				'arrTEFVoucher': arrTEFVoucher
			});

			PrintTEFVoucher.download(query).then(function (printTEFVoucherResult) {
				if (!_.isEmpty(printTEFVoucherResult[0].data)) {
					printTEFVoucherResult = { TEFVOUCHER: printTEFVoucherResult[0].data };
					self.handlePrintReceipt(printTEFVoucherResult);
				}
			});
		});
	};

	this.payAccount = function () {
		return self.findIntegrations().then(function (integrations) {
			if (integrations.error) {
				return self.sendPayment();
			} else {
				integrations = integrations.data;
				var mustCompleteIntegrationResult = self.mustCompleteIntegration(integrations);

				if (mustCompleteIntegrationResult.length > 0) {
					return IntegrationService.completeIntegration(integrations).then(function (completeIntegration) {
						if (!completeIntegration.error) {
							return self.sendPayment();
						} else {
							return completeIntegration;
						}
					}.bind(this));
				} else {
					return self.sendPayment();
				}
			}
		}.bind(this));
	};

	this.mustCompleteIntegration = function (integrations) {
		// valida se as integrações realizadas necessitam de confirmação
		return integrations.filter(function (integration) {
			return integration.PAYMENTCONFIRMATION;
		});
	};

	this.sendPayment = function () {
		return AccountSaleCode.findOne().then(function (saleCodeObj) {
			return PaymentRepository.findOne().then(function (payment) {
				self.preparePayment(payment);

				var query = Query.build()
					.where('DATA').equals(payment)
					.where('saleCode').equals(saleCodeObj.saleCode);

				return PaymentPayAccount.download(query).then(function (data) {
					data = data[0];
					return self.statusPaymentReturn(data.error, data.error ? data.message : '', data);
				});
			}.bind(this));
		}.bind(this));
	};

	this.chargePersonalCredit = function (paymentDetails) {
		return self.findIntegrations().then(function (integrations) {
			if (integrations.error) {
				return self.personalCreditConfirmTransaction(paymentDetails);
			} else {
				integrations = integrations.data;
				var mustCompleteIntegrationResult = self.mustCompleteIntegration(integrations);

				if (mustCompleteIntegrationResult.length > 0) {
					return IntegrationService.completeIntegration(integrations).then(function (completeIntegration) {
						if (!completeIntegration.error) {
							return self.personalCreditConfirmTransaction(paymentDetails);
						} else {
							return completeIntegration;
						}
					}.bind(this));
				} else {
					return self.personalCreditConfirmTransaction(paymentDetails);
				}
			}
		}.bind(this));
	};

	this.personalCreditConfirmTransaction = function (paymentDetails) {
		return AccountSaleCode.findOne().then(function (saleCodeObj) {
			var query = Query.build()
				.where('DATA').equals(paymentDetails)
				.where('saleCode').equals(saleCodeObj.saleCode);
			return ChargePersonalCredit.download(query);
		});
	};

	this.preparePayment = function (payment) {
		payment.TIPORECE.forEach(function (tiporece) {
			Util.extend(tiporece, tiporece.TRANSACTION.data);
			_.unset(tiporece, 'TRANSACTION');
		});
	};

	this.handleCancelForSale = function (integrations) {
		// para PAYMENTCONFIRMATION = true é chamado o cancelamento
		return self.mustRedirectReversal(integrations) ?
			IntegrationService.cancelIntegration(integrations[0]) :
			IntegrationService.reversalIntegration(self.removePaymentSale, integrations).then(function(reversalIntegrationResult){
				if(!reversalIntegrationResult.error) {
					self.handleRefoundTEFVoucher(reversalIntegrationResult.data);

					return self.removePayment(integrations).then(function(){
						return reversalIntegrationResult;
					}.bind(this));
				} else {
					if(reversalIntegrationResult.data.length > 1) {
						var reversedPayments = Array();
						var paymentsToRemove = Array();
						reversalIntegrationResult.data.pop();

						reversalIntegrationResult.data.forEach(function(reversedPayment){
							paymentsToRemove.push(reversedPayment.toRemove);

							reversedPayment = {
								'CDNSUHOSTTEF': reversedPayment.toRemove.CDNSUHOSTTEF,
								'NRCONTROLTEF': reversedPayment.REVERSEDNRCONTROLTEF 
							};
							reversedPayments.push(reversedPayment); 
						});

						return self.removePayment(reversedPayments).then(function(){
							return self.removePaymentSale(paymentsToRemove).then(function(){
								self.handlePrintReceipt({TEFVOUCHER: reversalIntegrationResult.data});
								return reversalIntegrationResult;
							});
						}.bind(this));
					} else {
						return reversalIntegrationResult;
					}
				}
			}.bind(this)
		);
	};

	this.mustRedirectReversal = function (integrations) {
		// pega PAYMENTCONFIRMATION da primeira posição pois será o mesmo para qualquer recebimento
		return integrations[0].PAYMENTCONFIRMATION && this.isOnSale;
	};

	this.findIntegrations = function () {
		var tiporeceTransactions = Array();

		return self.findAllPayment().then(function (arrTiporece) {
			arrTiporece.forEach(function (tiporece) {
				if (tiporece.TRANSACTION.status) {
					tiporece.TRANSACTION.data.CDTIPORECE = tiporece.CDTIPORECE;
					tiporece.TRANSACTION.data.DTHRINCOMVEN = tiporece.DTHRINCOMVEN;
					tiporece.TRANSACTION.data.VRMOVIVEND = tiporece.VRMOVIVEND;
					tiporece.TRANSACTION.data.IDTIPORECE = tiporece.IDTIPORECE;
					tiporeceTransactions.push(tiporece.TRANSACTION.data);
				}
			});
			return self.statusPaymentReturn(tiporeceTransactions.length === 0, '', tiporeceTransactions);
		});
	};

	this.clearPayment = function () {
		PaymentRepository.clearAll();
	};

	this.statusPaymentReturn = function (error, message, data) {
		return {
			error: error,
			message: message,
			data: data
		};
	};

	this.setIsOnSale = function (ISONSALE) {
		this.isOnSale = ISONSALE;
		if (ISONSALE) {
			this.updateSaleCode();
		}
	};

	this.updateSaleCode = function () {
		var saleCodeObj = [{
			'saleCode': new Date().getTime()
		}];
		AccountSaleCode.clearAll().then(function () {
			AccountSaleCode.save(saleCodeObj);
		}.bind(this));
	};

	this.getConsumerLimit = function (CDCLIENTE, CDCONSUMIDOR, type) {
		var query = Query.build()
			.where('CDCLIENTE').equals(CDCLIENTE)
			.where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
			.where('type').equals(type);
		return GetConsumerLimit.download(query);
	};

	this.handleOpenDiscount = function () {
		return self.findAllPayment().then(function (TIPORECE) {
			return self.statusPaymentReturn(TIPORECE.length > 0, '', null);
		});
	};

	this.handleApplyDiscount = function (currentRow) {
		return PaymentRepository.findOne().then(function (payment) {
			self.calculateDiscount(currentRow, payment.DATASALE);
			var DATASALE = payment.DATASALE;
			// verifica se valor após desconto aplicado é maior que zero e se todos os itens terão valor final >= 0.01
			if (DATASALE.PCTDESCONTO < 100 &&
				(parseFloat((DATASALE.TOTAL - DATASALE.VRDESCONTO - DATASALE.FIDELITYVALUE).toFixed(2)) - (0.01 * payment.numeroProdutos)) >= 0) {
				// salva dados da venda com desconto aplicado
				payment.desconto.CDSUPERVISOR = currentRow.CDSUPERVISORd;
				payment.desconto.logDesconto = currentRow.TIPODESCONTO;
				payment.desconto.motivoDesconto = currentRow.MOTIVODESCONTO;
				payment.desconto.CDGRPOCORDESC = !_.isEmpty(currentRow.CDOCORR) ? currentRow.CDOCORR[0] : null;
				return PaymentRepository.save(payment).then(function () {
					return self.statusPaymentReturn(false, '', null);
				}.bind(this));
			} else {
				return self.statusPaymentReturn(true, '', null);
			}
		});
	};

	this.calculateDiscount = function (currentRow, DATASALE) {
		var VRDESCONTO = currentRow.VRDESCONTO;
		var TIPODESCONTO = currentRow.TIPODESCONTO;
		var TOTALVENDA = 0;

		if (TIPODESCONTO === 'P') {
			DATASALE.PCTDESCONTO = VRDESCONTO;
			DATASALE.VRDESCONTO = ApplicationContext.UtilitiesService.truncValue(DATASALE.TOTAL * (VRDESCONTO / 100));
		} else {
			DATASALE.PCTDESCONTO = ApplicationContext.UtilitiesService.truncValue((VRDESCONTO / DATASALE.TOTAL) * 100);
			DATASALE.VRDESCONTO = VRDESCONTO;
		}
		DATASALE.TIPODESCONTO = TIPODESCONTO;

		// desconto aplicado no valor bruto dos itens
		TOTALVENDA = parseFloat((DATASALE.TOTAL - DATASALE.VRDESCONTO - DATASALE.FIDELITYDISCOUNT).toFixed(2));

		// aplica valores calculados nos dados da venda
		DATASALE.TOTALVENDA = DATASALE.FALTANTE = Math.round((TOTALVENDA + DATASALE.VRTXSEVENDA + DATASALE.VRCOUVERT) * 100) / 100;
	};

	this.updateCartPrices = function (chave, products, CDCLIENTE, CDCONSUMIDOR) {
		var query = Query.build()
			.where('chave').equals(chave)
			.where('products').equals(products)
			.where('CDCLIENTE').equals(CDCLIENTE)
			.where('CDCONSUMIDOR').equals(CDCONSUMIDOR);
		return CartPricesRepository.download(query);
	};

    this.handlePrintReceipt = function(dadosImpressao, delayPrint) {
    	if(_.isUndefined(delayPrint)) {
    		delayPrint = true;
    	}

    	OperatorRepository.findOne().then(function(operatorData){
			if (!_.isEmpty(dadosImpressao)){
				if (_.get(dadosImpressao, 'TEXTOCUPOM1VIA')){
					
					PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOCUPOM1VIA);
					PrinterService.printerCommand(PrinterService.BARCODE_COMMAND, dadosImpressao.TEXTOCODIGOBARRAS);
					PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOQRCODE);
					PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTORODAPE);
					// Impressão do rodapé da API de Painel de senhas do Madero.
					if (!_.isEmpty(dadosImpressao.TEXTOPAINELSENHA)) {
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOPAINELSENHA.inicio);
						PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOPAINELSENHA.qrCode);
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOPAINELSENHA.final);
					}

					if (!_.isEmpty(dadosImpressao.TEXTOCUPOM2VIA)) {
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTOCUPOM2VIA);
						PrinterService.printerCommand(PrinterService.BARCODE_COMMAND, dadosImpressao.TEXTOCODIGOBARRAS);
						PrinterService.printerCommand(PrinterService.QRCODE_COMMAND, dadosImpressao.TEXTOQRCODE);
						PrinterService.printerCommand(PrinterService.TEXT_COMMAND, dadosImpressao.TEXTORODAPE);
					}
					var space = (operatorData.IDTPEMISSAOFOS  === 'SAT' || !_.isEmpty(dadosImpressao.TEXTOPAINELSENHA)) ? 2 : 0;
					space = operatorData.IDHABCAIXAVENDA === 'FOS' ? space + 1 : space;
					self.printerSpaceCommand(space);
				}

				if (_.get(dadosImpressao, 'TEFVOUCHER')) {
					dadosImpressao.TEFVOUCHER.forEach(function (tefVoucher) {
						if (!_.isEmpty(tefVoucher.STLPRIVIA)) {
							PrinterService.printerCommand(PrinterService.TEXT_COMMAND, tefVoucher.STLPRIVIA);
						 	self.printerSpaceCommand(2);

						 	if(delayPrint)
						 		PrinterService.printerCommand(PrinterService.DELAY_COMMAND, '3000');
	                    }
					});
					dadosImpressao.TEFVOUCHER.forEach(function(tefVoucher){
						if (!_.isEmpty(tefVoucher.STLSEGVIA)){
							PrinterService.printerCommand(PrinterService.TEXT_COMMAND, tefVoucher.STLSEGVIA);
							self.printerSpaceCommand(2);
						}
					});
				}

				// inicializa a impressão dos cupons
				// mesmo retornando seu resultado, não se trata a resposta da impressão (hoje)
				PrinterService.printerInit().then(function (result) {
					if (result.error)
						ScreenService.alertNotification(result.message);
				});
			}
		}.bind(this));
	};

	this.printerSpaceCommand = function (max) {
		for (var i = 0; i < max; i++) {
			PrinterService.printerSpaceCommand();
		}
	};

	this.qrCodeSale = function (chave, qrCode, cpf) {
		return AccountSaleCode.findOne().then(function (saleCodeObj) {
			var query = Query.build()
				.where('chave').equals(chave)
				.where('QRCODE').equals(qrCode)
				.where('CPF').equals(cpf)
				.where('saleCode').equals(saleCodeObj.saleCode);
			return QRCodeSaleRepository.download(query);
		});
	};

	this.handleAccountAddition = function (CDSUPERVISOR) {
		return PaymentRepository.findOne().then(function (payment) {
			var DATASALE = payment.DATASALE;
			// ajusta nova taxa de serviço
			var NEWVRTXSEVENDA = (DATASALE.FALTANTE < DATASALE.VRTXSEVENDA) ?
				parseFloat((DATASALE.VRTXSEVENDA - DATASALE.FALTANTE).toFixed(2)) : 0;

			DATASALE.TOTALVENDA = parseFloat((DATASALE.TOTALVENDA -
				parseFloat((DATASALE.VRTXSEVENDA - NEWVRTXSEVENDA).toFixed(2))).toFixed(2));
			DATASALE.VRTXSEVENDA = NEWVRTXSEVENDA;
			self.calcTotalSale(payment);

			// adiciona supervisor para log
			payment.servico.CDSUPERVISOR = CDSUPERVISOR;
			payment.servico.logServico = !NEWVRTXSEVENDA ? 'RET_TAX' : 'ALT_TAX';

			return PaymentRepository.save(payment);
		}.bind(this));
	};

    this.removePayment = function(payments){
    	if(!_.isArray(payments)) {
    		payments = Array(payments);
    	}

    	var query = Query.build()
			.where('DATA').equals(payments);

		return RemovePayment.download(query);
    };

}

Configuration(function (ContextRegister) {
	ContextRegister.register('PaymentService', PaymentService);
});