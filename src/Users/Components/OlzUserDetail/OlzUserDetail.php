<?php

namespace Olz\Users\Components\OlzUserDetail;

use Olz\Components\Common\OlzComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\Users\User;

/** @extends OlzComponent<array<string, mixed>> */
class OlzUserDetail extends OlzComponent {
    public function getHtml(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $args['id']]);

        if (!$user) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $out = OlzHeader::render([
            'back_link' => "{$code_href}verein",
            'title' => $user->getFullName(),
            'description' => "{$user->getFullName()} - Profil.",
            'norobots' => true,
        ]);

        $out .= "<div class='content-full olz-user-detail'>";

        $image_paths = $this->authUtils()->getUserAvatar($user);
        $image_src_html = $this->htmlUtils()->getImageSrcHtml($image_paths);
        $img_html = "<img {$image_src_html} alt='' class='image'>";

        $auth_user_id = $this->session()->get('auth_user_id');
        $is_parent = $auth_user_id && intval($user->getParentUserId()) === intval($auth_user_id);
        $is_self = $auth_user_id && intval($user->getId()) === intval($auth_user_id);
        $has_permissions = $this->authUtils()->hasPermission('users');
        $can_edit = $is_parent || $is_self || $has_permissions;
        $edit_admin = '';
        $edit_password = '';
        if ($can_edit) {
            $json_id = json_encode($user->getId());
            $edit_admin = <<<ZZZZZZZZZZ
                <div>
                    <button
                        id='edit-user-button'
                        class='btn btn-primary'
                        onclick='return olz.editUser({$json_id})'
                    >
                        <img src='{$code_href}assets/icns/edit_white_16.svg' class='noborder' />
                        Bearbeiten
                    </button>
                </div>
                ZZZZZZZZZZ;
            $edit_password = <<<'ZZZZZZZZZZ'
                     <button
                        class='btn btn-secondary'
                        onclick='return olz.initOlzChangePasswordModal()'
                        id='change-password-button'
                    >
                        Passwort ändern
                    </button>
                ZZZZZZZZZZ;
        }

        $street = $user->getStreet() ?? '(Keine Adresse)';
        $postal_code = $user->getPostalCode() ?? '(Keine PLZ)';
        $city = $user->getCity() ?? '(Kein Ort)';
        $region = $user->getRegion() ?? 'Keine Region';
        $country_code = $user->getCountryCode() ?? 'Kein Land';
        $birthdate = $user->getBirthdate()?->format('d.m.Y') ?? '(Unbekannt)';
        $phone = $user->getPhone() ?? '(Unbekannt)';

        if (
            !$user->getParentUserId()
            && !$user->isEmailVerified()
            && !$this->authUtils()->hasPermission('verified_email', $user)
        ) {
            if ($user->getEmailVerificationToken()) {
                $out .= <<<'ZZZZZZZZZZ'
                    <div class='alert alert-danger' role='alert'>
                        Deine E-Mail-Adresse ist noch nicht bestätigt. Bitte prüfe deine Inbox (und dein Spam-Postfach) auf unsere Bestätigungs-E-Mail (Betreff: "[OLZ] E-Mail bestätigen").
                        <a
                            href='#'
                            onclick='olz.initOlzVerifyUserEmailModal()'
                            id='verify-user-email-link'
                        >
                            Erneut senden
                        </a>
                    </div>
                    ZZZZZZZZZZ;
            } else {
                $out .= <<<'ZZZZZZZZZZ'
                    <div class='alert alert-danger' role='alert'>
                        Deine E-Mail-Adresse ist noch nicht bestätigt.
                        <a
                            href='#'
                            onclick='olz.initOlzVerifyUserEmailModal()'
                            id='verify-user-email-link'
                        >
                            Jetzt bestätigen
                        </a>
                    </div>
                    ZZZZZZZZZZ;
            }
        }

        $out .= "<div class='edit-user-container'>{$edit_admin}</div>";
        $out .= "<div class='image-container'>{$img_html}</div>";
        $out .= "<h1 class='name-container'>{$user->getFullName()}</h1>";
        $out .= "<div class='info-container username'>Benutzername: {$user->getUsername()}</div>";
        if ($can_edit) {
            $out .= "<div class='info-container address1'>{$street}</div>";
            $out .= "<div class='info-container address2'>{$postal_code} {$city} ({$region}, {$country_code})</div>";
            $out .= "<div class='info-container birthdate'>Geburtsdatum: {$birthdate}</div>";
            $out .= "<div class='info-container phone'>Telephon: {$phone}</div>";
        }

        $has_official_email = $this->authUtils()->hasPermission('user_email', $user);
        $email_html = '';
        if ($has_official_email) {
            $host = $this->envUtils()->getEmailForwardingHost();
            $email = "{$user->getUsername()}@{$host}";
            $email_html = $this->htmlUtils()->replaceEmailAdresses($email);
        } else {
            $email_html = (
                $user->getEmail()
                ? $this->htmlUtils()->replaceEmailAdresses($user->getEmail())
                : ''
            );
        }
        if ($email_html) {
            $out .= "<div class='info-container email'>{$email_html}</div>";
        }
        $out .= $edit_password;

        if ($can_edit) {
            $out .= "<h2>Familie</h2>";
            $child_users = $user_repo->findBy(['parent_user' => $user->getId()]);
            if ($child_users) {
                $out .= "<ul class='info-container'>";
                foreach ($child_users as $child_user) {
                    $out .= "<li>Familienmitglied <a href='{$code_href}benutzer/{$child_user->getId()}'>{$child_user->getFullName()}</a></li>";
                }
                $out .= "</ul>";
            }
            if ($user->getParentUserId()) {
                $parent_user = $user_repo->findOneBy(['id' => $user->getParentUserId()]);
                $out .= "<div class='info-container'>Familienmitglied von <a href='{$code_href}benutzer/{$parent_user?->getId()}'>{$parent_user?->getFullName()}</a></div>";
                if ($child_users) {
                    $this->log()->warning("User {$user->getId()} has parent and children.");
                }
            } else {
                $json_id = json_encode($user->getId());
                $out .= <<<ZZZZZZZZZZ
                    <div>
                        <button
                            id='add-child-user-button'
                            class='btn btn-secondary'
                            onclick='return olz.addChildUser({$json_id})'
                        >
                            <img src='{$code_href}assets/icns/new_white_16.svg' class='noborder' />
                            Familienmitglied hinzufügen
                        </button>
                    </div>
                    ZZZZZZZZZZ;
            }
        }
        $out .= "</div>";

        $out .= OlzFooter::render();

        return $out;
    }
}
