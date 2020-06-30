Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderReturnTablesRepository = RepositoryFactory.factory('/OrderReturnTablesRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderReturnTablesRepository', OrderReturnTablesRepository);
});