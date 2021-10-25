/** ### This file is auto-generated, modifying is futile! ### */

export type OlzTransportConnectionSuggestion = {
    'mainConnection': OlzTransportConnection,
    'sideConnections': Array<{
    'connection': OlzTransportConnection,
    'joiningStationId': string,
}>,
    'debug': string,
};
export type OlzTransportConnection = {
    'sections': Array<OlzTransportSection>,
};
export type OlzTransportSection = {
    'departure': OlzTransportHalt,
    'arrival': OlzTransportHalt,
    'passList': Array<OlzTransportHalt>,
};
export type OlzTransportHalt = {
    'stationId': string,
    'stationName': string,
    'time': string,
};

// eslint-disable-next-line no-shadow
export type OlzApiEndpoint =
    'onDaily'|
    'onContinuously'|
    'login'|
    'logout'|
    'updateUser'|
    'updatePassword'|
    'signUpWithPassword'|
    'loginWithStrava'|
    'signUpWithStrava'|
    'executeEmailReaction'|
    'linkTelegram'|
    'onTelegram'|
    'getLogs'|
    'updateNotificationSubscriptions'|
    'updateOlzText'|
    'startUpload'|
    'updateUpload'|
    'finishUpload'|
    'createRegistration'|
    'createRegistrationForm'|
    'getManagedUsers'|
    'getRegistrationForm'|
    'createNews'|
    'searchTransportConnection'|
    'getWebdavAccessToken'|
    'revokeWebdavAccessToken';

type OlzApiEndpointMapping = {[key in OlzApiEndpoint]: any};

export interface OlzApiRequests extends OlzApiEndpointMapping {
    onDaily: {
            'authenticityCode': string,
        },
    onContinuously: {
            'authenticityCode': string,
        },
    login: {
            'usernameOrEmail': string,
            'password': string,
        },
    logout: {
        }|null,
    updateUser: {
            'id': number,
            'firstName': string,
            'lastName': string,
            'username': string,
            'email': string,
            'phone': string|null,
            'gender': 'M'|'F'|'O'|null,
            'birthdate': string|null,
            'street': string,
            'postalCode': string,
            'city': string,
            'region': string,
            'countryCode': string,
        },
    updatePassword: {
            'id': number,
            'oldPassword': string,
            'newPassword': string,
        },
    signUpWithPassword: {
            'firstName': string,
            'lastName': string,
            'username': string,
            'password': string,
            'email': string,
            'phone': string|null,
            'gender': 'M'|'F'|'O'|null,
            'birthdate': string|null,
            'street': string,
            'postalCode': string,
            'city': string,
            'region': string,
            'countryCode': string,
        },
    loginWithStrava: {
            'code': string,
        },
    signUpWithStrava: {
            'stravaUser': string,
            'accessToken': string,
            'refreshToken': string,
            'expiresAt': string,
            'firstName': string,
            'lastName': string,
            'username': string,
            'email': string,
            'phone': string|null,
            'gender': 'M'|'F'|'O'|null,
            'birthdate': string|null,
            'street': string,
            'postalCode': string,
            'city': string,
            'region': string,
            'countryCode': string,
        },
    executeEmailReaction: {
            'token': string,
        },
    linkTelegram: {
        }|null,
    onTelegram: {
            'authenticityCode': string,
            'telegramEvent': string,
        },
    getLogs: {
            'index': number,
        },
    updateNotificationSubscriptions: {
            'deliveryType': 'email'|'telegram',
            'monthlyPreview': boolean,
            'weeklyPreview': boolean,
            'deadlineWarning': boolean,
            'deadlineWarningDays': '1'|'2'|'3'|'7',
            'dailySummary': boolean,
            'dailySummaryAktuell': boolean,
            'dailySummaryBlog': boolean,
            'dailySummaryForum': boolean,
            'dailySummaryGalerie': boolean,
            'weeklySummary': boolean,
            'weeklySummaryAktuell': boolean,
            'weeklySummaryBlog': boolean,
            'weeklySummaryForum': boolean,
            'weeklySummaryGalerie': boolean,
        },
    updateOlzText: {
            'id': number,
            'text': string,
        },
    startUpload: {
            'suffix': string|null,
        },
    updateUpload: {
            'id': string,
            'part': number,
            'content': string,
        },
    finishUpload: {
            'id': string,
            'numberOfParts': number,
        },
    createRegistration: {
            'registrationForm': number,
            'fieldValues': {[key: string]: any},
        },
    createRegistrationForm: {
            'title': string,
            'description': string,
            'fields': Array<{
            'type': 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'string'|'enum'|'booking',
            'isOptional': boolean,
            'title': string,
            'description': string,
            'options': Array<string>|null,
        }>,
            'opensAt': string|null,
            'closesAt': string|null,
            'ownerUser': number,
            'ownerRole': number,
        },
    getManagedUsers: {
        }|null,
    getRegistrationForm: {
            'registrationForm': number,
            'user': number,
        },
    createNews: {
            'ownerUserId': number|null,
            'ownerRoleId': number|null,
            'author': string|null,
            'authorUserId': number|null,
            'authorRoleId': number|null,
            'title': string,
            'teaser': string,
            'content': string,
            'externalUrl': string|null,
            'tags': Array<string>,
            'terminId': number|null,
            'onOff': boolean,
            'imageIds': Array<string>,
            'fileIds': Array<string>,
        },
    searchTransportConnection: {
            'destination': string,
            'arrival': string,
        },
    getWebdavAccessToken: {
        }|null,
    revokeWebdavAccessToken: {
        }|null,
}

