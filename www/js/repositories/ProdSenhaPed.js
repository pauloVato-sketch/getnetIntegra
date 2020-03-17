Configuration(function(ContextRegister, RepositoryFactory) {
    var ProdSenhaPed = RepositoryFactory.factory('/ProdSenhaPed', 'MEMORY', 1, 20000);
    ContextRegister.register('ProdSenhaPed', ProdSenhaPed);
});