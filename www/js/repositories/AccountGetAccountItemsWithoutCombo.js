Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountItemsWithoutCombo = RepositoryFactory.factory('/AccountGetAccountItemsWithoutCombo', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountItemsWithoutCombo', AccountGetAccountItemsWithoutCombo);
});