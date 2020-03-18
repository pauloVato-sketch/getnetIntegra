Configuration(function(ContextRegister, RepositoryFactory) {
	var TableActiveTable = RepositoryFactory.factory('/TableActiveTable', 'MEMORY', 1, 20000);
	ContextRegister.register('TableActiveTable', TableActiveTable);
});