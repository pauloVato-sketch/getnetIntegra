function CustomGridController($scope, maskEngine, templateManager, ScreenService, $timeout, ApplicationContext) {

	var controller = this;

	this.currencyFormat = function (value) {
		return maskEngine.currencyFormat(value);
	};

	$scope.widget.shoppingCart = {
		items: [],
		subtotal: 0,
		deliveryFee: 0,
		total: 0
	};

	$scope.widget.setDeliveryFee = function (fee) {
		$scope.widget.shoppingCart.deliveryFee = fee;
		$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
	};

	var actions = [{
		"label": "Cancelar",
		"showAsAction": "back",
		"isVisible": true,
		"showOnForm": true,
		"showOnList": true,
		"hideIcon": true,
		"events": [{
			"name": "ActionEvent",
			"code": "ScreenService.closePopup();",
			"id": 9998
		}]
	}, {
		"label": "OK",
		"showAsAction": "never",
		"isVisible": true,
		"showOnForm": true,
		"showOnList": true,
		"events": [{
			"name": "ActionEvent",
			"code": "args.owner.widget.saveItem(args.owner.widget);",
			"id": 9999
		}]
	}, {
		"icon": "trash",
		"showAsAction": "always|edit|checked_rows|view",
		"isVisible": true,
		"showOnForm": true,
		"showOnList": true,
		"label": "Excluir item",
		"events": [{
			"name": "ActionEvent",
			"code": "args.owner.widget.deleteItem(args.owner.widget)",
			"id": 9999
		}]
	}];

	var popupWidget = metaDataFactory.widgetFactory({
		"id": 9999,
		"name": "product_detail_widget",
		"container": $scope.widget.container,
		"parentWidget": $scope.widget,
		"label": "",
		"template": "widget/product-grid/detail.html",
		"isVisible": true,
		"popupNoBlock": false,

		"fields": [{
			"isReadOnly": false,
			"isVisible": true,
			"isVisibleForm": true,
			"isVisibleGrid": false,
			"template": "field/number.html",
			"label": "Quantidade",
			"name": "qtty",
			"minValue": 1,
			"maxValue": 999,
			"spin": true
		}],
		"dataSource": [],
		"dataSourceFilter": [],
		"events": [],
		"actions": actions
	});

	popupWidget.saveItem = function (widget) {
		widget.productItem.qtty = widget.getField('qtty').value();
		if (controller.checkConstraints(widget)) {
			if (!widget.updating) {
				controller.addItem(widget.productItem, false);
			} else {
				controller.updateItem(widget.productItem);
			}

			ScreenService.closePopup();
		} else {
			ScreenService.showMessage("Verifique os erros nas seleções dos detalhes do produto!", "error");
		}
	};

	popupWidget.deleteItem = function (widget) {
		ScreenService.confirmMessage("Tem certeza que deseja excluir o produto?", "question", function () {
			widget.productItem.qtty = 0;
			controller.updateItem(widget.productItem);
			ScreenService.closePopup();
		});
	};

	this.checkConstraints = function (widget) {
		var constraintsOK = true;
		for (var i = 0; i < widget.productItem.detailPages.length; i++) {
			var page = widget.productItem.detailPages[i];
			var constraints = page.constraints;

			page.constraintError = (constraints.minSelection && (!page.selectItems || page.selectItems.length < constraints.minSelection)) ||
				(constraints.maxSelection && page.selectItems && page.selectItems.length > constraints.maxSelection);
			if (page.constraintError && constraintsOK) {
				constraintsOK = false;
				controller.selectDetailPage(page, widget.productItem);
			}
		}

		return constraintsOK;
	};

	this.showProductDetails = function (item, updating) {
		popupWidget.label = updating ? ScreenService.i18n("Atualizar Produto") : ScreenService.i18n("Incluir Produto");
		popupWidget.productItem = item;
		popupWidget.updating = updating;
		// delete action only when updating
		popupWidget.actions[2].isVisible = updating;
		if ($scope.widget.onProductClick) {
			$scope.widget.onProductClick({
				data: popupWidget
			});
		}
		ScreenService.openPopup(popupWidget);
	};

	function clearItemDetails(item) {
		item.optionsPrice = 0;
		for (var i = 0; i < item.detailPages.length; i++) {
			item.detailPages[i].selectItems = [];
		}
	}

	function getSelectedOptions(item) {
		var items = [];
		if (item.detailPages) {
			for (var i = 0; i < item.detailPages.length; i++) {
				if (item.detailPages[i].selectItems) {
					items = items.concat(item.detailPages[i].selectItems);
				}
			}
		}

		return $.map(items, function (obj) {
			return obj[$scope.widget.observationCodeField];
		}).join('|');
	}

	this.addItem = function (item, details) {
		if (Util.isUndefined(details)) {
			details = (item[$scope.widget.productDetailsField] || []);
		}

		var itemAdded = $.extend(true, {}, item);

		if (details) { // When clicking the item.
			itemAdded.qtty = 1;
			itemAdded.optionsPrice = 0;
			this.showProductDetails(itemAdded, false);
		} else { // When adding the item.
			var existingItem = $.grep($scope.widget.shoppingCart.items, function (element) {
				return element[$scope.widget.productCodeField] == item[$scope.widget.productCodeField] &&
					getSelectedOptions(element) == getSelectedOptions(item);
			});

			if (existingItem.length === 0) { // New item in the cart.
				itemAdded.ID = this.makeItemID();
				itemAdded.qtty = itemAdded.qtty || 1;
				itemAdded.optionsPrice = itemAdded.optionsPrice || 0;
				itemAdded.detailPages = itemAdded.detailPages || [];
				itemAdded.total = (itemAdded[$scope.widget.productPriceField] + itemAdded.optionsPrice) * itemAdded.qtty;
				$scope.widget.shoppingCart.items.push(itemAdded);
			} else { // Similar item already exists in the cart.
				existingItem[0].qtty += item.qtty || 1;
				existingItem[0].total += (item[$scope.widget.productPriceField] + (item.optionsPrice || 0)) * (item.qtty || 1);
			}

			// Transforms the observations into a specific array to be read by the checking screen.
			if (itemAdded.selectedPage) {
				itemAdded[$scope.widget.observationCodeField] = [];
				for (var i in itemAdded.selectedPage.selectItems) {
					itemAdded[$scope.widget.observationCodeField].push(itemAdded.selectedPage.selectItems[i][$scope.widget.observationCodeField]);
				}
			}

			$scope.widget.shoppingCart.subtotal += (item[$scope.widget.productPriceField] + (item.optionsPrice || 0)) * (item.qtty || 1);
			$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
			this.updateCartAction();

			templateManager.updateTemplate();
		}
	};

	this.makeItemID = function () {
		var id = 0;
		$scope.widget.shoppingCart.items.forEach(function (cartItem) {
			if (cartItem.ID > id) id = cartItem.ID;
		});
		return ++id;
	};

	this.updateCartItem = function (item) {
		item.oldQtty = item.qtty;
		item.oldOptionsPrice = item.optionsPrice;
		this.showProductDetails(item, true);
	};

	this.updateItem = function (item) {
		if (item.qtty > 0) {
			item.total = (item[$scope.widget.productPriceField] + item.optionsPrice) * (item.qtty || 1);
		} else {
			$scope.widget.shoppingCart.items.splice($scope.widget.shoppingCart.items.indexOf(item), 1);
		}

		$scope.widget.shoppingCart.subtotal -= (item[$scope.widget.productPriceField] + item.oldOptionsPrice) * item.oldQtty;
		$scope.widget.shoppingCart.subtotal += (item[$scope.widget.productPriceField] + item.optionsPrice) * item.qtty;
		$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
		this.updateCartAction();
		templateManager.updateTemplate();
	};

	this.updateCartAction = function () {
		//$scope.widget.getAction('cart').hint = $scope.widget.shoppingCart.items.length;
		//$scope.widget.getAction('cart').readOnly = $scope.widget.shoppingCart.items.length === 0;
	};

	this.clearShoppingCart = function () {
		ScreenService.confirmMessage("Tem certeza que deseja cancelar o seu pedido?", "question", function () {
			$scope.widget.shoppingCart.items = [];
			$scope.widget.shoppingCart.subtotal = 0;
			$scope.widget.shoppingCart.total = $scope.widget.shoppingCart.subtotal + $scope.widget.shoppingCart.deliveryFee;
			controller.updateCartAction();
			templateManager.updateTemplate();
		});
	};

	this.confirmOrder = function () {
		$scope.widget.getAction('cart').click();
	};

	this.productSearch = function (item) {
		return !$scope.searchList ||
			(item[$scope.widget.productNameField] && item[$scope.widget.productNameField].indexOfLatin($scope.searchList) != -1) ||
			(item[$scope.widget.productDescField] && item[$scope.widget.productDescField].indexOfLatin($scope.searchList) != -1);
	};

	this.clearSearch = function () {
		$scope.searchList = "";
	};

	this.selectDetailPage = function (page, item) {
		item.selectedPage = page;

		var detailContainer = $('.product-detail-pages');
		var tabLine = detailContainer.find('.zh-tab-line');
		$timeout(function () {
			var pageHeaderElement = detailContainer.find('.detail-pages-header > li').eq(item.detailPages.indexOf(page));
			tabLine.width(pageHeaderElement.width());
			tabLine.css('left', pageHeaderElement.position().left);
		});
	};

	this.selectOption = function (option, page, item) {
		if (Util.isUndefined(page.selectItems)) {
			page.selectItems = [];
		}

		var index = this.getOptionIndex(page, option);
		if (index > -1) {
			item.optionsPrice -= page.selectItems[index].price || 0;
			page.selectItems.splice(index, 1);
		} else {
			page.selectItems.push(option);
			item.optionsPrice += option.price || 0;
			if (page.constraints.maxSelection && page.selectItems.length > page.constraints.maxSelection) {
				item.optionsPrice -= page.selectItems[0].price || 0;
				page.selectItems.splice(0, 1);
			}
		}
	};

	this.getOptionIndex = function (page, option) {
		var index = -1;
		if (page.selectItems) {
			for (var i = 0; i < page.selectItems.length; i++) {
				if (option[$scope.widget.parentWidget.observationCodeField] == page.selectItems[i][$scope.widget.parentWidget.observationCodeField]) {
					index = i;
				}
			}
		}

		return index;
	};
}