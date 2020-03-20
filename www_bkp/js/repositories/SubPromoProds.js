Configuration(function(ContextRegister, RepositoryFactory) {
    var SubPromoProds = RepositoryFactory.factory('/SubPromoProds', 'MEMORY', 1, 20000);
    ContextRegister.register('SubPromoProds', SubPromoProds);
});