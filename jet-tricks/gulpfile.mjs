'use strict';

import gulp from 'gulp';
import rename from 'gulp-rename';
import notify from 'gulp-notify';
import autoprefixer from 'gulp-autoprefixer';
import gulpSass from 'gulp-sass';
import * as sass from 'sass';
import plumber from 'gulp-plumber';
import checktextdomain from 'gulp-checktextdomain';

const sassCompiler = gulpSass(sass);

//frontend
gulp.task('jet-tricks-frontend', () => {
	return gulp.src('./assets/scss/jet-tricks-frontend.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sassCompiler( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: false
		}))

		.pipe(rename('jet-tricks-frontend.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

gulp.task('jet-tricks-admin', () => {
	return gulp.src('./assets/scss/jet-tricks-admin.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sassCompiler( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: false
		}))

		.pipe(rename('jet-tricks-admin.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

gulp.task('jet-tricks-editor', () => {
	return gulp.src('./assets/scss/jet-tricks-editor.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sassCompiler( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: false
		}))

		.pipe(rename('jet-tricks-editor.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

gulp.task('jet-tricks-icons', () => {
	return gulp.src('./assets/scss/jet-tricks-icons.scss')
		.pipe(
			plumber( {
				errorHandler: function ( error ) {
					console.log('=================ERROR=================');
					console.log(error.message);
					this.emit( 'end' );
				}
			})
		)
		.pipe(sassCompiler( { outputStyle: 'compressed' } ))
		.pipe(autoprefixer({
				browsers: ['last 10 versions'],
				cascade: false
		}))

		.pipe(rename('jet-tricks-icons.css'))
		.pipe(gulp.dest('./assets/css/'))
		.pipe(notify('Compile Sass Done!'));
});

//watch
gulp.task('watch', () => {
	gulp.watch('./assets/scss/**', gulp.series( 'jet-tricks-frontend' ) );
	gulp.watch('./assets/scss/**', gulp.series( 'jet-tricks-admin' ) );
	gulp.watch('./assets/scss/**', gulp.series( 'jet-tricks-editor' ) );
	gulp.watch('./assets/scss/**', gulp.series( 'jet-tricks-icons' ) );
});

gulp.task( 'checktextdomain', () => {
	return gulp.src( ['**/*.php', '!cherry-framework/**/*.php'] )
		.pipe( checktextdomain( {
			text_domain: 'jet-tricks',
			keywords:    [
				'__:1,2d',
				'_e:1,2d',
				'_x:1,2c,3d',
				'esc_html__:1,2d',
				'esc_html_e:1,2d',
				'esc_html_x:1,2c,3d',
				'esc_attr__:1,2d',
				'esc_attr_e:1,2d',
				'esc_attr_x:1,2c,3d',
				'_ex:1,2c,3d',
				'_n:1,2,4d',
				'_nx:1,2,4c,5d',
				'_n_noop:1,2,3d',
				'_nx_noop:1,2,3c,4d',
				'translate_nooped_plural:1,2c,3d'
			]
		} ) );
} );
