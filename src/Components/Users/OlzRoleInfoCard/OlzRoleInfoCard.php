<?php

namespace Olz\Components\Users\OlzRoleInfoCard;

use Olz\Components\Common\OlzComponent;

class OlzRoleInfoCard extends OlzComponent {
    public function getHtml($args = []): string {
        $role = $args['role'];
        $user = $args['user'] ?? null;

        $code_href = $this->envUtils()->getCodeHref();

        $out = "<div class='olz-role-info-card'>";
        $out .= "<div class='role-name-container'><a href='{$code_href}verein/{$role->getUsername()}' class='linkint'>{$role->getName()}</a></div>";
        $users = $user ? [$user] : $role->getUsers();
        if (count($users) === 0) {
            $out .= "<p><i>Keine Ressort-Verantwortlichen</i></p>";
        }
        foreach ($users as $user) {
            $image_paths = $this->authUtils()->getUserAvatar($user);
            $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
            $img_html = "<img {$image_src_html} alt='' class='image'>";

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
            $email_out = $email_html ? "<div class='email'>{$email_html}</div>" : '';

            $out .= <<<ZZZZZZZZZZ
                <div class='user'>
                    <div class='avatar'>{$img_html}</div>
                    <div class='info'>
                        <div class='name'>{$user->getFullName()}</div>
                        {$email_out}
                    </div>
                </div>
                ZZZZZZZZZZ;
        }
        $out .= "</div>";
        return $out;
    }
}
