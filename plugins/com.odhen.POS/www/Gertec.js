var exec = require('cordova/exec');

exports.printText = function (text, success, error) {
    exec(success, error, 'Gertec', 'printText', [text]);
};

exports.printQrCode = function (text, success, error) {
    exec(success, error, 'Gertec', 'printQrCode', [text]);
};

exports.printBarCode = function (text, success, error) {
    exec(success, error, 'Gertec', 'printBarCode', [text]);
};