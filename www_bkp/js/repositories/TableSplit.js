Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSplit = RepositoryFactory.factory('/TableSplit', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSplit', TableSplit);
});