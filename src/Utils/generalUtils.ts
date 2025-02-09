import {OlzMetaData} from '../Api/client/generated_olz_api_types';

export function assert<T>(value: T|null|undefined, errorMessage?: string): T {
    if (value === null || value === undefined) {
        throw new Error(errorMessage ?? 'Non-null/undefined assertion failed');
    }
    return value;
}

export function assertUnreachable(value: never): never {
    throw new Error(`Unexpectedly reachable using value: ${value}`);
}

export function getErrorOrThrow(err: unknown): Error {
    if (!(err instanceof Error)) {
        throw new Error('Thrown thing is not an error ¯\\_ (ツ)_/¯');
    }
    return err;
}

export function isDefined<T>(value: T|undefined|null): value is T {
    return value !== null && value !== undefined;
}

export function isLocal(): boolean {
    const localHostnames: {[hostname: string]: true} = {
        'localhost': true,
        '127.0.0.1': true,
    };
    /* istanbul ignore next */
    return localHostnames[location.hostname] ?? false;
}

export function timeout(milliseconds: number): Promise<void> {
    return new Promise((resolve) => {
        setTimeout(resolve, milliseconds);
    });
}

/* istanbul ignore next */
export function loadScript(src: string): Promise<void> {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.type = 'text/javascript';
        script.onload = () => resolve();
        script.onerror = reject;
        script.src = src;
        document.head.append(script);
    });
}

export interface Entity<DataType> {
    id: number;
    meta: OlzMetaData;
    data: DataType;
}
