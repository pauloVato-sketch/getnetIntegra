module.exports = function(grunt) {
	var srcPath = [
		'src/ZhPreferences.js',
		'src/Config.js'
	];

	var codeCoverageFiles = [srcPath, '!src/Config.js'];

	var dependencies = ['node_modules/q/q.js'];
	var helpers = ['tests/helpers/**/*.js'];
	var specsPath = 'tests/specs/**/*.spec.js';

	grunt.initConfig({
		concat: {
			zh: {
				options: {
					process: function(src, filepath) {
						return '// FILE: ' + filepath + '\n' + src + '\n';
					}
				},
				src: [srcPath],
				dest: 'dist/preferences.js'
			}
		},
		uglify: {
			options: {
				mangle: false,
				compress: false,
				report: 'min',
				banner: '/*! <%= grunt.template.today("dd-mm-yyyy") %> */\n'
			},
			dist: {
				files: {
					'dist/preferences.min.js': [srcPath]
				}
			}
		},
		jshint: {
			all: ['Gruntfile.js', specsPath, srcPath]
		},
		jasmine: {
			pivotal: {
				src: codeCoverageFiles,
				options: {
					specs: specsPath,
					vendor: dependencies,
					helpers: helpers,
					template: require('grunt-template-jasmine-istanbul'),
					templateOptions: {
						coverage: 'bin/coverage/coverage.json',
						report: 'bin/coverage',
						thresholds: {
							lines: 100,
							statements: 100,
							branches: 100,
							functions: 100
						}
					}
				}
			},
			toWatch: {
				src: codeCoverageFiles,
				options: {
					specs: specsPath,
					vendor: dependencies,
					helpers: helpers,
				}
			}
		},
		watch: {
			pivotal: {
				files: [specsPath, srcPath],
				tasks: ['jshint', 'concat']
			}
		},
		bump: {
			options: {
				files: ['bower.json', 'package.json'],
				pushTo: 'origin',
				commitFiles: ["-a"],
				push: true
			}
		},
		zhstyles: {
			options: {
				stylesPath: 'dist/style/colors',
				cssPath: 'dist/style/style.css'
			}
		},
		jsdoc: {
			dist: {
				options: {
					configure: 'jsdoc.conf.json'
				}
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-jasmine');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-zh-styles');
	grunt.loadNpmTasks('grunt-bump');
	grunt.loadNpmTasks('grunt-jsdoc');

	grunt.registerTask('default', ['jshint', 'jasmine:toWatch', 'concat']);
	grunt.registerTask('test', ['jshint', 'jasmine:pivotal']);
	grunt.registerTask('doc', ['jsdoc']);
	grunt.registerTask('deploy', ['concat', 'uglify', 'zhstyles']);

};