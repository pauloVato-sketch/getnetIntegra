Configuration(function(ContextRegister, RepositoryFactory) {
    var CampanhaFlag = RepositoryFactory.factory('/CampanhaFlag', 'MEMORY', 1, 20000);
    ContextRegister.register('CampanhaFlag', CampanhaFlag);
});