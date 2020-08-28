Configuration(function(ContextRegister, RepositoryFactory) {
	var FiliaisLogin = RepositoryFactory.factory('/FiliaisLogin', 'ONLINE', 1, 20000);
	ContextRegister.register('FiliaisLogin', FiliaisLogin);
});