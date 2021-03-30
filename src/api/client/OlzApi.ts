/** ### This file is auto-generated, modifying is futile! ### */

// eslint-disable-next-line no-shadow
export enum OlzApiEndpoint {
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

