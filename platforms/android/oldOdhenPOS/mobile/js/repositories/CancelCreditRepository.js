Configuration(function(ContextRegister, RepositoryFactory) {
    var CancelCreditRepository = RepositoryFactory.factory('/CancelCreditRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('CancelCreditRepository', CancelCreditRepository);
});