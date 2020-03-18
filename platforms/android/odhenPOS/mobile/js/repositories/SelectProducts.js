Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectProducts = RepositoryFactory.factory('/SelectProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectProducts', SelectProducts);
});