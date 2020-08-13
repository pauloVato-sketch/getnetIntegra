Configuration(function(ContextRegister, RepositoryFactory) {
	var TransactionsRepository = RepositoryFactory.factory('/TransactionsRepository', 'MEMORY');
	ContextRegister.register('TransactionsRepository', TransactionsRepository);
});