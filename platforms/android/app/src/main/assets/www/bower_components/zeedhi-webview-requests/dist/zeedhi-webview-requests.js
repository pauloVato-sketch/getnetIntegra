'use strict';

// FILE: src/packages/currentLocation.js
var _currentLocation = {
    apiKey: '',
    createPromise: function createPromise() {
        var _this = this;

        var promise = new Promise(function (resolve, reject) {
            window.currentLocationSuccess = function (latitude, longitude) {
                _this.requestAddressToGoogle(latitude, longitude).done(function (googleAddress) {
                    resolve(googleAddress);
                }).fail(function (err) {
                    reject(err);
                });
            };
            window.currentLocationError = function (err) {
                reject(err);
            };
        });
        return promise;
    },
    requestAddressToGoogle: function requestAddressToGoogle(latitude, longitude) {
        return $.ajax({
            type: 'POST',
            url: 'https://maps.googleapis.com/maps/api/geocode/json?key=' + this.apiKey + '&latlng=' + latitude + ',' + longitude + '&sensor=false'
        });
    },
    androidGetCurrentLocation: function androidGetCurrentLocation(apiKey) {
        this.apiKey = apiKey;
        var promise = this.createPromise();
        GEOLocationInterface.getCurrentLocation();
        return promise;
    },
    iOSGetCurrentLocation: function iOSGetCurrentLocation(apiKey) {
        this.apiKey = apiKey;
        var promise = this.createPromise();
        window.location = "geolocationinterface://getcurrentlocation";
        return promise;
    }
};

// FILE: src/packages/loginFacebook.js
var _loginFacebook = {
    createPromise: function createPromise() {
        var promise = new Promise(function (resolve, reject) {
            window.loginFacebookSuccess = function (user, token) {
                user.accessToken = token;
                resolve(user);
            };
            window.loginFacebookError = function (err) {
                reject(err);
            };
            window.loginFacebookCancel = function () {
                reject();
            };
        });
        return promise;
    },
    androidOpenFacebook: function androidOpenFacebook() {
        var promise = this.createPromise();
        FacebookInterface.startFacebook();
        return promise;
    },
    iOSOpenFacebook: function iOSOpenFacebook() {
        var promise = this.createPromise();
        window.location = "facebookinterface://facebooklogin";
        return promise;
    }
};

// FILE: src/packages/navigationApps.js
var _navigationApps = {
    apiKey: '',
    createPromise: function createPromise() {
        var promise = new Promise(function (resolve, reject) {
            window.navigationAppsSuccess = function () {
                resolve();
            };
            window.navigationAppsError = function (err) {
                reject(err);
            };
        });
        return promise;
    },
    requestAddressToGoogle: function requestAddressToGoogle(address) {
        return $.ajax({
            type: 'POST',
            url: 'https://maps.googleapis.com/maps/api/geocode/json?key=' + this.apiKey + '&address=' + address + '&sensor=false'
        });
    },
    androidOpenNavigationApps: function androidOpenNavigationApps(address, apiKey) {
        this.apiKey = apiKey;
        var promise = this.createPromise();
        this.requestAddressToGoogle(address).done(function (googleAddress) {
            var latitude = googleAddress.results[0].geometry.location.lat;
            var longitude = googleAddress.results[0].geometry.location.lng;
            GEOLocationInterface.openNavigationApps(latitude, longitude, address);
        }).fail(function (err) {
            window.navigationAppsError(err);
        });
        return promise;
    },
    iOSOpenNavigationApps: function iOSOpenNavigationApps(address, apiKey) {
        this.apiKey = apiKey;
        var promise = this.createPromise();
        this.requestAddressToGoogle(address).done(function (googleAddress) {
            var latitude = googleAddress.results[0].geometry.location.lat;
            var longitude = googleAddress.results[0].geometry.location.lng;
            window.location = 'geolocationinterface://opennavigationapps?coordenates=' + latitude + ',' + longitude + ',' + address + ';';
        }).fail(function (err) {
            window.navigationAppsError(err);
        });
        return promise;
    }
};

