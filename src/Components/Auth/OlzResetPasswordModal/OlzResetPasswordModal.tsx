import React from 'react';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {olzApi} from '../../../Api/client';
import {OlzApiRequests} from '../../../Api/client/generated_olz_api_types';
import {initOlzEditModal, OlzEditModal} from '../../../Components/Common/OlzEditModal/OlzEditModal';
import {OlzTextField} from '../../../Components/Common/OlzTextField/OlzTextField';
import {codeHref} from '../../../Utils/constants';
import {getApiString, getResolverResult, validateNotEmpty} from '../../../Utils/formUtils';
import {loadRecaptchaToken, loadRecaptcha} from '../../../Utils/recaptchaUtils';

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
        recaptchaToken: '',
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

    const [isSubmitting, setIsSubmitting] = React.useState<boolean>(false);
    const [recaptchaConsentGiven, setRecaptchaConsentGiven] = React.useState<boolean>(false);
    const [isWaitingForCaptcha, setIsWaitingForCaptcha] = React.useState<boolean>(false);
    const [successMessage, setSuccessMessage] = React.useState<string>('');
    const [errorMessage, setErrorMessage] = React.useState<string>('');

    React.useEffect(() => {
        if (!recaptchaConsentGiven) {
            return;
        }
        setIsWaitingForCaptcha(true);
        loadRecaptcha().then(() => {
            window.setTimeout(() => {
                setIsWaitingForCaptcha(false);
            }, 1100);
        });
    }, [recaptchaConsentGiven]);

    const onSubmit: SubmitHandler<OlzResetPasswordForm> = async (values) => {
        setIsSubmitting(true);
        const data = getApiFromForm(values);
        const recaptchaToken = await loadRecaptchaToken();

        const [err, response] = await olzApi.getResult('resetPassword', {...data, recaptchaToken});
        if (response?.status === 'DENIED') {
            setSuccessMessage('');
            setErrorMessage('Der reCaptcha-Token wurde abgelehnt.');
            setIsSubmitting(false);
            return;
        } else if (response?.status !== 'OK') {
            setSuccessMessage('');
            setErrorMessage(`Fehler: ${err?.message} (Antwort: ${response?.status}).`);
            setIsSubmitting(false);
            return;
        }
        setSuccessMessage('E-Mail versendet. Bitte warten...');
        setErrorMessage('');
        // This removes Google's injected reCaptcha script again
        window.location.reload();
    };

    const dialogTitle = 'Passwort zurücksetzen';

    return (
        <OlzEditModal
            modalId='reset-password-modal'
            dialogTitle={dialogTitle}
            successMessage={successMessage}
            errorMessage={errorMessage}
            isWaitingForCaptcha={isWaitingForCaptcha}
            isSubmitting={isSubmitting}
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
                            href={`${codeHref}fragen_und_antworten#benutzername-email-herausfinden`}
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
            <div className='mb-3'>
                <input
                    type='checkbox'
                    name='recaptcha-consent-given'
                    value='yes'
                    checked={recaptchaConsentGiven}
                    onChange={(e) => setRecaptchaConsentGiven(e.target.checked)}
                    id='recaptcha-consent-given-input'
                />
                Ich akzeptiere, dass beim Zurücksetzen des Passworts einmalig Google reCaptcha verwendet wird, um Bot-Spam zu verhinden.
                &nbsp;
                <a
                    href={`${codeHref}datenschutz`}
                    target='_blank'
                >
                    Weitere Informationen zum Datenschutz
                </a>
            </div>
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
