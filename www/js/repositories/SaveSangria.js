Configuration(function(ContextRegister, RepositoryFactory) {
    var SaveSangria = RepositoryFactory.factory('/SaveSangria', 'MEMORY', 1, 60000);
    ContextRegister.register('SaveSangria', SaveSangria);
});
