// =============================================================================
// Der Index von allem Frontend Code.
// Webpack generiert daraus den Ordner `jsbuild/olz`.
// =============================================================================

import lightGallery from 'lightgallery';
import lgVideo from 'lightgallery/plugins/video';

import './index.scss';

export * from './Anniversary/index';
export * from './Apps/index';
export * from './Captcha/index';
export * from './Components/index';
export * from './Faq/index';
export * from './Karten/index';
export * from './News/index';
export * from './Roles/index';
export * from './Service/index';
export * from './Snippets/index';
export * from './Startseite/index';
export * from './Suche/index';
export * from './Termine/index';
export * from './Users/index';

/* @ts-ignore: Ignore file is not a module. */
import * as bootstrap from 'bootstrap';
import $ from 'jquery';

window.bootstrap = bootstrap;

export function loaded(): void {
    $(() => {
        const lightGalleryElems = document.querySelectorAll('.lightgallery');
        for (let i = 0; i < lightGalleryElems.length; i++) {
            lightGallery(lightGalleryElems[i] as HTMLElement, {
                hideControlOnEnd: true,
                plugins: [lgVideo],
                speed: 500,
                selector: 'a[data-src]',
            });
        }
    });
    console.log('OLZ loaded!');
}
