/* global __dirname, module, require */
/* exported module */

const path = require('path');
const webpack = require('webpack');

module.exports = [
    {
        entry: './src/index.js',
        output: {
            path: path.resolve(__dirname, 'src/jsbuild'),
            publicPath: '/_/jsbuild/',
            filename: 'olz.min.js',
            library: 'olz',
        },
        mode: 'development',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                },
                {
                    test: /\.css$/,
                    use: ['style-loader', 'css-loader'],
                },
                {
                    test: /\.(png|gif)$/,
                    use: ['file-loader'],
                },
                {
                    test: /\.(ttf|woff|eot|svg)$/,
                    use: ['file-loader'],

                },
            ],
        },
        plugins: [
            new webpack.ProvidePlugin({
                'jQuery': 'jquery',
            }),
        ],
        watchOptions: {
            aggregateTimeout: 300,
            poll: 1000,
        },
        stats: {
            colors: true,
        },
        devtool: 'source-map',
    },
];
