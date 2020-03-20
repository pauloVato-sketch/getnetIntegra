// FILE: src/services/ZhFilterBuilder.js
/**
 * Service to create the filter conditions
 *
 * @class
 * @since 3.0.0
 *
 * @param {QueryBuilder} Query Service to build the query conditions
 */
function ZhFilterBuilder(Query) {

    var defaultOperators = {
        'field/between-dates.html': Query.BETWEEN,
        'field/calendar.html': Query.EQ,
        'field/date.html': Query.EQ,
        'field/datetime.html': Query.EQ,
        'field/interval.html': Query.BETWEEN,
        'field/number-edit.html': Query.EQ,
        'field/number-keyboard.html': Query.EQ,
        'field/number.html': Query.EQ,
        'field/radio.html': Query.EQ,
        'field/select.html': Query.EQ,
        'field/select-simple.html': Query.EQ,
        'field/select-native.html': Query.EQ,
        'field/select-multiple.html': Query.IN,
        'default': Query.LIKE_INSENSITIVE
    };

    var buildFilterByTemplate = {
        'field/select.html': buildSelectFilter,
        'field/select-native.html': buildSelectNativeFilter,
        'field/select-multiple.html': buildSelectMultipleFilter,
        'default': buildDefaultFilter
    };

    /**
     * Register the default operator for a field
     *
     * @since 3.0.0
     *
     * @param {String} template Template of the field
     * @param {String} operator Operator to use on template
     */
    this.setDefaultOperator = function(template, operator) {
        defaultOperators[template] = operator;
    };

    /**
     * Register the method used to create condition for a field
     *
     * @since 3.0.0
     *
     * @param {String}   template Template of the field
     * @param {Function} callback Method to be used to create the condition
     */
    this.setBuildFilterByTemplate = function(template, callback) {
        buildFilterByTemplate[template] = callback;
    };

    function getFieldTemplate(field) {
        return field.template.split('#').pop();
    }

    function getFilterBuilderByTemplate(template) {
        if (!buildFilterByTemplate[template]) {
            template = 'default';
        }

        return buildFilterByTemplate[template];
    }

    /**
     * Build filter conditions
     *
     * @since 3.0.0
     *
     * @param  {Field[]} fields List of fields to get the filter values
     * @param  {Object}  row    Row to get the values from
     *
     * @return {Object[]}       List of conditions
     */
    this.buildFilter = function (fields, row) {
        var query = Query.build();

        fields.forEach(function(field) {
            var template = getFieldTemplate(field);

            getFilterBuilderByTemplate(template)(query, field, row);
        });

        return query.where();
    };

    var operatorFlow = {
        "BETWEEN": function (query, value) {
            query.between([value.start, value.end]);
        },
        "NOT BETWEEN": function (query, value) {
            query.notBetween([value.start, value.end]);
        },
        "LIKE": function (query, value) {
            query.like('%' + value + '%');
        },
        "LIKE_I": function (query, value) {
            query.likeInsensitive('%' + value + '%');
        },
        "NOT LIKE": function (query, value) {
            query.notLike('%' + value + '%');
        },
        "LIKE ALL": function (query, value) {
            query.likeAll('%' + value + '%');
        },
        "<>": function (query, value) {
            query.notEquals(value);
        },
        "=": function (query, value) {
            query.equals(value);
        },
        ">": function (query, value) {
            query.greater(value);
        },
        "<": function (query, value) {
            query.lower(value);
        },
        ">=": function (query, value) {
            query.greaterOrEqual(value);
        },
        "<=": function (query, value) {
            query.lowerOrEqual(value);
        },
        "IN": function (query, value) {
            query.in(value);
        },
        "NOT IN": function (query, value) {
            query.notIn(value);
        }
    };

    function buildFilter(query, fieldName, operator, value) {
        query.where(fieldName);
        operatorFlow[operator](query, value);
    }

    function buildDefaultFilter(query, field, currentRow) {
        var fieldName = field.name;
        var value = currentRow[fieldName];

        if (value) {
            var operator = getFieldOperator(field);
            buildFilter(query, fieldName, operator, value);
        }
    }

    function buildSelectWithValueFieldFilter(query, field, currentRow, operator) {
        var fieldName = field.valueField;
        var value = currentRow[fieldName];
        if (value) {
            buildFilter(query, fieldName, operator, value);
        }
    }

    function buildSelectWithValueFieldsFilter(query, field, currentRow, operator) {
        var fieldName = field.name;
        var value = currentRow[fieldName];

        if (value) {
            buildFilter(query, fieldName, operator, value);
        }
    }

    function buildSelectWithOutDataFilter(query, field, currentRow, operator) {
        var outData = field.outData;
        Object.keys(outData).forEach(function(outDataKey) {
            var fieldName = outData[outDataKey];
            var value = currentRow[fieldName];
            if (value) {
                buildFilter(query, fieldName, operator, value);
            }
        });
    }

    function buildSelectFilter(query, field, currentRow) {
        var operator = getFieldOperator(field);
        if (field.valueField) {
            buildSelectWithValueFieldFilter(query, field, currentRow, operator);
        } else if (field.valueFields) {
            buildSelectWithValueFieldsFilter(query, field, currentRow, operator);
        } else if (field.outData) {
            buildSelectWithOutDataFilter(query, field, currentRow, operator);
        } else {
            throw new Error('Invalid select configuration on field ' + field.name);
        }
    }

    function buildSelectNativeWithValueFieldFilter(query, field, currentRow, operator) {
        var fieldName = field.valueField;
        var value = currentRow[fieldName];
        if (value) {
            buildFilter(query, fieldName, operator, value);
        }
    }

    function buildSelectNativeFilter(query, field, currentRow) {
        var operator = getFieldOperator(field);

        if (field.valueField) {
            buildSelectNativeWithValueFieldFilter(query, field, currentRow, operator);
        } else {
            throw new Error('Invalid select-native configuration on field ' + field.name);
        }

    }

    function buildSelectMultipleWithValueFieldFilter(query, field, currentRow, operator) {
        var fieldName = field.name;
        var value = currentRow[fieldName];

        if (value) {
            buildFilter(query, fieldName, operator, value);
        }
    }

    function buildSelectMultipleWithValueFieldsFilter(query, field, currentRow, operator) {
        throw new Error('Filter on field select-multiple with property valuesFields is not supported yet. ' +
            'Field "'+field.name+'"');
    }

    function buildSelectMultipleFilter(query, field, currentRow) {
        var operator = getFieldOperator(field);

        if (field.valueField) {
            buildSelectMultipleWithValueFieldFilter(query, field, currentRow, operator);
        } else if (field.valueFields) {
            buildSelectMultipleWithValueFieldsFilter(query, field, currentRow, operator);
        } else {
            throw new Error('Invalid select-multiple configuration on field ' + field.name);
        }
    }

    function getFieldOperator(field) {
        var template = getFieldTemplate(field);
        return field.operator || defaultOperators[template] || defaultOperators['default'];
    }

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterBuilder', ZhFilterBuilder);
});

