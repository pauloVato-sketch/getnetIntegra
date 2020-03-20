function CieloTestController(CieloTestService) {

	this.testCieloMobile = function() {
		/*Testes relacionados a manipulação da string url
		var url = "cielomobile://pagar?urlCallback=appcliente://retornopagamento&mensagem=%7B%22dataTransacao%22:%22141208104222%22,%22valor%22:%221200%22,%22idTransacao%22:%22123412%22,%22referencia%22:%22refer%C3%AAncia%22,%22tipoTransacao%22:1,%22nomeAplicacao%22:%22aplicado%20cliente%22,%22estVenda%22:%22000000000000000004%22%7D";

		var GET = {};
		var query = url.substring(45).split("&");
		for (var i = 0, max = query.length; i < max; i++) {
			if (query[i] === "") continue;// check for trailing & with no param
			var param = query[i].split("=");
			GET[decodeURIComponent(param[0])] = decodeURIComponent(param[1] || "");
		}
		var mensagem = GET['mensagem'];
		console.log(GET);
		*/

		CieloTestService.testConnection();
	};
}

Configuration(function(ContextRegister) {
	ContextRegister.register('CieloTestController', CieloTestController);
});