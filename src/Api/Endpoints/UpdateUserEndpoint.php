<?php

namespace Olz\Api\Endpoints;

use Olz\Api\OlzEndpoint;
use Olz\Entity\User;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\Fields\ValidationError;

class UpdateUserEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'UpdateUserEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'OK_NO_EMAIL_VERIFICATION',
                'DENIED',
                'ERROR',
            ]]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\IntegerField([]),
            'firstName' => new FieldTypes\StringField(['allow_empty' => false]),
            'lastName' => new FieldTypes\StringField(['allow_empty' => false]),
            'username' => new FieldTypes\StringField(['allow_empty' => false]),
            'email' => new FieldTypes\StringField(['allow_empty' => false]),
            'phone' => new FieldTypes\StringField(['allow_null' => true]),
            'gender' => new FieldTypes\EnumField(['allowed_values' => ['M', 'F', 'O'], 'allow_null' => true]),
            'birthdate' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'street' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'postalCode' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'city' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'region' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'countryCode' => new FieldTypes\StringField(['max_length' => 2, 'allow_empty' => true, 'allow_null' => true]),
            'siCardNumber' => new FieldTypes\IntegerField(['min_value' => 100000, 'allow_empty' => true, 'allow_null' => true]),
            'solvNumber' => new FieldTypes\StringField(['allow_empty' => true, 'allow_null' => true]),
            'avatarId' => new FieldTypes\StringField(['allow_null' => true]),
            'recaptchaToken' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    protected function handle($input) {
        $auth_username = $this->session()->get('user');

        $user_repo = $this->entityManager()->getRepository(User::class);
        $user = $user_repo->findOneBy(['id' => $input['id']]);

        if ($user->getUsername() !== $auth_username) {
            return ['status' => 'ERROR'];
        }

        // Username validation
        $new_username = $input['username'];
        $is_username_updated = $new_username !== $user->getUsername();
        if (!$this->authUtils()->isUsernameAllowed($new_username)) {
            throw new ValidationError(['username' => ["Der Benutzername darf nur Buchstaben, Zahlen, und die Zeichen -_. enthalten."]]);
        }
        $same_username_user = $user_repo->findOneBy(['username' => $new_username]);
        if ($is_username_updated && $same_username_user) {
            throw new ValidationError(['username' => ["Es existiert bereits eine Person mit diesem Benutzernamen."]]);
        }

        // Email validation
        $new_email = $input['email'];
        $is_email_updated = $new_email !== $user->getEmail();
        if (preg_match('/@olzimmerberg\.ch$/i', $new_email)) {
            throw new ValidationError(['email' => ["Bitte keine @olzimmerberg.ch E-Mail verwenden."]]);
        }
        $token = $input['recaptchaToken'];
        if ($is_email_updated && !$token) {
            throw new ValidationError(['recaptchaToken' => ["Bei einer E-Mail-Änderung muss ein ReCaptcha Token angegeben werden."]]);
        }
        if ($token && !$this->recaptchaUtils()->validateRecaptchaToken($token)) {
            return ['status' => 'DENIED'];
        }
        $same_email_user = $user_repo->findOneBy(['email' => $new_email]);
        if ($is_email_updated && $same_email_user) {
            throw new ValidationError(['email' => ["Es existiert bereits eine Person mit dieser E-Mail Adresse."]]);
        }

        $new_birthdate = $input['birthdate'] ? new \DateTime($input['birthdate']) : null;

        $now_datetime = new \DateTime($this->dateUtils()->getIsoNow());
        if ($is_username_updated) {
            $user->setOldUsername($user->getUsername());
            $user->setUsername($new_username);
        }
        if ($is_email_updated) {
            $user->setEmailIsVerified(false);
            $user->setEmailVerificationToken(null);
            $user->removePermission('verified_email');
        }
        $user->setFirstName($input['firstName']);
        $user->setLastName($input['lastName']);
        $user->setEmail($new_email);
        $user->setPhone($input['phone']);
        $user->setGender($input['gender']);
        $user->setBirthdate($new_birthdate);
        $user->setStreet($input['street']);
        $user->setPostalCode($input['postalCode']);
        $user->setCity($input['city']);
        $user->setRegion($input['region']);
        $user->setCountryCode($input['countryCode']);
        $user->setSiCardNumber($input['siCardNumber']);
        $user->setSolvNumber($input['solvNumber']);
        $user->setLastModifiedAt($now_datetime);
        $this->entityManager()->flush();

        $user_id = $user->getId();
        $data_path = $this->envUtils()->getDataPath();
        $avatar_id = $input['avatarId'];
        $source_path = "{$data_path}temp/{$avatar_id}";
        $destination_path = "{$data_path}img/users/{$user_id}.jpg";
        if ($avatar_id === '-') {
            $this->unlink($destination_path);
        } elseif ($avatar_id) {
            $this->rename($source_path, $destination_path);
        }

        $this->session()->set('user', $input['username']);

        if ($is_email_updated) {
            $this->emailUtils()->setLogger($this->log());
            try {
                $this->emailUtils()->sendEmailVerificationEmail($user, $token);
            } catch (RecaptchaDeniedException $exc) {
                // @codeCoverageIgnoreStart
                // Reason: Should not be reached.
                throw new \Exception('This should never happen! Token was verified before!');
                // @codeCoverageIgnoreEnd
            } catch (\Throwable $th) {
                return ['status' => 'OK_NO_EMAIL_VERIFICATION'];
            }
            $this->entityManager()->flush();
        }

        return [
            'status' => 'OK',
        ];
    }

    // @codeCoverageIgnoreStart
    // Reason: Mocked in tests.

    protected function unlink($path) {
        unlink($path);
    }

    protected function rename($source_path, $destination_path) {
        rename($source_path, $destination_path);
    }

    // @codeCoverageIgnoreEnd
}
