Configuration(function(ContextRegister, RepositoryFactory) {
	var RemovePayment = RepositoryFactory.factory('/RemovePayment', 'MEMORY', 1, 20000);
	ContextRegister.register('RemovePayment', RemovePayment);
});