import React from 'react';
import {olzApi} from '../../../../Api/client';

export const OlzRegistrationView = () => {
    const createRegistration = () => {
        olzApi.call('createRegistration', {
            meta: {
                ownerUserId: null, // Current user is owner
                ownerRoleId: null, // Just the user, no role owns it
                onOff: true, // Activate
            },
            data: {
                title: 'Meine Anmeldung',
                description: 'Eine Test-Anmeldung',
                infos: [
                    {
                        type: 'firstName',
                        isOptional: false,
                        title: 'Vorname',
                        description: null,
                        options: null,
                    },
                    {
                        type: 'lastName',
                        isOptional: false,
                        title: 'Nachname',
                        description: null,
                        options: null,
                    },
                    {
                        type: 'siCardNumber',
                        isOptional: true,
                        title: 'SI-Card Nummer',
                        description: '= Badge-Nummer. Leer lassen falls nicht vorhanden.',
                        options: null,
                    },
                    {
                        type: 'enum',
                        isOptional: false,
                        title: 'Menü',
                        description: 'Allergien bitte unter "Bemerkungen".',
                        options: [
                            {text: 'Fleisch'},
                            {text: 'Vegetarisch'},
                            {text: 'Fisch'},
                        ],
                    },
                    {
                        type: 'string',
                        isOptional: true,
                        title: 'Bemerkungen',
                        description: null,
                        options: null,
                    },
                ],
                opensAt: '2022-09-10 18:00:00',
                closesAt: '2022-12-24 18:00:00',
            },
        });
    };

    return (
        <div>
            RegistrationView
            <button
                onClick={createRegistration}
            >
                Anmeldungsformular erstellen
            </button>
        </div>
    );
};
