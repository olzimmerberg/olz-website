/* eslint-env jasmine */

import {getValidationErrorFromResponseText, ValidationError} from './ValidationError';

describe('getValidationErrorFromResponseText', () => {
    it('works when there is no reponse text', () => {
        expect(getValidationErrorFromResponseText(undefined)).toEqual(undefined);
        expect(getValidationErrorFromResponseText('')).toEqual(undefined);
    });

    it('works for invalid JSON', () => {
        expect(getValidationErrorFromResponseText('invalid json')).toEqual(undefined);
    });

    it('works for non-ValidationError JSON', () => {
        expect(getValidationErrorFromResponseText(JSON.stringify({}))).toEqual(undefined);
        expect(getValidationErrorFromResponseText(JSON.stringify({
            error: {},
        }))).toEqual(undefined);
        expect(getValidationErrorFromResponseText(JSON.stringify({
            error: {type: 'invalid'},
        }))).toEqual(undefined);
    });

    it('works without message', () => {
        expect(() => getValidationErrorFromResponseText(JSON.stringify({
            error: {
                type: 'ValidationError',
                validationErrors: ['test'],
            },
        }))).toThrow();
    });

    it('works without validation errors', () => {
        expect(() => getValidationErrorFromResponseText(JSON.stringify({
            error: {type: 'ValidationError'},
            message: 'test',
        }))).toThrow();
    });

    it('works ValidationError JSON', () => {
        expect(getValidationErrorFromResponseText(JSON.stringify({
            error: {
                type: 'ValidationError',
                validationErrors: ['testError'],
            },
            message: 'testMessage',
        }))).toEqual(new ValidationError('testMessage', ['testError']));
    });
});
