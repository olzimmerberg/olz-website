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
        $booking = new Booking();
        $this->entityUtils()->createOlzEntity($booking, $input['meta']);
        $this->updateEntityWithData($booking, $input['data']);

        $this->entityManager()->persist($booking);
        $this->entityManager()->flush();

        $internal_booking_id = $booking->getId() ?? 0;
        $external_booking_id = $this->idUtils()->toExternalId($internal_booking_id, 'Booking') ?: '-';

        return [
            'id' => $external_booking_id,
        ];
    }
}
