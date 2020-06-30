Configuration(function(ContextRegister, RepositoryFactory) {
	var UtilitiesTest = RepositoryFactory.factory('/UtilitiesTest', 'MEMORY');
	ContextRegister.register('UtilitiesTest', UtilitiesTest);
});