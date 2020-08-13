Configuration(function(ContextRegister, RepositoryFactory) {
	var DelayedProductsRepository = RepositoryFactory.factory('/DelayedProductsRepository', 'MEMORY');
	ContextRegister.register('DelayedProductsRepository', DelayedProductsRepository);
});