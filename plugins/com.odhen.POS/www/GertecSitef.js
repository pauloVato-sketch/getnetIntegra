var exec = require('cordova/exec');

exports.payment = function (params, success, error) {
    exec(success, error, 'GertecSitef', 'payment', [params]);
};

exports.continue = function (params, success, error) {
    exec(success, error, 'GertecSitef', 'continue', [params]);
};

exports.abort = function (success) {
    exec(success, null, 'GertecSitef', 'abort', []);
};

exports.exportLogs = function (success) {
    exec(success, null, 'GertecSitef', 'exportLogs', []);
};

exports.enableLogs = function () {
	exec(null, null, 'GertecSitef', 'enableLogs', []);
};

exports.disableLogs = function () {
	exec(null, null, 'GertecSitef', 'disableLogs', []);
};

exports.deleteLogs = function () {
	exec(null, null, 'GertecSitef', 'deleteLogs', []);
};