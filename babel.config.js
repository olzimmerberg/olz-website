/* global module */
/* exported module */

module.exports = (api) => {
    api.cache(true);

    const presets = [
        '@babel/preset-env',
        '@babel/preset-react',
    ];
    const plugins = [
        '@babel/proposal-class-properties',
        '@babel/proposal-object-rest-spread',
    ];

    return {
        presets: presets,
        plugins: plugins,
    };
};
