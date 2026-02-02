<?php

namespace Olz\Users\Components\OlzUserInfoModal;

use Olz\Components\Common\OlzComponent;

/** @extends OlzComponent<array<string, mixed>> */
class OlzUserInfoModal extends OlzComponent {
    public function getHtml(mixed $args): string {
        $user = $args['user'];
        $mode = $args['mode'] ?? 'name';
        $user_id = intval($user->getId());

        if ($mode == 'name') {
            return <<<ZZZZZZZZZZ
                <a
                    href='#'
                    onclick='return olz.initOlzUserInfoModal({$user_id})'
                    class='olz-user-info-modal-trigger'
                >
                    {$user->getFullName()}
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
                    <div class='name'>{$user->getFullName()}</div>
                </a>
                ZZZZZZZZZZ;
        }
        return "olz_user_info_with_popup: mode {$mode} nicht definiert";
    }
}
