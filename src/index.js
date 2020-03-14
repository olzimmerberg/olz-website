export * from './scripts/jscripts';
export * from './scripts/fader';
export * from './scripts/accordion';

export function loaded() {
    // TODO: remove this!
    for (const key of Object.keys(olz)) {
        window[key] = olz[key];
    }
    console.log('OLZ loaded!');
}
