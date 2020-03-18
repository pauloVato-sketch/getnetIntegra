Configuration(function(ContextRegister, RepositoryFactory) {
	var GroupPriceChart = RepositoryFactory.factory('/GroupPriceChart', 'MEMORY');
	ContextRegister.register('GroupPriceChart', GroupPriceChart);
});