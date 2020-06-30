Configuration(function(ContextRegister, RepositoryFactory) {
    var SmartPromoRepository = RepositoryFactory.factory('/SmartPromoRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('SmartPromoRepository', SmartPromoRepository);
});