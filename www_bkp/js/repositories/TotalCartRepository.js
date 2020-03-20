Configuration(function(ContextRegister, RepositoryFactory) {
	var TotalCartRepository = RepositoryFactory.factory('/TotalCartRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('TotalCartRepository', TotalCartRepository);
});