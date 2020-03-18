Configuration(function(ContextRegister, RepositoryFactory) {
    var SaleTypesRepository = RepositoryFactory.factory('/SaleTypesRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('SaleTypesRepository', SaleTypesRepository);
});