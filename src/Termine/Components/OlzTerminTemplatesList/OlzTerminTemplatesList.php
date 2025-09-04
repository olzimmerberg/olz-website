<?php

namespace Olz\Termine\Components\OlzTerminTemplatesList;

use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Termine\Components\OlzTerminTemplatesListItem\OlzTerminTemplatesListItem;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzTerminTemplatesListParams extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzTerminTemplatesList extends OlzRootComponent {
    public function getSearchTitle(): string {
        return 'TODO';
    }

    public function getSearchResults(array $terms): array {
        return [];
    }

    public function getHtml(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzTerminTemplatesListParams::class);
        $code_href = $this->envUtils()->getCodeHref();

        if (!$this->authUtils()->hasPermission('termine')) {
            $this->httpUtils()->dieWithHttpError(401);
            throw new \Exception('should already have failed');
        }

        $out = OlzHeader::render([
            'back_link' => "{$code_href}termine",
            'title' => 'Termin-Vorlagen',
            'description' => "Vorlagen, um OL Zimmerberg-Termine zu erstellen.",
            'norobots' => true,
        ]);

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
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
        $termin_templates = $termin_template_repo->findBy(['on_off' => 1]);
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
