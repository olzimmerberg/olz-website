<?php

require_once __DIR__.'/../../../config/paths.php';
require_once __DIR__.'/../../../admin/olz_functions.php';

function olz_user_info_card($user) {
    global $data_path, $data_href;
    $image_path = "img/users/{$user->getId()}.jpg";
    $out = "<table><tr><td style='width:1px;'>";
    $out .= is_file("{$data_path}{$image_path}") ? "<img src='{$data_href}{$image_path}' alt='' style='height:64px;'>" : "&nbsp;";
    $out .= "</td><td style='padding-left:10px;'>";
    $out .= "<b>{$user->getFullName()}</b>";
    // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
    // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
    $out .= ($user->getEmail() ? "<br>".olz_mask_email($user->getEmail(), "Email", "") : "");
    $out .= "</td></tr></table>";
    return $out;
}
