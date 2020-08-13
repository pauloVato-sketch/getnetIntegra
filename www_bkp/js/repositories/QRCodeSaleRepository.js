Configuration(function(ContextRegister, RepositoryFactory) {
    var QRCodeSaleRepository = RepositoryFactory.factory('/QRCodeSaleRepository', 'MEMORY', 1, 60000);
    ContextRegister.register('QRCodeSaleRepository', QRCodeSaleRepository);
});