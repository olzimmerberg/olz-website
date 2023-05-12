/* eslint-env jasmine */

import {ValidationError} from '../../../../src/Api/client';
import {EMAIL_REGEX, FieldResult, isFieldResult, validFieldResult, invalidFieldResult, FieldResultOrDictThereof, isFieldResultOrDictThereofValid, getFieldResultOrDictThereofErrors, getFieldResultOrDictThereofValue, validFormData, invalidFormData, getDataForRequest, camelCaseToDashCase, getAsserted, getCountryCode, getEmail, getFormField, getGender, getIsoDateTime, getIsoDate, getIsoTime, getPassword, getPhone, getRequired, getStringOrEmpty, getStringOrNull} from './OlzDefaultForm';

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

describe('FieldResult', () => {
    describe('isFieldResult', () => {
        function isString(thing: unknown): thing is string {
            return typeof thing === 'string';
        }

        it('returns true for ValidFieldResult', () => {
            const fieldResult: FieldResult<string> = {
                fieldId: 'asdf',
                isValid: true,
                value: 'test',
                errors: [],
            };
            expect(isFieldResult(fieldResult)).toBe(true);
        });

        it('returns true for InvalidFieldResult', () => {
            const fieldResult: FieldResult<string> = {
                fieldId: 'asdf',
                isValid: false,
                value: null,
                errors: [new ValidationError('', {})],
            };
            expect(isFieldResult(fieldResult)).toBe(true);
        });

        it('returns true for undefined value', () => {
            expect(isFieldResult({
                fieldId: 'asdf',
                isValid: true,
                errors: [],
            })).toBe(true);
        });

        it('returns true for succeeding value type check', () => {
            const fieldResult: FieldResult<string> = {
                fieldId: 'asdf',
                isValid: true,
                value: 'test',
                errors: [],
            };
            expect(isFieldResult(fieldResult, isString)).toBe(true);
        });

        it('returns false for failing value type check', () => {
            const fieldResult: FieldResult<number> = {
                fieldId: 'asdf',
                isValid: true,
                value: 3,
                errors: [],
            };
            expect(isFieldResult(fieldResult, isString)).toBe(false);
        });

        it('returns false for other things', () => {
            expect(isFieldResult(undefined)).toBe(false);
            expect(isFieldResult(null)).toBe(false);
            expect(isFieldResult(false)).toBe(false);
            expect(isFieldResult(true)).toBe(false);
            expect(isFieldResult(0)).toBe(false);
            expect(isFieldResult(1)).toBe(false);
            expect(isFieldResult(() => 1)).toBe(false);
            expect(isFieldResult('')).toBe(false);
            expect(isFieldResult('test')).toBe(false);
            expect(isFieldResult([])).toBe(false);
            expect(isFieldResult({})).toBe(false);
            expect(isFieldResult({
                isValid: true,
                value: 3,
                errors: [],
            })).toBe(false);
            expect(isFieldResult({
                fieldId: 'asdf',
                value: 3,
                errors: [],
            })).toBe(false);
            expect(isFieldResult({
                fieldId: 'asdf',
                isValid: true,
                value: 3,
            })).toBe(false);
        });
    });

    describe('validFieldResult', () => {
        it('returns ValidFieldResult', () => {
            const expectedFieldResult: FieldResult<number> = {
                fieldId: 'asdf',
                isValid: true,
                value: 3,
                errors: [],
            };
            expect(validFieldResult('asdf', 3)).toEqual(expectedFieldResult);
        });
    });

    describe('invalidFieldResult', () => {
        it('returns ValidFieldResult', () => {
            const expectedFieldResult: FieldResult<number> = {
                fieldId: 'asdf',
                isValid: false,
                value: null,
                errors: [new ValidationError('', {})],
            };
            expect(invalidFieldResult('asdf', [new ValidationError('', {})], null))
                .toEqual(expectedFieldResult);
        });
    });
});

