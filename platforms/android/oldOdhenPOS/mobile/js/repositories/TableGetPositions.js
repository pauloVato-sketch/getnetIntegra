Configuration(function(ContextRegister, RepositoryFactory) {
	var TableGetPositions = RepositoryFactory.factory('/TableGetPositions', 'MEMORY', 1, 20000);
	ContextRegister.register('TableGetPositions', TableGetPositions);
});