// FILE: src/services/ZhFilterDescriptionBuilder.js
/**
 * Service to create the applied filter description
 *
 * @class
 * @since 3.0.0
 *
 * @param {DecodeValueFieldService} DecodeValueFieldService Service to retrieve field value
 */
function ZhFilterDescriptionBuilder(DecodeValueFieldService) {

    /**
     * Build filter description for a filter preference
     *
     * @since 3.0.0
     *
     * @param  {String} preferenceName Filter preference label
     *
     * @return {Object[]}              List containing the applied preference description
     */
    this.buildFilterAppliedInfo = function(preferenceName) {
        return [{
            'label': 'Filter',
            'value': preferenceName
        }];
    };

    /**
     * Build filter description for a list of filter conditions
     *
     * @since 3.0.0
     *
     * @param  {Field[]} fields         List of fields on the list widget
     * @param  {Object}  currentRow     Row of the applied filter
     * @param  {Object}  searchCriteria Search criteria currently applied
     *
     * @return {Object[]}               List of descriptions for the applied conditions
     */
    this.buildFilterInfo = function(fields, currentRow, searchCriteria) {
        var filterInfo = [];

        fields.forEach(function(field) {
            buildDefaultFilterInfo(filterInfo, field, currentRow);
        });

        buildFilterInfoForSearchCriteria(fields, filterInfo, searchCriteria);

        return filterInfo;
    };

    function buildFilterInfoForSearchCriteria(fields, filterInfo, searchCriteria) {
        if (searchCriteria) {
            Object.keys(searchCriteria).forEach(function(fieldName) {
                if (searchCriteria[fieldName] !== '') {
                    var fieldLabel;
                    if (fieldName === '_ALL') {
                        fieldLabel = 'All fields';
                    } else {
                        fieldLabel = getFieldByName(fields, fieldName).label;
                    }

                    filterInfo.push(buildFilterInfo(fieldLabel, searchCriteria[fieldName]));
                }
            });
        }
    }

    function getFieldByName(fields, fieldName) {
        return fields.filter(function(field) {
            return field.name === fieldName;
        }).shift();
    }

    function buildFilterInfo(label, value) {
        return {
            'label': label,
            'value': value
        };
    }

    function isConditionProvided(condition) {
        return condition !== undefined && condition !== null && condition !== '';
    }

    function buildDefaultFilterInfo(filterInfo, field, currentRow) {
        var condition = currentRow[field.name];
        if (isConditionProvided(condition)) {
            var value = DecodeValueFieldService.model(field, condition);
            filterInfo.push(buildFilterInfo(field.label, value));
        }
    }

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterDescriptionBuilder', ZhFilterDescriptionBuilder);
});

// FILE: src/services/ZhFilterPreferences.js
/**
 * A filter preference
 * @typedef {Object} Filter
 * @property {String} ID                 Filter unique identifier
 * @property {String} USER_ID            User identifier that created the filter
 * @property {String} ORGANIZATION_ID    Organization the user belongs to
 * @property {String} LABEL              Filter label
 * @property {String} WIDGET_ID          Widget identifier the filter belongs to
 * @property {String} FILTER_ROW         Row indicating the filter settings
 * @property {String} LAST_MODIFIED_DATE Last date the filter was modified
 */

/**
 * Service to handle filters storage
 *
 * @class
 * @since 3.0.0
 *
 * @param {ZhPreferences}              ZhPreferences              Service to handle preferences storage
 * @param {ZhFilterBuilder}            ZhFilterBuilder            Service to create filters conditions
 * @param {ZhFilterDescriptionBuilder} ZhFilterDescriptionBuilder Service to conditions description
 * @param {TemplateManager}            templateManager            Service to handle template
 * @param {EventAggregator}            eventAggregator            Service to use project events
 * @param {ZhUtil}                     ZhUtil                     Service to provice utilities methods
 * @param {EventEngine}                eventEngine                Service to use models events
 * @param {MetaDataFactory}            metaDataFactory            Service to factory models from metadata
 */
