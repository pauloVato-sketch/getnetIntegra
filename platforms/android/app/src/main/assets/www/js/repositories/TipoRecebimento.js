Configuration(function(ContextRegister, RepositoryFactory) {
    var TipoRecebimento = RepositoryFactory.factory('/TipoRecebimento', 'MEMORY', 1, 20000);
    ContextRegister.register('TipoRecebimento', TipoRecebimento);
});