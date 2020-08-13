Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryPrint = RepositoryFactory.factory('/DeliveryPrint', 'MEMORY', 1, 25000);
	ContextRegister.register('DeliveryPrint', DeliveryPrint);
});