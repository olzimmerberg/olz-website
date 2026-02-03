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
        expect(formUtils.getFormBoolean(false, 'foo')).toEqual('');
    });

    it('works for true', () => {
        expect(formUtils.getFormBoolean(true)).toEqual('yes');
        expect(formUtils.getFormBoolean(true, 'foo')).toEqual('foo');
    });

    it('works for null', () => {
        expect(formUtils.getFormBoolean(null)).toEqual('');
        expect(formUtils.getFormBoolean(null, 'foo')).toEqual('');
    });

    it('works for undefined', () => {
        expect(formUtils.getFormBoolean(undefined)).toEqual('');
        expect(formUtils.getFormBoolean(undefined, 'foo')).toEqual('');
    });
});

describe('getApiBoolean', () => {
    it('works for false', () => {
        expect(formUtils.getApiBoolean('')).toEqual(false);
    });

    it('works for true', () => {
        expect(formUtils.getApiBoolean('yes')).toEqual(true);
    });

    it('works for booleans', () => {
        expect(formUtils.getApiBoolean(false)).toEqual(false);
        expect(formUtils.getApiBoolean(true)).toEqual(true);
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

describe('validateStringLength', () => {
    it('returns undefined for OK strings', () => {
        expect(formUtils.validateStringLength('', 0, 0))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('1', 1, 1))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('123', null, null))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('123', 3, null))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('123', null, 3))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('123', 10, null))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('123', null, 1))
            .toEqual(undefined);
    });

    it('whitespace is being stripped', () => {
        expect(formUtils.validateStringLength(' ', 0, null))
            .toEqual(undefined);
        expect(formUtils.validateStringLength(' ', null, 1))
            .toEqual({type: 'validate', message: 'Die Eingabe muss mindestens 1 Zeichen lang sein.'});
        expect(formUtils.validateStringLength('\t', 0, null))
            .toEqual(undefined);
        expect(formUtils.validateStringLength('\t', null, 1))
            .toEqual({type: 'validate', message: 'Die Eingabe muss mindestens 1 Zeichen lang sein.'});
    });

    it('returns validation error for too short strings', () => {
        expect(formUtils.validateStringLength('', null, 1))
            .toEqual({type: 'validate', message: 'Die Eingabe muss mindestens 1 Zeichen lang sein.'});
        expect(formUtils.validateStringLength(' ', null, 2))
            .toEqual({type: 'validate', message: 'Die Eingabe muss mindestens 2 Zeichen lang sein.'});
        expect(formUtils.validateStringLength('123', null, 4))
            .toEqual({type: 'validate', message: 'Die Eingabe muss mindestens 4 Zeichen lang sein.'});
        expect(formUtils.validateStringLength('123', 100, 10))
            .toEqual({type: 'validate', message: 'Die Eingabe muss mindestens 10 Zeichen lang sein.'});
    });

    it('returns validation error for too long strings', () => {
        expect(formUtils.validateStringLength('123', 0, null))
            .toEqual({type: 'validate', message: 'Die Eingabe darf höchstens 0 Zeichen lang sein.'});
        expect(formUtils.validateStringLength('123', 2, null))
            .toEqual({type: 'validate', message: 'Die Eingabe darf höchstens 2 Zeichen lang sein.'});
        expect(formUtils.validateStringLength('123', 2, 1))
            .toEqual({type: 'validate', message: 'Die Eingabe darf höchstens 2 Zeichen lang sein.'});
    });
});

describe('validateStringRegex', () => {
    it('returns undefined for OK strings', () => {
        expect(formUtils.validateStringRegex('', '^$', 'Ungültig'))
            .toEqual(undefined);
        expect(formUtils.validateStringRegex('a', '^a$', 'Ungültig'))
            .toEqual(undefined);
        expect(formUtils.validateStringRegex('A', /^a$/i, 'Ungültig'))
            .toEqual(undefined);
    });

    it('returns validation error for mismatches', () => {
        expect(formUtils.validateStringRegex('', '^a$', 'Ungültig'))
            .toEqual({type: 'validate', message: 'Ungültig'});
        expect(formUtils.validateStringRegex('a', '^$', 'Ungültig'))
            .toEqual({type: 'validate', message: 'Ungültig'});
        expect(formUtils.validateStringRegex('A', /^a$/, 'Ungültig'))
            .toEqual({type: 'validate', message: 'Ungültig'});
    });
});

