interface TypedWindow {
    olzCodeHref: unknown;
    olzDataHref: unknown;
    olzUser: unknown;
}

interface UserConstant {
    permissions?: string;
    root?: string;
    username?: string;
    id?: number;
}

/* @ts-expect-error: Ignore type unsafety. */
const typedWindow: TypedWindow = window;

/* istanbul ignore next */
export const codeHref: string = typeof typedWindow.olzCodeHref === 'string'
    ? typedWindow.olzCodeHref : '/';

/* istanbul ignore next */
export const dataHref: string = typeof typedWindow.olzDataHref === 'string'
    ? typedWindow.olzDataHref : '/';

/* istanbul ignore next */
export const user: UserConstant = typeof typedWindow.olzUser === 'object'
    ? typedWindow.olzUser as UserConstant : {};
