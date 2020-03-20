Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsMensDescontoObs = RepositoryFactory.factory('/ParamsMensDescontoObs', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsMensDescontoObs', ParamsMensDescontoObs);
});