/** ### This file is auto-generated, modifying is futile! ### */

export type OlzAuthenticatedUser = {
    'id': number,
    'firstName': string,
    'lastName': string,
    'username': string,
}|null;

export type OlzAuthenticatedRole = {
    'id': number,
    'name': string,
    'username': string,
};

export type OlzMetaData = {
    'ownerUserId': number|null,
    'ownerRoleId': number|null,
    'onOff': boolean,
};

export type OlzNewsData = {
    'format': OlzNewsFormat,
    'author': string|null,
    'authorUserId': number|null,
    'authorRoleId': number|null,
    'title': string,
    'teaser': string,
    'content': string,
    'externalUrl': string|null,
    'tags': Array<string>,
    'terminId': number|null,
    'imageIds': Array<string>|null,
    'fileIds': Array<string>,
};

export type OlzNewsFormat = 'aktuell'|'galerie'|'video';

export type OlzMetaDataOrNull = {
    'ownerUserId': number|null,
    'ownerRoleId': number|null,
    'onOff': boolean,
}|null;

export type OlzNewsDataOrNull = {
    'format': OlzNewsFormat,
    'author': string|null,
    'authorUserId': number|null,
    'authorRoleId': number|null,
    'title': string,
    'teaser': string,
    'content': string,
    'externalUrl': string|null,
    'tags': Array<string>,
    'terminId': number|null,
    'imageIds': Array<string>|null,
    'fileIds': Array<string>,
}|null;

export type OlzWeeklyPictureData = {
    'text': string,
    'imageId': string,
    'alternativeImageId': string|null,
};

export type OlzTerminData = {
    'startDate': string,
    'startTime': string|null,
    'endDate': string|null,
    'endTime': string|null,
    'title': string,
    'text': string,
    'link': string,
    'deadline': string|null,
    'newsletter': boolean,
    'solvId': string|null,
    'go2olId': string|null,
    'types': Array<string>,
    'onOff': boolean,
    'coordinateX': number|null,
    'coordinateY': number|null,
    'fileIds': Array<string>,
};

export type OlzTerminDataOrNull = {
    'startDate': string,
    'startTime': string|null,
    'endDate': string|null,
    'endTime': string|null,
    'title': string,
    'text': string,
    'link': string,
    'deadline': string|null,
    'newsletter': boolean,
    'solvId': string|null,
    'go2olId': string|null,
    'types': Array<string>,
    'onOff': boolean,
    'coordinateX': number|null,
    'coordinateY': number|null,
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
    'type': 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'siCardNumber'|'solvNumber'|'string'|'enum'|'reservation',
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
    'getAuthenticatedUser'|
    'getAuthenticatedRoles'|
    'updateUser'|
    'verifyUserEmail'|
    'updatePassword'|
    'signUpWithPassword'|
    'loginWithStrava'|
    'signUpWithStrava'|
    'deleteUser'|
    'executeEmailReaction'|
    'linkTelegram'|
    'onTelegram'|
    'updateOlzText'|
    'startUpload'|
    'updateUpload'|
    'finishUpload'|
    'createNews'|
    'getNews'|
    'editNews'|
    'updateNews'|
    'deleteNews'|
    'createWeeklyPicture'|
    'createTermin'|
    'getTermin'|
    'editTermin'|
    'updateTermin'|
    'deleteTermin'|
    'createBooking'|
    'createRegistration'|
    'getManagedUsers'|
    'getPrefillValues'|
    'getRegistration'|
    'getWebdavAccessToken'|
    'revokeWebdavAccessToken'|
    'getAppGoogleSearchCredentials'|
    'importTermine'|
    'getLogs'|
    'getAppMonitoringCredentials'|
    'updateNotificationSubscriptions'|
    'searchTransportConnection'|
    'getMySkillLevels'|
    'updateMySkillLevels'|
    'registerSkillCategories'|
    'registerSkills'|
    'updateResults'|
    'getAppStatisticsCredentials'|
    'getAppYoutubeCredentials';

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
    getAuthenticatedUser: Record<string, never>|null,
    getAuthenticatedRoles: Record<string, never>|null,
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
            'recaptchaToken': string|null,
        },
    verifyUserEmail: {
            'recaptchaToken': string,
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
            'recaptchaToken': string,
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
    createWeeklyPicture: {
            'meta': OlzMetaData,
            'data': OlzWeeklyPictureData,
        },
    createTermin: {
            'meta': OlzMetaData,
            'data': OlzTerminData,
        },
    getTermin: {
            'id': number,
        },
    editTermin: {
            'id': number,
        },
    updateTermin: {
            'id': number,
            'meta': OlzMetaDataOrNull,
            'data': OlzTerminDataOrNull,
        },
    deleteTermin: {
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
    getPrefillValues: {
            'userId': number|null,
        },
    getRegistration: {
            'id': string,
        },
    getWebdavAccessToken: Record<string, never>|null,
    revokeWebdavAccessToken: Record<string, never>|null,
    getAppGoogleSearchCredentials: Record<string, never>,
    importTermine: Record<string, never>,
    getLogs: {
            'index': number,
        },
    getAppMonitoringCredentials: Record<string, never>,
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
            'dailySummaryTermine': boolean,
            'weeklySummary': boolean,
            'weeklySummaryAktuell': boolean,
            'weeklySummaryBlog': boolean,
            'weeklySummaryForum': boolean,
            'weeklySummaryTermine': boolean,
        },
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
    getAppYoutubeCredentials: Record<string, never>,
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
    getAuthenticatedUser: {
            'user': OlzAuthenticatedUser,
        },
    getAuthenticatedRoles: {
            'roles': Array<OlzAuthenticatedRole>|null,
        },
    updateUser: {
            'status': 'OK'|'OK_NO_EMAIL_VERIFICATION'|'DENIED'|'ERROR',
        },
    verifyUserEmail: {
            'status': 'OK'|'DENIED'|'ERROR',
        },
    updatePassword: {
            'status': 'OK'|'OTHER_USER'|'INVALID_OLD',
        },
    signUpWithPassword: {
            'status': 'OK'|'OK_NO_EMAIL_VERIFICATION'|'DENIED',
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
    createWeeklyPicture: {
            'status': 'OK'|'ERROR',
            'id': number,
        },
    createTermin: {
            'status': 'OK'|'ERROR',
            'id': number,
        },
    getTermin: {
            'id': number,
            'meta': OlzMetaData,
            'data': OlzTerminData,
        },
    editTermin: {
            'id': number,
            'meta': OlzMetaData,
            'data': OlzTerminData,
        },
    updateTermin: {
            'status': 'OK'|'ERROR',
            'id': number,
        },
    deleteTermin: {
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
    getPrefillValues: {
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
    getAppGoogleSearchCredentials: {
            'username': string,
            'password': string,
        },
    importTermine: Record<string, never>,
    getLogs: {
            'content': string|null,
        },
    getAppMonitoringCredentials: {
            'username': string,
            'password': string,
        },
    updateNotificationSubscriptions: {
            'status': 'OK'|'ERROR',
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
    getAppYoutubeCredentials: {
            'username': string,
            'password': string,
        },
}

