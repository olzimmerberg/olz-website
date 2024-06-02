<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Entity\Anmelden\Booking;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\Fields\FieldTypes;
use PhpTypeScriptApi\HttpError;

trait BookingEndpointTrait {
    use WithUtilsTrait;

    public function usesExternalId(): bool {
        return true;
    }

    public function getEntityDataField(bool $allow_null): FieldTypes\Field {
        return self::getBookingDataField($allow_null);
    }

    public static function getBookingDataField(bool $allow_null = false): FieldTypes\Field {
        return new FieldTypes\ObjectField([
            'export_as' => $allow_null ? 'OlzBookingDataOrNull' : 'OlzBookingData',
            'field_structure' => [
                'registrationId' => new FieldTypes\StringField(['allow_null' => false]),
                // see README for documentation.
                'values' => new FieldTypes\DictField(['item_field' => new FieldTypes\Field()]),
            ],
            'allow_null' => $allow_null,
        ]);
    }

    // TODO: Implement once needed
    /** @return array<string, mixed> */
    public function getEntityData(Booking $entity): array {
        return [];
    }

    /** @param array<string, mixed> $input_data */
    public function updateEntityWithData(Booking $entity, array $input_data): void {
        $current_user = $this->authUtils()->getCurrentUser();

        $external_registration_id = $input_data['registrationId'];
        $internal_registration_id = $this->idUtils()->toInternalId($external_registration_id, 'Registration');
        $registration_repo = $this->entityManager()->getRepository(Registration::class);
        $registration = $registration_repo->findOneBy(['id' => $internal_registration_id]);

        if (!$registration) {
            throw new HttpError(400, "Invalid registration: {$external_registration_id}");
        }

        $valid_values = [];
        $registration_info_repo = $this->entityManager()->getRepository(RegistrationInfo::class);
        foreach ($input_data['values'] as $ident => $value) {
            $registration_info = $registration_info_repo->findOneBy([
                'registration' => $registration,
                'ident' => $ident,
            ]);
            if (!$registration_info) {
                $this->log()->warning("Creating booking with unknown info '{$ident}' for registration {$internal_registration_id}.");
            }
            // TODO: Validate reservation is not duplicate
            $valid_values[$ident] = $value;
        }
        $values_json = json_encode($valid_values);

        $entity->setRegistration($registration);
        $entity->setUser($current_user);
        $entity->setFormData($values_json);
    }
}
