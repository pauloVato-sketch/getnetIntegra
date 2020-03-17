Configuration(function(ContextRegister, RepositoryFactory) {
	var ConsumerLoginRepository = RepositoryFactory.factory('/ConsumerLoginRepository', 'MEMORY');
	ContextRegister.register('ConsumerLoginRepository', ConsumerLoginRepository);
});