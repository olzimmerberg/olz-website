<?php

namespace Olz\Termine\Components\OlzTerminTemplatesList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Termine\Components\OlzTerminTemplatesListItem\OlzTerminTemplatesListItem;

class OlzTerminTemplatesList extends OlzComponent {
    public function getHtml($args = []): string {
        $this->httpUtils()->validateGetParams([]);
        $code_href = $this->envUtils()->getCodeHref();

        if (!$this->authUtils()->hasPermission('termine')) {
            $this->httpUtils()->dieWithHttpError(401);
        }

        $out = OlzHeader::render([
            'back_link' => "{$code_href}termine",
            'title' => 'Termin-Vorlagen',
            'description' => "Vorlagen, um OL Zimmerberg-Termine zu erstellen.",
            'norobots' => true,
        ]);

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
                <div style='padding:0 10px 10px 10px;'>
                    <button
                        id='termin-solv-import-button'
                        class='btn btn-secondary'
                    >
                        <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                        Von SOLV importieren (TODO)
                    </button>
                </div>
            </div>
            <div class='content-middle'>
                <button
                    id='create-termin-template-button'
                    class='btn btn-secondary'
                    onclick='return olz.initOlzEditTerminTemplateModal()'
                >
                    <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                    Neue Vorlage erstellen
                </button>
                <h1>Termin-Vorlagen</h1>
            ZZZZZZZZZZ;
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        $termin_templates = $termin_template_repo->findAll();
        foreach ($termin_templates as $termin_template) {
            $out .= OlzTerminTemplatesListItem::render([
                'termin_template' => $termin_template,
            ]);
        }

        $out .= <<<'ZZZZZZZZZZ'
            </div>
            ZZZZZZZZZZ;

        $out .= OlzFooter::render();

        return $out;
    }
}
