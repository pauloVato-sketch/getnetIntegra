Configuration(function(ContextRegister, RepositoryFactory) {
	var TableOpen = RepositoryFactory.factory('/TableOpen', 'MEMORY', 1, 20000);
	ContextRegister.register('TableOpen', TableOpen);
});