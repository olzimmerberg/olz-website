<?php

require_once __DIR__.'/../../../config/server.php';
require_once __DIR__.'/../../../admin/olz_functions.php';

function olz_user_info_card($user) {
    global $_CONFIG;
    $image_path = "img/users/{$user->getId()}.jpg";
    $out = "<table><tr><td style='width:1px;'>";
    $out .= is_file("{$_CONFIG->getDataPath()}{$image_path}") ? "<img src='{$_CONFIG->getDataHref()}{$image_path}' alt='' style='height:64px;'>" : "&nbsp;";
    $out .= "</td><td style='padding-left:10px;'>";
    $out .= "<b>{$user->getFullName()}</b>";
    // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
    // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
    $out .= ($user->getEmail() ? "<br>".olz_mask_email($user->getEmail(), "Email", "") : "");
    $out .= "</td></tr></table>";
    return $out;
}
