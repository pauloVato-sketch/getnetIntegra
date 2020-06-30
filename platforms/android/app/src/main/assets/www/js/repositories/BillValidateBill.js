Configuration(function(ContextRegister, RepositoryFactory) {
	var BillValidateBill = RepositoryFactory.factory('/BillValidateBill', 'MEMORY', 1, 20000);
	ContextRegister.register('BillValidateBill', BillValidateBill);
});