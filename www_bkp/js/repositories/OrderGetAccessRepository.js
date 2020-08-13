Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderGetAccessRepository = RepositoryFactory.factory('/OrderGetAccessRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderGetAccessRepository', OrderGetAccessRepository);
});