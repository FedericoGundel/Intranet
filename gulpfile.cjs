const gulp = require('gulp');
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');

// Ruta a tus archivos CSS
const cssPath = 'public/css/*.css';

// Tarea para minificar CSS
gulp.task('minify-css', function () {
  return gulp.src([cssPath, '!public/css/*.min.css']) // Ignora los minificados
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('public/css'));
});

// Tarea para observar cambios
gulp.task('watch', function () {
  gulp.watch([cssPath, '!public/css/*.min.css'], gulp.series('minify-css'));
});
