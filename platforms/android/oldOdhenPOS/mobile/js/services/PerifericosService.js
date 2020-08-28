function PerifericosService(ScreenService) {
	var header = {
		"Content-Type": "application/json"
	};
	var POST = "POST";

	//Recebe array de parametros para impressao. 
	//Cada posicao deve conter informacoes necessarias 
	//para realizar uma requisicao ao perifericos.

	this.print = function (params) {
		try {
			ScreenService.showLoader();
			if (!Array.isArray(params)) {
				if (params.comandos) {
					params = [params];
				} else {
					params = _.toArray(params);
				}
			}
			var promiseArray = params.map(function (param) {
				if (param.saas) {
					var url = param.impressora.DSIPPONTE + "/print";
					var body = JSON.stringify({
						printerInfo: {
							printerType: param.impressora.IDMODEIMPRES,
							port: param.impressora.CDPORTAIMPR
						},
						commands: param.comandos
					});

					return fetch(
						url, {
						headers: header,
						method: POST,
						body: body
					}
					);
				}
			});
			return Promise.all(promiseArray);
		} catch (error) {
			error.message = 'Não foi possível comunicar com a impressora. <br><br>' + params[0].NMIMPRLOJA + ' : Endereço do Periféricos inválido ou se encontra desligado.';
			error.error = true;
			return error;
		} finally {
			ScreenService.hideLoader();
		}
	};

	this.test = function (params) {
		ScreenService.showLoader();
		var body = JSON.stringify({
			printerInfo: {
				printerType: params.IDMODEIMPRES,
				port: params.CDPORTAIMPR
			}
		});

		var options = {
			headers: header,
			method: POST,
			body: body
		};

		const url = params.DSIPPONTE + "/test";
		return fetch(url, options)
			.then(function (response) {
				response = response.text();
				try {
					response = JSON.parse(response);
				} catch (error) {
					response.message = 'Não foi possível comunicar com a impressora. <br><br>' + params.NMIMPRLOJA + ' : Endereço do Periféricos inválido ou se encontra desligado.';
					response.error = true;
				}
				return response;
			}).catch(function (error) {
				error.message = 'Não foi possível comunicar com a impressora. <br><br>' + params.NMIMPRLOJA + ' : Endereço do Periféricos inválido ou se encontra desligado.';
				error.error = true;
				return error;
			}).finally(function () {
				ScreenService.hideLoader();
			});
	};
}

Configuration(function (ContextRegister) {
	ContextRegister.register("PerifericosService", PerifericosService);
});
