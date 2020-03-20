Configuration(function(ContextRegister, RepositoryFactory) {
	var TableTransferItem = RepositoryFactory.factory('/TableTransferItem', 'MEMORY', 4, 5000);
	ContextRegister.register('TableTransferItem', TableTransferItem);
});