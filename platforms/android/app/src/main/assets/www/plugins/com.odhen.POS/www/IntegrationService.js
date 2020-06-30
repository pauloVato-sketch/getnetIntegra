cordova.define("com.odhen.POS.IntegrationService", function(require, exports, module) {

var exec = require('cordova/exec');

exports.payment = function (params, success, error) {
    exec(success, error, 'IntegrationService', 'payment', [params]);
};

exports.refund = function (params, success, error) {
    exec(success, error, 'IntegrationService', 'refund', [params]);
};

exports.print = function (params, success, error) {
    exec(success, error, 'IntegrationService', 'print', [params]);
};

});