Configuration(function(ContextRegister, RepositoryFactory) {
	var BillRepository = RepositoryFactory.factory('/BillRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('BillRepository', BillRepository);
});