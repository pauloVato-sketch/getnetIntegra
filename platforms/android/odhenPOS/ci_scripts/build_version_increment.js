var jf = require('jsonfile');
var sys = require('sys')
var exec = require('child_process').exec;
var child;
function incrementVersion(){
	var file = __dirname+'/../version/version.json';
	var obj = jf.readFileSync(file);
	obj.build++;
	obj.revision_date = new Date().getTime();
	jf.writeFileSync(file, obj);
	return obj;
}

function createGitTag(version){
	var version_number = version.version+"."+version.revision+"."+version.build;
	exec("git commit -am \"Ci version increment ("+version_number+")\"", function(){
		exec('git tag '+version_number, function(){
			console.log('version increment finish!');
		});
	});
}
createGitTag(incrementVersion());
//test