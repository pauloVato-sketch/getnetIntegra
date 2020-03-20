Configuration(function(ContextRegister, RepositoryFactory) {
    var UltimasVendasDesc = RepositoryFactory.factory('/UltimasVendasDesc', 'MEMORY', 1, 20000);
    ContextRegister.register('UltimasVendasDesc', UltimasVendasDesc);
});