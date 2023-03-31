<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024;

use Olz\Apps\Panini2024\Metadata;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\AuthUtils;
use Olz\Utils\DbUtils;
use Olz\Utils\EnvUtils;
use Olz\Utils\HttpUtils;
use Olz\Utils\LogsUtils;

class OlzPanini2024 {
    public static function render($args = []) {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $logger = LogsUtils::fromEnv()->getLogger('Panini2024');
        $auth_utils = AuthUtils::fromEnv();
        $current_user = $auth_utils->getCurrentUser();
        $entity_manager = DbUtils::fromEnv()->getEntityManager();
        $env_utils = EnvUtils::fromEnv();
        $http_utils = HttpUtils::fromEnv();
        $http_utils->setLog($logger);
        $http_utils->validateGetParams([], $_GET);
        $data_path = $env_utils->getDataPath();

        $out = '';

        $out .= OlzHeader::render([
            'back_link' => "{$code_href}apps/",
            'title' => "Panini '24",
            'norobots' => true,
        ]);

        $esc_first_name = json_encode('');
        $esc_last_name = json_encode('');
        $esc_panini_2024_picture = json_encode(null);
        if ($current_user) {
            $esc_first_name = json_encode($current_user->getFirstName());
            $esc_last_name = json_encode($current_user->getLastName());
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

        $metadata = new Metadata();
        $out .= $metadata->getJsCssImports();

        $out .= OlzFooter::render();

        return $out;
    }
}
