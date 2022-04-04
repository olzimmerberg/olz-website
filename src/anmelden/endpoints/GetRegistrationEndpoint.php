<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';
require_once __DIR__.'/../AnmeldenConstants.php';

class GetRegistrationEndpoint extends OlzEndpoint {
    public function runtimeSetup() {
        parent::runtimeSetup();
        global $entityManager;
        require_once __DIR__.'/../../config/doctrine_db.php';
        require_once __DIR__.'/../../model/index.php';
        $this->setEntityManager($entityManager);
    }

    public function setEntityManager($new_entity_manager) {
        $this->entityManager = $new_entity_manager;
    }

    public static function getIdent() {
        return 'GetRegistrationEndpoint';
    }

    public function getResponseField() {
        $registration_data_field = new FieldTypes\ObjectField(['field_structure' => [
            'title' => new FieldTypes\StringField(['allow_empty' => false]),
            'description' => new FieldTypes\StringField(['allow_empty' => true]),
            // see README for documentation.
            'infos' => new FieldTypes\ArrayField([
                'item_field' => AnmeldenConstants::getRegistrationInfoField(),
            ]),
            'opensAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'closesAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            'ownerUserId' => new FieldTypes\IntegerField(['allow_null' => false]),
            'ownerRoleId' => new FieldTypes\IntegerField(['allow_null' => true]),
            'onOff' => new FieldTypes\BooleanField(['default_value' => true]),
            'prefillValues' => new FieldTypes\DictField([
                'item_field' => new FieldTypes\Field(),
                'allow_null' => true,
            ]),
        ]]);
        return new FieldTypes\ObjectField(['field_structure' => [
            'id' => new FieldTypes\StringField(['allow_null' => false]),
            'data' => $registration_data_field,
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'registrationId' => new FieldTypes\StringField([]),
            'userId' => new FieldTypes\IntegerField(['min_value' => 1]), // Can be a managed user
        ]]);
    }

    protected function handle($input) {
        $external_id = $input['registrationId'];
        $internal_id = $this->idUtils->toInternalId($external_id, 'Registration');
        $registration_repo = $this->entityManager->getRepository(Registration::class);
        $registration = $registration_repo->findOneBy(['id' => $internal_id]);

        $infos = [];
        $registration_info_repo = $this->entityManager->getRepository(RegistrationInfo::class);
        $registration_infos = $registration_info_repo->findBy(
            ['registration' => $registration, 'onOff' => true],
            ['indexWithinRegistration' => 'ASC'],
        );
        foreach ($registration_infos as $registration_info) {
            $options = json_decode($registration_info->getOptions() ?? 'null', true);
            $infos[] = [
                'type' => $registration_info->getType(),
                'isOptional' => $registration_info->getIsOptional(),
                'title' => $registration_info->getTitle(),
                'description' => $registration_info->getDescription(),
                'options' => $options,
            ];
        }

        $owner_user = $registration->getOwnerUser();
        $owner_role = $registration->getOwnerRole();

        return [
            'id' => $external_id,
            'data' => [
                'ownerUserId' => $owner_user ? $owner_user->getId() : null,
                'ownerRoleId' => $owner_role ? $owner_role->getId() : null,
                'onOff' => $registration->getOnOff(),
                'title' => $registration->getTitle(),
                'description' => $registration->getDescription(),
                'infos' => $infos,
                'opensAt' => $registration->getOpensAt(),
                'closesAt' => $registration->getClosesAt(),
                'prefillValues' => [],
            ],
        ];
    }
}
