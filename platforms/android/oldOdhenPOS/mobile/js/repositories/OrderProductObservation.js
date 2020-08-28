Configuration(function(ContextRegister, RepositoryFactory) {
	var OrderProductObservation = RepositoryFactory.factory('/OrderProductObservation', 'MEMORY', 1, 20000);
	ContextRegister.register('OrderProductObservation', OrderProductObservation);
});