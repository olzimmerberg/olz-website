<?php

namespace Olz\Apps\Anmelden\Endpoints;

use Olz\Api\OlzCreateEntityEndpoint;
use Olz\Entity\Anmelden\Booking;
use Olz\Entity\Anmelden\Registration;
use Olz\Entity\Anmelden\RegistrationInfo;

class CreateBookingEndpoint extends OlzCreateEntityEndpoint {
    use BookingEndpointTrait;

    public static function getIdent() {
        return 'CreateBookingEndpoint';
    }

    protected function handle($input) {
        $current_user = $this->authUtils()->getCurrentUser();
        $input_data = $input['data'];

        $external_registration_id = $input_data['registrationId'];
        $internal_registration_id = $this->idUtils()->toInternalId($external_registration_id, 'Registration');
        $registration_repo = $this->entityManager()->getRepository(Registration::class);
        $registration = $registration_repo->findOneBy(['id' => $internal_registration_id]);

        if (!$registration) {
            return ['status' => 'ERROR'];
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

        $booking = new Booking();
        $this->entityUtils()->createOlzEntity($booking, $input['meta']);
        $booking->setRegistration($registration);
        $booking->setUser($current_user);
        $booking->setFormData($values_json);

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
