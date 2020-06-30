Configuration(function(ContextRegister, RepositoryFactory) {
	var TableCancelOpen = RepositoryFactory.factory('/TableCancelOpen', 'MEMORY', 1, 20000);
	ContextRegister.register('TableCancelOpen', TableCancelOpen);
});