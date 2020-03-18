Configuration(function(ContextRegister, RepositoryFactory) {
	var CaixasLogin = RepositoryFactory.factory('/CaixasLogin', 'ONLINE', 1, 20000);
	ContextRegister.register('CaixasLogin', CaixasLogin);
});