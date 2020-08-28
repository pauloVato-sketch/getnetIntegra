Configuration(function(ContextRegister, RepositoryFactory) {
	var ConsumerRepository = RepositoryFactory.factory('/ConsumerRepository', 'MEMORY');
	ContextRegister.register('ConsumerRepository', ConsumerRepository);
});