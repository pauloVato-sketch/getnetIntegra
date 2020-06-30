Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetTableTrasanctions = RepositoryFactory.factory('/AccountGetTableTrasanctions', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetTableTrasanctions', AccountGetTableTrasanctions);
});