Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryControlRepository = RepositoryFactory.factory('/DeliveryControlRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliveryControlRepository', DeliveryControlRepository);
});