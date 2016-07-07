import gulp     from 'gulp';
import path     from 'path';
import webpack  from 'webpack';
import WebpackDevServer from 'webpack-dev-server';
import karma    from 'karma';
import ip from 'ip';
import webpackConfig from './webpack.config';

let paths = {
    build: path.join(__dirname, 'target/build')
};


/**
 * Gulp-Task: Fuehrt webpack aus und startet den Development-Server
 */
gulp.task('dev-server', () => {
    return new WebpackDevServer(webpack(webpackConfig.development), {
        hot: true,
        contentBase: './dist/',
        watchOptions: {
            aggregateTimeout: 100, poll: 300
        }, stats: {
            colors: true
        }
    }).listen(3000, 'localhost', function (err) {
        if (err) {
            console.error(err);
        }
    });
});


/**
 * Gulp-Task: Fuehrt die Karma-Tests auf dem PhantomJS Browser aus
 */
gulp.task('test-phantomjs', (done) => {

    let hostname = process.env.host || ip.address();
    let externalport = process.env.externalport || 7777;

    return new karma.Server({
        configFile: __dirname + '/karma.conf.js',
        hostname: hostname,
        port: externalport,
        browsers: ['PhantomJS']
    }, done).start();
});


gulp.task('webpack-prod', [], (done) => {
    return webpack(webpackConfig.production, done);
});


gulp.task('build', ['test-phantomjs', 'webpack-prod'], (done) => {
    return gulp
        .src(path.join('target', 'build', '*'))
        .pipe(gulp.dest('dist'))();
});

gulp.task('build-skipTests', ['webpack-prod'], (done) => {
    return gulp
        .src(path.join('target', 'build', '*'))
        .pipe(gulp.dest('dist'))();
});


gulp.task('default', ['dev-server']);
