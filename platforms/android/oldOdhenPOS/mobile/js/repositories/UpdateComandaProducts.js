Configuration(function(ContextRegister, RepositoryFactory) {
    var UpdateComandaProducts = RepositoryFactory.factory('/UpdateComandaProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('UpdateComandaProducts', UpdateComandaProducts);
});