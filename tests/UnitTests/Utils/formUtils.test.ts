/* eslint-env jasmine */

import * as formUtils from '../../../src/Utils/formUtils';

describe('EMAIL_REGEX', () => {
    it('matches real email adresses', () => {
        expect(formUtils.EMAIL_REGEX.exec('test.address@olzimmerberg.ch')).not.toBe(null);
        expect(formUtils.EMAIL_REGEX.exec('plus+adress@some-hoster.tv')).not.toBe(null);
    });

    it('does not match invalid email adresses', () => {
        expect(formUtils.EMAIL_REGEX.exec('')).toBe(null);
        expect(formUtils.EMAIL_REGEX.exec('not.an.email')).toBe(null);
        expect(formUtils.EMAIL_REGEX.exec('also@weird')).toBe(null);
    });
});

describe('getResolverResult', () => {
    const VALUES = {foo: 'foo value', bar: 'bar value'};
    const FIELD_ERROR = {type: 'validate', message: 'fake-error!'};

    it('works for no errors', () => {
        const result = formUtils.getResolverResult({foo: undefined}, VALUES);
        expect(result).toEqual({errors: {}, values: VALUES});
    });

    it('works for empty errors', () => {
        const result = formUtils.getResolverResult({}, {foo: 'foo value', bar: 'bar value'});
        expect(result).toEqual({errors: {}, values: VALUES});
    });

    it('works for errors', () => {
        const result = formUtils.getResolverResult({foo: FIELD_ERROR}, VALUES);
        expect(result).toEqual({errors: {foo: FIELD_ERROR}, values: {}});
    });
});

// Boolean

describe('getFormBoolean', () => {
    it('works for false', () => {
        expect(formUtils.getFormBoolean(false)).toEqual('');
    });

    it('works for true', () => {
        expect(formUtils.getFormBoolean(true)).toEqual('yes');
    });

    it('works for null', () => {
        expect(formUtils.getFormBoolean(null)).toEqual('');
    });

    it('works for undefined', () => {
        expect(formUtils.getFormBoolean(undefined)).toEqual('');
    });
});

describe('getApiBoolean', () => {
    it('works for false', () => {
        expect(formUtils.getApiBoolean('')).toEqual(false);
    });

    it('works for true', () => {
        expect(formUtils.getApiBoolean('yes')).toEqual(true);
    });
});

// Numeric

describe('getFormNumber', () => {
    it('works for 0', () => {
        expect(formUtils.getFormNumber(0)).toEqual('0');
    });

    it('works for 3.14', () => {
        expect(formUtils.getFormNumber(3.14)).toEqual('3.14');
    });

    it('works for null', () => {
        expect(formUtils.getFormNumber(null)).toEqual('');
    });

    it('works for undefined', () => {
        expect(formUtils.getFormNumber(undefined)).toEqual('');
    });
});

describe('validateInteger', () => {
    it('returns error for nullish user inputs', () => {
        expect(formUtils.validateInteger(''))
            .toEqual({type: 'required', message: 'Darf nicht leer sein.'});
    });

    it('returns error for invalid number', () => {
        expect(formUtils.validateInteger('invalid'))
            .toEqual({type: 'validate', message: 'Muss eine Zahl sein.'});
    });

    it('returns error for non-integer number', () => {
        expect(formUtils.validateInteger('3.14'))
            .toEqual({type: 'validate', message: 'Muss eine Ganzzahl sein.'});
    });

    it('returns undefined for integer', () => {
        expect(formUtils.validateInteger('7'))
            .toEqual(undefined);
    });

    it('returns undefined for zero', () => {
        expect(formUtils.validateInteger('0'))
            .toEqual(undefined);
    });
});

describe('validateIntegerOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateIntegerOrNull(''))
            .toEqual(undefined);
    });

    it('returns same as validateInteger for non-nullish user inputs', () => {
        expect(formUtils.validateIntegerOrNull('7'))
            .toEqual(formUtils.validateInteger('7'));
    });
});

