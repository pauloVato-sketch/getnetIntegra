Configuration(function(ContextRegister, RepositoryFactory) {
	var TableRepository = RepositoryFactory.factory('/TableRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('TableRepository', TableRepository);
});