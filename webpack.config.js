/* global __dirname, module, require */
/* exported module */

const path = require('path');
const webpack = require('webpack');
const WebpackShellPluginNext = require('webpack-shell-plugin-next');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const {StatsWriterPlugin} = require('webpack-stats-plugin');

const defaultConfig = {
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.(ts|tsx)$/,
                use: 'ts-loader',
                exclude: /node_modules/,
            },
            {
                test: /\.(js|jsx)$/,
                loader: 'babel-loader',
            },
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader',
                ],
            },
            {
                test: /\.(png|gif)$/,
                type: 'asset/resource',
            },
            {
                test: /\.(ttf|woff(|2)|eot|svg)$/,
                type: 'asset/resource',
            },
        ],
    },
    resolve: {
        extensions: ['.ts', '.tsx', '.js', '.jsx', '.json'],
    },
    plugins: [
        new webpack.ProvidePlugin({
            '$': 'jquery',
            'jQuery': 'jquery',
            'window.jQuery': 'jquery',
        }),
        new MiniCssExtractPlugin({
            filename: '[name].min.css',
        }),
        new WebpackShellPluginNext({
            onBuildStart: {
                scripts: ['php ./src/api/client/generate.php'],
                blocking: true,
                parallel: false,
            },
        }),
        new StatsWriterPlugin({
            fields: null,
            stats: {chunkModules: true},
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
            filename: '[name].min.js',
            library: 'olz',
            libraryTarget: 'window',
        },
    },
    {
        ...defaultConfig,
        entry: './src/anmelden/index.tsx',
        output: {
            path: path.resolve(__dirname, 'src/anmelden/jsbuild'),
            publicPath: '/_/anmelden/jsbuild/',
            filename: '[name].min.js',
            library: 'olzAnmelden',
            libraryTarget: 'window',
        },
    },
    {
        ...defaultConfig,
        entry: './src/resultate/index.ts',
        output: {
            path: path.resolve(__dirname, 'src/resultate/jsbuild'),
            publicPath: '/_/resultate/jsbuild/',
            filename: '[name].min.js',
            library: 'olzResults',
            libraryTarget: 'window',
        },
    },
    {
        ...defaultConfig,
        entry: './src/resultate/live_uploader/public_html/index.ts',
        output: {
            path: path.resolve(__dirname, 'src/resultate/live_uploader/public_html/jsbuild'),
            publicPath: './jsbuild/',
            filename: '[name].min.js',
            library: 'olzResults',
            libraryTarget: 'window',
        },
    },
];
