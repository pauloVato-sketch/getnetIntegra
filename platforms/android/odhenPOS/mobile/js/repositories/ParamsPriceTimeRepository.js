Configuration(function(ContextRegister, RepositoryFactory) {
    var ParamsPriceTimeRepository = RepositoryFactory.factory('/ParamsPriceTimeRepository', 'MEMORY', 1, 30000);
    ContextRegister.register('ParamsPriceTimeRepository', ParamsPriceTimeRepository);
});