Configuration(function(ContextRegister, RepositoryFactory) {
    var VoucherRepository = RepositoryFactory.factory('/VoucherRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('VoucherRepository', VoucherRepository);
});