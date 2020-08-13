module.exports = function(grunt) {

	var projectConfigPath = 'config.js';
    var repositoryPath    = 'js/repositories/**/*.js';
    var integrationPath   = 'js/integrations/*.js';
    var printerPath       = 'js/printer/*.js';
    var servicePath       = 'js/services/*.js';
    var controllerPath    = 'js/controllers/*.js';

    var assetsPath = [
        'js/overrides.js',
    	projectConfigPath,
        repositoryPath,
        'js/services/WindowService.js',
        integrationPath,
        printerPath,
        'js/services/PrinterService.js',
        servicePath,
        'js/controllers/AccountController.js',
        'js/controllers/OperatorController.js',
        'js/controllers/OrderController.js',
        'js/controllers/TableController.js',
        controllerPath
    ];

    grunt.initConfig({
        concat: {
            none: {},
            zh: {
                options: {
                    process: function(src, filepath) {
                        return '\n' + '// FILE: ' + filepath + '\n' + src;
                    }
                },
                src: assetsPath,
                dest: 'dist/pos_main.js'
            }
        },
        jshint: {
            all: ['Gruntfile.js', assetsPath],
            options: {
            	esversion: 6
            }
        },
        uglify: {
            options: {
                mangle: false,
                compress: false,
                report: 'min',
                // the banner is inserted at the top of the output
                banner: '/*! <%= grunt.template.today("dd-mm-yyyy") %> */\n'
            },
            dist: {
                files: {
                    'dist/waiter.min.js': assetsPath
                }
            }
        },
        watch: {
            pivotal: {
                files: assetsPath,
                tasks: ['concat', 'jshint']
            }
        },
        zhIdGenerator: {
            options: {
                jsonPath: ["../mobile/json"],
                replaceIds: true
            }
        },
		mocha: {
			src: ['index.html'],
			options: {
				run: true,
                growlOnSuccess: false,
                reporter: 'spec'
			}
		},
        copy: {
            files: {
                expand: true,
                src: '../*.*',
                dest: '../release/odhenPOS/*.*'
            },
            backend: {
                expand: true,
                src: '../backend/**',
                dest: '../release/odhenPOS/**'
            },
                ci_scripts: {
                expand: true,
                src: '../ci_scripts/**',
                dest: '../release/odhenPOS/**'
            },
            mobile: {
                expand: true,
                src: '../mobile/**',
                dest: '../release/odhenPOS/**'
            },
            playStoreKey: {
                expand: true,
                src: '../playStoreKey',
                dest: '../release/odhenPOS/playStoreKey'
            }
          },
        clean: {
            release: ['../release/odhenPOS/'],
            all_paths: ['../release/odhenPOS/backend/vendor/odhen/api/vendor/']
        }
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-watch');
    // grunt.loadNpmTasks('grunt-zh-id-generator');
    grunt.loadNpmTasks('grunt-mocha');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');

    // Default task.
    // Foi retirado o teste unitário, (parametro 'mocha') na função registerTask por erros.
    grunt.registerTask('default', ['jshint', 'concat']);

    // Distribuicao de release
    grunt.registerTask('dist', ['clean:release', 'copy', 'clean:all_paths']);
};