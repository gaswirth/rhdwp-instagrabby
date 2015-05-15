module.exports = function(grunt) {

	var npmDependencies = require('./package.json').devDependencies;
	var hasStylus = npmDependencies['grunt-contrib-stylus'] !== undefined;

grunt.initConfig({
	pkg: grunt.file.readJSON('package.json'),

	jshint : {
		all : ['Gruntfile.js', 'js/*.js', '!js/**/*modernizr*.js', '!js/**/*.min.js', '!js/vendor/**/*.js']
	},

	watch : {
		js : {
			files: ['js/**/*.js'],
			tasks : ['jshint'],
			options : {
				livereload : true
			}
		},
		stylus : {
			files: ['stylus/**/*.styl'],
			tasks : (hasStylus) ? ['stylus:dev'] : null
		},
		php : {
			files : ['**/*.php'],
			options : {
				livereload : true
			}
		},
		css : {
			files : ['**/*.css'],
			options : {
				livereload : true
			}
		}
	},

	// Stylus dev and production build tasks
	stylus : {
		production : {
			files : [
				{
					src : ['**/*.styl', '!**/_*.styl'],
					cwd : 'stylus',
					dest : 'css',
					ext: '.css',
					expand : true
				}
			],
			options : {
				compress : true,
				banner: '/***\n' +
						'* RHD Production: Compiled at <%= grunt.template.today("h:MM:ss TT") %> on <%= grunt.template.today("dd-mm-yyyy") %>\n' +
						'***/\n\n'
			}
		},
		dev : {
			files : [
				{
					src : ['**/*.styl', '!**/_*.styl'],
					cwd : 'stylus',
					dest : 'css',
					ext : '.css',
					expand : true
				}
			],
			options : {
				compress : false,
				banner: '/***\n' +
						'* RHD Dev: Compiled at <%= grunt.template.today("h:MM:ss TT") %> on <%= grunt.template.today("dd-mm-yyyy") %>\n' +
						'***/\n\n'
			}
		},
	}
});

	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-stylus');

	grunt.registerTask('setup', ['stylus:dev']);
	grunt.registerTask('default', ['jshint', 'watch']);
};
