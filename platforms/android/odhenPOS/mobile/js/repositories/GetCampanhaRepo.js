Configuration(function(ContextRegister, RepositoryFactory) {
    var GetCampanhaRepo = RepositoryFactory.factory('/GetCampanhaRepo', 'MEMORY', 1, 20000);
    ContextRegister.register('GetCampanhaRepo', GetCampanhaRepo);
});