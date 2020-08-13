Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSelectedTable = RepositoryFactory.factory('/TableSelectedTable', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSelectedTable', TableSelectedTable);
});