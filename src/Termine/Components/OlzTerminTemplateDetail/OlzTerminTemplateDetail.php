<?php

namespace Olz\Termine\Components\OlzTerminTemplateDetail;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Common\OlzLocationMap\OlzLocationMap;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Termine\TerminLabel;
use Olz\Entity\Termine\TerminTemplate;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{filter?: ?string}> */
class OlzTerminTemplateDetailParams extends HttpParams {
}

class OlzTerminTemplateDetail extends OlzComponent {
    /** @var array<string, string> */
    protected static $iconBasenameByType = [
        'programm' => 'termine_type_programm_20.svg',
        'weekend' => 'termine_type_weekend_20.svg',
        'ol' => 'termine_type_ol_20.svg',
        'training' => 'termine_type_training_20.svg',
        'club' => 'termine_type_club_20.svg',
        'meldeschluss' => 'termine_type_meldeschluss_20.svg',
    ];

    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams(OlzTerminTemplateDetailParams::class);

        $code_href = $this->envUtils()->getCodeHref();
        $code_path = $this->envUtils()->getCodePath();
        $user = $this->authUtils()->getCurrentUser();
        $id = $args['id'] ?? null;

        $termin_template = $this->getTerminTemplateById($id);

        if (!$termin_template) {
            $this->httpUtils()->dieWithHttpError(404);
        }

        $title = $termin_template->getTitle() ?? '';
        $back_link = "{$code_href}termine/vorlagen";
        $out = OlzHeader::render([
            'back_link' => $back_link,
            'title' => "{$title} - Vorlagen",
            'description' => "Vorlagen, um OL Zimmerberg-Termine zu erstellen.",
            'norobots' => true,
        ]);

        // Creation Tools
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $creation_tools = '';
        if ($has_termine_permissions) {
            $esc_template_id = json_encode($id);
            $creation_tools .= <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='create-termin-template-button'
                        class='btn btn-secondary'
                        onclick='return olz.initOlzEditTerminModal(undefined, {$esc_template_id})'
                    >
                        <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                        Neuer Termin aus dieser Vorlage
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        $out .= <<<ZZZZZZZZZZ
            <div class='content-right'>
                <div style='padding:4px 3px 10px 3px;'>
                    {$creation_tools}
                </div>
            </div>
            <div class='content-middle'>
            ZZZZZZZZZZ;

        $start_time = $termin_template->getStartTime() ?? '';
        $duration_seconds = $termin_template->getDurationSeconds() ?? '';
        $title = $termin_template->getTitle() ?? '';
        $text = $termin_template->getText() ?? '';
        $labels = [...$termin_template->getLabels()];
        $termin_location = $termin_template->getLocation();
        $image_ids = $termin_template->getImageIds();

        $out .= "<div class='olz-termin-template-detail'>";

        // Editing Tools
        $is_owner = $user && intval($termin_template->getOwnerUser()?->getId() ?? 0) === intval($user->getId());
        $has_termine_permissions = $this->authUtils()->hasPermission('termine');
        $can_edit = $is_owner || $has_termine_permissions;
        if ($can_edit) {
            $json_id = json_encode(intval($id));
            $out .= <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-termin-template-button'
                        class='btn btn-primary'
                        onclick='return olz.editTerminTemplate({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                    <button
                        id='delete-termin-template-button'
                        class='btn btn-danger'
                        onclick='return olz.deleteTerminTemplate({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/delete_white_16.svg' class='noborder' />
                        Löschen
                    </button>
                </div>
                ZZZZZZZZZZ;
        }

        $duration_interval = \DateInterval::createFromDateString("+{$duration_seconds} seconds");
        $end_time = (clone $start_time)->add($duration_interval);
        $pretty_date = $start_time ? (
            $duration_seconds
            ? $start_time->format('H:i')." – ".$end_time->format('H:i')
            : $start_time->format('H:i')
        ) : '(irgendwann)';
        $label_imgs = implode('', array_map(function (TerminLabel $label) use ($code_path, $code_href) {
            $ident = $label->getIdent();
            // TODO: Remove fallback mechanism?
            $fallback_path = "{$code_path}assets/icns/termine_type_{$ident}_20.svg";
            $fallback_href = is_file($fallback_path)
                ? "{$code_href}assets/icns/termine_type_{$ident}_20.svg" : null;
            $icon_href = $label->getIcon() ? $label->getFileHref($label->getIcon()) : $fallback_href;
            return $icon_href ? "<img src='{$icon_href}' alt='' class='type-icon'>" : '';
        }, $labels));

        $out .= "<h2>{$pretty_date}</h2>";
        $out .= "<h1>{$title} {$label_imgs}</h1>";

        if ($termin_location) {
            $out .= OlzLocationMap::render([
                'name' => $termin_location->getName(),
                'latitude' => $termin_location->getLatitude(),
                'longitude' => $termin_location->getLongitude(),
                'zoom' => 13,
            ]);
        }

        $text_html = $this->htmlUtils()->renderMarkdown($text);
        $text_html = $termin_template->replaceImagePaths($text_html);
        $text_html = $termin_template->replaceFilePaths($text_html);
        $out .= "<div>{$text_html}</div>";

        if (count($image_ids) > 0) {
            $out .= "<h3>Bilder</h3><div class='lightgallery gallery-container'>";
            foreach ($image_ids as $image_id) {
                $out .= "<div class='gallery-image'>";
                $out .= $this->imageUtils()->olzImage(
                    'termin_templates',
                    $id,
                    $image_id,
                    110,
                    'gallery[myset]'
                );
                $out .= "</div>";
            }
            $out .= "</div>";
        }

        $out .= "</div>"; // olz-termin-location-detail
        $out .= "</div>"; // content-middle

        $out .= OlzFooter::render();

        return $out;
    }

    protected function getTerminTemplateById(int $id): ?TerminTemplate {
        $termin_template_repo = $this->entityManager()->getRepository(TerminTemplate::class);
        return $termin_template_repo->findOneBy([
            'id' => $id,
            'on_off' => 1,
        ]);
    }
}
