Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsParameterRepository = RepositoryFactory.factory('/ParamsParameterRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsParameterRepository', ParamsParameterRepository);
});