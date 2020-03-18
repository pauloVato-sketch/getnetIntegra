Configuration(function(ContextRegister, RepositoryFactory) {
    var CalculaDescontoSubgrupo = RepositoryFactory.factory('/CalculaDescontoSubgrupo', 'MEMORY', 1, 20000);
    ContextRegister.register('CalculaDescontoSubgrupo', CalculaDescontoSubgrupo);
});