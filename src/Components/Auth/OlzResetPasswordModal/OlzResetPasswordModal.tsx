import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal, OlzEditModalStatus} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {OlzCaptcha} from '../../../Captcha/Components/OlzCaptcha/OlzCaptcha';
import {codeHref} from '../../../Utils/constants';
import {getApiString, getResolverResult, validateNotEmpty} from '../../../Utils/formUtils';

import './OlzResetPasswordModal.scss';

interface OlzResetPasswordForm {
    usernameOrEmail: string;
}

const resolver: Resolver<OlzResetPasswordForm> = async (values) => {
    const errors: FieldErrors<OlzResetPasswordForm> = {};
    errors.usernameOrEmail = validateNotEmpty(values.usernameOrEmail);
    return getResolverResult(errors, values);
};

function getApiFromForm(formData: OlzResetPasswordForm): OlzApiRequests['resetPassword'] {
    return {
        usernameOrEmail: getApiString(formData.usernameOrEmail) ?? '',
        captchaToken: '',
    };
}

// ---

export const OlzResetPasswordModal = (): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzResetPasswordForm>({
        resolver,
        defaultValues: {
            usernameOrEmail: '',
        },
    });

    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});
    const [captchaToken, setCaptchaToken] = React.useState<string|null>(null);

    const onSubmit: SubmitHandler<OlzResetPasswordForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        const data = getApiFromForm(values);
        if (!captchaToken) {
            setStatus({id: 'SUBMIT_FAILED', message: 'Die Captcha-Aufgabe wurde nicht erledigt.'});
            return;
        }

        const [err, response] = await olzApi.getResult('resetPassword', {...data, captchaToken});
        if (response?.status === 'DENIED') {
            setStatus({id: 'SUBMIT_FAILED', message: 'Die Bot-Prüfung wurde nicht korrekt erledigt.'});
            return;
        } else if (response?.status !== 'OK') {
            setStatus({id: 'SUBMIT_FAILED', message: `Fehler: ${err?.message} (Antwort: ${response?.status}).`});
            return;
        }
        setStatus({id: 'SUBMITTED', message: 'E-Mail versendet. Bitte warten...'});
        window.location.reload();
    };

    const dialogTitle = 'Passwort zurücksetzen';

    return (
        <OlzEditModal
            modalId='reset-password-modal'
            dialogTitle={dialogTitle}
            status={status}
            submitLabel='E-Mail senden'
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className='mb-3 instructions'>
                Wir schicken dir ein E-Mail mit dem Betreff "[OLZ] Passwort zurücksetzen".
                Es enthält ein Passwort und einen Link, mit dem du dieses dann als dein neues Passwort setzen kannst.
            </div>
            <div className='mb-3'>
                <OlzTextField
                    title={<>
                        Benutzername oder E-Mail
                        <a
                            href={`${codeHref}fragen_und_antworten/benutzername_email_herausfinden`}
                            className='help-link'
                        >
                            Vergessen?
                        </a>
                    </>}
                    name='usernameOrEmail'
                    errors={errors}
                    register={register}
                />
            </div>
            <OlzCaptcha onToken={setCaptchaToken}/>
        </OlzEditModal>
    );
};

export function initOlzResetPasswordModal(): boolean {
    return initOlzEditModal('reset-password-modal', () => (
        <OlzResetPasswordModal/>
    ), (modal) => {
        modal.addEventListener('shown.bs.modal', () => {
            document.getElementById('usernameOrEmail-input')?.focus();
        });
    });
}
