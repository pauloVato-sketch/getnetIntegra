Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountChangeClientConsumer = RepositoryFactory.factory('/AccountChangeClientConsumer', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountChangeClientConsumer', AccountChangeClientConsumer);
});