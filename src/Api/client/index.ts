import * as gen from './generated_olz_api_types';
import {OlzApi, olzApi} from './OlzApi';
import {ValidationError} from 'php-typescript-api';

type OlzApiEndpoint = gen.OlzApiEndpoint;
type OlzApiRequests = gen.OlzApiRequests;
type OlzApiResponses = gen.OlzApiResponses;
type OlzMetaData = gen.Olz_Api_OlzEntityEndpointTrait_OlzMetaData;
type OlzRunData = gen.Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunData;
type OlzLogLevel = gen.Olz_Apps_Logs_Endpoints_GetLogsEndpoint_OlzLogLevel;
type OlzLogsQuery = gen.Olz_Apps_Logs_Endpoints_GetLogsEndpoint_OlzLogsQuery;
type OlzMemberInfo = gen.Olz_Apps_Members_Endpoints_ImportMembersEndpoint_OlzMemberInfo;
type OlzTransportSuggestion = gen.Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportSuggestion;
type OlzTransportHalt = gen.Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportHalt;
type OlzTransportSection = gen.Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportSection;
type OlzPanini2024PictureData = gen.Olz_Apps_Panini2024_Endpoints_UpdateMyPanini2024Endpoint_OlzPanini2024PictureData;
type OlzCaptchaConfig = gen.Olz_Captcha_Utils_CaptchaUtils_OlzCaptchaConfig;
type OlzAuthenticatedRole = gen.Olz_Api_Endpoints_GetAuthenticatedRolesEndpoint_OlzAuthenticatedRole;
type OlzAuthenticatedUser = gen.Olz_Api_Endpoints_GetAuthenticatedUserEndpoint_OlzAuthenticatedUser;
type OlzSearchableEntityType = gen.Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzSearchableEntityType;
type OlzEntityResult = gen.Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzEntityResult;
type OlzQuestionCategoryData = gen.Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryData;
type OlzQuestionData = gen.Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionData;
type OlzKarteData = gen.Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteData;
type OlzKarteKind = gen.Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteKind;
type OlzAuthorInfoData = gen.Olz_News_Endpoints_GetAuthorInfoEndpoint_OlzAuthorInfoData;
type OlzNewsData = gen.Olz_News_Endpoints_NewsEndpointTrait_OlzNewsData;
type OlzNewsFormat = gen.Olz_News_Endpoints_NewsEndpointTrait_OlzNewsFormat;
type OlzRoleData = gen.Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleData;
type OlzRoleInfoData = gen.Olz_Roles_Endpoints_GetRoleInfoEndpoint_OlzRoleInfoData;
type OlzDownloadData = gen.Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadData;
type OlzLinkData = gen.Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkData;
type OlzSnippetData = gen.Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetData;
type OlzWeeklyPictureData = gen.Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureData;
type OlzTerminLabelData = gen.Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData;
type OlzTerminLocationData = gen.Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationData;
type OlzTerminData = gen.Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminData;
type OlzTerminTemplateData = gen.Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateData;
type OlzUserData = gen.Olz_Users_Endpoints_UserEndpointTrait_OlzUserData;
type OlzUserInfoData = gen.Olz_Users_Endpoints_GetUserInfoEndpoint_OlzUserInfoData;
type OlzLocationCoordinates = gen.Olz_Utils_MapUtils_OlzLocationCoordinates;
type OlzEmailInfoData = gen.Olz_Captcha_Endpoints_DecryptEmailTokenEndpoint_OlzEmailInfoData;

export {
    OlzApi,
    olzApi,
    OlzApiEndpoint,
    OlzApiRequests,
    OlzApiResponses,
    ValidationError,

    // ---

    OlzMetaData,
    OlzRunData,
    OlzLogLevel,
    OlzLogsQuery,
    OlzMemberInfo,
    OlzTransportSuggestion,
    OlzTransportHalt,
    OlzTransportSection,
    OlzPanini2024PictureData,
    OlzCaptchaConfig,
    OlzAuthenticatedRole,
    OlzAuthenticatedUser,
    OlzSearchableEntityType,
    OlzEntityResult,
    OlzQuestionCategoryData,
    OlzQuestionData,
    OlzKarteData,
    OlzKarteKind,
    OlzAuthorInfoData,
    OlzNewsData,
    OlzNewsFormat,
    OlzRoleData,
    OlzRoleInfoData,
    OlzDownloadData,
    OlzLinkData,
    OlzSnippetData,
    OlzWeeklyPictureData,
    OlzTerminLabelData,
    OlzTerminLocationData,
    OlzTerminData,
    OlzTerminTemplateData,
    OlzUserData,
    OlzUserInfoData,
    OlzLocationCoordinates,
    OlzEmailInfoData,
};
