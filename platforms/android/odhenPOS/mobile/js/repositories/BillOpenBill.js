Configuration(function(ContextRegister, RepositoryFactory) {
	var BillOpenBill = RepositoryFactory.factory('/BillOpenBill', 'MEMORY', 1, 20000);
	ContextRegister.register('BillOpenBill', BillOpenBill);
});