Configuration(function(ContextRegister, RepositoryFactory, ZHPromise) {
	var SellerControl = RepositoryFactory.factory('/SellerControl', 'LOCAL');
	ContextRegister.register('SellerControl', SellerControl);
});