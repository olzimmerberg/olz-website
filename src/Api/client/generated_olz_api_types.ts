/** ### This file is auto-generated, modifying is futile! ### */

export type OlzAuthenticatedUser = {'id': number, 'firstName': string, 'lastName': string, 'username': string};

export type OlzAuthenticatedRole = {'id': number, 'name': string, 'username': string};

export type OlzSearchableEntityType = ('Download' | 'Link' | 'Question' | 'QuestionCategory' | 'SolvEvent' | 'TerminLabel' | 'TerminLocation' | 'TerminTemplate' | 'Role' | 'User');

export type OlzEntityPositionResult = {'id': number, 'position': (number | null), 'title': string};

export type OlzEntityResult = {'id': number, 'title': string};

export type OlzMetaData = {'ownerUserId': (number | null), 'ownerRoleId': (number | null), 'onOff': boolean};

export type OlzDownloadData = {'name': string, 'position'?: (number | null), 'fileId'?: (string | null)};

export type OlzDownloadId = number;

export type OlzKarteData = {'kartennr'?: (number | null), 'name': string, 'latitude'?: (number | null), 'longitude'?: (number | null), 'year'?: (number | null), 'scale'?: (string | null), 'place'?: (string | null), 'zoom'?: (number | null), 'kind'?: (OlzKarteKind | null), 'previewImageId'?: (string | null)};

export type OlzKarteId = number;

export type OlzKarteKind = ('ol' | 'stadt' | 'scool');

export type OlzLinkData = {'position'?: (number | null), 'name': string, 'url': string};

export type OlzLinkId = number;

export type OlzNewsData = {'format': OlzNewsFormat, 'authorUserId'?: (number | null), 'authorRoleId'?: (number | null), 'authorName'?: (string | null), 'authorEmail'?: (string | null), 'publishAt'?: (IsoDateTime | null), 'title': string, 'teaser': string, 'content': string, 'externalUrl'?: (string | null), 'tags': Array<string>, 'terminId'?: (number | null), 'imageIds'?: (Array<string> | null), 'fileIds': Array<string>};

export type OlzNewsId = number;

export type OlzNewsFormat = ('aktuell' | 'kaderblog' | 'forum' | 'galerie' | 'video' | 'anonymous');

export type IsoDateTime = string;

export type OlzAuthorInfoData = {'roleName'?: (string | null), 'roleUsername'?: (string | null), 'firstName': string, 'lastName': string, 'email'?: (Array<string> | null), 'avatarImageId'?: ({[key: string]: string} | null)};

export type OlzRoleData = {'username': string, 'name': string, 'description': string, 'guide': string, 'imageIds': Array<string>, 'fileIds': Array<string>, 'parentRole'?: (number | null), 'positionWithinParent'?: (number | null), 'featuredPosition'?: (number | null), 'canHaveChildRoles': boolean};

export type OlzRoleId = number;

export type OlzRoleMembershipIds = {'roleId': number, 'userId': number};

export type OlzRoleInfoData = {'name'?: (string | null), 'username'?: (string | null), 'assignees': Array<{'firstName': string, 'lastName': string, 'email'?: (Array<string> | null), 'avatarImageId'?: {[key: string]: string}}>};

export type OlzSnippetId = number;

export type OlzSnippetData = {'text': string, 'imageIds': Array<string>, 'fileIds': Array<string>};

export type OlzQuestionData = {'ident': string, 'question': string, 'categoryId'?: (number | null), 'positionWithinCategory'?: (number | null), 'answer': string, 'imageIds': Array<string>, 'fileIds': Array<string>};

export type OlzQuestionId = number;

export type OlzQuestionCategoryData = {'position': number, 'name': string};

export type OlzQuestionCategoryId = number;

export type OlzWeeklyPictureData = {'text': string, 'imageId': string, 'publishedDate'?: (IsoDate | null)};

export type OlzWeeklyPictureId = number;

export type IsoDate = string;

export type OlzTerminData = {'fromTemplateId'?: (number | null), 'startDate'?: (IsoDate | null), 'startTime'?: (IsoTime | null), 'endDate'?: (IsoDate | null), 'endTime'?: (IsoTime | null), 'title'?: (string | null), 'text': string, 'deadline'?: (IsoDateTime | null), 'shouldPromote': boolean, 'newsletter': boolean, 'solvId'?: (number | null), 'go2olId'?: (string | null), 'types': Array<string>, 'locationId'?: (number | null), 'coordinateX'?: (number | null), 'coordinateY'?: (number | null), 'imageIds': Array<string>, 'fileIds': Array<string>};

