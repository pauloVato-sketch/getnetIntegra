Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderRequestLoginRepository = RepositoryFactory.factory('/OrderRequestLoginRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderRequestLoginRepository', OrderRequestLoginRepository);
});