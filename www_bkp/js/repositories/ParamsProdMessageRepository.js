Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsProdMessageRepository = RepositoryFactory.factory('/ParamsProdMessageRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsProdMessageRepository', ParamsProdMessageRepository);
});