export type OlzTerminId = number;

export type IsoTime = string;

export type OlzTerminLabelData = {'ident': string, 'name': string, 'details': string, 'icon'?: (string | null), 'position'?: (number | null), 'imageIds': Array<string>, 'fileIds': Array<string>};

export type OlzTerminLabelId = number;

export type OlzTerminLocationData = {'name': string, 'details': string, 'latitude': number, 'longitude': number, 'imageIds': Array<string>};

export type OlzTerminLocationId = number;

export type OlzTerminTemplateData = {'startTime'?: (IsoTime | null), 'durationSeconds'?: (number | null), 'title': string, 'text': string, 'deadlineEarlierSeconds'?: (number | null), 'deadlineTime'?: (IsoTime | null), 'shouldPromote': boolean, 'newsletter': boolean, 'types': Array<string>, 'locationId'?: (number | null), 'imageIds': Array<string>, 'fileIds': Array<string>};

export type OlzTerminTemplateId = number;

export type OlzUserData = {'parentUserId'?: (number | null), 'firstName': string, 'lastName': string, 'username': string, 'password'?: (string | null), 'email'?: (string | null), 'phone'?: (string | null), 'gender'?: (('M' | 'F' | 'O') | null), 'birthdate'?: (IsoDate | null), 'street'?: (string | null), 'postalCode'?: (string | null), 'city'?: (string | null), 'region'?: (string | null), 'countryCode'?: (IsoCountry | null), 'siCardNumber'?: (number | null), 'solvNumber'?: (string | null), 'avatarImageId'?: (string | null)};

export type OlzUserId = number;

export type IsoCountry = string;

export type OlzUserInfoData = {'firstName': string, 'lastName': string, 'email'?: (Array<string> | null), 'avatarImageId'?: {[key: string]: string}};

export type OlzCaptchaConfig = {'rand': string, 'date': string, 'mac': string};

export type OlzBookingData = {'registrationId': string, 'values': {[key: string]: unknown}};

export type OlzBookingId = string;

export type OlzRegistrationData = {'title': string, 'description': string, 'infos': Array<OlzRegistrationInfo>, 'opensAt'?: (IsoDateTime | null), 'closesAt'?: (IsoDateTime | null)};

export type OlzRegistrationId = string;

export type OlzRegistrationInfo = {'type': ValidRegistrationInfoType, 'isOptional': boolean, 'title': string, 'description': string, 'options'?: (({'text': Array<string>} | {'svg': Array<string>}) | null)};

export type ValidRegistrationInfoType = ('email' | 'firstName' | 'lastName' | 'gender' | 'street' | 'postalCode' | 'city' | 'region' | 'countryCode' | 'birthdate' | 'phone' | 'siCardNumber' | 'solvNumber' | 'string' | 'enum' | 'reservation');

export type ManagedUser = {'id': number, 'firstName': string, 'lastName': string};

export type UserPrefillData = {'firstName': string, 'lastName': string, 'username': string, 'email': string, 'phone'?: (string | null), 'gender'?: (('M' | 'F' | 'O') | null), 'birthdate'?: (IsoDate | null), 'street'?: (string | null), 'postalCode'?: (string | null), 'city'?: (string | null), 'region'?: (string | null), 'countryCode'?: (IsoCountry | null), 'siCardNumber'?: (number | null), 'solvNumber'?: (string | null)};

export type OlzLogsQuery = {'channel': string, 'targetDate'?: (IsoDateTime | null), 'firstDate'?: (IsoDateTime | null), 'lastDate'?: (IsoDateTime | null), 'minLogLevel'?: (OlzLogLevel | null), 'textSearch'?: (string | null), 'pageToken'?: (string | null)};

export type OlzLogLevel = ('debug' | 'info' | 'notice' | 'warning' | 'error' | 'critical' | 'alert' | 'emergency');

export type OlzMemberInfo = {'ident': string, 'action': ('CREATE' | 'UPDATE' | 'DELETE' | 'KEEP'), 'username'?: (string | null), 'matchingUsername'?: (string | null), 'user'?: ({'id': number, 'firstName': string, 'lastName': string} | null), 'updates': {[key: string]: {'old': string, 'new': string}}};

export type OlzTransportSuggestion = {'mainConnection': OlzTransportConnection, 'sideConnections': Array<{'connection': OlzTransportConnection, 'joiningStationId': string}>, 'originInfo': Array<OlzOriginInfo>, 'debug': string};

