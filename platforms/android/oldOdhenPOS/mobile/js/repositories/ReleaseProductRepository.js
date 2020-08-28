Configuration(function(ContextRegister, RepositoryFactory) {
	var ReleaseProductRepository = RepositoryFactory.factory('/ReleaseProductRepository', 'MEMORY');
	ContextRegister.register('ReleaseProductRepository', ReleaseProductRepository);
});