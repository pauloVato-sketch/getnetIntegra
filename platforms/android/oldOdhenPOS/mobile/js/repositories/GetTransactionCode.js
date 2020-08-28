Configuration(function(ContextRegister, RepositoryFactory) {
	var GetTransactionCode = RepositoryFactory.factory('/GetTransactionCode', 'MEMORY', 1, 20000);
	ContextRegister.register('GetTransactionCode', GetTransactionCode);
});