Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderControlUserAccessRepository = RepositoryFactory.factory('/OrderControlUserAccessRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderControlUserAccessRepository', OrderControlUserAccessRepository);
});