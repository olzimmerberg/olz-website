// =============================================================================
// Der Index von allem Frontend Code.
// Webpack generiert daraus den Ordner `jsbuild/`.
// =============================================================================

import lightGallery from 'lightgallery';
import lgVideo from 'lightgallery/plugins/video';

import './email_reaktion.scss';
import './fuer_einsteiger.scss';
import './index.scss';
import './konto_passwort.scss';
import './konto_strava.scss';
import './logs.scss';
import './profil.scss';
import './startseite.scss';
import './webftp.scss';

export * from './components/index';
export * from './email_reaktion';
export * from './features/index';
export * from './fuer_einsteiger';
export * from './konto_passwort';
export * from './konto_strava';
export * from './logs';
export * from './news/index';
export * from './profil';
export * from './scripts/index';
export * from './styles/index';
export * from './termine/index';
export * from './webftp';

export * from '../src/Apps/index';
export * from '../src/Utils/index';

/* @ts-ignore: Ignore file is not a module. */
export * from 'bootstrap';
/* @ts-expect-error: Ignore file is not a module. */
export * from 'jquery';
/* @ts-ignore: Ignore file is not a module. */
export * from 'jquery-ui/ui/widgets/datepicker';

// OLZ library (as generated by webpack, i.e. all exports of this file)
declare const olz: {[key: string]: any};

export function loaded(): void {
    // TODO: remove this!
    for (const key of Object.keys(olz)) {
        /* @ts-expect-error: Ignore type unsafety. */
        window[key] = olz[key];
    }
    /* @ts-expect-error: Ignore type unsafety. */
    // eslint-disable-next-line dot-notation
    window['$'] = $;

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
        $.datepicker.setDefaults({
            dateFormat: 'yy-mm-dd',
        });
        $('.datepicker').datepicker();
    });
    console.log('OLZ loaded!');
}
