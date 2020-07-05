// =============================================================================
// Der Index von allem Frontend Code.
// Webpack generiert daraus den Ordner `jsbuild/`.
// =============================================================================

import 'lightgallery/dist/css/lightgallery.css';
import 'jquery-ui/themes/base/theme.css';
import 'jquery-ui/themes/base/datepicker.css';
import 'typeface-open-sans';
import './header.css';
import './index.css';
import './menu.css';
import './styles.css';

export * from './components/index';
export * from './header';
export * from './scripts/jscripts';
export * from './scripts/fader';
export * from './scripts/accordion';
export * from 'lightgallery';
export * from 'jquery';
export * from 'jquery-ui/ui/widgets/datepicker';

export function loaded() {
    // TODO: remove this!
    for (const key of Object.keys(olz)) {
        window[key] = olz[key];
    }

    $(document).ready(() => {
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
