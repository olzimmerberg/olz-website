<?php

namespace Olz\Components\Users\OlzUserInfoCard;

use Olz\Components\Common\OlzComponent;

class OlzUserInfoCard extends OlzComponent {
    public function getHtml($args = []): string {
        $user = $args['user'];

        require_once __DIR__.'/../../../../_/admin/olz_functions.php';

        $code_href = $this->envUtils()->getCodeHref();
        $data_href = $this->envUtils()->getDataHref();
        $data_path = $this->envUtils()->getDataPath();

        $image_path = $this->authUtils()->getUserAvatar($user);
        $img_html = "<img src='{$image_path}' alt='' class='image'>";

        $out = "<div class='olz-user-info-card'>";
        $out .= "<div class='image-container'>{$img_html}</div>";
        $out .= "<div class='name-container'>{$user->getFullName()}</div>";
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