describe('validateNumber', () => {
    it('returns error for nullish user inputs', () => {
        expect(formUtils.validateNumber(''))
            .toEqual({type: 'required', message: 'Darf nicht leer sein.'});
    });

    it('returns error for invalid number', () => {
        expect(formUtils.validateNumber('invalid'))
            .toEqual({type: 'validate', message: 'Muss eine Zahl sein.'});
    });

    it('returns undefined for number', () => {
        expect(formUtils.validateNumber('3.14'))
            .toEqual(undefined);
    });

    it('returns undefined for zero', () => {
        expect(formUtils.validateNumber('0'))
            .toEqual(undefined);
    });
});

describe('validateNumberOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateNumberOrNull(''))
            .toEqual(undefined);
    });

    it('returns same as validateNumber for non-nullish user inputs', () => {
        expect(formUtils.validateNumberOrNull('3.14'))
            .toEqual(formUtils.validateNumber('3.14'));
    });
});

describe('getApiNumber', () => {
    it('works for 0', () => {
        expect(formUtils.getApiNumber('0')).toEqual(0);
    });

    it('works for 3.14', () => {
        expect(formUtils.getApiNumber('3.14')).toEqual(3.14);
    });

    it('works for empty string', () => {
        expect(formUtils.getApiNumber('')).toEqual(null);
    });

    it('works for invalid number', () => {
        expect(formUtils.getApiNumber('invalid')).toEqual(NaN);
    });
});

// String

describe('getFormString', () => {
    it('works for text', () => {
        expect(formUtils.getFormString('text')).toEqual('text');
    });

    it('works for special chars', () => {
        expect(formUtils.getFormString('ĩä😎=/+')).toEqual('ĩä😎=/+');
    });

    it('works for null', () => {
        expect(formUtils.getFormString(null)).toEqual('');
    });

    it('works for undefined', () => {
        expect(formUtils.getFormString(undefined)).toEqual('');
    });
});

describe('validateNotEmpty', () => {
    it('returns error for nullish user inputs', () => {
        expect(formUtils.validateNotEmpty(''))
            .toEqual({type: 'required', message: 'Darf nicht leer sein.'});
    });

    it('returns undefined for non-null user inputs', () => {
        expect(formUtils.validateNotEmpty('non-null'))
            .toEqual(undefined);
    });
});

describe('getApiString', () => {
    it('works for text', () => {
        expect(formUtils.getApiString('text')).toEqual('text');
    });

    it('works for special chars', () => {
        expect(formUtils.getApiString('ĩä😎=/+')).toEqual('ĩä😎=/+');
    });

    it('works for empty string', () => {
        expect(formUtils.getApiString('')).toEqual(null);
    });
});

// Date & Time

