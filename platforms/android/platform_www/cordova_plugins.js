cordova.define('cordova/plugin_list', function(require, exports, module) {
  module.exports = [
    {
      "id": "com.odhen.POS.IntegrationService",
      "file": "plugins/com.odhen.POS/www/IntegrationService.js",
      "pluginId": "com.odhen.POS",
      "clobbers": [
        "cordova.plugins.IntegrationService"
      ]
    },
    {
      "id": "cordova-plugin-android-permissions.Permissions",
      "file": "plugins/cordova-plugin-android-permissions/www/permissions.js",
      "pluginId": "cordova-plugin-android-permissions",
      "clobbers": [
        "cordova.plugins.permissions"
      ]
    },
    {
      "id": "cordova-plugin-device.device",
      "file": "plugins/cordova-plugin-device/www/device.js",
      "pluginId": "cordova-plugin-device",
      "clobbers": [
        "device"
      ]
    },
    {
      "id": "cordova-plugin-keyboard.keyboard",
      "file": "plugins/cordova-plugin-keyboard/www/keyboard.js",
      "pluginId": "cordova-plugin-keyboard",
      "clobbers": [
        "window.Keyboard"
      ]
    },
    {
      "id": "phonegap-plugin-barcodescanner.BarcodeScanner",
      "file": "plugins/phonegap-plugin-barcodescanner/www/barcodescanner.js",
      "pluginId": "phonegap-plugin-barcodescanner",
      "clobbers": [
        "cordova.plugins.barcodeScanner"
      ]
    },
    {
      "id": "cordova-android-movetasktoback.tsd",
      "file": "plugins/cordova-android-movetasktoback/js/typescript_deferred.js",
      "pluginId": "cordova-android-movetasktoback"
    },
    {
      "id": "cordova-android-movetasktoback.plugin",
      "file": "plugins/cordova-android-movetasktoback/js/plugin.js",
      "pluginId": "cordova-android-movetasktoback",
      "clobbers": [
        "mayflower"
      ]
    }
  ];
  module.exports.metadata = {
    "com.odhen.POS": "0.0.1",
    "cordova-plugin-android-permissions": "1.0.0",
    "cordova-plugin-androidx": "1.0.2",
    "cordova-plugin-device": "2.0.3",
    "cordova-plugin-keyboard": "1.2.0",
    "cordova-plugin-whitelist": "1.3.3",
    "phonegap-plugin-barcodescanner": "8.1.0",
    "cordova-plugin-androidx-adapter": "1.1.0",
    "cordova-android-movetasktoback": "0.1.4"
  };
});