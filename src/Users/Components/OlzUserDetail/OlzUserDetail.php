<?php

namespace Olz\Users\Components\OlzUserDetail;

use Doctrine\Common\Collections\Criteria;
use Olz\Components\Common\OlzRootComponent;
use Olz\Components\Page\OlzFooter\OlzFooter;
use Olz\Components\Page\OlzHeader\OlzHeader;
use Olz\Entity\ForwardedEmail;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use Olz\Repository\Roles\PredefinedRole;
use Olz\Roles\Components\OlzRoleInfoModal\OlzRoleInfoModal;

/** @extends OlzRootComponent<array<string, mixed>> */
class OlzUserDetail extends OlzRootComponent {
    public function hasAccess(): bool {
        return true;
    }

    public function searchSqlWhenHasAccess(array $terms): string|array|null {
        return null;
    }

    public function getHtmlWhenHasAccess(mixed $args): string {
        $code_href = $this->envUtils()->getCodeHref();
        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $args['id']]);
        if (!$user) {
            $this->httpUtils()->dieWithHttpError(404);
            throw new \Exception('should already have failed');
        }

        $user_id = $user->getId() ?? -1;
        $parent_id = $user->getParentUserId() ?? -1;
        $auth_user_id = $this->authUtils()->getCurrentAuthUser()?->getId();
        $is_root = $this->authUtils()->hasPermission('all');
        if (!$is_root && $user_id !== $auth_user_id && $parent_id !== $auth_user_id) {
            $this->httpUtils()->dieWithHttpError(403);
            throw new \Exception('should already have failed');
        }

        $role_repo = $this->entityManager()->getRepository(Role::class);
        $sysadmin_role = $role_repo->getPredefinedRole(PredefinedRole::Sysadmin);

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

        $out .= <<<ZZZZZZZZZZ
            <div class='edit-user-container'>{$edit_admin}</div>
            <div class='image-container'>{$img_html}</div>
            <h1 class='name-container'>{$user->getFullName()}</h1>
            <div class='info-container username'>Benutzername: {$user->getUsername()}</div>
            ZZZZZZZZZZ;
        if ($can_edit) {
            $out .= <<<ZZZZZZZZZZ
                <div class='info-container address'>
                    <div>{$street}</div>
                    <div>{$postal_code} {$city} ({$region}, {$country_code})</div>
                </div>
                <div class='info-container birthdate'>Geburtsdatum: {$birthdate}</div>
                <div class='info-container phone'>Telephon: {$phone}</div>
                ZZZZZZZZZZ;
        }

        $has_official_email = $this->authUtils()->hasPermission('user_email', $user);
        $email_html = '';
        if ($has_official_email) {
            $host = $this->envUtils()->getEmailForwardingHost();
            $olz_email = "{$user->getUsername()}@{$host}";
            $email = $user->getEmail() ? $olz_email : null;
            $email_html = "<div class='info-container'>Du hast eine <b>offizielle</b> OLZ E-Mail-Adresse: <b>{$olz_email}</b></div>";
            if ($user->getOldUsername()) {
                $old_olz_email = "{$user->getOldUsername()}@{$host}";
                $email_html .= "<div class='info-container'>Du hast ausserdem eine <b>alte</b> offizielle OLZ E-Mail-Adresse: <b>{$old_olz_email}</b> <i>(nicht mehr benutzen!)</i></div>";
            }
            $email_html .= "<div class='info-container'>Die E-Mails <b>werden weitergeleitet</b> an: <b>{$user->getEmail()}</b></div>";
            $email_html .= "<h3>Kürzlich weitergeleitete E-Mails</h3>";
            $email_html .= "<table class='forwarded-emails'><tr><th>Datum</th><th>Absender</th><th>Betreff</th></tr>";
            $iso_now = $this->dateUtils()->getIsoNow();
            $minus_one_month = \DateInterval::createFromDateString("-30 days");
            $one_month_ago = (new \DateTime($iso_now))->add($minus_one_month);
            $forwarded_email_repo = $this->entityManager()->getRepository(ForwardedEmail::class);
            $forwarded_emails = $forwarded_email_repo->matching(Criteria::create()
                ->where(Criteria::expr()->andX(
                    Criteria::expr()->eq('recipient_user', $user),
                    Criteria::expr()->gt('forwarded_at', $one_month_ago),
                ))
                ->orderBy(['forwarded_at' => 'DESC'])
                ->setFirstResult(0)
                ->setMaxResults(1000));
            foreach ($forwarded_emails as $forwarded_email) {
                $email_html .= <<<ZZZZZZZZZZ
                    <tr>
                        <td class='nowrap'>{$forwarded_email->getForwardedAt()?->format('d.m.Y H:i')}</td>
                        <td class='nowrap'>{$forwarded_email->getSenderAddress()}</td>
                        <td>{$forwarded_email->getSubject()}</td>
                    </tr>
                    ZZZZZZZZZZ;
            }
            $email_html .= "</table>";
        } else {
            $email = $user->getEmail();
            $email_html = "<div class='info-container'>Du hast <b>keine offizielle</b> OLZ E-Mail-Adresse.</div>";
            $sysadmin_modal = $sysadmin_role ? OlzRoleInfoModal::render(['role' => $sysadmin_role]) : '"Website"';
            $email_html .= "<div class='info-container'>Bei Fragen: kontaktiere das Ressort {$sysadmin_modal}.</div>";
        }
        if ($email) {
            $email_out = $this->htmlUtils()->replaceEmailAdresses($email);
            $out .= "<div class='info-container email'>{$email_out}</div>";
        }
        $out .= $edit_password;

        $out .= <<<ZZZZZZZZZZ
            <h2>Berechtigungen</h2>
            <div class='info-container'>Persönliche Berechtigungen: <b>{$this->prettyPrintPermissionMap($user->getPermissionMap())}</b></div>
            ZZZZZZZZZZ;
        foreach ($user->getRoles() as $role) {
            $role_modal = OlzRoleInfoModal::render(['role' => $role]);
            $out .= "<div class='info-container'>Berechtigungen im Rahmen von {$role_modal}: <b>{$this->prettyPrintPermissionMap($role->getPermissionMap())}</b></div>";
        }

        $out .= <<<ZZZZZZZZZZ
            <h2>E-Mail Weiterleitung</h2>
            {$email_html}
            ZZZZZZZZZZ;

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

    /** @param array<string, bool> $permissions_map */
    protected function prettyPrintPermissionMap(array $permissions_map): string {
        $out = '';
        foreach ($permissions_map as $permission => $is_given) {
            if (!$is_given) {
                continue;
            }
            if ($out !== '') {
                $out .= ', ';
            }
            $out .= $permission;
        }
        return $out !== '' ? $out : '(keine Berechtigungen)';
    }
}
