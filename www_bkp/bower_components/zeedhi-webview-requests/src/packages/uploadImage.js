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
