Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderBlockedIps = RepositoryFactory.factory('/OrderBlockedIps', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderBlockedIps', OrderBlockedIps);
});