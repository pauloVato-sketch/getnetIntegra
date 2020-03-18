Configuration(function(ContextRegister, RepositoryFactory) {
    var NeighborhoodRepository = RepositoryFactory.factory('/NeighborhoodRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('NeighborhoodRepository', NeighborhoodRepository);
});