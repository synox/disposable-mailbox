/*
 * @author u215942 (Stefan Zeller)
 * @version: 1.0.1
 * @since 04.04.2016
 */

exports.webpackLoaders = [
    {
        test: /\.js$/, exclude: [/node_modules/],
        loader: 'babel',
        query: {
            // https://github.com/babel/babel-loader#options
            cacheDirectory: true,
            presets: ['es2015']
        }
    }, {
        test: /\.json$/, loader: 'json'
    }, {
        test: /\.html$/, loader: 'html'
    }, {
        test: /\.css$/, loader: 'style!css'
    }, {
        test: /\.(jpe?g|png|gif|svg)$/i, loader: 'url'
    }, {
        test: /\.(woff|woff2)$/, loader: 'url?mimetype=application/font-woff'
    }, {
        test: /\.ttf$/, loader: 'url'
    }, {
        test: /\.eot$/, loader: 'url'
    }
];