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
    public function hasAccess(): bool {
        return $this->authUtils()->hasPermission('termine');
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        $code_href = $this->envUtils()->getCodeHref();
        return $this->searchUtils()->getStaticResultQuery([
            'link' => "{$code_href}termine/vorlagen",
            'icon' => "{$code_href}assets/icns/termine_type_all_20.svg",
            'title' => $this->getPageTitle(),
            'text' => $this->getPageDescription(),
        ], $terms);
    }

    public function getPageTitle(): string {
        return "Termin-Vorlagen";
    }

    public function getPageDescription(): string {
        return "Vorlagen, um Termine effizienter erstellen zu können.";
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzTerminTemplatesListParams::class);
        $code_href = $this->envUtils()->getCodeHref();

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
