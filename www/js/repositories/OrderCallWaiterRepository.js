Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderCallWaiterRepository = RepositoryFactory.factory('/OrderCallWaiterRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderCallWaiterRepository', OrderCallWaiterRepository);
});