Configuration(function(ContextRegister, RepositoryFactory) {
	var ConfigIpRepository = RepositoryFactory.factory('/ConfigIpRepository', 'INDEXEDDB');
	ContextRegister.register('ConfigIpRepository', ConfigIpRepository);
});