function ZhFilterPreferences(ZhPreferences, ZhFilterBuilder, ZhFilterDescriptionBuilder, templateManager, eventAggregator, ZhUtil, eventEngine, metaDataFactory) {

    var originalFilter = {
        'ID': 'ORIGINAL',
        'USER_ID': 0,
        'ORGANIZATION_ID': 0,
        'LABEL': 'Original'
    };

    function shouldShowFilterAction(widget) {
        return Util.get(widget, ['floatingControl', 'searchAction']) !== false ||
            Util.get(widget, ['floatingControl', 'customizationAction', 'filter']) !== false;
    }

    eventAggregator.subscribe('beforeProjectStartup', function() {
        ZhPreferences.registerRepository('zh-filter-preferences#filter');

        templateManager.project.customFloatingActions.push({
            'name': 'Filter Action',
            'label': 'Search',
            'type': 'filter',
            'order': 20,
            'icon': 'search',
            'template': 'zh-filter-preferences#floating-filter-card.html',
            'expandable': true,
            'isVisible': shouldShowFilterAction
        });
    });

    eventEngine.registerEvent('FilterBeforeApply', 'beforeFilterApply');
    eventEngine.registerEvent('FilterAfterApply', 'afterFilterApply');

    /**
     * Update dataSource with local saved filters
     *
     * @since 3.0.0
     *
     * @param  {String}     widgetID   Widget identifier
     * @param  {DataSource} dataSource DataSource that contains the loaded filters
     *
     * @return {Promise}               Promise resolved after the filters are loaded into the dataSource
     */
    this.updateFilters = function(widgetID, dataSource) {
        return this.getWidgetFilters(widgetID).then(function(filters) {
            return filters;
        }).then(function(filters) {
            return this.addIsDefaultFilterToFilters(filters, widgetID).then(function(filters) {
                dataSource.data = filters;
            });
        }.bind(this));
    };

    /**
     * Factory fields used on filter widget
     *
     * @since 3.0.0
     *
     * @param  {Widget}   parentWidget Widget to be the parent of the fields
     * @param  {Fields[]} gridFields   Fields from the widget to use the filter
     *
     * @return {Fields[]}              Factored fields
     */
    this.factoryFields = function(parentWidget, gridFields) {
        var fields = prepareFieldsToFormFilter(factoryFields(parentWidget, gridFields));
        var searchFields = prepareFieldsToFormFilter(factorySearchFields(parentWidget, gridFields));
        return fields.concat(searchFields);
    };

    /**
     * Add property to identify if the filter is selected as default
     *
     * @since 3.0.0
     *
     * @param {Filter[]} filters  List of filters to update the default filter property
     * @param {String}   widgetID Widget to get the default properties
     */
    this.addIsDefaultFilterToFilters = function(filters, widgetID) {
        return ZhPreferences.getDefaultWidgetPreferences(widgetID).then(function(defaultPreferences) {
            var defaultFilter = defaultPreferences.DEFAULT_FILTER;
            return filters.map(addIsDefaultFilterToFilter.bind(this, defaultFilter));
        });
    };

    function addIsDefaultFilterToFilter(defaultFilter, filter) {
        filter.DEFAULT_FILTER = filter.ID === defaultFilter;
        return filter;
    }

    /**
     * Load filters
     *
     * @since 3.0.0
     *
     * @param  {Query} query       Conditions to filter the preferences
     *
     * @return {Promise<Filter[]>} Loaded filters
     */
    this.loadFilters = function(query) {
        return ZhPreferences.loadPreferences('filter', query);
    };

    /**
     * Retrieve local cached filters for specified widget
     *
     * @since 3.0.0
     *
     * @param  {String} widgetID   Widget identifier
     *
     * @return {Promise<Filter[]>} Filter preferences
     */
    this.getWidgetFilters = function(widgetID) {
        return ZhPreferences.getWidgetPreferences('filter', widgetID);
    };

    /**
     * Retrieve local cached filters
     *
     * @since 3.0.0
     *
     * @param  {Query} query       Conditions to filter the preferences
     *
     * @return {Promise<Filter[]>} Filter preferences
     */
    this.getFilters = function(query) {
        return ZhPreferences.getPreferences('filter', query);
    };

    /**
     * Persist filters
     *
     * @since 3.0.0
     *
     * @param  {Filter[]} filters  Filters to persist
     *
     * @return {Promise<Filter[]>} List of persisted filters
     */
    this.saveFilters = function(filters) {
        return ZhPreferences.savePreferences('filter', filters);
    };

    /**
     * Delete filters
     *
     * @since 3.0.0
     *
     * @param  {Filter[]} filters  Filters to delete
     *
     * @return {Promise<Filter[]>} List of deleted filters
     */
    this.deleteFilters = function(filters) {
        return ZhPreferences.deletePreferences('filter', filters);
    };

    /**
     * Set a filter as the default preference
     *
     * @since 3.0.0
     *
     * @param  {String}  widgetID Widget unique identifier
     * @param  {Integer} filterID Filter unique identifier
     *
     * @return {Promise<Object>}  Widget default preferences
     */
    this.updateDefaultFilter = function(widgetID, filterID) {
        return ZhPreferences.updateDefaultWidgetPreferences('DEFAULT_FILTER', widgetID, filterID);
    };

    function applyDefaultValues(widget, filter) {
        filter.FILTER_ROW = '{}';
    }

    function getFilterConditions(filters) {
        return filters.filter(function(filter) {
            return filter.name.indexOf('SEARCHLIST_') === -1;
        }).map(function(filter) {
            filter.isCustomFilter = true;
            return filter;
        });
    }

    function getSearchConditions(filters) {
        var searchFields = Util.clone(filters);
        searchFields = searchFields.filter(function(filter) {
            return ~filter.name.indexOf('SEARCHLIST_');
        });

        return searchFields.map(function(filter) {
            filter.name = filter.name.replace('SEARCHLIST_', '');
            return filter;
        });
    }

    /**
     * Apply original filter
     *
     * @since 3.0.0
     *
     * @param  {Widget} widget Widget to apply the original filter on
     *
     * @return {Object[]}      Rows returned using the filter
     */
    this.applyOriginalFilter = function(widget) {
        applyDefaultValues(widget, originalFilter);
        return this.applyFilter(widget, originalFilter);
    };

    function triggerBeforeFilterApplyEvent(widget, preference, filters) {
        if (widget.beforeFilterApply) {
            widget.beforeFilterApply({
                'preference': preference,
                'filters': filters
            });
        }
    }

    function triggerAfterFilterApplyEvent(widget, preference, filters) {
        if (widget.afterFilterApply) {
            widget.afterFilterApply({
                'preference': preference,
                'filters': filters
            });
        }
    }

    /**
     * Apply filter
     *
     * @since 3.0.0
     *
     * @param  {Widget} widget     Widget to apply the original filter on
     * @param  {Filter} preference Preference to be applied on widget
     *
     * @return {Object[]}          Rows returned using the filter
     */
    this.applyFilter = function(widget, preference) {
        var filterRow = JSON.parse(preference.FILTER_ROW);

        var fields = this.factoryFields(null, widget.fields);
        var filters = ZhFilterBuilder.buildFilter(fields, filterRow);

        triggerBeforeFilterApplyEvent(widget, preference, filters);

        var filterConditions = getFilterConditions(filters);
        widget.dataSourceFilter = getOriginalDataSourceFilters(widget.dataSourceFilter)
            .concat(filterConditions);

        var searchConditions = getSearchConditions(filters);
        widget.setSearchCriteria(searchConditions);

        var searchCriteria = buildSearchCriteria(searchConditions);

        return widget.reload(ZhUtil.getLikeAllFilter(widget, searchCriteria)).then(function() {
            this.updateFilterInfo(widget, fields, filterRow, preference);
            widget.setAppliedFilter(preference);

            triggerAfterFilterApplyEvent(widget, preference, filters);
        }.bind(this));
    };

    /**
     * Update the applied filter information
     *
     * @since 3.0.0
     *
     * @param  {Widget}  widget         Widget to update the filter information
     * @param  {Field[]} fields         List of fields used to create the filter
     * @param  {Object}  filterRow      Row representing the filter configuration
     * @param  {Filter}  preference     Filter currently applied
     * @param  {Object}  searchCriteria Search criteria currently applied
     */
    this.updateFilterInfo = function(widget, fields, filterRow, preference, searchCriteria) {
        var filterInfo;
        if (preference.ID === 'ORIGINAL' || preference.ID === 'EDITED') {
            filterInfo = ZhFilterDescriptionBuilder.buildFilterInfo(fields, filterRow, searchCriteria);
        } else {
            filterInfo = ZhFilterDescriptionBuilder.buildFilterAppliedInfo(preference.LABEL);
        }

        widget._filterInfo = filterInfo;
    };

    function trimPercentageSymbol(value) {
        if (value.charAt(0) === '%') {
            value = value.substr(1);
        }

        if (value.charAt(value.length - 1) === '%') {
            value = value.substr(0, value.length - 1);
        }

        return value;
    }

    function buildSearchCriteria(conditions) {
        var searchCriteria = {};
        conditions.forEach(function(condition) {
            searchCriteria[condition.name] = trimPercentageSymbol(condition.value);
        });

        return searchCriteria;
    }

    function getOriginalDataSourceFilters(dataSourceFilter) {
        return dataSourceFilter.filter(function(filter) {
            return !filter.isCustomFilter;
        });
    }

    function removeConflictingProperties(field) {
        var conflictingProperties = ['selectWidget'];
        conflictingProperties.forEach(function(property) {
            delete field[property];
        });
    }

    function factoryFields(parentWidget, gridFields) {
        return gridFields.filter(function(field) {
            return !Util.isUndefined(field.template);
        }).map(function(field) {
            var factoredField = metaDataFactory.fieldFactory(field, parentWidget);
            removeConflictingProperties(factoredField);
            return factoredField;
        });
    }

    function factorySearchFields(parentWidget, gridFields) {
        var fields = [];
        fields.push(metaDataFactory.fieldFactory({
            'id': 'SEARCHLIST__ALL',
            'label': 'All Fields',
            'name': 'SEARCHLIST__ALL',
            'class': 12,
            'template': 'field/text-edit.html',
            'isVisible': true,
            'showOnList': true,
            'descending': true,
            'fieldGroup': 'COLUMNS_FILTER'
        }, parentWidget));

        return fields.concat(gridFields.filter(function(field) {
            return !field.skipSearch && Util.isUndefined(field.template);
        }).map(function(field) {
            var factoredField = metaDataFactory.fieldFactory(field);
            delete factoredField.$$hashKey;
            factoredField.id = 'SEARCHLIST_'+factoredField.id;
            factoredField.name = 'SEARCHLIST_'+factoredField.name;
            factoredField.class = 6;
            factoredField.isVisible = true;
            factoredField.readOnly = false;
            factoredField.validations = {};
            factoredField.template = 'field/text-edit.html';
            factoredField.fieldGroup = 'COLUMNS_FILTER';
            factoredField.widget = parentWidget;
            return factoredField;
        }));
    }

    function prepareFieldsToFormFilter(fields) {
        return fields.map(function(field) {
            return field.filterProperties ? mergeFieldNewProperties(field, field.filterProperties) : field;
        });
    }

    function mergeFieldNewProperties(field, filterProperties) {
        var keys = Object.keys(field);
        keys = keys.concat(
            Object.keys(filterProperties).filter(function(key) {
                return keys.indexOf(key) == -1;
            })
        );
        keys.forEach(function(property) {
            field[property] = filterProperties.hasOwnProperty(property) ? filterProperties[property] : field[property];
        });
        if (field.dataSource && !(field.dataSource instanceof DataSource)) {
            field.dataSource = metaDataFactory.factoryDataSource(field.dataSource, field.dataSourceFilter, field);
        }
        return field;
    }

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterPreferences', ZhFilterPreferences);
});

