function TransactionsService(Query, TransactionsRepository, SendEmailTransaction, UpdateTransactionEmail, FindRowToCancel, UpdateCanceledTransaction, TransactionsMoveTransactions){
	
	this.findTransaction = function(DTHRFIMMOVini, DTHRFIMMOVfim, NRADMCODE){
		var query = Query.build()
						.where('DTHRFIMMOV').equals(DTHRFIMMOVini)
						.where('DTHRFIMMOV').equals(DTHRFIMMOVfim)
						.where('NRADMCODE').equals(NRADMCODE);
		return TransactionsRepository.download(query);
	};
	
	this.sendTransactionEmail = function(NRSEQMOVMOB, DSEMAILCLI){
		var query = Query.build()
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB)
						.where('DSEMAILCLI').equals(DSEMAILCLI);
		return SendEmailTransaction.download(query);
	};
	
	this.updateTransactionEmail = function(DSEMAILCLI, NRSEQMOVMOB){
		var query = Query.build()
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB)
						.where('DSEMAILCLI').equals(DSEMAILCLI);
		return UpdateTransactionEmail.download(query);
	};
	this.findRowToCancel = function(chave, NRSEQMOVMOB){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB);
		return FindRowToCancel.download(query);
	};
	this.updateCanceledTransaction = function(NRSEQMOVMOB){
		var query = Query.build()
						.where('NRSEQMOVMOB').equals(NRSEQMOVMOB);
		return UpdateCanceledTransaction.download(query);
	};
	
	this.moveTransactions = function(chave, NRVENDAREST, NRCOMANDA, NRLUGARMESA, positions){
		var query = Query.build()
						.where('chave').equals(chave)
						.where('NRVENDAREST').equals(NRVENDAREST)
						.where('NRCOMANDA').equals(NRCOMANDA)
						.where('NRLUGARMESA').equals(NRLUGARMESA)
						.where('positions').equals(positions);
		return TransactionsMoveTransactions.download(query);
		
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('TransactionsService', TransactionsService);
});