Configuration(function(ContextRegister, RepositoryFactory) {
	var RegisterOpen = RepositoryFactory.factory('/RegisterOpen', 'MEMORY', 4, 20000);
	ContextRegister.register('RegisterOpen', RegisterOpen);
});