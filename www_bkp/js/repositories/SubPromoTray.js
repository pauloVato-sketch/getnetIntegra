Configuration(function(ContextRegister, RepositoryFactory) {
    var SubPromoTray = RepositoryFactory.factory('/SubPromoTray', 'MEMORY', 1, 20000);
    ContextRegister.register('SubPromoTray', SubPromoTray);
});