Configuration(function(ContextRegister, RepositoryFactory) {
    var ConsumerBalanceRepository = RepositoryFactory.factory('/ConsumerBalanceRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('ConsumerBalanceRepository', ConsumerBalanceRepository);
});
