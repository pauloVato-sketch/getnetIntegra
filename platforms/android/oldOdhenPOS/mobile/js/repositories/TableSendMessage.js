Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSendMessage = RepositoryFactory.factory('/TableSendMessage', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSendMessage', TableSendMessage);
});