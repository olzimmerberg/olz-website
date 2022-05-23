module.exports = {
    'extends': 'stylelint-config-sass-guidelines',
    'plugins': [
        'stylelint-scss',
    ],
    'rules': {
        'block-closing-brace-newline-after': 'always',
        'block-closing-brace-newline-before': 'always',
        'block-opening-brace-newline-after': 'always',
        'indentation': 4,
        'max-nesting-depth': 10,
        'selector-max-id': 3,
        'selector-no-qualifying-type': null,
        'selector-max-compound-selectors': 5,
    },
};
