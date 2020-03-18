Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderGetCallRepository = RepositoryFactory.factory('/OrderGetCallRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderGetCallRepository', OrderGetCallRepository);
});