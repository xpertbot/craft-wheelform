const webpack = require('webpack');
const path = require('path');
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
                use: [
                        {
                            loader: 'file-loader',
                            options: {
                                name: 'css/[name].css',
                            }
                        },
                        {
                            loader: 'extract-loader'
                        },
                        {
                            loader: 'css-loader?url=false',
                            options: {
                                importLoaders: 1
                            }
                        },
                        {
                            loader: "postcss-loader",
                            options: {
                                postcssOptions: {
                                    plugins: [
                                        'autoprefixer'
                                    ]
                                }
                            }
                        },
                        {
                            loader: 'sass-loader',
                            options: {
                                implementation: require("sass"),
                                sassOptions: {
                                    fiber: false,
                                },
                            },
                        },
                    ]
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
        new VueLoaderPlugin()
    ]
}
