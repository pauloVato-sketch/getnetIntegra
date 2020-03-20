function ConsumerController(ConsumerService, OperatorRepository, StateRepository, CityRepository, NeighborhoodRepository, UtilitiesService, ScreenService) {

var self = this;

	//Prepara os dados para a tela de Cadastro de consumidor.
	this.resetConsumerRegister = function (widget) {
		widget.newRow();
		OperatorRepository.findOne().then(function(data){
            widget.currentRow.CDCLIENTE = data.CDCLIENTE;
			widget.getField('NMFANTCLIE').value(data.NMFANTCLIE);
            widget.getField('NMESTADO').readOnly = true;
            widget.getField('NMMUNICIPIO').readOnly = true;
            widget.getField('NMBAIRRO').readOnly = true;
            widget.currentRow.CDPAIS = null;
            widget.currentRow.SGESTADO = null;
            widget.currentRow.CDMUNICIPIO = null;
            widget.currentRow.CDBAIRRO = null;
            widget.currentRow.CDTIPOCONS = null;
            widget.currentRow.CDTIPOVENDA = null;
		});
	};

	//Valida data de nascimento do consumidor.
	this.validateConsumerBirthday = function (field) {
		if (_.isEmpty(field.value())) return true;
        if (!UtilitiesService.validateDate(field.value())) {
			ScreenService.notificationMessage('Data de nascimento inválida.', 'error', 4000);
            return false;
		}
        return true;
	};

	//Valida CPF do consumidor.
	this.validateConsumerCPF = function (field) {
        if (_.isEmpty(field.value())) return true;
		if (!UtilitiesService.isValidCPF(field.value())){
			ScreenService.notificationMessage('CPF inválido.', 'error', 4000);
            return false;
		}
        return true;
	};

	//Valida E-mail do consumidor.
	this.validateConsumerEmail = function(field) {
        if (_.isEmpty(field.value())) return true;
		if (!UtilitiesService.checkEmail(field.value())){
			ScreenService.notificationMessage('E-mail inválido.', 'error', 4000);
            return false;
		}
        return true;
	};

	// Prepara os dados na tela para envio ao backend.
	this.addConsumer = function(widget) {
        var isValidbirth = self.validateConsumerBirthday(widget.getField('consumerBirth'));
        var isValidCPF = self.validateConsumerCPF(widget.getField('consumerCPF'));
        var isValidEmail = self.validateConsumerEmail(widget.getField('consumerEmail'));

        if (!widget.isValid()){
            ScreenService.showMessage("Favor preencha os campos obrigatórios.");
        }
        else if (widget.currentRow.CDTIPOVENDA == null){
            ScreenService.showMessage("Favor escolha o tipo de venda.");
        }
        else {
            if (isValidbirth && isValidCPF && isValidEmail){
                ConsumerService.addConsumer(widget.currentRow).then(function (){
                    self.resetConsumerRegister(widget);
                    ScreenService.showMessage("Consumidor inserido com sucesso.");
                });
            }
        }
	};

    this.prepareStates = function(statesSelect, citiesSelect, neighborhoodsSelect, CDPAIS, SGESTADO, CDMUNICIPIO, CDBAIRRO){
        statesSelect.clearValue();
        citiesSelect.clearValue();
        neighborhoodsSelect.clearValue();

        statesSelect.readOnly = false;
        statesSelect.widget.currentRow.SGESTADO = "";
        StateRepository.clearAll().then(function (){
            if (_.isEmpty(CDPAIS)){
                statesSelect.readOnly = true;
                citiesSelect.readOnly = true;
                neighborhoodsSelect.readOnly = true;
                CDPAIS = "";
                SGESTADO = "";
                CDMUNICIPIO = "";
                CDBAIRRO = "";
            }

            statesSelect.dataSourceFilter[0].value = CDPAIS;

            if (CDPAIS){
                statesSelect.reload();
            }
        });
    };

    this.prepareCities = function(citiesSelect, neighborhoodsSelect, CDPAIS, SGESTADO, CDMUNICIPIO, CDBAIRRO){
        citiesSelect.clearValue();
        neighborhoodsSelect.clearValue();

        citiesSelect.readOnly = false;
        citiesSelect.widget.currentRow.CDMUNICIPIO = "";
        CityRepository.clearAll().then(function (){
            if (_.isEmpty(SGESTADO)){
                citiesSelect.readOnly = true;
                neighborhoodsSelect.readOnly = true;
                SGESTADO = "";
                CDMUNICIPIO = "";
                CDBAIRRO = "";
            }

            citiesSelect.dataSourceFilter[0].value = CDPAIS;
            citiesSelect.dataSourceFilter[1].value = SGESTADO;

            if (SGESTADO){
                citiesSelect.reload();
            }
        });
    };

    this.prepareNeighborhoods = function(neighborhoodsSelect, CDPAIS, SGESTADO, CDMUNICIPIO){
        neighborhoodsSelect.clearValue();

        neighborhoodsSelect.readOnly = false;
        neighborhoodsSelect.widget.currentRow.CDBAIRRO = "";
        NeighborhoodRepository.clearAll().then(function (){
            if (_.isEmpty(SGESTADO)){
                neighborhoodsSelect.readOnly = true;
                CDMUNICIPIO = "";
            }

            neighborhoodsSelect.dataSourceFilter[0].value = CDPAIS;
            neighborhoodsSelect.dataSourceFilter[1].value = SGESTADO;
            neighborhoodsSelect.dataSourceFilter[2].value = CDMUNICIPIO;

            if (CDMUNICIPIO){
                neighborhoodsSelect.reload();
            }
        });
    };

}

Configuration(function(ContextRegister){
	ContextRegister.register('ConsumerController', ConsumerController);
});