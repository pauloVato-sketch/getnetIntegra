Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsObservationsRepository = RepositoryFactory.factory('/ParamsObservationsRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsObservationsRepository', ParamsObservationsRepository);
});