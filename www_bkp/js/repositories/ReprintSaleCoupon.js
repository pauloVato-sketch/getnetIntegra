Configuration(function(ContextRegister, RepositoryFactory) {
	var ReprintSaleCoupon = RepositoryFactory.factory('/ReprintSaleCoupon', 'MEMORY', 1, 20000);
	ContextRegister.register('ReprintSaleCoupon', ReprintSaleCoupon);
});