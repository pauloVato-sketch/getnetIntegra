Configuration(function(ContextRegister, RepositoryFactory) {
	var UtilitiesRequestsRepository = RepositoryFactory.factory('/UtilitiesRequestsRepository', 'MEMORY');
	ContextRegister.register('UtilitiesRequestsRepository', UtilitiesRequestsRepository);
});