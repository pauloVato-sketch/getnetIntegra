Configuration(function(ContextRegister, RepositoryFactory) {
	var OperatorValidateSupervisor = RepositoryFactory.factory('/OperatorValidateSupervisor', 'MEMORY', 1, 20000);
	ContextRegister.register('OperatorValidateSupervisor', OperatorValidateSupervisor);
});