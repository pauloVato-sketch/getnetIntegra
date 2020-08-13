function AuthService(RestEngine) {

	this.logout = function(){
		var params = {
			requestType: "Row",
			row: {}
		};
		var operatorLogoffRoute = '/operator/logout';
		return RestEngine.post(operatorLogoffRoute, params).then(function(response) {
			return response.messages.shift().message;
		});
	};

}

Configuration(function(ContextRegister) {
	ContextRegister.register('AuthService', AuthService);
});