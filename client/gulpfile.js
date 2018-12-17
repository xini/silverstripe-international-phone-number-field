// load modules
var autoprefixer	= require('gulp-autoprefixer'),
	gulp 			= require('gulp'),
	del 			= require('del'),
	path 			= require('path'),
	plumber 		= require('gulp-plumber'),
	cleancss 		= require('gulp-clean-css'),
	concat 			= require('gulp-concat-util'),
	sass 			= require('gulp-sass'),
	cssimport 		= require("gulp-cssimport"),
	stripDebug 		= require('gulp-strip-debug'),
	uglify 			= require('gulp-uglify'),
	sourcemaps 		= require('gulp-sourcemaps'),
	stripdebug 		= require('gulp-strip-debug');

// load paths
var paths = {
	"src": "./src/",
	"dist": "./dist/",
	"absoluteBase": "/resources/vendor/innoweb/silverstripe-international-phone-number-field",
	
	"styles": {
		"src": "scss/",
		"filter": "/**/*.+(scss)",
		"dist": "css/"
	},
	"scripts": {
		"src": "javascript/",
		"filter": "/**/*.+(js)",
		"dist": "javascript/"
	},
	"images": {
		"src": "./node_modules/intl-tel-input/build/img/",
		"filter": "/**/*.+(png|jpg|gif)",
		"dist": "images/"
	}
};

var scriptFiles = {
	"utils.js": [
		"node_modules/intl-tel-input/build/js/utils.js"
	],
	"intl-phone-number-library.js": [
		"node_modules/intl-tel-input/build/js/intlTelInput.js"
	],
	"intl-phone-number-field.js": [
		"src/javascript/jquery-validator.js",
		"src/javascript/field-init.js",
	]
}


var sassOptions = {
    errLogToConsole: true,
    outputStyle: 'compressed'
};

var autoprefixerOptions = {
    browsers: ['last 2 versions', '> 1%', 'IE >= 9'],
    cascade: false,
    supports: false
};

gulp.task('styles', ['cleanstyles'], function () {
    return gulp
        .src(paths.src + paths.styles.src + paths.styles.filter)
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(sourcemaps.init())
        .pipe(cssimport({matchPattern: "*.css"}))
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(cleancss({processImport: true, keepSpecialComments: 0}))
        .pipe(sourcemaps.write('.', {
            sourceMappingURLPrefix: paths.absoluteBase + '/' + paths.styles.dist
        }))
        .pipe(gulp.dest(paths.dist + paths.styles.dist));
});

gulp.task('cleanstyles', function() {
    return del.sync([
    	paths.dist + paths.styles.dist
    ]);
});

gulp.task('scripts', ['cleanscripts'], function() {
    var scriptNames = Object.keys(scriptFiles);
    scriptNames.forEach(function(scriptName) {
        return gulp
            .src(
                scriptFiles[scriptName],
                {
                    cwd: path.join(process.cwd(), './'),
                    nosort: true
                }
            )
            .pipe(plumber({
                errorHandler: onError
            }))
            .pipe(sourcemaps.init())
            .pipe(concat(scriptName))
//            .pipe(stripdebug())
            .pipe(uglify({mangle: false}))
            .pipe(sourcemaps.write('.', {
                sourceMappingURLPrefix: paths.absoluteBase + '/' + paths.scripts.dist
            }))
            .pipe(gulp.dest(paths.dist + paths.scripts.dist));
    });
});

gulp.task('cleanscripts', function() {
    return del.sync([
    	paths.dist + paths.scripts.dist
    ]);
});

gulp.task('images', ['cleanimages'], function(){
    return gulp
        .src(paths.images.src + paths.images.filter)
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(gulp.dest(paths.dist + paths.images.dist))
});

gulp.task('cleanimages', function() {
    return del.sync([
    	paths.dist + paths.images.dist + paths.images.filter
    ]);
});

gulp.task('watch', function() {
	gulp.watch(paths.src + paths.styles.src + paths.styles.filter, ['styles']);
	gulp.watch(paths.src + paths.scripts.src + paths.scripts.filter, ['scripts']);
});

gulp.task('default', ['styles', 'scripts', 'images']);

gulp.task('default', ['styles', 'scripts', 'images', 'watch']);

var onError = function(err) {
    console.log(err);
}
