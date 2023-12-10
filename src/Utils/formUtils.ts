import {FieldError, FieldErrors, FieldValues, ResolverResult} from 'react-hook-form';
import {isDefined} from './generalUtils';

export const EMAIL_REGEX = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

export const ISO_DATETIME_REGEX = /^\s*([0-9]{4})\s*-\s*([0-9]{1,2})\s*-\s*([0-9]{1,2})\W+([0-9]{1,2})\s*:\s*([0-9]{1,2})\s*(:\s*([0-9]{1,2})\s*)?$/;
export const SWISS_DATETIME_REGEX = /^\s*([0-9]{1,2})\.\s*([0-9]{1,2})\.\s*([0-9]{4})\W+([0-9]{1,2})\s*:\s*([0-9]{1,2})\s*(:\s*([0-9]{1,2})\s*)?$/;
export const ISO_DATE_REGEX = /^\s*([0-9]{4})\s*-\s*([0-9]{1,2})\s*-\s*([0-9]{1,2})\s*$/;
export const SWISS_DATE_REGEX = /^\s*([0-9]{1,2})\.\s*([0-9]{1,2})\.\s*([0-9]{4})\s*$/;
export const TIME_REGEX = /^\s*([0-9]{1,2})\s*:\s*([0-9]{1,2})\s*(:\s*([0-9]{1,2})\s*)?$/;

export function getResolverResult<T extends FieldValues>(
    errorsOrUndefined: FieldErrors<T>,
    values: T,
): ResolverResult<T> {
    let hasErrors = false;
    const errors: FieldErrors<T> = {};
    const keys = Object.keys(errorsOrUndefined) as (keyof T)[];
    for (const key of keys) {
        const errorValue = errorsOrUndefined[key];
        if (errorValue !== undefined) {
            hasErrors = true;
            errors[key] = errorValue;
        }
    }
    return {errors, values: hasErrors ? {} : values};
}

// Boolean

export function getFormBoolean(bool: boolean|null|undefined, value = 'yes'): string {
    return bool ? value : '';
}

export function getApiBoolean(value: string): boolean {
    return Boolean(value.trim());
}

// Numeric

export function getFormNumber(number: number|null|undefined): string {
    return isDefined(number) ? String(number) : '';
}

export function validateNumberOrNull(valueArg: string): FieldError|undefined {
    const value = valueArg.trim();
    if (value === '') {
        return undefined;
    }
    return validateNumber(value);
}

export function validateNumber(value: string): FieldError|undefined {
    if (value.trim() === '') {
        return {type: 'required', message: 'Darf nicht leer sein.'};
    }
    if (isNaN(Number(value))) {
        return {type: 'validate', message: 'Muss eine Zahl sein.'};
    }
    return undefined;
}

export function validateIntegerOrNull(valueArg: string): FieldError|undefined {
    const value = valueArg.trim();
    if (value === '') {
        return undefined;
    }
    return validateInteger(value);
}

export function validateInteger(value: string): FieldError|undefined {
    const numberError = validateNumber(value);
    if (numberError) {
        return numberError;
    }
    if (Math.round(Number(value)) !== Number(value)) {
        return {type: 'validate', message: 'Muss eine Ganzzahl sein.'};
    }
    return undefined;
}

export function getApiNumber(value: string): number|null {
    if (value.trim() === '') {
        return null;
    }
    return Number(value);
}

// String

export function getFormString(string: string|null|undefined): string {
    return string ?? '';
}

export function validateNotEmpty(value: string): FieldError|undefined {
    if (!value) {
        return {type: 'required', message: 'Darf nicht leer sein.'};
    }
    return undefined;
}

export function getApiString(value: string): string|null {
    if (value.trim() === '') {
        return null;
    }
    return value;
}

// Date & Time

export function validateDateTimeOrNull(valueArg: string): [FieldError|undefined, string] {
    const value = valueArg.trim();
    if (value === '') {
        return [undefined, ''];
    }
    return validateDateTime(value);
}