// FILE: src/controllers/ZhFilterWidgetsBuilder.js
/**
 * Service to create widgets to handle filters
 *
 * @class
 * @since 3.0.0
 *
 * @param {RestEngine}      restEngine      Service to make http requests
 * @param {TemplateManager} templateManager Service to handle templates
 * @param {MetaDataFactory} metaDataFactory Service to factory models from metadata
 */
function ZhFilterWidgetsBuilder(restEngine, templateManager, metaDataFactory) {

    function getWidgetJSON(widgetName) {
        var widgetPath = 'zh-filter-preferences#json/widgets/'+widgetName+'.json';
        return restEngine.requestMetaData(widgetPath).then(function(response) {
            return response.data;
        });
    }

    /**
     * Retrieve a factored widget
     *
     * @since 3.0.0
     *
     * @param  {String} widgetName Widget name
     *
     * @return {Widget}            Factored widget
     */
    this.factoryWidget = function(widgetName) {
        return getWidgetJSON(widgetName).then(function(widget) {
            return metaDataFactory.widgetFactory(widget, templateManager.container);
        });
    };

    /**
     * Retrieve factored filter configuration widget
     *
     * @since 3.0.0
     *
     * @return {Widget} Filter configuration widget
     */
    this.factoryFilterConfigurationWidget = function() {
        return this.factoryWidget('config');
    };

    /**
     * Retrieve factored create filter widget
     *
     * @since 3.0.0
     *
     * @return {Widget} Create filter widget
     */
    this.factoryCreateFilterWidget = function() {
        return this.factoryWidget('createFilter');
    };

    /**
     * Retrieve factored build filters conditions widget
     *
     * @since 3.0.0
     *
     * @return {Widget} Build filters conditions widget
     */
    this.factoryBuildFiltersConditionsWidget = function() {
        return this.factoryWidget('createFilterConditions');
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterWidgetsBuilder', ZhFilterWidgetsBuilder);
});

// FILE: src/controllers/ZhFilterCreateFilterConditionsController.js
/**
 * Create filter conditions widget controller
 *
 * @class
 * @since 3.0.0
 *
 * @param {ZhFilterPreferences}    ZhFilterPreferences    Service to handle filter preferences methods
 * @param {ZhFilterWidgetsBuilder} ZhFilterWidgetsBuilder Service to create widgets to handle filters
 * @param {MessageService}         MessageService         Service to show messages
 * @param {ScreenService}          ScreenService          Service to manipulate screen
 * @param {ZHPromise}              ZHPromise              Service to handle promises
 * @param {ZhUtil}                 ZhUtil                 Service that container utilities methods
 */
