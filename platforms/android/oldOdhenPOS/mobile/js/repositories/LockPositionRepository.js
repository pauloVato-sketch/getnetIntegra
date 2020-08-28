Configuration(function(ContextRegister, RepositoryFactory) {
    var LockPositionRepository = RepositoryFactory.factory('/LockPositionRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('LockPositionRepository', LockPositionRepository);
});