describe('validateCountryCode', () => {
    it('returns validation error for nullish user inputs', () => {
        expect(formUtils.validateCountryCode(''))
            .toEqual([
                {type: 'validate', message: 'Der Ländercode muss zwei Zeichen lang sein.'},
                '',
            ]);
        expect(formUtils.validateCountryCode(' '))
            .toEqual([
                {type: 'validate', message: 'Der Ländercode muss zwei Zeichen lang sein.'},
                '',
            ]);
        expect(formUtils.validateCountryCode('\t'))
            .toEqual([
                {type: 'validate', message: 'Der Ländercode muss zwei Zeichen lang sein.'},
                '',
            ]);
    });

    it('returns countryCode for correct user inputs', () => {
        expect(formUtils.validateCountryCode('CH'))
            .toEqual([undefined, 'CH']);
        expect(formUtils.validateCountryCode('DE'))
            .toEqual([undefined, 'DE']);
        expect(formUtils.validateCountryCode('US \t'))
            .toEqual([undefined, 'US']);
    });

    it('returns countryCode for some country names', () => {
        expect(formUtils.validateCountryCode(' Switzerland'))
            .toEqual([undefined, 'CH']);
        expect(formUtils.validateCountryCode('Schweiz'))
            .toEqual([undefined, 'CH']);
        expect(formUtils.validateCountryCode('SUI'))
            .toEqual([undefined, 'CH']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateCountryCode('P'))
            .toEqual([
                {type: 'validate', message: 'Der Ländercode muss zwei Zeichen lang sein.'},
                'P',
            ]);
        expect(formUtils.validateCountryCode('WTF'))
            .toEqual([
                {type: 'validate', message: 'Der Ländercode muss zwei Zeichen lang sein.'},
                'WTF',
            ]);
        expect(formUtils.validateCountryCode('not.a.country'))
            .toEqual([
                {type: 'validate', message: 'Der Ländercode muss zwei Zeichen lang sein.'},
                'not.a.country',
            ]);
    });
});

describe('validateCountryCodeOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateCountryCodeOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateCountryCode for non-nullish user inputs', () => {
        expect(formUtils.validateCountryCodeOrNull('CH'))
            .toEqual(formUtils.validateCountryCode('CH'));
    });
});

describe('validateEmail', () => {
    it('returns validation error for nullish user inputs', () => {
        expect(formUtils.validateEmail(''))
            .toEqual([
                {type: 'validate', message: 'Ungültige E-Mail Adresse "".'},
                '',
            ]);
        expect(formUtils.validateEmail(' '))
            .toEqual([
                {type: 'validate', message: 'Ungültige E-Mail Adresse "".'},
                '',
            ]);
        expect(formUtils.validateEmail('\t'))
            .toEqual([
                {type: 'validate', message: 'Ungültige E-Mail Adresse "".'},
                '',
            ]);
    });

    it('returns email address for correct user inputs', () => {
        expect(formUtils.validateEmail('test.adress@olzimmerberg.ch'))
            .toEqual([undefined, 'test.adress@olzimmerberg.ch']);
        expect(formUtils.validateEmail('plus+adress@some-hoster.tv'))
            .toEqual([undefined, 'plus+adress@some-hoster.tv']);
        expect(formUtils.validateEmail(' whitespace@being-trimmed.org \t'))
            .toEqual([undefined, 'whitespace@being-trimmed.org']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateEmail('not.an.email'))
            .toEqual([
                {type: 'validate', message: 'Ungültige E-Mail Adresse "not.an.email".'},
                'not.an.email',
            ]);
        expect(formUtils.validateEmail('also@weird'))
            .toEqual([
                {type: 'validate', message: 'Ungültige E-Mail Adresse "also@weird".'},
                'also@weird',
            ]);
    });
});

