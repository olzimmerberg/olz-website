import {OlzApiEndpoint, OlzApiRequests} from './OlzApi';

type ErrorsByField<T extends OlzApiEndpoint> = {[fieldName in keyof OlzApiRequests[T]]: string[]};

export class ValidationError<T extends OlzApiEndpoint> {
    constructor(
        public message: string,
        private validationErrors: ErrorsByField<T>,
    ) {}

    public getValidationErrors(): ErrorsByField<T> {
        return this.validationErrors;
    }
}

export function getValidationErrorFromResponseText<T extends OlzApiEndpoint>(
    responseText?: string,
): ValidationError<T>|undefined {
    if (!responseText) {
        return undefined;
    }
    const structuredError = JSON.parse(responseText);
    if (structuredError?.['error']?.['type'] !== 'ValidationError') {
        return undefined;
    }
    const message = structuredError?.['message'];
    const validationErrors = structuredError?.['error']?.['validationErrors'];
    if (!message) {
        throw new Error(`Validation error missing message: ${structuredError}`);
    }
    if (!validationErrors) {
        throw new Error(`Validation error missing errors: ${structuredError}`);
    }
    return new ValidationError(message, validationErrors);
}
