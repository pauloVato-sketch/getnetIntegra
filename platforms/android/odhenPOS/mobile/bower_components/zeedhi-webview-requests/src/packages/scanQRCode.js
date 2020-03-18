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