describe('validateEmailOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateEmailOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateEmail for non-nullish user inputs', () => {
        expect(formUtils.validateEmailOrNull('test.adress@olzimmerberg.ch'))
            .toEqual(formUtils.validateEmail('test.adress@olzimmerberg.ch'));
    });
});

describe('validateGender', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateGender(''))
            .toEqual([undefined, null]);
        expect(formUtils.validateGender(null))
            .toEqual([undefined, null]);
        expect(formUtils.validateGender(undefined))
            .toEqual([undefined, null]);
    });

    it('returns gender for correct user inputs', () => {
        expect(formUtils.validateGender('M'))
            .toEqual([undefined, 'M']);
        expect(formUtils.validateGender('F'))
            .toEqual([undefined, 'F']);
        expect(formUtils.validateGender('O'))
            .toEqual([undefined, 'O']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateGender('not.a.gender'))
            .toEqual([
                {type: 'validate', message: 'Ungültiges Geschlecht "not.a.gender" ausgewählt.'},
                null,
            ]);
        expect(formUtils.validateGender('P'))
            .toEqual([
                {type: 'validate', message: 'Ungültiges Geschlecht "P" ausgewählt.'},
                null,
            ]);
    });
});

describe('validatePassword', () => {
    it('returns validation error for nullish user inputs', () => {
        expect(formUtils.validatePassword(''))
            .toEqual(
                {type: 'validate', message: 'Das Passwort muss mindestens 8 Zeichen lang sein.'},
            );
    });

    it('returns undefined for correct user inputs', () => {
        expect(formUtils.validatePassword('longenough'))
            .toEqual(undefined);
        expect(formUtils.validatePassword('also..ok'))
            .toEqual(undefined);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validatePassword('tooshor'))
            .toEqual(
                {type: 'validate', message: 'Das Passwort muss mindestens 8 Zeichen lang sein.'},
            );
        expect(formUtils.validatePassword('wtf'))
            .toEqual(
                {type: 'validate', message: 'Das Passwort muss mindestens 8 Zeichen lang sein.'},
            );
        expect(formUtils.validatePassword('1234'))
            .toEqual(
                {type: 'validate', message: 'Das Passwort muss mindestens 8 Zeichen lang sein.'},
            );
        expect(formUtils.validatePassword('admin'))
            .toEqual(
                {type: 'validate', message: 'Das Passwort muss mindestens 8 Zeichen lang sein.'},
            );
    });
});

describe('validatePhone', () => {
    it('returns validation error for nullish user inputs', () => {
        expect(formUtils.validatePhone(''))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '',
            ]);
        expect(formUtils.validatePhone(' '))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                ' ',
            ]);
        expect(formUtils.validatePhone('\t'))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '\t',
            ]);
    });

    it('returns phone for correct user inputs', () => {
        expect(formUtils.validatePhone('+41441234567'))
            .toEqual([undefined, '+41441234567']);
        expect(formUtils.validatePhone(' +41 79 123 45 67'))
            .toEqual([undefined, '+41791234567']);
        expect(formUtils.validatePhone('+41\t78\t1234567 \t'))
            .toEqual([undefined, '+41781234567']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validatePhone('no letters allowed'))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                'no letters allowed',
            ]);
        expect(formUtils.validatePhone('+'))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '+',
            ]);
        expect(formUtils.validatePhone('+ '))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '+ ',
            ]);
        expect(formUtils.validatePhone('123 45 67'))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '123 45 67',
            ]);
        expect(formUtils.validatePhone('01 123 45 67'))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '01 123 45 67',
            ]);
        expect(formUtils.validatePhone('044 765 43 21'))
            .toEqual([
                {type: 'validate', message: 'Die Telefonnummer muss mit internationalem Präfix (Schweiz: +41) eingegeben werden.'},
                '044 765 43 21',
            ]);
    });
});

