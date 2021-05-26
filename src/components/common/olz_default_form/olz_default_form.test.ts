/* eslint-env jasmine */

import {ValidationError} from '../../../api/client';
import {EMAIL_REGEX, getDataForRequest, camelCaseToDashCase, getEmail, getGender, getIsoDateFromSwissFormat, getPassword, getCountryCode} from './olz_default_form';

describe('EMAIL_REGEX', () => {
    it('matches real email adresses', () => {
        expect(EMAIL_REGEX.exec('test.address@olzimmerberg.ch')).not.toBe(null);
        expect(EMAIL_REGEX.exec('plus+adress@some-hoster.tv')).not.toBe(null);
    });

    it('does not match invalid email adresses', () => {
        expect(EMAIL_REGEX.exec('')).toBe(null);
        expect(EMAIL_REGEX.exec('not.an.email')).toBe(null);
        expect(EMAIL_REGEX.exec('also@weird')).toBe(null);
    });
});

// describe('olzDefaultFormSubmit', () => {
//     it('works', () => {
//         olzDefaultFormSubmit(
//             OlzApiEndpoint.signUpWithPassword,
//             {
//                 firstName: () => '',
//                 lastName: () => '',
//                 username: () => '',
//                 password: () => '',
//                 email: () => '',
//                 gender: () => 'M' as const,
//                 birthdate: () => '',
//                 street: () => '',
//                 postalCode: () => '',
//                 city: () => '',
//                 region: () => '',
//                 countryCode: () => '',
//             },
//             {
//                 elements: [] as unknown as HTMLFormElement[],
//             } as unknown as HTMLFormElement,
//             () => 'erfolgreich',
//         );
//         expect(EMAIL_REGEX.exec('test.address@olzimmerberg.ch')).not.toBe(null);
//         expect(EMAIL_REGEX.exec('plus+adress@some-hoster.tv')).not.toBe(null);
//     });
// });

describe('getDataForRequest', () => {
    it('returns form data when there is no error', () => {
        expect(getDataForRequest(
            {
                foo: (f: HTMLFormElement) => f.foo.value,
                bar: (f: HTMLFormElement) => f.bar.value,
            },
            {
                foo: {value: 'foo'},
                bar: {value: 'bar'},
            } as unknown as HTMLFormElement,
        )).toEqual({foo: 'foo', bar: 'bar'});
    });

    it('throws validation error when there are validation errors', () => {
        expect(() => getDataForRequest(
            {
                foo: (f: HTMLFormElement) => f.foo.value,
                err1: () => {
                    throw new ValidationError('err1', {err1: 'testerror1!'});
                },
                err2: () => {
                    throw new ValidationError('err2', {err2: 'testerror2!'});
                },
            },
            {
                foo: {value: 'foo'},
            } as unknown as HTMLFormElement,
        )).toThrow(ValidationError);
    });

    it('throws error when there are non-validation errors', () => {
        expect(() => getDataForRequest(
            {
                foo: (f: HTMLFormElement) => f.foo.value,
                err: () => {
                    throw new Error('');
                },
            },
            {
                foo: {value: 'foo'},
            } as unknown as HTMLFormElement,
        )).toThrow(Error);
    });
});

// describe('showValidationErrors', () => {
//     it('shows validation errors', () => {
//         showValidationErrors(
//             {
//                 message: 'msg',
//                 validationErrors: {
//                     foo: ['foo1', 'foo2'],
//                     bar: ['bar'],
//                 },
//             },
//             {
//                 foo: {
//                     // value: 'foo',
//                     classList: {
//                         add: jest.fn(),
//                     },
//                     setAttribute: jest.fn(),
//                 },
//                 bar: 'bar',
//             } as unknown as HTMLFormElement,
//         );
//         expect({}).toEqual({foo: 'foo', bar: 'bar'});
//     });
// });

describe('camelCaseToDashCase', () => {
    it('kinda works', () => {
        expect(camelCaseToDashCase('foo')).toEqual('foo');
        expect(camelCaseToDashCase('camelCaseString')).toEqual('camel-case-string');
        expect(camelCaseToDashCase('caseWithNumbers123')).toEqual('case-with-numbers123');
    });
});