describe('FieldResultOrDictThereof', () => {
    const validFieldResult1: FieldResultOrDictThereof<number> = {
        fieldId: 'asdf',
        isValid: true,
        value: 3,
        errors: [],
    };
    const invalidFieldResult1: FieldResultOrDictThereof<number> = {
        fieldId: 'qwer',
        isValid: false,
        value: null,
        errors: [new ValidationError('', {})],
    };

    const dictOfValidFieldResult1: FieldResultOrDictThereof<{valid: number, alsoValid: number}> = {
        valid: validFieldResult1,
        alsoValid: validFieldResult1,
    };
    const dictOfInvalidFieldResult1: FieldResultOrDictThereof<{valid: number, invalid: number}> = {
        valid: validFieldResult1,
        invalid: invalidFieldResult1,
    };

    describe('isFieldResultOrDictThereofValid', () => {
        it('returns true for ValidFieldResult', () => {
            expect(isFieldResultOrDictThereofValid(validFieldResult1))
                .toEqual(true);
        });

        it('returns false for InvalidFieldResult', () => {
            expect(isFieldResultOrDictThereofValid(invalidFieldResult1))
                .toEqual(false);
        });

        it('returns true for dict of ValidFieldResult', () => {
            expect(isFieldResultOrDictThereofValid(dictOfValidFieldResult1))
                .toEqual(true);
        });

        it('returns false for dict of InvalidFieldResult', () => {
            expect(isFieldResultOrDictThereofValid(dictOfInvalidFieldResult1))
                .toEqual(false);
        });
    });

    describe('getFieldResultOrDictThereofErrors', () => {
        it('returns empty array for ValidFieldResult', () => {
            expect(getFieldResultOrDictThereofErrors(validFieldResult1))
                .toEqual([]);
        });

        it('returns errors for InvalidFieldResult', () => {
            expect(getFieldResultOrDictThereofErrors(invalidFieldResult1))
                .toEqual([new ValidationError('', {})]);
        });

        it('returns empty array for dict of ValidFieldResult', () => {
            expect(getFieldResultOrDictThereofErrors(dictOfValidFieldResult1))
                .toEqual([]);
        });

        it('returns errors for dict of InvalidFieldResult', () => {
            expect(getFieldResultOrDictThereofErrors(dictOfInvalidFieldResult1))
                .toEqual([new ValidationError('', {})]);
        });
    });

    describe('getFieldResultOrDictThereofValue', () => {
        it('returns value for ValidFieldResult', () => {
            expect(getFieldResultOrDictThereofValue(validFieldResult1))
                .toEqual(3);
        });

        it('returns value for InvalidFieldResult', () => {
            expect(() => getFieldResultOrDictThereofValue(invalidFieldResult1))
                .toThrow();
        });

        it('returns value for dict of ValidFieldResult', () => {
            expect(getFieldResultOrDictThereofValue(dictOfValidFieldResult1))
                .toEqual({valid: 3, alsoValid: 3});
        });

        it('returns value for dict of InvalidFieldResult', () => {
            expect(() => getFieldResultOrDictThereofValue(dictOfInvalidFieldResult1))
                .toThrow();
        });
    });
});


describe('FormDataForRequest', () => {
    describe('validFormData', () => {
        it('returns ValidFormDataForRequest', () => {
            expect(validFormData({})).toEqual({isValid: true, data: {}});
        });
    });

    describe('invalidFormData', () => {
        it('returns ValidFormDataForRequest', () => {
            expect(invalidFormData([new ValidationError('', {})]))
                .toEqual({isValid: false, errors: [new ValidationError('', {})]});
        });
    });
});

