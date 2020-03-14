import * as jQueryTmp from 'jquery';

export * from './scripts/jscripts';
export * from './scripts/fader';
export * from './scripts/accordion';

export const jQuery = jQueryTmp;
export const $ = jQueryTmp;

export function loaded() {
    // TODO: remove this!
    for (const key of Object.keys(olz)) {
        window[key] = olz[key];
    }
    console.log('OLZ loaded!');
}
