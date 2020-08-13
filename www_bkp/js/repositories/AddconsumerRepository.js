Configuration(function(ContextRegister, RepositoryFactory) {
    var AddconsumerRepository = RepositoryFactory.factory('/AddconsumerRepository', 'MEMORY', 1, 20000);
    ContextRegister.register('AddconsumerRepository', AddconsumerRepository);
});