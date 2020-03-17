Configuration(function(ContextRegister, RepositoryFactory) {
	var FindPendingPayments = RepositoryFactory.factory('/FindPendingPayments', 'MEMORY', 1, 20000);
	ContextRegister.register('FindPendingPayments', FindPendingPayments);
});