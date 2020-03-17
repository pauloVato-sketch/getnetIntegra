(function () {

	Configuration(registerSelectAutocomplete);

	function registerSelectAutocomplete(decodeTemplateFieldService) {
		decodeTemplateFieldService.registerViewTemplate(
			"zh-select-autocomplete#field/select-autocomplete.html",
			"zh-select-autocomplete#field/select-autocomplete-view.html"
		);
	}

})();