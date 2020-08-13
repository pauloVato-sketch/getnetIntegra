Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCheckAccess = RepositoryFactory.factory('/OrderCheckAccess', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCheckAccess', OrderCheckAccess);
});