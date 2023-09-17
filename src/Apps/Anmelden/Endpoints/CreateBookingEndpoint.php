<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Anmelden\Booking;

class CreateBookingEndpoint extends OlzCreateEntityEndpoint {
    use BookingEndpointTrait;

    public static function getIdent() {
        return 'CreateBookingEndpoint';
    }

    protected function handle($input) {
        $booking = new Booking();
        $this->entityUtils()->createOlzEntity($booking, $input['meta']);
        $this->updateEntityWithData($booking, $input['data']);

        $this->entityManager()->persist($booking);
        $this->entityManager()->flush();

        $internal_booking_id = $booking->getId();
        $external_booking_id = $this->idUtils()->toExternalId($internal_booking_id, 'Booking');

        return [
            'status' => 'OK',
            'id' => $external_booking_id,
        ];
    }
}
