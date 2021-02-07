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
        $image_path = "img/users/{$user->getId()}.jpg";
        $img_src = is_file("{$_CONFIG->getDataPath()}{$image_path}") ? "{$_CONFIG->getDataHref()}{$image_path}" : "{$_CONFIG->getCodeHref()}icns/user.jpg";
        $trigger = "<div class='olz-user-info-with-popup'><img src='{$img_src}' alt=''><br>{$user->getFullName()}</div>";
        $popup = olz_user_info_card($user);
        return olz_popup($trigger, $popup);
    }
    return "olz_user_info_with_popup: mode {$mode} nicht definiert";
}
