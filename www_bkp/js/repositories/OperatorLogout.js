Configuration(function(ContextRegister, RepositoryFactory) {
	var OperatorLogout = RepositoryFactory.factory('/OperatorLogout', 'MEMORY', 1, 20000);
	ContextRegister.register('OperatorLogout', OperatorLogout);
});