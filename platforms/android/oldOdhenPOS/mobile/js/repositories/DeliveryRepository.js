Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryRepository = RepositoryFactory.factory('/DeliveryRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliveryRepository', DeliveryRepository);
});