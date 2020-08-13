Configuration(function(ContextRegister, RepositoryFactory) {
	var RegisterClosingPayments = RepositoryFactory.factory('/RegisterClosingPayments', 'MEMORY', 1, 20000);
	ContextRegister.register('RegisterClosingPayments', RegisterClosingPayments);
});