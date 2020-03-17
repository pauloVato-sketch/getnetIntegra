Configuration(function(ContextRegister, RepositoryFactory) {
    var CarrinhoDesistencia = RepositoryFactory.factory('/CarrinhoDesistencia', 'MEMORY', 1, 20000);
    ContextRegister.register('CarrinhoDesistencia', CarrinhoDesistencia);
});