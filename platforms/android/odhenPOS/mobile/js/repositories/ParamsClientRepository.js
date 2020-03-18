Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsClientRepository = RepositoryFactory.factory('/ParamsClientRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsClientRepository', ParamsClientRepository);
});