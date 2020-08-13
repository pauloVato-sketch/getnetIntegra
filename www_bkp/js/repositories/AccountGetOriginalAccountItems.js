Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetOriginalAccountItems = RepositoryFactory.factory('/AccountGetOriginalAccountItems', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetOriginalAccountItems', AccountGetOriginalAccountItems);
});