Configuration(function(ContextRegister, RepositoryFactory) {
    var SelectVendedores = RepositoryFactory.factory('/SelectVendedores', 'MEMORY', 1, 20000);
    ContextRegister.register('SelectVendedores', SelectVendedores);
});