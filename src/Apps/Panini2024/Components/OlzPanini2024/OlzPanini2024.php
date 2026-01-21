<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024;

use Olz\Apps\Panini2024\Metadata;
use Olz\Apps\Panini2024\Panini2024Constants;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzPanini2024Params extends HttpParams {
}

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzPanini2024 extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        return null;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $this->httpUtils()->validateGetParams(OlzPanini2024Params::class);
        $current_user = $this->authUtils()->getCurrentUser();
        $code_href = $this->envUtils()->getCodeHref();
        $entity_manager = $this->dbUtils()->getEntityManager();
        $data_path = $this->envUtils()->getDataPath();
        $metadata = new Metadata();
        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        $deadline_datetime = new \DateTime(Panini2024Constants::UPDATE_DEADLINE);

        $has_admin_access = $this->authUtils()->hasPermission('all');
        $is_read_only = ($now_datetime > $deadline_datetime && !$has_admin_access);

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Panini '24",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        if ($current_user) {
            $esc_first_name = json_encode($current_user->getFirstName());
            $esc_last_name = json_encode($current_user->getLastName());
            $esc_panini_2024_picture = json_encode(null);
            $esc_is_read_only = json_encode($is_read_only);
            $panini_repo = $entity_manager->getRepository(Panini2024Picture::class);
            $picture = $panini_repo->findOneBy(['owner_user' => $current_user]);
            if ($picture) {
                $esc_panini_2024_picture = json_encode([
                    'id' => intval($picture->getId()),
                    'line1' => $picture->getLine1(),
                    'line2' => $picture->getLine2(),
                    'association' => $picture->getAssociation(),
                    'imgSrc' => $picture->getImgSrc(),
                    'infos' => $picture->getInfos(),
                ]);
                $portraits_path = "{$data_path}panini_data/portraits/";
                $portrait_path = "{$portraits_path}{$picture->getId()}/{$picture->getImgSrc()}";
                $temp_path = "{$data_path}temp/{$picture->getImgSrc()}";
                copy($portrait_path, $temp_path);
            }
            $out .= <<<ZZZZZZZZZZ
                <script>
                    window.olzPanini2024FirstName = {$esc_first_name};
                    window.olzPanini2024LastName = {$esc_last_name};
                    window.olzPanini2024Picture = {$esc_panini_2024_picture};
                    window.olzPanini2024IsReadOnly = {$esc_is_read_only};
                </script>
                <div id='panini-react-root'>
                    Lädt...
                </div>
                ZZZZZZZZZZ;
        } else {
            $out .= OlzNoAppAccess::render([
                'app' => $metadata,
            ]);
        }

        $out .= "</div>";

        $out .= $metadata->getJsCssImports();
        $out .= OlzFooter::render();

        return $out;
    }
}
