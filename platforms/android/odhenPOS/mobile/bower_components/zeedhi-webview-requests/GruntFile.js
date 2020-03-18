 module.exports = function(grunt) {
	var srcPaths = {
		js: [
            'src/packages/*.js',
			'src/index.js'
		]
	};

    grunt.initConfig({
        concat: {
            zh: {
                options: {
                    process: function(src, filepath) {
                        return '\n' + '// FILE: ' + filepath + '\n' + src;
                    }
                },
                src: srcPaths.js,
                dest: 'dist/es6/zeedhi-webview-requests.js'
            }
        },
        uglify: {
        	options: {
                mangle: false,
                compress: true,
                report: 'min',
                banner: '/*! <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            dist: {
                files: {
                    'dist/zeedhi-webview-requests.min.js': 'dist/zeedhi-webview-requests.js'
                }
            }
        },
        jshint: {
            all: ['Gruntfile.js', srcPaths.js],
            options: {
            	esversion: 6
            }
        },
        watch: {
            js: {
				files: srcPaths.js,
				tasks: ['concat', 'babel']
			}
        },
		babel: {
			options: {
				sourceMap: true,
				presets: ['babel-preset-es2015']
			},
			dist: {
				files: {
					'dist/zeedhi-webview-requests.js': 'dist/es6/zeedhi-webview-requests.js'
				}
			}
		}
    });

    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-babel');

    // Default task.
    grunt.registerTask('default', ['concat', 'babel', 'uglify']);
};