function ZhFilterCreateFilterConditionsController(ZhFilterPreferences, ZhFilterWidgetsBuilder, MessageService, ScreenService, ZHPromise, ZhUtil) {

    var createFilterConditionsWidgetPromise = null;

    /**
     * Retrieve widget to create filter conditions
     *
     * @since 3.0.0
     *
     * @return {Widget} Widget to create filter conditions
     */
    this.getCreateFilterConditionsWidget = function() {
        if (createFilterConditionsWidgetPromise === null) {
            createFilterConditionsWidgetPromise = ZhFilterWidgetsBuilder.factoryBuildFiltersConditionsWidget();
        }

        return createFilterConditionsWidgetPromise;
    };

    /**
     * Retrieve widget to create filter conditions with filter popup configuration
     *
     * @since 3.0.0
     *
     * @return {Widget} Widget to create filter conditions
     */
    this.getCreateFilterConditionsWidgetForFilterPopup = function() {
        return this.getCreateFilterConditionsWidget().then(function(createFilterConditionsWidget) {
            createFilterConditionsWidget.showAsModal = false;

            createFilterConditionsWidget.actions[0].isVisible = true;
            createFilterConditionsWidget.actions[1].isVisible = true;
            createFilterConditionsWidget.actions[2].isVisible = false;
            createFilterConditionsWidget.actions[3].isVisible = false;

            return createFilterConditionsWidget;
        });
    };


    /**
     * Retrieve widget to create filter conditions with edit preference configuration
     *
     * @since 3.0.0
     *
     * @return {Widget} Widget to create filter conditions
     */
    this.getCreateFilterConditionsWidgetForEditPreference = function() {
        return this.getCreateFilterConditionsWidget().then(function(createFilterConditionsWidget) {
            createFilterConditionsWidget.showAsModal = true;

            createFilterConditionsWidget.actions[0].isVisible = false;
            createFilterConditionsWidget.actions[1].isVisible = false;
            createFilterConditionsWidget.actions[2].isVisible = true;
            createFilterConditionsWidget.actions[3].isVisible = true;

            return createFilterConditionsWidget;
        });
    };

    /**
     * Save filter and apply
     *
     * @since 3.0.0
     *
     * @param  {Widget} createFilterConditionsWidget Widget that has the filter conditions to be saved
     *
     * @return {Promise}                             Promise resolved when saved filter is applied
     */
    this.saveAndApplyFilter = function(createFilterConditionsWidget) {
        if (createFilterConditionsWidget.isValid()) {
            var currentRow = createFilterConditionsWidget.currentRow;
            var filter = createFilterConditionsWidget.preference;

            filter.FILTER_ROW = JSON.stringify(ZhUtil.clearRow(currentRow));

            var configWidget = createFilterConditionsWidget.createNewFilterWidget.configWidget;
            var listWidget = configWidget.listWidget;

            return ZhFilterPreferences.saveFilters([filter]).then(function() {
                return ZhFilterPreferences.applyFilter(listWidget, filter);
            }).then(function() {
                ScreenService.closePopup(true);
                listWidget.activate();
                ZhFilterPreferences.updateFilters(listWidget.id, configWidget.dataSource);
            }).catch(function(error) {
                MessageService.showMessageByCode('zh-filter-preferences#save_error', {
                    'ERROR': error.message
                });
            });
        } else {
            return ZHPromise.when();
        }
    };

    /**
     * Apply filter
     *
     * @since 3.0.0
     *
     * @param  {Widget} createFilterConditionsWidget Widget that has the filter conditions to be saved
     *
     * @return {Promise}                             Promise resolved when saved filter is applied
     */
    this.applyFilter = function(createFilterConditionsWidget) {
        if (createFilterConditionsWidget.isValid()) {
            var currentRow = createFilterConditionsWidget.currentRow;

            var filter = {
                'ID': 'EDITED',
                'FILTER_ROW': JSON.stringify(ZhUtil.clearRow(currentRow))
            };

            var listWidget = createFilterConditionsWidget.listWidget;
            return ZhFilterPreferences.applyFilter(listWidget, filter).then(function() {
                ScreenService.closePopup();
            });
        } else {
            return ZHPromise.when();
        }
    };
}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterCreateFilterConditionsController', ZhFilterCreateFilterConditionsController);
});

// FILE: src/controllers/ZhFilterCreateFilterController.js
/**
 * Create new filter widget controller
 *
 * @class
 * @since 3.0.0
 *
 * @param {ZhPreferences}                            ZhPreferences                            Service to handle preferences persistance
 * @param {ZhFilterPreferences}                      ZhFilterPreferences                      Service to handle filter preferences methods
 * @param {ZhFilterWidgetsBuilder}                   ZhFilterWidgetsBuilder                   Service to create widgets to handle filters
 * @param {ZHPromise}                                ZHPromise                                Service to handle promises
 * @param {ScreenService}                            ScreenService                            Service to manipulate screen
 * @param {ZhFilterCreateFilterConditionsController} ZhFilterCreateFilterConditionsController Controller to manipulate create filter conditions widget
 */
function ZhFilterCreateFilterController(ZhPreferences, ZhFilterPreferences, ZhFilterWidgetsBuilder, ZHPromise, ScreenService, ZhFilterCreateFilterConditionsController) {

    var createFilterWidgetPromise = null;

    /**
     * Retrieve widget to create a new filter
     *
     * @since 3.0.0
     *
     * @return {Promise<Widget>} Widget to create a new filter
     */
    this.getCreateFilterWidget = function() {
        if (createFilterWidgetPromise === null) {
            createFilterWidgetPromise = ZhFilterWidgetsBuilder.factoryCreateFilterWidget();
        }

        return createFilterWidgetPromise;
    };

    /**
     * Open widget to create the filter conditions
     *
     * @since 3.0.0
     *
     * @param  {Widget} createNewFilterWidget Widget with the filter configuration
     *
     * @return {Promise}                     Promise resolved after the widget to create the layout properties is opened or if the layout configuration is invalid
     */
    this.proceedCreatingFilter = function(createNewFilterWidget) {
        if (createNewFilterWidget.isValid()) {
            return openCreateFilterConditionsWidget(createNewFilterWidget);
        } else {
            return ZHPromise.when();
        }
    };

    function openCreateFilterConditionsWidget(createNewFilterWidget) {
        var label = createNewFilterWidget.currentRow.LABEL;
        return ZhFilterCreateFilterConditionsController.getCreateFilterConditionsWidgetForEditPreference().then(function(createFilterConditionsWidget) {
            var configWidget = createNewFilterWidget.configWidget;
            var listWidget = configWidget.listWidget;

            createFilterConditionsWidget.label = ScreenService.i18n('Filter') + ' - ' + label;
            createFilterConditionsWidget.createNewFilterWidget = createNewFilterWidget;

            listWidget.fieldGroups = listWidget.fieldGroups || [];
            var fieldGroups = createFilterConditionsWidget.fieldGroups.concat(listWidget.fieldGroups);
            createFilterConditionsWidget.fieldGroups = fieldGroups;

            return ZhPreferences.getUserID().then(function(userID) {
                return {
                    '__is_new': true,
                    'ID': null,
                    'USER_ID': userID,
                    'LABEL': label,
                    'WIDGET_ID': listWidget.id,
                    'LAST_MODIFIED_DATE': null
                };
            }).then(function(preference) {
                createFilterConditionsWidget.preference = preference;

                createFilterConditionsWidget.fields = ZhFilterPreferences.factoryFields(createFilterConditionsWidget, listWidget.fields);

                createFilterConditionsWidget.newRow();
                return ScreenService.openPopup(createFilterConditionsWidget);
            });
        });
    }

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterCreateFilterController', ZhFilterCreateFilterController);
});

