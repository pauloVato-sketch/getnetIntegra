Configuration(function(ContextRegister, RepositoryFactory) {
    var CityRepository = RepositoryFactory.factory('/CityRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('CityRepository', CityRepository);
});