<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024All;

use Olz\Apps\Panini2024\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\HttpUtils;

class OlzPanini2024All extends OlzComponent {
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
            'title' => "Panini '24 All",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";

        if ($this->authUtils()->hasPermission('panini2024')) {
            $panini_repo = $entity_manager->getRepository(Panini2024Picture::class);
            $pictures = $panini_repo->findAll();
            foreach ($pictures as $picture) {
                $id = $picture->getId();
                $out .= <<<ZZZZZZZZZZ
                <div>
                    {$id}: 
                    <img
                        src='/apps/panini24/single/{$id}.jpg'
                        alt='{$id}'
                        style='max-width: 250px; max-height: 250px;'
                    />
                </div>
                ZZZZZZZZZZ;
            }
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
