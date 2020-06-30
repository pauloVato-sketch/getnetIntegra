Configuration(function(ContextRegister, RepositoryFactory) {
	var RegisterClose = RepositoryFactory.factory('/RegisterClose', 'MEMORY', 4, 20000);
	ContextRegister.register('RegisterClose', RegisterClose);
});