<?php

namespace Olz\Components\Page\OlzFooter;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzFooter extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();

        $honeypot_username = $this->emailUtils()->generateSpamEmailAddress();
        $host = $this->envUtils()->getEmailForwardingHost();
        $honeypot_email = "{$honeypot_username}@{$host}";
        $honeypot_name = implode(' ', array_map(function ($part) {
            return ucfirst($part);
        }, explode('.', $honeypot_username)));
        $spam_honeypot = <<<ZZZZZZZZZZ
            <span class='kontakt'>
                Kontakt: <a href='mailto:{$honeypot_email}'>{$honeypot_name}</a>
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
            </body>
            </html>
            ZZZZZZZZZZ;
    }
}
