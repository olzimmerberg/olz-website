<?php

use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../AnmeldenConstants.php';

class CreateRegistrationEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'CreateRegistrationEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'registrationId' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'title' => new FieldTypes\StringField(['allow_empty' => false]),
            'description' => new FieldTypes\StringField(['allow_empty' => true]),
            // see README for documentation.
            'infos' => new FieldTypes\ArrayField([
                'item_field' => AnmeldenConstants::getRegistrationInfoField(),
            ]),
            'opensAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'closesAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'ownerUserId' => new FieldTypes\IntegerField(['allow_empty' => false]),
            'ownerRoleId' => new FieldTypes\IntegerField(['allow_empty' => true]),
            'onOff' => new FieldTypes\BooleanField(['default_value' => true]),
        ]]);
    }

    protected function handle($input) {
        $has_access = $this->authUtils->hasPermission('any');
        if (!$has_access) {
            throw new HttpError(403, "Kein Zugriff!");
        }

        $registration = new Registration();
        $this->entityUtils->createOlzEntity($registration, $input);
        $registration->setTitle($input['title']);
        $registration->setDescription($input['description']);
        $registration->setOpensAt($input['opensAt'] ? new DateTime($input['opensAt']) : null);
        $registration->setClosesAt($input['closesAt'] ? new DateTime($input['closesAt']) : null);

        $this->entityManager->persist($registration);

        foreach ($input['infos'] as $index => $info_spec) {
            $title_ident = preg_replace('/[^a-zA-Z0-9]+/', '_', $info_spec['title']);
            $ident = "{$index}-{$title_ident}";

            $options_json = json_encode($info_spec['options']);

            $registration_info = new RegistrationInfo();
            $this->entityUtils->createOlzEntity($registration_info, $input);
            $registration_info->setRegistration($registration);
            $registration_info->setIndexWithinRegistration($index);
            $registration_info->setIdent($ident);
            $registration_info->setTitle($info_spec['title']);
            $registration_info->setDescription($info_spec['description']);
            $registration_info->setType($info_spec['type']);
            $registration_info->setIsOptional($info_spec['isOptional'] ? true : false);
            $registration_info->setOptions($options_json);

            $this->entityManager->persist($registration_info);
        }
        $this->entityManager->flush();

        $internal_id = $registration->getId();
        $external_id = $this->idUtils->toExternalId($internal_id, 'Registration');

        return [
            'status' => 'OK',
            'registrationId' => $external_id,
        ];
    }
}
