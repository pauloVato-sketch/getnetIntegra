Configuration(function(ContextRegister, RepositoryFactory) {
	var ChangeProductDiscount = RepositoryFactory.factory('/ChangeProductDiscount', 'MEMORY', 1, 20000);
	ContextRegister.register('ChangeProductDiscount', ChangeProductDiscount);
});