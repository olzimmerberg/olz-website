import * as bootstrap from 'bootstrap';
import React from 'react';
import emojiRegex from 'emoji-regex';
import {useForm, SubmitHandler, Resolver, FieldErrors} from 'react-hook-form';
import {initReact} from '../../../Utils/reactUtils';
import {getResolverResult, validateStringRegex} from '../../../Utils/formUtils';
import {OlzTextField} from '../OlzTextField/OlzTextField';
import {OlzEditModal, OlzEditModalStatus} from '../OlzEditModal/OlzEditModal';

interface OlzCustomReactionForm {
    emoji: string;
}

const resolver: Resolver<OlzCustomReactionForm> = async (values) => {
    const errors: FieldErrors<OlzCustomReactionForm> = {};
    errors.emoji = validateStringRegex(
        values.emoji,
        `^${emojiRegex().source}$`,
        'Muss ein Emoji sein',
    );
    return getResolverResult(errors, values);
};

// ---

interface OlzCustomReactionDialogProps {
    onConfirm: (emoji: string) => unknown;
}

export const OlzCustomReactionDialog = (props: OlzCustomReactionDialogProps): React.ReactElement => {
    const {register, handleSubmit, formState: {errors}} = useForm<OlzCustomReactionForm>({
        resolver,
    });
    const [status, setStatus] = React.useState<OlzEditModalStatus>({id: 'IDLE'});

    const onSubmit: SubmitHandler<OlzCustomReactionForm> = async (values) => {
        setStatus({id: 'SUBMITTING'});
        await props.onConfirm(values.emoji);
        setStatus({id: 'SUBMITTED'});
        // This could probably be done more smoothly!
        window.location.reload();
    };

    return (
        <OlzEditModal
            modalId='emoji-modal'
            dialogTitle='Mit eigenem Emoji reagieren'
            status={status}
            onSubmit={handleSubmit(onSubmit)}
        >
            <div className={'mb-3'}>
                <OlzTextField
                    title='Emoji'
                    name='emoji'
                    errors={errors}
                    register={register}
                />
            </div>
        </OlzEditModal>
    );
};

export function olzCustomReaction(
    onConfirm: (emoji: string) => Promise<unknown>,
): void {
    initReact(
        'dialog-react-root',
        <OlzCustomReactionDialog
            onConfirm={onConfirm}
        />,
    );
    setTimeout(() => {
        const modal = document.getElementById('emoji-modal');
        if (modal) {
            new bootstrap.Modal(modal, {backdrop: 'static'}).show();
        }
    }, 1);
}
