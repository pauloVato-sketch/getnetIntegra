Configuration(function(ContextRegister, RepositoryFactory) {
	var PedidosEntreguesRepository = RepositoryFactory.factory('/PedidosEntreguesRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('PedidosEntreguesRepository', PedidosEntreguesRepository);
});