Configuration(function(ContextRegister, RepositoryFactory) {
    var SubPromoGroups = RepositoryFactory.factory('/SubPromoGroups', 'MEMORY', 1, 20000);
    ContextRegister.register('SubPromoGroups', SubPromoGroups);
});