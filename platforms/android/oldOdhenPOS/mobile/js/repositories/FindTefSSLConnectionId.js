Configuration(function(ContextRegister, RepositoryFactory) {
	var FindTefSSLConnectionId = RepositoryFactory.factory('/FindTefSSLConnectionId', 'MEMORY', 1, 25000);
	ContextRegister.register('FindTefSSLConnectionId', FindTefSSLConnectionId);
});