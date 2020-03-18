Configuration(function(ContextRegister, RepositoryFactory) {
	var PriceChart = RepositoryFactory.factory('/PriceChart', 'MEMORY');
	ContextRegister.register('PriceChart', PriceChart);
});