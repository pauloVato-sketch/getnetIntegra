Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountCancelProduct = RepositoryFactory.factory('/AccountCancelProduct', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountCancelProduct', AccountCancelProduct);
});