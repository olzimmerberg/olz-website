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
        './src/Api/': percentCoverage(40),
        './src/Components/': percentCoverage(50),
        './src/Components/Common': percentCoverage(50),
        './src/Utils/': percentCoverage(100),
    },
};
export default jestConfig;
