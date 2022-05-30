<?php

function olz_user_info_card($user) {
    global $_CONFIG;

    require_once __DIR__.'/../../../config/server.php';
    require_once __DIR__.'/../../../admin/olz_functions.php';

    $image_base_path = "img/users/{$user->getId()}";
    $img_html = "<img src='{$_CONFIG->getCodeHref()}icns/user.jpg' alt='' class='olz-user-info-card-image'>";
    if (is_file("{$_CONFIG->getDataPath()}{$image_base_path}.jpg")) {
        $img_html = "<img src='{$_CONFIG->getDataHref()}{$image_base_path}.jpg' alt='' class='olz-user-info-card-image'>";
    }

    $out = "<table><tr><td class='olz-user-info-card-image-td'>";
    $out .= $img_html;
    $out .= "</td><td class='olz-user-info-card-info-td'>";
    $out .= "<b>{$user->getFullName()}</b>";
    // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
    // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
    $out .= ($user->getEmail() ? "<br>".olz_mask_email($user->getEmail(), "Email", "") : "");
    $out .= "</td></tr></table>";
    return $out;
}
