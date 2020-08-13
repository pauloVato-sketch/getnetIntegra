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
