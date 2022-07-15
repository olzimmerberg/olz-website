/** ### This file is auto-generated, modifying is futile! ### */

export type OlzMetaData = {
    'ownerUserId': number|null,
    'ownerRoleId': number|null,
    'onOff': boolean,
};

export type OlzNewsData = {
    'author': string|null,
    'authorUserId': number|null,
    'authorRoleId': number|null,
    'title': string,
    'teaser': string,
    'content': string,
    'externalUrl': string|null,
    'tags': Array<string>,
    'terminId': number|null,
    'imageIds': Array<string>,
    'fileIds': Array<string>,
};

export type OlzMetaDataOrNull = {
    'ownerUserId': number|null,
    'ownerRoleId': number|null,
    'onOff': boolean,
}|null;

export type OlzNewsDataOrNull = {
    'author': string|null,
    'authorUserId': number|null,
    'authorRoleId': number|null,
    'title': string,
    'teaser': string,
    'content': string,
    'externalUrl': string|null,
    'tags': Array<string>,
    'terminId': number|null,
    'imageIds': Array<string>,
    'fileIds': Array<string>,
}|null;

export type OlzBookingData = {
    'registrationId': string,
    'values': {[key: string]: any},
};

export type OlzRegistrationData = {
    'title': string,
    'description': string,
    'infos': Array<OlzRegistrationInfo>,
    'opensAt': string|null,
    'closesAt': string|null,
};

export type OlzRegistrationInfo = {
    'type': 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'string'|'enum'|'reservation',
    'isOptional': boolean,
    'title': string,
    'description': string,
    'options': Array<{
    'text': string,
}>|null,
};

export type OlzTransportSuggestion = {
    'mainConnection': OlzTransportConnection,
    'sideConnections': Array<{
    'connection': OlzTransportConnection,
    'joiningStationId': string,
}>,
    'originInfo': Array<OlzOriginInfo>,
    'debug': string,
};

export type OlzTransportConnection = {
    'sections': Array<OlzTransportSection>,
};

export type OlzTransportSection = {
    'departure': OlzTransportHalt,
    'arrival': OlzTransportHalt,
    'passList': Array<OlzTransportHalt>,
    'isWalk': boolean,
};

export type OlzTransportHalt = {
    'stationId': string,
    'stationName': string,
    'time': string,
};

export type OlzOriginInfo = {
    'halt': OlzTransportHalt,
    'isSkipped': boolean,
    'rating': number,
};

// eslint-disable-next-line no-shadow
export type OlzApiEndpoint =
    'onDaily'|
    'onContinuously'|
    'login'|
    'resetPassword'|
    'logout'|
    'updateUser'|
    'updatePassword'|
    'signUpWithPassword'|
    'loginWithStrava'|
    'signUpWithStrava'|
    'deleteUser'|
    'executeEmailReaction'|
    'linkTelegram'|
    'onTelegram'|
    'updateNotificationSubscriptions'|
    'updateOlzText'|
    'startUpload'|
    'updateUpload'|
    'finishUpload'|
    'createNews'|
    'getNews'|
    'editNews'|
    'updateNews'|
    'deleteNews'|
    'createBooking'|
    'createRegistration'|
    'getManagedUsers'|
    'getRegistration'|
    'getWebdavAccessToken'|
    'revokeWebdavAccessToken'|
    'getLogs'|
    'getAppMonitoringCredentials'|
    'searchTransportConnection'|
    'getMySkillLevels'|
    'updateMySkillLevels'|
    'registerSkillCategories'|
    'registerSkills'|
    'updateResults'|
    'getAppStatisticsCredentials';

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
    resetPassword: {
            'usernameOrEmail': string,
            'recaptchaToken': string,
        },
    logout: Record<string, never>|null,
    updateUser: {
            'id': number,
            'firstName': string,
            'lastName': string,
            'username': string,
            'email': string,
            'phone': string|null,
            'gender': 'M'|'F'|'O'|null,
            'birthdate': string|null,
            'street': string|null,
            'postalCode': string|null,
            'city': string|null,
            'region': string|null,
            'countryCode': string|null,
            'siCardNumber': number|null,
            'solvNumber': string|null,
            'avatarId': string|null,
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
            'street': string|null,
            'postalCode': string|null,
            'city': string|null,
            'region': string|null,
            'countryCode': string|null,
            'siCardNumber': number|null,
            'solvNumber': string|null,
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
            'siCardNumber': number|null,
            'solvNumber': string|null,
        },
    deleteUser: {
            'id': number,
        },
    executeEmailReaction: {
            'token': string,
        },
    linkTelegram: Record<string, never>|null,
    onTelegram: {
            'authenticityCode': string,
            'telegramEvent': string,
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
            'dailySummaryTermine': boolean,
            'weeklySummary': boolean,
            'weeklySummaryAktuell': boolean,
            'weeklySummaryBlog': boolean,
            'weeklySummaryForum': boolean,
            'weeklySummaryGalerie': boolean,
            'weeklySummaryTermine': boolean,
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
    createNews: {
            'meta': OlzMetaData,
            'data': OlzNewsData,
        },
    getNews: {
            'id': number,
        },
    editNews: {
            'id': number,
        },
    updateNews: {
            'id': number,
            'meta': OlzMetaDataOrNull,
            'data': OlzNewsDataOrNull,
        },
    deleteNews: {
            'id': number,
        },
    createBooking: {
            'meta': OlzMetaData,
            'data': OlzBookingData,
        },
    createRegistration: {
            'meta': OlzMetaData,
            'data': OlzRegistrationData,
        },
    getManagedUsers: Record<string, never>|null,
    getRegistration: {
            'id': string,
        },
    getWebdavAccessToken: Record<string, never>|null,
    revokeWebdavAccessToken: Record<string, never>|null,
    getLogs: {
            'index': number,
        },
    getAppMonitoringCredentials: Record<string, never>,
    searchTransportConnection: {
            'destination': string,
            'arrival': string,
        },
    getMySkillLevels: {
            'skillFilter': {
            'categoryIdIn': Array<string>,
        }|null,
        },
    updateMySkillLevels: {
            'updates': {[key: string]: {
            'change': number,
        }},
        },
    registerSkillCategories: {
            'skillCategories': Array<{
            'name': string,
            'parentCategoryName': string|null,
        }>,
        },
    registerSkills: {
            'skills': Array<{
            'name': string,
            'categoryIds': Array<string>,
        }>,
        },
    updateResults: {
            'file': string,
            'content': string,
        },
    getAppStatisticsCredentials: Record<string, never>,
}

