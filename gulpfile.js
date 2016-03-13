var gulp = require('gulp');
var concat = require('gulp-concat');
var plumber = require('gulp-plumber');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');


var scssFiles = 'web/src/scss/**/*.scss';

gulp.task('default', ['scss'], function() {
    gulp.watch(scssFiles, ['scss']);
});

gulp.task('scss', function() {
    try {
        gulp.src(scssFiles)
            .pipe(plumber())
            .pipe(sass({style: 'compressed'}))
            .pipe(concat('style.css'))
            .pipe(autoprefixer({
                browsers: ['last 2 versions'],
                cascade: false
            }))
            .pipe(gulp.dest('web/compiled'));
    } catch(err) {}
});