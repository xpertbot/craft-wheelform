const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    entry: './resources/js/entry.js',
    output: {
        path: path.resolve(__dirname, './src/assets/js'),
        filename: 'wheelform-bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                loader: 'babel-loader'
            },
            {
                test:/\.(s*)css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: ['style-loader','css-loader', 'sass-loader']
                })
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin({
            filename: './src/assets/css/cp-wheelform.css',
            allChunks: true
        })
    ]
}
