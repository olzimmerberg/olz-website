<?php

function olz_user_info_with_popup($user, $mode = 'name') {
    global $_CONFIG;

    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../olz_popup/olz_popup.php';
    require_once __DIR__.'/../olz_user_info_card/olz_user_info_card.php';

    if ($mode == 'name') {
        $trigger = "<div class='olz-user-info-with-popup'>{$user->getFullName()}</div>";
        $popup = olz_user_info_card($user);
        return olz_popup($trigger, $popup);
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
        $popup = olz_user_info_card($user);
        return olz_popup($trigger, $popup);
    }
    return "olz_user_info_with_popup: mode {$mode} nicht definiert";
}
