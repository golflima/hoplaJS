var gulp = require('gulp');
var cleanCss = require('gulp-clean-css');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var sourcemaps = require('gulp-sourcemaps');
var uglify = require('gulp-uglify');

var assets = './web/assets';
var maps = 'maps';

gulp.task('css', function() {  
    return gulp.src([
            './res/vendor/bootstrap/dist/css/bootstrap.css',
            './res/vendor/bootstrap/dist/css/bootstrap-theme.css',
            './res/site.css',
        ])
        .pipe(sourcemaps.init())
        .pipe(concat('site.css'))
        .pipe(gulp.dest(assets))
        .pipe(rename('site.min.css'))
        .pipe(cleanCss())
        .pipe(sourcemaps.write(maps))
        .pipe(gulp.dest(assets));
});

gulp.task('js', function() {  
    return gulp.src([
            './res/vendor/jquery/dist/jquery.js',
            './res/vendor/bootstrap/dist/js/bootstrap.js',
            './res/vendor/uglifyjs2/lib/utils.js',
            './res/vendor/uglifyjs2/lib/ast.js',
            './res/vendor/uglifyjs2/lib/parse.js',
            './res/vendor/uglifyjs2/lib/transform.js',
            './res/vendor/uglifyjs2/lib/scope.js',
            './res/vendor/uglifyjs2/lib/output.js',
            './res/vendor/uglifyjs2/lib/compress.js',
            './res/site.js',
        ])
        .pipe(sourcemaps.init())
        .pipe(concat('site.js'))
        .pipe(gulp.dest(assets))
        .pipe(rename('site.min.js'))
        .pipe(uglify())
        .pipe(sourcemaps.write(maps))
        .pipe(gulp.dest(assets));
});

gulp.task('default', ['css', 'js']);