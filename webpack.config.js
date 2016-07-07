var webpack = require('webpack');
var extend = require('node.extend');
var path = require('path');
var browserSyncPlugin = require('browser-sync-webpack-plugin');
var ngAnnotatePlugin = require('ng-annotate-webpack-plugin');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var webjsConfig = require('./shared.build.config');
var url = require('url');
var proxyMiddleware = require('proxy-middleware');

/**
 * Gemeinsame Konfigurationsdatei fuer Webpack (der Teil, der fuer alle Umgebungen gleich ist)
 * @type {} Webpack Konfiguration
 */
var commonConfig = {
    context: path.resolve(__dirname, 'src/app'),
    // Einstiegspunkt fuer Webpack
    entry: {
        mailbox: './app.js'
        // angular: 'angular',
        // anguboot: 'angular-ui-bootstrap',
        // angurouter: 'angular-ui-router',
        // bootcss: 'bootstrap/dist/css/bootstrap.css',
        // angusan: 'angular-sanitize',
        // autolinker: 'autolinker',
        // babelpolyfill: 'babel-polyfill',
        // phonetic: 'phonetic'
    },
    output: {
        path: path.join(__dirname, 'dist'),
        filename: '[name]bundle.js'
    },
    // Modulkonfiguration fuer alle Dateitypen, welcher Loader soll verwendet werden
    module: {
        loaders: webjsConfig.webpackLoaders
    },
    resolve: {
        fallback: path.join(__dirname, 'node_modules')
    },
    resolveLoader: {fallback: path.join(__dirname, 'node_modules')}
};

/**
 * Production Konfigurationsdatei fuer Webpack (der Teil, der nur fuer den produktiven Build ist)
 * @type {} Webpack Konfiguration
 */
var production = extend({}, commonConfig, {
    output: {
        path: path.join(__dirname, 'target/build'),
        filename: '[name]_[hash].js'
    },
    plugins: [
        new ngAnnotatePlugin({add: true}),
        new webpack.optimize.DedupePlugin(),
        new webpack.NoErrorsPlugin(),
        new webpack.optimize.UglifyJsPlugin({
            minimize: true,
            compress: {
                warnings: true
            },
            sourceMap: false
        }),
        // add js files
        new HtmlWebpackPlugin({
            template: '../index.html'
        })
    ]
});

// development config

// forward requests (you may also have to change  "backend_url" in app.js
var proxyOptions = url.parse('http://localhost:8080');
proxyOptions.route = '/backend.php';

var development = extend({}, commonConfig, {
    plugins: [
        new browserSyncPlugin({
            proxy: 'localhost:3000',
            middleware: proxyMiddleware(proxyOptions)
        }),
        new HtmlWebpackPlugin({
            template: '../index.html'
        })
    ],
    watch: true,
    devtool: 'source-map'
});

module.exports = {production: production, development: development};