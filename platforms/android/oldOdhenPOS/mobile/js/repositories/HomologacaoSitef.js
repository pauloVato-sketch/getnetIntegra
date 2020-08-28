Configuration(function(ContextRegister, RepositoryFactory) {
    var HomologacaoSitef = RepositoryFactory.factory('/HomologacaoSitef', 'MEMORY', 1, 20000);
    ContextRegister.register('HomologacaoSitef', HomologacaoSitef);
});