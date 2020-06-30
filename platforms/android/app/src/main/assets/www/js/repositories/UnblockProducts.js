Configuration(function(ContextRegister, RepositoryFactory) {
    var UnblockProducts = RepositoryFactory.factory('/UnblockProducts', 'MEMORY', 1, 20000);
    ContextRegister.register('UnblockProducts', UnblockProducts);
});