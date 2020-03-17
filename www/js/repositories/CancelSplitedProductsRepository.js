Configuration(function(ContextRegister, RepositoryFactory) {
	var CancelSplitedProductsRepository = RepositoryFactory.factory('/CancelSplitedProductsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('CancelSplitedProductsRepository', CancelSplitedProductsRepository);
});