<?php

namespace Olz\Components\Users\OlzRoleInfoCard;

use Olz\Utils\AuthUtils;
use Olz\Utils\EnvUtils;

class OlzRoleInfoCard {
    public static function render($args = []) {
        $role = $args['role'];
        $user = $args['user'] ?? null;

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $auth_utils = AuthUtils::fromEnv();
        $env_utils = EnvUtils::fromEnv();
        $code_href = $env_utils->getCodeHref();
        $data_href = $env_utils->getDataHref();
        $data_path = $env_utils->getDataPath();

        $img_html = "<img src='/icns/role.svg' alt='' class='logo'>";
        if ($user) {
            $image_base_path = "img/users/{$user->getId()}";
            $initials = strtoupper($user->getFirstName()[0].$user->getLastName()[0]);
            $img_html = "<img src='{$code_href}icns/user.php?initials={$initials}' alt='' class='image'>";
            if (is_file("{$data_path}{$image_base_path}.jpg")) {
                $img_html = "<img src='{$data_href}{$image_base_path}.jpg' alt='' class='image'>";
            }
        }

        $out = "<div class='olz-role-info-card bg-green'>";
        $out .= "<div class='role-name-container'><a href='{$code_href}verein.php?ressort={$role->getUsername()}' class='linkint'>{$role->getName()}</a></div>";
        $out .= "<div class='image-container'>{$img_html}</div>";
        $out .= "<div class='user-name-container'>{$user->getFullName()}</div>";
        // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
        // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
        $has_official_email = $auth_utils->hasPermission('user_email', $user);
        $email_html = '';
        if ($has_official_email) {
            $email = $user->getUsername().'@olzimmerberg.ch';
            $email_html = olz_mask_email($email, "Email", "");
        } else {
            $email_html = ($user->getEmail() ? olz_mask_email($user->getEmail(), "Email", "") : '');
        }
        if ($email_html) {
            $out .= "<div class='email-container'>{$email_html}</div>";
        }
        $out .= "</div>";
        return $out;
    }
}
