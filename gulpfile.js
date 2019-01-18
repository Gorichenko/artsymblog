let gulp = require('gulp');
let cleanCSS = require('gulp-clean-css');
let minifyjs = require('gulp-js-minify');
let uglify = require('gulp-uglify');
let watch = require('gulp-watch');

gulp.task('minify-css', () => {
    return gulp.src('public/web/css/*.css')
        .pipe(cleanCSS({debug: true}, (details) => {
            console.log(`${details.name}: ${details.stats.originalSize}`);
            console.log(`${details.name}: ${details.stats.minifiedSize}`);
        }))
        .pipe(gulp.dest('public/dist/css'));
});

gulp.task('gulp-uglify', () => {
    return gulp.src('public/web/js/*.js')
        .pipe(uglify())
        .pipe(gulp.dest('public/dist/js'))
});

gulp.task('watch', function() {
    gulp.watch('public/web/css/*.css', ['minify-css']);
    gulp.watch('public/web/js/*.js', ['gulp-uglify']);
});


gulp.task('default', ['minify-css', 'gulp-uglify', 'watch']);