// FILE: src/packages/redePoynt.js
var redePoynt = {
    createPromiseDoPayment: function createPromiseDoPayment() {
        var promise = new Promise(function (resolve, reject) {
            window.doPaymentRedePoyntSuccess = function (success) {
                resolve(success);
            };
            window.doPaymentRedePoyntError = function (err) {
                reject(err);
            };
        });
        return promise;
    },
    androidDoPaymentRedePoynt: function androidDoPaymentRedePoynt(value, transactionId, type) {
        var promise = this.createPromiseDoPayment();
        RedePoyntInterface.doPayment(value, transactionId, type);
        return promise;
    },
    createPromiseOnReprint: function createPromiseOnReprint() {
        var promise = new Promise(function (resolve, reject) {
            window.onReprintRedePoyntSuccess = function (success) {
                resolve(success);
            };
            window.onReprintRedePoyntError = function (err) {
                reject(err);
            };
        });
        return promise;
    },
    androidOnReprintRedePoynt: function androidOnReprintRedePoynt() {
        var promise = this.createPromiseOnReprint();
        RedePoyntInterface.onReprint();
        return promise;
    },
    createPromiseOnReversal: function createPromiseOnReversal() {
        var promise = new Promise(function (resolve, reject) {
            window.onReversalRedePoyntSuccess = function (success) {
                resolve(success);
            };
            window.onReversalRedePoyntError = function (err) {
                reject(err);
            };
        });
        return promise;
    },
    androidOnReversalRedePoynt: function androidOnReversalRedePoynt() {
        var promise = this.createPromiseOnReversal();
        RedePoyntInterface.onReversal();
        return promise;
    },
    androidHideAppRedePoynt: function androidHideAppRedePoynt() {
        RedePoyntInterface.hideApp();
        return new Promise(function (resolve, reject) {
            resolve();
        });
    },
    createPromisePrint: function createPromisePrint() {
        var promise = new Promise(function (resolve, reject) {
            window.printRedePoyntSuccess = function (success) {
                resolve(success);
            };
            window.printRedePoyntError = function (err) {
                reject(err);
            };
        });
        return promise;
    },
    androidPrintRedePoynt: function androidPrintRedePoynt(content, qrCode, footer) {
        var promise = this.createPromisePrint();
        RedePoyntPrinterJSInterface.printWithPoynt(content, qrCode, footer);
        return promise;
    }
};

// FILE: src/packages/scanCreditCard.js
var _scanCreditCard = {
    createPromise: function createPromise() {
        var promise = new Promise(function (resolve, reject) {
            window.scanCreditCardSuccess = function (cardNumber, cardHolderName, cardCVV, cardExpiryMonth, cardExpiryYear) {
                var creditCard = {
                    "number": cardNumber,
                    "holderName": cardHolderName,
                    "cvv": cardCVV,
                    "month": cardExpiryMonth,
                    "year": cardExpiryYear
                };
                resolve(creditCard);
            };
            window.scanCreditCardError = function (err) {
                reject(err);
            };
            window.scanCreditCardCancel = function () {
                reject();
            };
        });
        return promise;
    },
    androidOpenScanCreditCard: function androidOpenScanCreditCard() {
        var promise = this.createPromise();
        ScanCreditCardInterface.getCreditCard();
        return promise;
    },
    iOSOpenScanCreditCard: function iOSOpenScanCreditCard() {
        var promise = this.createPromise();
        window.location = "scancreditcardinterface://getcreditcard";
        return promise;
    }
};

// FILE: src/packages/scanQRCode.js
var _scanQRCode = {
    createPromise: function createPromise() {
        var promise = new Promise(function (resolve, reject) {
            window.scanQRCodeSuccess = function (qrcode) {
                resolve(qrcode);
            };
            window.scanQRCodeError = function (err) {
                reject(err);
            };
            window.scanQRCodeCancel = function () {
                reject();
            };
        });
        return promise;
    },
    androidOpenScanQRCode: function androidOpenScanQRCode() {
        var promise = this.createPromise();
        ScanQRCodeInterface.scanQRCode();
        return promise;
    },
    iOSOpenScanQRCode: function iOSOpenScanQRCode() {
        var promise = this.createPromise();
        window.location = "scanqrcodeinterface://scanqrcode";
        return promise;
    }
};

