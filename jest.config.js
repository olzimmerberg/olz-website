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
        './_/': percentCoverage(6),
        './_/api/': percentCoverage(13),
        './_/components/': percentCoverage(9),
        './_/components/auth': percentCoverage(0),
        './_/components/common': percentCoverage(53),
        './_/components/notify': percentCoverage(0),
        './_/components/page': percentCoverage(0),
        './_/components/users': percentCoverage(0),
        './_/components/verein': percentCoverage(0),
        './_/features/': percentCoverage(50),
        './_/library/': percentCoverage(0),
        './_/scripts/': percentCoverage(0),
        './_/utils/': percentCoverage(100),
    },
};
module.exports = jestConfig;
