Configuration(function(ContextRegister, RepositoryFactory) {
    var CheckRefilRepository = RepositoryFactory.factory('/CheckRefilRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CheckRefilRepository', CheckRefilRepository);
});