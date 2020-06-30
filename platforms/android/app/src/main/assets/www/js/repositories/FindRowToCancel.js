Configuration(function(ContextRegister, RepositoryFactory) {
	var FindRowToCancel = RepositoryFactory.factory('/FindRowToCancel', 'MEMORY');
	ContextRegister.register('FindRowToCancel', FindRowToCancel);
});