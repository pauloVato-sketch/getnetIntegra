Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsMenuRepository = RepositoryFactory.factory('/ParamsMenuRepository', 'MEMORY', 1, 45000);
	ContextRegister.register('ParamsMenuRepository', ParamsMenuRepository);
});