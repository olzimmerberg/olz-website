import {olzApi} from '../../../Api/client';
import {OlzUserData} from '../../../Api/client/generated_olz_api_types';
import {olzConfirm} from '../../../Components/Common/OlzConfirmationDialog/OlzConfirmationDialog';
import {initOlzEditUserModal} from '../OlzEditUserModal/OlzEditUserModal';

import './OlzUserDetail.scss';

export function editUser(
    userId: number,
): boolean {
    olzApi.call('editUser', {id: userId})
        .then((response) => {
            const options = {
                showPassword: false,
                isPasswordRequired: false,
                isEmailRequired: response.data.parentUserId === null,
            };
            initOlzEditUserModal(options, response.id, response.meta, response.data);
        });
    return false;
}

export function deleteUser(userId: number): boolean {
    olzConfirm('Wirklich löschen?').then(() => {
        olzApi.call('deleteUser', {id: userId}).then(() => {
            window.setTimeout(() => {
                // This could probably be done more smoothly!
                window.location.reload();
            }, 1000);
        });
    });
    return false;
}

export function addChildUser(
    parentUserId: number,
): boolean {
    olzApi.call('getUser', {id: parentUserId})
        .then((response) => {
            const options = {
                showPassword: true,
                isPasswordRequired: false,
                isEmailRequired: false,
            };
            const prefillData: OlzUserData = {
                parentUserId,
                firstName: '',
                lastName: response.data.lastName,
                username: '',
                password: null,
                email: null,
                phone: null,
                gender: null,
                birthdate: null,
                street: null,
                postalCode: null,
                city: null,
                region: null,
                countryCode: null,
                siCardNumber: null,
                solvNumber: null,
                avatarId: null,
            };
            initOlzEditUserModal(options, undefined, undefined, prefillData);
        });
    return false;
}

