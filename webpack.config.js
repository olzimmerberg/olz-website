/* global __dirname, module, require */
/* exported module */

const path = require('path');
const webpack = require('webpack');
const WebpackShellPlugin = require('webpack-shell-plugin');

const defaultConfig = {
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.ts$/,
                use: 'ts-loader',
                exclude: /node_modules/,
            },
            {
                test: /\.js$/,
                loader: 'babel-loader',
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader'],
            },
            {
                test: /\.s[ac]ss$/i,
                use: ['style-loader', 'css-loader', 'sass-loader'],
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
    resolve: {
        extensions: ['.d.ts', '.ts', '.js', '.json'],
    },
    plugins: [
        new webpack.ProvidePlugin({
            '$':'jquery',
            'jQuery':'jquery',
            'window.jQuery':'jquery',
        }),
        new WebpackShellPlugin({
            onBuildStart: ['php ./src/api/client/generate.php'],
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
        entry: './src/index.ts',
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
    {
        ...defaultConfig,
        entry: './src/resultate/live_uploader/public_html/index.js',
        output: {
            path: path.resolve(__dirname, 'src/resultate/live_uploader/public_html/jsbuild'),
            publicPath: './jsbuild/',
            filename: 'olz-results.min.js',
            library: 'olzResults',
        },
    },
];
