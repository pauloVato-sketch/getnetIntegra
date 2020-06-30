Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountSaleCode = RepositoryFactory.factory('/AccountSaleCode', 'MEMORY');
	ContextRegister.register('AccountSaleCode', AccountSaleCode);
});