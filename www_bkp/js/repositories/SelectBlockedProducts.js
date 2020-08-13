Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectBlockedProducts = RepositoryFactory.factory('/SelectBlockedProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectBlockedProducts', SelectBlockedProducts);
});