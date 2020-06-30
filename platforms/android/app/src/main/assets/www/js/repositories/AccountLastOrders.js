Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountLastOrders = RepositoryFactory.factory('/AccountLastOrders', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountLastOrders', AccountLastOrders);
});