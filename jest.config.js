/* eslint-env node */

const percentCoverage = (percent) => ({
    branches: percent,
    functions: percent,
    lines: percent,
    statements: percent,
});

const jestConfig = {
    preset: 'ts-jest',
    transform: {
        '^.+\\.tsx?$': 'ts-jest',
        '^.+\\.jsx?$': 'babel-jest',
    },
    testEnvironment: 'jsdom',
    testRegex: '.*/.*\\.test\\.tsx?',
    testPathIgnorePatterns: ['node_modules/', 'lib/', 'vendor/'],
    transformIgnorePatterns: ['node_modules/(?!ol)'],
    collectCoverage: true,
    maxConcurrency: 1,
    setupFiles: ['<rootDir>/jestEnv.js'],
    moduleNameMapper: {
        '^.+\\.(css|scss)$': '<rootDir>/jestIgnoreStub.js',
        '^typeface-open-sans$': '<rootDir>/jestIgnoreStub.js',
    },
    coverageThreshold: {
        './src/Api/': percentCoverage(13),
        './src/Components/': percentCoverage(9),
        './src/Components/Auth': percentCoverage(0),
        './src/Components/Common': percentCoverage(29),
        './src/Utils/': percentCoverage(90),
    },
};
module.exports = jestConfig;
