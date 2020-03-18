Configuration(function(ContextRegister, RepositoryFactory) {
	var NewConsumerRepository = RepositoryFactory.factory('/NewConsumerRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('NewConsumerRepository', NewConsumerRepository);
});