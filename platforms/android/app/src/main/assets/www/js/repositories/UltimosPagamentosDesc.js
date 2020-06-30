Configuration(function(ContextRegister, RepositoryFactory) {
    var UltimosPagamentosDesc = RepositoryFactory.factory('/UltimosPagamentosDesc', 'MEMORY', 1, 20000);
    ContextRegister.register('UltimosPagamentosDesc', UltimosPagamentosDesc);
});