// =============================================================================
// Der Index von allem Frontend Code.
// Webpack generiert daraus den Ordner `jsbuild/`.
// =============================================================================

import 'bootstrap';
import 'lightgallery/dist/css/lightgallery.css';
import 'jquery-ui/themes/base/theme.css';
import 'jquery-ui/themes/base/datepicker.css';
import 'typeface-open-sans';
import './bootstrap.scss';
import './index.scss';
import './konto_strava.scss';
import './konto_passwort.scss';
import './profil.scss';
import './styles.scss';

export * from './components/index';
export * from './features/index';
export * from './fuer_einsteiger';
export * from './kontakt';
export * from './konto_passwort';
export * from './konto_strava';
export * from './profil';
export * from './scripts/jscripts';
export * from './scripts/fader';
export * from './scripts/accordion';
export * from './termine';
export * from 'lightgallery';
export * from 'jquery';
export * from 'jquery-ui/ui/widgets/datepicker';

export function loaded() {
    // TODO: remove this!
    for (const key of Object.keys(olz)) {
        window[key] = olz[key];
    }

    $(() => {
        $('.lightgallery').lightGallery({
            selector: 'a[data-src]',
        });
        $.datepicker.setDefaults({
            dateFormat: 'yy-mm-dd',
        });
        $('.datepicker').datepicker();
    });
    console.log('OLZ loaded!');
}
