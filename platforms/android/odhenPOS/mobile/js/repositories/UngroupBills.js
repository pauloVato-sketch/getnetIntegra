Configuration(function(ContextRegister, RepositoryFactory) {
	var UngroupBills = RepositoryFactory.factory('/UngroupBills', 'MEMORY', 1, 20000);
	ContextRegister.register('UngroupBills', UngroupBills);
});