Configuration(function(ContextRegister, RepositoryFactory) {
    var PositionControlRepository = RepositoryFactory.factory('/PositionControlRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('PositionControlRepository', PositionControlRepository);
});