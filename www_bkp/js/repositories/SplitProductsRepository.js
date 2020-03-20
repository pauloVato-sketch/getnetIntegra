Configuration(function(ContextRegister, RepositoryFactory) {
	var SplitProductsRepository = RepositoryFactory.factory('/SplitProductsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('SplitProductsRepository', SplitProductsRepository);
});