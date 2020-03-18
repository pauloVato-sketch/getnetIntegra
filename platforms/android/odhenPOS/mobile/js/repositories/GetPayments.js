Configuration(function(ContextRegister, RepositoryFactory) {
	var GetPayments = RepositoryFactory.factory('/GetPayments', 'MEMORY', 1, 20000);
	ContextRegister.register('GetPayments', GetPayments);
});