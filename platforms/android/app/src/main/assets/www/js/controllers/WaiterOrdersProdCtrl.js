function WaiterOrdersProdCtrl($scope, ApplicationContext, $rootScope, templateManager) {
	$scope.searchList = $rootScope.searchList;

	$scope.init = function() {
		$scope.startWith = 0;
		$scope.flagNextAndPrev = false;
	};

	$scope.$watch('$rootScope.searchList', function(newData) {
		$scope.searchList = newData;
		if (newData) {
			$scope.widget.dataSource.filter({}).then(function(filtered) {
				$scope.widget.dataSource.data = filtered;
			});
		}
	});

	$scope.finishWith = 0;

	$scope.$watch($scope.getNrTotalButtons, function() {
		templateManager.updateTemplate();
	});

	$scope.getNrTotalButtons = function() {
		var widgetWidth = $('.zh-positions-container').width();
		var buttonSize = $('.odh-button-space:last').width();
        if ($('.lower-margin').width() != null){
            widgetWidth = $('.col-xs-12.col-sm-12.col-md-12').width();
        }
		if (buttonSize === null || buttonSize === 0) buttonSize = 52;
		var buttons = 0;
		while (buttons*buttonSize < widgetWidth) buttons++;
		buttons -= 2;
		if(buttons < 3){
			buttons = 3;
		}
		return buttons;
	};

	$scope.getQtNavegators = function(startWith, nrTotalPosicoes, nrTotalButtons) {
		var qt = 0;
		if (startWith > 0) {
			qt++;
			nrTotalButtons--;
		}
		if ((nrTotalPosicoes - startWith) > nrTotalButtons) {
			qt++;
		}
		return qt;
	};

	$scope.prev = function() {
		var newFinishWith = $scope.startWith - 1;
		var positionsOnScreen = $scope.getNrTotalButtons() - 2;
		var newStartWith = newFinishWith - positionsOnScreen + 1;
		$scope.startWith = (newStartWith <= 1) ? 0 : newStartWith;
		$scope.flagNextAndPrev = true;
	};

	$scope.next = function() {
		$scope.startWith = $scope.finishWith + 1;
		$scope.flagNextAndPrev = true;
	};

	$scope.getQtPositionsInScreen = function(startWith, nrTotalPosicoes) {
		return $scope.getNrTotalButtons() - $scope.getQtNavegators(startWith, nrTotalPosicoes, $scope.getNrTotalButtons());
	};

	$scope.lastNrTotalPosicoes = null;
    $scope.lastStartWith = null;
    $scope.lastResult = null;

    $scope.buildPositionsArray = function(nrTotalPosicoes, startWith) {
        if ((nrTotalPosicoes !== $scope.lastNrTotalPosicoes) || (startWith !== $scope.lastStartWith) ||($scope.field && $scope.field.forceReload)) {
            if($scope.field && $scope.field.forceReload){
            	$scope.field.forceReload = false;
            }
            $scope.lastNrTotalPosicoes = nrTotalPosicoes;
            $scope.lastStartWith = startWith;
			$scope.field = $scope.field || {};
			$scope.field._buttons = $scope.field._buttons || [];

			if (!startWith) startWith = 0;

			var result = [];
			var qtPositionsInArray = $scope.getQtPositionsInScreen(startWith, nrTotalPosicoes);

			var i = 0;
			for (i = startWith; i < (startWith + qtPositionsInArray) && i < nrTotalPosicoes; i++) {
				result.push(i);
				$scope.field._buttons.push({'index': i, 'selected': false});
				$scope.field._isStatusChanged = false;
			}

			$scope.finishWith = i - 1;

			/* Check when positions must be checked */
			if (typeof $scope.widget.position == "number") {
				$scope.checkActualPosition($scope.widget.position, result);
			}

			$scope.lastResult = result;
            return result;
        } else {
            return $scope.lastResult || [];
        }
    };

	$scope.getPositionName = function(position) {
		var clientMapping = $scope.field.dataSource.data[0].clientMapping;
		var consumerMapping = $scope.field.dataSource.data[0].consumerMapping;

		if (position > 0) {
			if (consumerMapping[position]) {
				return ' - ' + buildPositionName(consumerMapping[position].NMCONSUMIDOR);
			} else if (clientMapping[position]) {
				return ' - ' + buildPositionName(clientMapping[position].NMRAZSOCCLIE);
			} else {
				return '';
			}
		} else {
			return '';
		}
	};

	function buildPositionName(name) {
		if (name.length > 10) {
		   name = name.substr(0, 9) + '...';
		}
		return name;
	}

	$scope.clientMapping = {};
	$scope.consumerMapping = {};

	$scope.checkActualPosition = function(currentPosition, result) {
		var isOnScreen = result.some(function(position) {
			return currentPosition == position;
		});

		if (!isOnScreen && !$scope.flagNextAndPrev) {
			$scope.next();
			$scope.flagNextAndPrev = false;
		}
	};
	$scope.idealTextColor = idealTextColor;

    $scope.select = Util.buildDebounceMethod(function(widget, product, position) {
        widget.currentRow = product;
        if (product.IDTIPORECE) {
            ApplicationContext.PaymentController.receivePayment(widget, product);
        }
        else {
			ApplicationContext.AccountController.handleSelectedProduct(widget, product, position);
        }
    }, 450, true);

	$scope.toggleButtonSelectedStatus = function(field, buttonIndex, ignoreCustomFunction) {
		field.newPosition = buttonIndex;
		if (!field.toggleButtonSelectedStatus) {
			field.toggleButtonSelectedStatus = $scope.toggleButtonSelectedStatus;
		}

		var qtPositionsInArray = parseInt(field.dataSource.data[0].NRPOSICAOMESA);

		field._buttons[buttonIndex].selected = !field._buttons[buttonIndex].selected;

		field.position = [];
		for (var p = 0; p < qtPositionsInArray; p++) {
			if ($scope.isButtonSelected(field, p)) {
				field.position.push(p);
			}
		}

		/** poo-taa-rea **/
		field._isStatusChanged = true;
		if (field) {
			if (field.touchstart) {
				field.touchstart();
			} else if (field.click) {
				field.click();
			}

			if (field.forceFunction && field.customFunction) {
				var customFunction = _.get(ApplicationContext, field.customFunction);
				if (customFunction && !ignoreCustomFunction) {
					customFunction(field);
				}
			}
		}
	};

	$scope.isButtonSelected = function(field, buttonIndex){
		return field._buttons[buttonIndex].selected;
	};

	$scope.onLongTabProduct = function(widget, product){
		if (product.CDPRODUTO){
			var popupDetalhes = widget.container.getWidget('popupDetalhesProduto');
			popupDetalhes.currentRow = product;
			ApplicationContext.ScreenService.openPopup(popupDetalhes);
		}
	};
}