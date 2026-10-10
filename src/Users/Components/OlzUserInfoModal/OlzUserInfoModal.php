<?php

namespace Olz\Users\Components\OlzUserInfoModal;

use Olz\Common\Components\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzUserInfoModal extends OlzComponent {
    public function getHtml(mixed $args): string {
        $user = $args['user'];
        $mode = $args['mode'] ?? 'name';
        $user_id = intval($user->getId());
        $esc_full_name = htmlspecialchars($user->getFullName());

        if ($mode == 'name') {
            return <<<ZZZZZZZZZZ
                <a
                    href='#'
                    title='{$esc_full_name}'
                    onclick='return olz.initOlzUserInfoModal({$user_id})'
                    class='olz-user-info-modal-trigger name'
                >
                    {$esc_full_name}
                </a>
                ZZZZZZZZZZ;
        }
        if ($mode == 'name_picture') {
            $image_paths = $this->authUtils()->getUserAvatar($user);
            $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
            $img_html = "<img {$image_src_html} alt='' class='image'>";

            return <<<ZZZZZZZZZZ
                <a
                    href='#'
                    onclick='return olz.initOlzUserInfoModal({$user_id})'
                    class='olz-user-info-modal-trigger'
                >
                    {$img_html}
                    <div title='{$esc_full_name}' class='name'>{$esc_full_name}</div>
                </a>
                ZZZZZZZZZZ;
        }
        return "olz_user_info_with_popup: mode {$mode} nicht definiert";
    }
}
