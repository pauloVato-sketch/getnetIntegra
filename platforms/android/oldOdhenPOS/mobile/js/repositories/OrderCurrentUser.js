Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCurrentUser = RepositoryFactory.factory('/OrderCurrentUser', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCurrentUser', OrderCurrentUser);
});