/** ### This file is auto-generated, modifying is futile! ### */

// eslint-disable-next-line no-shadow
export enum OlzApiEndpoint {
    createRegistration = 'createRegistration',
    createRegistrationForm = 'createRegistrationForm',
    getManagedUsers = 'getManagedUsers',
    getRegistrationForm = 'getRegistrationForm',
    onDaily = 'onDaily',
    onContinuously = 'onContinuously',
    login = 'login',
    logout = 'logout',
    updateUser = 'updateUser',
    updatePassword = 'updatePassword',
    signUpWithPassword = 'signUpWithPassword',
    loginWithStrava = 'loginWithStrava',
    signUpWithStrava = 'signUpWithStrava',
    executeEmailReaction = 'executeEmailReaction',
    linkTelegram = 'linkTelegram',
    onTelegram = 'onTelegram',
    getLogs = 'getLogs',
    updateNotificationSubscriptions = 'updateNotificationSubscriptions',
    updateOlzText = 'updateOlzText',
}

type OlzApiEndpointMapping = {[key in OlzApiEndpoint]: {[fieldId: string]: any}};

export interface OlzApiRequests extends OlzApiEndpointMapping {
    createRegistration: {
        registrationForm: number,
        fieldValues: {[key: string]: any},
    },
    createRegistrationForm: {
        title: string,
        description: string,
        fields: Array<{
            'type': 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'string'|'enum'|'booking',
            'isOptional': boolean,
            'title': string,
            'description': string,
            'options': Array<string>|null,
        }>,
        opensAt: string|null,
        closesAt: string|null,
        ownerUser: number,
        ownerRole: number,
    },
    getManagedUsers: {
    },
    getRegistrationForm: {
        registrationForm: number,
        user: number,
    },
    onDaily: {
        authenticityCode: string,
    },
    onContinuously: {
        authenticityCode: string,
    },
    login: {
        username: string,
        password: string,
    },
    logout: {
    },
    updateUser: {
        id: number,
        firstName: string,
        lastName: string,
        username: string,
        email: string,
        phone: string|null,
        gender: 'M'|'F'|'O'|null,
        birthdate: string|null,
        street: string,
        postalCode: string,
        city: string,
        region: string,
        countryCode: string,
    },
    updatePassword: {
        id: number,
        oldPassword: string,
        newPassword: string,
    },
    signUpWithPassword: {
        firstName: string,
        lastName: string,
        username: string,
        password: string,
        email: string,
        phone: string|null,
        gender: 'M'|'F'|'O'|null,
        birthdate: string|null,
        street: string,
        postalCode: string,
        city: string,
        region: string,
        countryCode: string,
    },
    loginWithStrava: {
        code: string,
    },
    signUpWithStrava: {
        stravaUser: string,
        accessToken: string,
        refreshToken: string,
        expiresAt: string,
        firstName: string,
        lastName: string,
        username: string,
        email: string,
        phone: string|null,
        gender: 'M'|'F'|'O'|null,
        birthdate: string|null,
        street: string,
        postalCode: string,
        city: string,
        region: string,
        countryCode: string,
    },
    executeEmailReaction: {
        token: string,
    },
    linkTelegram: {
    },
    onTelegram: {
        authenticityCode: string,
        telegramEvent: string,
    },
    getLogs: {
        index: number,
    },
    updateNotificationSubscriptions: {
        deliveryType: 'email'|'telegram',
        monthlyPreview: boolean,
        weeklyPreview: boolean,
        deadlineWarning: boolean,
        deadlineWarningDays: '1'|'2'|'3'|'7',
        dailySummary: boolean,
        dailySummaryAktuell: boolean,
        dailySummaryBlog: boolean,
        dailySummaryForum: boolean,
        dailySummaryGalerie: boolean,
        weeklySummary: boolean,
        weeklySummaryAktuell: boolean,
        weeklySummaryBlog: boolean,
        weeklySummaryForum: boolean,
        weeklySummaryGalerie: boolean,
    },
    updateOlzText: {
        id: number,
        text: string,
    },
}

export interface OlzApiResponses extends OlzApiEndpointMapping {
    createRegistration: {
        status: 'OK'|'ERROR',
    },
    createRegistrationForm: {
        status: 'OK'|'ERROR',
    },
    getManagedUsers: {
        status: 'OK'|'ERROR',
        managedUsers: Array<{
            'id': number,
            'firstName': number,
            'lastName': number,
        }>|null,
    },
    getRegistrationForm: {
        status: 'OK'|'ERROR',
        title: string,
        description: string,
        fields: Array<{
            'type': 'email'|'firstName'|'lastName'|'gender'|'street'|'postalCode'|'city'|'region'|'countryCode'|'birthdate'|'phone'|'string'|'enum'|'booking',
            'isOptional': boolean,
            'title': string,
            'description': string,
            'options': Array<string>|null,
        }>,
        opensAt: string|null,
        closesAt: string|null,
        ownerUser: number,
        ownerRole: number,
        prefillValues: {[key: string]: any}|null,
    },
    onDaily: {
    },
    onContinuously: {
    },
    login: {
        status: 'INVALID_CREDENTIALS'|'BLOCKED'|'AUTHENTICATED',
    },
    logout: {
        status: 'NO_SESSION'|'SESSION_CLOSED',
    },
    updateUser: {
        status: 'OK'|'ERROR',
    },
    updatePassword: {
        status: 'OK'|'OTHER_USER'|'INVALID_OLD',
    },
    signUpWithPassword: {
        status: 'OK',
    },
    loginWithStrava: {
        status: 'NOT_REGISTERED'|'INVALID_CODE'|'AUTHENTICATED',
        tokenType: string|null,
        expiresAt: string|null,
        refreshToken: string|null,
        accessToken: string|null,
        userIdentifier: string|null,
        firstName: string|null,
        lastName: string|null,
        gender: 'M'|'F'|'O'|null,
        city: string|null,
        region: string|null,
        country: string|null,
        profilePictureUrl: string|null,
    },
    signUpWithStrava: {
        status: 'OK',
    },
    executeEmailReaction: {
        status: 'INVALID_TOKEN'|'OK',
    },
    linkTelegram: {
        chatLink: string,
    },
    onTelegram: {
    },
    getLogs: {
        content: string|null,
    },
    updateNotificationSubscriptions: {
        status: 'OK'|'ERROR',
    },
    updateOlzText: {
        status: 'OK'|'ERROR',
    },
}

