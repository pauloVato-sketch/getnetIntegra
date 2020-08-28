Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderLoginUserRepository = RepositoryFactory.factory('/OrderLoginUserRepository', 'MEMORY');
	ContextRegister.register('OrderLoginUserRepository', OrderLoginUserRepository);
});