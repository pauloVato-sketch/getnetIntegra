Configuration(function(ContextRegister, RepositoryFactory) {
	var SessionRepository = RepositoryFactory.factory('/SessionRepository', 'INDEXEDDB');
	ContextRegister.register('SessionRepository', SessionRepository);
});