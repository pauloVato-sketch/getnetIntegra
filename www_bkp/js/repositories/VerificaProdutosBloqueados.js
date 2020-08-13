Configuration(function(ContextRegister, RepositoryFactory) {
	var VerificaProdutosBloqueados = RepositoryFactory.factory('/VerificaProdutosBloqueados', 'MEMORY', 1, 20000);
	ContextRegister.register('VerificaProdutosBloqueados', VerificaProdutosBloqueados);
});