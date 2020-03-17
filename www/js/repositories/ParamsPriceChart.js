Configuration(function(ContextRegister, RepositoryFactory) {
	var ParamsPriceChart = RepositoryFactory.factory('/ParamsPriceChart', 'MEMORY');
	ContextRegister.register('ParamsPriceChart', ParamsPriceChart);
});