Configuration(function(ContextRegister, RepositoryFactory) {
	var BillActiveBill = RepositoryFactory.factory('/BillActiveBill', 'MEMORY', 1, 20000);
	ContextRegister.register('BillActiveBill', BillActiveBill);
});