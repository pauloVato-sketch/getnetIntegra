Configuration(function(ContextRegister, RepositoryFactory) {
    var StateRepository = RepositoryFactory.factory('/StateRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('StateRepository', StateRepository);
});