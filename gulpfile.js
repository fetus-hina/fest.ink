var gulp = require('gulp');
var logger = require('gulp-logger');
var uglify = require('gulp-uglify');
var $ = require('gulp-load-plugins')();

gulp.task('gh-fork', function() {
  gulp.src('resources/gh-fork-ribbon/gh-fork-ribbon.js')
    .pipe(uglify({
      preserveComments: 'some',
      output: {
        ascii_only: true,
      },
    }))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/gh-fork-ribbon'));
});

gulp.task('tz-data', function() {
  gulp.src('resources/tz-data/tz-init.js')
    .pipe(uglify({
      preserveComments: 'some',
      output: {
        ascii_only: true,
      },
    }))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/tz-data'));

  gulp.src('runtime/tzdata/**')
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/tz-data/files'));
});

gulp.task('fest-ink-css', function() {
  gulp.src('resources/fest.ink/*.less')
    .pipe($.less())
    .pipe($.minifyCss({keepBreaks:true}))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/fest.ink'))
});

gulp.task('fest-ink-js', function() {
  gulp.src('resources/fest.ink/fest.js/*.js')
    .pipe($.concat('fest.js', {newLine:';'}))
    .pipe(uglify({
      preserveComments: 'some',
      output: {
        ascii_only: true,
      },
    }))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/fest.ink'));
});

gulp.task('default', [
  'fest-ink-css',
  'fest-ink-js',
  'gh-fork',
  'tz-data',
]);
