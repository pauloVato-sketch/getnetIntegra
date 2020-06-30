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
