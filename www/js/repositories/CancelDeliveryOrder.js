Configuration(function(ContextRegister, RepositoryFactory) {
	var CancelDeliveryOrder = RepositoryFactory.factory('/CancelDeliveryOrder', 'MEMORY', 1, 20000);
	ContextRegister.register('CancelDeliveryOrder', CancelDeliveryOrder);
});