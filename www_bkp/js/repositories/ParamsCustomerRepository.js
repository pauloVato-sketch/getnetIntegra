Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsCustomerRepository = RepositoryFactory.factory('/ParamsCustomerRepository', 'MEMORY', 1, 90000);
	ContextRegister.register('ParamsCustomerRepository', ParamsCustomerRepository);
});