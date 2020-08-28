Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountSavedCarts = RepositoryFactory.factory('/AccountSavedCarts', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountSavedCarts', AccountSavedCarts);
});