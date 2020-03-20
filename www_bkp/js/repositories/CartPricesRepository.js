Configuration(function(ContextRegister, RepositoryFactory) {
    var CartPricesRepository = RepositoryFactory.factory('/CartPricesRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CartPricesRepository', CartPricesRepository);
});