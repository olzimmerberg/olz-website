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
import './header.scss';
import './index.scss';
import './menu.scss';
import './profile.scss';
import './styles.scss';

export * from './components/index';
export * from './fuer_einsteiger';
export * from './header';
export * from './kontakt';
export * from './profile';
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
