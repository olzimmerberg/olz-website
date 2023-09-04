<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024All;

use Olz\Apps\Panini2024\Metadata;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Components\Users\OlzUserInfoWithPopup\OlzUserInfoWithPopup;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Utils\HttpUtils;

class OlzPanini2024All extends OlzComponent {
    public function getHtml($args = []): string {
        require_once __DIR__.'/../../../../../_/config/init.php';

        session_start_if_cookie_set();

        require_once __DIR__.'/../../../../../_/admin/olz_functions.php';

        $current_user = $this->authUtils()->getCurrentUser();
        $code_href = $this->envUtils()->getCodeHref();
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
        $out .= "<div class='olz-panini-2024-all'>";

        if ($this->authUtils()->hasPermission('panini2024')) {
            $panini_repo = $entity_manager->getRepository(Panini2024Picture::class);
            $pictures = $panini_repo->findAll();
            $out .= "<table>";
            $out .= <<<'ZZZZZZZZZZ'
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>OLZ-Konto</th>
                <th>Wappen</th>
                <th>Infos</th>
                <th>Aktiv</th>
                <th>Bild</th>
            </tr>
            ZZZZZZZZZZ;
            foreach ($pictures as $picture) {
                $id = $picture->getId();
                $line1 = $picture->getLine1();
                $line2 = $picture->getLine2();
                $owner_user = $picture->getOwnerUser();
                $user_html = $owner_user ? OlzUserInfoWithPopup::render([
                    'user' => $owner_user,
                    'mode' => 'name',
                ]) : '-';
                $association = $picture->getAssociation();
                $infos = $picture->getInfos();
                $infos_emojis = '';
                for ($i = 0; $i < 5; $i++) {
                    if ($i !== 0) {
                        $infos_emojis .= ' ';
                    }
                    $is_valid = ($infos[$i] ?? false) && strlen($infos[$i]) > 0;
                    $infos_emojis .= $is_valid ? '✅' : '❌';
                }
                $on_off = $picture->getOnOff();
                $on_off_emoji = $on_off ? '✅' : '❌';
                $out .= <<<ZZZZZZZZZZ
                <tr>
                    <td>{$id}</td>
                    <td>{$line1}<br/>{$line2}</td>
                    <td>{$user_html}</td>
                    <td>{$association}</td>
                    <td>{$infos_emojis}</td>
                    <td>{$on_off_emoji}</td>
                    <td id='panini-picture-{$id}'>
                        <button onclick='olzPanini2024.showPaniniPicture(&quot;{$id}&quot;)'>
                            anzeigen
                        </button>
                    </td>
                </tr>
                ZZZZZZZZZZ;
            }
            $out .= "</table>";
        } else {
            $out .= OlzNoAppAccess::render([
                'app' => $metadata,
            ]);
        }

        $out .= "</div>";
        $out .= "</div>";

        $out .= $metadata->getJsCssImports();
        $out .= OlzFooter::render();

        return $out;
    }
}
