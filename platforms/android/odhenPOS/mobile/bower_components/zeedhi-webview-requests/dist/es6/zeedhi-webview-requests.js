
// FILE: src/packages/currentLocation.js
const currentLocation = {
    apiKey: '',
    createPromise() {
        let promise = new Promise((resolve, reject) => {
            window.currentLocationSuccess = (latitude, longitude) => {
                this.requestAddressToGoogle(latitude, longitude)
                .done((googleAddress) => {
                    resolve(googleAddress)
                })
                .fail((err) => {
                    reject(err)
                })
            }
            window.currentLocationError = (err) => {
                reject(err)
            }
        })
        return promise
    },
    requestAddressToGoogle(latitude, longitude) {
        return $.ajax({
			type: 'POST',
			url: `https://maps.googleapis.com/maps/api/geocode/json?key=${this.apiKey}&latlng=${latitude},${longitude}&sensor=false`
        })
    },
    androidGetCurrentLocation(apiKey) {
        this.apiKey = apiKey;
        let promise = this.createPromise()
        GEOLocationInterface.getCurrentLocation()
        return promise
    },
    iOSGetCurrentLocation(apiKey) {
        this.apiKey = apiKey;
        let promise = this.createPromise()
        window.location = "geolocationinterface://getcurrentlocation";
        return promise
    }
}


// FILE: src/packages/loginFacebook.js
const loginFacebook = {
    createPromise() {
        let promise = new Promise((resolve, reject) => {
            window.loginFacebookSuccess = (user, token) => {
                user.accessToken = token
                resolve(user)
            }
            window.loginFacebookError = (err) => {
                reject(err)
            }
            window.loginFacebookCancel = () => {
                reject()
            }
        })
        return promise
    },
    androidOpenFacebook() {
        let promise = this.createPromise()
        FacebookInterface.startFacebook()
        return promise
    },
    iOSOpenFacebook() {
        let promise = this.createPromise()
        window.location = "facebookinterface://facebooklogin"
        return promise
    }
}


// FILE: src/packages/navigationApps.js
const navigationApps = {
    apiKey: '',
    createPromise() {
        let promise = new Promise((resolve, reject) => {
            window.navigationAppsSuccess = () => {
                resolve()
            }
            window.navigationAppsError = (err) => {
                reject(err)
            }
        })
        return promise
    },
    requestAddressToGoogle(address) {
        return $.ajax({
			type: 'POST',
			url: `https://maps.googleapis.com/maps/api/geocode/json?key=${this.apiKey}&address=${address}&sensor=false`
        })
    },
    androidOpenNavigationApps(address, apiKey) {
        this.apiKey = apiKey;
        let promise = this.createPromise()
        this.requestAddressToGoogle(address)
        .done((googleAddress) => {
            let latitude = googleAddress.results[0].geometry.location.lat
            let longitude = googleAddress.results[0].geometry.location.lng
            GEOLocationInterface.openNavigationApps(latitude, longitude, address)
        })
        .fail((err) => {
            window.navigationAppsError(err)
        })
        return promise
    },
    iOSOpenNavigationApps(address, apiKey) {
        this.apiKey = apiKey;
        let promise = this.createPromise()
        this.requestAddressToGoogle(address)
        .done((googleAddress) => {
            let latitude = googleAddress.results[0].geometry.location.lat
            let longitude = googleAddress.results[0].geometry.location.lng
            window.location = `geolocationinterface://opennavigationapps?coordenates=${latitude},${longitude},${address};`
        })
        .fail((err) => {
            window.navigationAppsError(err)
        })
        return promise
    }
}


