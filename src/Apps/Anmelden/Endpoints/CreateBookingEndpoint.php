<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Anmelden\Booking;

/**
 * @phpstan-import-type OlzBookingId from BookingEndpointTrait
 * @phpstan-import-type OlzBookingData from BookingEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzBookingId, OlzBookingData>
 */
class CreateBookingEndpoint extends OlzCreateEntityTypedEndpoint {
    use BookingEndpointTrait;

    protected function handle(mixed $input): mixed {
        $entity = new Booking();
        $this->entityUtils()->createOlzEntity($entity, $input['meta'] ?? null);
        $this->updateEntityWithData($entity, $input['data']);

        $this->entityManager()->persist($entity);
        $this->entityManager()->flush();

        $internal_booking_id = $entity->getId() ?? 0;
        $external_booking_id = $this->idUtils()->toExternalId($internal_booking_id, 'Booking') ?: '-';

        return [
            'id' => $external_booking_id,
        ];
    }
}
