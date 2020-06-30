Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCurrentProductRepository = RepositoryFactory.factory('/OrderCurrentProductRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCurrentProductRepository', OrderCurrentProductRepository);
});