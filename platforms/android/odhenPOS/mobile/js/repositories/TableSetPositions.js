Configuration(function(ContextRegister, RepositoryFactory) {
	var TableSetPositions = RepositoryFactory.factory('/TableSetPositions', 'MEMORY', 1, 20000);
	ContextRegister.register('TableSetPositions', TableSetPositions);
});