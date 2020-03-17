function WaiterNamedPositions($scope, $timeout, ApplicationContext, $rootScope, templateManager, ScreenService) {

	function unselectAllPositions() {
		if (_.get($scope, 'field._buttons')) {
			$scope.field._buttons.forEach(function(currentButton) {
				currentButton.selected = false;
			});
			$scope.field.position = [];
		}
	}

	$scope.init = function() {
		this.stateService = ApplicationContext.WaiterNamedPositionsState;

		$scope.startWith              = this.stateService.startWith;
		$scope.flagNextAndPrev        = this.stateService.flagNextAndPrev;
		$scope.showPrev               = this.stateService.showPrev;
		$scope.showNext               = this.stateService.showNext;
		$scope.oldNrTotalPosicoes     = this.stateService.oldNrTotalPosicoes;
		$scope.oldMaxButtons          = this.stateService.oldMaxButtons;
		$scope.maxButtons             = this.stateService.maxButtons;
		$scope.currentPage            = this.stateService.currentPage;
		$scope.oldCurrentPage         = this.stateService.oldCurrentPage;
		$scope.finishWith             = this.stateService.finishWith;
		$scope.clientMapping          = this.stateService.clientMapping;
		$scope.consumerMapping        = this.stateService.consumerMapping;
		$scope.positionNamedMapping   = this.stateService.positionNamedMapping;
		$scope.numberOfButtons        = this.stateService.numberOfButtons;
		$scope.pageHistory            = this.stateService.pageHistory;
		$scope.oldTotalPosicoes       = this.stateService.oldTotalPosicoes;
		$scope.currentPositionsObject = this.stateService.currentPositionsObject;

		if (this.stateService.mustUnselect) {
			unselectAllPositions();
			this.stateService.mustUnselect = false;
		}
	};

	$scope.init();

	$scope.$on('$destroy', function() {
		this.stateService = ApplicationContext.WaiterNamedPositionsState;

		this.stateService.startWith              = $scope.startWith;
		this.stateService.flagNextAndPrev        = $scope.flagNextAndPrev;
		this.stateService.showPrev               = $scope.showPrev;
		this.stateService.showNext               = $scope.showNext;
		this.stateService.oldNrTotalPosicoes     = $scope.oldNrTotalPosicoes;
		this.stateService.oldMaxButtons          = $scope.oldMaxButtons;
		this.stateService.maxButtons             = $scope.maxButtons;
		this.stateService.currentPage            = $scope.currentPage;
		this.stateService.oldCurrentPage         = $scope.oldCurrentPage;
		this.stateService.finishWith             = $scope.finishWith;
		this.stateService.clientMapping          = $scope.clientMapping;
		this.stateService.consumerMapping        = $scope.consumerMapping;
		this.stateService.positionNamedMapping   = $scope.positionNamedMapping;
		this.stateService.numberOfButtons        = $scope.numberOfButtons;
		this.stateService.pageHistory            = $scope.pageHistory;
		this.stateService.oldTotalPosicoes       = $scope.oldTotalPosicoes;
		this.stateService.currentPositionsObject = $scope.currentPositionsObject;
	});

	$scope.$watch(function(scope){return scope.currentPositionsObject;}, function() {
		$timeout(paginatePositions);
	}, true);

	$scope.prev = function() {
        if ($scope.pageHistory == undefined) $scope.pageHistory = [0];
		$scope.startWith = $scope.pageHistory.pop();
		$scope.currentPage.page--;
		$scope.currentPage.paginated = false;
		$scope.flagNextAndPrev = true;
		$scope.buildNamedPositions(false, true);
		handleNavigationButtons();
	};

	$scope.next = function() {
        if ($scope.pageHistory)	$scope.pageHistory.push($scope.startWith);
		$scope.startWith = $scope.finishWith;
		$scope.currentPage.page++;
		$scope.currentPage.paginated = false;
		handleNavigationButtons();
		$scope.buildNamedPositions(false, true);
	};

	$scope.buildNamedPositions = function(useMaxButtons, isChangingPage) {
		var nrTotalPosicoes = $scope.field.dataSource.data[0].NRPOSICAOMESA;
		var clientChanged = $scope.field.dataSource.data[0].clientChanged;
		if (clientChanged || useMaxButtons || isChangingPage || nrTotalPosicoes != $scope.oldTotalPosicoes) {
			if (clientChanged) {
				$scope.field.dataSource.data[0].clientChanged = false;
			}
			if ($scope.currentPage && (clientChanged || nrTotalPosicoes != $scope.oldTotalPosicoes)) {
				$scope.currentPage.paginated = false;
			}
			var isSubtracting = $scope.oldTotalPosicoes > nrTotalPosicoes;
			$scope.oldTotalPosicoes = nrTotalPosicoes;

			$scope.field = $scope.field || {};
			$scope.field._buttons = $scope.field._buttons || [];
			if (!$scope.startWith) {
				$scope.startWith = 0;
			}
			var result = [];
			var idx = $scope.startWith;
			var numberOfButtons = 0;
			for (idx; idx < nrTotalPosicoes; idx++) {
				numberOfButtons++;
				result.push(idx);
				if (_.findIndex($scope.field._buttons, {'index': idx}) === -1) {
					$scope.field._buttons.push({'index': idx, 'selected': false});
				}
				$scope.field._isStatusChanged = false;
				if (useMaxButtons && numberOfButtons == $scope.maxButtons + 1) {
					break;
				}
			}
			$scope.numberOfButtons = numberOfButtons;
			$scope.finishWith = idx;
			if (useMaxButtons) {
				$scope.finishWith = $scope.finishWith + 1;
			}

			if (typeof $scope.widget.position == "number") {
				$scope.checkActualPosition($scope.widget.position, result);
			}
			$scope.currentPositionsObject = result;

			if (isChangingPage || isSubtracting) {
				handleNavigationButtons(isSubtracting);
			}
		}

		return $scope.currentPositionsObject;
	};

	function paginatePositions() {
		// only paginates if there are buttons to paginate
        if ($scope.currentPage == null){
            $scope.currentPage = {
                page: 0,
                paginated: false
            };
        }
		if ($('#odh-named-positions'+$scope.field.id).width()) {
			var nrTotalPosicoes = $scope.field.dataSource.data[0].NRPOSICAOMESA;
			$scope.maxButtons = calculateHowManyButtonsFit();
			if (!$scope.currentPage.paginated) {
			 // && (nrTotalPosicoes != $scope.oldNrTotalPosicoes) || ($scope.maxButtons != $scope.oldMaxButtons) || ($scope.currentPage.page != $scope.oldCurrentPage)) {
				$scope.oldCurrentPage = $scope.currentPage.page;
				$scope.oldNrTotalPosicoes = nrTotalPosicoes;
				$scope.oldMaxButtons = $scope.maxButtons;

				if ($scope.maxButtons && (nrTotalPosicoes > $scope.maxButtons)) {
					$scope.buildNamedPositions(true, false);
					handleNavigationButtons();
					$scope.currentPage.paginated = true;
					templateManager.updateTemplate();
				}
			}
		}
	}

	function handleNavigationButtons(isSubtracting) {
		var nrTotalPosicoes = $scope.field.dataSource.data[0].NRPOSICAOMESA;
		if ($scope.startWith > 0) {
			$scope.showPrev = true;
		} else {
			$scope.showPrev = false;
		}
		var maxButtons = $scope.maxButtons;
		if (isSubtracting) {
			maxButtons++;
		}
		if (nrTotalPosicoes > $scope.startWith + maxButtons) {
			$scope.showNext = true;
		} else {
			$scope.showNext = false;
		}
	}

	function calculateHowManyButtonsFit() {
		// this function must run only after the buttons appear on the screen (after the buildNamedPositions)
		var widgetWidth = $('#odh-named-positions'+$scope.field.id).width() - 100;
		var buttonsToCalculate = $('#odh-named-positions'+$scope.field.id +'>.odh-button-space');
		var totalButtonWidth = 0;
		var buttonToBreak = 0;
		for (var idx = 0; idx < buttonsToCalculate.length; idx++) {
			var currentButton = $(buttonsToCalculate[idx]);

			if (currentButton.width) {
				totalButtonWidth += currentButton.width();
			}
			buttonToBreak = idx;
			if (totalButtonWidth > widgetWidth) {
				break;
			}
		}
		var maxButtons = totalButtonWidth <= widgetWidth ? buttonsToCalculate.length - 2 : buttonToBreak - 2;
		if (maxButtons < 0) {
			maxButtons = 0;
		}
		return maxButtons;
	}

	$scope.getPositionName = function(position) {
		var clientMapping = $scope.field.dataSource.data[0].clientMapping;
		var consumerMapping = $scope.field.dataSource.data[0].consumerMapping;
		var positionNamedMapping = $scope.field.dataSource.data[0].positionNamedMapping;

		if (position > 0) {
			if (positionNamedMapping[position]){
				return ' - ' + buildPositionName(positionNamedMapping[position].DSCONSUMIDOR);
			} else if (consumerMapping[position]) {
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
		if (name && name.length > 10) {
			return name.substr(0, 9) + '...';
		} else {
			return name;
		}
	}

	$scope.checkActualPosition = function(currentPosition, result) {
		var isOnScreen = result.some(function(position) {
			return currentPosition == position;
		});

		if (!isOnScreen && !$scope.flagNextAndPrev) {
			$scope.next();
			$scope.flagNextAndPrev = false;
		}
	};

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

		field._isStatusChanged = true;
		if (field) {
			if (field.forceFunction && field.customFunction) {
				var customFunction = _.get(ApplicationContext, field.customFunction);
				if (customFunction && !ignoreCustomFunction) {
					customFunction(field);
				}
			}
		}
	};

	$scope.isButtonSelected = function(field, buttonIndex){
        if (_.isEmpty(field._buttons)){
            $scope.buildNamedPositions(true, false);
        }
		return field._buttons[buttonIndex].selected;
	};
}