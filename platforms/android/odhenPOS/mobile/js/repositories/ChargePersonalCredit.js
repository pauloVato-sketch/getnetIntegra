Configuration(function(ContextRegister, RepositoryFactory) {
    var ChargePersonalCredit = RepositoryFactory.factory('/ChargePersonalCredit', 'MEMORY', 1, 60000);
    ContextRegister.register('ChargePersonalCredit', ChargePersonalCredit);
});