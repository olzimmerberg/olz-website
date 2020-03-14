import 'lightgallery/dist/css/lightgallery.css';

export * from './scripts/jscripts';
export * from './scripts/fader';
export * from './scripts/accordion';
export * from 'lightgallery';

export const $ = jQuery;

export function loaded() {
    // TODO: remove this!
    for (const key of Object.keys(olz)) {
        window[key] = olz[key];
    }

    $(document).ready(() => {
        $(".lightgallery").lightGallery({
            selector: 'a[data-src]',
        });
    });
    console.log('OLZ loaded!');
}
