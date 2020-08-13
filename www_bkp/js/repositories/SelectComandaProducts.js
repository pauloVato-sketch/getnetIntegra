Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectComandaProducts = RepositoryFactory.factory('/SelectComandaProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectComandaProducts', SelectComandaProducts);
});