Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPaymentBegin = RepositoryFactory.factory('/AccountPaymentBegin', 'MEMORY');
	ContextRegister.register('AccountPaymentBegin', AccountPaymentBegin);
});