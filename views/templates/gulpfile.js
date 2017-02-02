var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var minifyCss = require('gulp-minify-css');
var exec = require('child_process').exec;

gulp.task('moviesHbs', function() {
    exec('handlebars js/movies -f js/movies/movies.hbs', function(err, stdout, stderr){
        if(err) {
            throw err;
        }
    });
});

gulp.task('movies', ['moviesHbs'], function() {
   return gulp.src([
        'js/jquery-2.1.4.min.js',
        'js/jBox/jbox.min.js',
        'js/bootstrap.min.js',
        'js/handlebars.runtime-v4.0.2.js',
        'js/handlebarsHelpers.js', 
        'js/movies/movies.hbs', 
        'js/handlebarsPartials.js',
        'js/movies/commons.js',
        'js/movies/moviesActionAdd.js',
        'js/movies/moviesActionEdit.js',
        'js/movies/moviesActionLogout.js',
        'js/movies/moviesActionDelete.js',
        'js/movies/moviesActionSearch.js',
        'js/movies/moviesActionDetails.js',
        'js/movies/moviesActionList.js',
        'js/bootstrap-toggle.min.js'
    ])
    .pipe(concat('movies.js'))
    .pipe(uglify())
    .pipe(rename('movies.min.js'))
    .pipe(gulp.dest('dist/js/movies')); 
});

gulp.task('adminHbs', function(cb) {
    exec('handlebars js/admin -f js/admin/admin.hbs', function(err, stdout, stderr){
        cb(err);
    });
});

gulp.task('admin', ['adminHbs'], function() {
    return gulp.src([
        'js/jquery-2.1.4.min.js',
        'js/bootstrap.min.js',
        'js/handlebars.runtime-v4.0.2.js',
        'js/admin/admin.hbs',
        'js/admin/adminActions.js'
    ])
    .pipe(concat('admin.js'))
    .pipe(uglify())
    .pipe(rename('admin.min.js'))
    .pipe(gulp.dest('dist/js/admin'));
});

gulp.task('fonts', function(){
    return gulp.src([
        'fonts/*.*'
    ])
    .pipe(gulp.dest('dist/css/fonts'));
});

gulp.task('moviesStyles', ['fonts'], function() {
    return gulp.src([
        'css/bootstrap.min.css',
        'css/bootstrap-theme.min.css',
        'css/movies/*.css',
        'js/jBox/jbox.min.css',
        'js/jBox/themes/*.css',
        'css/Forms.css',
        'css/bootstrap-toggle.min.css'
    ])
    .pipe(concat('movies.css'))
    .pipe(minifyCss())
    .pipe(rename('movies.min.css'))
    .pipe(gulp.dest('dist/css/movies'));
});

gulp.task('adminStyles', ['fonts'], function(){
    return gulp.src([
        'css/bootstrap.min.css',
        'css/bootstrap-theme.min.css',
        'css/admin/*.css',
        'css/Forms.css',
        'js/jBox/jbox.css'
    ])
    .pipe(concat('admin.css'))
    .pipe(minifyCss())
    .pipe(rename('admin.min.css'))
    .pipe(gulp.dest('dist/css/admin'));
});

gulp.task('styles', ['moviesStyles', 'adminStyles']);

function swallowError(err) {
    console.log(err);
    this.emit(end);
}

gulp.task('watch', function() {
    gulp.watch(['css/*.*', 'css/movies/*.*', 'css/admin/*.*'], ['styles'])
        .on('error', swallowError);
    gulp.watch(['js/movies/*.*', '!js/movies/movies.hbs'], ['movies'])
        .on('error', swallowError);
    gulp.watch(['js/admin/*.*', '!js/admin/admin.hbs'], ['admin'])
        .on('error', swallowError);
});

gulp.task('default', ['admin', 'movies', 'styles', 'watch']);