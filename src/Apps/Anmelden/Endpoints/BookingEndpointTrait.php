<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Entity\Anmelden\Booking;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;
use Olz\Utils\WithUtilsTrait;
use PhpTypeScriptApi\HttpError;

/**
 * @phpstan-type OlzBookingId non-empty-string
 * @phpstan-type OlzBookingData array{
 *   registrationId: non-empty-string,
 *   values: array<string, mixed>,
 * }
 */
trait BookingEndpointTrait {
    use WithUtilsTrait;

    /** @return OlzBookingData */
    public function getEntityData(Booking $entity): array {
        $registration = $entity->getRegistration();
        $registration_id = $registration->getId() ?? 0;
        $external_registration_id = $this->idUtils()->toExternalId($registration_id, 'Registration') ?: '-';

        $values_json = json_decode($entity->getFormData(), true);
        $valid_values = [];
        $registration_info_repo = $this->entityManager()->getRepository(RegistrationInfo::class);
        foreach ($values_json as $ident => $value) {
            $registration_info = $registration_info_repo->findOneBy([
                'registration' => $registration,
                'ident' => $ident,
            ]);
            if (!$registration_info) {
                $this->log()->warning("Creating booking with unknown info '{$ident}' for registration {$registration->getId()}.");
            }
            // TODO: Validate reservation is not duplicate
            $valid_values[$ident] = $value;
        }
        return [
            'registrationId' => $external_registration_id,
            'values' => $valid_values,
        ];
    }

    /** @param OlzBookingData $input_data */
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
        $values_json = json_encode($valid_values) ?: '{}';

        $entity->setRegistration($registration);
        $entity->setUser($current_user);
        $entity->setFormData($values_json);
    }
}
