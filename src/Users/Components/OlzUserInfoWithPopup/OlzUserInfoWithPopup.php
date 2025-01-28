<?php

namespace Olz\Users\Components\OlzUserInfoWithPopup;

use Olz\Components\Common\OlzComponent;

class OlzUserInfoWithPopup extends OlzComponent {
    /** @param array<string, mixed> $args */
    public function getHtml(array $args = []): string {
        $user = $args['user'];
        $mode = $args['mode'] ?? 'name';
        $user_id = intval($user->getId());

        if ($mode == 'name') {
            return "<div><a href='#' onclick='return olz.initOlzUserInfoModal({$user_id})' class='olz-user-info-with-popup'>{$user->getFullName()}</a></div>";
        }
        if ($mode == 'name_picture') {
            $image_paths = $this->authUtils()->getUserAvatar($user);
            $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
            $img_html = "<img {$image_src_html} alt='' class='image'>";

            return "<div><a href='#' onclick='return olz.initOlzUserInfoModal({$user_id})' class='olz-user-info-with-popup'>{$img_html}<br>{$user->getFullName()}</a></div>";
        }
        return "olz_user_info_with_popup: mode {$mode} nicht definiert";
    }
}
