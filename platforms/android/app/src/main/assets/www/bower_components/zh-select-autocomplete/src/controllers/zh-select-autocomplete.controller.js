function ZhSelectAutoCompleteController($scope, $rootScope, $timeout, templateManager, ScreenService, ZhUtil, SelectService, ZHPromise, EventChildPropagation, DecodeValueFieldService, $filter, metaDataFactory) {

	var self = this;
    var lastSearch = "";
    var propertyToSearch = [];
	var isMakingRequest = false;
	var shouldSelectAfterLoad = false;

	$scope.field.lazyLoadEvent = $scope.field.lazyLoadEvent || templateManager.project.lazyLoadEvent;

	$scope.isRequestBeingMade = function() {
		return isMakingRequest;
	};

	$scope.selectAfterLoad = function() {
		shouldSelectAfterLoad = true;
	};

    var dataInitialized = Util.isUndefinedOrNull($scope.field.dataSource.lazyLoad) ?
            true : !$scope.field.dataSource.lazyLoad;

	$scope.viewMore = ScreenService.i18n("View more...");
	$scope.filteredData = [];
	$scope.showDropdown = true;
	var filterUpdated = false;

	var oldFieldToggleDropdown = $scope.field.toggleDropdown;
    $scope.field.toggleDropdown = function(){
        $scope.showDropdown = !$scope.showDropdown;
    };

	var oldFieldInitializeField = $scope.field.initializeField;
	$scope.field.initializeField = function() {
		if(!dataInitialized){
			dataInitialized = true;
			updateDatasource(lastSearch, true);
		}
	};

	var parentReload = $scope.field.reload.bind($scope.field);

	/**
	 * Override the field function reload to initialize the field when called.
	 * @param {Object[]} [addictionalFilters] - An object of filters to be applied when reloading the field
	 * @returns {Promise.<Array>} Field data with the given fields.
	 */
	$scope.field.reload = function(addictionalFilters){
		$scope.field.initializeField();
		return parentReload(addictionalFilters).then(function(response){
			updateDatasource();
			return response;
		});
	};

	/**
	 * Get a row from dataSets
	 *
	 * @function getNewData
	 * @private
	 * @param   {Object} keys - DataSets returned by request
	 * @returns {Array} Returns a data array.
	 */
	function getNewData(dataSets){
		var data;
		result = dataSets.dataset;
		var keys = Object.keys(result);

		if (keys && keys.length > 0 && keys[0] !== '0') {
			var dataSetName = $scope.field.dataSource.name.split('/').pop();
			if (result[dataSetName]) {
				data = result[dataSetName];
			}
		} else {
			data = result;
		}

		return data;
	}

	/**
	 * Make a request to get an specific value.
	 *
	 * @function findNewValue
	 * @private
	 * @param   {Array} keys - CurrentRow's keys.
	 * @param   {Field} field - Field.
	 * @returns {Array} Returns a row.
	 */
	function findNewValue(keys, field, value){
		var filter = [];
		keys.forEach(function(key){
			filter.push({
				"name": key,
				"operator": '=',
				"value": value[key]
			});
		});
		var storageStrategy = field.dataSource.getStorageStrategy();
		return storageStrategy.getDataSource(field.dataSource.name, function(){}, filter, 1, 2, undefined, 0);
	}

	/**
	 * Prepares the value.
	 *
	 * @function findNewValue
	 * @private
	 * @param   {Array} valueFields - The field's valueFields
	 * @param   {String|Object} value - the value to be inserted.
	 * @returns {Object} The prepared value.
	 */
	function prepareValue(valueFields, value) {
		var valueBuilt = {};

		if(valueFields.length === 1 && typeof value !== 'object'){
			valueBuilt[valueFields[0]] =  value;
		} else {
			valueBuilt = value;
		}

		return valueBuilt;
	}

	/**
	 * Override the field function value to use valueFields.
	 * @param {Object} - The value to be set.
	 */
	var oldFieldValue = $scope.field.value;
	$scope.field.value = function(value){
		if(value){
			var shouldMakeRequest = $scope.field.dataSource.rest && $scope.field.hasPagination;
			currentRow = $scope.field.widget.currentRow;
			valueFields = $scope.field.valueFields;

			value = prepareValue(valueFields, value);

			if(shouldMakeRequest){
				findNewValue(valueFields, $scope.field, value).then(function(result){
					result = getNewData(result);
					if(result.length === 1){
						self.addItemAsSelected(result[0], $scope.field);
					} else {
						currentRow[$scope.field.name] = "";
					}
				});
			} else {
				var row = $scope.field.dataSource.data.filter(function(row){
					var result = true;

					valueFields.forEach(function(key){
						if(row[key] !== value[key]){
							result = false;
						}
					});

					return result;
				});

				if(row.length === 1){
					self.addItemAsSelected(row[0], $scope.field);
				} else {
					currentRow[$scope.field.name] = "";
				}
			}
		} else {
			return this.widget.currentRow[this.name];
		}
	};

	/**
	 * Extends the behavior of it's parent (ListController).
	 */
	function extendBehavior() {
		Util.extend(self, new ListTemplatable());
	}
	extendBehavior();

    /**
     * Retrieves the code to be executed on FieldOnAdd event.
     * @returns {String} - Code to be executed.
     */
	function getOnAddCode(){
		var onAddEventCode = $scope.field.events.filter(function(event) {
			return event.name === "FieldOnAdd";
		});

		if(onAddEventCode.length > 0){
			return onAddEventCode[0].code;
		} else {
			return "args.owner.widget.onAdd()";
		}
	}

	var onAddCode = getOnAddCode();

	/**
	 * Build all actions of the widget that appears when the field's popup is opened.
	 *
	 * @function buildActions
	 * @private
	 * @param   {Field} field - Widget owner
	 * @returns {Array} - Actions of the widget
	 */
	function buildActions(field) {
		var actions = [{
			"label": ScreenService.i18n("Cancel"),
			"showAsAction": "back",
			"isVisible": true,
			"showOnForm": true,
			"showOnList": true,
			"hideIcon": true,
			"events": [{
				"name": "ActionEvent",
				"code": "ScreenService.closePopup(); args.owner.widget.afterClosePopup(args.owner.widget.selectField);",
				"id": 9998
			}]
		}];

		if (field.showAddButtonAs == 'action') {
			actions.push({
				"icon": "plus",
				"showAsAction": "always|edit|checked_rows|view",
				"isVisible": true,
				"showOnForm": true,
				"showOnList": true,
				"events": [{
					"name": "ActionEvent",
					"code": onAddCode,
					"id": 9999
				}]
			});
		}

		return actions;
	}

    /**
     * Initialize the field.
     *
     * @function initialize
     */
    (function initialize() {
        initializeValuesOnCurrentRow();
        initializeFieldProperties();
        return $scope.field.dataSource.data;
    })();

    /**
     * Prepares the new clearValue function for the select-autocomplete's field.
     */
    $scope.field.clearValue = function() {
        clearFieldValue(this);
    };

    /**
     * Clears the select-autocomplete value and it's dependents.
     * @param {Field} field The select autocomplete.
     */
    function clearFieldValue(field) {
        $scope.resetPlaceholderValue();
        if (!field.isReadOnly()) {
            field.clearByOutData();
            field.clearByName();
            field.clearByOutDescription();
            field.dataSource.clearCheckedRows();
            setChanged(field);
            if (field.change) {
                field.change();
            }
            $scope.clearAutoCompleteListData();
            $scope.hideList();
            templateManager.updateTemplate();
            $scope.removeFocusOfInput();
        }
    }

	/**
     * Config which template of widget will appear when the field's popup is opened.
     *
     * @function configTemplate
     * @private
     * @param   {Field} field - Widget owner
     * @returns {String} template - Widget template
     */
	function configTemplate(field){
		var template;
		if (!field.showSelectAs) {
			template = "widget/list.html";
		}
		else if (field.showSelectAs === "tree") {
			template = "widget/tree/grid.html";
		}
		else {
			template = "widget/" + field.showSelectAs + ".html";
		}

		return template;
	}

	/**
     * Create the fields to be inserted
     *
     * @function buildFields
     * @private
     * @param   {descriptionField} displayedField - Used to create the fields
     * @param   {Number} key - Array position
     */
	function buildFields(displayedField, key){
        this[key] = {
            "name": displayedField,
            "showLabel": false,
            "showOnList": true,
            "mask": this.mask
        };
    }

    /**
     * Insert labels on the fields.
     *
     * @function insertLabels
     * @private
     * @param   {String} label - label to be inserted
     * @param   {Number} key - Array position
     */
    function insertLabels(label, key){
        this[key].label = label;
        this[key].showLabel = true;
    }

	/**
     * Build all fields of the widget that appears when the field's popup is opened.
     *
     * @function buildSelectWidgetFields
     * @private
     * @param   {Field} field - Widget owner
     */
	function buildSelectWidgetFields(field) {
        var fields = [];

        if (field.fields && field.fields.length) {
            fields = field.fields;
        } else {
            field.descriptionFields.forEach(buildFields, fields);
        }

        if(field.displayedFieldsLabels){
            field.displayedFieldsLabels.forEach(insertLabels, fields);
        }

        fields.forEach(function(currentElement) {
            metaDataFactory.fieldFactory(currentElement, field.selectWidget, true);
        });
	}

	/**
     * Build all events of the widget that appears when the field's popup is opened.
     *
     * @function buildEvents
     * @private
     * @returns {Array} - Events of the widget
     */
	function buildEvents(){
		return [
			{
				"name": "WidgetAfterMoveRow",
				"code": "args.owner.setSelected(args);",
				"id": 9999
			},
			{
				"name": "WidgetAfterLoadDataSource",
				"code": "args.owner.updateDatasource()",
				"id": 9999
			}
		];
	}

	/**
	 * Set the args' row as selected.
	 *
	 * @function setSelected
	 * @private
	 * @param {Object} args - The window args.
	 */
	function setSelected(args) {
		if (!$scope.field.allowSelectOnlyLastChild || ($scope.field.allowSelectOnlyLastChild && this.isTree($scope.field.selectWidget) && isLastChild(args.row))) {
			ScreenService.closePopup();
			self.onAutoCompleteItemClick(args.row);
		}
	}

	/**
	 * Routine executed after the field's popup be closed.
	 *
	 * @function afterClosePopup
	 * @private
	 * @param {Field} field - The field that had it's popup closed.
	 */
	function afterClosePopup(field) {
		if (field.element) {
			field.element.focus();
		}
	}

	/**
	 * Build the widget that appears when the field's popup is opened.
	 *
	 * @function buildSelectWidget
	 * @private
	 * @param {Field} args - Widget owner.
	 * @param {Object} widgetConfig - Widget's propertys
	 */
	function buildSelectWidget(field, widgetConfig) {
		if (!field.selectWidget){
			field.selectWidget = metaDataFactory.widgetFactory({
				"id": 9000,
				"name": "Widget",
				"label": field.label,
				"template": widgetConfig.template,
				"container": field.widget.container,
				"isVisible": true,
				"popupNoBlock": false,
				"showDescriptionOrder": false,
				"floatingControl": {
					"openSearch": !Util.isMobile(),
					"defaultSearchField": field.defaultSearchField,
					"customizationAction": {
						"layout": false,
						"filter": false,
						"view": false
					},
					"xlsAction": false,
					"pdfAction": false,
					"csvAction": false
				},
				"parentProperty": field.parentProperty,
				"primaryProperty": field.primaryProperty,
				"expandingProperty": field.expandingProperty,
				"breadcrumbProperty": field.breadcrumbProperty,
				"order": field.order,
				"reverse": field.reverse,
				"bringGroupsCollapsed": field.bringGroupsCollapsed,
				"orderFromGroups": field.orderFromGroups,
				"selectField": field,
				"showCheckbox": false,
				"onAdd": field.onAdd,
				"showAddButtonAs": field.showAddButtonAs,
				"addButtonText": field.addButtonText,
				"noMatchText": field.noMatchText,
				"searchNoMatchText": field.searchNoMatchText,
				"fields": [],
				"dataSource": field.dataSource,
				"dataSourceFilter": field.dataSourceFilter,
				"itemsPerPage": field.itemsPerPage || 30,
				"searchWithPagination": Util.isUndefinedOrNull(field.searchWithPagination) ? true : field.searchWithPagination,
				"hasPagination": Util.isUndefinedOrNull(field.hasPagination) ? true : field.hasPagination,
				"onDemand": field.onDemand,
				"allowSelectOnlyLastChild": field.allowSelectOnlyLastChild,
				"actions": widgetConfig.actions,
				"events": EventChildPropagation.propagate(widgetConfig.widgetEvents, field),
				"searchCriteria": field.searchCriteria
			});
			buildSelectWidgetFields(field);
		}
		field.selectWidget.setSelected = setSelected;
		field.selectWidget.updateDatasource = updateDatasource;
		field.selectWidget.afterClosePopup = afterClosePopup;
	}

	/**
     * Reload the field
     *
     * @function processReload
     * @private
     * @param   {boolean} isToReload
     * @param   {Field} field
     * @returns {Promise} - A resolved promise
     */
	function processReload(isToReload, field){
		var defer = ZHPromise.defer();

		if (isToReload) {
			field.selectWidget.searchCriteria = {};
			field.reload().then(defer.resolve, defer.resolve);
		}
		else {
			defer.resolve();
		}

		return defer.promise;
	}

	/**
     * Change the dataSource's data to the filtered by the given value.
     *
     * @function innerSearch
     * @private
     * @param {String} selectListSearch - The value to be searched on the field.
     * @param {Field} field - The field where will be searched the values and changed the data of the dataSource.
     */
    function innerSearch(selectListSearch, field) {
        if (Util.isArray(field.filterDefinitions) && field.filterDefinitions.length) {
            var dataSource = field.dataSource;
            var filter = ZhUtil.buildFiltersInField(field, selectListSearch);
            dataSource.filter(filter, function(data) {
                dataSource.data = data;
                templateManager.updateTemplate();
            });
        }
    }

    /**
     * Removes all filters from the dataSource's data
     *
     * @function  cleanFieldSearch
     * @private
     * @param {Field} field - The field where will be removed the filters of the dataSource's data.
     */
    function cleanFieldSearch(field) {
        innerSearch("", field);
    }

	/**
     * Build the Widget to be open
     *
     * @function buildFieldWidget
     * @private
     * @param   {Field} field - Widget owner
     * @returns {Promise} - A resolved promise
     */
	function buildFieldWidget(field) {
		cleanFieldSearch(field);

		var isToReload = !!(field.selectWidget && Util.objectHasAnyValue(field.selectWidget.searchCriteria));

		if (!field.selectWidget) {
			initializeSelectWidget(field);
			isToReload = !!(field.dataSource && field.dataSource.lazyLoad);
		}

		return processReload(isToReload, field);
	}

	/**
     * Build configs to be used on the widget
     *
     * @function buildwidgetConfig
     * @private
     * @param   {Field} field - field used to build the configs
     * @returns {Object} Object with the configs
     */
	function buildWidgetConfig(field) {
		var widgetConfig = {};
		widgetConfig.template = configTemplate(field);
		widgetConfig.actions = buildActions(field);
		widgetConfig.widgetEvents = buildEvents();
		return widgetConfig;
	}

	/**
     * Initialize the field's SelectWidget propertys
     *
     * @function initializeSelectWidget
     * @private
     * @param   {Field} field - SelectWidget owner
     */
	function initializeSelectWidget(field) {
		buildSelectWidget(field, buildWidgetConfig(field));
		overrideReload(field);
	}

	var oldReload;
	function overrideReload(field) {
		oldReload = field.selectWidget.reload;
		field.selectWidget.reload = function() {
			if(dataInitialized){
				return oldReload.call(field.selectWidget);
			} else {
				return ZHPromise.when();
			}
		};
	}

	/**
	 * Call the beforeSelectOpen event if defined
	 *
	 * @function processBeforeSelectOpenEvent
	 * @private
	 * @param   {Field} field - Widget owner
	 */
	$scope.processBeforeSelectOpenEvent = function(field){
		if (field.beforeSelectOpen) {
			field.beforeSelectOpen();
		}
	};

	/**
     * Call the afterSelectOpen event if defined
     *
     * @function processAfterSelectOpenEvent
     * @param   {Field} field - Widget owner
     */
	$scope.processAfterSelectOpenEvent = function(field){
		if (field.afterSelectOpen) {
			field.afterSelectOpen();
		}
	};

	/**
     * Remove element Focus
     *
     * @function removeElementFocus
     * @private
     * @param   {Field} field - Field where the focus will be removed
     */
	function removeElementFocus(field) {
		if (field.element) {
			field.element.blur();
			window.getSelection().removeAllRanges(); //bug webkit
		}
	}

	/**
     * Open the popup of the field
     *
     * @function openField
     *
     * @param   {Field} field - Popup owner
     */
	$scope.openField = function(field, $event) {
        $event.stopPropagation();
        $scope.hideList();
		if (!field.isReadOnly()) {
			$scope.processBeforeSelectOpenEvent(field);
			removeElementFocus(field);
            $scope.inputWithEditableContent.click();
			buildFieldWidget(field).then(function() {
				$scope.clearSearchFilter();
				ScreenService.openPopup(field.selectWidget);
				$scope.processAfterSelectOpenEvent(field);
			});
		}
	};

	/**
	 * Open the popup of the field on the scope.
	 */
	var oldFieldOpenField = $scope.field.openField;
	$scope.field.openField = function() {
		$scope.openField($scope.field);
	};

    /**
     * Config the property valueField because the controller works only with valueFields.
     *
     * @function configValueFields
     * @private
     */
    function configValueFields(){
        var field = $scope.field;

        if(!field.valueFields){
            field.valueFields = [field.valueField];
        }
    }

    /**
     * Set the property descriptionField to avoid component break if not defined and config the property
     * descriptionField because the controller works only with descriptionFields.
     *
     * @function configDescriptionFields
     * @private
     */
    function configDescriptionFields() {
        var field = $scope.field;
        field.descriptionFields = field.descriptionFields ? field.descriptionFields : [field.descriptionField || field.name];
    }

	/**
	 * Clear the outdata related to the component.
	 */
	var oldFieldClearByOutData = $scope.field.clearByOutData;
	$scope.field.clearByOutData = (function() {
		for (var prop in this.outData) {
			this.widget.currentRow[this.outData[prop]] = "";
		}
	}).bind($scope.field);

	/**
	 * Clear the value of the selected row by the component from the current row.
	 *
	 * @function clearByName
	 * @public
	 */
	var oldFieldClearByName = $scope.field.clearByName;
	$scope.field.clearByName = (function () {
		this.widget.currentRow[this.name] = "";
	}).bind($scope.field);

	/**
	 * Clear the outDescription related to the component
	 *
	 * @function clearByOutDescription
	 * @public
	 */
	var oldFieldClearByOutDescription = $scope.field.clearByOutDescription;
	$scope.field.clearByOutDescription = (function() {
		this.widget.currentRow[this.outDescription] = "";
	}).bind($scope.field);

	if ($scope.selectField) {
		var oldSelectFieldOpenField = $scope.selectField.openField;
		$scope.selectField.openField = function() {
			$scope.openField($scope.selectField);
		};
	}

	/**
	 * Set the widget of the component as changed.
	 *
	 * @function setChanged
	 * @private
	 * @param {Field} field - The field to have it's current row set as changed.
	 */
	var setChanged = function(field) {
		field.widget.currentRow.changed = true;
	};

    /**
     * Clear the filter of the component, and the values related to him from the current row.
     *
     * @function clearField
     * @public
     * @param {Field} field - The field to have it's values cleared.
     */
    $scope.clearField = function(field, $event) {
        $event.stopPropagation();
        clearFieldValue(field);
	};

	$scope.hasValueSetted = function() {
		var field = $scope.field;
		var currentRow = field.widget.currentRow;
		return !!currentRow[field.name];
	};

    $scope.$watch($scope.hasValueSetted, function(newVal){
    	$scope.showSpan = newVal;
    });

    /**
     * Searches for the given value if the value is valid.
     *
     * @function search
     * @public
     * @param {String} selectListSearch - The value to be searched. Should be bigger than 0.
     * @param {Field} field - The field that will have the data that will be searched.
     */
    $rootScope.search = function(selectListSearch, field) {
        var minLength = field.minimumInputLength || 0;
        if (selectListSearch.length > minLength) {
            innerSearch(selectListSearch, field);
        }
    };

	/**
	 * Add the given item as selected
	 *
	 * @function addItemAsSelected
	 * @public
	 * @param {Object} item - The selected item.
	 * @param {Field} field - The item's field, where the item will be set as selected.
	 */
	this.addItemAsSelected = function(item, field) {
		(field.outData ? changeByOut : changeByName)(field, item);
		if(field.outDescription){
			changeByDescription(field, item);
		}
		item.__isSelected = true;
		field.dataSource.checkedRows = [item];
		setChanged(field);

		if (field._afterSelected) {
			field._afterSelected(item);
		}
		if (field.change) field.change();
	};

	/**
	 * Returns the mounted description of the selected value.
	 *
	 * @function getDescriptionFromSelectedValue
	 * @public
	 * @returns {String} The value that should be displayed of the selected row.
	 */
	$scope.getDescriptionFromSelectedValue = function() {
		var fieldName = $scope.field.name;
		var currentRow = $scope.field.widget.currentRow;

		return DecodeValueFieldService.model($scope.field, currentRow[fieldName]);
	};

	/**
	 * Returns the item's identifier.
	 *
	 * @function getItemIdentifier
	 * @public
	 * @param   {Object} item - The item to get it's identifier.
	 * @returns {String} The item's identifier.
	 */
	this.getItemIdentifier = function(item) {
		var identifier = [];
		var field = $scope.field;
		var valueFields = field.valueFields;

		for(var valueField in valueFields){
			identifier.push(item[valueFields[valueField]]);
		}

		return identifier.join(" | ");
	};

	/**
	 * Return the ordered data.
	 *
	 * @function orderDropdown
	 * @private
	 * @param {Object} data - The data to be ordered.
	 * @param {String} order - The selectWidget's order.
	 * @returns {Object} The ordered data.
	 */
	function orderDropdown(data, order){
		return $filter('orderBy')(data, order);
	}

    /**
     * Updates the dataSource, applying any typed filter.
     *
     * @function updateDatasourceRemote
     * @private
     * @param {DataSourceFilter} itemSearch - The field's input value, used to filter the dataSource.
     */
    function updateDatasourceRemote(itemSearch, isToReload){
        if($scope.field.dataSource.rest){
            if((itemSearch !== undefined) && itemSearch !== lastSearch || isToReload){
                if($scope.field.searchableFields){
					var filterSet = false;
					var fieldName = $scope.field.searchableFields.join("|");
					if(!$scope.filterToAdd){
						$scope.filterToAdd = {};
						filterSet = true;
					}
					$scope.filterToAdd.name = fieldName;
					$scope.filterToAdd.operator = "LIKE_ALL";
					$scope.filterToAdd.value = buildSearchValue(itemSearch);

					if(filterSet){
						$scope.field.dataSourceFilter = $scope.field.dataSourceFilter || [];
						$scope.field.dataSourceFilter.push($scope.filterToAdd);
					}
					isMakingRequest = true;
					if($scope.field.dataSource.disableLoader && $scope.showLoader){
						$scope.showLoader();
					}
					$scope.field.dataSource.load($scope.field.dataSourceFilter, 1, $scope.field.selectWidget.itemsPerPage).then(function(){
						if($scope.field.selectWidget.order){
							$scope.field.dataSource.data = orderDropdown($scope.field.dataSource.data, $scope.field.selectWidget.order);
							$scope.filteredData = $scope.field.dataSource.data;
						}

						if(shouldSelectAfterLoad && $scope.field.dataSource.data.length === 1){
							self.addItemAsSelected($scope.field.dataSource.data[0], $scope.field);
							shouldSelectAfterLoad = false;
						}

						isMakingRequest = false;
						if($scope.field.dataSource.disableLoader && $scope.hideLoader){
							$scope.hideLoader();
						}
					});
                    resetDropdown();
                } else {
                    throw new Error('Field ' + $scope.field.name + ' must have searchableFields. ');
                }
            }
        }
	}

	/**
	 * Deletes the filter add by searching in selectAutocomplete
	 */
	$scope.$on("$destroy", function(){
		if($scope.field.dataSourceFilter){
			$scope.field.dataSourceFilter.forEach(function(filter){
				if(angular.equals(filter, $scope.filterToAdd)){
					$scope.field.dataSourceFilter.splice($scope.field.dataSourceFilter.indexOf(filter), 1);
				}
			});
		}

		if ($scope.field.selectWidget) {
			if (oldReload) {
				$scope.field.selectWidget.reload = oldReload;
			}
		}

	    $scope.field.toggleDropdown = oldFieldToggleDropdown;
		$scope.field.initializeField = oldFieldInitializeField;
		$scope.field.value = oldFieldValue;
		$scope.field.openField = oldFieldOpenField;
		$scope.field.clearByOutData = oldFieldClearByOutData;
		$scope.field.clearByName = oldFieldClearByName;
		$scope.field.clearByOutDescription = oldFieldClearByOutDescription;
		$scope.field.reload = parentReload;
		if ($scope.selectField) {
			$scope.selectField.openField = oldSelectFieldOpenField;
		}
	});


    /**
     * Creates a filtered local copy of the Datasource.
     *
     * @function updateDatasourceLocal
     * @private
     * @param {DataSourceFilter} itemSearch - The field's input value, used to filter the dataSource copy.
     */
    function updateDatasourceLocal(itemSearch) {
        var expression = function(row) {
            var result = false;
            $scope.field.searchableFields.forEach(function(fieldName) {
                var rowValue = String(row[fieldName]).toLowerCase();
                switch ($scope.field.searchIn){
                    case 'beginning':
                        result = rowValue.startsWith(itemSearch.toLowerCase()) ? true : result;
                        break;
                    case 'end':
                        result = rowValue.endsWith(itemSearch.toLowerCase()) ? true : result;
                        break;
                    default:
                        result = rowValue.indexOf(itemSearch.toLowerCase()) !== -1 ? true : result;
                        break;
                }
            });

            return result;
        };

		$scope.filteredData = $filter('filter')($scope.field.dataSource.data, expression);
		if($scope.field.selectWidget.order){
			$scope.filteredData = orderDropdown($scope.filteredData, $scope.field.selectWidget.order);
		}
		resetDropdown();
		$timeout(function(){
            $scope.$digest();
        }, 0, false);
    }

    /**
     * Chose which method will be used to filter the data according with
     * the searchWithPagination property
     *
     * @function    updateDatasource
     * @private
     * @param {String} itemSearch - value used to filter the data
     */
    function updateDatasource(itemSearch, isToReload){
        itemSearch = itemSearch || $scope.itemSearch || "";

        if($scope.shouldMakeRequest(itemSearch, isToReload)){
            updateDatasourceRemote(itemSearch, isToReload);
        }
        else {
            updateDatasourceLocal(itemSearch);
        }

        lastSearch = itemSearch;
    }

	/**
     * Updates the dataSource and updates the selected item in dropdown.
     *
     * @function onItemSearchChanged
     * @private
     * @param {String} itemSearch - value used to filter the data
     */
	function onItemSearchChanged(itemSearch, isToReload){
		updateDatasource(itemSearch, isToReload);
		if($scope.updateDropdown){
			$scope.updateDropdown();
		}
	}

    /**
     * Verifies if the request should be made.
     *
     * @function shouldMakeRequest
     * @private
     * @param {String} itemSearch - value used to filter the data
     */
	$scope.shouldMakeRequest = function(itemSearch, isToReload) {
		/* Checks if datasource is currently making requests */
		if($scope.field.searchWithPagination && dataInitialized && !isMakingRequest){
			/* Checks if the request has already been made */
			if(itemSearch == lastSearch && !filterUpdated){
				return false;
			} else {
				filterUpdated = false;
				var itemsPerPage = $scope.field.itemsPerPage || 30;
				var pageSize = 30 < itemsPerPage ? 30 : itemsPerPage;

				/* Checks if the request is a more specific version of last request.
				 *  If so, checks if the last request has already found all the data.
 				 */
				return (!(itemSearch.length > 0 && itemSearch.indexOf(lastSearch) !== -1 && self.getData() && self.getData().length < pageSize) || isToReload);
			}
		} else {
			return false;
		}
    };

    updateDatasource();

    $scope.$watch('field.dataSourceFilter', propagateFieldDataSourceFilterToSelectWidget, true);
    $scope.$watch('itemSearch', Util.buildDebounceMethod(onItemSearchChanged, 500, false));

    /**
     * Propagate the field's DataSourceFilter to the selectWidget.
     *
     * @function propagateFieldDataSourceFilterToSelectWidget
     * @private
     * @param {DataSourceFilter} fieldDataSourceFilter - The field's DataSourceFilter to be propagated.
     */
    function propagateFieldDataSourceFilterToSelectWidget(fieldDataSourceFilter) {
		filterUpdated = true;
		if ($scope.field.selectWidget) {
            $scope.field.selectWidget.dataSourceFilter = fieldDataSourceFilter;
        }
		updateDatasource($scope.itemSearch, true);
    }

    /**
     * Defines the operator that will be used by filter.
     *
     * @function setOperator
     */
    function setOperator() {
        $scope.field.operator = $scope.field.operator || "=";
    }


    /**
     * Set hasPagination's default value if not set
     */
    function configHasPagination() {
        var field = $scope.field;
		if(field.hasPagination === undefined){
			field.hasPagination = true;
		}
    }

    /**
     * Initialize the field properties.
     *
     * @function initializeFieldProperties
     */
    function initializeFieldProperties() {
        configValueFields();
        configDescriptionFields();
        configHasPagination();
        buildPropertiesToSearch();
        setOperator();
        initializeSelectWidget($scope.field);
    }

    /**
     * Test the row to check if it should be displayed on the field's dropdown.
     *
     * @function searchFilter
     * @public
     * @param   {Object} row - The row to be tested.
     * @returns {Boolean} Result telling if the field should be displayed.
     */
    $scope.searchFilter = function(row) {
        return Util.isUndefinedOrNull($scope.itemSearch) || propertyToSearch.some(function(data) {
            return ~row[data].indexOfLatin($scope.itemSearch);
        });
    };

	/**
	 * Get the item with the given identifier.
	 *
	 * @function searchItem
	 * @private
	 * @param   {String} itemIdentifier - The desired item's identifier.
	 * @returns {Object} The item with the given identifier.
	 */
	function searchItem(itemIdentifier){
		var field = $scope.field;
		var data = field.dataSource.data;
		var valueFields = field.valueFields;
		var itemIdentifierArr = itemIdentifier.split(" | ");

		function findItem(item){
			var result = true;
			for(var valueField in valueFields){
				result &= item[valueFields[valueField]] == itemIdentifierArr[valueField];
			}
			return result;
		}

		item = data.find(findItem);
		return item;
	}

	/**
	 * Field's routine executed after one row be selected.
	 *
	 * @function onAutoCompleteItemSelect
	 * @public
	 * @param {String} selectedItemIdentifier - The identifier of the selected row.
	 */
	$scope.onAutoCompleteItemSelect = function(selectedItemIdentifier) {
		var item = searchItem(selectedItemIdentifier);
		self.clearEditableInput();

		if(!Util.isUndefinedOrNull(item))
			self.addItemAsSelected(item, $scope.field);

		$scope.removeFocusOfInput();
	};

	/**
	 * Set the item passed as clicked, clearing the component's last input and hiding it's list.
	 *
	 * @function onAutoCompleteItemClick
	 * @public
	 * @param {Object} clickedItem - The clicked item, with it's data.
	 */
	this.onAutoCompleteItemClick = function(clickedItem) {
		self.clearEditableInput();
		self.addItemAsSelected(clickedItem, $scope.field);
		$scope.removeFocusOfInput();
	};

	/**
	 * Tells if the given row is the last child.
	 *
	 * @function isLastChild
	 * @private
	 * @param   {Object} row - The row to be tested.
	 * @returns {Boolean} Result of the test of the row being the last child.
	 */
	var isLastChild = function(row) {
		return row._childCount === 0;
	};

	/**
	 * Change the values on the current row used to identify the selected row and, print the fields value on the screen.
	 *
	 * @function changeByName
	 * @private
	 * @param {Field} field - The field to have it's values changed.
	 * @param {Object} row - The selected row.
	 */
	function changeByName(field, row) {
		var valueFields = field.valueFields;
		var currentRow = field.widget.currentRow;
		var fieldName = field.name;
		currentRow[fieldName] = "";

		function buildCurrentRowValueFields(value, key){
			currentRow[value] = row[value];
		}

		valueFields.forEach(buildCurrentRowValueFields);
		currentRow[fieldName] = self.getItemDescription(row);
	}

	/**
	 * Change the given field current row value based on the configured outdata.
	 *
	 * @function changeByOut
	 * @private
	 * @param {Field} field - The field to have it's values changed.
	 * @param {Object} row - The selected row.
	 */
	function changeByOut(field, row) {
		if (field.widget.currentRow) {
			var outData = field.outData || {};

			Object.keys(outData).forEach(function(key) {
				if (Array.isArray(outData[key])) {
					outData[key].forEach(function(valueKey) {
						field.widget.currentRow[valueKey] = row[key];
					});
				}
				else {
					field.widget.currentRow[outData[key]] = row[key];
				}
			});
		}
	}

	/**
	 * Change the given field current row value based on the configured outDescription.
	 *
	 * @function changeByDescription
	 * @private
	 * @param {Field} field - The field to have it's values changed.
	 * @param {Object} row - The selected row.
	 */
	function changeByDescription(field, row) {
		field.widget.currentRow[field.outDescription] = self.getItemDescription(row);
	}

	/**
	 * Get the component's actual value, on it's placeholder, returning the fieldRow, if it is passed.
	 *
	 * @function getSelectionText
	 * @public
	 * @param {Object} [fieldRow] - The clicked item, with it's data.
	 */
	this.getSelectionText = function getSelectionText(fieldRow) {
		if ($scope.field.placeholder && !fieldRow) {
			selectionText = $scope.field.placeholder;
		}
		else {
			selectionText = fieldRow || ScreenService.i18n("Search");
		}
		return selectionText;
	};

	/**
	 * Open a new window.
	 *
	 * @param  {Field} field - The field with the container's name that will be opened.
	 */
	$scope.maintainWindow = function(field) {
		if (field.maintainWindow) {
			var maintainWindowFilter = SelectService.buildMaintainFilter(field);
			ScreenService.openWindow(field.maintainWindow, maintainWindowFilter);
		}
	};

	/**
	 * Build the value to be used as filter.
	 *
	 * @function buildSearchValue
	 * @private
	 * @param {String} itemSearch - The field's input value, used to build the filter.
	 */
	function buildSearchValue(itemSearch) {
		switch ($scope.field.searchIn){
			case 'beginning':
				return itemSearch + "%";
			case 'end':
				return "%" + itemSearch;
			default:
				return "%" + itemSearch + "%";
		}
	}

	/**
	 * Clear the variables related to the filter.
	 *
	 * @function clearSearchFilter
	 * @public
	 * @param   {Object} row - The row to be tested.
	 * @returns {Boolean} Result telling if the field should be displayed.
	 */
	$scope.clearSearchFilter = function(){
		$scope.itemSearch = '';
	};

	/**
	 * Insert into the propertysToSearch the fields that the user can search on the field's dropdown.
	 *
	 * @function addFieldNamesToPropertyToSearch
	 * @private
	 * @param {Array.<Fields>} fields - The fields that should be avaible to search.
	 */
	function addFieldNamesToPropertyToSearch(fields) {
		fields.forEach(function(field){
			propertyToSearch.push(field);
		});
	}

	/**
	 * Build the properties that the user can search on the field's  dropdown.
	 *
	 * @function buildPropertiesToSearch
	 * @private
	 */
	function buildPropertiesToSearch(){
		var field = $scope.field;
		$scope.field.searchWithPagination = Util.isUndefinedOrNull($scope.field.searchWithPagination) ? true : $scope.field.searchWithPagination;
		var propertyToAdd = null;
		if (!Util.isUndefined(field.searchableFields)) {
			propertyToAdd = (field.searchableFields);
		} else if (!Util.isUndefined(field.displayedFields)) {
			propertyToAdd = (field.displayedFields);
		} else {
			propertyToAdd = (field.descriptionFields);
		}

		addFieldNamesToPropertyToSearch(propertyToAdd);
	}

	/**
	 * Initilize the field.name value on it's currentRow.
	 *
	 * @function initializeValuesOnCurrentRow
	 * @private
	 */
	function initializeValuesOnCurrentRow() {
		var currentRow = $scope.field.widget.currentRow;
		var fieldName = $scope.field.name;
		currentRow[fieldName] = currentRow[fieldName] || "";
	}

	/**
	 * Remove the item from the current row by it's index.
	 *
	 * @function removeSelectedItemByIndex
	 * @private
	 */
	function removeSelectedItemByIndex(index) {
		var widgetParent = $scope.field.widget;
		var fieldName = $scope.field.name;
		widgetParent.currentRow[fieldName].splice(index, 1);
	}

	/**
	 * Functions used by template
	 **/

	/**
	 * Get the component's label.
	 *
	 * @function getFieldLabel
	 * @public
	 * @returns {String} The component's label.
	 */
	this.getFieldLabel = function() {
		return $scope.field.label;
	};

	/**
	 * Get the selected item's values.
	 *
	 * @function getSelectedValues
	 * @public
	 * @returns {Object} The values of the selected item.
	 */
	this.getSelectedValues = function() {
		return $scope.field.widget.currentRow[$scope.field.name];
	};

	/**
	 * Get the selected item's description field.
	 *
	 * @function getSelectedItemDescription
	 * @public
	 * @returns {String} The component's data.
	 */
	this.getSelectedItemDescription = function(item) {
		return item[$scope.field.descriptionField];
	};

	/**
	 * Get the component's data.
	 *
	 * @function getData
	 * @public
	 * @returns {Array<Object>} The component's data.
	 */
	this.getData = function() {
		return $scope.filteredData;
	};

	$scope.$watchCollection(function() {
		return self.getData();
	}, function(newValues, oldValues) {
		if (newValues !== oldValues) {
			if($scope.changeDropdownListPosition){
				$scope.changeDropdownListPosition();
			}
		}
	});

	$scope.dropDownLimit = 10;

	/**
	 * Updates the number of items in the dropdown.
	 *
	 * @function loadMore
	 * @public
	 */
	$scope.loadMore = function($event){
		if($scope.dropDownLimit === 20){
			$scope.dropDownLimit += 10;
			$scope.viewMore = ScreenService.i18n("Open search widget");
			$scope.openList();
			$event.stopPropagation();
		} else if($scope.dropDownLimit >= 30){
			$timeout(function(){$scope.inputWithEditableContent.blur();}, 0, false);
			resetDropdown();
			$scope.openField($scope.field, $event);
		} else {
			$scope.dropDownLimit += 10;
			$scope.openList();
			$event.stopPropagation();
		}
	};

	/**
	 * Set the dropdown to it's initial state.
	 *
	 * @function resetDropdown
	 * @private
	 */
	function resetDropdown() {
		$scope.dropDownLimit = 10;
		$scope.viewMore = ScreenService.i18n("View more...");
	}

	/**
	 * Verify if the LoadMore should be shown on dropDown.
	 *
	 * @function shouldShowLoadMore
	 * @public
	 */
	$scope.shouldShowLoadMore = function(){
		if($scope.field.hasPagination) {
			return $scope.filteredData.length >= $scope.dropDownLimit;
		} else {
			return false;
		}
	};

	/**
	 * Translate the text of the item according to field's option and i18n.
	 *
	 * @param  {Field}  field The field with the options.
	 * @param  {Object} item  The item With the text to be translated.
	 * @return {String}       The translated text.
	 */
	function translateText(field, item){
		if(!field.getTemplateSimpleName){
			Util.extend(field, new Templatable());
		}
		var text = item[field.name];
		return DecodeValueFieldService.model(field, text);
	}

	/**
	 * Returns the given item's text to be shown on the dropdown.
	 *
	 * @function getItemText
	 * @public
	 * @param   {Object} item - An item from the autocomplete field.
	 * @returns {String}        The item's description field if both have one, or an empty string.
	 */
	this.getItemText = function(item) {
		var itemDescription = [];
		var descriptionFields = $scope.field.descriptionFields;
		var displayedFields = $scope.field.displayedFields;
		var textSpacer = $scope.field.textSpacer || " | ";
		var fields = $scope.field.fields;

		function buildTranslatedItemDescription(description, key){
			var field = fields.filter(function(field){
				return field.name === description;
			})[0];
			itemDescription[key] = translateText(field, item);
		}

		function buildItemDescription(description, key){
			itemDescription[key] = item[description];
		}

		if(displayedFields){
			if(fields){
				displayedFields.forEach(buildTranslatedItemDescription);
			}
			else {
				displayedFields.forEach(buildItemDescription);
			}
		}
		else {
			if(fields){
				descriptionFields.forEach(buildTranslatedItemDescription);
			}
			else {
				descriptionFields.forEach(buildItemDescription);
			}
		}
		return itemDescription.join(textSpacer);
	};


	/**
	 * Returns the given item's descriptionField if the field has an descriptionField.
	 *
	 * @function getItemDescription
	 * @public
	 * @param   {Object} item - An item from the autocomplete field.
	 * @returns {String}        The item's description field if both have one, or an empty string.
	 */
	this.getItemDescription = function(item){
		var itemDescription = [];
		var descriptionFields = $scope.field.descriptionFields;
		var textSpacer = $scope.field.textSpacer || " | ";
		var fields = $scope.field.fields;

		function buildTranslatedItemDescription(description, key){
			var field = fields.filter(function(field){
				return field.name === description;
			})[0];
			itemDescription[key] = translateText(field, item);
		}

		function buildItemDescription(description, key){
			itemDescription[key] = item[description];
		}

		if(fields){
			descriptionFields.forEach(buildTranslatedItemDescription);
		}
		else {
			descriptionFields.forEach(buildItemDescription);
		}
		itemDescription = itemDescription.join(textSpacer);

		return itemDescription;
	};

	/**
	 * Remove the item with the given index from the parent field's current row.
	 *
	 * @function onRemoveSelectedItem
	 * @public
	 * @param {Integer} item - The index of the item to be removed.
	 */
	this.onRemoveSelectedItem = function(removedItemIndex) {
		removeSelectedItemByIndex(removedItemIndex);
	};

	/**
	 * Functions called from directive
	 *
	 **/

	/**
	 * Functions used internally
	 **/

	/**
	 * Clear the field's search parameter and it's list data.
	 *
	 * @function clearAutoCompleteListData
	 */
	$scope.clearAutoCompleteListData = function() {
		$scope.itemSearch = '';
		$scope.clearInputFieldText();
		$scope.autoCompleteListData = [];
		$scope.clearSelectedItem();
		$scope.showSpan = true;
	};

	/**
	 * Clear the value on the field's input.
	 *
	 * @function clearEditableInput
	 * @public
	 */
	this.clearEditableInput = function() {
		$scope.inputWithEditableContent.val('');
	};

	/**
	 * Tells if the component should display the span with the currentRow value.
	 *
	 * @function shouldShowSpan
	 * @public
	 * @returns {Boolean} Value telling if the span should be displayed.
	 */
	this.shouldShowSpan = function() {
		var isReadOnly = $scope.field.isReadOnly();
		return isReadOnly || $scope.showSpan;
	};
}
