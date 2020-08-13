Configuration(function(ContextRegister, RepositoryFactory) {
	var SetTableRepository = RepositoryFactory.factory('/SetTableRepository', 'MEMORY');
	ContextRegister.register('SetTableRepository', SetTableRepository);
});