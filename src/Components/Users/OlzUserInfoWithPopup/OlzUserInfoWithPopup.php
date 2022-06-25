<?php

namespace Olz\Components\Users\OlzUserInfoWithPopup;

use Olz\Components\Users\OlzPopup\OlzPopup;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;

class OlzUserInfoWithPopup {
    public static function render($args = []) {
        global $_CONFIG;

        $user = $args['user'];
        $mode = $args['mode'] ?? 'name';

        require_once __DIR__.'/../../../../_/config/server.php';

        if ($mode == 'name') {
            $trigger = "<div class='olz-user-info-with-popup'>{$user->getFullName()}</div>";
            $popup = OlzUserInfoCard::render(['user' => $user]);
            return OlzPopup::render(['trigger' => $trigger, 'popup' => $popup]);
        }
        if ($mode == 'name_picture') {
            $image_base_path = "img/users/{$user->getId()}";
            $img_html = "<img src='{$_CONFIG->getCodeHref()}icns/user.jpg' alt=''>";
            if (is_file("{$_CONFIG->getDataPath()}{$image_base_path}.jpg")) {
                $img_html = "<img src='{$_CONFIG->getDataHref()}{$image_base_path}.jpg' alt=''>";
                if (is_file("{$_CONFIG->getDataPath()}{$image_base_path}@2x.jpg")) {
                    $img_html = <<<ZZZZZZZZZZ
                    <img
                        srcset='
                            {$_CONFIG->getDataHref()}{$image_base_path}@2x.jpg 2x,
                            {$_CONFIG->getDataHref()}{$image_base_path}.jpg 1x
                        '
                        src='{$_CONFIG->getDataHref()}{$image_base_path}.jpg'
                        alt=''
                    >
                    ZZZZZZZZZZ;
                }
            }
            $trigger = "<div class='olz-user-info-with-popup'>{$img_html}<br>{$user->getFullName()}</div>";
            $popup = OlzUserInfoCard::render(['user' => $user]);
            return OlzPopup::render(['trigger' => $trigger, 'popup' => $popup]);
        }
        return "olz_user_info_with_popup: mode {$mode} nicht definiert";
    }
}
