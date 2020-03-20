Configuration(function(ContextRegister, RepositoryFactory) {
	var UpdateItemsPrices = RepositoryFactory.factory('/UpdateItemsPrices', 'MEMORY', 1, 20000);
	ContextRegister.register('UpdateItemsPrices', UpdateItemsPrices);
});