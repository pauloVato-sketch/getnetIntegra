Configuration(function(ContextRegister, RepositoryFactory) {	
	var SSLConnectionId = RepositoryFactory.factory('/SSLConnectionId', 'LOCAL');
	ContextRegister.register('SSLConnectionId', SSLConnectionId);
});