// FILE: src/packages/uploadImage.js
var _uploadImage = {
    createPromise: function createPromise() {
        var promise = new Promise(function (resolve, reject) {
            window.imageSuccess = function (type, base64, size) {
                var img = {
                    name: "image." + type,
                    type: "image/" + type,
                    size: size,
                    b64File: "data:image/" + type + ";base64," + base64
                };
                resolve(img);
            };
            window.imageError = function (err) {
                reject(err);
            };
            window.imageCancel = function () {
                reject();
            };
        });
        return promise;
    },
    androidOpenGallery: function androidOpenGallery() {
        var promise = this.createPromise();
        UploadImageInterface.getImageFromGallery();
        return promise;
    },
    iOSOpenGallery: function iOSOpenGallery() {
        var promise = this.createPromise();
        window.location = "uploadimageinterface://upload";
        return promise;
    }
};

// FILE: src/index.js
var webViewInterface = {
    ANDROID: "ANDROID",
    IOS: "iOS",
    UNEXPECTED: "UNEXPECTED OPERATION SYSTEM",
    getOS: function getOS() {
        var userAgent = navigator.userAgent || navigator.vendor || window.opera;
        if (/android/i.test(userAgent)) {
            return this.ANDROID;
        } else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
            return this.IOS;
        } else {
            return this.UNEXPECTED;
        }
    },
    unexpectedOperationSystem: function unexpectedOperationSystem() {
        var _this2 = this;

        return new Promise(function (resolve, reject) {
            reject(_this2.UNEXPECTED);
        });
    },
    uploadImage: function uploadImage() {
        if (this.getOS() == this.ANDROID) {
            return _uploadImage.androidOpenGallery();
        } else if (this.getOS() == this.IOS) {
            return _uploadImage.iOSOpenGallery();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    loginFacebook: function loginFacebook() {
        if (this.getOS() == this.ANDROID) {
            return _loginFacebook.androidOpenFacebook();
        } else if (this.getOS() == this.IOS) {
            return _loginFacebook.iOSOpenFacebook();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    scanCreditCard: function scanCreditCard() {
        if (this.getOS() == this.ANDROID) {
            return _scanCreditCard.androidOpenScanCreditCard();
        } else if (this.getOS() == this.IOS) {
            return _scanCreditCard.iOSOpenScanCreditCard();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    scanQRCode: function scanQRCode() {
        if (this.getOS() == this.ANDROID) {
            return _scanQRCode.androidOpenScanQRCode();
        } else if (this.getOS() == this.IOS) {
            return _scanQRCode.iOSOpenScanQRCode();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    navigationApps: function navigationApps(address, addressName) {
        if (this.getOS() == this.ANDROID) {
            return _navigationApps.androidOpenNavigationApps(address, addressName);
        } else if (this.getOS() == this.IOS) {
            return _navigationApps.iOSOpenNavigationApps(address, addressName);
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    currentLocation: function currentLocation(address) {
        if (this.getOS() == this.ANDROID) {
            return _currentLocation.androidGetCurrentLocation(address);
        } else if (this.getOS() == this.IOS) {
            return _currentLocation.iOSGetCurrentLocation(address);
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    redePoyntDoPayment: function redePoyntDoPayment(value, transactionId, type) {
        if (this.getOS() == this.ANDROID) {
            return redePoynt.androidDoPaymentRedePoynt(value, transactionId, type);
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    redePoyntOnReprint: function redePoyntOnReprint() {
        if (this.getOS() == this.ANDROID) {
            return redePoynt.androidOnReprintRedePoynt();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    redePoyntOnReversal: function redePoyntOnReversal() {
        if (this.getOS() == this.ANDROID) {
            return redePoynt.androidOnReversalRedePoynt();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    redePoyntHideApp: function redePoyntHideApp() {
        if (this.getOS() == this.ANDROID) {
            return redePoynt.androidHideAppRedePoynt();
        } else {
            return this.unexpectedOperationSystem();
        }
    },
    redePoyntPrint: function redePoyntPrint(content, qrCode, footer) {
        if (this.getOS() == this.ANDROID) {
            return redePoynt.androidPrintRedePoynt(content, qrCode, footer);
        } else {
            return this.unexpectedOperationSystem();
        }
    }
};
//# sourceMappingURL=zeedhi-webview-requests.js.map
