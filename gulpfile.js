var gulp = require('gulp');
var logger = require('gulp-logger');
var uglify = require('gulp-uglify');
var $ = require('gulp-load-plugins')();

var doLess = function (src, dest) {
  gulp.src(src)
    .pipe($.less())
    .pipe($.minifyCss({keepBreaks:true}))
    .pipe(gulp.dest(dest))
    .pipe(logger({beforeEach:'[less] wrote:'}));
}

var doUglify = function (src, dest) {
  gulp.src(src)
    .pipe(uglify({preserveComments:'some'}))
    .pipe(gulp.dest(dest));
};

gulp.task('ikamodoki', function() {
  doLess(
    'resources/ikamodoki/ikamodoki.less',
    'resources/.compiled/ikamodoki'
  );

  gulp.src('resources/ikamodoki/font/**')
    .pipe(gulp.dest('resources/.compiled/ikamodoki/font'));
});

gulp.task('gh-fork', function() {
  doUglify(
    'resources/gh-fork-ribbon/*.js',
    'resources/.compiled/gh-fork-ribbon'
  );
});

gulp.task('tz-data', function() {
  doUglify(
    'resources/tz-data/*.js',
    'resources/.compiled/tz-data'
  );

  gulp.src('runtime/tzdata/**')
    .pipe(gulp.dest('resources/.compiled/tz-data/files'));
});

gulp.task('less', function() {
  doLess(
    'resources/fest.ink/*.less',
    'resources/.compiled/fest.ink'
  );
});

gulp.task('uglify', function() {
  doUglify(
    'resources/fest.ink/*.js',
    'resources/.compiled/fest.ink'
  );
});

gulp.task('default', [
  'ikamodoki',
  'gh-fork',
  'tz-data',
  'less',
  'uglify',
]);