describe('validatePhoneOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validatePhoneOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validatePhone for non-nullish user inputs', () => {
        expect(formUtils.validatePhoneOrNull('+41441234567'))
            .toEqual(formUtils.validatePhone('+41441234567'));
    });
});

describe('validateAhv', () => {
    it('returns validation error for nullish user inputs', () => {
        expect(formUtils.validateAhv(''))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '',
            ]);
        expect(formUtils.validateAhv(' '))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '',
            ]);
        expect(formUtils.validateAhv('\t'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '',
            ]);
    });

    it('returns AHV number for correct user inputs', () => {
        expect(formUtils.validateAhv('756.0000.0000.00'))
            .toEqual([undefined, '756.0000.0000.00']);
        expect(formUtils.validateAhv('756.1234.1234.12 '))
            .toEqual([undefined, '756.1234.1234.12']);
        expect(formUtils.validateAhv(' 756.9999.9999.99'))
            .toEqual([undefined, '756.9999.9999.99']);
    });

    it('returns validation error for invalid user inputs', () => {
        expect(formUtils.validateAhv('abc.abcd.abcd.ab'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                'abc.abcd.abcd.ab',
            ]);
        expect(formUtils.validateAhv('...'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '...',
            ]);
        expect(formUtils.validateAhv('123.1234.1234.12'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '123.1234.1234.12',
            ]);
        expect(formUtils.validateAhv('756.123.123.1234'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '756.123.123.1234',
            ]);
        expect(formUtils.validateAhv('756 0000 0000 00'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '756 0000 0000 00',
            ]);
        expect(formUtils.validateAhv('756,1234,1234,12'))
            .toEqual([
                {type: 'validate', message: 'Ungültige AHV-Nummer (Format: 756.XXXX.XXXX.XX)'},
                '756,1234,1234,12',
            ]);
    });
});

describe('validateAhvOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateAhvOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateAhv for non-nullish user inputs', () => {
        expect(formUtils.validateAhvOrNull('756.1234.1234.12'))
            .toEqual(formUtils.validateAhv('756.1234.1234.12'));
    });
});

describe('validateEmailOrNull', () => {
    it('returns null for nullish user inputs', () => {
        expect(formUtils.validateEmailOrNull(''))
            .toEqual([undefined, '']);
    });

    it('returns same as validateEmail for non-nullish user inputs', () => {
        expect(formUtils.validateEmailOrNull('test.adress@olzimmerberg.ch'))
            .toEqual(formUtils.validateEmail('test.adress@olzimmerberg.ch'));
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
        expect(formUtils.validateDateTime('2006-01-13T18:03:00'))
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

describe('getDateTimeFeedback', () => {
    it('returns empty for nullish user inputs', () => {
        expect(formUtils.getDateTimeFeedback(''))
            .toEqual('');
    });

    it('returns empty for invalid user inputs', () => {
        expect(formUtils.getDateTimeFeedback('invalid'))
            .toEqual('');
    });

    it('returns feedback', () => {
        expect(formUtils.getDateTimeFeedback('13.01.2006 18:03'))
            .toEqual('🟢 Freitag');
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

    it('returns same as validateDate for non-nullish user inputs', () => {
        expect(formUtils.validateDateOrNull('13.01.2006'))
            .toEqual(formUtils.validateDate('13.01.2006'));
    });
});

describe('getDateFeedback', () => {
    it('returns empty for nullish user inputs', () => {
        expect(formUtils.getDateFeedback(''))
            .toEqual('');
    });

    it('returns empty for invalid user inputs', () => {
        expect(formUtils.getDateFeedback('invalid'))
            .toEqual('');
    });

    it('returns feedback', () => {
        expect(formUtils.getDateFeedback('13.01.2006'))
            .toEqual('🟢 Freitag');
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
