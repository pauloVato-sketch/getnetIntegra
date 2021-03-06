// FILE: src/ZhPreferences.js
// FILE: src/ZhPreferences.js
/**
 * Service to handle preferences storage
 *
 * @class
 * @param {RepositoryFactory} RepositoryFactory Service to factory repositories
 * @param {RequestEngine}     requestEngine     Service to make http requests
 * @param {RequestFactory}    requestFactory    Service to factory requests
 * @param {ZHPromise}         ZHPromise         Service to manipulate promises
 * @param {Query}             Query             Service to build queries
 */
function ZhPreferences(RepositoryFactory, requestEngine, requestFactory, ZHPromise, Query, $q) {

    var repositoryMap = {};

    var userWidgetConfiguration = {};

    /**
     * Register new repository to save preferences
     *
     * @param  {String} name Repository name used to save a preference
     */
    this.registerRepository = function(name) {
        var repositoryName = name.split('#').pop();
        repositoryMap[repositoryName] = RepositoryFactory.factory(name, 'MEMORY');
    };

    /**
     * Load all user preferences from all repositories and save them on the repositories
     *
     * @param  {String} userID Identifier of user to load it's preferences
     *
     * @return {Promise}       User preferences from all repositories
     */
    this.loadUserPreferences = function(userID) {
        return requestEngine.doRequest(requestFactory.factory({
            'serviceName': 'zh-preferences#/preferences',
            'requestType': 'filterData',
            'filter': [{'name': 'USER_ID', 'operator': '=', 'value': userID}]
        })).then(null, function(response) {
            throw new Error(response.data.error);
        }).then(function(response) {
            var promises = [];
            Object.keys(repositoryMap).forEach(function(repositoryName) {
                var dataSetName = repositoryName.split('/').pop();
                promises.push(repositoryMap[repositoryName].save(response.dataset[dataSetName]));
            });

            return ZHPromise.all(promises);
        });
    };

    /**
     * Load preferences using a specified filter and save them on the repository
     *
     * @param  {String} preferenceType Repository name
     * @param  {Query}  query          Conditions to filter the preferences
     *
     * @return {Promise}               Preferences array
     */
    this.loadPreferences = function(preferenceType, query) {
        return requestEngine.doRequest(requestFactory.factory({
            'serviceName': 'zh-preferences#/' + preferenceType,
            'requestType': 'filterData',
            'filter': query.where()
        })).then(null, function(response) {
            throw new Error(response.data.error);
        }).then(function(response) {
            return response.dataset[preferenceType];
        }).then(function(preferences) {
            return ZHPromise.all([preferences, repositoryMap[preferenceType].remove(query)]);
        }).then(function(args) {
            var preferences = args[0];
            var deleteResponse = args[1];

            return repositoryMap[preferenceType].save(preferences).then(function() {
                return preferences;
            });
        });
    };

    /**
     * Persist preferences
     *
     * @param  {String}   preferenceType Repository name
     * @param  {Object[]} preferences    Array of preferences to persist
     *
     * @return {Promise}                 Preferences saved
     */
    this.savePreferences = function(preferenceType, preferences) {
        return requestEngine.doRequest(requestFactory.factory({
            'serviceName': 'zh-preferences#/' + preferenceType + '/save',
            'requestType': 'dataSource',
            'dataSource': preferences
        })).then(null, function(response) {
            throw new Error(response.data.error);
        }).then(function(response) {
            var returnedIds = response.dataset[preferenceType];
            return preferences.map(function(preference, index) {
                preference.__is_new = false;
                preference.changed = false;
                preference.ID = returnedIds[index].ID;
                return preference;
            });
        }).then(function(preferences) {
            return repositoryMap[preferenceType].save(preferences);
        });
    };

    /**
     * Delete preferences
     *
     * @param  {String}   preferenceType Repository name
     * @param  {Object[]} preferences    Array of preferences to delete
     *
     * @return {Promise}                 Preferences deleted
     */
    this.deletePreferences = function(preferenceType, preferences) {
        return requestEngine.doRequest(requestFactory.factory({
            'serviceName': 'zh-preferences#/' + preferenceType + '/remove',
            'requestType': 'dataSource',
            'dataSource': preferences
        })).then(null, function(response) {
            throw new Error(response.data.error);
        }).then(function(response) {
            var deletedIds = response.dataset[preferenceType].map(function(preference) {
                return preference.ID;
            });
            var query = Query.build()
                .where('ID').in(deletedIds);

            return repositoryMap[preferenceType].remove(query);
        });
    };

    /**
     * Get local saved preferences from repository
     *
     * @param  {String} preferenceType Repository name
     * @param  {Query}  query          Conditions to filter the preferences
     *
     * @return {Promise}               Array of preferences
     */
    this.getPreferences = function(preferenceType, query) {
        return repositoryMap[preferenceType].find(query);
    };

    /**
     * Get local saved preferences from repository filtering by widget and logged user
     *
     * @param  {String} preferenceType Repository name
     * @param  {String} widgetID       Widget identified
     *
     * @return {Promise}               Array of preferences
     */
    this.getWidgetPreferences = function(preferenceType, widgetID) {
        return this.getUserID().then(function(userID) {
            return Query.build()
                .where('USER_ID').equals(userID)
                .where('WIDGET_ID').equals(widgetID);
        }).then(function(query) {
            return this.getPreferences(preferenceType, query);
        }.bind(this));
    };

    /**
     * Get default preferences for user on specified widget
     *
     * @param  {String} widgetID Widget to get default preferences
     *
     * @return {Promise}         Widget default preferences
     */
    this.getDefaultWidgetPreferences = function(widgetID) {
        if (userWidgetConfiguration[widgetID] === undefined) {
            userWidgetConfiguration[widgetID] = this.getUserID().then(function(userID) {
                var query = Query.build()
                    .where('USER_ID').equals(userID)
                    .where('WIDGET_ID').equals(widgetID);

                return ZHPromise.all([
                    userID,
                    requestEngine.doRequest(requestFactory.factory({
                        'serviceName': 'zh-preferences#/default-preferences',
                        'requestType': 'filterData',
                        'filterData': query.where()
                    }))
                ]);
            }).then(function(args) {
                var userID = args[0];
                var response = args[1];
                return response.dataset.user_widget.pop() || {
                    '__is_new': true,
                    'ID': null,
                    'USER_ID': userID,
                    'ORGANIZATION_ID': null,
                    'WIDGET_ID': widgetID,
                    'DEFAULT_FILTER': null,
                    'DEFAULT_LAYOUT': null,
                    'DEFAULT_VIEW': null,
                    'LAST_MODIFIED_DATE': null
                };
            });
        }

        return userWidgetConfiguration[widgetID];
    };

    /**
     * Change default preference for user on specified widget
     *
     * @param  {String}  property            Property that contains the default preference ID
     * @param  {String}  widgetID            Widget to set the default preference
     * @param  {Integer} defaultPreferenceID Default preference identifier
     *
     * @return {Promise}                     Widget default preferences
     */
    this.updateDefaultWidgetPreferences = function(property, widgetID, defaultPreferenceID) {
        userWidgetConfiguration[widgetID] = this.getDefaultWidgetPreferences(widgetID).then(function(widgetDefaultPreferences) {
            widgetDefaultPreferences[property] = defaultPreferenceID;
            return ZHPromise.all([
                widgetDefaultPreferences,
                requestEngine.doRequest(requestFactory.factory({
                    'serviceName': 'zh-preferences#/default-preferences/save',
                    'requestType': 'dataSource',
                    'dataSource': [widgetDefaultPreferences]
                }))
            ]);
        }).then(function(args) {
            var widgetDefaultPreferences = args[0];
            var response = args[1];
            var returnedId = response.dataset.user_widget.pop();
            widgetDefaultPreferences.__is_new = false;
            widgetDefaultPreferences.ID = returnedId.ID;
            return widgetDefaultPreferences;
        });

        return userWidgetConfiguration[widgetID];
    };

    var userPromise = null;

    /**
     * Invalidate logged user and clear preferences loaded
     */
    this.unsetUser = function() {
        userPromise = null;
        userWidgetConfiguration = {};

        Object.keys(repositoryMap).forEach(function(repositoryName) {
            repositoryMap[repositoryName].clearAll();
        });
    };

    /**
     * Set logged user
     *
     * @param {String} userID Logged user
     */
    this.setUserID = function(userID) {
        if (userPromise === null) {
            userPromise = ZHPromise.defer();
        }

        this.loadUserPreferences(userID).then(function() {
            userPromise.resolve(userID);
        });
    };

    /**
     * Get logged user ID
     *
     * @return {Promise} User identifier
     */
    this.getUserID = function() {
        if (userPromise === null) {
            userPromise = ZHPromise.defer();
        }

        return userPromise.promise;
    };

}

Configuration(function(ContextRegister) {
    ContextRegister.register('ZhPreferences', ZhPreferences);
});

// FILE: src/Config.js
(function(){

window.metaDataUrls = window.metaDataUrls || [];
window.serviceUrls = window.serviceUrls || [];

var PACKAGE_NAME = 'zh-preferences';

var hasMetaData = window.metaDataUrls.some(function(metaDataUrl){
	return metaDataUrl.package === PACKAGE_NAME;
});

var hasServiceUrl = window.metaDataUrls.some(function(metaDataUrl){
    return metaDataUrl.package === PACKAGE_NAME;
});

if (!hasMetaData) {
    window.metaDataUrls.push({
        package: PACKAGE_NAME,
        baseUrl: 'bower_components/zh-preferences/dist/assets/'
    });
}

if (!hasServiceUrl) {
    window.serviceUrls.push({
        package: PACKAGE_NAME,
        baseUrl: '../backend/service/index.php/zh-preferences'
    });
}

})();
