const debug = (process.env.NODE_ENV !== 'production');
const webpack = require('webpack');
const path = require('path');

module.exports = {
    context: path.join(__dirname, "resources", "assets", "js", "admin"),
    devtool: debug ? "inline-sourcemap" : null,
    cache: false,
    entry: ["babel-polyfill", "./main.js"],
    resolve: {
        alias: {
            components: path.join(__dirname, "resources", "assets", "js", "admin", "components"),
            config: path.join(__dirname, "resources", "assets", "js", "admin", "config"),
            forms: path.join(__dirname, "resources", "assets", "js", "admin", "forms"),
            stores: path.join(__dirname, "resources", "assets", "js", "admin", "stores"),
            actions: path.join(__dirname, "resources", "assets", "js", "admin", "actions"),
            common: path.join(__dirname, "resources", "assets", "js")
        }
    },
    module: {
        loaders: [
            {
                test: /\.jsx?$/,
                exclude: /(node_modules|bower_components)/,
                loader: 'babel-loader',
                query: {
                    presets: ['react', 'es2015', 'stage-0'],
                    plugins: ['react-html-attrs', 'transform-decorators-legacy', 'transform-class-properties', 'transform-object-assign']
                }
            }
        ]
    },

    output: {
        path: path.join(__dirname, 'public', 'js', 'admin'),
        filename: "combined.min.js"
    },
    plugins: debug ? [] : [
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: JSON.stringify('production')
            }
        }),
        new webpack.optimize.DedupePlugin(),
        new webpack.optimize.OccurenceOrderPlugin(),
        new webpack.optimize.UglifyJsPlugin({ mangle: false, sourcemap: false })
    ]
};