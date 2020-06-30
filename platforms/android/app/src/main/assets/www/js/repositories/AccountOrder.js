Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountOrder = RepositoryFactory.factory('/AccountOrder', 'MEMORY', 1, 25000);
	ContextRegister.register('AccountOrder', AccountOrder);
});