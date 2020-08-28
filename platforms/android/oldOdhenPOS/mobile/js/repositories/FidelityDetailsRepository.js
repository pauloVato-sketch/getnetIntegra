Configuration(function(ContextRegister, RepositoryFactory) {
    var FidelityDetailsRepository = RepositoryFactory.factory('/FidelityDetailsRepository', 'MEMORY', 1, 30000);
    ContextRegister.register('FidelityDetailsRepository', FidelityDetailsRepository);
});