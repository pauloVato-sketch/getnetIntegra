Configuration(function(ContextRegister, RepositoryFactory) {
	var AccountGetAccountDetails = RepositoryFactory.factory('/AccountGetAccountDetails', 'MEMORY', 1, 20000);
	ContextRegister.register('AccountGetAccountDetails', AccountGetAccountDetails);
});