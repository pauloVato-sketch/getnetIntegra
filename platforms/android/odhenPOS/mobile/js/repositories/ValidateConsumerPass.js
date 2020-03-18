Configuration(function(ContextRegister, RepositoryFactory) {
    var ValidateConsumerPass = RepositoryFactory.factory('/ValidateConsumerPass', 'MEMORY', 1, 30000);
    ContextRegister.register('ValidateConsumerPass', ValidateConsumerPass);
});