Configuration(function(ContextRegister, RepositoryFactory) {
    var ParamsCardsRepository = RepositoryFactory.factory('/ParamsCardsRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('ParamsCardsRepository', ParamsCardsRepository);
});