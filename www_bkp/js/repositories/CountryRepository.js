Configuration(function(ContextRegister, RepositoryFactory) {
    var CountryRepository = RepositoryFactory.factory('/CountryRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CountryRepository', CountryRepository);
});