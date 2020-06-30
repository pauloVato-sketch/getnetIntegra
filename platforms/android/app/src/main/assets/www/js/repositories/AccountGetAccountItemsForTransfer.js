Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountItemsForTransfer = RepositoryFactory.factory('/AccountGetAccountItemsForTransfer', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountItemsForTransfer', AccountGetAccountItemsForTransfer);
});