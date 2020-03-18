Configuration(function(ContextRegister, RepositoryFactory) {
	var TransactionsMoveTransactions = RepositoryFactory.factory('/TransactionsMoveTransactions', 'MEMORY');
	ContextRegister.register('TransactionsMoveTransactions', TransactionsMoveTransactions);
});