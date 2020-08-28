Configuration(function(ContextRegister, RepositoryFactory) {
	var SaleCancelRepository = RepositoryFactory.factory('/SaleCancelRepository', 'MEMORY', 1, 60000);
	ContextRegister.register('SaleCancelRepository', SaleCancelRepository);
});