export function validateDateTime(valueArg: string): [FieldError|undefined, string] {
    const value = valueArg.trim();
    let timestamp = null;
    const swissMatch = SWISS_DATETIME_REGEX.exec(value);
    const isoMatch = ISO_DATETIME_REGEX.exec(value);
    if (swissMatch) {
        timestamp = Date.parse(
            `${swissMatch[3]}-${swissMatch[2]}-${swissMatch[1]} ${swissMatch[4]}:${swissMatch[5]}:${swissMatch[7] ? swissMatch[7] : '00'}`,
        );
    } else if (isoMatch) {
        timestamp = Date.parse(
            `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]} ${isoMatch[4]}:${isoMatch[5]}:${isoMatch[7] ? isoMatch[7] : '00'}`,
        );
    } else {
        return [{type: 'validate', message: 'Muss in einem gültigen Zeitpunkt-Format sein.'}, ''];
    }

    if (!timestamp) {
        return [{type: 'validate', message: 'Muss ein gültiger Zeitpunkt sein.'}, ''];
    }
    const dateObject = new Date(timestamp);
    const year = String(dateObject.getFullYear()).padStart(4, '0');
    const month = String(dateObject.getMonth() + 1).padStart(2, '0');
    const day = String(dateObject.getDate()).padStart(2, '0');
    const hours = String(dateObject.getHours()).padStart(2, '0');
    const minutes = String(dateObject.getMinutes()).padStart(2, '0');
    const seconds = String(dateObject.getSeconds()).padStart(2, '0');
    const newValue = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    return [undefined, newValue];
}

export function validateDateOrNull(valueArg: string): [FieldError|undefined, string] {
    const value = valueArg.trim();
    if (value === '') {
        return [undefined, ''];
    }
    return validateDate(value);
}

export function validateDate(valueArg: string): [FieldError|undefined, string] {
    const value = valueArg.trim();
    let timestamp = null;
    const swissMatch = SWISS_DATE_REGEX.exec(value);
    const isoMatch = ISO_DATE_REGEX.exec(value);
    if (swissMatch) {
        timestamp = Date.parse(
            `${swissMatch[3]}-${swissMatch[2]}-${swissMatch[1]} 12:00:00`,
        );
    } else if (isoMatch) {
        timestamp = Date.parse(
            `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]} 12:00:00`,
        );
    } else {
        return [{type: 'validate', message: 'Muss in einem gültigen Datum-Format sein.'}, ''];
    }
    if (!timestamp) {
        return [{type: 'validate', message: 'Muss ein gültiges Datum sein.'}, ''];
    }
    const dateObject = new Date(timestamp);
    const year = String(dateObject.getFullYear()).padStart(4, '0');
    const month = String(dateObject.getMonth() + 1).padStart(2, '0');
    const day = String(dateObject.getDate()).padStart(2, '0');
    const newValue = `${year}-${month}-${day}`;
    return [undefined, newValue];
}

export function validateTimeOrNull(valueArg: string): [FieldError|undefined, string] {
    const value = valueArg.trim();
    if (value === '') {
        return [undefined, ''];
    }
    return validateTime(value);
}

export function validateTime(valueArg: string): [FieldError|undefined, string] {
    const value = valueArg.trim();
    const res = TIME_REGEX.exec(value);
    if (!res) {
        return [{type: 'validate', message: 'Muss in einem gültigen Tageszeit-Format sein.'}, ''];
    }
    const today = new Date().toISOString().substring(0, 10);
    const timestamp = Date.parse(`${today} ${res[1]}:${res[2]}:${res[4] ? res[4] : '00'}`);
    if (!timestamp) {
        return [{type: 'validate', message: 'Muss eine gültige Tageszeit sein.'}, ''];
    }
    const dateObject = new Date(timestamp);
    const hours = String(dateObject.getHours()).padStart(2, '0');
    const minutes = String(dateObject.getMinutes()).padStart(2, '0');
    const seconds = String(dateObject.getSeconds()).padStart(2, '0');
    const newValue = `${hours}:${minutes}:${seconds}`;
    return [undefined, newValue];
}
