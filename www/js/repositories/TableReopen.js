Configuration(function(ContextRegister, RepositoryFactory) {
	var TableReopen = RepositoryFactory.factory('/TableReopen', 'MEMORY', 1, 20000);
	ContextRegister.register('TableReopen', TableReopen);
});