// FILE: src/packages/redePoynt.js
const redePoynt = {
    createPromiseDoPayment() {
        let promise = new Promise((resolve, reject) => {
            window.doPaymentRedePoyntSuccess = (success) => {
                resolve(success)
            }
            window.doPaymentRedePoyntError = (err) => {
                reject(err)
            }
        })
        return promise
    },
    androidDoPaymentRedePoynt(value, transactionId, type) {
        let promise = this.createPromiseDoPayment()
        RedePoyntInterface.doPayment(value, transactionId, type);
        return promise
    },
    createPromiseOnReprint() {
        let promise = new Promise((resolve, reject) => {
            window.onReprintRedePoyntSuccess = (success) => {
                resolve(success)
            }
            window.onReprintRedePoyntError = (err) => {
                reject(err)
            }
        })
        return promise
    },
    androidOnReprintRedePoynt() {
        let promise = this.createPromiseOnReprint()
        RedePoyntInterface.onReprint();
        return promise
    },
    createPromiseOnReversal() {
        let promise = new Promise((resolve, reject) => {
            window.onReversalRedePoyntSuccess = (success) => {
                resolve(success)
            }
            window.onReversalRedePoyntError = (err) => {
                reject(err)
            }
        })
        return promise
    },
    androidOnReversalRedePoynt() {
        let promise = this.createPromiseOnReversal()
        RedePoyntInterface.onReversal();
        return promise
    },
    androidHideAppRedePoynt() {
        RedePoyntInterface.hideApp();
        return new Promise((resolve, reject) => {
            resolve()
        })
    },
    createPromisePrint() {
        let promise = new Promise((resolve, reject) => {
            window.printRedePoyntSuccess = (success) => {
                resolve(success)
            }
            window.printRedePoyntError = (err) => {
                reject(err)
            }
        })
        return promise
    },
    androidPrintRedePoynt(content, qrCode, footer) {
        let promise = this.createPromisePrint()
        RedePoyntPrinterJSInterface.printWithPoynt(content, qrCode, footer);
        return promise
    },
}


// FILE: src/packages/scanCreditCard.js
const scanCreditCard = {
    createPromise() {
        let promise = new Promise((resolve, reject) => {
            window.scanCreditCardSuccess = (cardNumber, cardHolderName, cardCVV, cardExpiryMonth, cardExpiryYear) => {
                let creditCard = {
                    "number"     :   cardNumber,
                    "holderName" :   cardHolderName,
                    "cvv"        :   cardCVV,
                    "month"      :   cardExpiryMonth,
                    "year"       :   cardExpiryYear
                }
                resolve(creditCard)
            }
            window.scanCreditCardError = (err) => {
                reject(err)
            }
            window.scanCreditCardCancel = () => {
                reject()
            }
        })
        return promise
    },
    androidOpenScanCreditCard() {
        let promise = this.createPromise()
        ScanCreditCardInterface.getCreditCard();
        return promise
    },
    iOSOpenScanCreditCard() {
        let promise = this.createPromise()
        window.location = "scancreditcardinterface://getcreditcard";
        return promise
    }
}


// FILE: src/packages/scanQRCode.js
const scanQRCode = {
    createPromise() {
        let promise = new Promise((resolve, reject) => {
            window.scanQRCodeSuccess = (qrcode) => {
                resolve(qrcode)
            }
            window.scanQRCodeError  = (err) => {
                reject(err)
            }
            window.scanQRCodeCancel = () => {
                reject()
            }
        })
        return promise
    },
    androidOpenScanQRCode() {
        let promise = this.createPromise()
        ScanQRCodeInterface.scanQRCode()
        return promise
    },
    iOSOpenScanQRCode() {
        let promise = this.createPromise()
        window.location = "scanqrcodeinterface://scanqrcode";
        return promise
    }
}


// FILE: src/packages/uploadImage.js
const uploadImage = {
    createPromise() {
        let promise = new Promise((resolve, reject) => {
            window.imageSuccess = (type, base64, size) => {
                let img = {
                    name    :   "image."+type,
                    type    :   "image/"+type,
                    size    :   size,
                    b64File :   "data:image/"+ type +";base64,"+base64
                }
                resolve(img)
            }
            window.imageError = (err) => {
                reject(err)
            }
            window.imageCancel = () => {
                reject()
            }
        })
        return promise
    },
    androidOpenGallery() {
        let promise = this.createPromise()
        UploadImageInterface.getImageFromGallery()
        return promise
    },
    iOSOpenGallery() {
        let promise = this.createPromise()
        window.location = "uploadimageinterface://upload"
        return promise
    }
}


