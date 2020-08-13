Configuration(function(ContextRegister, RepositoryFactory) {
    var ConsumerTypeRepository = RepositoryFactory.factory('/ConsumerTypeRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('ConsumerTypeRepository', ConsumerTypeRepository);
});