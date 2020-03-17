Configuration(function(ContextRegister, RepositoryFactory) {
	var UpdateCanceledTransaction = RepositoryFactory.factory('/UpdateCanceledTransaction', 'MEMORY');
	ContextRegister.register('UpdateCanceledTransaction', UpdateCanceledTransaction);
});