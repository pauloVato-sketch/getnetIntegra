Configuration(function(ContextRegister, RepositoryFactory) {
	var PaymentPayAccount = RepositoryFactory.factory('/PaymentPayAccount', 'MEMORY', 1, 60000);
	ContextRegister.register('PaymentPayAccount', PaymentPayAccount);
});