// FILE: src/controllers/ZhFilterConfigWidgetController.js
/**
 * Filter configuration widget controller
 *
 * @class
 * @since 3.0.0
 *
 * @param {ZhPreferences}                            ZhPreferences                            Service to handle preferences persistance
 * @param {ZhFilterPreferences}                      ZhFilterPreferences                      Service to handle filter preferences methods
 * @param {ZhFilterWidgetsBuilder}                   ZhFilterWidgetsBuilder                   Service to create widgets to handle filters
 * @param {ScreenService}                            ScreenService                            Service to manipulate screen
 * @param {MessageService}                           MessageService                           Service to show messages
 * @param {ZHPromise}                                ZHPromise                                Service to handle promises
 * @param {ZhFilterCreateFilterController}           ZhFilterCreateFilterController           Controller to manipulate create new filter widget
 * @param {ZhFilterCreateFilterConditionsController} ZhFilterCreateFilterConditionsController Controller to manipulate create filter conditions widget
 */
function ZhFilterConfigWidgetController(ZhPreferences, ZhFilterPreferences, ZhFilterWidgetsBuilder, ScreenService, MessageService, ZHPromise, ZhFilterCreateFilterController, ZhFilterCreateFilterConditionsController) {

    var configWidgetPromise = null;

    /**
     * Retrieve widget that handles filters configuration
     *
     * @since 3.0.0
     *
     * @param  {Widget}     listWidget Widget that is being used to create filters for
     * @param  {DataSource} dataSource DataSource to use on config widget
     *
     * @return {Promise<Widget>}       Widget that handles filters configuration
     */
    this.getConfigWidget = function(listWidget, dataSource) {
        if (configWidgetPromise === null) {
            configWidgetPromise = ZhFilterWidgetsBuilder.factoryFilterConfigurationWidget();
        }

        var userID = ZhPreferences.getUserID();

        return ZHPromise.all([userID, configWidgetPromise]).then(function(args) {
            var userID = args[0];
            var configWidget = args[1];

            configWidget.listWidget = listWidget;
            configWidget.dataSourceFilter = [
                {'name': 'WIDGET_ID', 'operator': '=', 'value': listWidget.id},
                {'name': 'USER_ID', 'operator': '=', 'value': userID}
            ];
            configWidget.dataSource = dataSource;

            return configWidget;
        });
    };

    /**
     * Open widget to create a new filter
     *
     * @since 3.0.0
     *
     * @param  {Widget} configWidget Widget that handles filters configuration
     *
     * @return {Promise}             Promise that is resolved when the widget is opened
     */
    this.openCreateFilterWidget = function(configWidget) {
        return ZhFilterCreateFilterController.getCreateFilterWidget().then(function(createNewFilterWidget) {
            createNewFilterWidget.newRow();
            createNewFilterWidget.configWidget = configWidget;
            return ScreenService.openPopup(createNewFilterWidget);
        });
    };

    /**
     * Open popup to edit a saved filter
     *
     * @since 3.0.0
     *
     * @param  {Filter} filter   Filter to edit
     *
     * @return {Promise<Widget>} Widget used to edit the filter
     */
    this.editFilter = function(configWidget, filter) {
        var listWidget = configWidget.listWidget;
        return ZhFilterCreateFilterConditionsController.getCreateFilterConditionsWidgetForEditPreference()
            .then(function(createFilterConditionsWidget) {
                createFilterConditionsWidget.preference = filter;
                createFilterConditionsWidget.label = ScreenService.i18n('Filter') + ' - ' + filter.LABEL;
                createFilterConditionsWidget.createNewFilterWidget = {
                    'configWidget': configWidget
                };

                var fields = ZhFilterPreferences.factoryFields(createFilterConditionsWidget, listWidget.fields);
                createFilterConditionsWidget.fields = fields;

                createFilterConditionsWidget.currentRow = JSON.parse(filter.FILTER_ROW);
                return ScreenService.openPopup(createFilterConditionsWidget);
            });
    };

    /**
     * Delete filters
     *
     * @since 3.0.0
     *
     * @param  {Widget} configWidget Widget that contains the filters to delete
     *
     * @return {Promise}             Promise resolved after filters are deleted
     */
    this.deleteFilters = function(configWidget) {
        var filtersToDelete = configWidget.getCheckedRows();

        return MessageService.confirmMessageByCode('zh-filter-preferences#delete', {
            'NR_OF_FILTERS': filtersToDelete.length
        }).then(function() {
            return ZhFilterPreferences.deleteFilters(filtersToDelete).then(function() {
                configWidget.dataSource.clearCheckedRows();
                ZhFilterPreferences.updateFilters(configWidget.listWidget.id, configWidget.dataSource);
            }).catch(function(error) {
                MessageService.showMessageByCode('zh-filter-preferences#delete_error', {
                    'ERROR': error.message
                });
            });
        });
    };

    /**
     * Set a filter as the default preference
     *
     * @since 3.0.0
     *
     * @param  {Widget} configWidget Widget that handles filters configuration
     * @param  {Filter} filter       Filter that it's default value has changed
     *
     * @return {Promise<Object>}     Widget default preferences
     */
    this.updateDefaultFilter = function(configWidget, filter) {
        var listWidget = configWidget.listWidget;

        var filterID = filter.DEFAULT_FILTER ? filter.ID : null;

        return ZhFilterPreferences.updateDefaultFilter(listWidget.id, filterID);
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhFilterConfigWidgetController', ZhFilterConfigWidgetController);
});

