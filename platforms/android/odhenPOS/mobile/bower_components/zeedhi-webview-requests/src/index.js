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