export interface OlzApiResponses extends OlzApiEndpointMapping {
    onDaily: {
        }|null,
    onContinuously: {
        }|null,
    login: {
            'status': 'INVALID_CREDENTIALS'|'BLOCKED'|'AUTHENTICATED',
        },
    logout: {
            'status': 'NO_SESSION'|'SESSION_CLOSED',
        },
    updateUser: {
            'status': 'OK'|'ERROR',
        },
    updatePassword: {
            'status': 'OK'|'OTHER_USER'|'INVALID_OLD',
        },
    signUpWithPassword: {
            'status': 'OK',
        },
    loginWithStrava: {
            'status': 'NOT_REGISTERED'|'INVALID_CODE'|'AUTHENTICATED',
            'tokenType': string|null,
            'expiresAt': string|null,
            'refreshToken': string|null,
            'accessToken': string|null,
            'userIdentifier': string|null,
            'firstName': string|null,
            'lastName': string|null,
            'gender': 'M'|'F'|'O'|null,
            'city': string|null,
            'region': string|null,
            'country': string|null,
            'profilePictureUrl': string|null,
        },
    signUpWithStrava: {
            'status': 'OK',
        },
    executeEmailReaction: {
            'status': 'INVALID_TOKEN'|'OK',
        },
    linkTelegram: {
            'botName': string,
            'pin': string,
        },
    onTelegram: {
        }|null,
    getLogs: {
            'content': string|null,
        },
    updateNotificationSubscriptions: {
            'status': 'OK'|'ERROR',
        },
    updateOlzText: {
            'status': 'OK'|'ERROR',
        },
    startUpload: {
            'status': 'OK'|'ERROR',
            'id': string|null,
        },
    updateUpload: {
            'status': 'OK'|'ERROR',
        },
    finishUpload: {
            'status': 'OK'|'ERROR',
        },
    createRegistration: {
            'status': 'OK'|'ERROR',
        },
    createRegistrationForm: {
            'status': 'OK'|'ERROR',
        },
    getManagedUsers: {
            'status': 'OK'|'ERROR',
            'managedUsers': Array<{
            'id': number,
            'firstName': number,
            'lastName': number,
        }>|null,
        },
    getRegistrationForm: {
            'status': 'OK'|'ERROR',
            'title': string,
            'description': string,
            'fields': Array<{
            'type': 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'string'|'enum'|'booking',
            'isOptional': boolean,
            'title': string,
            'description': string,
            'options': Array<string>|null,
        }>,
            'opensAt': string|null,
            'closesAt': string|null,
            'ownerUser': number,
            'ownerRole': number,
            'prefillValues': {[key: string]: any}|null,
        },
    createNews: {
            'status': 'OK'|'ERROR',
            'newsId': number|null,
        },
    searchTransportConnection: {
            'status': 'OK'|'ERROR',
            'suggestions': Array<OlzTransportConnectionSuggestion>|null,
        },
    getWebdavAccessToken: {
            'status': 'OK'|'ERROR',
            'token': string|null,
        },
    revokeWebdavAccessToken: {
            'status': 'OK'|'ERROR',
        },
}

