var webpack = require('webpack');
var path = require('path');
var HtmlWebpackPlugin = require('html-webpack-plugin');
var validate = require('webpack-validator');
var merge = require('webpack-merge');

var TARGET = process.env.npm_lifecycle_event;

// based on https://github.com/gaearon/react-hot-boilerplate
const commonConfig = {
    context: path.resolve(__dirname, 'src'),
    entry: [
        './app.js'
    ],
    output: {
        path: path.join(__dirname, 'dist'),
        filename: 'bundle_[hash].js'
    },
    plugins: [
        new webpack.NoErrorsPlugin(),
        new HtmlWebpackPlugin({
            template: './index.html'
        })
    ],
    module: {
        loaders: [
            {
                test: /\.json$/, loader: 'json'
            }, {
                test: /\.html$/, loader: 'html'
            }, {
                test: /\.css$/, loader: 'style!css'
            }, {
                test: /\.scss$/, loader: 'style!css!sass'
            }, {
                test: /\.(jpe?g|png|gif|svg)$/i, loader: 'url'
            }, {
                test: /\.(woff|woff2)$/, loader: 'url?mimetype=application/font-woff'
            }, {
                test: /\.ttf$/, loader: 'url'
            }, {
                test: /\.eot$/, loader: 'url'
            }

        ]
    }
};

var config;
switch (TARGET) {
    case 'size':
    case 'build':
        config = merge(commonConfig, {
            plugins: [
                new webpack.optimize.DedupePlugin(),
                new webpack.optimize.UglifyJsPlugin({
                    minimize: true,
                    compress: {
                        warnings: false
                    }
                }),
                new webpack.DefinePlugin({
                    DEVELOPMENT: JSON.stringify(false)
                }),
                new webpack.DefinePlugin({
                    'process.env': {
                        'NODE_ENV': JSON.stringify('production')
                    }
                })

            ],
            // without react-hot in prod
            module: {
                loaders: [
                    {
                        test: /\.js$/, exclude: [/node_modules/],
                        loader: 'babel',
                        query: {
                            // https://github.com/babel/babel-loader#options
                            cacheDirectory: true,
                            presets: ['es2015']
                        }
                    }
                ]
            }
        });
        break;
    default:
        // develop
        config = merge(commonConfig, {
            devtool: 'eval',
            plugins: [
                new webpack.HotModuleReplacementPlugin(),
                new webpack.DefinePlugin({
                    DEVELOPMENT: JSON.stringify(true)
                })
            ],
            module: {
                loaders: [
                    {
                        test: /\.js$/,
                        loaders: ['babel'],
                        include: path.join(__dirname, 'src')
                    },
                ]
            }

        });
        // replace entry instead of merge
        config.entry = [
            'webpack-dev-server/client?http://localhost:3000',
            'webpack/hot/only-dev-server',
            './app.js'
        ];
}

if (TARGET === "size") {
    // no validation with size target
    module.exports = config;
} else {
    module.exports = validate(config);
}

