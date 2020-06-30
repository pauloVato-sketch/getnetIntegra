Configuration(function(ContextRegister, RepositoryFactory) {
    var UpdateServiceTax = RepositoryFactory.factory('/UpdateServiceTax', 'MEMORY', 1, 20000);
    ContextRegister.register('UpdateServiceTax', UpdateServiceTax);
});