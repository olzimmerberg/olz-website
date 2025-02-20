<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\AuthRequest;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Fields\ValidationError;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzUserId from UserEndpointTrait
 * @phpstan-import-type OlzUserData from UserEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzUserId, OlzUserData, array{
 *   recaptchaToken?: ?non-empty-string,
 * }, array{
 *   status: 'OK'|'OK_NO_EMAIL_VERIFICATION'|'DENIED'|'ERROR',
 * }>
 */
class CreateUserEndpoint extends OlzCreateEntityTypedEndpoint {
    use UserEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureUserEndpointTrait();
        $this->phpStanUtils->registerTypeImport(UserEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $current_user = $this->authUtils()->getCurrentUser();
        $token = $input['custom']['recaptchaToken'] ?? null;
        if (!$current_user && !$this->recaptchaUtils()->validateRecaptchaToken($token)) {
            return ['custom' => ['status' => 'DENIED'], 'id' => null];
        }

        $parent_user_id = $input['data']['parentUserId'] ?? null;
        if ($parent_user_id !== null) {
            if (!$current_user) {
                throw new HttpError(403, "Kein Zugriff!");
            }
            if ($parent_user_id !== $current_user->getId()) {
                // Create child of someone else
                if (!$this->authUtils()->hasPermission('users')) {
                    throw new HttpError(403, "Kein Zugriff!");
                }
            }
        }

        $first_name = $input['data']['firstName'];
        $last_name = $input['data']['lastName'];
        $username = $input['data']['username'];
        $email = $input['data']['email'] ?? null;
        $password = $input['data']['password'] ?? null;
        $this->log()->info("New sign-up (using password): {$first_name} {$last_name} ({$username}@) <{$email}> (Parent: {$parent_user_id})");
        if (!$parent_user_id && !$email) {
            throw new ValidationError(['email' => ["Feld darf nicht leer sein."]]);
        }
        if (!$parent_user_id && !$password) {
            throw new ValidationError(['password' => ["Feld darf nicht leer sein."]]);
        }
        if (!$this->authUtils()->isUsernameAllowed($username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        if (!$parent_user_id && !$this->authUtils()->isPasswordAllowed($password)) {
            throw new ValidationError(['password' => ["Das Passwort muss mindestens 8 Zeichen lang sein."]]);
        }
        if ($email && preg_match('/@olzimmerberg\.ch$/i', $email)) {
            throw new ValidationError(['email' => ["Bitte keine @olzimmerberg.ch E-Mail verwenden."]]);
        }
        $ip_address = $this->server()['REMOTE_ADDR'];
        $auth_request_repo = $this->entityManager()->getRepository(AuthRequest::class);
        $user_repo = $this->entityManager()->getRepository(User::class);

        $same_username_user = $user_repo->findOneBy(['username' => $username]);
        $same_email_user = $user_repo->findOneBy(['email' => $email]);
        // TODO: Test users with old username, roles
        if ($username && $same_username_user) {
            if ($same_username_user->getPasswordHash()) {
                throw new ValidationError(['username' => ["Es existiert bereits eine Person mit diesem Benutzernamen. Wolltest du gar kein Konto erstellen, sondern dich nur einloggen?"]]);
            }
            // If it's an existing user WITHOUT password, we just update that existing user!
            $entity = $same_username_user;
            $this->entityUtils()->updateOlzEntity($entity, ['onOff' => true]);
        } elseif ($email && $same_email_user) {
            if ($same_email_user->getPasswordHash()) {
                throw new ValidationError(['email' => ["Es existiert bereits eine Person mit dieser E-Mail Adresse. Wolltest du gar kein Konto erstellen, sondern dich nur einloggen?"]]);
            }
            // If it's an existing user WITHOUT password, we just update that existing user!
            $entity = $same_email_user;
            $this->entityUtils()->updateOlzEntity($entity, ['onOff' => true]);
        } else {
            $entity = new User();
            $this->entityUtils()->createOlzEntity($entity, ['onOff' => true]);
        }

        $entity->setOldUsername(null);
        $this->updateEntityWithData($entity, $input['data']);
        $entity->setEmailIsVerified(false);
        $entity->setEmailVerificationToken(null);

        $password_hash = $password ? $this->authUtils()->hashPassword($password) : null;

        $entity->setPasswordHash($password_hash);
        $entity->setParentUserId($parent_user_id);
        $entity->setPermissions('');
        $entity->setRoot(null);
        $entity->setMemberType(null);
        $entity->setMemberLastPaid(null);
        $entity->setWantsPostalMail(false);
        $entity->setPostalTitle(null);
        $entity->setPostalName(null);
        $entity->setJoinedOn(null);
        $entity->setJoinedReason(null);
        $entity->setLeftOn(null);
        $entity->setLeftReason(null);
        $entity->setNotes('');
        $entity->setLastLoginAt(null);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        if (!$parent_user_id) {
            $this->session()->resetConfigure(['timeout' => 3600]);

            $root = $entity->getRoot() !== '' ? $entity->getRoot() : './';
            $this->session()->set('auth', $entity->getPermissions());
            $this->session()->set('root', $root);
            $this->session()->set('user', $entity->getUsername());
            $this->session()->set('user_id', "{$entity->getId()}");
            $this->session()->set('auth_user', $entity->getUsername());
            $this->session()->set('auth_user_id', "{$entity->getId()}");
            $auth_request_repo->addAuthRequest($ip_address, 'AUTHENTICATED_PASSWORD', $entity->getUsername());

            $this->emailUtils()->setLogger($this->log());
            try {
                $this->emailUtils()->sendEmailVerificationEmail($entity);
            } catch (\Throwable $th) {
                return [
                    'custom' => ['status' => 'OK_NO_EMAIL_VERIFICATION'],
                    'id' => $entity->getId(),
                ];
            }
            $this->entityManager()->flush();
        }

        return [
            'custom' => ['status' => 'OK'],
            'id' => $entity->getId(),
        ];
    }
}
