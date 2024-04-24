<?php

namespace Olz\Karten\Components\OlzKartenListItem;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Schema\OlzMapData\OlzMapData;

class OlzKartenListItem extends OlzComponent {
    public function getHtml($args = []): string {
        $out = '';
        $karte = $args['karte'];

        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $user = $this->authUtils()->getCurrentUser();

        $owner_user = $karte->getOwnerUser();
        $is_owner = $user && $owner_user && intval($owner_user->getId() ?? 0) === intval($user->getId());
        $has_all_permissions = $this->authUtils()->hasPermission('all');
        $can_edit = $is_owner || $has_all_permissions;
        $edit_admin = '';
        if ($can_edit) {
            $json_id = json_encode(intval($karte->getId()));
            $has_blog = $this->authUtils()->hasPermission('kaderblog', $user);
            $json_mode = htmlentities(json_encode($has_blog ? 'account_with_blog' : 'account'));
            $edit_admin = <<<ZZZZZZZZZZ
                <button
                    class='btn btn-secondary-outline btn-sm edit-karten-list-button'
                    onclick='return olz.kartenListItemEditKarte({$json_id}, {$json_mode})'
                >
                    <img src='{$code_href}assets/icns/edit_16.svg' class='noborder' />
                </button>
                ZZZZZZZZZZ;
        }

        $map = '';
        $preview = $karte->getPreviewImageId();
        if ($preview > '') {
            $img_href = "{$data_href}img/karten/{$karte->getId()}/img/{$preview}";
            $map = "<span class='lightgallery'><a href='{$img_href}' data-src='{$img_href}'><img src='{$code_href}assets/icns/magnifier_16.svg' style='float:right;border:none;'></a></span>";
        }

        $out .= OlzMapData::render([
            'name' => $karte->getName(),
            'year' => $karte->getYear(),
            'scale' => $karte->getScale(),
        ]);
        $name = $karte->getName();
        if ($karte->getKind() === 'scool') {
            $name = $name." (".$karte->getPlace().")";
        }
        $scale = $karte->getScale() ?? '';
        if ($scale === '') {
            $scale = "&nbsp;";
        }
        if ($karte->getCenterX() > 0) {
            $out .= <<<ZZZZZZZZZZ
                <tr class='olz-karten-list-item'>
                    <td>{$edit_admin}<a href='#{$name}' onclick='goto({$karte->getCenterX()},{$karte->getCenterY()},{$karte->getZoom()},&quot;{$name}&quot;);return false' class='linkmap' itemprop='name'>{$name}</a>{$map}</td>
                    <td>{$scale}</td>
                    <td>{$karte->getYear()}</td>
                </tr>
                ZZZZZZZZZZ;
        } else {
            $out .= <<<ZZZZZZZZZZ
                <tr class='olz-karten-list-item'>
                    <td>{$edit_admin}<span class='linkmap' itemprop='name'>{$name}</span></td>
                    <td>{$scale}</td>
                    <td>{$karte->getYear()}</td>
                </tr>
                ZZZZZZZZZZ;
        }

        return $out;
    }
}
