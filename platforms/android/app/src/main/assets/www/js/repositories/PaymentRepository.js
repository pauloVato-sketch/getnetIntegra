Configuration(function(ContextRegister, RepositoryFactory) {
	var PaymentRepository = RepositoryFactory.factory('/PaymentRepository', 'MEMORY', 1, 20000);
	ContextRegister.register('PaymentRepository', PaymentRepository);
});
