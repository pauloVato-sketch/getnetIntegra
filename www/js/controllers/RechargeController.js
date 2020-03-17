function RechargeController(Query, ScreenService, templateManager, eventAggregator, UtilitiesService, WindowService) {
// function RechargeController(Query, ScreenService, templateManager, RechargeService, ProfileService, LoginRepository, RechargeValueRepository, eventAggregator, BankDataRepository, LoginController, UtilitiesService, RechargeCardRepository, WindowService) {

	/* Gets the consumer's authorized store families with zero value, and updates the recharge grid. */
	this.getRechargeGrid = function(rechargeGrid){
		rechargeGrid.dataSource.data = [];
		RechargeValueRepository.clearAll().then(function(){
			LoginRepository.findOne().then(function (consumerData){
				RechargeService.getRechargeGrid(consumerData.CDFILIAL, consumerData.NRCPFRESPCON, consumerData.CDCLIENTE).then(function (grid){
					var valorTotal = {CDCONSUMIDOR: null, CDFAMILISALD: null, NMCONSUMIDOR: "Valor Total",
									  NMFAMILISALD: null, NRCPFRESPCON: null, PARENT: null,
									  PRIMARY: null, VRSALDCONFAM: "0,00"};
					grid.push(valorTotal);
					rechargeGrid.dataSource.data = grid;
				});
			});
		});
	};

	/* Opens the popup for the recharge value. */
	this.openRechargeValuePopup = function(row, widget) {
		if(row.NMCONSUMIDOR === null){
			delete widget.dataSource.data;
			widget.newRow();
			/* If a recharge value was already chosen, restores this value to the popup. */
			if (parseFloat(row.VRSALDCONFAM).toFixed(2) > 0){
				widget.setCurrentRow(row);
				widget.moveToFirst();
			}
			else {
				widget.moveToFirst();
			}
			widget.label = row.NMFAMILISALD;
			widget.currentRow.PARENT = row.PARENT;
			ScreenService.openPopup(widget);
		}
	};

	/* Updates the recharge grid with the value. */
	this.updateRechargeGrid = function(value, dataSource, fieldName, widget) {
		fieldParent = widget.currentRow.PARENT;
		value = value.toFixed(2);
		if (value > 0) {
			/* Updates the grid with the recharge value entered in the popup.
			   The for is needed to find the right family in the grid. */
			for (var i in dataSource){
				if (dataSource[i].NMFAMILISALD === fieldName && dataSource[i].PARENT === fieldParent){
					oldVRSALDCONFAM = dataSource[i].VRSALDCONFAM;
					dataSource[i].VRSALDCONFAM = parseFloat(value.replace(',','.')).toFixed(2).replace('.',',');
					if(oldVRSALDCONFAM == "0,00"){
						//Add to Valor Total
						dataSource[dataSource.length - 1].VRSALDCONFAM = (parseFloat(dataSource[dataSource.length - 1].VRSALDCONFAM) + parseFloat(value)).toFixed(2).replace('.',',');
					} else {
						/*If there was a value in the field, the value is subtracted from
						  Valor Total and then the new value is set. */
						dataSource[dataSource.length - 1].VRSALDCONFAM = (parseFloat(dataSource[dataSource.length - 1].VRSALDCONFAM) - parseFloat(oldVRSALDCONFAM));
						dataSource[dataSource.length - 1].VRSALDCONFAM = (parseFloat(dataSource[dataSource.length - 1].VRSALDCONFAM) + parseFloat(value)).toFixed(2).replace('.',',');
					}
					templateManager.updateTemplate();
				}
			}
			ScreenService.closePopup();
		} else {
			ScreenService.showMessage('O valor da recarga deve ser maior que R$ 0,00.');
		}
	};

	/* Sets the total recharge value for the label at the end of the recharge wizard. */
	this.setRechargeLabel = function(widget, fieldsWidget) {
		fieldsWidget.newRow();
		fieldsWidget.currentRow.$error = {};

		RechargeValueRepository.findAll().then(function (rechargeValues){
			valorTotal = rechargeValues[rechargeValues.length - 1].VRSALDCONFAM;
			widget.getField('vrTotalPedido').label = 'R$ ' + valorTotal;

			var row = fieldsWidget.currentRow;
			var storedCardDetails = getLocalVar('cardDetails');
			if (storedCardDetails){
				if (storedCardDetails.nrCartao) row.cardNumber = storedCardDetails.nrCartao;
				if (storedCardDetails.dtVencimento) row.cardExpiration = storedCardDetails.dtVencimento;
			}

			row.valorPedido = parseFloat(valorTotal).toFixed(2).replace('.', '').replace(/ /g, '');
			fieldsWidget.setCurrentRow(row);
			templateManager.updateTemplate();
		});
	};

	this.isValidCardNumber = function (field, row) {
		var cardNumber;
		if (field.field) {
			//É um elemento
			cardNumber = field.val().replace(/ /g, '').replace(/_/g, '');
		} else {
			//Não é elemento, busca na row
			cardNumber = row.cardNumber.replace(/ /g, '');
		}

		var valid = true;

		if (cardNumber.length > 1) {
			if (!cardNumber.startsWith(this.visaNumbers) && !cardNumber.startsWith(this.masterNumbers)) {
				valid = false;
			}
		}

		return valid;
	};

	this.setInvalidCard = function (fieldName, widget) {
		this.cleanFieldErrors(fieldName, widget);
		widget.currentRow.$error[fieldName] = [{"message" : "cartão não aceito"}];
	};

	this.cleanFieldErrors = function (fieldName, widget) {
		if (widget.currentRow.$error[fieldName]) {
			delete widget.currentRow.$error[fieldName];
		}
	};

	this.isExpiredCard = function (date) {
		var valid = true;
		var year = date.substr(date.length - 2);
		var month = date.substring(0, 2);

		var today = new Date();
		var currentMonth = ((today.getMonth() + 1).toString()).fixLength(2, "0");
		var currentYear = (today.getFullYear().toString().substr(2)).fixLength(2, "0");

		if (year < currentYear) {
			valid = false;
		} else if (year == currentYear) {
			if (month < currentMonth) {
				valid = false;
			}
		}

		return valid;
	};

	this.isValidCardExpiration = function (date) {
		var month = date.substring(0, 2);

		return month.between(1, 12);
	};

	String.prototype.fixLength = function (length, completeWhiteSpacesWith) {
		var dif = length - this.length;
		var str = "";
		if (dif > 0) {
			for (var i = 0; i < dif; i++) {
				str += completeWhiteSpacesWith;
			}
		}
		str += this;
		return str;
	};

	this.validateCardExpirationField = function (row, widget, fieldName) {
		var cardExpiration = row.cardExpiration || "";
		var date = cardExpiration.replace('/', '');

		if (!this.isValidCardExpiration(date) && date) {
			this.cleanFieldErrors(fieldName, widget);
			widget.currentRow.$error[fieldName] = [{"message" : "data inválida"}];
		} else if (!this.isExpiredCard(date) && date) {
			this.cleanFieldErrors(fieldName, widget);
			widget.currentRow.$error[fieldName] = [{"message" : "data vencida"}];
		}
	};

	String.prototype.between = function (first, second) {
		return this >= first && this <= second;
	};

	String.prototype.startsWith = function (array){
		for (var i = 0; i < array.length; i++) {
			if (this.slice(0, array[i].length) == array[i]) {
				return true;
			}
		}
		return false;
	};

	this.validateField = function (creditCardWidget) {
		var cardNumberField = creditCardWidget.getField('cardNumber');

		this.cleanFieldErrors(cardNumberField.name, cardNumberField.widget);
		if (!cardNumberField.widget.currentRow[cardNumberField.name]) {
			cardNumberField.widget.currentRow.$error[cardNumberField.name] = [
				{
					"message": "Obrigatório",
					"class": "cardNumberField-require"
				}
			];
		}
	};

	this.setCardFlag = function (creditCardWidget) {
		var cardFlagField = creditCardWidget.getField('cardFlag');
		var cardImageField = creditCardWidget.getField('cardImage');

		switch(cardFlagField.value()) {
			case "1":
				cardImageField.source = "images/cards/visa.png";
				break;
			case "2":
				cardImageField.source = "images/cards/mastercard.png";
				break;
			case "3":
				cardImageField.source = "images/cards/american_express.png";
				break;
			case "33":
				cardImageField.source = "images/cards/diners.png";
				break;
			case "41":
				cardImageField.source = "images/cards/elo.png";
				break;
			case "224":
				cardImageField.source = "images/cards/alelo.png";
				break;
			case "225":
				cardImageField.source = "images/cards/alelo.png";
				break;
			case "280":
				cardImageField.source = "images/cards/sodexo.png";
				break;
			case "281":
				cardImageField.source = "images/cards/sodexo.png";
				break;
			default:
				cardImageField.source = "images/cards/default.png";
				break;
		}
	};

	this.isValidFieldsForSubmit = function (widget) {
		widget.currentRow.$error = {};
		var fields = [
			'cardNumber',
			'cardExpiration',
			'securityCode'
		];

		for (var i = 0; i < fields.length; i++) {
			if (!widget.currentRow[fields[i]]) {
				widget.currentRow.$error[fields[i]] = [{"message" : "obrigatório", "class": "field-require"}];
			}
		}

		this.validateCardExpirationField(widget.currentRow, widget, widget.getField('cardExpiration').name);

		return Object.keys(widget.currentRow.$error).length === 0;
	};

	this.doBilletRecharge = function () {
		var row = templateManager.container.widgets[1].currentRow;
		LoginRepository.findOne().then(function (consumerData) {
			RechargeValueRepository.findAll().then(function (rechargeValues) {
				var query = Query.build()
								.where('CDBANCO').equals(row.selectedBank);
				BankDataRepository.findOne(query).then(function (bank) {
					RechargeService.doBilletRecharge(consumerData.CDFILIAL, consumerData.CDCLIENTE, row.selectedBank, rechargeValues, bank).then(function (billetBack) {
						var url = UtilitiesService.billetUrl + billetBack[0].NRPEDCRECONS + '.html';
						this.openPrintTab(url);
						//RechargeService.billetEmail(url, consumerData.CDCLIENTE, consumerData.CDCONSUMIDOR);
						RechargeValueRepository.remove(Query.build()).then(function (){
							WindowService.openWindow('PARENT_DASHBOARD_SCREEN');
						});
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}.bind(this));
	};

	this.openPrintTab = function (HTMLpath) {
	   // var wind = window.open(HTMLpath);
		//wind.print();
		window.open(HTMLpath);
	};

	/* Recharge procedure. */
	this.doCardRecharge = function() {
		if (this.isValidFieldsForSubmit(templateManager.container.widgets[1])) {
			var row = templateManager.container.widgets[1].currentRow;

			var codigoPedido = '01921902919210';

			var valorPedido = row.valorPedido;
			var codigoBandeira = templateManager.container.getWidget('creditCard').getField('cardFlag').value();
			//if (codigoBandeira === "") codigoBandeira = templateManager.container.widgets[1].fields[1].dataSource.data[0].value;

			var nrCartaoOrig = row.cardNumber;
			var dtVencimentoOrig = row.cardExpiration;

			var nrCartao = row.cardNumber.replace(/ /g, '');
			var dtVencimento = row.cardExpiration.replace('/', '');
			var codSeguranca = row.securityCode;

			LoginRepository.findOne().then(function (consumerData){
				RechargeValueRepository.findAll().then(function (rechargeValues){
					rechargeValues.pop(); //pulling out valorTotal because there's already valorPedido
					RechargeService.doCardRecharge(valorPedido, codigoPedido, codigoBandeira, dtVencimento, nrCartao, codSeguranca, consumerData.CDCLIENTE, consumerData.CDCONSUMIDOR, rechargeValues).then(function (paymentResponse){
						if (paymentResponse[0].RESULT === true){
							RechargeValueRepository.remove(Query.build()).then(function (){
								var cardDetails = {
									"nrCartao": nrCartaoOrig,
									"dtVencimento": dtVencimentoOrig
								};
								setLocalVar("cardDetails", cardDetails);
							});
							WindowService.openWindow('PARENT_DASHBOARD_SCREEN');
						}
					});
				});
			});
		}
	};

	/* Gets the consumer's details. This function call is identical to the one the Profile Page. */
	this.getConsumerDetails = function(widget) {
		LoginRepository.findOne().then(function (consumerData){
			ProfileService.getConsumerDetails(consumerData.CDCLIENTE, consumerData.CDCONSUMIDOR, consumerData.CDFILIAL).then(function(consumerDetails){
				widget.dataSource.data = consumerDetails;
				widget.moveToFirst();
				templateManager.updateTemplate();
			});
		});
	};

	eventAggregator.onRequestError(function(data){
		if (~data.data.config.url.indexOf('RechargeCardRepository')){
			ScreenService.showMessage('Não foi possivel conectar com a Instituição Financeira. Tente novamente mais tarde.');
		}
	});

}

Configuration(function(ContextRegister) {
	ContextRegister.register('RechargeController', RechargeController);
});
