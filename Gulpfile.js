'use strict';

/**
 * @var {Gulp} gulp
 */
var gulp = require('gulp');
var plumber = require('gulp-plumber');

var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');

var less = require('gulp-less');
var autoprefixer = require('gulp-autoprefixer');
var csslint = require('gulp-csslint');
var cssnano = require('gulp-cssnano');

var htmlmin = require('gulp-htmlmin');

var imagemin = require('gulp-imagemin');
var pngquant = require('imagemin-pngquant');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var clean = require('gulp-clean');

var bump = require('gulp-bump');
var replace = require('gulp-replace');
var argv = require('yargs').argv;
var git = require('gulp-git');
var shell = require('gulp-shell');
var runSequence = require('run-sequence');

var fs = require('fs');
var pkg = require('./package.json');

var paths = {
    src: 'res/src',
    srcResJs: ['res/src/js/plugins.js','res/src/js/main.js'],
    srcResLess: ['res/src/css/**/*.less', '!res/src/css/**/.*.less'],
    srcResCss: ['res/src/css/**/*.css', '!res/src/css/normalize.css'],
    srcNgJs: ['res/src/ng/**/*.js'],
    srcNgLess: ['res/src/ng/**/*.less'],
    srcNgCss: ['res/src/ng/**/*.css'],
    srcNgHtml: ['res/src/ng/**/*.html'],
    srcImg: 'res/src/img/**/*.{png,gif,jpg}',
    dst: 'res/dist',
    pkgConfigs: [
        'package.json',
        'bower.json',
        'composer.json',
        'chayka.json',
        '.yo-rc.json'
    ]
};

function handleError(err) {
    console.log(err.toString());
    this.emit('end');
}

gulp.task('clean', function(){
    return gulp.src([paths.dist], {read: false})
        .pipe(clean({force: true}));
});

/**
 * CSS
 */
gulp.task('css', function(){
    return gulp.src('res/src/css/normalize.css')
        .pipe(plumber(handleError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions']
        }))
        .pipe(sourcemaps.init())
        .pipe(cssnano())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('res/dist/css'));
});

gulp.task('less:res', function(){
    return gulp.src(paths.srcResLess)
        .pipe(plumber(handleError))
        .pipe(less())
        .pipe(autoprefixer({
            browsers: ['last 2 versions']
        }))
        .pipe(gulp.dest('res/src/css'))
        .pipe(csslint())
        .pipe(csslint.reporter())
        .pipe(sourcemaps.init())
        .pipe(cssnano())
        .pipe(concat('build.css'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('res/dist/css'));
});

gulp.task('less:ng', function(){
    return gulp.src(paths.srcNgLess)
        .pipe(plumber(handleError))
        .pipe(less())
        .pipe(autoprefixer({
            browsers: ['last 2 versions']
        }))
        .pipe(gulp.dest('res/src/ng'))
        .pipe(csslint())
        .pipe(csslint.reporter())
        .pipe(sourcemaps.init())
        .pipe(cssnano())
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('res/dist/ng'));
});

gulp.task('less', ['less:res', 'less:ng']);

/**
 * JS
 */
gulp.task('js:res', function(){
    gulp.src(paths.srcResJs)
        .pipe(plumber(handleError))
        .pipe(jshint())
        .pipe(jshint.reporter('default'))
        .pipe(sourcemaps.init())
        .pipe(uglify({
            mangle: false
        }))
        .pipe(concat('build.js'))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('res/dist/js'));
});

gulp.task('js:ng', function(){
    gulp.src(paths.srcNgJs)
        .pipe(plumber(handleError))
        .pipe(jshint())
        .pipe(jshint.reporter('default'))
        .pipe(sourcemaps.init())
        .pipe(uglify({
            mangle: false
        }))
        .pipe(sourcemaps.write('.'))
        .pipe(gulp.dest('res/dist/ng'));
});

gulp.task('js', ['js:res', 'js:ng']);

/**
 * Html
 */
gulp.task('html', function() {
    return gulp.src(paths.srcNgHtml)
        .pipe(htmlmin({collapseWhitespace: true}))
        .pipe(gulp.dest('res/dist/ng'))
});

/**
 * Images
 */
gulp.task('img', function(){
    return gulp.src(paths.srcImg)
        .pipe(plumber(handleError))
        .pipe(imagemin({
            progressive: true,
            svgoPlugins: [{removeViewBox: false}],
            use: [pngquant()]
        }))
        .pipe(gulp.dest(paths.dst + '/img'));
});

/**
 * Releases
 */
gulp.task('git:tag', function(){
    var currentVersion = 'v' + pkg.version;
    git.tag(currentVersion, 'Version ' + pkg.version, function (err) {
        if (err) {
            throw err;
        }
    });
});

gulp.task('git:push', function(){
    git.push('origin', 'master', {args: '--follow-tags'}, function (err) {
        if (err) {
            throw err;
        }
    });
});

gulp.task('git:add', function() {
    return gulp.src('.')
        .pipe(git.add());
});

gulp.task('git:commit:bump', function(){
    var pkgBumped = JSON.parse(fs.readFileSync('./package.json'));
    var newVersion = pkgBumped.version;
    gulp.src('.')
        .pipe(git.commit('Bumped to version ' + newVersion));
});

gulp.task('replace:version:bump', function(){
    var pkgBumped = JSON.parse(fs.readFileSync('./package.json'));
    var newVersion = pkgBumped.version;
    gulp.src(['*.wpp.php', 'style.css'])
        .pipe(replace(/Version:\s*[^\s]+/, 'Version: ' + newVersion))
        .pipe(gulp.dest('.'));
});

gulp.task('release:notes', shell.task([
    'cat RELEASE-NOTES.md >> RELEASE-HISTORY.md',
    'echo "" > RELEASE-NOTES.md'
]));

gulp.task('release', function(){
    runSequence('git:tag', 'release:notes');
});

/**
 * Get a task function that bumps version
 * @param release
 * @return {Function}
 */
function bumpVersion(release){
    return function() {
        release = release || 'prerelease';
        var version = argv.setversion;
        var options = {};
        if (version) {
            options.version = version;
        } else if (release) {
            options.type = release;
        }
        gulp.src(paths.pkgConfigs)
            .pipe(bump(options))
            .pipe(gulp.dest('./'))
            .on('end', function(){
                runSequence('replace:version:bump', 'git:add', 'git:commit:bump', 'git:push');
            });

    };
}
var releaseIfNeeded = pkg.version.indexOf('-') >=0 ? [] : ['release'];
gulp.task('bump:norelease', bumpVersion());
gulp.task('bump:prerelease', releaseIfNeeded, bumpVersion('prerelease'));
gulp.task('bump:patch', releaseIfNeeded, bumpVersion('patch'));
gulp.task('bump:minor', releaseIfNeeded, bumpVersion('minor'));
gulp.task('bump:major', releaseIfNeeded, bumpVersion('major'));


/**
 * Heads up, build does not include images optimization,
 * run it separately, if you need to.
 */
gulp.task('build', ['less', 'js', 'css', 'html']);

gulp.task('watch', ['build'], function(){
    gulp.watch(paths.srcResLess, [
        'less:res'
    ]);
    gulp.watch(paths.srcNgLess, [
        'less:ng'
    ]);
    gulp.watch(paths.srcResJs, [
        'js:res'
    ]);
    gulp.watch(paths.srcNgJs, [
        'js:ng'
    ]);
    gulp.watch(paths.srcNgHtml, [
        'html'
    ]);
    gulp.watch(paths.srcImg, [
        'img'
    ]);
});

gulp.task('default', ['watch']);