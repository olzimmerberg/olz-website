interface TypedWindow {
    olzCodeHref: string;
    olzDataHref: unknown;
}

/* @ts-expect-error: Ignore type unsafety. */
const typedWindow: TypedWindow = window;

/* istanbul ignore next */
export const codeHref: string = typeof typedWindow.olzCodeHref === 'string'
    ? typedWindow.olzCodeHref : '/';

/* istanbul ignore next */
export const dataHref: string = typeof typedWindow.olzDataHref === 'string'
    ? typedWindow.olzDataHref : '/';
