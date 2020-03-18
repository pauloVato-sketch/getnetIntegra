Configuration(function(ContextRegister, RepositoryFactory) {
    var TransferCreditRepository = RepositoryFactory.factory('/TransferCreditRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('TransferCreditRepository', TransferCreditRepository);
});