<?php

require_once __DIR__.'/../../../config/paths.php';
require_once __DIR__.'/../olz_popup/olz_popup.php';
require_once __DIR__.'/../olz_user_info_card/olz_user_info_card.php';

function olz_user_info_with_popup($user, $mode = 'name') {
    global $data_path, $data_href, $code_href;
    if ($mode == 'name') {
        $trigger = "<div class='olz-user-info-with-popup'>{$user->getFullName()}</div>";
        $popup = olz_user_info_card($user);
        return olz_popup($trigger, $popup);
    }
    if ($mode == 'name_picture') {
        $image_path = "img/users/{$user->getId()}.jpg";
        $img_src = is_file("{$data_path}{$image_path}") ? "{$data_href}{$image_path}" : "{$code_href}icns/user.jpg";
        $trigger = "<div class='olz-user-info-with-popup'><img src='{$img_src}' alt=''><br>{$user->getFullName()}</div>";
        $popup = olz_user_info_card($user);
        return olz_popup($trigger, $popup);
    }
    return "olz_user_info_with_popup: mode {$mode} nicht definiert";
}
