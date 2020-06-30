Configuration(function(ContextRegister, RepositoryFactory) {
	var SmartPromoGroups = RepositoryFactory.factory('/SmartPromoGroups', 'MEMORY', 1, 20000);
	ContextRegister.register('SmartPromoGroups', SmartPromoGroups);
});