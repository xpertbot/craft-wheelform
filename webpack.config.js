const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');

module.exports = {
    entry: ['./resources/js/entry.js', './resources/sass/cp-wheelform.scss'],
    output: {
        path: path.resolve(__dirname, './src/assets'),
        filename: 'js/wheelform-bundle.js'
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
                    use: ['css-loader', 'sass-loader']
                })
            }
        ]
    },
    plugins: [
        new ExtractTextPlugin({
            filename: './css/cp-wheelform.css',
            allChunks: true
        })
    ]
}
