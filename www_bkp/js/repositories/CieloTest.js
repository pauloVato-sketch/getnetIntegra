Configuration(function(ContextRegister, RepositoryFactory) {
	var CieloTest = RepositoryFactory.factory('/CieloTest', 'MEMORY');
	ContextRegister.register('CieloTest', CieloTest);
});