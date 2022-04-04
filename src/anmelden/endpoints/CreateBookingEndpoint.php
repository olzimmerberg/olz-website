<?php

use PhpTypeScriptApi\Fields\FieldTypes;

require_once __DIR__.'/../../api/OlzEndpoint.php';

class CreateBookingEndpoint extends OlzEndpoint {
    public static function getIdent() {
        return 'CreateBookingEndpoint';
    }

    public function getResponseField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'status' => new FieldTypes\EnumField(['allowed_values' => [
                'OK',
                'ERROR',
            ]]),
            'bookingId' => new FieldTypes\StringField(['allow_null' => true]),
        ]]);
    }

    public function getRequestField() {
        return new FieldTypes\ObjectField(['field_structure' => [
            'registrationId' => new FieldTypes\StringField(['allow_null' => false]),
            // see README for documentation.
            'values' => new FieldTypes\DictField(['item_field' => new FieldTypes\Field()]),
        ]]);
    }

    protected function handle($input) {
        $current_user = $this->authUtils->getSessionUser();
        $external_registration_id = $input['registrationId'];
        $internal_registration_id = $this->idUtils->toInternalId($external_registration_id, 'Registration');
        $registration_repo = $this->entityManager->getRepository(Registration::class);
        $registration = $registration_repo->findOneBy(['id' => $internal_registration_id]);

        if (!$registration) {
            return ['status' => 'ERROR'];
        }

        $valid_values = [];
        $registration_info_repo = $this->entityManager->getRepository(RegistrationInfo::class);
        foreach ($input['values'] as $ident => $value) {
            $registration_info = $registration_info_repo->findOneBy([
                'registration' => $registration,
                'ident' => $ident,
            ]);
            if (!$registration_info) {
                $this->logger->warning("Creating booking with unknown info '{$ident}' for registration {$internal_registration_id}.");
            }
            // TODO: Validate reservation is not duplicate
            $valid_values[$ident] = $value;
        }
        $values_json = json_encode($valid_values);

        $booking = new Booking();
        $this->entityUtils->createOlzEntity($booking, ['onOff' => 1]);
        $booking->setRegistration($registration);
        $booking->setUser($current_user);
        $booking->setFormData($values_json);

        $this->entityManager->persist($booking);
        $this->entityManager->flush();

        $internal_booking_id = $booking->getId();
        $external_booking_id = $this->idUtils->toExternalId($internal_booking_id, 'Booking');

        return [
            'status' => 'OK',
            'bookingId' => $external_booking_id,
        ];
    }
}
