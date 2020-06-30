Configuration(function(ContextRegister, RepositoryFactory) {
	var CancelDeliveryProduct = RepositoryFactory.factory('/CancelDeliveryProduct', 'MEMORY', 1, 20000);
	ContextRegister.register('CancelDeliveryProduct', CancelDeliveryProduct);
});