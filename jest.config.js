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
        './src/': percentCoverage(6),
        './src/api/': percentCoverage(13),
        './src/components/': percentCoverage(9),
        './src/components/auth': percentCoverage(0),
        './src/components/common': percentCoverage(60),
        './src/components/notify': percentCoverage(0),
        './src/components/page': percentCoverage(0),
        './src/components/users': percentCoverage(0),
        './src/components/verein': percentCoverage(0),
        './src/features/': percentCoverage(50),
        './src/library/': percentCoverage(0),
        './src/scripts/': percentCoverage(0),
        './src/utils/': percentCoverage(100),
    },
};
module.exports = jestConfig;
