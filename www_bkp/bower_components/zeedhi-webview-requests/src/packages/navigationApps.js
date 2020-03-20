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
