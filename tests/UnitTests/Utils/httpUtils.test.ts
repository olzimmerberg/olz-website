/* eslint-env jasmine */

import {isBot} from '../../../src/Utils/httpUtils';

describe('isBot', () => {
    // see jestEnv.js

    it('returns true for bot', () => {
        expect(isBot('Is_a_Bot')).toBe(true);
    });

    it('returns false for browser', () => {
        expect(isBot('Not_a_Bot')).toBe(false);
    });
});
