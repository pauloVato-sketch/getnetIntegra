Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGetMessageHistory = RepositoryFactory.factory('/TableGetMessageHistory', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGetMessageHistory', TableGetMessageHistory);
});