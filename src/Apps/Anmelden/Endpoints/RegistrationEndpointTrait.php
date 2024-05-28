<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Apps\Anmelden\AnmeldenConstants;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;

trait RegistrationEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return true;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return self::getRegistrationDataField($allow_null);
    }

    public static function getRegistrationDataField(bool $allow_null = false) {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzRegistrationDataOrNull' : 'OlzRegistrationData',
            'field_structure' => [
                'title' => new FieldTypes\StringField(['allow_empty' => false]),
                'description' => new FieldTypes\StringField(['allow_empty' => true]),
                // see README for documentation.
                'infos' => new FieldTypes\ArrayField([
                    'item_field' => AnmeldenConstants::getRegistrationInfoField(),
                ]),
                'opensAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
                'closesAt' => new FieldTypes\DateTimeField(['allow_null' => true]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    /** @return array<string, mixed> */
    public function getEntityData(Registration $entity): array {
        $infos = [];
        $registration_info_repo = $this->entityManager()->getRepository(RegistrationInfo::class);
        $registration_infos = $registration_info_repo->findBy(
            ['registration' => $entity, 'onOff' => true],
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

        return [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'infos' => $infos,
            'opensAt' => $entity->getOpensAt(),
            'closesAt' => $entity->getClosesAt(),
        ];
    }

    public function updateEntityWithData(Registration $entity, array $input_data): void {
        $entity->setTitle($input_data['title']);
        $entity->setDescription($input_data['description']);
        $entity->setOpensAt($input_data['opensAt'] ? new \DateTime($input_data['opensAt']) : null);
        $entity->setClosesAt($input_data['closesAt'] ? new \DateTime($input_data['closesAt']) : null);
    }
}
