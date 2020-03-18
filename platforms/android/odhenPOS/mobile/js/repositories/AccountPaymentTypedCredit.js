Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountPaymentTypedCredit = RepositoryFactory.factory('/AccountPaymentTypedCredit', 'MEMORY');
	ContextRegister.register('AccountPaymentTypedCredit', AccountPaymentTypedCredit);
});