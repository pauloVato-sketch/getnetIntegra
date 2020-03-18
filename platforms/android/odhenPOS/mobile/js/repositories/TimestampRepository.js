Configuration(function(ContextRegister, RepositoryFactory) {
	var TimestampRepository = RepositoryFactory.factory('/TimestampRepository', 'MEMORY');
	ContextRegister.register('TimestampRepository', TimestampRepository);
});