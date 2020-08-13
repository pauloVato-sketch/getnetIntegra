Configuration(function(ContextRegister, RepositoryFactory) {
	var SmartPromoTray = RepositoryFactory.factory('/SmartPromoTray', 'MEMORY', 1, 20000);
	ContextRegister.register('SmartPromoTray', SmartPromoTray);
});