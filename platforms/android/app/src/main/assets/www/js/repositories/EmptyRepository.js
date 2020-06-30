Configuration(function(ContextRegister, RepositoryFactory) {
	var EmptyRepository = RepositoryFactory.factory('/EmptyRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('EmptyRepository', EmptyRepository);
});