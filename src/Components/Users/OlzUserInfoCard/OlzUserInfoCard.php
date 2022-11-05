<?php

namespace Olz\Components\Users\OlzUserInfoCard;

use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;

class OlzUserInfoCard {
    public static function render($args = []) {
        $user = $args['user'];

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();
        $data_path = $env_utils->getDataPath();

        $image_base_path = "img/users/{$user->getId()}";
        $img_html = "<img src='{$code_href}icns/user.jpg' alt='' class='olz-user-info-card-image'>";
        if (is_file("{$data_path}{$image_base_path}.jpg")) {
            $img_html = "<img src='{$data_href}{$image_base_path}.jpg' alt='' class='olz-user-info-card-image'>";
        }

        $out = "<table><tr><td class='olz-user-info-card-image-td'>";
        $out .= $img_html;
        $out .= "</td><td class='olz-user-info-card-info-td'>";
        $out .= "<b>{$user->getFullName()}</b>";
        // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
        // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
        $has_official_email = $auth_utils->hasPermission('user_email', $user);
        if ($has_official_email) {
            $email = $user->getUsername().'@olzimmerberg.ch';
            $out .= "<br>".olz_mask_email($email, "Email", "");
        } else {
            $out .= ($user->getEmail() ? "<br>".olz_mask_email($user->getEmail(), "Email", "") : "");
        }
        $out .= "</td></tr></table>";
        return $out;
    }
}
