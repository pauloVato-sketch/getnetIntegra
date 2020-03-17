Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliveryCheckOutOrders = RepositoryFactory.factory('/DeliveryCheckOutOrders', 'MEMORY');
	ContextRegister.register('DeliveryCheckOutOrders', DeliveryCheckOutOrders);
});