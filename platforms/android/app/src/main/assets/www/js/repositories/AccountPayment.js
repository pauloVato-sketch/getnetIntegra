Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPayment = RepositoryFactory.factory('/AccountPayment', 'MEMORY');
	ContextRegister.register('AccountPayment', AccountPayment);
});