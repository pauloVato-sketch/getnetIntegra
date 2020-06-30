function ConsumerService(AddconsumerRepository, Query){

    this.addConsumer = function(consumerData){
        var dados = JSON.stringify(consumerData);
        var query = Query.build()
                        .where('dados').equals(dados);
        return AddconsumerRepository.download(query);
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ConsumerService', ConsumerService);
});