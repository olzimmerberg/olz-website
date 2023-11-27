<?php

namespace Olz\Components\Users\OlzRoleInfoCard;

use Olz\Components\Common\OlzComponent;

class OlzRoleInfoCard extends OlzComponent {
    public function getHtml($args = []): string {
        $role = $args['role'];
        $user = $args['user'] ?? null;

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $code_href = $this->envUtils()->getCodeHref();
        $data_path = $this->envUtils()->getDataPath();

        $img_html = "<img src='{$code_href}assets/icns/role.svg' alt='' class='logo'>";
        if ($user) {
            $image_paths = $this->authUtils()->getUserAvatar($user);
            $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
            $img_html = "<img {$image_src_html} alt='' class='image'>";
        }

        $out = "<div class='olz-role-info-card'>";
        $out .= "<div class='role-name-container'><a href='{$code_href}verein/{$role->getUsername()}' class='linkint'>{$role->getName()}</a></div>";
        $out .= "<div class='image-container'>{$img_html}</div>";
        $out .= "<div class='user-name-container'>{$user->getFullName()}</div>";
        // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
        // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
        $has_official_email = $this->authUtils()->hasPermission('user_email', $user);
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
