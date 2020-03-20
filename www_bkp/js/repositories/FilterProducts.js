Configuration(function(ContextRegister, RepositoryFactory) {
    var FilterProducts = RepositoryFactory.factory('/FilterProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('FilterProducts', FilterProducts);
});