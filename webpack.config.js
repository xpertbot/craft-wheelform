const webpack = require('webpack');
const path = require('path');

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
            }
            // {
            //     test: /\.scss$/,
            //     use: [
            //         "sass-loader"
            //     ]
            // }
        ]
    }
}
