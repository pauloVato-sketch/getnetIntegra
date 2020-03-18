Configuration(function(ContextRegister, RepositoryFactory) {
    var ParamsFamilyRepository = RepositoryFactory.factory('/ParamsFamilyRepository', 'MEMORY', 1, 15000);
    ContextRegister.register('ParamsFamilyRepository', ParamsFamilyRepository);
});