Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCheckBlockedUsers = RepositoryFactory.factory('/OrderCheckBlockedUsers', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCheckBlockedUsers', OrderCheckBlockedUsers);
});