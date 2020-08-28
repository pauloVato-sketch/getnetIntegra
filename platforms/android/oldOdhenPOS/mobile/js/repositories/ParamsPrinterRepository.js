Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsPrinterRepository = RepositoryFactory.factory('/ParamsPrinterRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsPrinterRepository', ParamsPrinterRepository);
});