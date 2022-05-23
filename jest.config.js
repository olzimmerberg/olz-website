/* eslint-env node */

const percentCoverage = (percent) => ({
    branches: percent,
    functions: percent,
    lines: percent,
    statements: percent,
});

const jestConfig = {
    transform: {
        '^.+\\.tsx?$': 'ts-jest',
        '^.+\\.[t|j]sx?$': 'babel-jest',
    },
    testEnvironment: 'jsdom',
    testRegex: '.*/.*\\.test\\.tsx?',
    testPathIgnorePatterns: ['node_modules/', 'lib/', 'vendor/'],
    collectCoverage: true,
    maxConcurrency: 1,
    setupFiles: ['<rootDir>/jestEnv.js'],
    moduleNameMapper: {
        '^.+\\.(css|scss)$': '<rootDir>/jestIgnoreStub.js',
        '^typeface-open-sans$': '<rootDir>/jestIgnoreStub.js',
    },
    coverageThreshold: {
        './public/_/': percentCoverage(6),
        './public/_/api/': percentCoverage(13),
        './public/_/components/': percentCoverage(9),
        './public/_/components/auth': percentCoverage(0),
        './public/_/components/common': percentCoverage(53),
        './public/_/components/notify': percentCoverage(0),
        './public/_/components/page': percentCoverage(0),
        './public/_/components/users': percentCoverage(0),
        './public/_/components/verein': percentCoverage(0),
        './public/_/features/': percentCoverage(50),
        './public/_/library/': percentCoverage(0),
        './public/_/scripts/': percentCoverage(0),
        './public/_/utils/': percentCoverage(100),
    },
};
module.exports = jestConfig;
