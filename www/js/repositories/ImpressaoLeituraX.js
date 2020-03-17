Configuration(function(ContextRegister, RepositoryFactory) {
    var ImpressaoLeituraX = RepositoryFactory.factory('/ImpressaoLeituraX', 'MEMORY', 1, 20000);
    ContextRegister.register('ImpressaoLeituraX', ImpressaoLeituraX);
});