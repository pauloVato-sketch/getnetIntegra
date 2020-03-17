Configuration(function(ContextRegister, RepositoryFactory) {
	var SendEmailTransaction = RepositoryFactory.factory('/SendEmailTransaction', 'MEMORY');
	ContextRegister.register('SendEmailTransaction', SendEmailTransaction);
});