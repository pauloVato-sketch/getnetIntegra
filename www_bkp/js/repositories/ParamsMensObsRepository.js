Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsMensObsRepository = RepositoryFactory.factory('/ParamsMensObsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsMensObsRepository', ParamsMensObsRepository);
});