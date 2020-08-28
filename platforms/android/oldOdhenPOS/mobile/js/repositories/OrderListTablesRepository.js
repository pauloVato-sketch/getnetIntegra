Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderListTablesRepository = RepositoryFactory.factory('/OrderListTablesRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderListTablesRepository', OrderListTablesRepository);
});