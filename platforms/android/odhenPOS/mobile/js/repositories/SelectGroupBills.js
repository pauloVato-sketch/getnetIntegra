Configuration(function(ContextRegister, RepositoryFactory) {
	var SelectGroupBills = RepositoryFactory.factory('/SelectGroupBills', 'MEMORY', 1, 20000);
	ContextRegister.register('SelectGroupBills', SelectGroupBills);
});