export type OlzTransportConnection = {'sections': Array<OlzTransportSection>};

export type OlzOriginInfo = {'halt': OlzTransportHalt, 'isSkipped': boolean, 'rating': number};

export type OlzTransportSection = {'departure': OlzTransportHalt, 'arrival': OlzTransportHalt, 'passList': Array<OlzTransportHalt>, 'isWalk': boolean};

export type OlzTransportHalt = {'stationId': string, 'stationName': string, 'time': IsoDateTime};

export type OlzPanini2024PictureData = {'id'?: (number | null), 'line1': string, 'line2': string, 'residence': string, 'uploadId': string, 'onOff': boolean, 'info1': string, 'info2': string, 'info3': string, 'info4': string, 'info5': string};

export type OlzSkillCategoryData = {'name': string, 'parentCategoryName'?: (string | null)};

export type OlzSkillData = {'name': string, 'categoryIds': Array<string>};

// eslint-disable-next-line no-shadow
export type OlzApiEndpoint =
    'onContinuously'|
    'login'|
    'resetPassword'|
    'switchUser'|
    'logout'|
    'getAuthenticatedUser'|
    'getAuthenticatedRoles'|
    'getEntitiesAroundPosition'|
    'verifyUserEmail'|
    'updatePassword'|
    'executeEmailReaction'|
    'linkTelegram'|
    'onTelegram'|
    'startUpload'|
    'updateUpload'|
    'finishUpload'|
    'searchEntities'|
    'createDownload'|
    'getDownload'|
    'editDownload'|
    'updateDownload'|
    'deleteDownload'|
    'createKarte'|
    'getKarte'|
    'editKarte'|
    'updateKarte'|
    'deleteKarte'|
    'createLink'|
    'getLink'|
    'editLink'|
    'updateLink'|
    'deleteLink'|
    'createNews'|
    'getNews'|
    'editNews'|
    'updateNews'|
    'deleteNews'|
    'getAuthorInfo'|
    'createRole'|
    'getRole'|
    'editRole'|
    'updateRole'|
    'deleteRole'|
    'addUserRoleMembership'|
    'removeUserRoleMembership'|
    'getRoleInfo'|
    'getSnippet'|
    'editSnippet'|
    'updateSnippet'|
    'createQuestion'|
    'getQuestion'|
    'editQuestion'|
    'updateQuestion'|
    'deleteQuestion'|
    'createQuestionCategory'|
    'getQuestionCategory'|
    'editQuestionCategory'|
    'updateQuestionCategory'|
    'deleteQuestionCategory'|
    'createWeeklyPicture'|
    'getWeeklyPicture'|
    'editWeeklyPicture'|
    'updateWeeklyPicture'|
    'deleteWeeklyPicture'|
    'createTermin'|
    'getTermin'|
    'editTermin'|
    'updateTermin'|
    'deleteTermin'|
    'createTerminLabel'|
    'listTerminLabels'|
    'getTerminLabel'|
    'editTerminLabel'|
    'updateTerminLabel'|
    'deleteTerminLabel'|
    'createTerminLocation'|
    'getTerminLocation'|
    'editTerminLocation'|
    'updateTerminLocation'|
    'deleteTerminLocation'|
    'createTerminTemplate'|
    'getTerminTemplate'|
    'editTerminTemplate'|
    'updateTerminTemplate'|
    'deleteTerminTemplate'|
    'createUser'|
    'getUser'|
    'editUser'|
    'updateUser'|
    'deleteUser'|
    'getUserInfo'|
    'startCaptcha'|
    'createBooking'|
    'createRegistration'|
    'getManagedUsers'|
    'getPrefillValues'|
    'getRegistration'|
    'executeCommand'|
    'getWebdavAccessToken'|
    'revokeWebdavAccessToken'|
    'getLogs'|
    'importMembers'|
    'exportMembers'|
    'getAppMonitoringCredentials'|
    'updateNotificationSubscriptions'|
    'searchTransportConnection'|
    'listPanini2024Pictures'|
    'updateMyPanini2024'|
    'getMySkillLevels'|
    'updateMySkillLevels'|
    'registerSkillCategories'|
    'registerSkills'|
    'updateResults'|
    'getAppSearchEnginesCredentials'|
    'getAppStatisticsCredentials'|
    'getAppYoutubeCredentials';

type OlzApiEndpointMapping = {[key in OlzApiEndpoint]: unknown};