// FILE: src/index.js
const webViewInterface = {
    ANDROID: "ANDROID",
    IOS: "iOS",
    UNEXPECTED: "UNEXPECTED OPERATION SYSTEM",
    getOS() {
        let userAgent = navigator.userAgent || navigator.vendor || window.opera
        if (/android/i.test(userAgent)) {
            return this.ANDROID
        } else if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
            return this.IOS
        } else {
            return this.UNEXPECTED
        }
    },
    unexpectedOperationSystem() {
        return new Promise((resolve, reject) => {
            reject(this.UNEXPECTED)
        })
    },
    uploadImage() {
        if(this.getOS() == this.ANDROID) {
            return uploadImage.androidOpenGallery()
        } else if(this.getOS() == this.IOS) {
            return uploadImage.iOSOpenGallery()
        } else {
           return this.unexpectedOperationSystem()
        }
    },
    loginFacebook() {
        if(this.getOS() == this.ANDROID) {
            return loginFacebook.androidOpenFacebook()
        } else if(this.getOS() == this.IOS) {
            return loginFacebook.iOSOpenFacebook()
        } else {
           return this.unexpectedOperationSystem()
        } 
    },
    scanCreditCard() {
        if(this.getOS() == this.ANDROID) {
            return scanCreditCard.androidOpenScanCreditCard()
        } else if(this.getOS() == this.IOS) {
            return scanCreditCard.iOSOpenScanCreditCard()
        } else {
           return this.unexpectedOperationSystem()
        } 
    },
    scanQRCode() {
        if(this.getOS() == this.ANDROID) {
            return scanQRCode.androidOpenScanQRCode()
        } else if(this.getOS() == this.IOS) {
            return scanQRCode.iOSOpenScanQRCode()
        } else {
            return this.unexpectedOperationSystem()
        } 
    },
    navigationApps(address, addressName) {
        if(this.getOS() == this.ANDROID) {
            return navigationApps.androidOpenNavigationApps(address, addressName)
        } else if(this.getOS() == this.IOS) {
            return navigationApps.iOSOpenNavigationApps(address, addressName)
        } else {
           return this.unexpectedOperationSystem()
        } 
    },
    currentLocation(address) {
        if(this.getOS() == this.ANDROID) {
            return currentLocation.androidGetCurrentLocation(address)
        } else if(this.getOS() == this.IOS) {
            return currentLocation.iOSGetCurrentLocation(address)
        } else {
           return this.unexpectedOperationSystem()
        } 
    },
    redePoyntDoPayment(value, transactionId, type) {
        if(this.getOS() == this.ANDROID) {
            return redePoynt.androidDoPaymentRedePoynt(value, transactionId, type)
        } else {
           return this.unexpectedOperationSystem()
        } 
    },
    redePoyntOnReprint() {
        if(this.getOS() == this.ANDROID) {
            return redePoynt.androidOnReprintRedePoynt()
        } else {
           return this.unexpectedOperationSystem()
        } 
    },
    redePoyntOnReversal() {
        if(this.getOS() == this.ANDROID) {
            return redePoynt.androidOnReversalRedePoynt()
        } else {
            return this.unexpectedOperationSystem()
        } 
    },
    redePoyntHideApp() {
        if(this.getOS() == this.ANDROID) {
            return redePoynt.androidHideAppRedePoynt()
        } else {
            return this.unexpectedOperationSystem()
        } 
    },
    redePoyntPrint(content, qrCode, footer) {
        if(this.getOS() == this.ANDROID) {
            return redePoynt.androidPrintRedePoynt(content, qrCode, footer)
        } else {
            return this.unexpectedOperationSystem()
        } 
    }
}