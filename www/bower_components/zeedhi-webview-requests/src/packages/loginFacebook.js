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
