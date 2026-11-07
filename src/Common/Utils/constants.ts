interface TypedWindow {
    olzCodeHref: unknown;
    olzDataHref: unknown;
    olzUser: unknown;
    olzChildUsers: unknown;
    olzIsoNow: unknown;
    olzBotRegexes: unknown;
}

export interface UserConstant {
    id?: number;
    username?: string;
    name?: string;
    permissions?: string;
    root?: string;
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

/* istanbul ignore next */
export const childUsers: Array<UserConstant> =
    (
        typeof typedWindow.olzChildUsers === 'object'
        && Array.isArray(typedWindow.olzChildUsers)
        && typedWindow.olzChildUsers.every((item) => typeof item === 'object')
    ) ? typedWindow.olzChildUsers as Array<UserConstant> : [];

/* istanbul ignore next */
export const isoNow: string = typeof typedWindow.olzIsoNow === 'string'
    ? typedWindow.olzIsoNow as string : new Date().toISOString();

/* istanbul ignore next */
export const botRegexes: string[] = typeof typedWindow.olzBotRegexes === 'object'
    ? typedWindow.olzBotRegexes as string[] : [];
