(function(){

window.metaDataUrls = window.metaDataUrls || [];
window.serviceUrls = window.serviceUrls || [];

var PACKAGE_NAME = 'zh-preferences';

var hasMetaData = window.metaDataUrls.some(function(metaDataUrl){
	return metaDataUrl.package === PACKAGE_NAME;
});

var hasServiceUrl = window.metaDataUrls.some(function(metaDataUrl){
    return metaDataUrl.package === PACKAGE_NAME;
});

if (!hasMetaData) {
    window.metaDataUrls.push({
        package: PACKAGE_NAME,
        baseUrl: 'bower_components/zh-preferences/dist/assets/'
    });
}

if (!hasServiceUrl) {
    window.serviceUrls.push({
        package: PACKAGE_NAME,
        baseUrl: '../backend/service/index.php/zh-preferences'
    });
}

})();