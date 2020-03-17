Configuration(function(ContextRegister, RepositoryFactory) {
	var ExportLogs = RepositoryFactory.factory('/ExportLogs', 'MEMORY', 1, 20000);
	ContextRegister.register('ExportLogs', ExportLogs);
});