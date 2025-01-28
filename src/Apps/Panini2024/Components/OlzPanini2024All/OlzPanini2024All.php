<?php

namespace Olz\Apps\Panini2024\Components\OlzPanini2024All;

use Olz\Apps\Panini2024\Metadata;
use Olz\Apps\Panini2024\Utils\Panini2024Utils;
use Olz\Components\Apps\OlzNoAppAccess\OlzNoAppAccess;
use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Panini2024\Panini2024Picture;
use Olz\Users\Components\OlzUserInfoWithPopup\OlzUserInfoWithPopup;
use Olz\Utils\HttpParams;

/** @extends HttpParams<array{}> */
class OlzPanini2024AllParams extends HttpParams {
}

class OlzPanini2024All extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $this->httpUtils()->validateGetParams(OlzPanini2024AllParams::class);
        $code_href = $this->envUtils()->getCodeHref();
        $entity_manager = $this->dbUtils()->getEntityManager();
        $metadata = new Metadata();

        $out = OlzHeader::render([
            'back_link' => "{$code_href}service/",
            'title' => "Panini '24 All",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full'>";
        $out .= "<div class='olz-panini-2024-all'>";

        if ($this->authUtils()->hasPermission('panini2024')) {
            $panini_repo = $entity_manager->getRepository(Panini2024Picture::class);
            $pictures = $panini_repo->findAll();
            $out .= "<table>";
            $ids = json_encode(array_map(function ($picture) {
                return $picture->getId();
            }, $pictures));
            $out .= <<<ZZZZZZZZZZ
                <tr>
                    <th class='column id'>ID</th>
                    <th class='column name'>Name</th>
                    <th class='column account'>OLZ-Konto</th>
                    <th class='column association'>Wappen</th>
                    <th class='column infos'>Infos</th>
                    <th class='column active'>Aktiv</th>
                    <th class='column picture'>
                        Bild
                        <button onclick='olzPanini2024.showPaniniPictures({$ids})'>
                            alle anzeigen
                        </button>
                    </th>
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
                $association_emoji = (Panini2024Utils::ASSOCIATION_MAP[$association] ?? 0) ? '✅' : '❌';
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
                        <td class='column id'>{$id}</td>
                        <td class='column name'>{$line1}<br/>{$line2}</td>
                        <td class='column account'>{$user_html}</td>
                        <td class='column association'>{$association_emoji} {$association}</td>
                        <td class='column infos'>{$infos_emojis}</td>
                        <td class='column active'>{$on_off_emoji}</td>
                        <td class='column picture' id='panini-picture-{$id}'>
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
