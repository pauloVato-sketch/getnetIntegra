Configuration(function(ContextRegister, RepositoryFactory) {
	var GroupBills = RepositoryFactory.factory('/GroupBills', 'MEMORY', 1, 20000);
	ContextRegister.register('GroupBills', GroupBills);
});