describe('getEmail', () => {
    it('returns null for nullish user inputs', () => {
        expect(getEmail('email', '')).toEqual(null);
    });

    it('returns email adress for correct user inputs', () => {
        expect(getEmail('email', 'test.adress@olzimmerberg.ch')).toEqual('test.adress@olzimmerberg.ch');
        expect(getEmail('email', 'plus+adress@some-hoster.tv')).toEqual('plus+adress@some-hoster.tv');
    });

    it('throws validation error for invalid user inputs', () => {
        expect(() => getEmail('email', 'not.an.email')).toThrow(ValidationError);
        expect(() => getEmail('email', 'also@weird')).toThrow(ValidationError);
    });
});

describe('getGender', () => {
    it('returns null for nullish user inputs', () => {
        expect(getGender('gender', '')).toEqual(null);
    });

    it('returns gender for correct user inputs', () => {
        expect(getGender('gender', 'M')).toEqual('M');
        expect(getGender('gender', 'F')).toEqual('F');
        expect(getGender('gender', 'O')).toEqual('O');
    });

    it('throws validation error for invalid user inputs', () => {
        expect(() => getGender('gender', 'not.a.gender')).toThrow(ValidationError);
        expect(() => getGender('gender', 'P')).toThrow(ValidationError);
    });
});

describe('getIsoDateFromSwissFormat', () => {
    it('returns null for nullish user inputs', () => {
        expect(getIsoDateFromSwissFormat('date', '')).toEqual(null);
    });

    it('returns date for correct user inputs', () => {
        expect(getIsoDateFromSwissFormat('date', '13.1.2006')).toEqual('2006-01-13 12:00:00');
        expect(getIsoDateFromSwissFormat('date', '13. 1. 2006')).toEqual('2006-01-13 12:00:00');
    });

    it('throws validation error for invalid user inputs', () => {
        expect(() => getIsoDateFromSwissFormat('date', 'not.a.date')).toThrow(ValidationError);
        expect(() => getIsoDateFromSwissFormat('date', '2006.01.13')).toThrow(ValidationError);
        expect(() => getIsoDateFromSwissFormat('date', '01/13/2006')).toThrow(ValidationError);
        expect(() => getIsoDateFromSwissFormat('date', '32.1.2006')).toThrow(ValidationError);
    });
});

describe('getPassword', () => {
    it('returns null for nullish user inputs', () => {
        expect(getPassword('password', '')).toEqual(null);
    });

    it('returns password for correct user inputs', () => {
        expect(getPassword('password', 'longenough')).toEqual('longenough');
        expect(getPassword('password', 'also..ok')).toEqual('also..ok');
    });

    it('throws validation error for invalid user inputs', () => {
        expect(() => getPassword('password', 'tooshor')).toThrow(ValidationError);
        expect(() => getPassword('password', 'wtf')).toThrow(ValidationError);
        expect(() => getPassword('password', '1234')).toThrow(ValidationError);
        expect(() => getPassword('password', 'admin')).toThrow(ValidationError);
    });
});

describe('getCountryCode', () => {
    it('returns null for nullish user inputs', () => {
        expect(getCountryCode('countryCode', '')).toEqual('');
    });

    it('returns countryCode for correct user inputs', () => {
        expect(getCountryCode('countryCode', 'CH')).toEqual('CH');
        expect(getCountryCode('countryCode', 'DE')).toEqual('DE');
        expect(getCountryCode('countryCode', 'US')).toEqual('US');
    });

    it('returns countryCode for some country names', () => {
        expect(getCountryCode('countryCode', ' Switzerland')).toEqual('CH');
        expect(getCountryCode('countryCode', 'Schweiz')).toEqual('CH');
    });

    it('throws validation error for invalid user inputs', () => {
        expect(() => getCountryCode('countryCode', 'P')).toThrow(ValidationError);
        expect(() => getCountryCode('countryCode', 'WTF')).toThrow(ValidationError);
        expect(() => getCountryCode('countryCode', 'not.a.country')).toThrow(ValidationError);
    });
});
