Configuration(function(ContextRegister, RepositoryFactory) {
	var TablePrepareOpening = RepositoryFactory.factory('/TablePrepareOpening', 'MEMORY', 1, 20000);
	ContextRegister.register('TablePrepareOpening', TablePrepareOpening);
});