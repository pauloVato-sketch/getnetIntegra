Configuration(function(ContextRegister, RepositoryFactory) {
    var PositionCodeRepository = RepositoryFactory.factory('/PositionCodeRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('PositionCodeRepository', PositionCodeRepository);
});