export interface OlzApiRequests extends OlzApiEndpointMapping {
    onContinuously: {'authenticityCode': string},
    login: {'usernameOrEmail': string, 'password': string, 'rememberMe': boolean},
    resetPassword: {'usernameOrEmail': string, 'captchaToken': string},
    switchUser: {'userId': number},
    logout: (Record<string, never> | null),
    getAuthenticatedUser: (Record<string, never> | null),
    getAuthenticatedRoles: (Record<string, never> | null),
    getEntitiesAroundPosition: {'entityType': OlzSearchableEntityType, 'entityField': string, 'id'?: (number | null), 'position'?: (number | null), 'filter'?: ({[key: string]: string} | null)},
    verifyUserEmail: (Record<string, never> | null),
    updatePassword: {'id': number, 'oldPassword': string, 'newPassword': string},
    executeEmailReaction: {'token': string},
    linkTelegram: (Record<string, never> | null),
    onTelegram: {'authenticityCode': string, 'telegramEvent': string},
    startUpload: {'suffix'?: (string | null)},
    updateUpload: {'id': string, 'part': number, 'content': string},
    finishUpload: {'id': string, 'numberOfParts': number},
    searchEntities: {'entityType': OlzSearchableEntityType, 'query'?: (string | null), 'id'?: (number | null), 'filter'?: ({[key: string]: string} | null)},
    createDownload: {'meta': OlzMetaData, 'data': OlzDownloadData, 'custom'?: never},
    getDownload: {'id': OlzDownloadId, 'custom'?: never},
    editDownload: {'id': OlzDownloadId, 'custom'?: never},
    updateDownload: {'id': OlzDownloadId, 'meta': OlzMetaData, 'data': OlzDownloadData, 'custom'?: never},
    deleteDownload: {'id': OlzDownloadId, 'custom'?: never},
    createKarte: {'meta': OlzMetaData, 'data': OlzKarteData, 'custom'?: never},
    getKarte: {'id': OlzKarteId, 'custom'?: never},
    editKarte: {'id': OlzKarteId, 'custom'?: never},
    updateKarte: {'id': OlzKarteId, 'meta': OlzMetaData, 'data': OlzKarteData, 'custom'?: never},
    deleteKarte: {'id': OlzKarteId, 'custom'?: never},
    createLink: {'meta': OlzMetaData, 'data': OlzLinkData, 'custom'?: never},
    getLink: {'id': OlzLinkId, 'custom'?: never},
    editLink: {'id': OlzLinkId, 'custom'?: never},
    updateLink: {'id': OlzLinkId, 'meta': OlzMetaData, 'data': OlzLinkData, 'custom'?: never},
    deleteLink: {'id': OlzLinkId, 'custom'?: never},
    createNews: {'meta': OlzMetaData, 'data': OlzNewsData, 'custom'?: {'captchaToken'?: (string | null)}},
    getNews: {'id': OlzNewsId, 'custom'?: never},
    editNews: {'id': OlzNewsId, 'custom'?: never},
    updateNews: {'id': OlzNewsId, 'meta': OlzMetaData, 'data': OlzNewsData, 'custom'?: never},
    deleteNews: {'id': OlzNewsId, 'custom'?: never},
    getAuthorInfo: {'id': OlzNewsId, 'captchaToken'?: (string | null)},
    createRole: {'meta': OlzMetaData, 'data': OlzRoleData, 'custom'?: never},
    getRole: {'id': OlzRoleId, 'custom'?: never},
    editRole: {'id': OlzRoleId, 'custom'?: never},
    updateRole: {'id': OlzRoleId, 'meta': OlzMetaData, 'data': OlzRoleData, 'custom'?: never},
    deleteRole: {'id': OlzRoleId, 'custom'?: never},
    addUserRoleMembership: {'ids': OlzRoleMembershipIds, 'custom'?: never},
    removeUserRoleMembership: {'ids': OlzRoleMembershipIds, 'custom'?: never},
    getRoleInfo: {'id': OlzRoleId, 'captchaToken'?: (string | null)},
    getSnippet: {'id': OlzSnippetId, 'custom'?: never},
    editSnippet: {'id': OlzSnippetId, 'custom'?: never},
    updateSnippet: {'id': OlzSnippetId, 'meta': OlzMetaData, 'data': OlzSnippetData, 'custom'?: never},
    createQuestion: {'meta': OlzMetaData, 'data': OlzQuestionData, 'custom'?: never},
    getQuestion: {'id': OlzQuestionId, 'custom'?: never},
    editQuestion: {'id': OlzQuestionId, 'custom'?: never},
    updateQuestion: {'id': OlzQuestionId, 'meta': OlzMetaData, 'data': OlzQuestionData, 'custom'?: never},
    deleteQuestion: {'id': OlzQuestionId, 'custom'?: never},
    createQuestionCategory: {'meta': OlzMetaData, 'data': OlzQuestionCategoryData, 'custom'?: never},
    getQuestionCategory: {'id': OlzQuestionCategoryId, 'custom'?: never},
    editQuestionCategory: {'id': OlzQuestionCategoryId, 'custom'?: never},
    updateQuestionCategory: {'id': OlzQuestionCategoryId, 'meta': OlzMetaData, 'data': OlzQuestionCategoryData, 'custom'?: never},
    deleteQuestionCategory: {'id': OlzQuestionCategoryId, 'custom'?: never},
    createWeeklyPicture: {'meta': OlzMetaData, 'data': OlzWeeklyPictureData, 'custom'?: never},
    getWeeklyPicture: {'id': OlzWeeklyPictureId, 'custom'?: never},
    editWeeklyPicture: {'id': OlzWeeklyPictureId, 'custom'?: never},
    updateWeeklyPicture: {'id': OlzWeeklyPictureId, 'meta': OlzMetaData, 'data': OlzWeeklyPictureData, 'custom'?: never},
    deleteWeeklyPicture: {'id': OlzWeeklyPictureId, 'custom'?: never},
    createTermin: {'meta': OlzMetaData, 'data': OlzTerminData, 'custom'?: never},
    getTermin: {'id': OlzTerminId, 'custom'?: never},
    editTermin: {'id': OlzTerminId, 'custom'?: never},
    updateTermin: {'id': OlzTerminId, 'meta': OlzMetaData, 'data': OlzTerminData, 'custom'?: never},
    deleteTermin: {'id': OlzTerminId, 'custom'?: never},
    createTerminLabel: {'meta': OlzMetaData, 'data': OlzTerminLabelData, 'custom'?: never},
    listTerminLabels: {'custom'?: never},
    getTerminLabel: {'id': OlzTerminLabelId, 'custom'?: never},
    editTerminLabel: {'id': OlzTerminLabelId, 'custom'?: never},
    updateTerminLabel: {'id': OlzTerminLabelId, 'meta': OlzMetaData, 'data': OlzTerminLabelData, 'custom'?: never},
    deleteTerminLabel: {'id': OlzTerminLabelId, 'custom'?: never},
    createTerminLocation: {'meta': OlzMetaData, 'data': OlzTerminLocationData, 'custom'?: never},
    getTerminLocation: {'id': OlzTerminLocationId, 'custom'?: never},
    editTerminLocation: {'id': OlzTerminLocationId, 'custom'?: never},
    updateTerminLocation: {'id': OlzTerminLocationId, 'meta': OlzMetaData, 'data': OlzTerminLocationData, 'custom'?: never},
    deleteTerminLocation: {'id': OlzTerminLocationId, 'custom'?: never},
    createTerminTemplate: {'meta': OlzMetaData, 'data': OlzTerminTemplateData, 'custom'?: never},
    getTerminTemplate: {'id': OlzTerminTemplateId, 'custom'?: never},
    editTerminTemplate: {'id': OlzTerminTemplateId, 'custom'?: never},
    updateTerminTemplate: {'id': OlzTerminTemplateId, 'meta': OlzMetaData, 'data': OlzTerminTemplateData, 'custom'?: never},
    deleteTerminTemplate: {'id': OlzTerminTemplateId, 'custom'?: never},
    createUser: {'meta': OlzMetaData, 'data': OlzUserData, 'custom'?: {'captchaToken'?: (string | null)}},
    getUser: {'id': OlzUserId, 'custom'?: never},
    editUser: {'id': OlzUserId, 'custom'?: never},
    updateUser: {'id': OlzUserId, 'meta': OlzMetaData, 'data': OlzUserData, 'custom'?: never},
    deleteUser: {'id': OlzUserId, 'custom'?: never},
    getUserInfo: {'id': OlzUserId, 'captchaToken'?: (string | null)},
    startCaptcha: Record<string, never>,
    createBooking: {'meta': OlzMetaData, 'data': OlzBookingData, 'custom'?: never},
    createRegistration: {'meta': OlzMetaData, 'data': OlzRegistrationData, 'custom'?: never},
    getManagedUsers: (Record<string, never> | null),
    getPrefillValues: {'userId'?: (number | null)},
    getRegistration: {'id': OlzRegistrationId, 'custom'?: never},
    executeCommand: {'command': string, 'argv'?: (string | null)},
    getWebdavAccessToken: (Record<string, never> | null),
    revokeWebdavAccessToken: (Record<string, never> | null),
    getLogs: {'query': OlzLogsQuery},
    importMembers: {'csvFileId': string},
    exportMembers: Record<string, never>,
    getAppMonitoringCredentials: (Record<string, never> | null),
    updateNotificationSubscriptions: {'deliveryType': ('email' | 'telegram'), 'monthlyPreview': boolean, 'weeklyPreview': boolean, 'deadlineWarning': boolean, 'deadlineWarningDays': ('1' | '2' | '3' | '7'), 'dailySummary': boolean, 'dailySummaryAktuell': boolean, 'dailySummaryBlog': boolean, 'dailySummaryForum': boolean, 'dailySummaryGalerie': boolean, 'dailySummaryTermine': boolean, 'weeklySummary': boolean, 'weeklySummaryAktuell': boolean, 'weeklySummaryBlog': boolean, 'weeklySummaryForum': boolean, 'weeklySummaryGalerie': boolean, 'weeklySummaryTermine': boolean},
    searchTransportConnection: {'destination': string, 'arrival': IsoDateTime},
    listPanini2024Pictures: {'filter'?: (({'idIs': number} | {'page': number}) | null)},
    updateMyPanini2024: {'data': OlzPanini2024PictureData},
    getMySkillLevels: {'skillFilter'?: ({'categoryIdIn': Array<string>} | null)},
    updateMySkillLevels: {'updates': {[key: string]: {'change': number}}},
    registerSkillCategories: {'skillCategories': Array<OlzSkillCategoryData>},
    registerSkills: {'skills': Array<OlzSkillData>},
    updateResults: {'file': string, 'content'?: (string | null), 'iofXmlFileId'?: (string | null)},
    getAppSearchEnginesCredentials: Record<string, never>,
    getAppStatisticsCredentials: Record<string, never>,
    getAppYoutubeCredentials: Record<string, never>,
}

