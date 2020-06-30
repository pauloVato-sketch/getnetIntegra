window.templateUrls = window.templateUrls || [];
var packageName = 'zh-select-autocomplete';

var hasPackageRegistered = window.templateUrls.some(function(templateUrl){
    return templateUrl.package === packageName;
});

if(!hasPackageRegistered){
    window.templateUrls.push({
        'package': packageName,
        'baseUrl': './bower_components/' + packageName + '/dist/templates/'
    });
}