function DeliveryController(DeliveryService, OperatorRepository, ScreenService, WindowService, templateManager) {

    var self = this;

	this.setDataSource = function(widget) {
		OperatorRepository.findOne().then(function(operatorData){
			var params = {
				'CDFILIAL':	operatorData.CDFILIAL,
				'CDLOJA': operatorData.CDLOJA 
			};
			
	        DeliveryService.getDeliveryOrders(params).then(function(response){
            	widget.dataSource.data = response;
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });

		});
    };

	this.setDataSourceControl = function(widget) {
		OperatorRepository.findOne().then(function(operatorData){
			var params = {
                "CDFILIAL": operatorData.CDFILIAL,
                "CDLOJA": operatorData.CDLOJA
            };

	        DeliveryService.setDataSourceControl(params).then(function (response){
            	widget.dataSource.data = response;
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });
		});
    };

    this.newOrder = function(){
		WindowService.openWindow('DELIVERY_ORDER_DETAIL_SCREEN').then(function(){
           var widgetOrder = templateManager.container.getWidget('order');
           widgetOrder.currentRow = {};
           widgetOrder.edit();
       });
    };

    this.openDeliveryDetail = function(widget){
        WindowService.openWindow('DELIVERY_ORDER_DETAIL_SCREEN').then(function(){
            var widgetOrder = templateManager.container.getWidget('order');
            widgetOrder.currentRow = widget.currentRow;

            widgetOrder.getField('SPOONROCKET').isVisible = false;
            if(widgetOrder.currentRow.IDORGCMDVENDA== 'DLV_IFO'){
                widgetOrder.getField('NRCOMANDAEXT').label = 'iFood';
                widgetOrder.getField('NRCOMANDAEXT').isVisible = true;
            }else if(widgetOrder.currentRow.IDORGCMDVENDA == 'DLV_SPO'){
                widgetOrder.getField('NRCOMANDAEXT').label = 'iFood';
                widgetOrder.getField('NRCOMANDAEXT').isVisible = true;
                widgetOrder.getField('SPOONROCKET').isVisible = true;
                widgetOrder.getField('SPOONROCKET').value('Entregador Automatico, não chamar.');
            }else if(widgetOrder.currentRow.IDORGCMDVENDA == 'DLV_UBR'){
                widgetOrder.getField('NRCOMANDAEXT').label = 'Uber Eats';
                widgetOrder.getField('NRCOMANDAEXT').isVisible = true;
            }else{
                widgetOrder.getField('NRCOMANDAEXT').isVisible = false;
            }

            widgetOrder.view();
            if(widgetOrder.currentRow.IDSTCOMANDA == 'P'){
                widgetOrder.getAction('cancelOrder').isVisible = false;
                widgetOrder.getAction('cupomFiscal').isVisible = false;
                widgetOrder.getAction('reprint').isVisible = true;
                widgetOrder.getAction('concludeOrder').isVisible = true;
            }else{
                widgetOrder.getAction('cancelOrder').isVisible = true;
                widgetOrder.getAction('cupomFiscal').isVisible = true;
                widgetOrder.getAction('reprint').isVisible = false;
                widgetOrder.getAction('concludeOrder').isVisible = false;
            }
        });
    };

    this.saidaPedidos = function(widget){
    	var widgetEntregador = widget.container.getWidget('popupEntregadorSaida');
    	var pedidos = widget.getCheckedRows();
    	var pedidosValidos = true;
    	pedidos.forEach(function(pedido){
    		if(pedido.IDSTCOMANDA != 'P'){
    			pedidosValidos = false;
    		}
    	});
    	if(pedidosValidos && pedidos.length > 0){
    		ScreenService.openPopup(widgetEntregador);
    	}else if(pedidos.length == 0){
            ScreenService.showMessage('Operação inválida. Pelo menos um pedido deve ser selecionado para ser entregue.');
        }else{
    		ScreenService.showMessage('Operação inválida. Todos os pedidos selecionados devem estar aguardando entregador.');
    	}
    };

    this.entregarPedidos = function(widget){
        if(widget.isValid()){
            var entregador = widget.currentRow.CDVENDEDOR? widget.currentRow.CDVENDEDOR : null;
            var pedidos = widget.container.getWidget('ordersControl').getCheckedRows();
            DeliveryService.entregarPedidos(pedidos, entregador).then(function(data){
                widget.currentRow = {};
                self.setDataSourceControl(widget.container.getWidget('ordersControl'));
                ScreenService.closePopup();
            }).catch(function (error) {
                ScreenService.showMessage(error);
            });
        }
    };

    this.chegadaEntregador = function(widget){
        var widgetEntregador = widget.container.getWidget('popupEntregadorChegada');
        ScreenService.openPopup(widgetEntregador);
    };

    this.getPedidosEntregues = function(widget){
        var widgetChegada = widget.container.getWidget('popUpChegadaPedidos');
        DeliveryService.getPedidosEntregues(widget.currentRow.CDVENDEDOR).then(function (response){
            widgetChegada.dataSource.data = response;
            ScreenService.closePopup();
            ScreenService.openPopup(widgetChegada);
        });
    };

    this.chegadaPedidos = function(widget){
        var widgetEntregador = widget.container.getWidget('popupEntregadorChegada');
        DeliveryService.chegadaPedidos(widget.dataSource.data, widgetEntregador.currentRow.CDVENDEDOR).then(function(data){
            self.setDataSourceControl(widget.container.getWidget('ordersControl'));
            widgetEntregador.currentRow = {};
            ScreenService.closePopup();
        }).catch(function (error) {
            ScreenService.showMessage(error);
        });
    };

    this.printDelivery = function(widget){
        var orders = widget.container.getWidget('ordersControl').getCheckedRows();
        if(orders.length == 0){
            ScreenService.showMessage('Pelo menos um pedido deve ser selecionado.');
        }else{
            ScreenService.confirmMessage("Deseja imprimir o relatório de entrega dos pedidos selecionados?",'question',
                function(success){
                    DeliveryService.printDelivery(orders).then(function(data){
                        if(!data[0].error){
                            ScreenService.showMessage('Relatório de entrega impresso com sucesso.');
                        }else{
                            ScreenService.showMessage('Houve um problema com a impressão do relatório de entrega.', 'ERROR');
                        }
                    }).catch(function (error) {
                        ScreenService.showMessage(error);
                    });
                }
            );
        }
    };

    this.reprintDeliveryCupomFiscal = function(widget){
        var orders = widget.container.getWidget('ordersControl').getCheckedRows();
        if(orders.length == 0){
            ScreenService.showMessage('Pelo menos um pedido deve ser selecionado.');
        }else{
            ScreenService.confirmMessage("Deseja reimprimir o cupom fiscal dos pedidos selecionados?",'question',
                function(success){
                    DeliveryService.reprintDeliveryCupomFiscal(orders).then(function(data){
                        if(!data[0].error){
                            ScreenService.showMessage('Cupom Fiscal impresso com sucesso.');
                        }else{
                            ScreenService.showMessage(data[0].message, 'ERROR');
                        }
                    }).catch(function (error) {
                        ScreenService.showMessage(error);
                    });
                }
            );
        }
    };

    this.nothing = function(){};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('DeliveryController', DeliveryController);
});