export interface OlzApiResponses extends OlzApiEndpointMapping {
    onContinuously: (Record<string, never> | null),
    login: {'status': ('AUTHENTICATED' | 'INVALID_CREDENTIALS' | 'BLOCKED'), 'numRemainingAttempts': (number | null)},
    resetPassword: {'status': ('OK' | 'DENIED' | 'ERROR')},
    switchUser: {'status': 'OK'},
    logout: {'status': ('NO_SESSION' | 'SESSION_CLOSED')},
    getAuthenticatedUser: {'user'?: (OlzAuthenticatedUser | null)},
    getAuthenticatedRoles: {'roles'?: (Array<OlzAuthenticatedRole> | null)},
    getEntitiesAroundPosition: {'before'?: (OlzEntityPositionResult | null), 'this'?: (OlzEntityPositionResult | null), 'after'?: (OlzEntityPositionResult | null)},
    verifyUserEmail: {'status': ('OK' | 'ERROR')},
    updatePassword: {'status': ('OK' | 'OTHER_USER' | 'INVALID_OLD')},
    executeEmailReaction: {'status': ('INVALID_TOKEN' | 'OK')},
    linkTelegram: {'botName': string, 'pin': string},
    onTelegram: (Record<string, never> | null),
    startUpload: {'status': ('OK' | 'ERROR'), 'id'?: (string | null)},
    updateUpload: {'status': ('OK' | 'ERROR')},
    finishUpload: {'status': ('OK' | 'ERROR')},
    searchEntities: {'result': Array<OlzEntityResult>},
    createDownload: {'id'?: (OlzDownloadId | null), 'custom'?: never},
    getDownload: {'id': OlzDownloadId, 'meta': OlzMetaData, 'data': OlzDownloadData, 'custom'?: never},
    editDownload: {'id': OlzDownloadId, 'meta': OlzMetaData, 'data': OlzDownloadData, 'custom'?: never},
    updateDownload: {'id': OlzDownloadId, 'custom'?: never},
    deleteDownload: {'custom'?: never},
    createKarte: {'id'?: (OlzKarteId | null), 'custom'?: never},
    getKarte: {'id': OlzKarteId, 'meta': OlzMetaData, 'data': OlzKarteData, 'custom'?: never},
    editKarte: {'id': OlzKarteId, 'meta': OlzMetaData, 'data': OlzKarteData, 'custom'?: never},
    updateKarte: {'id': OlzKarteId, 'custom'?: never},
    deleteKarte: {'custom'?: never},
    createLink: {'id'?: (OlzLinkId | null), 'custom'?: never},
    getLink: {'id': OlzLinkId, 'meta': OlzMetaData, 'data': OlzLinkData, 'custom'?: never},
    editLink: {'id': OlzLinkId, 'meta': OlzMetaData, 'data': OlzLinkData, 'custom'?: never},
    updateLink: {'id': OlzLinkId, 'custom'?: never},
    deleteLink: {'custom'?: never},
    createNews: {'id'?: (OlzNewsId | null), 'custom'?: {'status': ('OK' | 'DENIED' | 'ERROR')}},
    getNews: {'id': OlzNewsId, 'meta': OlzMetaData, 'data': OlzNewsData, 'custom'?: never},
    editNews: {'id': OlzNewsId, 'meta': OlzMetaData, 'data': OlzNewsData, 'custom'?: never},
    updateNews: {'id': OlzNewsId, 'custom'?: never},
    deleteNews: {'custom'?: never},
    getAuthorInfo: OlzAuthorInfoData,
    createRole: {'id'?: (OlzRoleId | null), 'custom'?: never},
    getRole: {'id': OlzRoleId, 'meta': OlzMetaData, 'data': OlzRoleData, 'custom'?: never},
    editRole: {'id': OlzRoleId, 'meta': OlzMetaData, 'data': OlzRoleData, 'custom'?: never},
    updateRole: {'id': OlzRoleId, 'custom'?: never},
    deleteRole: {'custom'?: never},
    addUserRoleMembership: {'custom'?: never},
    removeUserRoleMembership: {'custom'?: never},
    getRoleInfo: OlzRoleInfoData,
    getSnippet: {'id': OlzSnippetId, 'meta': OlzMetaData, 'data': OlzSnippetData, 'custom'?: never},
    editSnippet: {'id': OlzSnippetId, 'meta': OlzMetaData, 'data': OlzSnippetData, 'custom'?: never},
    updateSnippet: {'id': OlzSnippetId, 'custom'?: never},
    createQuestion: {'id'?: (OlzQuestionId | null), 'custom'?: never},
    getQuestion: {'id': OlzQuestionId, 'meta': OlzMetaData, 'data': OlzQuestionData, 'custom'?: never},
    editQuestion: {'id': OlzQuestionId, 'meta': OlzMetaData, 'data': OlzQuestionData, 'custom'?: never},
    updateQuestion: {'id': OlzQuestionId, 'custom'?: never},
    deleteQuestion: {'custom'?: never},
    createQuestionCategory: {'id'?: (OlzQuestionCategoryId | null), 'custom'?: never},
    getQuestionCategory: {'id': OlzQuestionCategoryId, 'meta': OlzMetaData, 'data': OlzQuestionCategoryData, 'custom'?: never},
    editQuestionCategory: {'id': OlzQuestionCategoryId, 'meta': OlzMetaData, 'data': OlzQuestionCategoryData, 'custom'?: never},
    updateQuestionCategory: {'id': OlzQuestionCategoryId, 'custom'?: never},
    deleteQuestionCategory: {'custom'?: never},
    createWeeklyPicture: {'id'?: (OlzWeeklyPictureId | null), 'custom'?: never},
    getWeeklyPicture: {'id': OlzWeeklyPictureId, 'meta': OlzMetaData, 'data': OlzWeeklyPictureData, 'custom'?: never},
    editWeeklyPicture: {'id': OlzWeeklyPictureId, 'meta': OlzMetaData, 'data': OlzWeeklyPictureData, 'custom'?: never},
    updateWeeklyPicture: {'id': OlzWeeklyPictureId, 'custom'?: never},
    deleteWeeklyPicture: {'custom'?: never},
    createTermin: {'id'?: (OlzTerminId | null), 'custom'?: never},
    getTermin: {'id': OlzTerminId, 'meta': OlzMetaData, 'data': OlzTerminData, 'custom'?: never},
    editTermin: {'id': OlzTerminId, 'meta': OlzMetaData, 'data': OlzTerminData, 'custom'?: never},
    updateTermin: {'id': OlzTerminId, 'custom'?: never},
    deleteTermin: {'custom'?: never},
    createTerminLabel: {'id'?: (OlzTerminLabelId | null), 'custom'?: never},
    listTerminLabels: {'items': Array<{'id': OlzTerminLabelId, 'meta': OlzMetaData, 'data': OlzTerminLabelData, 'custom'?: never}>, 'custom'?: never},
    getTerminLabel: {'id': OlzTerminLabelId, 'meta': OlzMetaData, 'data': OlzTerminLabelData, 'custom'?: never},
    editTerminLabel: {'id': OlzTerminLabelId, 'meta': OlzMetaData, 'data': OlzTerminLabelData, 'custom'?: never},
    updateTerminLabel: {'id': OlzTerminLabelId, 'custom'?: never},
    deleteTerminLabel: {'custom'?: never},
    createTerminLocation: {'id'?: (OlzTerminLocationId | null), 'custom'?: never},
    getTerminLocation: {'id': OlzTerminLocationId, 'meta': OlzMetaData, 'data': OlzTerminLocationData, 'custom'?: never},
    editTerminLocation: {'id': OlzTerminLocationId, 'meta': OlzMetaData, 'data': OlzTerminLocationData, 'custom'?: never},
    updateTerminLocation: {'id': OlzTerminLocationId, 'custom'?: never},
    deleteTerminLocation: {'custom'?: never},
    createTerminTemplate: {'id'?: (OlzTerminTemplateId | null), 'custom'?: never},
    getTerminTemplate: {'id': OlzTerminTemplateId, 'meta': OlzMetaData, 'data': OlzTerminTemplateData, 'custom'?: never},
    editTerminTemplate: {'id': OlzTerminTemplateId, 'meta': OlzMetaData, 'data': OlzTerminTemplateData, 'custom'?: never},
    updateTerminTemplate: {'id': OlzTerminTemplateId, 'custom'?: never},
    deleteTerminTemplate: {'custom'?: never},
    createUser: {'id'?: (OlzUserId | null), 'custom'?: {'status': ('OK' | 'OK_NO_EMAIL_VERIFICATION' | 'DENIED' | 'ERROR')}},
    getUser: {'id': OlzUserId, 'meta': OlzMetaData, 'data': OlzUserData, 'custom'?: never},
    editUser: {'id': OlzUserId, 'meta': OlzMetaData, 'data': OlzUserData, 'custom'?: never},
    updateUser: {'id': OlzUserId, 'custom'?: {'status': ('OK' | 'OK_NO_EMAIL_VERIFICATION' | 'DENIED' | 'ERROR')}},
    deleteUser: {'custom'?: never},
    getUserInfo: OlzUserInfoData,
    startCaptcha: {'config': OlzCaptchaConfig},
    createBooking: {'id'?: (OlzBookingId | null), 'custom'?: never},
    createRegistration: {'id'?: (OlzRegistrationId | null), 'custom'?: never},
    getManagedUsers: {'status': ('OK' | 'ERROR'), 'managedUsers': (Array<ManagedUser> | null)},
    getPrefillValues: UserPrefillData,
    getRegistration: {'id': OlzRegistrationId, 'meta': OlzMetaData, 'data': OlzRegistrationData, 'custom'?: never},
    executeCommand: {'error': boolean, 'output': string},
    getWebdavAccessToken: {'status': ('OK' | 'ERROR'), 'token'?: (string | null)},
    revokeWebdavAccessToken: {'status': ('OK' | 'ERROR')},
    getLogs: {'content': Array<string>, 'pagination': {'previous': (string | null), 'next': (string | null)}},
    importMembers: {'status': ('OK' | 'ERROR'), 'members': Array<OlzMemberInfo>},
    exportMembers: {'status': ('OK' | 'ERROR'), 'csvFileId'?: (string | null)},
    getAppMonitoringCredentials: {'username': string, 'password': string},
    updateNotificationSubscriptions: {'status': ('OK' | 'ERROR')},
    searchTransportConnection: {'status': ('OK' | 'ERROR'), 'suggestions'?: (Array<OlzTransportSuggestion> | null)},
    listPanini2024Pictures: Array<{'data': OlzPanini2024PictureData}>,
    updateMyPanini2024: {'status': ('OK' | 'ERROR')},
    getMySkillLevels: {[key: string]: {'value': number}},
    updateMySkillLevels: {'status': ('OK' | 'ERROR')},
    registerSkillCategories: {'idByName': {[key: string]: string}},
    registerSkills: {'idByName': {[key: string]: string}},
    updateResults: {'status': ('OK' | 'INVALID_FILENAME' | 'INVALID_BASE64_DATA' | 'ERROR')},
    getAppSearchEnginesCredentials: {'username': string, 'password': string},
    getAppStatisticsCredentials: {'username': string, 'password': string},
    getAppYoutubeCredentials: {'username': string, 'password': string},
}

