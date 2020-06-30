Configuration(function(ContextRegister, RepositoryFactory) {
	var TableTransferTable = RepositoryFactory.factory('/TableTransferTable', 'MEMORY', 4, 5000);
	ContextRegister.register('TableTransferTable', TableTransferTable);
});