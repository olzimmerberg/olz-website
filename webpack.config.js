/* global __dirname, module, require */
/* exported module */

const path = require('path');
const webpack = require('webpack');

const defaultConfig = {
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
                test: /\.(ttf|woff(|2)|eot|svg)$/,
                use: ['file-loader'],

            },
        ],
    },
    plugins: [
        new webpack.ProvidePlugin({
            '$':'jquery',
            'jQuery':'jquery',
            'window.jQuery':'jquery',
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
};

module.exports = [
    {
        ...defaultConfig,
        entry: './src/index.js',
        output: {
            path: path.resolve(__dirname, 'src/jsbuild'),
            publicPath: '/_/jsbuild/',
            filename: 'olz.min.js',
            library: 'olz',
        },
    },
    {
        ...defaultConfig,
        entry: './src/resultate/index.js',
        output: {
            path: path.resolve(__dirname, 'src/resultate/jsbuild'),
            publicPath: '/_/resultate/jsbuild/',
            filename: 'olz-results.min.js',
            library: 'olzResults',
        },
    },
];
