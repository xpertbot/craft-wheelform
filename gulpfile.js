var gulp = require('gulp');
var babelify = require('babelify');
var browserify = require('browserify');
var buffer = require('vinyl-buffer');
var source = require('vinyl-source-stream');
var notify = require('gulp-notify');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var plumber = require('gulp-plumber');

gulp.task('build', function(){
    var options = {
        entries: 'resources/js/entry.js',
        extension: ['.js'],
        debug: true,
        paths: ['./node_modules/', './resources/js/']
    };

    return browserify(options)
        .transform(babelify)
        .bundle()
        .on('error', function(err){
            console.log(err.stack);
            notify("Error: <%= err.message %>");
            this.emit('end');
          })
        .pipe(source('wheelform-bundle.js'))
        .pipe(buffer())
        .pipe(gulp.dest('./src/assets/js/'));
});

gulp.task('sass', function(){
    return gulp.src('resources/sass/**/*.scss')
        .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
        .pipe(sass({
            outputStyle: 'compressed',
            includePaths: ['resources/sass', 'node_modules/']
        }))
        .pipe(autoprefixer())
        .pipe(plumber.stop())
        .pipe(gulp.dest('./src/assets/css/'))
});

gulp.task('default',['sass', 'build'], function() {
    gulp.watch(['resources/js/**/*.js'], ['build']);
    gulp.watch(['resources/sass/**/*.scss'], ['sass']);
});
