describe('ZhPreferences', function() {

    var repositoryFactory;
    var requestEngine;
    var requestFactory;
    var zhPromise;
    var query;
    var zhPreferences;

    beforeEach(function() {
        zhPromise = Q;
        repositoryFactory = jasmine.createSpyObj('repositoryFactory', ['factory']);
        requestEngine = jasmine.createSpyObj('requestEngine', ['doRequest']);
        requestFactory = jasmine.createSpyObj('requestFactory', ['factory']);
        query = jasmine.createSpyObj('query', ['build']);
        zhPreferences = new ZhPreferences(repositoryFactory, requestEngine, requestFactory, zhPromise, query);
    });

    it('registerRepository', function() {
        zhPreferences.registerRepository('repository');

        expect(repositoryFactory.factory).toHaveBeenCalledWith('repository', 'MEMORY');
    });

    it('loadUserPreferences', function(done) {
        var userID = 'user-1';

        var filterRepository = jasmine.createSpyObj('filter', ['save']);
        var layoutRepository = jasmine.createSpyObj('layout', ['save']);

        filterRepository.save.and.callFake(function() {
            return Q.when('filterReturn');
        });

        layoutRepository.save.and.callFake(function() {
            return Q.when('layoutReturn');
        });

        var repositories = {
            'filter': filterRepository,
            'layout': layoutRepository
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/preferences',
            'requestType': 'filterData',
            'filter': [{'name': 'USER_ID', 'operator': '=', 'value': userID}],
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        repositoryFactory.factory.and.callFake(function(name) {
            return repositories[name];
        });

        var filters = [{'ID': 1}, {'ID': 2}];
        var layouts = [{'ID': 3}, {'ID': 4}];

        var response = {
            'dataset': {
                'filter': filters,
                'layout': layouts
            }
        };

        requestEngine.doRequest.and.callFake(function() {
            return Q.when(response);
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/preferences',
            'requestType': 'filterData',
            'filter': [{'name': 'USER_ID', 'operator': '=', 'value': userID}]
        };

        zhPreferences.registerRepository('filter');
        zhPreferences.registerRepository('layout');
        zhPreferences.loadUserPreferences(userID).then(function(result) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);

            expect(filterRepository.save).toHaveBeenCalledWith(filters);
            expect(layoutRepository.save).toHaveBeenCalledWith(layouts);

            expect(result).toEqual(['filterReturn', 'layoutReturn']);
            done();
        });

    });

    it('loadUserPreferences: request error', function(done) {
        var userID = 'user-1';

        var filterRepository = jasmine.createSpyObj('filter', ['save']);
        var layoutRepository = jasmine.createSpyObj('layout', ['save']);

        var repositories = {
            'filter': filterRepository,
            'layout': layoutRepository
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/preferences',
            'requestType': 'filterData',
            'filter': [{'name': 'USER_ID', 'operator': '=', 'value': userID}],
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        repositoryFactory.factory.and.callFake(function(name) {
            return repositories[name];
        });

        requestEngine.doRequest.and.callFake(function() {
            return Q.reject({'data': {'error': 'request error'}});
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/preferences',
            'requestType': 'filterData',
            'filter': [{'name': 'USER_ID', 'operator': '=', 'value': userID}]
        };

        zhPreferences.registerRepository('filter');
        zhPreferences.registerRepository('layout');
        zhPreferences.loadUserPreferences(userID).then(null, function(error) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);

            expect(filterRepository.save).not.toHaveBeenCalled();
            expect(layoutRepository.save).not.toHaveBeenCalled();

            expect(error).toEqual(new Error('request error'));
            done();
        });
    });

    it('loadPreferences', function(done) {
        var preferenceType = 'filter';
        var query = {
            where: function() {
                return [{'columnName': 'ID', 'value': 1}];
            }
        };

        var filterRepository = jasmine.createSpyObj('filter', ['save', 'remove']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/filter',
            'requestType': 'filterData',
            'filter': [{'columnName': 'ID', 'value': 1}]
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/filter',
            'requestType': 'filterData',
            'filter': [{'columnName': 'ID', 'value': 1}],
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        var filters = [{'ID': 1}, {'ID': 2}, {'ID': 3}];

        var response = {
            'dataset': {'filter': filters}
        };

        requestEngine.doRequest.and.callFake(function() {
            return Q.when(response);
        });

        filterRepository.save.and.callFake(function() {
            return Q.when();
        });

        zhPreferences.registerRepository(preferenceType);
        zhPreferences.loadPreferences(preferenceType, query).then(function(response) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(filterRepository.remove).toHaveBeenCalledWith(query);
            expect(filterRepository.save).toHaveBeenCalledWith(filters);
            expect(response).toEqual(filters);
            done();
        });
    });

    it('loadPreferences: request error', function(done) {
        var preferenceType = 'filter';
        var query = {
            where: function() {
                return [{'columnName': 'ID', 'value': 1}];
            }
        };

        var filterRepository = jasmine.createSpyObj('filter', ['save', 'remove']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/filter',
            'requestType': 'filterData',
            'filter': [{'columnName': 'ID', 'value': 1}]
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/filter',
            'requestType': 'filterData',
            'filter': [{'columnName': 'ID', 'value': 1}],
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        var filters = [{'ID': 1}, {'ID': 2}, {'ID': 3}];

        requestEngine.doRequest.and.callFake(function() {
            return Q.reject({'data': {'error': 'request error'}});
        });

        zhPreferences.registerRepository(preferenceType);
        zhPreferences.loadPreferences(preferenceType, query).then(null, function(error) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(filterRepository.remove).not.toHaveBeenCalled();
            expect(filterRepository.save).not.toHaveBeenCalled();
            expect(error).toEqual(new Error('request error'));
            done();
        });
    });

    it('savePreferences', function(done) {
        var filterRepository = jasmine.createSpyObj('filter', ['save']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var preferenceType = 'filter';
        var preferences = [{'NAME': '1'}, {'NAME': '2'}];

        var expectedRequest = {
            'serviceName': 'zh-preferences#/filter/save',
            'requestType': 'dataSource',
            'dataSource': preferences
        };
        var factoredRequest = {
            'serviceName': 'zh-preferences#/filter/save',
            'requestType': 'dataSource',
            'dataSource': preferences,
            'factoredRequest': true
        };

        var response = {
            dataset: {
                filter: [{'ID': 1}, {'ID': 2}]
            }
        };

        var expectedPreferences = [
            {
                'ID': 1,
                '__is_new': false,
                'changed': false,
                'NAME': '1'
            },
            {
                'ID': 2,
                '__is_new': false,
                'changed': false,
                'NAME': '2'
            }
        ];

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        requestEngine.doRequest.and.callFake(function() {
            return Q.when(response);
        });

        filterRepository.save.and.callFake(function() {
            return expectedPreferences;
        });

        zhPreferences.registerRepository('filter');
        zhPreferences.savePreferences(preferenceType, preferences).then(function(response) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(filterRepository.save).toHaveBeenCalledWith(expectedPreferences);
            expect(response).toEqual(expectedPreferences);
            done();
        });
    });

    it('savePreferences: request error', function(done) {
        var filterRepository = jasmine.createSpyObj('filter', ['save']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var preferenceType = 'filter';
        var preferences = [{'NAME': '1'}, {'NAME': '2'}];

        var expectedRequest = {
            'serviceName': 'zh-preferences#/filter/save',
            'requestType': 'dataSource',
            'dataSource': preferences
        };
        var factoredRequest = {
            'serviceName': 'zh-preferences#/filter/save',
            'requestType': 'dataSource',
            'dataSource': preferences,
            'factoredRequest': true
        };

        var response = {
            dataset: {
                filter: [{'ID': 1}, {'ID': 2}]
            }
        };

        var expectedPreferences = [
            {
                'ID': 1,
                '__is_new': false,
                'changed': false,
                'NAME': '1'
            },
            {
                'ID': 2,
                '__is_new': false,
                'changed': false,
                'NAME': '2'
            }
        ];

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        requestEngine.doRequest.and.callFake(function() {
            return Q.reject({'data': {'error': 'request error'}});
        });

        zhPreferences.registerRepository('filter');
        zhPreferences.savePreferences(preferenceType, preferences).then(null, function(error) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(filterRepository.save).not.toHaveBeenCalled();
            expect(error).toEqual(new Error('request error'));
            done();
        });
    });

    it('deletePreferences', function(done) {
        var filterRepository = jasmine.createSpyObj('filter', ['remove']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var preferenceType = 'filter';
        var preferences = [{'ID': 1}, {'ID': 2}];

        var expectedRequest = {
            'serviceName': 'zh-preferences#/filter/remove',
            'requestType': 'dataSource',
            'dataSource': preferences
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/filter/remove',
            'requestType': 'dataSource',
            'dataSource': preferences,
            'factoredRequest': true
        };

        var response = {
            dataset: {
                filter: [{'ID': 1}, {'ID': 2}]
            }
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        requestEngine.doRequest.and.callFake(function() {
            return Q.when(response);
        });

        var whereMethod = jasmine.createSpy('where');

        query.build.and.callFake(function() {
            return {
                where: whereMethod
            };
        });

        var inMethod = jasmine.createSpy('in');

        whereMethod.and.callFake(function() {
            return {
                in: inMethod
            };
        });

        var expectedRemoveQuery = [
            {
                'columnName': 'ID',
                'operator': 'IN',
                'value': [1, 2]
            }
        ];

        inMethod.and.callFake(function() {
            return expectedRemoveQuery;
        });

        var deleteReturn = [{'ID': 1}, {'ID': 2}];
        filterRepository.remove.and.callFake(function() {
            return deleteReturn;
        });

        zhPreferences.registerRepository('filter');
        zhPreferences.deletePreferences(preferenceType, preferences).then(function(response) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(filterRepository.remove).toHaveBeenCalledWith(expectedRemoveQuery);
            expect(whereMethod).toHaveBeenCalledWith('ID');
            expect(inMethod).toHaveBeenCalledWith([1, 2]);
            expect(response).toEqual(deleteReturn);
            done();
        });
    });

    it('deletePreferences: request error', function(done) {
        var filterRepository = jasmine.createSpyObj('filter', ['remove']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var preferenceType = 'filter';
        var preferences = [{'ID': 1}, {'ID': 2}];

        var expectedRequest = {
            'serviceName': 'zh-preferences#/filter/remove',
            'requestType': 'dataSource',
            'dataSource': preferences
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/filter/remove',
            'requestType': 'dataSource',
            'dataSource': preferences,
            'factoredRequest': true
        };

        var response = {
            dataset: {
                filter: [{'ID': 1}, {'ID': 2}]
            }
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        requestEngine.doRequest.and.callFake(function() {
            return Q.reject({'data': {'error': 'request error'}});
        });

        zhPreferences.registerRepository('filter');
        zhPreferences.deletePreferences(preferenceType, preferences).then(null, function(error) {
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(filterRepository.remove).not.toHaveBeenCalled();
            expect(error).toEqual(new Error('request error'));
            done();
        });
    });

    it('getPreferences', function(done) {
        var preferenceType = 'filter';

        var filterRepository = jasmine.createSpyObj(preferenceType, ['find']);

        repositoryFactory.factory.and.callFake(function(name) {
            return filterRepository;
        });

        var filters = [{'ID': 1}, {'ID': 2}, {'ID': 3}];

        filterRepository.find.and.callFake(function() {
            return Q.when(filters);
        });

        var query = 'test query';
        zhPreferences.registerRepository(preferenceType);
        zhPreferences.getPreferences(preferenceType, query).then(function(response) {
            expect(filterRepository.find).toHaveBeenCalledWith(query);
            expect(response).toEqual(filters);
            done();
        });
    });

    it('getWidgetPreferences', function(done) {
        var preferenceType = 'filter';
        var widgetId = '1234';
        var userID = '1';

        var filters = [{'ID': 1}, {'ID': 2}, {'ID': 3}];

        spyOn(zhPreferences, 'getPreferences').and.callFake(function() {
            return filters;
        });

        spyOn(zhPreferences, 'getUserID').and.callFake(function() {
            return Q.when(userID);
        });

        var expectedQuery = 'query';

        var equalsMethod2 = jasmine.createSpy('equals').and.callFake(function() {
            return expectedQuery;
        });

        var whereMethod2 = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod2
            };
        });

        var equalsMethod = jasmine.createSpy('equals').and.callFake(function() {
            return {
                where: whereMethod2
            };
        });

        var whereMethod = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod
            };
        });

        query.build.and.callFake(function() {
            return {
                where: whereMethod
            };
        });

        zhPreferences.getWidgetPreferences(preferenceType, widgetId).then(function(response) {
            expect(zhPreferences.getPreferences).toHaveBeenCalledWith(preferenceType, expectedQuery);
            expect(response).toEqual(filters);
            done();
        });
    });

    it('getUserID before setUserID', function(done) {
        spyOn(zhPreferences, 'loadUserPreferences').and.callFake(function() {
            return Q.when();
        });

        var userID = '123';
        zhPreferences.getUserID().then(function(result) {
            expect(zhPreferences.loadUserPreferences).toHaveBeenCalledWith(userID);
            expect(result).toEqual(userID);
            done();
        });

        zhPreferences.setUserID(userID);
    });

    it('getUserID after setUserID', function(done) {
        spyOn(zhPreferences, 'loadUserPreferences').and.callFake(function() {
            return Q.when();
        });

        var userID = '123';
        zhPreferences.setUserID(userID);

        zhPreferences.getUserID().then(function(result) {
            expect(zhPreferences.loadUserPreferences).toHaveBeenCalledWith(userID);
            expect(result).toEqual(userID);
            done();
        });
    });

    it('unsetUser', function() {
        var filterRepository = jasmine.createSpyObj('filter', ['clearAll']);
        var layoutRepository = jasmine.createSpyObj('layout', ['clearAll']);

        var repositories = {
            'filter': filterRepository,
            'layout': layoutRepository
        };

        repositoryFactory.factory.and.callFake(function(name) {
            return repositories[name];
        });

        zhPreferences.registerRepository('filter');
        zhPreferences.registerRepository('layout');

        zhPreferences.unsetUser();

        expect(filterRepository.clearAll).toHaveBeenCalled();
        expect(layoutRepository.clearAll).toHaveBeenCalled();
    });

    it('setUserID: calling multiple times', function(done) {
        spyOn(zhPreferences, 'loadUserPreferences').and.callFake(function() {
            return Q.when();
        });

        var userID = '123';
        zhPreferences.setUserID(userID);

        zhPreferences.getUserID().then(function(result) {
            expect(result).toEqual(userID);
            zhPreferences.getUserID().then(function(secondResult) {
                expect(secondResult).toEqual(userID);
                done();
            });
            zhPreferences.setUserID('another user');
        });
    });

    it('setUserID: calling multiple times after unsetting user', function(done) {
        spyOn(zhPreferences, 'loadUserPreferences').and.callFake(function() {
            return Q.when();
        });

        var userID = '123';
        zhPreferences.setUserID(userID);

        zhPreferences.getUserID().then(function(result) {
            expect(result).toEqual(userID);
            zhPreferences.unsetUser();
            zhPreferences.getUserID().then(function(secondResult) {
                expect(secondResult).toEqual('another user');
                done();
            });
            zhPreferences.setUserID('another user');
        });
    });

    it('getDefaultWidgetPreferences: with value being returned on request', function(done) {
        var userID = 'user-1';
        var widgetID = '123';

        var queryConditions = [{COLUMN: 'value'}];

        var expectedQuery = jasmine.createSpyObj('query', ['where']);

        expectedQuery.where.and.callFake(function() {
            return queryConditions;
        });

        var equalsMethod2 = jasmine.createSpy('equals').and.callFake(function() {
            return expectedQuery;
        });

        var whereMethod2 = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod2
            };
        });

        var equalsMethod = jasmine.createSpy('equals').and.callFake(function() {
            return {
                where: whereMethod2
            };
        });

        var whereMethod = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod
            };
        });

        query.build.and.callFake(function() {
            return {
                where: whereMethod
            };
        });

        spyOn(zhPreferences, 'getUserID').and.callFake(function() {
            return Q.when(userID);
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/default-preferences',
            'requestType': 'filterData',
            'filterData': queryConditions
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/default-preferences',
            'requestType': 'filterData',
            'filterData': queryConditions,
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        var defaultPreferences = {'ID': 1};

        var response = {
            dataset: {
                user_widget: [defaultPreferences]
            }
        };

        requestEngine.doRequest.and.callFake(function() {
            return response;
        });

        zhPreferences.getDefaultWidgetPreferences(widgetID).then(function(result) {
            expect(whereMethod).toHaveBeenCalledWith('USER_ID');
            expect(equalsMethod).toHaveBeenCalledWith(userID);
            expect(whereMethod2).toHaveBeenCalledWith('WIDGET_ID');
            expect(equalsMethod2).toHaveBeenCalledWith(widgetID);

            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);

            expect(result).toEqual(defaultPreferences);
            done();
        });
    });

    it('getDefaultWidgetPreferences: with value not being returned on request', function(done) {
        var userID = 'user-1';
        var widgetID = '123';

        var queryConditions = [{COLUMN: 'value'}];

        var expectedQuery = jasmine.createSpyObj('query', ['where']);

        expectedQuery.where.and.callFake(function() {
            return queryConditions;
        });

        var equalsMethod2 = jasmine.createSpy('equals').and.callFake(function() {
            return expectedQuery;
        });

        var whereMethod2 = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod2
            };
        });

        var equalsMethod = jasmine.createSpy('equals').and.callFake(function() {
            return {
                where: whereMethod2
            };
        });

        var whereMethod = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod
            };
        });

        query.build.and.callFake(function() {
            return {
                where: whereMethod
            };
        });

        spyOn(zhPreferences, 'getUserID').and.callFake(function() {
            return Q.when(userID);
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/default-preferences',
            'requestType': 'filterData',
            'filterData': queryConditions
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/default-preferences',
            'requestType': 'filterData',
            'filterData': queryConditions,
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        var defaultPreferences = {
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

        var response = {
            dataset: {
                user_widget: []
            }
        };

        requestEngine.doRequest.and.callFake(function() {
            return response;
        });

        zhPreferences.getDefaultWidgetPreferences(widgetID).then(function(result) {
            expect(whereMethod).toHaveBeenCalledWith('USER_ID');
            expect(equalsMethod).toHaveBeenCalledWith(userID);
            expect(whereMethod2).toHaveBeenCalledWith('WIDGET_ID');
            expect(equalsMethod2).toHaveBeenCalledWith(widgetID);

            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);

            expect(result).toEqual(defaultPreferences);
            done();
        });
    });

    it('getDefaultWidgetPreferences: calling multiple times for same widget', function(done) {
        var userID = 'user-1';
        var widgetID = '123';

        var queryConditions = [{COLUMN: 'value'}];

        var expectedQuery = jasmine.createSpyObj('query', ['where']);

        expectedQuery.where.and.callFake(function() {
            return queryConditions;
        });

        var equalsMethod2 = jasmine.createSpy('equals').and.callFake(function() {
            return expectedQuery;
        });

        var whereMethod2 = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod2
            };
        });

        var equalsMethod = jasmine.createSpy('equals').and.callFake(function() {
            return {
                where: whereMethod2
            };
        });

        var whereMethod = jasmine.createSpy('where').and.callFake(function() {
            return {
                equals: equalsMethod
            };
        });

        query.build.and.callFake(function() {
            return {
                where: whereMethod
            };
        });

        spyOn(zhPreferences, 'getUserID').and.callFake(function() {
            return Q.when(userID);
        });

        var expectedRequest = {
            'serviceName': 'zh-preferences#/default-preferences',
            'requestType': 'filterData',
            'filterData': queryConditions
        };

        var factoredRequest = {
            'serviceName': 'zh-preferences#/default-preferences',
            'requestType': 'filterData',
            'filterData': queryConditions,
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        var defaultPreferences = {
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

        var response = {
            dataset: {
                user_widget: []
            }
        };

        requestEngine.doRequest.and.callFake(function() {
            return response;
        });

        zhPreferences.getDefaultWidgetPreferences(widgetID).then(function(result) {
            expect(whereMethod).toHaveBeenCalledWith('USER_ID');
            expect(equalsMethod).toHaveBeenCalledWith(userID);
            expect(whereMethod2).toHaveBeenCalledWith('WIDGET_ID');
            expect(equalsMethod2).toHaveBeenCalledWith(widgetID);

            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);

            expect(result).toEqual(defaultPreferences);
        }).then(function() {
            return zhPreferences.getDefaultWidgetPreferences(widgetID);
        }).then(function(result) {
            expect(whereMethod.calls.count()).toEqual(1);
            expect(equalsMethod.calls.count()).toEqual(1);
            expect(whereMethod2.calls.count()).toEqual(1);
            expect(equalsMethod2.calls.count()).toEqual(1);
            expect(requestFactory.factory.calls.count()).toEqual(1);
            expect(requestEngine.doRequest.calls.count()).toEqual(1);

            expect(result).toEqual(defaultPreferences);
            done();
        });
    });

    it('updateDefaultWidgetPreferences', function(done) {
        var property = 'property that contains the default preference id';
        var widgetID = '123';
        var defaultPreferenceID = 'preference-1';

        var defaultPreferences = {
            '__is_new': true,
            'ID': null,
            'WIDGET_ID': widgetID,
            'LAST_MODIFIED_DATE': null
        };

        spyOn(zhPreferences, 'getDefaultWidgetPreferences').and.callFake(function() {
            return Q.when(defaultPreferences);
        });

        var updatedDefaultPreferences = {
            '__is_new': true,
            'ID': null,
            'WIDGET_ID': widgetID,
            'LAST_MODIFIED_DATE': null
        };
        updatedDefaultPreferences[property] = defaultPreferenceID;

        var factoredRequest = {
            'serviceName': 'zh-preferences#/default-preferences/save',
            'requestType': 'dataSource',
            'dataSource': [updatedDefaultPreferences],
            'factoredRequest': true
        };

        requestFactory.factory.and.callFake(function() {
            return factoredRequest;
        });

        var response = {
            dataset: {
                user_widget: [{ID: 1}]
            }
        };

        requestEngine.doRequest.and.callFake(function() {
            return response;
        });

        var returnDefaultPreferences = {
            '__is_new': true,
            'ID': null,
            'WIDGET_ID': widgetID,
            'LAST_MODIFIED_DATE': null
        };
        returnDefaultPreferences[property] = defaultPreferenceID;

        var expectedDefaultPreferences = {
            '__is_new': false,
            'ID': 1,
            'WIDGET_ID': widgetID,
            'LAST_MODIFIED_DATE': null,
            'property that contains the default preference id': 'preference-1'
        };

        var expectedRequest = {
            'serviceName': 'zh-preferences#/default-preferences/save',
            'requestType': 'dataSource',
            'dataSource': [{
                '__is_new': false,
                'ID': 1,
                'WIDGET_ID': widgetID,
                'LAST_MODIFIED_DATE': null,
                'property that contains the default preference id': defaultPreferenceID
            }]
        };

        zhPreferences.updateDefaultWidgetPreferences(property, widgetID, defaultPreferenceID).then(function(result) {
            expect(zhPreferences.getDefaultWidgetPreferences).toHaveBeenCalledWith(widgetID);
            expect(requestFactory.factory).toHaveBeenCalledWith(expectedRequest);
            expect(requestEngine.doRequest).toHaveBeenCalledWith(factoredRequest);
            expect(result).toEqual(expectedDefaultPreferences);
            done();
        });
    });

});