Configuration(function(ContextRegister, RepositoryFactory) {
	var DeliverySendOrders = RepositoryFactory.factory('/DeliverySendOrders', 'MEMORY', 1, 20000);
	ContextRegister.register('DeliverySendOrders', DeliverySendOrders);
});