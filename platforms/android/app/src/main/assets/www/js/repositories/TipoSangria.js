Configuration(function(ContextRegister, RepositoryFactory) {
    var TipoSangria = RepositoryFactory.factory('/TipoSangria', 'MEMORY', 1, 20000);
    ContextRegister.register('TipoSangria', TipoSangria);
});