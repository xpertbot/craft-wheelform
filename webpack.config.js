const webpack = require('webpack');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
    entry: ['./resources/js/entry.js', './resources/sass/cp-wheelform.scss'],
    output: {
        path: path.resolve(__dirname, './src/assets'),
        filename: 'js/wheelform-bundle.js'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader',
                options: {
                    loaders: {
                    }
                }
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
            },
            {
                test:/\.(s*)css$/,
                use: ExtractTextPlugin.extract({
                    fallback: 'style-loader',
                    use: [
                        { loader: 'css-loader', options: { importLoaders: 1 } },
                        'postcss-loader',
                        {
                            loader: 'sass-loader',
                            options: {
                                implementation: require("sass")
                            }
                        },
                    ]
                })
            }
        ]
    },
    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js',
        },
        extensions: ['*', '.js', '.vue', '.json']
    },
    plugins: [
        new ExtractTextPlugin({
            filename: './css/cp-wheelform.css',
            allChunks: true
        }),
        new VueLoaderPlugin(),
        require('autoprefixer')
    ]
}
