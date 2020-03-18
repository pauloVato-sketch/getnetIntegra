Configuration(function(ContextRegister, RepositoryFactory) {
	var Movcaixadlv = RepositoryFactory.factory('/Movcaixadlv', 'MEMORY', 1, 20000);
	ContextRegister.register('Movcaixadlv', Movcaixadlv);
});