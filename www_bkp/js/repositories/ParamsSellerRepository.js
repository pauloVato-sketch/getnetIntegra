Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsSellerRepository = RepositoryFactory.factory('/ParamsSellerRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('ParamsSellerRepository', ParamsSellerRepository);
});