// describe('olzDefaultFormSubmit', () => {
//     it('works', () => {
//         olzDefaultFormSubmit(
//             'signUpWithPassword',
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
            (f: HTMLFormElement) => ({
                isValid: true,
                data: {
                    usernameOrEmail: f.foo.value,
                    password: f.bar.value,
                    rememberMe: f.gurr.value,
                },
            }),
            {
                foo: {value: 'foo'},
                bar: {value: 'bar'},
                gurr: {value: false},
            } as unknown as HTMLFormElement,
        )).toEqual({
            usernameOrEmail: 'foo',
            password: 'bar',
            rememberMe: false,
        });
    });

    it('throws validation error when there are validation errors', () => {
        expect(() => getDataForRequest(
            () => ({
                isValid: false,
                errors: [
                    new ValidationError('err1', {err1: ['testerror1!']}),
                ],
            }),
            {
                foo: {value: 'foo'},
            } as unknown as HTMLFormElement,
        )).toThrow(ValidationError);
    });

    it('throws error when there are non-validation errors', () => {
        expect(() => getDataForRequest(
            () => {
                throw new Error('');
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

describe('getAsserted', () => {
    it('returns input when assertion is true', () => {
        expect(getAsserted(
            () => true,
            'test error',
            validFieldResult('assert', ''),
        ))
            .toEqual(validFieldResult('assert', ''));
    });

    it('returns error when assertion is false', () => {
        expect(getAsserted(
            () => false,
            'test error',
            validFieldResult('assert', ''),
        ))
            .toEqual(invalidFieldResult('assert', [new ValidationError('', {})], ''));
    });
});

describe('getCountryCode', () => {
    it('returns null for nullish user inputs', () => {
        expect(getCountryCode(validFieldResult('countryCode', '')))
            .toEqual(validFieldResult('countryCode', null));
    });

    it('returns countryCode for correct user inputs', () => {
        expect(getCountryCode(validFieldResult('countryCode', 'CH')))
            .toEqual(validFieldResult('countryCode', 'CH'));
        expect(getCountryCode(validFieldResult('countryCode', 'DE')))
            .toEqual(validFieldResult('countryCode', 'DE'));
        expect(getCountryCode(validFieldResult('countryCode', 'US \t')))
            .toEqual(validFieldResult('countryCode', 'US'));
    });

    it('returns countryCode for some country names', () => {
        expect(getCountryCode(validFieldResult('countryCode', ' Switzerland')))
            .toEqual(validFieldResult('countryCode', 'CH'));
        expect(getCountryCode(validFieldResult('countryCode', 'Schweiz')))
            .toEqual(validFieldResult('countryCode', 'CH'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getCountryCode(validFieldResult('countryCode', 'P')))
            .toEqual(invalidFieldResult('countryCode', [new ValidationError('', {})], 'P'));
        expect(getCountryCode(validFieldResult('countryCode', 'WTF')))
            .toEqual(invalidFieldResult('countryCode', [new ValidationError('', {})], 'WTF'));
        expect(getCountryCode(validFieldResult('countryCode', 'not.a.country')))
            .toEqual(invalidFieldResult('countryCode', [new ValidationError('', {})], 'not.a.country'));
    });
});

describe('getEmail', () => {
    it('returns null for nullish user inputs', () => {
        expect(getEmail(validFieldResult('email', '')))
            .toEqual(validFieldResult('email', null));
        expect(getEmail(validFieldResult('email', ' ')))
            .toEqual(validFieldResult('email', null));
        expect(getEmail(validFieldResult('email', '\t')))
            .toEqual(validFieldResult('email', null));
    });

    it('returns email adress for correct user inputs', () => {
        expect(getEmail(validFieldResult('email', 'test.adress@olzimmerberg.ch')))
            .toEqual(validFieldResult('email', 'test.adress@olzimmerberg.ch'));
        expect(getEmail(validFieldResult('email', 'plus+adress@some-hoster.tv')))
            .toEqual(validFieldResult('email', 'plus+adress@some-hoster.tv'));
        expect(getEmail(validFieldResult('email', ' whitespace@being-trimmed.org \t')))
            .toEqual(validFieldResult('email', 'whitespace@being-trimmed.org'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getEmail(validFieldResult('email', 'not.an.email')))
            .toEqual(invalidFieldResult('email', [new ValidationError('', {})], 'not.an.email'));
        expect(getEmail(validFieldResult('email', 'also@weird')))
            .toEqual(invalidFieldResult('email', [new ValidationError('', {})], 'also@weird'));
    });
});

describe('getGender', () => {
    it('returns null for nullish user inputs', () => {
        expect(getGender(validFieldResult('gender', '')))
            .toEqual(validFieldResult('gender', null));
        expect(getGender(validFieldResult('gender', null)))
            .toEqual(validFieldResult('gender', null));
        expect(getGender(validFieldResult('gender', undefined)))
            .toEqual(validFieldResult('gender', null));
    });

    it('returns gender for correct user inputs', () => {
        expect(getGender(validFieldResult('gender', 'M')))
            .toEqual(validFieldResult('gender', 'M'));
        expect(getGender(validFieldResult('gender', 'F')))
            .toEqual(validFieldResult('gender', 'F'));
        expect(getGender(validFieldResult('gender', 'O')))
            .toEqual(validFieldResult('gender', 'O'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getGender(validFieldResult('gender', 'not.a.gender')))
            .toEqual(invalidFieldResult('gender', [new ValidationError('', {})], null));
        expect(getGender(validFieldResult('gender', 'P')))
            .toEqual(invalidFieldResult('gender', [new ValidationError('', {})], null));
    });
});

describe('getIsoDateTime', () => {
    it('returns null for nullish user inputs', () => {
        expect(getIsoDateTime(validFieldResult('datetime', '')))
            .toEqual(validFieldResult('datetime', null));
    });

    it('returns datetime for correct swiss user inputs', () => {
        expect(getIsoDateTime(validFieldResult('datetime', '13.01.2006 18:03')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:03:00'));
        expect(getIsoDateTime(validFieldResult('datetime', '13.1.2006 18:03')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:03:00'));
        expect(getIsoDateTime(validFieldResult('datetime', '13. 1. 2006 18:43:36')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:43:36'));
        expect(getIsoDateTime(validFieldResult('datetime', ' 13. 1. 2006 \t 18:03 \t')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:03:00'));
    });

    it('returns datetime for correct ISO user inputs', () => {
        expect(getIsoDateTime(validFieldResult('datetime', '2006-01-13 18:03:00')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:03:00'));
        expect(getIsoDateTime(validFieldResult('datetime', '2006-1-13 18:03')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:03:00'));
        expect(getIsoDateTime(validFieldResult('datetime', '2006 - 1 - 13 18:43:36')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:43:36'));
        expect(getIsoDateTime(validFieldResult('datetime', ' 2006-\t1-13 \t 18:03 \t')))
            .toEqual(validFieldResult('datetime', '2006-01-13 18:03:00'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getIsoDateTime(validFieldResult('datetime', 'not.a.date')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], 'not.a.date'));
        expect(getIsoDateTime(validFieldResult('datetime', '2006.01.13 18:36')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], '2006.01.13 18:36'));
        expect(getIsoDateTime(validFieldResult('datetime', '01/13/2006 18:04')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], '01/13/2006 18:04'));
        expect(getIsoDateTime(validFieldResult('datetime', '32.1.2006 18:03')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], '32.1.2006 18:03'));
        expect(getIsoDateTime(validFieldResult('datetime', '30.1.2006 24:67')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], '30.1.2006 24:67'));
        expect(getIsoDateTime(validFieldResult('datetime', '13.01.06 18:03')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], '13.01.06 18:03'));
        expect(getIsoDateTime(validFieldResult('datetime', '13.01. 18:03')))
            .toEqual(invalidFieldResult('datetime', [new ValidationError('', {})], '13.01. 18:03'));
    });
});

describe('getIsoDate', () => {
    it('returns null for nullish user inputs', () => {
        expect(getIsoDate(validFieldResult('date', '')))
            .toEqual(validFieldResult('date', null));
    });

    it('returns date for correct swiss user inputs', () => {
        expect(getIsoDate(validFieldResult('date', '13.01.2006')))
            .toEqual(validFieldResult('date', '2006-01-13'));
        expect(getIsoDate(validFieldResult('date', '13.1.2006')))
            .toEqual(validFieldResult('date', '2006-01-13'));
        expect(getIsoDate(validFieldResult('date', '13. 1. 2006')))
            .toEqual(validFieldResult('date', '2006-01-13'));
        expect(getIsoDate(validFieldResult('date', ' 13. 1. 2006 \t')))
            .toEqual(validFieldResult('date', '2006-01-13'));
    });

    it('returns date for correct ISO user inputs', () => {
        expect(getIsoDate(validFieldResult('date', '2006-01-13')))
            .toEqual(validFieldResult('date', '2006-01-13'));
        expect(getIsoDate(validFieldResult('date', '2006-1-13')))
            .toEqual(validFieldResult('date', '2006-01-13'));
        expect(getIsoDate(validFieldResult('date', '2006 - 1 - 13')))
            .toEqual(validFieldResult('date', '2006-01-13'));
        expect(getIsoDate(validFieldResult('date', ' 2006-\t1-13 \t')))
            .toEqual(validFieldResult('date', '2006-01-13'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getIsoDate(validFieldResult('date', 'not.a.date')))
            .toEqual(invalidFieldResult('date', [new ValidationError('', {})], 'not.a.date'));
        expect(getIsoDate(validFieldResult('date', '2006.01.13')))
            .toEqual(invalidFieldResult('date', [new ValidationError('', {})], '2006.01.13'));
        expect(getIsoDate(validFieldResult('date', '01/13/2006')))
            .toEqual(invalidFieldResult('date', [new ValidationError('', {})], '01/13/2006'));
        expect(getIsoDate(validFieldResult('date', '32.1.2006')))
            .toEqual(invalidFieldResult('date', [new ValidationError('', {})], '32.1.2006'));
        expect(getIsoDateTime(validFieldResult('date', '13.01.06')))
            .toEqual(invalidFieldResult('date', [new ValidationError('', {})], '13.01.06'));
        expect(getIsoDateTime(validFieldResult('date', '13.01.')))
            .toEqual(invalidFieldResult('date', [new ValidationError('', {})], '13.01.'));
    });
});

describe('getIsoTime', () => {
    it('returns null for nullish user inputs', () => {
        expect(getIsoTime(validFieldResult('time', '')))
            .toEqual(validFieldResult('time', null));
    });

    it('returns date for correct user inputs', () => {
        expect(getIsoTime(validFieldResult('time', '18:03')))
            .toEqual(validFieldResult('time', '18:03:00'));
        expect(getIsoTime(validFieldResult('time', '18:3')))
            .toEqual(validFieldResult('time', '18:03:00'));
        expect(getIsoTime(validFieldResult('time', ' 8:\t20 \t')))
            .toEqual(validFieldResult('time', '08:20:00'));
        expect(getIsoTime(validFieldResult('time', '18:43:36')))
            .toEqual(validFieldResult('time', '18:43:36'));
        expect(getIsoTime(validFieldResult('time', ' \t 18:03 \t')))
            .toEqual(validFieldResult('time', '18:03:00'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getIsoTime(validFieldResult('time', 'not:a:time')))
            .toEqual(invalidFieldResult('time', [new ValidationError('', {})], 'not:a:time'));
        expect(getIsoTime(validFieldResult('time', '25:36')))
            .toEqual(invalidFieldResult('time', [new ValidationError('', {})], '25:36'));
        expect(getIsoTime(validFieldResult('time', '18:61')))
            .toEqual(invalidFieldResult('time', [new ValidationError('', {})], '18:61'));
    });
});

describe('getPassword', () => {
    it('returns null for nullish user inputs', () => {
        expect(getPassword(validFieldResult('password', '')))
            .toEqual(validFieldResult('password', null));
    });

    it('returns password for correct user inputs', () => {
        expect(getPassword(validFieldResult('password', 'longenough')))
            .toEqual(validFieldResult('password', 'longenough'));
        expect(getPassword(validFieldResult('password', 'also..ok')))
            .toEqual(validFieldResult('password', 'also..ok'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getPassword(validFieldResult('password', 'tooshor')))
            .toEqual(invalidFieldResult('password', [new ValidationError('', {})], 'tooshor'));
        expect(getPassword(validFieldResult('password', 'wtf')))
            .toEqual(invalidFieldResult('password', [new ValidationError('', {})], 'wtf'));
        expect(getPassword(validFieldResult('password', '1234')))
            .toEqual(invalidFieldResult('password', [new ValidationError('', {})], '1234'));
        expect(getPassword(validFieldResult('password', 'admin')))
            .toEqual(invalidFieldResult('password', [new ValidationError('', {})], 'admin'));
    });
});

describe('getPhone', () => {
    it('returns null for nullish user inputs', () => {
        expect(getPhone(validFieldResult('phone', '')))
            .toEqual(validFieldResult('phone', null));
        expect(getPhone(validFieldResult('phone', ' ')))
            .toEqual(validFieldResult('phone', null));
        expect(getPhone(validFieldResult('phone', '\t')))
            .toEqual(validFieldResult('phone', null));
    });

    it('returns password for correct user inputs', () => {
        expect(getPhone(validFieldResult('phone', '+41441234567')))
            .toEqual(validFieldResult('phone', '+41441234567'));
        expect(getPhone(validFieldResult('phone', ' +41 79 123 45 67')))
            .toEqual(validFieldResult('phone', '+41791234567'));
        expect(getPhone(validFieldResult('phone', '+41\t78\t1234567 \t')))
            .toEqual(validFieldResult('phone', '+41781234567'));
    });

    it('returns validation error for invalid user inputs', () => {
        expect(getPhone(validFieldResult('phone', 'no letters allowed')))
            .toEqual(invalidFieldResult('phone', [new ValidationError('', {})], 'no letters allowed'));
        expect(getPhone(validFieldResult('phone', '+')))
            .toEqual(invalidFieldResult('phone', [new ValidationError('', {})], '+'));
        expect(getPhone(validFieldResult('phone', '+ ')))
            .toEqual(invalidFieldResult('phone', [new ValidationError('', {})], '+ '));
        expect(getPhone(validFieldResult('phone', '123 45 67')))
            .toEqual(invalidFieldResult('phone', [new ValidationError('', {})], '123 45 67'));
        expect(getPhone(validFieldResult('phone', '01 123 45 67')))
            .toEqual(invalidFieldResult('phone', [new ValidationError('', {})], '01 123 45 67'));
        expect(getPhone(validFieldResult('phone', '044 765 43 21')))
            .toEqual(invalidFieldResult('phone', [new ValidationError('', {})], '044 765 43 21'));
    });
});

describe('getRequired', () => {
    it('returns value for non-null user inputs', () => {
        expect(getRequired(validFieldResult('fieldId', 'test')))
            .toEqual(validFieldResult('fieldId', 'test'));
        expect(getRequired(validFieldResult('fieldId', '')))
            .toEqual(validFieldResult('fieldId', ''));
        expect(getRequired(validFieldResult('fieldId', 1)))
            .toEqual(validFieldResult('fieldId', 1));
        expect(getRequired(validFieldResult('fieldId', 0)))
            .toEqual(validFieldResult('fieldId', 0));
        expect(getRequired(validFieldResult('fieldId', true)))
            .toEqual(validFieldResult('fieldId', true));
        expect(getRequired(validFieldResult('fieldId', false)))
            .toEqual(validFieldResult('fieldId', false));
    });

    it('returns validation error for nullish user inputs', () => {
        expect(getRequired(validFieldResult('fieldId', undefined)))
            .toEqual(invalidFieldResult('fieldId', [new ValidationError('', {})], undefined));
        expect(getRequired(validFieldResult('fieldId', null)))
            .toEqual(invalidFieldResult('fieldId', [new ValidationError('', {})], null));
    });
});

describe('getStringOrEmpty', () => {
    it('returns value for non-null user inputs', () => {
        expect(getStringOrEmpty(validFieldResult('fieldId', 'test')))
            .toEqual(validFieldResult('fieldId', 'test'));
    });

    it('returns empty string for nullish user inputs', () => {
        expect(getStringOrEmpty(validFieldResult('fieldId', undefined)))
            .toEqual(validFieldResult('fieldId', ''));
        expect(getStringOrEmpty(validFieldResult('fieldId', null)))
            .toEqual(validFieldResult('fieldId', ''));
        expect(getStringOrEmpty(validFieldResult('fieldId', '')))
            .toEqual(validFieldResult('fieldId', ''));
    });

    it('works with trimming', () => {
        expect(getStringOrEmpty(validFieldResult('fieldId', ' '), {trim: true}))
            .toEqual(validFieldResult('fieldId', ''));
        expect(getStringOrEmpty(validFieldResult('fieldId', ' '), {trim: false}))
            .toEqual(validFieldResult('fieldId', ' '));
    });
});

describe('getStringOrNull', () => {
    it('returns value for non-null user inputs', () => {
        expect(getStringOrNull(validFieldResult('fieldId', 'test')))
            .toEqual(validFieldResult('fieldId', 'test'));
    });

    it('returns empty string for nullish user inputs', () => {
        expect(getStringOrNull(validFieldResult('fieldId', undefined)))
            .toEqual(validFieldResult('fieldId', null));
        expect(getStringOrNull(validFieldResult('fieldId', null)))
            .toEqual(validFieldResult('fieldId', null));
        expect(getStringOrNull(validFieldResult('fieldId', '')))
            .toEqual(validFieldResult('fieldId', null));
    });

    it('works with trimming', () => {
        expect(getStringOrNull(validFieldResult('fieldId', ' '), {trim: true}))
            .toEqual(validFieldResult('fieldId', null));
        expect(getStringOrNull(validFieldResult('fieldId', ' '), {trim: false}))
            .toEqual(validFieldResult('fieldId', ' '));
    });
});

describe('getFormField', () => {
    it('returns error on inexistent field', () => {
        const form = {elements: {namedItem: (): undefined => undefined}} as unknown as HTMLFormElement;
        expect(getFormField(form, 'wtf'))
            .toEqual(invalidFieldResult('wtf', [new ValidationError('', {})], null));
    });

    it('returns error on invalid field', () => {
        const form = {elements: {namedItem: () => ({})}} as unknown as HTMLFormElement;
        expect(getFormField(form, 'wtf'))
            .toEqual(invalidFieldResult('wtf', [new ValidationError('', {})], null));
    });

    it('works for existing field', () => {
        const form = {elements: {namedItem: () => ({value: 'test'})}} as unknown as HTMLFormElement;
        expect(getFormField(form, 'wtf')).toEqual(validFieldResult('wtf', 'test'));
    });
});
