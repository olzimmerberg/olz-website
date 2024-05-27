<?php

namespace Olz\Components\Users\OlzUserInfoCard;

use Olz\Components\Common\OlzComponent;

class OlzUserInfoCard extends OlzComponent {
    public function getHtml(array $args = []): string {
        $user = $args['user'];

        $image_paths = $this->authUtils()->getUserAvatar($user);
        $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
        $img_html = "<img {$image_src_html} alt='' class='image'>";

        $out = "<div class='olz-user-info-card'>";
        $out .= "<div class='image-container'>{$img_html}</div>";
        $out .= "<div class='name-container'>{$user->getFullName()}</div>";
        // $out .= ($row["adresse"] ? "<br>".$row["adresse"] : "");
        // $out .= ($row["tel"] ? "<br>Tel. ".$row["tel"] : "");
        $has_official_email = $this->authUtils()->hasPermission('user_email', $user);
        $email_html = '';
        if ($has_official_email) {
            $email = $user->getUsername().'@olzimmerberg.ch';
            $email_html = $this->htmlUtils()->replaceEmailAdresses($email);
        } else {
            $email_html = (
                $user->getEmail()
                ? $this->htmlUtils()->replaceEmailAdresses($user->getEmail())
                : ''
            );
        }
        if ($email_html) {
            $out .= "<div class='email-container'>{$email_html}</div>";
        }
        $out .= "</div>";
        return $out;
    }
}
