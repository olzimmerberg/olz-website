/* global __dirname, module, require */
/* exported module */

const path = require('path');
const webpack = require('webpack');
const WebpackShellPlugin = require('webpack-shell-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

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
                use: ['file-loader'],
            },
            {
                test: /\.(ttf|woff(|2)|eot|svg)$/,
                use: ['file-loader'],

            },
        ],
    },
    resolve: {
        extensions: ['.d.ts', '.ts', '.tsx', '.js', '.jsx', '.json'],
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
            filename: '[name].min.js',
            library: 'olz',
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
        },
    },
];
