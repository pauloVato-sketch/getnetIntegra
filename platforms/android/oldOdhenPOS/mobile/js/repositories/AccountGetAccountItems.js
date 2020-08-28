Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountItems = RepositoryFactory.factory('/AccountGetAccountItems', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountItems', AccountGetAccountItems);
});