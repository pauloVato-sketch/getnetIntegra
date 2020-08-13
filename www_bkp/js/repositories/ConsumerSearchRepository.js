Configuration(function(ContextRegister, RepositoryFactory) {
    var ConsumerSearchRepository = RepositoryFactory.factory('/ConsumerSearchRepository', 'MEMORY', 1, 90000);
    ContextRegister.register('ConsumerSearchRepository', ConsumerSearchRepository);
});