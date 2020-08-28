function TableService(Query, AccountGetAccountItems, TableOpen, TableCancelOpen, TableSendMessage, TableCloseAccount, TableReopen, TableGetPositions, TableGroup, TableTransferItem, TableRepository, TableTransferTable, TableGetMessageHistory, TableSplit, TableSetPositions, TableActiveTable, ConsumerRepository, DelayedProductsRepository, ReleaseProductRepository, SplitProductsRepository, CancelSplitedProductsRepository, PositionCodeRepository, TableChangeStatus, PositionControlRepository){

	this.open = function(chave, NRMESA, NRPESMESAVEN, CDCLIENTE, CDCONSUMIDOR, CDVENDEDOR, positionsObject) {
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(NRMESA)
						.where('quantidade').equals(NRPESMESAVEN)
						.where('cdCliente').equals(CDCLIENTE)
						.where('cdConsumidor').equals(CDCONSUMIDOR)
						.where('cdVendedor').equals(CDVENDEDOR)
						.where('posicoes').equals(positionsObject);
		return TableOpen.download(query);
	};

	this.cancelOpen = function(chave, nrMesa){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('nrMesa').equals(nrMesa);
		return TableCancelOpen.download(query);
	};

	this.sendMessage = function(chave, NRCOMANDA, NRVENDAREST, impressoras, mensagem, historico, modo){

		if ((historico === null) || (historico === undefined) || (historico === '')) {
			historico = 'vazio';
		}

		var nrImpressoras = JSON.stringify(impressoras);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('nrImpressora').equals(nrImpressoras)
						.where('mensagem').equals(mensagem)
						.where('historico').equals(historico)
						.where('modo').equals(modo);
		return TableSendMessage.download(query);
	};

	this.closeAccount = function(chave, NRCOMANDA, NRVENDAREST, modo, consumacao, servico, couvert, valorConsumacao, pessoas, CDSUPERVISOR, NRMESA, IMPRIMEPARCIAL, txporcentservico){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('modo').equals(modo)
						.where('consumacao').equals(consumacao)
						.where('servico').equals(servico)
						.where('couvert').equals(couvert)
						.where('valorConsumacao').equals(valorConsumacao)
						.where('pessoas').equals(pessoas)
						.where('CDSUPERVISOR').equals(CDSUPERVISOR)
						.where('NRMESA').equals(NRMESA)
						.where('IMPRIMEPARCIAL').equals(IMPRIMEPARCIAL)
						.where('txporcentservico').equals(txporcentservico);
		return TableCloseAccount.download(query);
	};

	this.reopen = function(chave, mesa){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(mesa);
		return TableReopen.download(query);
	};

	this.groupTables = function(chave, mesa, listaMesas){
		var listaMesa = JSON.stringify(listaMesas);
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(mesa)
						.where('listaMesas').equals(listaMesa);
		return TableGroup.download(query);
	};

	this.transferItem = function(chave, mesaDestino, NRCOMANDA, NRVENDAREST, produto, posicao, CDSUPERVISOR, maxPosicoes){

		var produtos = JSON.stringify(produto);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesaDestino').equals(mesaDestino)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('produtos').equals(produtos)
						.where('posicao').equals(posicao)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR)
                        .where('maxPosicoes').equals(maxPosicoes);
		return TableTransferItem.download(query);
	};

	this.getTables = function(chave){
		var query = Query.build()
						.where('chave').equals(chave);
		return TableRepository.download(query);
	};

	this.validateOpening = function(chave, mesa, status, modo){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('mesa').equals(mesa)
						.where('status').equals(status)
						.where('modo').equals(modo);
		return TableActiveTable.download(query);
	};

	this.transferTable = function(chave, NRCOMANDA, NRVENDAREST, mesaDestino, CDSUPERVISOR){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('mesaDestino').equals(mesaDestino)
                        .where('CDSUPERVISOR').equals(CDSUPERVISOR);
		return TableTransferTable.download(query);
	};

	this.getMessageHistory = function(chave, NRCOMANDA, NRVENDAREST){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST);
		return TableGetMessageHistory.download(query);
	};

	this.splitTables = function(chave, NRCOMANDA, NRVENDAREST, listaMesas){

		var listaMesa = JSON.stringify(listaMesas);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('listaMesas').equals(listaMesa);
		return TableSplit.download(query);
	};

	this.setPositions = function(chave, NRCOMANDA, NRVENDAREST, quantidade){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('quantidade').equals(quantidade);
		return TableSetPositions.download(query);
	};

	this.getConsumersByClient = function(chave, CDCLIENTE){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('CDCLIENTE').equals(CDCLIENTE);
		return ConsumerRepository.download(query);
	};

	this.getDelayedProducts = function(chave, NRVENDAREST, NRCOMANDA){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA);
		return DelayedProductsRepository.download(query);
	};

	this.releaseTheProduct = function(chave, CDFILIAL, NRVENDAREST, NRCOMANDA, selectedProducts, printer){

		var produtos = JSON.stringify(selectedProducts);

		var query = Query.build()
						.where('chave').equals(chave)
						.where('CDFILIAL').equals(CDFILIAL)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('produtos').equals(produtos)
						.where('printer').equals(printer);
		return ReleaseProductRepository.download(query);
	};

	this.splitProducts = function(chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN, positions){

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRPRODCOMVEN').equals(NRPRODCOMVEN)
						.where('NRLUGARMESA').equals(positions);
		return SplitProductsRepository.download(query);
	};

	this.cancelSplitedProducts = function(chave, NRVENDAREST, NRCOMANDA, NRPRODCOMVEN){

		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRPRODCOMVEN').equals(NRPRODCOMVEN);
		return CancelSplitedProductsRepository.download(query);
	};

	this.generatePositionCode = function (chave, NRVENDAREST, NRCOMANDA, position){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('position').equals(position);
		return PositionCodeRepository.download(query);
	};

	this.changeTableStatus = function(chave, NRVENDAREST, NRCOMANDA, status){
		// altera o status da mesa para Recebimento
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('STATUS').equals(status);
		return TableChangeStatus.download(query);
	};

    this.positionControl = function (NRVENDAREST, position, unselecting, positions){
        var query = Query.build()
                        .where('NRVENDAREST').equals(NRVENDAREST)
                        .where('position').equals(position)
                        .where('unselecting').equals(unselecting)
                        .where('positions').equals(positions);
        return PositionControlRepository.download(query);
    };

}

Configuration(function(ContextRegister){
	ContextRegister.register('TableService', TableService);
});