Configuration(function(ContextRegister, RepositoryFactory) {
	var TableChangeStatus = RepositoryFactory.factory('/TableChangeStatus', 'MEMORY', 1, 20000);
	ContextRegister.register('TableChangeStatus', TableChangeStatus);
});