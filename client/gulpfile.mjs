import gulp from 'gulp';
const { series, parallel, src, dest, task, watch } = gulp;
import autoprefixer from 'gulp-autoprefixer';
import cssimport from 'gulp-cssimport';
import cleancss from 'gulp-clean-css';
import { deleteSync } from 'del';
import gulpif from 'gulp-if';
import plumber from 'gulp-plumber';
import * as dartSass from 'sass';
import gulpSass from 'gulp-sass';
const sass = gulpSass(dartSass);
import sourcemaps from 'gulp-sourcemaps';
import stripdebug from 'gulp-strip-debug';
import uglify from 'gulp-uglify';
import rollup from 'gulp-best-rollup-2';

import { nodeResolve } from "@rollup/plugin-node-resolve";
import { default as commonjs } from "@rollup/plugin-commonjs";

// load paths
const paths = {
	"styles": {
		"src": "src/scss/",
		"filter": "/*.+(scss)",
		"dist": "dist/css/"
	},
	"scripts": {
		"src": "src/javascript/",
		"filter": "/*.+(js)",
		"dist": "dist/javascript/"
	}
};

const sassOptions = {
    errLogToConsole: true,
    outputStyle: 'compressed'
};

const autoprefixerOptions = {
    cascade: false,
    supports: false
};

var debugEnabled = false;

function styles(cb) {
    src(paths.styles.src + paths.styles.filter)
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(sourcemaps.init())
        .pipe(cssimport({matchPattern: "*.css"}))
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(cleancss({
            level: {
                1: {
                    normalizeUrls: false
                },
                2: {
                    restructureRules: true
                }
            }
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(dest(paths.styles.dist));
    cb();
}

function scripts(cb) {
    src(paths.scripts.src + paths.scripts.filter)
        .pipe(plumber({
            errorHandler: onError
        }))
        .pipe(sourcemaps.init())
        .pipe(rollup({ plugins: [
                nodeResolve({
                    browser: true
                }),
                commonjs()
            ] }, 'module'))
        .pipe(
            gulpif(
                !debugEnabled,
                stripdebug()
            )
        )
        .pipe(uglify({mangle: false}))
        .pipe(sourcemaps.write('.'))
        .pipe(dest(paths.scripts.dist));
    cb();
}


function cleanscripts(cb) {
    deleteSync([
        paths.scripts.dist + paths.scripts.distfilter
    ]);
    cb();
}

function cleanstyles(cb) {
    deleteSync([
        paths.styles.dist + paths.styles.distfilter
    ]);
    cb();
}

function watchAll() {
    // watch for style changes
    watch(paths.styles.src + paths.styles.filter, series(cleanstyles, styles));
    // watch for script changes
    watch(paths.scripts.src + "/**/*.+(js)", series(cleanscripts, scripts));
}

function enableDebug(cb) {
    debugEnabled = true;
    cb();
}

function onError(err) {
    console.log(err);
}

task('clean', series(
    parallel(
        cleanstyles,
        cleanscripts
    )
));

task('build', series(
    parallel(
        cleanstyles,
        cleanscripts
    ),
    parallel(
        styles,
        scripts
    )
));

task('css', series(
    cleanstyles,
    styles
));

task('js', series(
    cleanscripts,
    scripts
));

task('default', series(
    parallel(
        cleanstyles,
        cleanscripts
    ),
    parallel(
        styles,
        scripts
    ),
    watchAll
));

task('debug', series(
    enableDebug,
    parallel(
        cleanstyles,
        cleanscripts
    ),
    parallel(
        styles,
        scripts
    ),
    watchAll
));
