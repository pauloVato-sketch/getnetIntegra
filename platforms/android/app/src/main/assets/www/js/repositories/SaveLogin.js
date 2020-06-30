Configuration(function(ContextRegister, RepositoryFactory) {	
	var SaveLogin = RepositoryFactory.factory('/SaveLogin', 'LOCAL');
	ContextRegister.register('SaveLogin', SaveLogin);
});