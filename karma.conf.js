var ip = require('ip');
var webjsConfig = require('./shared.build.config');

module.exports = function (config) {

    var seleniumWebgrid = {
        hostname: 'webtestgrid.myhost.com',
        port: 4444
    };

    config.set({

        hostname: ip.address(),

        basePath: __dirname,

        // frameworks to use
        // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
        frameworks: ['jasmine'],

        /* list of files/patterns to load in the browser
         Fuer den Phantomjs wird fuer die Methode 'bind' ein Polyfill geladen
         da der Browser die Methode nicht kennt und diese von den Frameworks verwendet wird */
        files: [
            './node_modules/phantomjs-polyfill/bind-polyfill.js', {
                pattern: 'spec.bundle.js', watched: false
            }
        ],

        // files to exclude
        exclude: [],

        // preprocess matching files before serving them to the browser
        // available preprocessors: https://npmjs.org/browse/keyword/karma-preprocessor
        preprocessors: {
            'spec.bundle.js': ['webpack', 'sourcemap', 'coverage']
        },

        webpack: {
            debug: true, devtool: 'inline-source-map', module: {
                loaders: webjsConfig.webpackLoaders
            }
        },

        webpackServer: {
            // prevent console spamming when running in Karma!
            noInfo: true
        },

        // available reporters: https://npmjs.org/browse/keyword/karma-reporter
        reporters: ['progress', 'junit', 'coverage'],

        // enable colors in the output
        colors: true,

        // level of logging
        // possible values: config.LOG_DISABLE || config.LOG_ERROR || config.LOG_WARN || config.LOG_INFO || config.LOG_DEBUG
        logLevel: config.LOG_INFO,

        // toggle whether to watch files and rerun tests upon incurring changes
        autoWatch: false,

        // Browser-Konfiguration auf dem Selenium Grid
        customLaunchers: {
            'SeleniumCH': {
                base: 'WebDriver',
                config: seleniumWebgrid,
                browserName: 'chrome'
            },
            'SeleniumFF': {
                base: 'WebDriver',
                config: seleniumWebgrid,
                browserName: 'firefox'
            },
            'SeleniumIE': {
                base: 'WebDriver',
                config: seleniumWebgrid,
                browserName: 'internet explorer',
                'x-ua-compatible': 'IE=edge'
            }
        },

        // start these browsers
        // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
        browsers: ['PhantomJS'], // Test auf dem PhantomJS
        // browsers: ['SeleniumFF', 'SeleniumCH', 'SeleniumIE'], // Test auf dem Selenium-Webgrid

        // if true, Karma runs tests once and exits
        singleRun: true,

        plugins: [
            'karma-junit-reporter',
            'karma-jasmine',
            'karma-coverage',
            'karma-phantomjs-launcher',
            'karma-webpack',
            'karma-sourcemap-loader',
            'karma-webdriver-launcher'
        ],

        // Coverage & JUnit Report fuer SonarQube
        junitReporter: {
            outputDir: 'target/surefire', suite: 'unit'
        }, coverageReporter: {
            reporters: [
                {
                    type: 'lcov', dir: 'target/surefire', subdir: '.'
                }
            ]
        }
    });
};
