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
