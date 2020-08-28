Configuration(function(ContextRegister, RepositoryFactory) {
    var ProdutosDesistencia = RepositoryFactory.factory('/ProdutosDesistencia', 'MEMORY', 1, 20000);
    ContextRegister.register('ProdutosDesistencia', ProdutosDesistencia);
});