export interface OlzApiResponses extends OlzApiEndpointMapping {
    onDaily: Record<string, never>|null,
    onContinuously: Record<string, never>|null,
    login: {
            'status': 'INVALID_CREDENTIALS'|'BLOCKED'|'AUTHENTICATED',
        },
    resetPassword: {
            'status': 'DENIED'|'ERROR'|'OK',
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
    deleteUser: {
            'status': 'OK'|'ERROR',
        },
    executeEmailReaction: {
            'status': 'INVALID_TOKEN'|'OK',
        },
    linkTelegram: {
            'botName': string,
            'pin': string,
        },
    onTelegram: Record<string, never>|null,
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
    createNews: {
            'status': 'OK'|'ERROR',
            'id': number,
        },
    getNews: {
            'id': number,
            'meta': OlzMetaData,
            'data': OlzNewsData,
        },
    editNews: {
            'id': number,
            'meta': OlzMetaData,
            'data': OlzNewsData,
        },
    updateNews: {
            'status': 'OK'|'ERROR',
            'id': number,
        },
    deleteNews: {
            'status': 'OK'|'ERROR',
        },
    createBooking: {
            'status': 'OK'|'ERROR',
            'id': string,
        },
    createRegistration: {
            'status': 'OK'|'ERROR',
            'id': string,
        },
    getManagedUsers: {
            'status': 'OK'|'ERROR',
            'managedUsers': Array<{
            'id': number,
            'firstName': string,
            'lastName': string,
        }>|null,
        },
    getRegistration: {
            'id': string,
            'meta': OlzMetaData,
            'data': OlzRegistrationData,
        },
    getWebdavAccessToken: {
            'status': 'OK'|'ERROR',
            'token': string|null,
        },
    revokeWebdavAccessToken: {
            'status': 'OK'|'ERROR',
        },
    getLogs: {
            'content': string|null,
        },
    getAppMonitoringCredentials: {
            'username': string,
            'password': string,
        },
    searchTransportConnection: {
            'status': 'OK'|'ERROR',
            'suggestions': Array<OlzTransportSuggestion>|null,
        },
    getMySkillLevels: {[key: string]: {
            'value': number,
        }},
    updateMySkillLevels: {
            'status': 'OK'|'ERROR',
        },
    registerSkillCategories: {
            'idByName': {[key: string]: string},
        },
    registerSkills: {
            'idByName': {[key: string]: string},
        },
    updateResults: {
            'status': 'OK'|'INVALID_FILENAME'|'INVALID_BASE64_DATA'|'ERROR',
        },
    getAppStatisticsCredentials: {
            'username': string,
            'password': string,
        },
}

