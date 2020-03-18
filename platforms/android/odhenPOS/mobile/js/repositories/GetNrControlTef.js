Configuration(function(ContextRegister, RepositoryFactory) {
    var GetNrControlTef = RepositoryFactory.factory('/GetNrControlTef', 'MEMORY', 1, 60000);
    ContextRegister.register('GetNrControlTef', GetNrControlTef);
});
