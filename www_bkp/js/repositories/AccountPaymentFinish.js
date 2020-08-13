Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPaymentFinish = RepositoryFactory.factory('/AccountPaymentFinish', 'MEMORY');
	ContextRegister.register('AccountPaymentFinish', AccountPaymentFinish);
});