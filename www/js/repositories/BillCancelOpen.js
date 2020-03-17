Configuration(function(ContextRegister, RepositoryFactory) {
	var BillCancelOpen = RepositoryFactory.factory('/BillCancelOpen', 'MEMORY', 1, 20000);
	ContextRegister.register('BillCancelOpen', BillCancelOpen);
});