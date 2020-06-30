Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderAllowUserAccessRepository = RepositoryFactory.factory('/OrderAllowUserAccessRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderAllowUserAccessRepository', OrderAllowUserAccessRepository);
});