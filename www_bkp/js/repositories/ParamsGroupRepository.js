Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsGroupRepository = RepositoryFactory.factory('/ParamsGroupRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsGroupRepository', ParamsGroupRepository);
});