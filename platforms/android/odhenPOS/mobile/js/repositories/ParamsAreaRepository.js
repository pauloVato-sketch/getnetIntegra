Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsAreaRepository = RepositoryFactory.factory('/ParamsAreaRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsAreaRepository', ParamsAreaRepository);
});