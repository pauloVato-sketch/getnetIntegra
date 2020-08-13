function BillService(Query, BillRepository, BillOpenBill, BillValidateBill, SetTableRepository, BillCancelOpen){

	this.getBills = function (chave){
		var query = Query.build()
						.where('chave').equals(chave);
		return BillRepository.download(query);
	};

	this.openBill = function(chave, DSCOMANDA, CDCLIENTE, CDCONSUMIDOR, NRMESA, CDVENDEDOR, DSCONSUMIDOR){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('DSCOMANDA').equals(DSCOMANDA)
						.where('CDCLIENTE').equals(CDCLIENTE)
						.where('CDCONSUMIDOR').equals(CDCONSUMIDOR)
						.where('nrMesa').equals(NRMESA)
						.where('CDVENDEDOR').equals(CDVENDEDOR)
						.where('DSCONSUMIDOR').equals(DSCONSUMIDOR);
		return BillOpenBill.download(query);
	};

	this.validateBill = function(chave, dsComanda){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('dsComanda').equals(dsComanda);
		return BillValidateBill.download(query);
	};

	this.setTheTable = function(chave, NRMESA, NRVENDAREST){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRMESA').equals(NRMESA)
						.where('NRVENDAREST').equals(NRVENDAREST);
		return SetTableRepository.download(query);
	};

	this.cancelOpen = function(chave, nrMesa, NRVENDAREST, NRCOMANDA, NRLUGARMESA){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('nrMesa').equals(nrMesa)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA);
		return BillCancelOpen.download(query);
	};

}

Configuration(function(ContextRegister){
	ContextRegister.register('BillService', BillService);
});