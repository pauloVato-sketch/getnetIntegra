Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetTrasanctions = RepositoryFactory.factory('/AccountGetTrasanctions', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetTrasanctions', AccountGetTrasanctions);
});