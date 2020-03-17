Configuration(function(ContextRegister, RepositoryFactory) {
	var GetConsumerLimit = RepositoryFactory.factory('/GetConsumerLimit', 'MEMORY', 1, 60000);
	ContextRegister.register('GetConsumerLimit', GetConsumerLimit);
});