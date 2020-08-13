Configuration(function(ContextRegister, RepositoryFactory) {
	var UpdateTransactionEmail = RepositoryFactory.factory('/UpdateTransactionEmail', 'MEMORY');
	ContextRegister.register('UpdateTransactionEmail', UpdateTransactionEmail);
});