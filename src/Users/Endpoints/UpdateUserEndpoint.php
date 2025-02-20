<?php

namespace Olz\Users\Endpoints;

use Olz\Api\OlzUpdateEntityTypedEndpoint;
use Olz\Entity\Roles\Role;
use Olz\Entity\Users\User;
use PhpTypeScriptApi\Fields\ValidationError;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-import-type OlzUserId from UserEndpointTrait
 * @phpstan-import-type OlzUserData from UserEndpointTrait
 *
 * @extends OlzUpdateEntityTypedEndpoint<OlzUserId, OlzUserData, never, array{
 *   status: 'OK'|'OK_NO_EMAIL_VERIFICATION'|'DENIED'|'ERROR',
 * }>
 */
class UpdateUserEndpoint extends OlzUpdateEntityTypedEndpoint {
    use UserEndpointTrait;

    public function configure(): void {
        parent::configure();
        $this->configureUserEndpointTrait();
        $this->phpStanUtils->registerTypeImport(UserEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
        $user_repo = $this->entityManager()->getRepository(User::class);
        $role_repo = $this->entityManager()->getRepository(Role::class);
        $entity = $this->getEntityById($input['id']);

        $current_user = $this->authUtils()->getCurrentUser();
        $is_me = (
            $current_user
            && $entity->getUsername() === $current_user->getUsername()
            && $entity->getId() === $current_user->getId()
        );
        $can_update = $this->entityUtils()->canUpdateOlzEntity($entity, null, 'users');
        if (!$is_me && !$can_update) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        // Username validation
        $old_username = $entity->getUsername();
        $new_username = $input['data']['username'];
        $is_username_updated = $new_username !== $old_username;
        if (!$this->authUtils()->isUsernameAllowed($new_username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        if ($is_username_updated) {
            $same_username_user = $user_repo->findOneBy(['username' => $new_username]);
            $same_old_username_user = $user_repo->findOneBy(['old_username' => $new_username]);
            $same_username_role = $role_repo->findOneBy(['username' => $new_username]);
            $same_old_username_role = $role_repo->findOneBy(['old_username' => $new_username]);
            $is_existing_username = (bool) (
                $same_username_user || $same_old_username_user
                || $same_username_role || $same_old_username_role
            );
            if ($is_existing_username) {
                throw new ValidationError(['username' => ["Dieser Benutzername ist bereits vergeben."]]);
            }
        }

        // Email validation
        $new_email = $input['data']['email'] ?? null;
        $is_email_updated = $new_email !== $entity->getEmail();
        if (preg_match('/@olzimmerberg\.ch$/i', $new_email ?? '')) {
            throw new ValidationError(['email' => ["Bitte keine @olzimmerberg.ch E-Mail verwenden."]]);
        }
        if ($is_email_updated) {
            $same_email_user = $user_repo->findOneBy(['email' => $new_email]);
            if ($same_email_user) {
                throw new ValidationError(['email' => ["Es existiert bereits eine Person mit dieser E-Mail Adresse."]]);
            }
        }

        // TODO Do this more elegantly?
        $old_data = $this->getEntityData($entity);
        $this->log()->notice('OLD:', [$old_data]);

        $this->entityUtils()->updateOlzEntity($entity, $input['meta']);
        if ($is_username_updated) {
            $entity->setOldUsername($entity->getUsername());
        }
        $this->updateEntityWithData($entity, $input['data']);
        if ($is_email_updated) {
            $entity->setEmailIsVerified(false);
            $entity->setEmailVerificationToken(null);
            $entity->removePermission('verified_email');
        }

        // TODO Do this more elegantly?
        $new_data = $this->getEntityData($entity);
        $this->log()->notice('NEW:', [$new_data]);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();
        $this->persistUploads($entity, $input['data']);

        if ($is_username_updated && $this->session()->get('user') === $old_username) {
            $this->session()->set('user', $new_username);
        }

        if ($is_email_updated) {
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
