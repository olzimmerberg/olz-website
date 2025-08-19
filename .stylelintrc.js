/* global module */

module.exports = {
    'extends': 'stylelint-config-sass-guidelines',
    'plugins': [
        'stylelint-scss',
    ],
    'rules': {
        'max-nesting-depth': 10,
        'selector-max-id': 3,
        'selector-no-qualifying-type': null,
        'selector-max-compound-selectors': 5,
    },
};
