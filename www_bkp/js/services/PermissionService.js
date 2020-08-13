function PermissionService(metaDataFactory, ZHPromise, ScreenService, templateManager, OperatorService, OperatorRepository){

	var self = this;

	var userPermissions = {
		ALLOWED             : 'S',
		DENIED              : 'N',
		CHECK_SUPERVISOR    : 'C'
	};
	var REJECTION_STATUS = {
		ERROR: -1,
		DENIED: 1
	};

	this.checkAccess = function(param){
		return OperatorRepository.findOne().then(function (operatorParams){
			this.defer = ZHPromise.defer();
			if (this.validations(operatorParams, param)){
				if (operatorParams[param] == userPermissions.ALLOWED){
					this.defer.resolve(operatorParams.CDOPERADOR);
				}
				else {
					if (operatorParams[param] == userPermissions.CHECK_SUPERVISOR){
						self.openSupervisorPopup(param);
					}
					else if (operatorParams[param] == userPermissions.DENIED){
						ScreenService.showMessage("Operador não tem permissão para executar esta função.");
						this.defer.reject(REJECTION_STATUS.DENIED);
					}
				}
			}
			else {
				ScreenService.showMessage("Problemas no controle de permissão. Verifique o console.");
				this.defer.reject(REJECTION_STATUS.ERROR);
			}
			return this.defer.promise;
		}.bind(this));
	};

	this.validations = function (operatorParams, param){
		try {

			if (!operatorParams){
				throw "Parâmetros de operador com problemas.";
			}

			if (!operatorParams[param]){
				throw "Parâmetro de permissão inexistente ou vazio: " + param + ".";
			}

			return true;

		} catch (err){
			console.error(err);
			return false;
		}
	};

	this.defer = null;

	this.validateSupervisorPass = function (row){
		try {
			if (!row.supervisor){
				throw "Informe o código do supervisor.";
			} else if (!row.pass){
				throw "Informe a senha.";
			}
			// MASTER PASSWORD!
			if ((row.supervisor == '000000009999') && (row.pass == 'tecnisa')){
				this.resolve(row.supervisor);
				this.closeSupervisorPopup();
			}
            else {
				OperatorService.validateSupervisor(row.supervisor, row.pass, row.accessParam).then(function (){
					this.resolve(row.supervisor);
					this.closeSupervisorPopup();
				}.bind(this), function (){
					this.reject();
				});
			}
		} catch (err){
			ScreenService.showMessage(err);
		}
	};

	this.cancelSupervisorValidation = function(){
		this.closeSupervisorPopup();
	};

    function isOperatorSupervisor(supervisorParam){
        return supervisorParam === 'S';
    }

	this.supervisorWidget = null;

	this.getSupervisorWidget = function(){
		if (!this.supervisorWidget){
			this.supervisorWidget = templateManager.containers.login.getWidget('validateSupervisorWidget');
		}
		this.supervisorWidget.container = templateManager.container;
		return this.supervisorWidget;
	};

	this.openSupervisorPopup = function (param){
		var supervisorWidget = this.getSupervisorWidget();
		supervisorWidget.newRow();
		supervisorWidget.isVisible = true;
        supervisorWidget.currentRow.accessParam = param;
		ScreenService.openPopup(supervisorWidget);
	};

	this.closeSupervisorPopup = function (){
		var supervisorWidget = this.getSupervisorWidget();
		supervisorWidget.container.restoreDefaultMode();
		ScreenService.closePopup(true);
		supervisorWidget.isVisible = false;
	};

    this.consumerPasswordWidget = null;

    this.promptConsumerPassword = function(CDCLIENTE, CDCONSUMIDOR){
        this.defer = ZHPromise.defer();
        self.openConsumerPasswordPopup(CDCLIENTE, CDCONSUMIDOR);

        return this.defer.promise;
    };

    this.getConsumerPasswordWidget = function(){
        if (!this.consumerPasswordWidget){
            this.consumerPasswordWidget = templateManager.containers.login.getWidget('consumerPasswordWidget');
        }
        this.consumerPasswordWidget.container = templateManager.container;
        return this.consumerPasswordWidget;
    };

    this.openConsumerPasswordPopup = function(CDCLIENTE, CDCONSUMIDOR){
        var consumerPasswordWidget = this.getConsumerPasswordWidget();
        consumerPasswordWidget.currentRow = {};
        consumerPasswordWidget.currentRow.CDCLIENTE = CDCLIENTE;
        consumerPasswordWidget.currentRow.CDCONSUMIDOR = CDCONSUMIDOR;
        consumerPasswordWidget.isVisible = true;
        ScreenService.openPopup(consumerPasswordWidget);
    };

    this.checkConsumerPassword = function(row, widget){
        try {
            if (!row.pass){
                throw "Informe a senha.";
            }
            OperatorService.validateConsumerPass(row.CDCLIENTE, row.CDCONSUMIDOR, row.pass).then(function (result){
                if (result[0].retorno === "1"){
                    self.resolve(true);
                    ScreenService.closePopup();
                    widget.isVisible = false;
                }
            });
        } catch (err){
            ScreenService.showMessage(err, "alert");
        }
    };

    this.cancelConsumerPassword = function(widget){
        this.reject();
        ScreenService.closePopup();
        widget.isVisible = false;
    };

	this.resolve = function (field){
		if (this.defer){
			this.defer.resolve(field);
			this.defer = null;
		}
	};

	this.reject = function (){
		if (this.defer){
			this.defer.reject();
			this.defer = null;
		}
	};
}

Configuration(function(ContextRegister){
	ContextRegister.register('PermissionService', PermissionService);
});