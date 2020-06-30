Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsProdMessageCancelRepository = RepositoryFactory.factory('/ParamsProdMessageCancelRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsProdMessageCancelRepository', ParamsProdMessageCancelRepository);
});