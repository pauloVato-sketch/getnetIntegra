Configuration(function(ContextRegister, RepositoryFactory) {
	var ConcludeOrderDlv = RepositoryFactory.factory('/ConcludeOrderDlv', 'MEMORY', 1, 20000);
	ContextRegister.register('ConcludeOrderDlv', ConcludeOrderDlv);
});