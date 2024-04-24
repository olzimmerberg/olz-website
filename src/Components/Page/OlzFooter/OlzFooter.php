<?php

namespace Olz\Components\Page\OlzFooter;

use Olz\Components\Common\OlzComponent;

class OlzFooter extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        return <<<ZZZZZZZZZZ
                        <div style='clear:both;'>&nbsp;</div>
                    </div>
                </div>
                <div class='olz-footer'>
                    <a href='{$code_href}fuer_einsteiger?von=footer'>Für Einsteiger</a>
                    <a href='{$code_href}fragen_und_antworten'>Fragen &amp; Antworten (FAQ)</a>
                    <a href='{$code_href}datenschutz'>Datenschutz</a>
                    <a href='{$code_href}sitemap'>Sitemap</a>
                </div>
                <div id='edit-entity-react-root'></div>
                <div id='dialog-react-root'></div>
                <div id='update-user-avatar-react-root'></div>
            </body>
            </html>
            ZZZZZZZZZZ;
    }
}
