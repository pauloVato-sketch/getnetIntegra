Configuration(function(ContextRegister, RepositoryFactory) {
    var TransferPositionRepository = RepositoryFactory.factory('/TransferPositionRepository', 'MEMORY', 1, 30000);
    ContextRegister.register('TransferPositionRepository', TransferPositionRepository);
});