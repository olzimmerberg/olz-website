<?php

namespace Olz\Components\Page\OlzFooter;

use Olz\Components\Common\OlzComponent;

class OlzFooter extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $code_href = $this->envUtils()->getCodeHref();

        $spam_email = $this->emailUtils()->generateSpamEmailAddress();
        $spam_name = implode(' ', array_map(function ($part) {
            return ucfirst($part);
        }, explode('.', $spam_email)));
        $spam_honeypot = <<<ZZZZZZZZZZ
            <span class='kontakt'>
                Kontakt: <a href='mailto:{$spam_email}@olzimmerberg.ch'>{$spam_name}</a>
            </span>
            ZZZZZZZZZZ;

        return <<<ZZZZZZZZZZ
                        <div style='clear:both;'>&nbsp;</div>
                    </div>
                </div>
                <div class='olz-footer'>
                    <a href='{$code_href}fuer_einsteiger?von=footer'>Für Einsteiger</a>
                    <a href='{$code_href}fragen_und_antworten'>Fragen &amp; Antworten (FAQ)</a>
                    <a href='{$code_href}datenschutz'>Datenschutz</a>
                    <a href='{$code_href}sitemap'>Sitemap</a>
                    {$spam_honeypot}
                </div>
                <div id='edit-entity-react-root'></div>
                <div id='dialog-react-root'></div>
                <div id='update-user-avatar-react-root'></div>
            </body>
            </html>
            ZZZZZZZZZZ;
    }
}
