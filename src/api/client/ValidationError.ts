import {OlzApiEndpoint, OlzApiRequests} from './OlzApi';

export type RequestFieldId<T extends OlzApiEndpoint> = keyof OlzApiRequests[T] & string;

export type ErrorsByField<T extends OlzApiEndpoint> = {[fieldName in RequestFieldId<T>]: string[]};

export class ValidationError<T extends OlzApiEndpoint> extends Error {
    constructor(
        message: string,
        private validationErrors: ErrorsByField<T>,
    ) {
        super(message); // 'Error' breaks prototype chain here
        Object.setPrototypeOf(this, new.target.prototype); // restore prototype chain
    }

    public getValidationErrors(): ErrorsByField<T> {
        return this.validationErrors;
    }
}

export function mergeValidationErrors<T extends OlzApiEndpoint>(validationErrors: ValidationError<T>[]): ValidationError<T> {
    const initialValidationErrors = {} as ErrorsByField<T>;
    let merged = new ValidationError<T>('', initialValidationErrors);
    for (const validationError of validationErrors) {
        const newMessage = validationError.message
            ? merged.message + (merged.message ? '\n' : '') + validationError.message
            : merged.message;
        // TODO: Deep merge (concat errors if key present in both dicts)
        const newValidationErrors = {
            ...merged.getValidationErrors(),
            ...validationError.getValidationErrors(),
        };
        merged = new ValidationError<T>(newMessage, newValidationErrors);
    }
    return merged;
}

export function getValidationErrorFromResponseText<T extends OlzApiEndpoint>(
    responseText?: string,
): ValidationError<T>|undefined {
    if (!responseText) {
        return undefined;
    }
    let structuredError;
    try {
        structuredError = JSON.parse(responseText);
    } catch (e: unknown) {
        return undefined;
    }
    if (structuredError?.error?.type !== 'ValidationError') {
        return undefined;
    }
    const message = structuredError?.message;
    const validationErrors = structuredError?.error?.validationErrors;
    if (!message) {
        throw new Error(`Validation error missing message: ${structuredError}`);
    }
    if (!validationErrors) {
        throw new Error(`Validation error missing errors: ${structuredError}`);
    }
    return new ValidationError(message, validationErrors);
}