describe('validateDateTime', () => {
    it('returns datetime for correct swiss user inputs', () => {
        expect(formUtils.validateDateTime('13.01.2006 18:03'))
            .toEqual([undefined, '2006-01-13 18:03:00']);
        expect(formUtils.validateDateTime('13.1.2006 18:03'))
            .toEqual([undefined, '2006-01-13 18:03:00']);
        expect(formUtils.validateDateTime('13. 1. 2006 18:43:36'))
            .toEqual([undefined, '2006-01-13 18:43:36']);
        expect(formUtils.validateDateTime(' 13. 1. 2006 \t 18:03 \t'))
            .toEqual([undefined, '2006-01-13 18:03:00']);
    });

    it('returns datetime for correct ISO user inputs', () => {
        expect(formUtils.validateDateTime('2006-01-13 18:03:00'))
            .toEqual([undefined, '2006-01-13 18:03:00']);
        expect(formUtils.validateDateTime('2006-1-13 18:03'))
            .toEqual([undefined, '2006-01-13 18:03:00']);
        expect(formUtils.validateDateTime('2006 - 1 - 13 18:43:36'))
            .toEqual([undefined, '2006-01-13 18:43:36']);
        expect(formUtils.validateDateTime(' 2006-\t1-13 \t 18:03 \t'))
            .toEqual([undefined, '2006-01-13 18:03:00']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateDateTime(''))
            .toEqual([{'message': 'Muss in einem gültigen Zeitpunkt-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('not.a.date'))
            .toEqual([{'message': 'Muss in einem gültigen Zeitpunkt-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('2006.01.13 18:36'))
            .toEqual([{'message': 'Muss in einem gültigen Zeitpunkt-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('01/13/2006 18:04'))
            .toEqual([{'message': 'Muss in einem gültigen Zeitpunkt-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('32.1.2006 18:03'))
            .toEqual([{'message': 'Muss ein gültiger Zeitpunkt sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('30.1.2006 24:67'))
            .toEqual([{'message': 'Muss ein gültiger Zeitpunkt sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('13.01.06 18:03'))
            .toEqual([{'message': 'Muss in einem gültigen Zeitpunkt-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDateTime('13.01. 18:03'))
            .toEqual([{'message': 'Muss in einem gültigen Zeitpunkt-Format sein.', 'type': 'validate'}, '']);
    });
});

describe('validateDateTimeOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateDateTimeOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateDateTime for non-nullish user inputs', () => {
        expect(formUtils.validateDateTimeOrNull('13.01.2006 18:03'))
            .toEqual(formUtils.validateDateTime('13.01.2006 18:03'));
    });
});

describe('validateDate', () => {
    it('returns date for correct swiss user inputs', () => {
        expect(formUtils.validateDate('13.01.2006'))
            .toEqual([undefined, '2006-01-13']);
        expect(formUtils.validateDate('13.1.2006'))
            .toEqual([undefined, '2006-01-13']);
        expect(formUtils.validateDate('13. 1. 2006'))
            .toEqual([undefined, '2006-01-13']);
        expect(formUtils.validateDate(' 13. 1. 2006 \t'))
            .toEqual([undefined, '2006-01-13']);
    });

    it('returns date for correct ISO user inputs', () => {
        expect(formUtils.validateDate('2006-01-13'))
            .toEqual([undefined, '2006-01-13']);
        expect(formUtils.validateDate('2006-1-13'))
            .toEqual([undefined, '2006-01-13']);
        expect(formUtils.validateDate('2006 - 1 - 13'))
            .toEqual([undefined, '2006-01-13']);
        expect(formUtils.validateDate(' 2006-\t1-13 \t'))
            .toEqual([undefined, '2006-01-13']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateDate(''))
            .toEqual([{'message': 'Muss in einem gültigen Datum-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDate('not.a.date'))
            .toEqual([{'message': 'Muss in einem gültigen Datum-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDate('2006.01.13'))
            .toEqual([{'message': 'Muss in einem gültigen Datum-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDate('01/13/2006'))
            .toEqual([{'message': 'Muss in einem gültigen Datum-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDate('32.1.2006'))
            .toEqual([{'message': 'Muss ein gültiges Datum sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDate('13.01.06'))
            .toEqual([{'message': 'Muss in einem gültigen Datum-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateDate('13.01.'))
            .toEqual([{'message': 'Muss in einem gültigen Datum-Format sein.', 'type': 'validate'}, '']);
    });
});

describe('validateDateOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateDateOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateTime for non-nullish user inputs', () => {
        expect(formUtils.validateDateOrNull('13.01.2006'))
            .toEqual(formUtils.validateDate('13.01.2006'));
    });
});

describe('validateTime', () => {
    it('returns date for correct user inputs', () => {
        expect(formUtils.validateTime('18:03'))
            .toEqual([undefined, '18:03:00']);
        expect(formUtils.validateTime('18:3'))
            .toEqual([undefined, '18:03:00']);
        expect(formUtils.validateTime(' 8:\t20 \t'))
            .toEqual([undefined, '08:20:00']);
        expect(formUtils.validateTime('18:43:36'))
            .toEqual([undefined, '18:43:36']);
        expect(formUtils.validateTime(' \t 18:03 \t'))
            .toEqual([undefined, '18:03:00']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateTime(''))
            .toEqual([{'message': 'Muss in einem gültigen Tageszeit-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateTime('not:a:time'))
            .toEqual([{'message': 'Muss in einem gültigen Tageszeit-Format sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateTime('25:36'))
            .toEqual([{'message': 'Muss eine gültige Tageszeit sein.', 'type': 'validate'}, '']);
        expect(formUtils.validateTime('18:61'))
            .toEqual([{'message': 'Muss eine gültige Tageszeit sein.', 'type': 'validate'}, '']);
    });
});

describe('validateTimeOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateTimeOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateTime for non-nullish user inputs', () => {
        expect(formUtils.validateTimeOrNull('18:03'))
            .toEqual(formUtils.validateTime('18:03'));
    });
});
