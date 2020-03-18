Configuration(function(ContextRegister, RepositoryFactory) {
	var SetDiscountFidelity = RepositoryFactory.factory('/SetDiscountFidelity', 'MEMORY', 1, 20000);
	ContextRegister.register('SetDiscountFidelity', SetDiscountFidelity);
});