// FILE: src/controllers/ZhFilterFloatingCardController.js
/**
 * Floating window controller
 *
 * @class
 * @since 3.0.0
 *
 * @param {$scope}             $scope             Floating window scope
 * @param {ApplicationContext} ApplicationContext Service to retrieve Zeedhi services
 * @param {ZhKeyboardService}  ZhKeyboardService  Service to handle keyboard binds
 * @param {templateManager}    templateManager    Service to handle templates
 * @param {metaDataFactory}    metaDataFactory    Service to factory models from metadata
 * @param {ScreenService}      ScreenService      Service to handle screen
 * @param {Query}              Query              Service to build queries
 * @param {eventEngine}        eventEngine        Service to handle events
 */
function ZhFilterFloatingCardController($scope, ApplicationContext, ZhKeyboardService, templateManager, metaDataFactory, ScreenService, Query, eventEngine) {

    var widget = $scope.widget;
    $scope.appliedPreference = null;

    var ZhFilterPreferences = ApplicationContext.ZhFilterPreferences;
    var ZhPreferences = ApplicationContext.ZhPreferences;
    var ZhFilterConfigWidgetController = ApplicationContext.ZhFilterConfigWidgetController;
    var ZhFilterCreateFilterConditionsController = ApplicationContext.ZhFilterCreateFilterConditionsController;

    var dataSource = metaDataFactory.factoryDataSource({
        'name': 'zh-filter-preferences#/filter',
        'localStorage': false,
        'lazyLoad': true,
        'rest': true,
        'data': []
    });

    dataSource.afterLoadDataSource(function() {
        return ZhFilterPreferences.addIsDefaultFilterToFilters(dataSource.data, widget.id).then(function(filters) {
            dataSource.data = filters;
        });
    });

    $scope.customizationCard = {
        'label': 'Filters',
        'type': 'filter',
        'openFilterConfiguration': openFilterConfiguration,
        'refreshFilters': refreshFilters,
        'openFilterWidget': openFilterWidget,
        'applyOriginalFilter': applyOriginalFilter,
        'applyFilter': applyPreference,
        'dataSource': dataSource
    };

    widget.setAppliedFilter = setAppliedFilter;
    widget.openFilterWidget = openFilterWidget;

    function setAppliedFilter(filter) {
        $scope.appliedPreference = filter;
    }

    function updateFilterRow(filterRow, searchCriteria) {
        var row = JSON.parse(filterRow);
        Object.keys(row).forEach(function(key) {
            if (key.indexOf('SEARCHLIST_') === 0) {
                delete row[key];
            }
        });

        Object.keys(searchCriteria).forEach(function(key) {
            row['SEARCHLIST_' + key] = searchCriteria[key];
        });

        return JSON.stringify(row);
    }

    $scope.$watchCollection('widget.searchCriteria', function(searchCriteria) {
        var filterRow = $scope.appliedPreference && $scope.appliedPreference.FILTER_ROW ?
            updateFilterRow($scope.appliedPreference.FILTER_ROW, searchCriteria || {}) : '{}';

        $scope.appliedPreference = {
            ID: 'EDITED',
            FILTER_ROW: filterRow
        };

        ZhFilterPreferences.updateFilterInfo(widget, widget.fields, JSON.parse(filterRow), $scope.appliedPreference, searchCriteria);
    });

    function openFilterConfiguration() {
        var dataSource = $scope.customizationCard.dataSource;
        return ZhFilterConfigWidgetController.getConfigWidget(widget, dataSource).then(function(configWidget) {
            return ScreenService.openPopup(configWidget);
        });
    }

    function refreshFilters() {
        return ZhPreferences.getUserID().then(function(userId) {
            var query = Query.build()
                .where('WIDGET_ID').equals(widget.id)
                .where('USER_ID').equals(userId);

            return dataSource.load(query.where());
        });
    }

    function openFilterWidget() {
        return ZhFilterCreateFilterConditionsController.getCreateFilterConditionsWidgetForFilterPopup().then(function(createFilterConditionsWidget) {
            createFilterConditionsWidget.listWidget = widget;
            createFilterConditionsWidget.label = 'Filter';

            var fields = ZhFilterPreferences.factoryFields(createFilterConditionsWidget, widget.fields);
            createFilterConditionsWidget.fields = fields;

            if ($scope.appliedPreference) {
                createFilterConditionsWidget.currentRow = JSON.parse($scope.appliedPreference.FILTER_ROW);
            }

            ScreenService.openPopup(createFilterConditionsWidget);
        });
    }

    function applyOriginalFilter() {
        return ZhFilterPreferences.applyOriginalFilter(widget);
    }

    function applyPreference(filter) {
        return ZhFilterPreferences.applyFilter(widget, filter);
    }

    function isFilterPreferencesActive() {
        return Util.get(widget, ['floatingControl', 'customizationAction', 'filter']) !== false;
    }

    function getDefaultFilter() {
        return dataSource.data.filter(function(filter) {
            return filter.DEFAULT_FILTER;
        }).shift();
    }

    function applyDefaultFilter() {
        var filter = getDefaultFilter();

        if (filter) {
            applyPreference(filter);
        }
    }

    function isMobiScrollOpen() {
        return $('.mbsc-mobiscroll').length > 0;
    }

    function hasPopupOpened() {
        return !ScreenService.isSideMenuOpen() && !isMobiScrollOpen() && !ScreenService.isAlertShown();
    }

    function openWidgetFilter(event, key) {
        if (hasPopupOpened() && isFilterPreferencesActive()) {
            openFilterWidget();
        }

        return false;
    }

    function openWidgetSearch(event, key) {
        if (hasPopupOpened()) {
            var scope = $('.zh-floating-control:last').scope();
            scope.openSearchAction();
        }

        return false;
    }

    function bindKeyboardEvents() {
        ZhKeyboardService.unbind(templateManager.project.shortcutKeys.openWidgetFilter || 'mod+l');
        ZhKeyboardService.unbind(templateManager.project.shortcutKeys.openWidgetSearch || 'mod+f');

        ZhKeyboardService.bind(templateManager.project.shortcutKeys.openWidgetFilter || 'mod+l', openWidgetFilter);
        ZhKeyboardService.bind(templateManager.project.shortcutKeys.openWidgetSearch || 'mod+f', openWidgetSearch);
    }
    bindKeyboardEvents();
    eventEngine.bindEvent(widget, 'WidgetOnActivate', bindKeyboardEvents);

    (function initFilterPreferences() {
        if (isFilterPreferencesActive()) {
            if (!widget.id) {
                throw new Error('Widget\'s must have "id" to use Preferences. '+
                    'Widget with name "'+widget.name+'" does not have an id.');
            }

            ZhFilterPreferences.updateFilters(widget.id, $scope.customizationCard.dataSource).then(function() {
                applyDefaultFilter();
            });
        }
    })();

}

