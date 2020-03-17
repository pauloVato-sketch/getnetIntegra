Configuration(function(ContextRegister, RepositoryFactory) {
	var SmartPromoProds = RepositoryFactory.factory('/SmartPromoProds', 'MEMORY', 1, 20000);
	ContextRegister.register('SmartPromoProds', SmartPromoProds);
});