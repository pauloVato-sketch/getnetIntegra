Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGetUpdatedTables = RepositoryFactory.factory('/TableGetUpdatedTables', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGetUpdatedTables', TableGetUpdatedTables);
});