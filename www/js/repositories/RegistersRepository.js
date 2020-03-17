Configuration(function(ContextRegister, RepositoryFactory) {
	var RegistersRepository = RepositoryFactory.factory('/RegistersRepository', 'MEMORY');
	ContextRegister.register('RegistersRepository', RegistersRepository);
});