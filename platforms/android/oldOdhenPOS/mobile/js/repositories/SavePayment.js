Configuration(function(ContextRegister, RepositoryFactory) {
	var SavePayment = RepositoryFactory.factory('/SavePayment', 'MEMORY', 1, 20000);
	ContextRegister.register('SavePayment', SavePayment);
});