// FILE: src/directives/ZhFilterFloatingControl.js
var ZeedhiDirectives = angular.module('ZeedhiDirectives');

ZeedhiDirectives.directive('zhFloatingControl', ['$timeout', ZhFilterFloatingControl]);

function ZhFilterFloatingControl($timeout) {

    function checkFloatingCardOverflow(e) {
        var actionElement = $(e.target).closest('.float-action');
        var floatingControl = actionElement.parents('.zh-floating-control');
        var widgetContainer = actionElement.closest('.container');
        var actionTop, actionBottom;
        var widgetTop, widgetBottom;

        if (!actionElement.hasClass('opened')) {
            actionElement.removeClass('fix-position-top');
            actionElement.removeClass('fix-position-bottom');

            actionTop = actionElement.offset().top;
            widgetTop = widgetContainer.offset().top;
            widgetBottom = widgetTop + widgetContainer.height();
            if (floatingControl.hasClass('pos-top')) {
                actionTop = actionTop - 200 + actionElement.height();
                if (actionTop < widgetTop) {
                    actionElement.addClass('fix-position-top');
                }
            } else {
                actionBottom = actionTop + 200;
                if (actionBottom > widgetBottom) {
                    actionElement.addClass('fix-position-bottom');
                }
            }
        }
    }

    return {
        priority: 10,
        link: function($scope, element, attrs, controller) {
            var widget = $scope.widget;
            if (widget.floatingControl === false) {
                return;
            }

            var defaultOptions = {
                searchAction: Util.isUndefined(element.parent().attr('data-hide-search')) && Util.isUndefined(element.parent().attr('hide-search')),
                openSearch: false
            };
            widget.floatingControl = Util.extend(defaultOptions, widget.floatingControl);

            widget.floatingControl.addStatus({
                priority: 20,
                classString: 'searching',
                icon: 'search',
                conditionFunction: function() {
                    return widget._filterInfo && widget._filterInfo.length > 0;
                },
                infoTemplate: function() {
                    if (widget.floatingControl.customizationAction && widget.floatingControl.customizationAction.filter) {
                        icon = 'filter';
                        method = 'openFilter';
                    } else {
                        icon = 'close-x';
                        method = 'clearFilter';
                    }

                    return '<div class="searching-control-info"><span data-zh-icon="'+icon+' no-border icon-white" data-zh-touchstart="'+method+'()"></span><div class="searching-control-info-details"><span ng-repeat="info in widget._filterInfo"><b>{{info.label|i18n}}:</b> {{info.value}}</span></div></div><div class="searching-control-info-text" data-zh-bind-translate="\'Filter applied\'"></div>';
                },
                scope: $scope
            });

            $scope.openSearchAction = function() {
                $timeout.cancel($scope._closeTimeout);
                if (!element.hasClass('opened')) {
                    $scope.toggleActionMenu();
                }
                $scope.closeOpenedActions();
                var searchAction = element.find('.search-action').closest('.float-action');
                if (searchAction && searchAction.length > 0) {
                    searchAction.find("span.search-action-icon").click();
                    Util.selectElement(searchAction.find('input'));
                }
            };

            $scope.openFilter = function() {
                $scope.widget.openFilterWidget();
            };

            $scope.clearFilter = function() {
                $scope.widget.searchCriteria = {};
            };

            $scope.togglePreferencesCard = function(e) {
                checkFloatingCardOverflow(e);
                $scope.toggleAction(e);
            };

            $scope.clearSearch = function(event, fieldName) {
                if (!fieldName) {
                    widget.searchCriteria = {};
                } else {
                    delete widget.searchCriteria[fieldName];
                }

                if (event) {
                    Util.selectElement($(event.target).closest(".floating-card-input").find("input"));
                }
            };

            $scope.toggleSearchFieldsList = function(event) {
                if (!widget.floatingControl.customizationAction || !widget.floatingControl.customizationAction.filter) {
                    var el = $(event.target).closest(".floating-card").find('.floating-card-search-field-select');
                    el.toggleClass("opened");

                    if (el.hasClass("opened")) {
                        $(event.target).closest(".floating-card").find('.floating-card-select-field-options').removeClass("opened");
                        $(event.target).closest(".floating-card").find('.zh-select-search-floating').removeClass("opened");
                    }
                }
            };

            $scope.selectSearchField = function(field) {
                $scope.searchField = field;
                element.find('.floating-card-search-field-select').removeClass('opened');
                element.find('input.zh-input-search-floating').focus();
            };
        }
    };

}

// FILE: src/Config.js
(function() {

window.templateUrls = window.templateUrls || [];
window.metadataUrls = window.metadataUrls || [];
window.serviceUrls = window.serviceUrls || [];

var PACKAGE_NAME = 'zh-filter-preferences';

var hasTemplateUrl = Boolean(window.templateUrls.filter(function(value) {
    return value.package === PACKAGE_NAME;
}).shift());

var hasMetadataUrl = Boolean(window.metadataUrls.filter(function(value) {
    return value.package === PACKAGE_NAME;
}).shift());

var hasServiceUrl = Boolean(window.serviceUrls.filter(function(value) {
    return value.package === PACKAGE_NAME;
}).shift());

if (!hasTemplateUrl) {
    window.templateUrls.push({
        package: PACKAGE_NAME,
        baseUrl: 'bower_components/zh-filter-preferences/dist/templates/'
    });
}

if (!hasMetadataUrl) {
    window.metadataUrls.push({
        package: PACKAGE_NAME,
        baseUrl: 'bower_components/zh-filter-preferences/dist/assets/'
    });
}

if (!hasServiceUrl) {
    window.serviceUrls.push({
        package: PACKAGE_NAME,
        baseUrl: '../backend/service/index.php/zh-preferences'
    });
}

})();
