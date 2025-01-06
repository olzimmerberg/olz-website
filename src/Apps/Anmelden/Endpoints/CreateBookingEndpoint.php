<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzCreateEntityTypedEndpoint;
use Olz\Entity\Anmelden\Booking;
use PhpTypeScriptApi\PhpStan\PhpStanUtils;

/**
 * @phpstan-import-type OlzBookingId from BookingEndpointTrait
 * @phpstan-import-type OlzBookingData from BookingEndpointTrait
 *
 * @extends OlzCreateEntityTypedEndpoint<OlzBookingId, OlzBookingData>
 */
class CreateBookingEndpoint extends OlzCreateEntityTypedEndpoint {
    use BookingEndpointTrait;

    public function configure(): void {
        parent::configure();
        PhpStanUtils::registerTypeImport(BookingEndpointTrait::class);
    }

    protected function handle(mixed $input): mixed {
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
