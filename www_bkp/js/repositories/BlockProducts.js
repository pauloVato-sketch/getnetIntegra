Configuration(function(ContextRegister, RepositoryFactory) {
    var BlockProducts = RepositoryFactory.factory('/BlockProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('BlockProducts', BlockProducts);
});