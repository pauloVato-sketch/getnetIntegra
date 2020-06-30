function GeneralFunctionsService(Query, ReprintSaleCoupon, BlockProducts, UnblockProducts, ImpressaoLeituraX, GetNrControlTef, SaveSangria, ExportLogs) {

	this.reprintSaleCoupon = function(reprintType, saleCode){
		var query = Query.build()
						.where('reprintType').equals(reprintType)
						.where('saleCode').equals(saleCode);
		return ReprintSaleCoupon.download(query);
	};

	this.blockProducts = function(widget){
		var query = Query.build()
			.where('CDPRODUTO').equals(widget.currentRow.selectProducts);
		return BlockProducts.download(query);
	};

	this.unblockProducts = function(widget){
		var query = Query.build()
			.where('CDPRODUTO').equals(widget.currentRow.selectBlockedProducts);
		return UnblockProducts.download(query);
	};

	this.impressaoLeituraX = function(){
		var query = Query.build();
		return ImpressaoLeituraX.download(query);
	};

	this.getNrControlTef = function(CDNSUHOSTTEF){
		var query = Query.build()
			.where('CDNSUHOSTTEF').equals(CDNSUHOSTTEF);
		return GetNrControlTef.download(query);
	};

	this.saveSangria = function(itemsSangria, imprimeSangria){
		var query = Query.build()
			.where('itemsSangria').equals(itemsSangria)
			.where('imprimeSangria').equals(imprimeSangria);
		return SaveSangria.download(query);
	};

	this.exportLogs = function(logContent, logName){
		var query = Query.build()
			.where('logContent').equals(logContent)
			.where('logName').equals(logName);
		return ExportLogs.download(query);
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('GeneralFunctionsService', GeneralFunctionsService);
});