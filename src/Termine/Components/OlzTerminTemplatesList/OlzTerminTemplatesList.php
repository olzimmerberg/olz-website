<?php

namespace Olz\Termine\Components\OlzTerminTemplatesList;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Termine\Components\OlzTerminTemplatesListItem\OlzTerminTemplatesListItem;
use Olz\Utils\HttpUtils;
use PhpTypeScriptApi\Fields\FieldTypes;

class OlzTerminTemplatesList extends OlzComponent {
    public function getHtml($args = []): string {
        $code_href = $this->envUtils()->getCodeHref();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $validated_get_params = $http_utils->validateGetParams([
            'filter' => new FieldTypes\StringField(['allow_null' => true]),
            'id' => new FieldTypes\IntegerField(['allow_null' => true]),
        ], $_GET);

        if (!$this->authUtils()->hasPermission('termine')) {
            $this->httpUtils()->dieWithHttpError(401);
        }

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}termine",
            'title' => 'Termin-Orte',
            'description' => "Orte, an denen Anlässe der OL Zimmerberg stattfinden.",
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
