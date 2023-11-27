<?php

namespace Olz\Components\Users\OlzUserInfoWithPopup;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Users\OlzPopup\OlzPopup;
use Olz\Components\Users\OlzUserInfoCard\OlzUserInfoCard;

class OlzUserInfoWithPopup extends OlzComponent {
    public function getHtml($args = []): string {
        $user = $args['user'];
        $mode = $args['mode'] ?? 'name';

        if ($mode == 'name') {
            $trigger = "<div class='olz-user-info-with-popup'>{$user->getFullName()}</div>";
            $popup = OlzUserInfoCard::render(['user' => $user]);
            return OlzPopup::render(['trigger' => $trigger, 'popup' => $popup]);
        }
        if ($mode == 'name_picture') {
            $image_paths = $this->authUtils()->getUserAvatar($user);
            $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
            $img_html = "<img {$image_src_html} alt='' class='image'>";

            $trigger = "<div class='olz-user-info-with-popup'>{$img_html}<br>{$user->getFullName()}</div>";
            $popup = OlzUserInfoCard::render(['user' => $user]);
            return OlzPopup::render(['trigger' => $trigger, 'popup' => $popup]);
        }
        return "olz_user_info_with_popup: mode {$mode} nicht definiert";
    }
}
