<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024;

use Olz\Apps\Panini2024\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\HttpUtils;

class OlzPanini2024 extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $current_user = $this->authUtils()->getCurrentUser();
        $entity_manager = $this->dbUtils()->getEntityManager();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($this->log());
        $http_utils->validateGetParams([], $_GET);
        $data_path = $this->envUtils()->getDataPath();
        $metadata = new Metadata();

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Panini '24",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        if ($current_user) {
            $esc_first_name = json_encode($current_user->getFirstName());
            $esc_last_name = json_encode($current_user->getLastName());
            $esc_panini_2024_picture = json_encode(null);
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
                $portrait_path = "{$portraits_path}{$picture->getImgSrc()}";
                $temp_path = "{$data_path}temp/{$picture->getImgSrc()}";
                copy($portrait_path, $temp_path);
            }
            $out .= <<<ZZZZZZZZZZ
            <script>
                window.olzPanini2024FirstName = {$esc_first_name};
                window.olzPanini2024LastName = {$esc_last_name};
                window.olzPanini2024Picture = {$esc_panini_2024_picture};
            </script>
            <div id='react-root' class='content-full'>
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
