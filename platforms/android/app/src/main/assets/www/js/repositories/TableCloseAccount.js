Configuration(function(ContextRegister, RepositoryFactory) {
	var TableCloseAccount = RepositoryFactory.factory('/TableCloseAccount', 'MEMORY', 4, 20000);
	ContextRegister.register('TableCloseAccount', TableCloseAccount);
});