Configuration(function(ContextRegister, RepositoryFactory) {
	var VendedoresLogin = RepositoryFactory.factory('/VendedoresLogin', 'ONLINE', 1, 20000);
	ContextRegister.register('VendedoresLogin', VendedoresLogin);
});
