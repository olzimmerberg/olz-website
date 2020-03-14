/* global __dirname, module, require */
/* exported module */

const path = require('path');

module.exports = [
    {
        entry: './src/index.js',
        output: {
            path: path.resolve(__dirname, 'src/jsbuild'),
            filename: 'olz.min.js',
            library: 'olz',
        },
        mode: 'development',
        module: {
            rules: [
                {
                    test: /\.js$/,
                    // exclude: /node_modules/,
                    include: [
                        path.resolve(__dirname, 'src'),
                    ],
                    loader: 'babel-loader',
                },
            ],
        },
        plugins: [],
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
