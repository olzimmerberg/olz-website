/* global __dirname, module, require */
/* exported module */

const fs = require('fs');
const path = require('path');
const WebpackShellPluginNext = require('webpack-shell-plugin-next');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const {StatsWriterPlugin} = require('webpack-stats-plugin');

const defaultConfig = {
    mode: 'development',
    module: {
        rules: [
            {
                test: /\.(ts|tsx)$/,
                use: [
                    {
                        loader: 'ts-loader',
                        options: {
                            experimentalWatchApi: true,
                        },
                    },
                ],
                exclude: /node_modules/,
            },
            {
                test: /\.(js|jsx)$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
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
        new MiniCssExtractPlugin({
            filename: '[name].min.css',
        }),
        new WebpackShellPluginNext({
            onBuildStart: {
                scripts: ['php ./src/Api/OlzApi.php'],
                blocking: true,
                parallel: false,
            },
        }),
        // Usage: npm run webpack-analyze ./path/to/stats.json
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

const appsPath = './src/Apps';
const apps = fs.readdirSync(appsPath);
const appConfigs = apps
    .filter((app) => fs.lstatSync(`${appsPath}/${app}`).isDirectory())
    .map((app) => {
        let extension = null;
        if (fs.existsSync(`${appsPath}/${app}/index.ts`)) {
            extension = 'ts';
        } else if (fs.existsSync(`${appsPath}/${app}/index.tsx`)) {
            extension = 'tsx';
        }
        if (!extension) {
            return null;
        }
        return {
            ...defaultConfig,
            entry: `${appsPath}/${app}/index.${extension}`,
            output: {
                path: path.resolve(__dirname, `public/jsbuild/app-${app}`),
                publicPath: `/jsbuild/app-${app}/`,
                filename: '[name].min.js',
                library: `olz${app}`,
                libraryTarget: 'window',
            },
        };
    })
    .filter((config) => config !== null);

module.exports = [
    {
        ...defaultConfig,
        entry: './_/index.ts',
        output: {
            path: path.resolve(__dirname, 'public/jsbuild/olz'),
            publicPath: '/jsbuild/olz/',
            filename: '[name].min.js',
            library: 'olz',
            libraryTarget: 'window',
        },
    },
    ...appConfigs,
    {
        ...defaultConfig,
        entry: './_/zimmerberg_ol/index.ts',
        output: {
            path: path.resolve(__dirname, 'public/jsbuild/zimmerberg_ol'),
            publicPath: '/jsbuild/zimmerberg_ol/',
            filename: '[name].min.js',
            library: 'olzZimmerbergOl',
            libraryTarget: 'window',
        },
    },
];
