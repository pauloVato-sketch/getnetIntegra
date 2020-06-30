Configuration(function(ContextRegister, RepositoryFactory) {
	var ItemSangria = RepositoryFactory.factory('/ItemSangria', 'MEMORY', 1, 20000);
	ContextRegister.register('ItemSangria', ItemSangria);
});