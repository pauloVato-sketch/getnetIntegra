function OrderDeliveryController(DeliveryService, ScreenService, WindowService, ParamsPriceChart,
                                 UtilitiesService, OperatorRepository) {
    const ERROR_CUPOM_FISCAL = 'Operação não permitida. Este cupom já foi impresso.';
    const PAYMENT_COMPLETED = 'Venda realizada. ';
    const NFCE_CONTINGENCY = 'NFCE emitido em modo de contigência.';

    const self = this;
    var pagamentos = {};

    this.checkOrder = function(widget){
        setLocalVar("saleCode", new Date().getTime());
        pagamentos = {};
    };

    this.geraNotaFiscal = function(widget){
        ScreenService.confirmMessage("Deseja imprimir o cupom fiscal do pedido selecionado?",'question',
            function(success){
                var currentRow = widget.currentRow;
                var nrvendarest = currentRow.NRVENDAREST;
                var cdfilial = currentRow.CDFILIAL; 
                var status = currentRow.IDSTCOMANDA;
                var nrcomanda = currentRow.DSCOMANDA;
                var email = null;
                if(currentRow.EMAIL){
                    email = currentRow.EMAIL;
                }
                var saleCode = getLocalVar('saleCode');
                self.getInfoFormasPagamento(widget.container.getWidget('formaPagamentoPopup'));
                var datasale = widget.container.getWidget('formaPagamentoPopup').dataSource.data;
                if(status == 'P'){
                    ScreenService.showMessage(ERROR_CUPOM_FISCAL);
                }else if(currentRow.DATASALE == '' || currentRow.DATASALE.TOTAL > currentRow.DATASALE.PAGO){
                    ScreenService.openPopup(widget.container.getWidget('formaPagamentoPopup'));
                    ScreenService.showMessage('Informe a forma de pagamento');
                }else{
                    DeliveryService.generatePayment(cdfilial, nrvendarest, status, saleCode, datasale, nrcomanda, email).then(function(response){
                        if(!response[0].error){
                            WindowService.openWindow('DELIVERY_ORDERS_SCREEN').then(function(){
                                var message = PAYMENT_COMPLETED;
                                if (_.get(response[0], 'IDSTATUSNFCE') === 'P') {
                                    message += '<br><br>' + NFCE_CONTINGENCY;
                                }
                                if (_.get(response[0], 'mensagemNfce')) {
                                    var retornoNfce = _.get(response[0], 'mensagemNfce');
                                    if (!~retornoNfce.indexOf("A - ")){
                                        message += '<br>' + _.get(response[0], 'mensagemNfce');
                                    }
                                }
                                if (_.get(response[0], 'mensagemImpressao')) {
                                    message += '<br><br>' + _.get(response[0], 'mensagemImpressao');
                                }
                                if(_.get(response[0], 'errorDlv')){
                                    message += '<br><br>'+_.get(response[0],'messageDlv');
                                }
                                ScreenService.showMessage(message);
                            });
                        }else{
                            ScreenService.showMessage(response[0].message);
                            setLocalVar("saleCode", new Date().getTime());
                        }
                    });
                }
            }
        );
    };

    this.getInfoFormasPagamento = function(widget){
        var widgetOrder = widget.container.getWidget('order');
        //Se o pedido já tiver sido finalizado, não é possivel alterar suas informações de pagamento.
        var comandaImpresso = widgetOrder.currentRow.IDSTCOMANDA != 'P';
        var DATASALE = widgetOrder.currentRow.DATASALE;
        var PRODUTOS = widgetOrder.currentRow.PRODUTOS;
        self.changeActionsDlv(widget, comandaImpresso);
        DATASALE.hasChanged = false;
        DATASALE.TOTAL = parseFloat(widgetOrder.currentRow.VRACRCOMANDA);
        PRODUTOS.forEach(function(produtos){
            DATASALE.TOTAL += parseFloat(produtos.VRPRECCOMVENTOTAL);
        });
        DATASALE.PAGO = 0;
        DATASALE.forEach(function(datasale){
            DATASALE.PAGO += parseFloat(datasale.VRMOVIVENDDLV);
        });
        DATASALE.FALTANTE = DATASALE.TOTAL - DATASALE.PAGO;
        DATASALE.FALTANTE = DATASALE.FALTANTE > 0 ? DATASALE.FALTANTE : 0;
        DATASALE.TROCO = DATASALE.PAGO - DATASALE.TOTAL;
        DATASALE.TROCO = DATASALE.TROCO > 0? DATASALE.TROCO : 0;

        DATASALE.TROCOCURRENCY = UtilitiesService.toCurrency(DATASALE.TROCO);
        DATASALE.FALTANTECURRENCY = UtilitiesService.toCurrency(DATASALE.FALTANTE);
        DATASALE.PAGOCURRENCY = UtilitiesService.toCurrency(DATASALE.PAGO);
        DATASALE.TOTALCURRENCY = UtilitiesService.toCurrency(DATASALE.TOTAL);
        widget.label = 'Formas de Pagamento  -  Total da Comanda: R$'+DATASALE.TOTALCURRENCY;
        widget.dataSource.data = DATASALE;
        if(widget.getField('CDTIPORECE') != 1 || widget.getField('CDTIPORECE') != 2 || widget.getField('CDTIPORECE') != 316 || widget.getField('CDTIPORECE') != 312 || widget.getField('CDTIPORECE') != 300 || widget.getField('CDTIPORECE') != 5 || widget.getField('CDTIPORECE') != 4){
            widget.getField('VRMOVIVENDDLV').range.max = DATASALE.FALTANTE;
        }

        //Backup dos pagamentos antes de modificacoes.
        if(Object.getOwnPropertyNames(pagamentos).length == 0){
            pagamentos = angular.copy(widget.container.getWidget('order').currentRow.DATASALE);
        }
    };

    this.getInfoFooterPayment = function(widget){
        widget.currentRow = widget.container.getWidget('order').currentRow.DATASALE;
        if(widget.currentRow.FALTANTE>0){
            widget.getField('TROCOCURRENCY').isVisible = false;
            widget.getField('FALTANTECURRENCY').isVisible = true;
        }else{
            widget.getField('TROCOCURRENCY').isVisible = true;
            widget.getField('FALTANTECURRENCY').isVisible = false;
        }
    };

    this.getInfoProdutosDlv = function(widget){ 
        var widgetOrder = widget.container.getWidget('order');
        var acrescimoComanda = parseFloat(widgetOrder.currentRow.VRACRCOMANDA);
        //Se o pedido já tiver sido finalizado, não é possivel alterar suas informações da comanda.
        var status = widgetOrder.currentRow.IDSTCOMANDA == 'P';
        self.changeActionsDlv(widget, status);

        widget.dataSource.data = widgetOrder.currentRow.PRODUTOS;
        totalProdutosFooter = 0;    
        widget.dataSource.data.forEach(function(produto){
            totalProdutosFooter += parseFloat(produto.VRPRECCOMVENTOTAL);
        });
        totalProdutosFooter += acrescimoComanda;
        totalProdutosFooter = UtilitiesService.toCurrency(totalProdutosFooter);
        acrescimoComanda = UtilitiesService.toCurrency(acrescimoComanda);
        widget.container.getWidget('produtosFooter').getField('TOTALPRODUTOS').value(totalProdutosFooter);
        widget.container.getWidget('produtosFooter').getField('ACRESCIMOCOMANDA').value(acrescimoComanda);
    };

    this.changeActionsDlv = function(widget, status){
        if(widget.name == 'formaPagamentoPopup'){
            widget.getAction('addPayment').isVisible = status;
            widget.getAction('deletePayment').isVisible = status;
            widget.getAction('Confirmar').isVisible = status;
        }else if(widget.name == 'produtosPopup'){
            widget.getAction('deleteProduct').isVisible = status;
        }
    };

    this.getInfoPagamento = function(widget){
        widget.currentRow = widget.container.getWidget('order').currentRow.DATASALE; 
        widget.currentRow.VRMOVIVENDDLV = parseFloat(widget.container.getWidget('order').currentRow.DATASALE.FALTANTE);
    };
    
    this.getPaymentTypes = function(args){
        var field = args.owner;
        ParamsPriceChart.findAll().then(function(paymentTypes){
            field.field.dataSource.data = paymentTypes;
        }.bind(this));
    };

    this.adicionarFormaPagamento = function(widget){
        var widgetPagamentos = widget.container.getWidget('formaPagamentoPopup');
        var pagamento = {
            'CDTIPORECE': widget.currentRow.CDTIPORECE,
            'NMTIPORECE': widget.currentRow.DSBUTTON,
            'VRMOVIVENDDLV': UtilitiesService.removeCurrency(widget.currentRow.VRMOVIVENDDLV),
            'NRSEQMOVDLV': new Date().getTime()
        };
        if(widget.isValid()){
            widgetPagamentos.dataSource.data.push(pagamento);
            widget.container.getWidget('order').currentRow.DATASALE = widgetPagamentos.dataSource.data;
            ScreenService.closePopup();
        }
    };

    this.deletarFormaPagamento = function(widget){
        var widgetPagamentos = widget.container.getWidget('formaPagamentoPopup');
        widgetPagamentos.dataSource.data = widgetPagamentos.dataSource.data.filter(function(recebimento){
            return recebimento.NRSEQMOVDLV != widget.getField("NRSEQMOVDLV").value();
        });
        widget.container.getWidget('order').currentRow.DATASALE = widgetPagamentos.dataSource.data;
       self.getInfoFormasPagamento(widget);
    };

    this.salvarFormaPagamento = function(widget){
        var params = {};
        params.NRVENDAREST = widget.container.getWidget('order').getField('NRVENDAREST').value();
        params.RECEBIMENTOS = widget.dataSource.data;
        if(widget.dataSource.data.PAGO >= widget.dataSource.data.TOTAL){
            DeliveryService.saveMovcaixadlv(params).then(function(result){
                pagamentos = angular.copy(widget.container.getWidget('order').currentRow.DATASALE);
                ScreenService.closePopup();
            });
        }else{
            ScreenService.showMessage('Valor total da comanda não alcançado. Valor a Pagar: R$'+UtilitiesService.toCurrency(widget.dataSource.data.FALTANTE));
        }
    };

    this.backPayments = function(widget){
        widget.container.getWidget('order').currentRow.DATASALE = angular.copy(pagamentos);
        ScreenService.closePopup();
    };

    this.abrirPopupNovoPagamento = function (args){
        if(args.owner.widget.dataSource.data.FALTANTE <= 0){
            ScreenService.showMessage('Valor total da comanda já atingido.');
        }else{
            ScreenService.openPopup(args.owner.widget.container.getWidget('novoPagamentoPopup'));
        }
    };

    this.cancelarNovaFormaPagamento = function(widget){
        widget.currentRow.DSBUTTON = '';
        widget.currentRow.VRMOVIVENDDLV = '';
        ScreenService.closePopup();
    };

    this.reprint = function(reprintWidget){
        var orderWidget = reprintWidget.parent;
        switch (reprintWidget.currentRow.name) {
            case "reprintDlv": self.printDeliveryRow(orderWidget); break;
            case "reprintCupomF": self.printDeliveryRowCf(orderWidget); break;
            default: break;
        }
        ScreenService.closePopup();
    };

    this.printDeliveryRow = function(widget){
        var order = [{
            'CDFILIAL': widget.currentRow.CDFILIAL,
            'CDLOJA': widget.currentRow.CDLOJA,
            'NRVENDAREST': widget.currentRow.NRVENDAREST,
            'CDCAIXA': widget.currentRow.CDCAIXA
        }];
        DeliveryService.printDelivery(order).then(function(data){
            if(!data[0].error){
                ScreenService.showMessage('Relatório de entrega impresso com sucesso.');
            }else{
                ScreenService.showMessage('Houve um problema com a impressão do relatório de entrega.', 'ERROR');
            }
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

    this.confirmDeleteProduct = function(widget){
        OperatorRepository.findOne().then(function (operatorParams){
            if (operatorParams.IDINFPRODPRODUZ == 'S') {
                ScreenService.confirmMessage('O produto selecionado já foi produzido?', 'question', function () {
                    self.deletarProduto(widget, operatorParams.CDOPERADOR, 'S', operatorParams.CDFILIAL);
                }, function () {
                    self.deletarProduto(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL);
                });
            } else {
                self.deletarProduto(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL);
            }
        });
    };

    this.deletarProduto = function(widget, CDOPERADOR, IDPRODPRODUZ, CDFILIAL){
        var widgetOrder = widget.container.getWidget('order');
        var params = widgetOrder.currentRow;
        params.CDOPERADOR = CDOPERADOR;
        params.IDPRODPRODUZ = IDPRODPRODUZ;
        params.CDFILIAL = CDFILIAL;
        params.saleCode = getLocalVar('saleCode');
        //temporario
        widget.currentRow.composicao = null;
        params.product = {
            'NRVENDAREST': widget.currentRow.NRVENDAREST, 
            'nrcomanda': widget.currentRow.DSCOMANDA, 
            'NRPRODCOMVEN': widget.currentRow.NRPRODCOMVEN, 
            'CDPRODPROMOCAO': widget.currentRow.CDPRODPROMOCAO, 
            'NRSEQPRODCOM': widget.currentRow.NRSEQPRODCOM, 
            'NRSEQPRODCUP': widget.currentRow.NRSEQPRODCUP, 
            'codigo': widget.currentRow.CDPRODUTO, 
            'quantidade': widget.currentRow.QTPRODCOMVEN, 
            'composicao': widget.currentRow.composicao
        };
        params.motivo = [];
        params.motivo.push('Cancelamento Delivery');
        ScreenService.confirmMessage("Deseja cancelar o produto selecionado?",'question',
            function(success){
                DeliveryService.deletarProduto(params).then(function(data){
                    if(!data[0].error){
                        if(data[0].funcao == 1){
                            pagamentos = {};
                            widgetOrder.currentRow.DATASALE = [];
                            self.getInfoFormasPagamento(widget.container.getWidget('formaPagamentoPopup'));
                            ScreenService.showMessage('Produto cancelado com sucesso.');
                        }else{
                            ScreenService.showMessage(data[0].message);
                        }
                        if(data[0].products.length > 0){
                            widgetOrder.currentRow.PRODUTOS = data[0].products;
                            self.getInfoProdutosDlv(widget);

                        }else{
                             ScreenService.showMessage('Todos os produtos foram cancelados. O pedido será cancelado.').then(function() {
                                 self.cancelarPedido(widgetOrder, CDOPERADOR, IDPRODPRODUZ, CDFILIAL, true);
                             });
                        }
                    }else{
                        ScreenService.showMessage('Houve um problema com o cancelamento do produto.', 'ERROR');
                    }
                }).catch(function (error) {
                    ScreenService.showMessage(error);
                });
            }
        );  
    };

    this.cancelProductionOrder = function(widget){
        OperatorRepository.findOne().then(function (operatorParams){
            if (operatorParams.IDINFPRODPRODUZ == 'S') {
                ScreenService.confirmMessage('O pedido selecionado já foi produzido?', 'question', function () {
                    self.cancelarPedido(widget, operatorParams.CDOPERADOR, 'S', operatorParams.CDFILIAL, false);
                }, function () {
                    self.cancelarPedido(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL, false);
                });
            } else {
                self.cancelarPedido(widget, operatorParams.CDOPERADOR, null, operatorParams.CDFILIAL, false);
            }
        });
    };

    this.cancelarPedido = function(widget, CDOPERADOR, IDPRODPRODUZ, CDFILIAL, CANCELADIRETO){
        var params = widget.currentRow;
        params.saleCode = getLocalVar('saleCode');
        params.motivo = [];
        params.motivo.push('Cancelamento Delivery');
        params.operador = CDOPERADOR;
        params.IDPRODPRODUZ = IDPRODPRODUZ;
        params.CDFILIAL = CDFILIAL;
        params.nrvendarest= params.NRVENDAREST;
        params.status= params.DSCOMANDA;
        ScreenService.closePopup();
        if(CANCELADIRETO){
            DeliveryService.cancelarPedido(params).then(function(data){
                if(!data[0].error){
                    ScreenService.goBack().then(function(){
                        ScreenService.showMessage('Pedido cancelado com sucesso.');
                    });
                }else{
                    ScreenService.closePopup().then(function(){
                        ScreenService.showMessage('Houve um problema com o cancelamento do produto.', 'ERROR');
                    });
                }
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });
        }else{
            ScreenService.confirmMessage("Deseja cancelar o pedido selecionado?",'question',
                function(success){
                    DeliveryService.cancelarPedido(params).then(function(data){
                        if(!data[0].error){
                            ScreenService.goBack().then(function(){
                                ScreenService.showMessage('Pedido cancelado com sucesso.');
                            });
                        }else{
                            ScreenService.showMessage('Houve um problema com o cancelamento do produto.', 'ERROR');
                        }
                    }).catch(function (error) {
                        ScreenService.showMessage(error);
                    });
                }
            );  
        }
    };

    this.printDeliveryRowCf = function(widget){
        var order = [{
            'CDFILIAL': widget.currentRow.CDFILIAL,
            'CDLOJA': widget.currentRow.CDLOJA,
            'NRVENDAREST': widget.currentRow.NRVENDAREST,
            'NRSEQVENDA': widget.currentRow.NRSEQVENDA,
            'CDCAIXA': widget.currentRow.CDCAIXA
        }];
        var param = [];
        param.push(order);
        DeliveryService.reprintDeliveryCupomFiscal(param).then(function(data){
            if(!data[0].error){
                ScreenService.showMessage('Cupom Fiscal impresso com sucesso.');
            }else{
                ScreenService.showMessage(data[0].message, 'ERROR');
            }
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

    this.concludeOrder = function(widget){
        DeliveryService.concludeOrderDlv(widget.currentRow).then(function(data){
            if(!data[0].error){
                ScreenService.goBack();
                ScreenService.showMessage('Pedido finalizado com sucesso.');
            }else{
                ScreenService.showMessage(data[0].message, 'ERROR');
            }
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('OrderDeliveryController', OrderDeliveryController);
});