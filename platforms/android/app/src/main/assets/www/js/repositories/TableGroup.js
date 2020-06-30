Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGroup = RepositoryFactory.factory('/TableGroup', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGroup', TableGroup);
});