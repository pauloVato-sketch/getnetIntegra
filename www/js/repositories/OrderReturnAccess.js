Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderReturnAccess = RepositoryFactory.factory('/OrderReturnAccess', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderReturnAccess', OrderReturnAccess);
});