Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsGroupPriceChart = RepositoryFactory.factory('/ParamsGroupPriceChart', 'MEMORY');
	ContextRegister.register('ParamsGroupPriceChart', ParamsGroupPriceChart);
});