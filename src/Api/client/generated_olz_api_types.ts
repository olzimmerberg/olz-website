/** ### This file is auto-generated, modifying is futile! ### */

export type Olz_Api_OlzTypedEndpoint4164b4d1d24dc38f611fd8292b3f625e_Request = {'authenticityCode': string};

export type Olz_Api_OlzTypedEndpoint4164b4d1d24dc38f611fd8292b3f625e_Response = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint7531943200fb44bf407e2f66cfaf55e1_Request = {'usernameOrEmail': string, 'password': string, 'rememberMe': boolean};

export type Olz_Api_OlzTypedEndpoint7531943200fb44bf407e2f66cfaf55e1_Response = {'status': ('AUTHENTICATED' | 'INVALID_CREDENTIALS' | 'BLOCKED'), 'numRemainingAttempts': (number | null)};

export type Olz_Api_OlzTypedEndpoint7b376aaa84e28c6a90e673850719b9d5_Request = {'usernameOrEmail': string, 'captchaToken': string};

export type Olz_Api_OlzTypedEndpoint7b376aaa84e28c6a90e673850719b9d5_Response = {'status': ('OK' | 'DENIED' | 'ERROR')};

export type Olz_Api_OlzTypedEndpoint829e19faa8eaa4462ec42721016d7672_Request = {'userId': number};

export type Olz_Api_OlzTypedEndpoint829e19faa8eaa4462ec42721016d7672_Response = {'status': 'OK'};

export type Olz_Api_OlzTypedEndpoint2b90bc820bc224346fa4f675cde48ece_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint2b90bc820bc224346fa4f675cde48ece_Response = {'status': ('NO_SESSION' | 'SESSION_CLOSED')};

export type Olz_Api_OlzTypedEndpoint8b12c01034c7f4da8eed9167af229c6f_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint8b12c01034c7f4da8eed9167af229c6f_Response = {'user'?: (Olz_Api_Endpoints_GetAuthenticatedUserEndpoint_OlzAuthenticatedUser | null)};

export type Olz_Api_Endpoints_GetAuthenticatedUserEndpoint_OlzAuthenticatedUser = {'id': number, 'firstName': string, 'lastName': string, 'username': string};

export type Olz_Api_OlzTypedEndpoint904c19fd8ac53353b810ddcb25f4858f_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint904c19fd8ac53353b810ddcb25f4858f_Response = {'roles'?: (Array<Olz_Api_Endpoints_GetAuthenticatedRolesEndpoint_OlzAuthenticatedRole> | null)};

export type Olz_Api_Endpoints_GetAuthenticatedRolesEndpoint_OlzAuthenticatedRole = {'id': number, 'name': string, 'username': string};

export type Olz_Api_OlzTypedEndpointebc70268597b516012e174432a1f406f_Request = {'entityType': Olz_Api_Endpoints_GetEntitiesAroundPositionEndpoint_OlzSearchableEntityType, 'entityField': string, 'id'?: (number | null), 'position'?: (number | null), 'filter'?: ({[key: string]: string} | null)};

export type Olz_Api_OlzTypedEndpointebc70268597b516012e174432a1f406f_Response = {'before'?: (Olz_Api_Endpoints_GetEntitiesAroundPositionEndpoint_OlzEntityPositionResult | null), 'this'?: (Olz_Api_Endpoints_GetEntitiesAroundPositionEndpoint_OlzEntityPositionResult | null), 'after'?: (Olz_Api_Endpoints_GetEntitiesAroundPositionEndpoint_OlzEntityPositionResult | null)};

export type Olz_Api_Endpoints_GetEntitiesAroundPositionEndpoint_OlzSearchableEntityType = Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzSearchableEntityType;

export type Olz_Api_Endpoints_GetEntitiesAroundPositionEndpoint_OlzEntityPositionResult = {'id': number, 'position': (number | null), 'title': string};

export type Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzSearchableEntityType = ('Download' | 'Link' | 'Question' | 'QuestionCategory' | 'SolvEvent' | 'TerminLabel' | 'TerminLocation' | 'TerminTemplate' | 'Role' | 'User');

export type Olz_Api_OlzTypedEndpoint4ed8fea3d3f2a3eeb335cfe0fe0b7a54_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint4ed8fea3d3f2a3eeb335cfe0fe0b7a54_Response = {'status': ('OK' | 'ERROR')};

export type Olz_Api_OlzTypedEndpointe0c73364ca4b2e5b51724d981de23a19_Request = {'id': number, 'oldPassword': string, 'newPassword': string};

export type Olz_Api_OlzTypedEndpointe0c73364ca4b2e5b51724d981de23a19_Response = {'status': ('OK' | 'OTHER_USER' | 'INVALID_OLD')};

export type Olz_Api_OlzTypedEndpoint1fb2df4ee7ff7fba2c6061bd8eb791e3_Request = {'token': string};

export type Olz_Api_OlzTypedEndpoint1fb2df4ee7ff7fba2c6061bd8eb791e3_Response = {'status': ('INVALID_TOKEN' | 'OK')};

export type Olz_Api_OlzTypedEndpointae9b80a323459f81cd5c7f02633ca62a_Request = {'code': string};

export type Olz_Api_OlzTypedEndpointae9b80a323459f81cd5c7f02633ca62a_Response = Record<string, never>;

export type Olz_Api_OlzTypedEndpoint521aed3921a5c624e481534d97de7df2_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint521aed3921a5c624e481534d97de7df2_Response = {'botName': string, 'pin': string};

export type Olz_Api_OlzTypedEndpointdf8fed3e82edd9906220af4abcb7a4c8_Request = {'authenticityCode': string, 'telegramEvent': string};

export type Olz_Api_OlzTypedEndpointdf8fed3e82edd9906220af4abcb7a4c8_Response = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint4b9701069d098c637985e79e8768561b_Request = {'suffix'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint4b9701069d098c637985e79e8768561b_Response = {'status': ('OK' | 'ERROR'), 'id'?: (string | null)};

export type Olz_Api_OlzTypedEndpointd117e6872a98ac8700650adae232c1b1_Request = {'id': string, 'part': number, 'content': string};

export type Olz_Api_OlzTypedEndpointd117e6872a98ac8700650adae232c1b1_Response = {'status': ('OK' | 'ERROR')};

export type Olz_Api_OlzTypedEndpoint9baba960f97e0a6240ed22098e6ff39a_Request = {'id': string, 'numberOfParts': number};

export type Olz_Api_OlzTypedEndpoint9baba960f97e0a6240ed22098e6ff39a_Response = {'status': ('OK' | 'ERROR')};

export type Olz_Api_OlzTypedEndpoint6fa66265cdbcade62b0a9b7e88701d82_Request = {'entityType': Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzSearchableEntityType, 'query'?: (string | null), 'id'?: (number | null), 'filter'?: ({[key: string]: string} | null)};

export type Olz_Api_OlzTypedEndpoint6fa66265cdbcade62b0a9b7e88701d82_Response = {'result': Array<Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzEntityResult>};

export type Olz_Api_Endpoints_SearchEntitiesEndpoint_OlzEntityResult = {'id': number, 'title': string};

export type Olz_Api_OlzTypedEndpoint684aef211f71db9c37adc9bd332bd07e_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_CustomRequest};

export type Olz_Api_OlzTypedEndpoint684aef211f71db9c37adc9bd332bd07e_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_Data = Olz_Service_Endpoints_CreateDownloadEndpoint_OlzDownloadData;

export type Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_Id = Olz_Service_Endpoints_CreateDownloadEndpoint_OlzDownloadId;

export type Olz_Api_OlzCreateEntityTypedEndpointe77f241a1f3df236251c968ecf335417_CustomResponse = never;

export type Olz_Api_OlzEntityEndpointTrait_OlzMetaData = {'ownerUserId': (number | null), 'ownerRoleId': (number | null), 'onOff': boolean};

export type Olz_Service_Endpoints_CreateDownloadEndpoint_OlzDownloadData = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadData;

export type Olz_Service_Endpoints_CreateDownloadEndpoint_OlzDownloadId = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadId;

export type Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadData = {'name': string, 'position'?: (number | null), 'fileId'?: (string | null)};

export type Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadId = number;

export type Olz_Api_OlzTypedEndpointe74f002b53b4440c17f4a57531cbf2f1_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_CustomRequest};

export type Olz_Api_OlzTypedEndpointe74f002b53b4440c17f4a57531cbf2f1_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_Id = Olz_Service_Endpoints_GetDownloadEndpoint_OlzDownloadId;

export type Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_Data = Olz_Service_Endpoints_GetDownloadEndpoint_OlzDownloadData;

export type Olz_Api_OlzGetEntityTypedEndpoint16734e8df6c714f432ffb3e9ab5b9dbc_CustomResponse = never;

export type Olz_Service_Endpoints_GetDownloadEndpoint_OlzDownloadId = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadId;

export type Olz_Service_Endpoints_GetDownloadEndpoint_OlzDownloadData = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadData;

export type Olz_Api_OlzTypedEndpoint3dad7d40795e6e21b3ddda39464756cd_Request = {'id': Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_CustomRequest};

export type Olz_Api_OlzTypedEndpoint3dad7d40795e6e21b3ddda39464756cd_Response = {'id': Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_Id = Olz_Service_Endpoints_EditDownloadEndpoint_OlzDownloadId;

export type Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_Data = Olz_Service_Endpoints_EditDownloadEndpoint_OlzDownloadData;

export type Olz_Api_OlzEditEntityTypedEndpointb0deca3c2c1e17065dc5b5f636c22b0f_CustomResponse = never;

export type Olz_Service_Endpoints_EditDownloadEndpoint_OlzDownloadId = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadId;

export type Olz_Service_Endpoints_EditDownloadEndpoint_OlzDownloadData = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadData;

export type Olz_Api_OlzTypedEndpoint9e732a8690606b1f652ba5bb495ac89f_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_CustomRequest};

export type Olz_Api_OlzTypedEndpoint9e732a8690606b1f652ba5bb495ac89f_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_Id = Olz_Service_Endpoints_UpdateDownloadEndpoint_OlzDownloadId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_Data = Olz_Service_Endpoints_UpdateDownloadEndpoint_OlzDownloadData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c4a1a5df42dcf617492d128669c491b_CustomResponse = never;

export type Olz_Service_Endpoints_UpdateDownloadEndpoint_OlzDownloadId = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadId;

export type Olz_Service_Endpoints_UpdateDownloadEndpoint_OlzDownloadData = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadData;

export type Olz_Api_OlzTypedEndpoint7973150998eccd6a727ac06752497c82_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint9a563f59ec487344ef5004824a48c955_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint9a563f59ec487344ef5004824a48c955_CustomRequest};

export type Olz_Api_OlzTypedEndpoint7973150998eccd6a727ac06752497c82_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint9a563f59ec487344ef5004824a48c955_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint9a563f59ec487344ef5004824a48c955_Id = Olz_Service_Endpoints_DeleteDownloadEndpoint_OlzDownloadId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint9a563f59ec487344ef5004824a48c955_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint9a563f59ec487344ef5004824a48c955_CustomResponse = never;

export type Olz_Service_Endpoints_DeleteDownloadEndpoint_OlzDownloadId = Olz_Service_Endpoints_DownloadEndpointTrait_OlzDownloadId;

export type Olz_Api_OlzTypedEndpointecb37fb77f8223d6eebe57ccfb56db6f_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_CustomRequest};

export type Olz_Api_OlzTypedEndpointecb37fb77f8223d6eebe57ccfb56db6f_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_Data = Olz_Karten_Endpoints_CreateKarteEndpoint_OlzKarteData;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_Id = Olz_Karten_Endpoints_CreateKarteEndpoint_OlzKarteId;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fed9ae1437ef8a1c3b34e19abb59ff1_CustomResponse = never;

export type Olz_Karten_Endpoints_CreateKarteEndpoint_OlzKarteData = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteData;

export type Olz_Karten_Endpoints_CreateKarteEndpoint_OlzKarteId = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteId;

export type Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteData = {'kartennr'?: (number | null), 'name': string, 'location'?: (Olz_Karten_Endpoints_KarteEndpointTrait_OlzLocationCoordinates | null), 'year'?: (number | null), 'scale'?: (string | null), 'place'?: (string | null), 'zoom'?: (number | null), 'kind'?: (Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteKind | null), 'previewImageId'?: (string | null)};

export type Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteId = number;

export type Olz_Karten_Endpoints_KarteEndpointTrait_OlzLocationCoordinates = Olz_Utils_MapUtils_OlzLocationCoordinates;

export type Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteKind = ('ol' | 'stadt' | 'scool');

export type Olz_Utils_MapUtils_OlzLocationCoordinates = {'latitude': number, 'longitude': number};

export type Olz_Api_OlzTypedEndpoint5d02f180fe5876e5d2e56cba74dfbb25_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_CustomRequest};

export type Olz_Api_OlzTypedEndpoint5d02f180fe5876e5d2e56cba74dfbb25_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_Id = Olz_Karten_Endpoints_GetKarteEndpoint_OlzKarteId;

export type Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_Data = Olz_Karten_Endpoints_GetKarteEndpoint_OlzKarteData;

export type Olz_Api_OlzGetEntityTypedEndpoint30455ea4a414619c516ba9c084b9d175_CustomResponse = never;

export type Olz_Karten_Endpoints_GetKarteEndpoint_OlzKarteId = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteId;

export type Olz_Karten_Endpoints_GetKarteEndpoint_OlzKarteData = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteData;

export type Olz_Api_OlzTypedEndpointad0ac204338a78ae67916f14b92190ac_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_CustomRequest};

export type Olz_Api_OlzTypedEndpointad0ac204338a78ae67916f14b92190ac_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_Id = Olz_Karten_Endpoints_EditKarteEndpoint_OlzKarteId;

export type Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_Data = Olz_Karten_Endpoints_EditKarteEndpoint_OlzKarteData;

export type Olz_Api_OlzEditEntityTypedEndpoint322b367486756d1431a7d2a20c403257_CustomResponse = never;

export type Olz_Karten_Endpoints_EditKarteEndpoint_OlzKarteId = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteId;

export type Olz_Karten_Endpoints_EditKarteEndpoint_OlzKarteData = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteData;

export type Olz_Api_OlzTypedEndpoint5ce6cd7a8fcb262a1776aa3f3da7e372_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_CustomRequest};

export type Olz_Api_OlzTypedEndpoint5ce6cd7a8fcb262a1776aa3f3da7e372_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_Id = Olz_Karten_Endpoints_UpdateKarteEndpoint_OlzKarteId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_Data = Olz_Karten_Endpoints_UpdateKarteEndpoint_OlzKarteData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint28ccea5c8e43d2dd30f5aaf50646444e_CustomResponse = never;

export type Olz_Karten_Endpoints_UpdateKarteEndpoint_OlzKarteId = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteId;

export type Olz_Karten_Endpoints_UpdateKarteEndpoint_OlzKarteData = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteData;

export type Olz_Api_OlzTypedEndpointf6e1d7620721d58d30a5bf6e3a5e27dd_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint12d5759d7ab1b4b561e75e3e742817ce_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint12d5759d7ab1b4b561e75e3e742817ce_CustomRequest};

export type Olz_Api_OlzTypedEndpointf6e1d7620721d58d30a5bf6e3a5e27dd_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint12d5759d7ab1b4b561e75e3e742817ce_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint12d5759d7ab1b4b561e75e3e742817ce_Id = Olz_Karten_Endpoints_DeleteKarteEndpoint_OlzKarteId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint12d5759d7ab1b4b561e75e3e742817ce_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint12d5759d7ab1b4b561e75e3e742817ce_CustomResponse = never;

export type Olz_Karten_Endpoints_DeleteKarteEndpoint_OlzKarteId = Olz_Karten_Endpoints_KarteEndpointTrait_OlzKarteId;

export type Olz_Api_OlzTypedEndpointc411b1a560c070b45bac8006c3bfde54_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_CustomRequest};

export type Olz_Api_OlzTypedEndpointc411b1a560c070b45bac8006c3bfde54_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_Data = Olz_Service_Endpoints_CreateLinkEndpoint_OlzLinkData;

export type Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_Id = Olz_Service_Endpoints_CreateLinkEndpoint_OlzLinkId;

export type Olz_Api_OlzCreateEntityTypedEndpointe17dbdf98f509cc021398b2b6f2bc52e_CustomResponse = never;

export type Olz_Service_Endpoints_CreateLinkEndpoint_OlzLinkData = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkData;

export type Olz_Service_Endpoints_CreateLinkEndpoint_OlzLinkId = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkId;

export type Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkData = {'position'?: (number | null), 'name': string, 'url': string};

export type Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkId = number;

export type Olz_Api_OlzTypedEndpoint5b0bc669977e4cabe5618a03d09c140a_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_CustomRequest};

export type Olz_Api_OlzTypedEndpoint5b0bc669977e4cabe5618a03d09c140a_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_Id = Olz_Service_Endpoints_GetLinkEndpoint_OlzLinkId;

export type Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_Data = Olz_Service_Endpoints_GetLinkEndpoint_OlzLinkData;

export type Olz_Api_OlzGetEntityTypedEndpoint8ac49830f4271c960e81eb72650f6a9b_CustomResponse = never;

export type Olz_Service_Endpoints_GetLinkEndpoint_OlzLinkId = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkId;

export type Olz_Service_Endpoints_GetLinkEndpoint_OlzLinkData = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkData;

export type Olz_Api_OlzTypedEndpointd86df622e688004c841884bde3783efb_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_CustomRequest};

export type Olz_Api_OlzTypedEndpointd86df622e688004c841884bde3783efb_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_Id = Olz_Service_Endpoints_EditLinkEndpoint_OlzLinkId;

export type Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_Data = Olz_Service_Endpoints_EditLinkEndpoint_OlzLinkData;

export type Olz_Api_OlzEditEntityTypedEndpoint49dba7dd65e0db8ddf0ec9108ecda363_CustomResponse = never;

export type Olz_Service_Endpoints_EditLinkEndpoint_OlzLinkId = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkId;

export type Olz_Service_Endpoints_EditLinkEndpoint_OlzLinkData = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkData;

export type Olz_Api_OlzTypedEndpointcad61eb502060c5f4138ce6971273211_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_CustomRequest};

export type Olz_Api_OlzTypedEndpointcad61eb502060c5f4138ce6971273211_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_Id = Olz_Service_Endpoints_UpdateLinkEndpoint_OlzLinkId;

export type Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_Data = Olz_Service_Endpoints_UpdateLinkEndpoint_OlzLinkData;

export type Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpointbaf6b01121f7184b6552d9f9a48e3ba0_CustomResponse = never;

export type Olz_Service_Endpoints_UpdateLinkEndpoint_OlzLinkId = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkId;

export type Olz_Service_Endpoints_UpdateLinkEndpoint_OlzLinkData = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkData;

export type Olz_Api_OlzTypedEndpointbd84c56358ff8d45b0b2695bbf8f2690_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint95d2c47985d0cc9f41a93a66fe53ed00_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint95d2c47985d0cc9f41a93a66fe53ed00_CustomRequest};

export type Olz_Api_OlzTypedEndpointbd84c56358ff8d45b0b2695bbf8f2690_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint95d2c47985d0cc9f41a93a66fe53ed00_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint95d2c47985d0cc9f41a93a66fe53ed00_Id = Olz_Service_Endpoints_DeleteLinkEndpoint_OlzLinkId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint95d2c47985d0cc9f41a93a66fe53ed00_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint95d2c47985d0cc9f41a93a66fe53ed00_CustomResponse = never;

export type Olz_Service_Endpoints_DeleteLinkEndpoint_OlzLinkId = Olz_Service_Endpoints_LinkEndpointTrait_OlzLinkId;

export type Olz_Api_OlzTypedEndpoint4c20f9ad209af44598bc580ebef999b8_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_CustomRequest};

export type Olz_Api_OlzTypedEndpoint4c20f9ad209af44598bc580ebef999b8_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_Data = Olz_News_Endpoints_CreateNewsEndpoint_OlzNewsData;

export type Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_CustomRequest = {'captchaToken'?: (string | null)};

export type Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_Id = Olz_News_Endpoints_CreateNewsEndpoint_OlzNewsId;

export type Olz_Api_OlzCreateEntityTypedEndpoint69bcb66c13ff91714c82736588ac4b06_CustomResponse = {'status': ('OK' | 'DENIED' | 'ERROR')};

export type Olz_News_Endpoints_CreateNewsEndpoint_OlzNewsData = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsData;

export type Olz_News_Endpoints_CreateNewsEndpoint_OlzNewsId = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsId;

export type Olz_News_Endpoints_NewsEndpointTrait_OlzNewsData = {'format': Olz_News_Endpoints_NewsEndpointTrait_OlzNewsFormat, 'authorUserId'?: (number | null), 'authorRoleId'?: (number | null), 'authorName'?: (string | null), 'authorEmail'?: (string | null), 'publishAt'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'title': string, 'teaser': string, 'content': string, 'externalUrl'?: (string | null), 'tags': Array<string>, 'terminId'?: (number | null), 'imageIds'?: (Array<string> | null), 'fileIds': Array<string>};

export type Olz_News_Endpoints_NewsEndpointTrait_OlzNewsId = number;

export type Olz_News_Endpoints_NewsEndpointTrait_OlzNewsFormat = ('aktuell' | 'kaderblog' | 'forum' | 'galerie' | 'video' | 'anonymous');

export type PhpTypeScriptApi_PhpStan_IsoDateTime = string;

export type Olz_Api_OlzTypedEndpoint6624b36fc79007ae66efd04a258afe95_Request = {'id': Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_CustomRequest};

export type Olz_Api_OlzTypedEndpoint6624b36fc79007ae66efd04a258afe95_Response = {'id': Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_Id = Olz_News_Endpoints_GetNewsEndpoint_OlzNewsId;

export type Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_Data = Olz_News_Endpoints_GetNewsEndpoint_OlzNewsData;

export type Olz_Api_OlzGetEntityTypedEndpointbbb3f3425588c51db6fe24e63f3fedc8_CustomResponse = never;

export type Olz_News_Endpoints_GetNewsEndpoint_OlzNewsId = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsId;

export type Olz_News_Endpoints_GetNewsEndpoint_OlzNewsData = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsData;

export type Olz_Api_OlzTypedEndpoint6f69dc824851471c31dfb3e6b49cbbba_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_CustomRequest};

export type Olz_Api_OlzTypedEndpoint6f69dc824851471c31dfb3e6b49cbbba_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_Id = Olz_News_Endpoints_EditNewsEndpoint_OlzNewsId;

export type Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_Data = Olz_News_Endpoints_EditNewsEndpoint_OlzNewsData;

export type Olz_Api_OlzEditEntityTypedEndpoint5463065281507b40942b5a35ad5e7795_CustomResponse = never;

export type Olz_News_Endpoints_EditNewsEndpoint_OlzNewsId = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsId;

export type Olz_News_Endpoints_EditNewsEndpoint_OlzNewsData = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsData;

export type Olz_Api_OlzTypedEndpoint9cc8cb238c7b7382e41e76c82f736c96_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_CustomRequest};

export type Olz_Api_OlzTypedEndpoint9cc8cb238c7b7382e41e76c82f736c96_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_Id = Olz_News_Endpoints_UpdateNewsEndpoint_OlzNewsId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_Data = Olz_News_Endpoints_UpdateNewsEndpoint_OlzNewsData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint19e382f345681f6b520f84914b1c2a84_CustomResponse = never;

export type Olz_News_Endpoints_UpdateNewsEndpoint_OlzNewsId = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsId;

export type Olz_News_Endpoints_UpdateNewsEndpoint_OlzNewsData = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsData;

export type Olz_Api_OlzTypedEndpoint94a3d0f53a145aff5f346769797884f7_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint933c6e721185eb3efea681d145f82dd1_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint933c6e721185eb3efea681d145f82dd1_CustomRequest};

export type Olz_Api_OlzTypedEndpoint94a3d0f53a145aff5f346769797884f7_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint933c6e721185eb3efea681d145f82dd1_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint933c6e721185eb3efea681d145f82dd1_Id = Olz_News_Endpoints_DeleteNewsEndpoint_OlzNewsId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint933c6e721185eb3efea681d145f82dd1_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint933c6e721185eb3efea681d145f82dd1_CustomResponse = never;

export type Olz_News_Endpoints_DeleteNewsEndpoint_OlzNewsId = Olz_News_Endpoints_NewsEndpointTrait_OlzNewsId;

export type Olz_Api_OlzTypedEndpoint6664ec7267d06a2c192baa1ac488f2dc_Request = {'id': Olz_News_Endpoints_GetAuthorInfoEndpoint_OlzNewsId, 'captchaToken'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint6664ec7267d06a2c192baa1ac488f2dc_Response = Olz_News_Endpoints_GetAuthorInfoEndpoint_OlzAuthorInfoData;

export type Olz_News_Endpoints_GetAuthorInfoEndpoint_OlzNewsId = number;

export type Olz_News_Endpoints_GetAuthorInfoEndpoint_OlzAuthorInfoData = {'roleName'?: (string | null), 'roleUsername'?: (string | null), 'firstName': string, 'lastName': string, 'email'?: (Array<string> | null), 'avatarImageId'?: ({[key: string]: string} | null)};

export type Olz_Api_OlzTypedEndpointf091e93370e42c7ba62ac4a32e1c40f5_Request = {'filter': Olz_News_Endpoints_ListNewsReactionsEndpoint_OlzNewsReactionFilter};

export type Olz_Api_OlzTypedEndpointf091e93370e42c7ba62ac4a32e1c40f5_Response = {'result': Array<Olz_News_Endpoints_ListNewsReactionsEndpoint_OlzReaction>};

export type Olz_News_Endpoints_ListNewsReactionsEndpoint_OlzNewsReactionFilter = {'newsEntryId': number};

export type Olz_News_Endpoints_ListNewsReactionsEndpoint_OlzReaction = {'userId': number, 'name': (string | null), 'emoji': string};

export type Olz_Api_OlzTypedEndpointb86ece73e26e81be28d034731631534a_Request = {'newsEntryId': number, 'emoji': string, 'action': ('on' | 'off' | 'toggle')};

export type Olz_Api_OlzTypedEndpointb86ece73e26e81be28d034731631534a_Response = {'result': (Olz_News_Endpoints_ToggleNewsReactionEndpoint_OlzReaction | null)};

export type Olz_News_Endpoints_ToggleNewsReactionEndpoint_OlzReaction = Olz_News_Endpoints_ListNewsReactionsEndpoint_OlzReaction;

export type Olz_Api_OlzTypedEndpoint576e136131c17cd3ce98f45eb4f09b42_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_CustomRequest};

export type Olz_Api_OlzTypedEndpoint576e136131c17cd3ce98f45eb4f09b42_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_Data = Olz_Roles_Endpoints_CreateRoleEndpoint_OlzRoleData;

export type Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_Id = Olz_Roles_Endpoints_CreateRoleEndpoint_OlzRoleId;

export type Olz_Api_OlzCreateEntityTypedEndpoint7c4d6b31e4b761693bbc799b48850c6a_CustomResponse = never;

export type Olz_Roles_Endpoints_CreateRoleEndpoint_OlzRoleData = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleData;

export type Olz_Roles_Endpoints_CreateRoleEndpoint_OlzRoleId = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleId;

export type Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleData = {'username': string, 'name': string, 'description': string, 'guide': string, 'imageIds': Array<string>, 'fileIds': Array<string>, 'parentRole'?: (number | null), 'positionWithinParent'?: (number | null), 'featuredPosition'?: (number | null), 'canHaveChildRoles': boolean};

export type Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleId = number;

export type Olz_Api_OlzTypedEndpoint1f2a0489dfbd35ff09e89fbbd44ac432_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_CustomRequest};

export type Olz_Api_OlzTypedEndpoint1f2a0489dfbd35ff09e89fbbd44ac432_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_Id = Olz_Roles_Endpoints_GetRoleEndpoint_OlzRoleId;

export type Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_Data = Olz_Roles_Endpoints_GetRoleEndpoint_OlzRoleData;

export type Olz_Api_OlzGetEntityTypedEndpoint197578dc0bcb55a846281eaebd02faf9_CustomResponse = never;

export type Olz_Roles_Endpoints_GetRoleEndpoint_OlzRoleId = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleId;

export type Olz_Roles_Endpoints_GetRoleEndpoint_OlzRoleData = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleData;

export type Olz_Api_OlzTypedEndpoint373ffe0fa05d1cd7a0158f3643700420_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_CustomRequest};

export type Olz_Api_OlzTypedEndpoint373ffe0fa05d1cd7a0158f3643700420_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_Id = Olz_Roles_Endpoints_EditRoleEndpoint_OlzRoleId;

export type Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_Data = Olz_Roles_Endpoints_EditRoleEndpoint_OlzRoleData;

export type Olz_Api_OlzEditEntityTypedEndpoint1251f0c1bbc483070891ef7c3f735bc7_CustomResponse = never;

export type Olz_Roles_Endpoints_EditRoleEndpoint_OlzRoleId = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleId;

export type Olz_Roles_Endpoints_EditRoleEndpoint_OlzRoleData = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleData;

export type Olz_Api_OlzTypedEndpoint539ece533e346efe6244a7c25d0cff48_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_CustomRequest};

export type Olz_Api_OlzTypedEndpoint539ece533e346efe6244a7c25d0cff48_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_Id = Olz_Roles_Endpoints_UpdateRoleEndpoint_OlzRoleId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_Data = Olz_Roles_Endpoints_UpdateRoleEndpoint_OlzRoleData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint60ab775eb66ed6a3aff17b6bbb155192_CustomResponse = never;

export type Olz_Roles_Endpoints_UpdateRoleEndpoint_OlzRoleId = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleId;

export type Olz_Roles_Endpoints_UpdateRoleEndpoint_OlzRoleData = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleData;

export type Olz_Api_OlzTypedEndpoint3cb1738439d90e249623ede72a6ddf99_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint699f9a793f55102897903b6594252d72_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint699f9a793f55102897903b6594252d72_CustomRequest};

export type Olz_Api_OlzTypedEndpoint3cb1738439d90e249623ede72a6ddf99_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint699f9a793f55102897903b6594252d72_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint699f9a793f55102897903b6594252d72_Id = Olz_Roles_Endpoints_DeleteRoleEndpoint_OlzRoleId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint699f9a793f55102897903b6594252d72_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint699f9a793f55102897903b6594252d72_CustomResponse = never;

export type Olz_Roles_Endpoints_DeleteRoleEndpoint_OlzRoleId = Olz_Roles_Endpoints_RoleEndpointTrait_OlzRoleId;

export type Olz_Api_OlzTypedEndpoint8545c2a902fcf0fefcefe74d4640e541_Request = {'ids': Olz_Api_OlzAddRelationTypedEndpointb796f5622ac1249a7001e2de3f6e59fe_Ids, 'custom'?: Olz_Api_OlzAddRelationTypedEndpointb796f5622ac1249a7001e2de3f6e59fe_CustomRequest};

export type Olz_Api_OlzTypedEndpoint8545c2a902fcf0fefcefe74d4640e541_Response = {'custom'?: Olz_Api_OlzAddRelationTypedEndpointb796f5622ac1249a7001e2de3f6e59fe_CustomResponse};

export type Olz_Api_OlzAddRelationTypedEndpointb796f5622ac1249a7001e2de3f6e59fe_Ids = Olz_Roles_Endpoints_AddUserRoleMembershipEndpoint_OlzRoleMembershipIds;

export type Olz_Api_OlzAddRelationTypedEndpointb796f5622ac1249a7001e2de3f6e59fe_CustomRequest = never;

export type Olz_Api_OlzAddRelationTypedEndpointb796f5622ac1249a7001e2de3f6e59fe_CustomResponse = never;

export type Olz_Roles_Endpoints_AddUserRoleMembershipEndpoint_OlzRoleMembershipIds = Olz_Roles_Endpoints_UserRoleMembershipEndpointTrait_OlzRoleMembershipIds;

export type Olz_Roles_Endpoints_UserRoleMembershipEndpointTrait_OlzRoleMembershipIds = {'roleId': number, 'userId': number};

export type Olz_Api_OlzTypedEndpointd48b6e3a2b3398eea742833d09dbe22f_Request = {'ids': Olz_Api_OlzRemoveRelationTypedEndpoint4bd0d166c36d885a8bb04eed4ab82a6e_Ids, 'custom'?: Olz_Api_OlzRemoveRelationTypedEndpoint4bd0d166c36d885a8bb04eed4ab82a6e_CustomRequest};

export type Olz_Api_OlzTypedEndpointd48b6e3a2b3398eea742833d09dbe22f_Response = {'custom'?: Olz_Api_OlzRemoveRelationTypedEndpoint4bd0d166c36d885a8bb04eed4ab82a6e_CustomResponse};

export type Olz_Api_OlzRemoveRelationTypedEndpoint4bd0d166c36d885a8bb04eed4ab82a6e_Ids = Olz_Roles_Endpoints_RemoveUserRoleMembershipEndpoint_OlzRoleMembershipIds;

export type Olz_Api_OlzRemoveRelationTypedEndpoint4bd0d166c36d885a8bb04eed4ab82a6e_CustomRequest = never;

export type Olz_Api_OlzRemoveRelationTypedEndpoint4bd0d166c36d885a8bb04eed4ab82a6e_CustomResponse = never;

export type Olz_Roles_Endpoints_RemoveUserRoleMembershipEndpoint_OlzRoleMembershipIds = Olz_Roles_Endpoints_UserRoleMembershipEndpointTrait_OlzRoleMembershipIds;

export type Olz_Api_OlzTypedEndpointd0f24d2860294d00c9f3671fd1b09be2_Request = {'id': Olz_Roles_Endpoints_GetRoleInfoEndpoint_OlzRoleId, 'captchaToken'?: (string | null)};

export type Olz_Api_OlzTypedEndpointd0f24d2860294d00c9f3671fd1b09be2_Response = Olz_Roles_Endpoints_GetRoleInfoEndpoint_OlzRoleInfoData;

export type Olz_Roles_Endpoints_GetRoleInfoEndpoint_OlzRoleId = number;

export type Olz_Roles_Endpoints_GetRoleInfoEndpoint_OlzRoleInfoData = {'name'?: (string | null), 'username'?: (string | null), 'assignees': Array<{'firstName': string, 'lastName': string, 'email'?: (Array<string> | null), 'avatarImageId'?: {[key: string]: string}}>};

export type Olz_Api_OlzTypedEndpointe17d3e151e9de1f6183752624a658c0d_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_CustomRequest};

export type Olz_Api_OlzTypedEndpointe17d3e151e9de1f6183752624a658c0d_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_Data = Olz_Anniversary_Endpoints_CreateRunEndpoint_OlzRunData;

export type Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_Id = Olz_Anniversary_Endpoints_CreateRunEndpoint_OlzRunId;

export type Olz_Api_OlzCreateEntityTypedEndpoint93f7bee6a51a8cae8d54842062c478ef_CustomResponse = never;

export type Olz_Anniversary_Endpoints_CreateRunEndpoint_OlzRunData = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunData;

export type Olz_Anniversary_Endpoints_CreateRunEndpoint_OlzRunId = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunId;

export type Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunData = {'userId'?: (number | null), 'runAt'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'distanceMeters': number, 'elevationMeters': number, 'sportType'?: (string | null), 'source'?: (string | null)};

export type Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunId = number;

export type Olz_Api_OlzTypedEndpoint68daac45b341eb65e90f62617fb47770_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_CustomRequest};

export type Olz_Api_OlzTypedEndpoint68daac45b341eb65e90f62617fb47770_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_Id = Olz_Anniversary_Endpoints_GetRunEndpoint_OlzRunId;

export type Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_Data = Olz_Anniversary_Endpoints_GetRunEndpoint_OlzRunData;

export type Olz_Api_OlzGetEntityTypedEndpoint118a027c8c24a9a26590e7ec5b5adc19_CustomResponse = never;

export type Olz_Anniversary_Endpoints_GetRunEndpoint_OlzRunId = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunId;

export type Olz_Anniversary_Endpoints_GetRunEndpoint_OlzRunData = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunData;

export type Olz_Api_OlzTypedEndpoint93292215f74430f493f350e58919db96_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_CustomRequest};

export type Olz_Api_OlzTypedEndpoint93292215f74430f493f350e58919db96_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_Id = Olz_Anniversary_Endpoints_EditRunEndpoint_OlzRunId;

export type Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_Data = Olz_Anniversary_Endpoints_EditRunEndpoint_OlzRunData;

export type Olz_Api_OlzEditEntityTypedEndpoint269a9159748f658726b391e08fbda34f_CustomResponse = never;

export type Olz_Anniversary_Endpoints_EditRunEndpoint_OlzRunId = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunId;

export type Olz_Anniversary_Endpoints_EditRunEndpoint_OlzRunData = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunData;

export type Olz_Api_OlzTypedEndpoint6a479260b2220873fed7e7be7ea4b10e_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_CustomRequest};

export type Olz_Api_OlzTypedEndpoint6a479260b2220873fed7e7be7ea4b10e_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_Id = Olz_Anniversary_Endpoints_UpdateRunEndpoint_OlzRunId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_Data = Olz_Anniversary_Endpoints_UpdateRunEndpoint_OlzRunData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint6e5de3dc7022063b27fadb509506b12a_CustomResponse = never;

export type Olz_Anniversary_Endpoints_UpdateRunEndpoint_OlzRunId = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunId;

export type Olz_Anniversary_Endpoints_UpdateRunEndpoint_OlzRunData = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunData;

export type Olz_Api_OlzTypedEndpoint9e5c64328cde85f3c6b5977670c5bbab_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint2955401b27d2f48d7b8454e8fdd0c457_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint2955401b27d2f48d7b8454e8fdd0c457_CustomRequest};

export type Olz_Api_OlzTypedEndpoint9e5c64328cde85f3c6b5977670c5bbab_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint2955401b27d2f48d7b8454e8fdd0c457_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint2955401b27d2f48d7b8454e8fdd0c457_Id = Olz_Anniversary_Endpoints_DeleteRunEndpoint_OlzRunId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint2955401b27d2f48d7b8454e8fdd0c457_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint2955401b27d2f48d7b8454e8fdd0c457_CustomResponse = never;

export type Olz_Anniversary_Endpoints_DeleteRunEndpoint_OlzRunId = Olz_Anniversary_Endpoints_RunEndpointTrait_OlzRunId;

export type Olz_Api_OlzTypedEndpoint780b029e7920d1ebbc5afd1dd5c75601_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_CustomRequest};

export type Olz_Api_OlzTypedEndpoint780b029e7920d1ebbc5afd1dd5c75601_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_Id = Olz_Snippets_Endpoints_GetSnippetEndpoint_OlzSnippetId;

export type Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_Data = Olz_Snippets_Endpoints_GetSnippetEndpoint_OlzSnippetData;

export type Olz_Api_OlzGetEntityTypedEndpoint0c6a4cb3f5864582bcd3972413f19ffa_CustomResponse = never;

export type Olz_Snippets_Endpoints_GetSnippetEndpoint_OlzSnippetId = Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetId;

export type Olz_Snippets_Endpoints_GetSnippetEndpoint_OlzSnippetData = Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetData;

export type Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetId = number;

export type Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetData = {'text': string, 'imageIds': Array<string>, 'fileIds': Array<string>};

export type Olz_Api_OlzTypedEndpointfc0d01153389cf8665d41bed43b284df_Request = {'id': Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_CustomRequest};

export type Olz_Api_OlzTypedEndpointfc0d01153389cf8665d41bed43b284df_Response = {'id': Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_Id = Olz_Snippets_Endpoints_EditSnippetEndpoint_OlzSnippetId;

export type Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_Data = Olz_Snippets_Endpoints_EditSnippetEndpoint_OlzSnippetData;

export type Olz_Api_OlzEditEntityTypedEndpointb3f8551cd40d3a3b9a0d5a4d8dba0da7_CustomResponse = never;

export type Olz_Snippets_Endpoints_EditSnippetEndpoint_OlzSnippetId = Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetId;

export type Olz_Snippets_Endpoints_EditSnippetEndpoint_OlzSnippetData = Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetData;

export type Olz_Api_OlzTypedEndpoint57759ba6ed8864df6ddb35deb6552f39_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_CustomRequest};

export type Olz_Api_OlzTypedEndpoint57759ba6ed8864df6ddb35deb6552f39_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_Id = Olz_Snippets_Endpoints_UpdateSnippetEndpoint_OlzSnippetId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_Data = Olz_Snippets_Endpoints_UpdateSnippetEndpoint_OlzSnippetData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint8e69c9f7b1b74597471b1899905f0e1f_CustomResponse = never;

export type Olz_Snippets_Endpoints_UpdateSnippetEndpoint_OlzSnippetId = Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetId;

export type Olz_Snippets_Endpoints_UpdateSnippetEndpoint_OlzSnippetData = Olz_Snippets_Endpoints_SnippetEndpointTrait_OlzSnippetData;

export type Olz_Api_OlzTypedEndpointbbc644cc4e246ea2a2feb3f1abf7d7af_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_CustomRequest};

export type Olz_Api_OlzTypedEndpointbbc644cc4e246ea2a2feb3f1abf7d7af_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_Data = Olz_Faq_Endpoints_CreateQuestionEndpoint_OlzQuestionData;

export type Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_Id = Olz_Faq_Endpoints_CreateQuestionEndpoint_OlzQuestionId;

export type Olz_Api_OlzCreateEntityTypedEndpointd20b8df64533ae3b1288b61ddac770fa_CustomResponse = never;

export type Olz_Faq_Endpoints_CreateQuestionEndpoint_OlzQuestionData = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionData;

export type Olz_Faq_Endpoints_CreateQuestionEndpoint_OlzQuestionId = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionId;

export type Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionData = {'ident': string, 'question': string, 'categoryId'?: (number | null), 'positionWithinCategory'?: (number | null), 'answer': string, 'imageIds': Array<string>, 'fileIds': Array<string>};

export type Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionId = number;

export type Olz_Api_OlzTypedEndpoint6cabc3aa69c0795ddd97b544305b8f28_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_CustomRequest};

export type Olz_Api_OlzTypedEndpoint6cabc3aa69c0795ddd97b544305b8f28_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_Id = Olz_Faq_Endpoints_GetQuestionEndpoint_OlzQuestionId;

export type Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_Data = Olz_Faq_Endpoints_GetQuestionEndpoint_OlzQuestionData;

export type Olz_Api_OlzGetEntityTypedEndpoint2b6128d4dd8a020656353ad14311a5b0_CustomResponse = never;

export type Olz_Faq_Endpoints_GetQuestionEndpoint_OlzQuestionId = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionId;

export type Olz_Faq_Endpoints_GetQuestionEndpoint_OlzQuestionData = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionData;

export type Olz_Api_OlzTypedEndpoint3c2e860b1e4ab5caff4ecac65d87289e_Request = {'id': Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_CustomRequest};

export type Olz_Api_OlzTypedEndpoint3c2e860b1e4ab5caff4ecac65d87289e_Response = {'id': Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_Id = Olz_Faq_Endpoints_EditQuestionEndpoint_OlzQuestionId;

export type Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_Data = Olz_Faq_Endpoints_EditQuestionEndpoint_OlzQuestionData;

export type Olz_Api_OlzEditEntityTypedEndpointf28955c83d97286583d7342235508d92_CustomResponse = never;

export type Olz_Faq_Endpoints_EditQuestionEndpoint_OlzQuestionId = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionId;

export type Olz_Faq_Endpoints_EditQuestionEndpoint_OlzQuestionData = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionData;

export type Olz_Api_OlzTypedEndpoint3f496fad070242433abaec2ffaef1e83_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_CustomRequest};

export type Olz_Api_OlzTypedEndpoint3f496fad070242433abaec2ffaef1e83_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_Id = Olz_Faq_Endpoints_UpdateQuestionEndpoint_OlzQuestionId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_Data = Olz_Faq_Endpoints_UpdateQuestionEndpoint_OlzQuestionData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint73ad222138dda23757a1ba6b859f85bf_CustomResponse = never;

export type Olz_Faq_Endpoints_UpdateQuestionEndpoint_OlzQuestionId = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionId;

export type Olz_Faq_Endpoints_UpdateQuestionEndpoint_OlzQuestionData = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionData;

export type Olz_Api_OlzTypedEndpointb6d91eff354e824535a5add156018e90_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpointf008672f878cc66ae77c5ef62da669ef_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointf008672f878cc66ae77c5ef62da669ef_CustomRequest};

export type Olz_Api_OlzTypedEndpointb6d91eff354e824535a5add156018e90_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointf008672f878cc66ae77c5ef62da669ef_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpointf008672f878cc66ae77c5ef62da669ef_Id = Olz_Faq_Endpoints_DeleteQuestionEndpoint_OlzQuestionId;

export type Olz_Api_OlzDeleteEntityTypedEndpointf008672f878cc66ae77c5ef62da669ef_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpointf008672f878cc66ae77c5ef62da669ef_CustomResponse = never;

export type Olz_Faq_Endpoints_DeleteQuestionEndpoint_OlzQuestionId = Olz_Faq_Endpoints_QuestionEndpointTrait_OlzQuestionId;

export type Olz_Api_OlzTypedEndpoint0d3074367e001f884d7c5f73435133a5_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_CustomRequest};

export type Olz_Api_OlzTypedEndpoint0d3074367e001f884d7c5f73435133a5_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_Data = Olz_Faq_Endpoints_CreateQuestionCategoryEndpoint_OlzQuestionCategoryData;

export type Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_Id = Olz_Faq_Endpoints_CreateQuestionCategoryEndpoint_OlzQuestionCategoryId;

export type Olz_Api_OlzCreateEntityTypedEndpoint923d0d1212549fcb398f6dccc73ae534_CustomResponse = never;

export type Olz_Faq_Endpoints_CreateQuestionCategoryEndpoint_OlzQuestionCategoryData = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryData;

export type Olz_Faq_Endpoints_CreateQuestionCategoryEndpoint_OlzQuestionCategoryId = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryId;

export type Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryData = {'position': number, 'name': string};

export type Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryId = number;

export type Olz_Api_OlzTypedEndpoint071fc36b0ee24f6749c16f7d4a322965_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_CustomRequest};

export type Olz_Api_OlzTypedEndpoint071fc36b0ee24f6749c16f7d4a322965_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_Id = Olz_Faq_Endpoints_GetQuestionCategoryEndpoint_OlzQuestionCategoryId;

export type Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_Data = Olz_Faq_Endpoints_GetQuestionCategoryEndpoint_OlzQuestionCategoryData;

export type Olz_Api_OlzGetEntityTypedEndpoint0da4ad5606bfc2d2548ff5f87c0ebb67_CustomResponse = never;

export type Olz_Faq_Endpoints_GetQuestionCategoryEndpoint_OlzQuestionCategoryId = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryId;

export type Olz_Faq_Endpoints_GetQuestionCategoryEndpoint_OlzQuestionCategoryData = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryData;

export type Olz_Api_OlzTypedEndpoint00aeff7ff6865019a41d9bfe3f18743c_Request = {'id': Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_CustomRequest};

export type Olz_Api_OlzTypedEndpoint00aeff7ff6865019a41d9bfe3f18743c_Response = {'id': Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_Id = Olz_Faq_Endpoints_EditQuestionCategoryEndpoint_OlzQuestionCategoryId;

export type Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_Data = Olz_Faq_Endpoints_EditQuestionCategoryEndpoint_OlzQuestionCategoryData;

export type Olz_Api_OlzEditEntityTypedEndpointae88a8fcd138fb58d6b7c45f1c5506cb_CustomResponse = never;

export type Olz_Faq_Endpoints_EditQuestionCategoryEndpoint_OlzQuestionCategoryId = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryId;

export type Olz_Faq_Endpoints_EditQuestionCategoryEndpoint_OlzQuestionCategoryData = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryData;

export type Olz_Api_OlzTypedEndpoint36058a8dfa05aa16d09dba72106832ed_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_CustomRequest};

export type Olz_Api_OlzTypedEndpoint36058a8dfa05aa16d09dba72106832ed_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_Id = Olz_Faq_Endpoints_UpdateQuestionCategoryEndpoint_OlzQuestionCategoryId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_Data = Olz_Faq_Endpoints_UpdateQuestionCategoryEndpoint_OlzQuestionCategoryData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7a47e7a48429b1840a99bcb11250d688_CustomResponse = never;

export type Olz_Faq_Endpoints_UpdateQuestionCategoryEndpoint_OlzQuestionCategoryId = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryId;

export type Olz_Faq_Endpoints_UpdateQuestionCategoryEndpoint_OlzQuestionCategoryData = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryData;

export type Olz_Api_OlzTypedEndpointf405714c333dde94eda1a92e90d4b01d_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpointd0171f1ade0e63f0518b74e77eaf5d37_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointd0171f1ade0e63f0518b74e77eaf5d37_CustomRequest};

export type Olz_Api_OlzTypedEndpointf405714c333dde94eda1a92e90d4b01d_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointd0171f1ade0e63f0518b74e77eaf5d37_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpointd0171f1ade0e63f0518b74e77eaf5d37_Id = Olz_Faq_Endpoints_DeleteQuestionCategoryEndpoint_OlzQuestionCategoryId;

export type Olz_Api_OlzDeleteEntityTypedEndpointd0171f1ade0e63f0518b74e77eaf5d37_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpointd0171f1ade0e63f0518b74e77eaf5d37_CustomResponse = never;

export type Olz_Faq_Endpoints_DeleteQuestionCategoryEndpoint_OlzQuestionCategoryId = Olz_Faq_Endpoints_QuestionCategoryEndpointTrait_OlzQuestionCategoryId;

export type Olz_Api_OlzTypedEndpoint2674d12f2366436f95d4daf04411206c_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_CustomRequest};

export type Olz_Api_OlzTypedEndpoint2674d12f2366436f95d4daf04411206c_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_Data = Olz_Startseite_Endpoints_CreateWeeklyPictureEndpoint_OlzWeeklyPictureData;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_Id = Olz_Startseite_Endpoints_CreateWeeklyPictureEndpoint_OlzWeeklyPictureId;

export type Olz_Api_OlzCreateEntityTypedEndpoint3fdb0e2c34224fe8dde6ab16dbedb828_CustomResponse = never;

export type Olz_Startseite_Endpoints_CreateWeeklyPictureEndpoint_OlzWeeklyPictureData = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureData;

export type Olz_Startseite_Endpoints_CreateWeeklyPictureEndpoint_OlzWeeklyPictureId = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureId;

export type Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureData = {'text': string, 'imageId': string, 'publishedDate'?: (PhpTypeScriptApi_PhpStan_IsoDate | null)};

export type Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureId = number;

export type PhpTypeScriptApi_PhpStan_IsoDate = string;

export type Olz_Api_OlzTypedEndpoint88ccfa34761b0d165792fe27c42044e9_Request = {'id': Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_CustomRequest};

export type Olz_Api_OlzTypedEndpoint88ccfa34761b0d165792fe27c42044e9_Response = {'id': Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_Id = Olz_Startseite_Endpoints_GetWeeklyPictureEndpoint_OlzWeeklyPictureId;

export type Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_Data = Olz_Startseite_Endpoints_GetWeeklyPictureEndpoint_OlzWeeklyPictureData;

export type Olz_Api_OlzGetEntityTypedEndpointf4253169028c9841b02cdbd47d4ae741_CustomResponse = never;

export type Olz_Startseite_Endpoints_GetWeeklyPictureEndpoint_OlzWeeklyPictureId = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureId;

export type Olz_Startseite_Endpoints_GetWeeklyPictureEndpoint_OlzWeeklyPictureData = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureData;

export type Olz_Api_OlzTypedEndpointe561bab850c7a74c97e08246fdd9b22a_Request = {'id': Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_CustomRequest};

export type Olz_Api_OlzTypedEndpointe561bab850c7a74c97e08246fdd9b22a_Response = {'id': Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_Id = Olz_Startseite_Endpoints_EditWeeklyPictureEndpoint_OlzWeeklyPictureId;

export type Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_Data = Olz_Startseite_Endpoints_EditWeeklyPictureEndpoint_OlzWeeklyPictureData;

export type Olz_Api_OlzEditEntityTypedEndpointd46319b104900f31b15c1b8b0c4a7f90_CustomResponse = never;

export type Olz_Startseite_Endpoints_EditWeeklyPictureEndpoint_OlzWeeklyPictureId = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureId;

export type Olz_Startseite_Endpoints_EditWeeklyPictureEndpoint_OlzWeeklyPictureData = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureData;

export type Olz_Api_OlzTypedEndpoint08b0e35d1bf64f83234f36f093bcd798_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_CustomRequest};

export type Olz_Api_OlzTypedEndpoint08b0e35d1bf64f83234f36f093bcd798_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_Id = Olz_Startseite_Endpoints_UpdateWeeklyPictureEndpoint_OlzWeeklyPictureId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_Data = Olz_Startseite_Endpoints_UpdateWeeklyPictureEndpoint_OlzWeeklyPictureData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint7284b2ae285e03c1aba1a941e17d2141_CustomResponse = never;

export type Olz_Startseite_Endpoints_UpdateWeeklyPictureEndpoint_OlzWeeklyPictureId = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureId;

export type Olz_Startseite_Endpoints_UpdateWeeklyPictureEndpoint_OlzWeeklyPictureData = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureData;

export type Olz_Api_OlzTypedEndpoint87b27e0fdd6b77c480d1728ae89e8863_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpointacb9fe3b881dc9391944c716beb4d36f_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointacb9fe3b881dc9391944c716beb4d36f_CustomRequest};

export type Olz_Api_OlzTypedEndpoint87b27e0fdd6b77c480d1728ae89e8863_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointacb9fe3b881dc9391944c716beb4d36f_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpointacb9fe3b881dc9391944c716beb4d36f_Id = Olz_Startseite_Endpoints_DeleteWeeklyPictureEndpoint_OlzWeeklyPictureId;

export type Olz_Api_OlzDeleteEntityTypedEndpointacb9fe3b881dc9391944c716beb4d36f_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpointacb9fe3b881dc9391944c716beb4d36f_CustomResponse = never;

export type Olz_Startseite_Endpoints_DeleteWeeklyPictureEndpoint_OlzWeeklyPictureId = Olz_Startseite_Endpoints_WeeklyPictureEndpointTrait_OlzWeeklyPictureId;

export type Olz_Api_OlzTypedEndpoint88ad41d096980d2d11b75722418e39af_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_CustomRequest};

export type Olz_Api_OlzTypedEndpoint88ad41d096980d2d11b75722418e39af_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_Data = Olz_Termine_Endpoints_CreateTerminEndpoint_OlzTerminData;

export type Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_Id = Olz_Termine_Endpoints_CreateTerminEndpoint_OlzTerminId;

export type Olz_Api_OlzCreateEntityTypedEndpoint6d4a259617742f8422319bb31597886c_CustomResponse = never;

export type Olz_Termine_Endpoints_CreateTerminEndpoint_OlzTerminData = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminData;

export type Olz_Termine_Endpoints_CreateTerminEndpoint_OlzTerminId = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminId;

export type Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminData = {'fromTemplateId'?: (number | null), 'startDate'?: (PhpTypeScriptApi_PhpStan_IsoDate | null), 'startTime'?: (PhpTypeScriptApi_PhpStan_IsoTime | null), 'endDate'?: (PhpTypeScriptApi_PhpStan_IsoDate | null), 'endTime'?: (PhpTypeScriptApi_PhpStan_IsoTime | null), 'title'?: (string | null), 'text': string, 'organizerUserId': (number | null), 'deadline'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'shouldPromote': boolean, 'newsletter': boolean, 'solvId'?: (number | null), 'go2olId'?: (string | null), 'types': Array<string>, 'locationId'?: (number | null), 'coordinateX'?: (number | null), 'coordinateY'?: (number | null), 'imageIds': Array<string>, 'fileIds': Array<string>};

export type Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminId = number;

export type PhpTypeScriptApi_PhpStan_IsoTime = string;

export type Olz_Api_OlzTypedEndpoint4455200f95effde91a87d232e9d2df9c_Request = {'id': Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_CustomRequest};

export type Olz_Api_OlzTypedEndpoint4455200f95effde91a87d232e9d2df9c_Response = {'id': Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_Id = Olz_Termine_Endpoints_GetTerminEndpoint_OlzTerminId;

export type Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_Data = Olz_Termine_Endpoints_GetTerminEndpoint_OlzTerminData;

export type Olz_Api_OlzGetEntityTypedEndpointeb2c48c20933f9c7cbec66ac99b7b823_CustomResponse = never;

export type Olz_Termine_Endpoints_GetTerminEndpoint_OlzTerminId = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminId;

export type Olz_Termine_Endpoints_GetTerminEndpoint_OlzTerminData = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminData;

export type Olz_Api_OlzTypedEndpoint444d1d5a251edf092840b6d4a8042bd5_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_CustomRequest};

export type Olz_Api_OlzTypedEndpoint444d1d5a251edf092840b6d4a8042bd5_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_Id = Olz_Termine_Endpoints_EditTerminEndpoint_OlzTerminId;

export type Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_Data = Olz_Termine_Endpoints_EditTerminEndpoint_OlzTerminData;

export type Olz_Api_OlzEditEntityTypedEndpoint3f4c6c7c335dbabcf516800a533ffa20_CustomResponse = never;

export type Olz_Termine_Endpoints_EditTerminEndpoint_OlzTerminId = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminId;

export type Olz_Termine_Endpoints_EditTerminEndpoint_OlzTerminData = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminData;

export type Olz_Api_OlzTypedEndpointde8bfc4d144ea939be23aab4dacd4467_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_CustomRequest};

export type Olz_Api_OlzTypedEndpointde8bfc4d144ea939be23aab4dacd4467_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_Id = Olz_Termine_Endpoints_UpdateTerminEndpoint_OlzTerminId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_Data = Olz_Termine_Endpoints_UpdateTerminEndpoint_OlzTerminData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint827424d32f382c2532e5b12d3a254417_CustomResponse = never;

export type Olz_Termine_Endpoints_UpdateTerminEndpoint_OlzTerminId = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminId;

export type Olz_Termine_Endpoints_UpdateTerminEndpoint_OlzTerminData = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminData;

export type Olz_Api_OlzTypedEndpointb3fe6adefad0c99cd5d2f6d0d13a9852_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpointfb0c3beb8255130c5e6b518b9fe87c65_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointfb0c3beb8255130c5e6b518b9fe87c65_CustomRequest};

export type Olz_Api_OlzTypedEndpointb3fe6adefad0c99cd5d2f6d0d13a9852_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointfb0c3beb8255130c5e6b518b9fe87c65_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpointfb0c3beb8255130c5e6b518b9fe87c65_Id = Olz_Termine_Endpoints_DeleteTerminEndpoint_OlzTerminId;

export type Olz_Api_OlzDeleteEntityTypedEndpointfb0c3beb8255130c5e6b518b9fe87c65_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpointfb0c3beb8255130c5e6b518b9fe87c65_CustomResponse = never;

export type Olz_Termine_Endpoints_DeleteTerminEndpoint_OlzTerminId = Olz_Termine_Endpoints_TerminEndpointTrait_OlzTerminId;

export type Olz_Api_OlzTypedEndpoint73faca57780e0d82c43937d3db325082_Request = {'filter': Olz_Termine_Endpoints_ListTerminReactionsEndpoint_OlzTerminReactionFilter};

export type Olz_Api_OlzTypedEndpoint73faca57780e0d82c43937d3db325082_Response = {'result': Array<Olz_Termine_Endpoints_ListTerminReactionsEndpoint_OlzReaction>};

export type Olz_Termine_Endpoints_ListTerminReactionsEndpoint_OlzTerminReactionFilter = {'terminId': number};

export type Olz_Termine_Endpoints_ListTerminReactionsEndpoint_OlzReaction = {'userId': number, 'name': (string | null), 'emoji': string};

export type Olz_Api_OlzTypedEndpointb2ff38c5eaa06dd6feeb2919a3e369a1_Request = {'terminId': number, 'emoji': string, 'action': ('on' | 'off' | 'toggle')};

export type Olz_Api_OlzTypedEndpointb2ff38c5eaa06dd6feeb2919a3e369a1_Response = {'result': (Olz_Termine_Endpoints_ToggleTerminReactionEndpoint_OlzReaction | null)};

export type Olz_Termine_Endpoints_ToggleTerminReactionEndpoint_OlzReaction = Olz_Termine_Endpoints_ListTerminReactionsEndpoint_OlzReaction;

export type Olz_Api_OlzTypedEndpointdf7751ad58812e0aea6158f1c6a8eab1_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_CustomRequest};

export type Olz_Api_OlzTypedEndpointdf7751ad58812e0aea6158f1c6a8eab1_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_Data = Olz_Termine_Endpoints_CreateTerminLabelEndpoint_OlzTerminLabelData;

export type Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_Id = Olz_Termine_Endpoints_CreateTerminLabelEndpoint_OlzTerminLabelId;

export type Olz_Api_OlzCreateEntityTypedEndpoint4583018f20bc8bb0fefcc8c8c9d0d6f1_CustomResponse = never;

export type Olz_Termine_Endpoints_CreateTerminLabelEndpoint_OlzTerminLabelData = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData;

export type Olz_Termine_Endpoints_CreateTerminLabelEndpoint_OlzTerminLabelId = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId;

export type Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData = {'ident': string, 'name': string, 'details': string, 'icon'?: (string | null), 'position'?: (number | null), 'imageIds': Array<string>, 'fileIds': Array<string>};

export type Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId = number;

export type Olz_Api_OlzTypedEndpointccb48a140bb788e43280a406985861f2_Request = {'custom'?: Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_CustomRequest};

export type Olz_Api_OlzTypedEndpointccb48a140bb788e43280a406985861f2_Response = {'items': Array<{'id': Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_Id, 'meta': Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_OlzMetaData, 'data': Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_Data, 'custom'?: Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_CustomItem}>, 'custom'?: Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_CustomResponse};

export type Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_CustomRequest = never;

export type Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_Id = Olz_Termine_Endpoints_ListTerminLabelsEndpoint_OlzTerminLabelId;

export type Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_Data = Olz_Termine_Endpoints_ListTerminLabelsEndpoint_OlzTerminLabelData;

export type Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_CustomItem = never;

export type Olz_Api_OlzListEntitiesTypedEndpoint0bb30ab12e032b63337ecacfa00e51f6_CustomResponse = never;

export type Olz_Termine_Endpoints_ListTerminLabelsEndpoint_OlzTerminLabelId = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId;

export type Olz_Termine_Endpoints_ListTerminLabelsEndpoint_OlzTerminLabelData = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData;

export type Olz_Api_OlzTypedEndpoint04827aea927649ecd854fdff9f2dd24b_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_CustomRequest};

export type Olz_Api_OlzTypedEndpoint04827aea927649ecd854fdff9f2dd24b_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_Id = Olz_Termine_Endpoints_GetTerminLabelEndpoint_OlzTerminLabelId;

export type Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_Data = Olz_Termine_Endpoints_GetTerminLabelEndpoint_OlzTerminLabelData;

export type Olz_Api_OlzGetEntityTypedEndpoint1022e4257a69ed6f9479d877c1cc0ba1_CustomResponse = never;

export type Olz_Termine_Endpoints_GetTerminLabelEndpoint_OlzTerminLabelId = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId;

export type Olz_Termine_Endpoints_GetTerminLabelEndpoint_OlzTerminLabelData = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData;

export type Olz_Api_OlzTypedEndpoint0d3536f5ad94d18007202ad91a395315_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_CustomRequest};

export type Olz_Api_OlzTypedEndpoint0d3536f5ad94d18007202ad91a395315_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_Id = Olz_Termine_Endpoints_EditTerminLabelEndpoint_OlzTerminLabelId;

export type Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_Data = Olz_Termine_Endpoints_EditTerminLabelEndpoint_OlzTerminLabelData;

export type Olz_Api_OlzEditEntityTypedEndpoint7704423d4d03903b1ef65575906863fb_CustomResponse = never;

export type Olz_Termine_Endpoints_EditTerminLabelEndpoint_OlzTerminLabelId = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId;

export type Olz_Termine_Endpoints_EditTerminLabelEndpoint_OlzTerminLabelData = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData;

export type Olz_Api_OlzTypedEndpointcb88a6855844d3e4c9be3d579506f138_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_CustomRequest};

export type Olz_Api_OlzTypedEndpointcb88a6855844d3e4c9be3d579506f138_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_Id = Olz_Termine_Endpoints_UpdateTerminLabelEndpoint_OlzTerminLabelId;

export type Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_Data = Olz_Termine_Endpoints_UpdateTerminLabelEndpoint_OlzTerminLabelData;

export type Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpointa8fbc42848d7691d4a45ec4ab84a760a_CustomResponse = never;

export type Olz_Termine_Endpoints_UpdateTerminLabelEndpoint_OlzTerminLabelId = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId;

export type Olz_Termine_Endpoints_UpdateTerminLabelEndpoint_OlzTerminLabelData = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelData;

export type Olz_Api_OlzTypedEndpointe0d943d9f6f32a6313e19de5816140e7_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint3f987a8e916ee19749aec9e8d01d7b63_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint3f987a8e916ee19749aec9e8d01d7b63_CustomRequest};

export type Olz_Api_OlzTypedEndpointe0d943d9f6f32a6313e19de5816140e7_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint3f987a8e916ee19749aec9e8d01d7b63_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint3f987a8e916ee19749aec9e8d01d7b63_Id = Olz_Termine_Endpoints_DeleteTerminLabelEndpoint_OlzTerminLabelId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint3f987a8e916ee19749aec9e8d01d7b63_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint3f987a8e916ee19749aec9e8d01d7b63_CustomResponse = never;

export type Olz_Termine_Endpoints_DeleteTerminLabelEndpoint_OlzTerminLabelId = Olz_Termine_Endpoints_TerminLabelEndpointTrait_OlzTerminLabelId;

export type Olz_Api_OlzTypedEndpoint47ec4ae1e51437bfe770286ed788d421_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_CustomRequest};

export type Olz_Api_OlzTypedEndpoint47ec4ae1e51437bfe770286ed788d421_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_Data = Olz_Termine_Endpoints_CreateTerminLocationEndpoint_OlzTerminLocationData;

export type Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_Id = Olz_Termine_Endpoints_CreateTerminLocationEndpoint_OlzTerminLocationId;

export type Olz_Api_OlzCreateEntityTypedEndpoint2e3e83e88b155ce1d8ac48145c5d3ddd_CustomResponse = never;

export type Olz_Termine_Endpoints_CreateTerminLocationEndpoint_OlzTerminLocationData = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationData;

export type Olz_Termine_Endpoints_CreateTerminLocationEndpoint_OlzTerminLocationId = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationId;

export type Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationData = {'name': string, 'details': string, 'location': Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzLocationCoordinates, 'imageIds': Array<string>};

export type Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationId = number;

export type Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzLocationCoordinates = Olz_Utils_MapUtils_OlzLocationCoordinates;

export type Olz_Api_OlzTypedEndpoint3df57566388159e570e908eca0bdbe86_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_CustomRequest};

export type Olz_Api_OlzTypedEndpoint3df57566388159e570e908eca0bdbe86_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_Id = Olz_Termine_Endpoints_GetTerminLocationEndpoint_OlzTerminLocationId;

export type Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_Data = Olz_Termine_Endpoints_GetTerminLocationEndpoint_OlzTerminLocationData;

export type Olz_Api_OlzGetEntityTypedEndpoint1a1e55388407b8507da2d323e4886deb_CustomResponse = never;

export type Olz_Termine_Endpoints_GetTerminLocationEndpoint_OlzTerminLocationId = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationId;

export type Olz_Termine_Endpoints_GetTerminLocationEndpoint_OlzTerminLocationData = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationData;

export type Olz_Api_OlzTypedEndpointd2420c029bcbc765d8b3c78f0047dfba_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_CustomRequest};

export type Olz_Api_OlzTypedEndpointd2420c029bcbc765d8b3c78f0047dfba_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_Id = Olz_Termine_Endpoints_EditTerminLocationEndpoint_OlzTerminLocationId;

export type Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_Data = Olz_Termine_Endpoints_EditTerminLocationEndpoint_OlzTerminLocationData;

export type Olz_Api_OlzEditEntityTypedEndpoint324ed9cd6bd0ac43e4f02deb3b7bad3f_CustomResponse = never;

export type Olz_Termine_Endpoints_EditTerminLocationEndpoint_OlzTerminLocationId = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationId;

export type Olz_Termine_Endpoints_EditTerminLocationEndpoint_OlzTerminLocationData = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationData;

export type Olz_Api_OlzTypedEndpointe8c3eb41794ce5b782001285a3eb5963_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_CustomRequest};

export type Olz_Api_OlzTypedEndpointe8c3eb41794ce5b782001285a3eb5963_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_Id = Olz_Termine_Endpoints_UpdateTerminLocationEndpoint_OlzTerminLocationId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_Data = Olz_Termine_Endpoints_UpdateTerminLocationEndpoint_OlzTerminLocationData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint2037671d0fa615b90b2b709f732ee8e2_CustomResponse = never;

export type Olz_Termine_Endpoints_UpdateTerminLocationEndpoint_OlzTerminLocationId = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationId;

export type Olz_Termine_Endpoints_UpdateTerminLocationEndpoint_OlzTerminLocationData = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationData;

export type Olz_Api_OlzTypedEndpoint3c354446449a4d6baa829ba754dd14a9_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint57e7060fdf5ee3b50acdd8fd5c330185_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint57e7060fdf5ee3b50acdd8fd5c330185_CustomRequest};

export type Olz_Api_OlzTypedEndpoint3c354446449a4d6baa829ba754dd14a9_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint57e7060fdf5ee3b50acdd8fd5c330185_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint57e7060fdf5ee3b50acdd8fd5c330185_Id = Olz_Termine_Endpoints_DeleteTerminLocationEndpoint_OlzTerminLocationId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint57e7060fdf5ee3b50acdd8fd5c330185_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint57e7060fdf5ee3b50acdd8fd5c330185_CustomResponse = never;

export type Olz_Termine_Endpoints_DeleteTerminLocationEndpoint_OlzTerminLocationId = Olz_Termine_Endpoints_TerminLocationEndpointTrait_OlzTerminLocationId;

export type Olz_Api_OlzTypedEndpoint0d736205261d0f493a89787987f13559_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_CustomRequest};

export type Olz_Api_OlzTypedEndpoint0d736205261d0f493a89787987f13559_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_Data = Olz_Termine_Endpoints_CreateTerminTemplateEndpoint_OlzTerminTemplateData;

export type Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_Id = Olz_Termine_Endpoints_CreateTerminTemplateEndpoint_OlzTerminTemplateId;

export type Olz_Api_OlzCreateEntityTypedEndpointa0d80c786e5bbdb8b19634df82467273_CustomResponse = never;

export type Olz_Termine_Endpoints_CreateTerminTemplateEndpoint_OlzTerminTemplateData = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateData;

export type Olz_Termine_Endpoints_CreateTerminTemplateEndpoint_OlzTerminTemplateId = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateId;

export type Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateData = {'startTime'?: (PhpTypeScriptApi_PhpStan_IsoTime | null), 'durationSeconds'?: (number | null), 'title': string, 'text': string, 'organizerUserId': (number | null), 'deadlineEarlierSeconds'?: (number | null), 'deadlineTime'?: (PhpTypeScriptApi_PhpStan_IsoTime | null), 'shouldPromote': boolean, 'newsletter': boolean, 'types': Array<string>, 'locationId'?: (number | null), 'imageIds': Array<string>, 'fileIds': Array<string>};

export type Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateId = number;

export type Olz_Api_OlzTypedEndpoint04f5859ab1af98a9d7a9daffe145cb98_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_CustomRequest};

export type Olz_Api_OlzTypedEndpoint04f5859ab1af98a9d7a9daffe145cb98_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_Id = Olz_Termine_Endpoints_GetTerminTemplateEndpoint_OlzTerminTemplateId;

export type Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_Data = Olz_Termine_Endpoints_GetTerminTemplateEndpoint_OlzTerminTemplateData;

export type Olz_Api_OlzGetEntityTypedEndpoint3d1d1c3b57baa87549682fd15ec9794e_CustomResponse = never;

export type Olz_Termine_Endpoints_GetTerminTemplateEndpoint_OlzTerminTemplateId = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateId;

export type Olz_Termine_Endpoints_GetTerminTemplateEndpoint_OlzTerminTemplateData = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateData;

export type Olz_Api_OlzTypedEndpointfcbc47c08732202358a60c9ea485ae5c_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_CustomRequest};

export type Olz_Api_OlzTypedEndpointfcbc47c08732202358a60c9ea485ae5c_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_Id = Olz_Termine_Endpoints_EditTerminTemplateEndpoint_OlzTerminTemplateId;

export type Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_Data = Olz_Termine_Endpoints_EditTerminTemplateEndpoint_OlzTerminTemplateData;

export type Olz_Api_OlzEditEntityTypedEndpoint8fd1a420d2b1d4e86f79330c730d50d7_CustomResponse = never;

export type Olz_Termine_Endpoints_EditTerminTemplateEndpoint_OlzTerminTemplateId = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateId;

export type Olz_Termine_Endpoints_EditTerminTemplateEndpoint_OlzTerminTemplateData = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateData;

export type Olz_Api_OlzTypedEndpoint2a445382fc2735e084e666300e02ea86_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_CustomRequest};

export type Olz_Api_OlzTypedEndpoint2a445382fc2735e084e666300e02ea86_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_Id = Olz_Termine_Endpoints_UpdateTerminTemplateEndpoint_OlzTerminTemplateId;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_Data = Olz_Termine_Endpoints_UpdateTerminTemplateEndpoint_OlzTerminTemplateData;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpoint0c80311f9a4d65c910ee31077802d2e3_CustomResponse = never;

export type Olz_Termine_Endpoints_UpdateTerminTemplateEndpoint_OlzTerminTemplateId = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateId;

export type Olz_Termine_Endpoints_UpdateTerminTemplateEndpoint_OlzTerminTemplateData = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateData;

export type Olz_Api_OlzTypedEndpoint765e16f72db7842d676f93fd840c22b1_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpointb3e91e02950d3248d8b885a73c6282da_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointb3e91e02950d3248d8b885a73c6282da_CustomRequest};

export type Olz_Api_OlzTypedEndpoint765e16f72db7842d676f93fd840c22b1_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpointb3e91e02950d3248d8b885a73c6282da_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpointb3e91e02950d3248d8b885a73c6282da_Id = Olz_Termine_Endpoints_DeleteTerminTemplateEndpoint_OlzTerminTemplateId;

export type Olz_Api_OlzDeleteEntityTypedEndpointb3e91e02950d3248d8b885a73c6282da_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpointb3e91e02950d3248d8b885a73c6282da_CustomResponse = never;

export type Olz_Termine_Endpoints_DeleteTerminTemplateEndpoint_OlzTerminTemplateId = Olz_Termine_Endpoints_TerminTemplateEndpointTrait_OlzTerminTemplateId;

export type Olz_Api_OlzTypedEndpoint5f141593eecf60875007b28b989fbeca_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_CustomRequest};

export type Olz_Api_OlzTypedEndpoint5f141593eecf60875007b28b989fbeca_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_Data = Olz_Users_Endpoints_CreateUserEndpoint_OlzUserData;

export type Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_CustomRequest = {'captchaToken'?: (string | null)};

export type Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_Id = Olz_Users_Endpoints_CreateUserEndpoint_OlzUserId;

export type Olz_Api_OlzCreateEntityTypedEndpoint618a6c61daa24b57dc7923d2e7002210_CustomResponse = {'status': ('OK' | 'OK_NO_EMAIL_VERIFICATION' | 'DENIED' | 'ERROR')};

export type Olz_Users_Endpoints_CreateUserEndpoint_OlzUserData = Olz_Users_Endpoints_UserEndpointTrait_OlzUserData;

export type Olz_Users_Endpoints_CreateUserEndpoint_OlzUserId = Olz_Users_Endpoints_UserEndpointTrait_OlzUserId;

export type Olz_Users_Endpoints_UserEndpointTrait_OlzUserData = {'parentUserId'?: (number | null), 'firstName': string, 'lastName': string, 'username': string, 'password'?: (string | null), 'email'?: (string | null), 'phone'?: (string | null), 'gender'?: (('M' | 'F' | 'O') | null), 'birthdate'?: (PhpTypeScriptApi_PhpStan_IsoDate | null), 'street'?: (string | null), 'postalCode'?: (string | null), 'city'?: (string | null), 'region'?: (string | null), 'countryCode'?: (Olz_Api_ApiObjects_IsoCountry | null), 'siCardNumber'?: (number | null), 'solvNumber'?: (string | null), 'ahvNumber'?: (string | null), 'dressSize'?: (string | null), 'avatarImageId'?: (string | null)};

export type Olz_Users_Endpoints_UserEndpointTrait_OlzUserId = number;

export type Olz_Api_ApiObjects_IsoCountry = string;

export type Olz_Api_OlzTypedEndpointa10faf7e5e15659c29bf2dd67639145c_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_CustomRequest};

export type Olz_Api_OlzTypedEndpointa10faf7e5e15659c29bf2dd67639145c_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_Id = Olz_Users_Endpoints_GetUserEndpoint_OlzUserId;

export type Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_Data = Olz_Users_Endpoints_GetUserEndpoint_OlzUserData;

export type Olz_Api_OlzGetEntityTypedEndpoint33f6b415d98ed77eb7937ab05c3dbf91_CustomResponse = never;

export type Olz_Users_Endpoints_GetUserEndpoint_OlzUserId = Olz_Users_Endpoints_UserEndpointTrait_OlzUserId;

export type Olz_Users_Endpoints_GetUserEndpoint_OlzUserData = Olz_Users_Endpoints_UserEndpointTrait_OlzUserData;

export type Olz_Api_OlzTypedEndpoint78c85b822da108f4e189423517b8fc0c_Request = {'id': Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_Id, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_CustomRequest};

export type Olz_Api_OlzTypedEndpoint78c85b822da108f4e189423517b8fc0c_Response = {'id': Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_Id, 'meta': Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_OlzMetaData, 'data': Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_Data, 'custom'?: Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_CustomResponse};

export type Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_Id = Olz_Users_Endpoints_EditUserEndpoint_OlzUserId;

export type Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_CustomRequest = never;

export type Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_Data = Olz_Users_Endpoints_EditUserEndpoint_OlzUserData;

export type Olz_Api_OlzEditEntityTypedEndpoint0973283491e296f59f91c71d7c12d5d4_CustomResponse = never;

export type Olz_Users_Endpoints_EditUserEndpoint_OlzUserId = Olz_Users_Endpoints_UserEndpointTrait_OlzUserId;

export type Olz_Users_Endpoints_EditUserEndpoint_OlzUserData = Olz_Users_Endpoints_UserEndpointTrait_OlzUserData;

export type Olz_Api_OlzTypedEndpoint4d89e85018426dc4142157a11ae464f1_Request = {'id': Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_Id, 'meta': Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_OlzMetaData, 'data': Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_Data, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_CustomRequest};

export type Olz_Api_OlzTypedEndpoint4d89e85018426dc4142157a11ae464f1_Response = {'id': Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_Id, 'custom'?: Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_CustomResponse};

export type Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_Id = Olz_Users_Endpoints_UpdateUserEndpoint_OlzUserId;

export type Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_Data = Olz_Users_Endpoints_UpdateUserEndpoint_OlzUserData;

export type Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_CustomRequest = never;

export type Olz_Api_OlzUpdateEntityTypedEndpointaa9e40a874f4b40045848ce9e835738f_CustomResponse = {'status': ('OK' | 'OK_NO_EMAIL_VERIFICATION' | 'DENIED' | 'ERROR')};

export type Olz_Users_Endpoints_UpdateUserEndpoint_OlzUserId = Olz_Users_Endpoints_UserEndpointTrait_OlzUserId;

export type Olz_Users_Endpoints_UpdateUserEndpoint_OlzUserData = Olz_Users_Endpoints_UserEndpointTrait_OlzUserData;

export type Olz_Api_OlzTypedEndpoint08e0d27bf8fd7b3910a2fa4f1da158d2_Request = {'id': Olz_Api_OlzDeleteEntityTypedEndpoint11a6699569b3d4dea94a33ec63c916cb_Id, 'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint11a6699569b3d4dea94a33ec63c916cb_CustomRequest};

export type Olz_Api_OlzTypedEndpoint08e0d27bf8fd7b3910a2fa4f1da158d2_Response = {'custom'?: Olz_Api_OlzDeleteEntityTypedEndpoint11a6699569b3d4dea94a33ec63c916cb_CustomResponse};

export type Olz_Api_OlzDeleteEntityTypedEndpoint11a6699569b3d4dea94a33ec63c916cb_Id = Olz_Users_Endpoints_DeleteUserEndpoint_OlzUserId;

export type Olz_Api_OlzDeleteEntityTypedEndpoint11a6699569b3d4dea94a33ec63c916cb_CustomRequest = never;

export type Olz_Api_OlzDeleteEntityTypedEndpoint11a6699569b3d4dea94a33ec63c916cb_CustomResponse = never;

export type Olz_Users_Endpoints_DeleteUserEndpoint_OlzUserId = Olz_Users_Endpoints_UserEndpointTrait_OlzUserId;

export type Olz_Api_OlzTypedEndpoint8fae1f69c089585dfb04c3666fe1bb14_Request = {'id': Olz_Users_Endpoints_GetUserInfoEndpoint_OlzUserId, 'captchaToken'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint8fae1f69c089585dfb04c3666fe1bb14_Response = Olz_Users_Endpoints_GetUserInfoEndpoint_OlzUserInfoData;

export type Olz_Users_Endpoints_GetUserInfoEndpoint_OlzUserId = number;

export type Olz_Users_Endpoints_GetUserInfoEndpoint_OlzUserInfoData = {'firstName': string, 'lastName': string, 'email'?: (Array<string> | null), 'avatarImageId'?: {[key: string]: string}};

export type Olz_Api_OlzTypedEndpointe42d5e2ac4e36f75c7f8f371041ebfe9_Request = Record<string, never>;

export type Olz_Api_OlzTypedEndpointe42d5e2ac4e36f75c7f8f371041ebfe9_Response = {'config': Olz_Captcha_Endpoints_StartCaptchaEndpoint_OlzCaptchaConfig};

export type Olz_Captcha_Endpoints_StartCaptchaEndpoint_OlzCaptchaConfig = Olz_Captcha_Utils_CaptchaUtils_OlzCaptchaConfig;

export type Olz_Captcha_Utils_CaptchaUtils_OlzCaptchaConfig = {'rand': string, 'date': string, 'mac': string};

export type Olz_Api_OlzTypedEndpoint170a4b1ce0b1702a55ee822927170ee6_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_CustomRequest};

export type Olz_Api_OlzTypedEndpoint170a4b1ce0b1702a55ee822927170ee6_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_Data = Olz_Apps_Anmelden_Endpoints_CreateBookingEndpoint_OlzBookingData;

export type Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_Id = Olz_Apps_Anmelden_Endpoints_CreateBookingEndpoint_OlzBookingId;

export type Olz_Api_OlzCreateEntityTypedEndpoint40a502a154310f752e60e292e38da9d2_CustomResponse = never;

export type Olz_Apps_Anmelden_Endpoints_CreateBookingEndpoint_OlzBookingData = Olz_Apps_Anmelden_Endpoints_BookingEndpointTrait_OlzBookingData;

export type Olz_Apps_Anmelden_Endpoints_CreateBookingEndpoint_OlzBookingId = Olz_Apps_Anmelden_Endpoints_BookingEndpointTrait_OlzBookingId;

export type Olz_Apps_Anmelden_Endpoints_BookingEndpointTrait_OlzBookingData = {'registrationId': string, 'values': {[key: string]: unknown}};

export type Olz_Apps_Anmelden_Endpoints_BookingEndpointTrait_OlzBookingId = string;

export type Olz_Api_OlzTypedEndpointa374395e4b7c60a53889b8edcc160983_Request = {'meta': Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_OlzMetaData, 'data': Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_Data, 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_CustomRequest};

export type Olz_Api_OlzTypedEndpointa374395e4b7c60a53889b8edcc160983_Response = {'id'?: (Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_Id | null), 'custom'?: Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_CustomResponse};

export type Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_Data = Olz_Apps_Anmelden_Endpoints_CreateRegistrationEndpoint_OlzRegistrationData;

export type Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_CustomRequest = never;

export type Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_Id = Olz_Apps_Anmelden_Endpoints_CreateRegistrationEndpoint_OlzRegistrationId;

export type Olz_Api_OlzCreateEntityTypedEndpoint59d3ca168646b61331983954251e3bcd_CustomResponse = never;

export type Olz_Apps_Anmelden_Endpoints_CreateRegistrationEndpoint_OlzRegistrationData = Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationData;

export type Olz_Apps_Anmelden_Endpoints_CreateRegistrationEndpoint_OlzRegistrationId = Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationId;

export type Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationData = {'title': string, 'description': string, 'infos': Array<Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationInfo>, 'opensAt'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'closesAt'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null)};

export type Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationId = string;

export type Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationInfo = {'type': Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_ValidRegistrationInfoType, 'isOptional': boolean, 'title': string, 'description': string, 'options'?: (({'text': Array<string>} | {'svg': Array<string>}) | null)};

export type Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_ValidRegistrationInfoType = ('email' | 'firstName' | 'lastName' | 'gender' | 'street' | 'postalCode' | 'city' | 'region' | 'countryCode' | 'birthdate' | 'phone' | 'siCardNumber' | 'solvNumber' | 'string' | 'enum' | 'reservation');

export type Olz_Api_OlzTypedEndpoint2a56c497e838f118422edaeb0e59f017_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint2a56c497e838f118422edaeb0e59f017_Response = {'status': ('OK' | 'ERROR'), 'managedUsers': (Array<Olz_Apps_Anmelden_Endpoints_GetManagedUsersEndpoint_ManagedUser> | null)};

export type Olz_Apps_Anmelden_Endpoints_GetManagedUsersEndpoint_ManagedUser = {'id': number, 'firstName': string, 'lastName': string};

export type Olz_Api_OlzTypedEndpointe8df9a15bcbe46c6d3bc03a99a7b021a_Request = {'userId'?: (number | null)};

export type Olz_Api_OlzTypedEndpointe8df9a15bcbe46c6d3bc03a99a7b021a_Response = Olz_Apps_Anmelden_Endpoints_GetPrefillValuesEndpoint_UserPrefillData;

export type Olz_Apps_Anmelden_Endpoints_GetPrefillValuesEndpoint_UserPrefillData = {'firstName': string, 'lastName': string, 'username': string, 'email': string, 'phone'?: (string | null), 'gender'?: (('M' | 'F' | 'O') | null), 'birthdate'?: (PhpTypeScriptApi_PhpStan_IsoDate | null), 'street'?: (string | null), 'postalCode'?: (string | null), 'city'?: (string | null), 'region'?: (string | null), 'countryCode'?: (Olz_Api_ApiObjects_IsoCountry | null), 'siCardNumber'?: (number | null), 'solvNumber'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint1ff73ac7b8317dd15bcd51870f0d2e80_Request = {'id': Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_Id, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_CustomRequest};

export type Olz_Api_OlzTypedEndpoint1ff73ac7b8317dd15bcd51870f0d2e80_Response = {'id': Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_Id, 'meta': Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_OlzMetaData, 'data': Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_Data, 'custom'?: Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_CustomResponse};

export type Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_Id = Olz_Apps_Anmelden_Endpoints_GetRegistrationEndpoint_OlzRegistrationId;

export type Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_CustomRequest = never;

export type Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_OlzMetaData = Olz_Api_OlzEntityEndpointTrait_OlzMetaData;

export type Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_Data = Olz_Apps_Anmelden_Endpoints_GetRegistrationEndpoint_OlzRegistrationData;

export type Olz_Api_OlzGetEntityTypedEndpoint0760c33ee0ef4b63a310f6c6f56a7444_CustomResponse = never;

export type Olz_Apps_Anmelden_Endpoints_GetRegistrationEndpoint_OlzRegistrationId = Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationId;

export type Olz_Apps_Anmelden_Endpoints_GetRegistrationEndpoint_OlzRegistrationData = Olz_Apps_Anmelden_Endpoints_RegistrationEndpointTrait_OlzRegistrationData;

export type Olz_Api_OlzTypedEndpoint90d5088f199da99e821f76ae4b14830f_Request = {'command': string, 'argv'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint90d5088f199da99e821f76ae4b14830f_Response = {'error': boolean, 'output': string};

export type Olz_Api_OlzTypedEndpointd065afa6cbe9fd64716c4f4434d58bae_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpointd065afa6cbe9fd64716c4f4434d58bae_Response = {'status': ('OK' | 'ERROR'), 'token'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint6347a804325089878aa1b491a312c836_Request = {'query': Olz_Apps_Logs_Endpoints_GetLogsEndpoint_OlzLogsQuery};

export type Olz_Api_OlzTypedEndpoint6347a804325089878aa1b491a312c836_Response = {'content': Array<string>, 'pagination': {'previous': (string | null), 'next': (string | null)}};

export type Olz_Apps_Logs_Endpoints_GetLogsEndpoint_OlzLogsQuery = {'channel': string, 'targetDate'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'firstDate'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'lastDate'?: (PhpTypeScriptApi_PhpStan_IsoDateTime | null), 'minLogLevel'?: (Olz_Apps_Logs_Endpoints_GetLogsEndpoint_OlzLogLevel | null), 'textSearch'?: (string | null), 'pageToken'?: (string | null)};

export type Olz_Apps_Logs_Endpoints_GetLogsEndpoint_OlzLogLevel = ('debug' | 'info' | 'notice' | 'warning' | 'error' | 'critical' | 'alert' | 'emergency');

export type Olz_Api_OlzTypedEndpoint60cda8840655ab20868437541c47f456_Request = {'csvFileId': string};

export type Olz_Api_OlzTypedEndpoint60cda8840655ab20868437541c47f456_Response = {'status': ('OK' | 'ERROR'), 'members': Array<Olz_Apps_Members_Endpoints_ImportMembersEndpoint_OlzMemberInfo>};

export type Olz_Apps_Members_Endpoints_ImportMembersEndpoint_OlzMemberInfo = {'ident': string, 'action': ('CREATE' | 'UPDATE' | 'DELETE' | 'KEEP'), 'username'?: (string | null), 'matchingUsername'?: (string | null), 'user'?: ({'id': number, 'firstName': string, 'lastName': string} | null), 'updates': {[key: string]: {'old': string, 'new': string}}};

export type Olz_Api_OlzTypedEndpoint50251594237e2dc1fb15a6350baa0238_Request = Record<string, never>;

export type Olz_Api_OlzTypedEndpoint50251594237e2dc1fb15a6350baa0238_Response = {'status': ('OK' | 'ERROR'), 'csvFileId'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint52d7b1ce86c234a277873630b3ab8b95_Request = (Record<string, never> | null);

export type Olz_Api_OlzTypedEndpoint52d7b1ce86c234a277873630b3ab8b95_Response = {'username': string, 'password': string};

export type Olz_Api_OlzTypedEndpointf4bafa0cce57bbea6b9a48aaa723a361_Request = {'deliveryType': ('email' | 'telegram'), 'monthlyPreview': boolean, 'weeklyPreview': boolean, 'deadlineWarning': boolean, 'deadlineWarningDays': ('1' | '2' | '3' | '7'), 'dailySummary': boolean, 'dailySummaryAktuell': boolean, 'dailySummaryBlog': boolean, 'dailySummaryForum': boolean, 'dailySummaryGalerie': boolean, 'dailySummaryTermine': boolean, 'weeklySummary': boolean, 'weeklySummaryAktuell': boolean, 'weeklySummaryBlog': boolean, 'weeklySummaryForum': boolean, 'weeklySummaryGalerie': boolean, 'weeklySummaryTermine': boolean};

export type Olz_Api_OlzTypedEndpointf4bafa0cce57bbea6b9a48aaa723a361_Response = {'status': ('OK' | 'ERROR')};

export type Olz_Api_OlzTypedEndpoint2b88ae7a3b1880cf64104cbba31f615d_Request = {'destination': string, 'arrival': PhpTypeScriptApi_PhpStan_IsoDateTime};

export type Olz_Api_OlzTypedEndpoint2b88ae7a3b1880cf64104cbba31f615d_Response = {'status': ('OK' | 'ERROR'), 'suggestions'?: (Array<Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportSuggestion> | null)};

export type Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportSuggestion = {'mainConnection': Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportConnection, 'sideConnections': Array<{'connection': Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportConnection, 'joiningStationId': string}>, 'originInfo': Array<Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzOriginInfo>, 'debug': string};

export type Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportConnection = {'sections': Array<Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportSection>};

export type Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzOriginInfo = {'halt': Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportHalt, 'isSkipped': boolean, 'rating': number};

export type Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportSection = {'departure': Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportHalt, 'arrival': Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportHalt, 'passList': Array<Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportHalt>, 'isWalk': boolean};

export type Olz_Apps_Oev_Endpoints_SearchTransportConnectionEndpoint_OlzTransportHalt = {'stationId': string, 'stationName': string, 'time': PhpTypeScriptApi_PhpStan_IsoDateTime};

export type Olz_Api_OlzTypedEndpoint03b59825b9902599a80ee265bb7fd709_Request = {'filter'?: (Olz_Apps_Panini2024_Endpoints_ListPanini2024PicturesEndpoint_OlzPanini2024Filter | null)};

export type Olz_Api_OlzTypedEndpoint03b59825b9902599a80ee265bb7fd709_Response = Array<{'data': Olz_Apps_Panini2024_Endpoints_ListPanini2024PicturesEndpoint_OlzPanini2024PictureData}>;

export type Olz_Apps_Panini2024_Endpoints_ListPanini2024PicturesEndpoint_OlzPanini2024Filter = ({'idIs': number} | {'page': number});

export type Olz_Apps_Panini2024_Endpoints_ListPanini2024PicturesEndpoint_OlzPanini2024PictureData = {'id': number, 'line1': string, 'line2'?: (string | null), 'association'?: (string | null), 'imgSrc': string, 'imgStyle': string, 'isLandscape': boolean, 'hasTop': boolean};

export type Olz_Api_OlzTypedEndpoint0512e306bea6fbc8e315fa6275a636ec_Request = {'data': Olz_Apps_Panini2024_Endpoints_UpdateMyPanini2024Endpoint_OlzPanini2024PictureData};

export type Olz_Api_OlzTypedEndpoint0512e306bea6fbc8e315fa6275a636ec_Response = {'status': ('OK' | 'ERROR')};

export type Olz_Apps_Panini2024_Endpoints_UpdateMyPanini2024Endpoint_OlzPanini2024PictureData = {'id'?: (number | null), 'line1': string, 'line2': string, 'residence': string, 'uploadId': string, 'onOff': boolean, 'info1': string, 'info2': string, 'info3': string, 'info4': string, 'info5': string};

export type Olz_Api_OlzTypedEndpointf6b4da7f60ffa6cde730312876e34828_Request = {'skillFilter'?: (Olz_Apps_Quiz_Endpoints_GetMySkillLevelsEndpoint_OlzSkillFilter | null)};

export type Olz_Api_OlzTypedEndpointf6b4da7f60ffa6cde730312876e34828_Response = {[key: string]: {'value': number}};

export type Olz_Apps_Quiz_Endpoints_GetMySkillLevelsEndpoint_OlzSkillFilter = {'categoryIdIn': Array<string>};

export type Olz_Api_OlzTypedEndpoint6635bad660fed6f9571be56c72986b9b_Request = {'updates': {[key: string]: {'change': number}}};

export type Olz_Api_OlzTypedEndpoint6635bad660fed6f9571be56c72986b9b_Response = {'status': ('OK' | 'ERROR')};

export type Olz_Api_OlzTypedEndpoint9e5cfbe2e06be54829a365c5dcefeb9a_Request = {'skillCategories': Array<Olz_Apps_Quiz_Endpoints_RegisterSkillCategoriesEndpoint_OlzSkillCategoryData>};

export type Olz_Api_OlzTypedEndpoint9e5cfbe2e06be54829a365c5dcefeb9a_Response = {'idByName': {[key: string]: string}};

export type Olz_Apps_Quiz_Endpoints_RegisterSkillCategoriesEndpoint_OlzSkillCategoryData = {'name': string, 'parentCategoryName'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint713ffb4d8f8bd57ef9bd9b67ea30b2c0_Request = {'skills': Array<Olz_Apps_Quiz_Endpoints_RegisterSkillsEndpoint_OlzSkillData>};

export type Olz_Api_OlzTypedEndpoint713ffb4d8f8bd57ef9bd9b67ea30b2c0_Response = {'idByName': {[key: string]: string}};

export type Olz_Apps_Quiz_Endpoints_RegisterSkillsEndpoint_OlzSkillData = {'name': string, 'categoryIds': Array<string>};

export type Olz_Api_OlzTypedEndpoint28723e95d996a77958c1a4183565cabd_Request = {'file': string, 'content'?: (string | null), 'iofXmlFileId'?: (string | null)};

export type Olz_Api_OlzTypedEndpoint28723e95d996a77958c1a4183565cabd_Response = {'status': ('OK' | 'INVALID_FILENAME' | 'INVALID_BASE64_DATA' | 'ERROR')};

export type Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Request = Record<string, never>;

export type Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Response = {'username': string, 'password': string};

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
    'linkStrava'|
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
    'listNewsReactions'|
    'toggleNewsReaction'|
    'createRole'|
    'getRole'|
    'editRole'|
    'updateRole'|
    'deleteRole'|
    'addUserRoleMembership'|
    'removeUserRoleMembership'|
    'getRoleInfo'|
    'createRun'|
    'getRun'|
    'editRun'|
    'updateRun'|
    'deleteRun'|
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
    'listTerminReactions'|
    'toggleTerminReaction'|
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
    onContinuously: Olz_Api_OlzTypedEndpoint4164b4d1d24dc38f611fd8292b3f625e_Request,
    login: Olz_Api_OlzTypedEndpoint7531943200fb44bf407e2f66cfaf55e1_Request,
    resetPassword: Olz_Api_OlzTypedEndpoint7b376aaa84e28c6a90e673850719b9d5_Request,
    switchUser: Olz_Api_OlzTypedEndpoint829e19faa8eaa4462ec42721016d7672_Request,
    logout: Olz_Api_OlzTypedEndpoint2b90bc820bc224346fa4f675cde48ece_Request,
    getAuthenticatedUser: Olz_Api_OlzTypedEndpoint8b12c01034c7f4da8eed9167af229c6f_Request,
    getAuthenticatedRoles: Olz_Api_OlzTypedEndpoint904c19fd8ac53353b810ddcb25f4858f_Request,
    getEntitiesAroundPosition: Olz_Api_OlzTypedEndpointebc70268597b516012e174432a1f406f_Request,
    verifyUserEmail: Olz_Api_OlzTypedEndpoint4ed8fea3d3f2a3eeb335cfe0fe0b7a54_Request,
    updatePassword: Olz_Api_OlzTypedEndpointe0c73364ca4b2e5b51724d981de23a19_Request,
    executeEmailReaction: Olz_Api_OlzTypedEndpoint1fb2df4ee7ff7fba2c6061bd8eb791e3_Request,
    linkStrava: Olz_Api_OlzTypedEndpointae9b80a323459f81cd5c7f02633ca62a_Request,
    linkTelegram: Olz_Api_OlzTypedEndpoint521aed3921a5c624e481534d97de7df2_Request,
    onTelegram: Olz_Api_OlzTypedEndpointdf8fed3e82edd9906220af4abcb7a4c8_Request,
    startUpload: Olz_Api_OlzTypedEndpoint4b9701069d098c637985e79e8768561b_Request,
    updateUpload: Olz_Api_OlzTypedEndpointd117e6872a98ac8700650adae232c1b1_Request,
    finishUpload: Olz_Api_OlzTypedEndpoint9baba960f97e0a6240ed22098e6ff39a_Request,
    searchEntities: Olz_Api_OlzTypedEndpoint6fa66265cdbcade62b0a9b7e88701d82_Request,
    createDownload: Olz_Api_OlzTypedEndpoint684aef211f71db9c37adc9bd332bd07e_Request,
    getDownload: Olz_Api_OlzTypedEndpointe74f002b53b4440c17f4a57531cbf2f1_Request,
    editDownload: Olz_Api_OlzTypedEndpoint3dad7d40795e6e21b3ddda39464756cd_Request,
    updateDownload: Olz_Api_OlzTypedEndpoint9e732a8690606b1f652ba5bb495ac89f_Request,
    deleteDownload: Olz_Api_OlzTypedEndpoint7973150998eccd6a727ac06752497c82_Request,
    createKarte: Olz_Api_OlzTypedEndpointecb37fb77f8223d6eebe57ccfb56db6f_Request,
    getKarte: Olz_Api_OlzTypedEndpoint5d02f180fe5876e5d2e56cba74dfbb25_Request,
    editKarte: Olz_Api_OlzTypedEndpointad0ac204338a78ae67916f14b92190ac_Request,
    updateKarte: Olz_Api_OlzTypedEndpoint5ce6cd7a8fcb262a1776aa3f3da7e372_Request,
    deleteKarte: Olz_Api_OlzTypedEndpointf6e1d7620721d58d30a5bf6e3a5e27dd_Request,
    createLink: Olz_Api_OlzTypedEndpointc411b1a560c070b45bac8006c3bfde54_Request,
    getLink: Olz_Api_OlzTypedEndpoint5b0bc669977e4cabe5618a03d09c140a_Request,
    editLink: Olz_Api_OlzTypedEndpointd86df622e688004c841884bde3783efb_Request,
    updateLink: Olz_Api_OlzTypedEndpointcad61eb502060c5f4138ce6971273211_Request,
    deleteLink: Olz_Api_OlzTypedEndpointbd84c56358ff8d45b0b2695bbf8f2690_Request,
    createNews: Olz_Api_OlzTypedEndpoint4c20f9ad209af44598bc580ebef999b8_Request,
    getNews: Olz_Api_OlzTypedEndpoint6624b36fc79007ae66efd04a258afe95_Request,
    editNews: Olz_Api_OlzTypedEndpoint6f69dc824851471c31dfb3e6b49cbbba_Request,
    updateNews: Olz_Api_OlzTypedEndpoint9cc8cb238c7b7382e41e76c82f736c96_Request,
    deleteNews: Olz_Api_OlzTypedEndpoint94a3d0f53a145aff5f346769797884f7_Request,
    getAuthorInfo: Olz_Api_OlzTypedEndpoint6664ec7267d06a2c192baa1ac488f2dc_Request,
    listNewsReactions: Olz_Api_OlzTypedEndpointf091e93370e42c7ba62ac4a32e1c40f5_Request,
    toggleNewsReaction: Olz_Api_OlzTypedEndpointb86ece73e26e81be28d034731631534a_Request,
    createRole: Olz_Api_OlzTypedEndpoint576e136131c17cd3ce98f45eb4f09b42_Request,
    getRole: Olz_Api_OlzTypedEndpoint1f2a0489dfbd35ff09e89fbbd44ac432_Request,
    editRole: Olz_Api_OlzTypedEndpoint373ffe0fa05d1cd7a0158f3643700420_Request,
    updateRole: Olz_Api_OlzTypedEndpoint539ece533e346efe6244a7c25d0cff48_Request,
    deleteRole: Olz_Api_OlzTypedEndpoint3cb1738439d90e249623ede72a6ddf99_Request,
    addUserRoleMembership: Olz_Api_OlzTypedEndpoint8545c2a902fcf0fefcefe74d4640e541_Request,
    removeUserRoleMembership: Olz_Api_OlzTypedEndpointd48b6e3a2b3398eea742833d09dbe22f_Request,
    getRoleInfo: Olz_Api_OlzTypedEndpointd0f24d2860294d00c9f3671fd1b09be2_Request,
    createRun: Olz_Api_OlzTypedEndpointe17d3e151e9de1f6183752624a658c0d_Request,
    getRun: Olz_Api_OlzTypedEndpoint68daac45b341eb65e90f62617fb47770_Request,
    editRun: Olz_Api_OlzTypedEndpoint93292215f74430f493f350e58919db96_Request,
    updateRun: Olz_Api_OlzTypedEndpoint6a479260b2220873fed7e7be7ea4b10e_Request,
    deleteRun: Olz_Api_OlzTypedEndpoint9e5c64328cde85f3c6b5977670c5bbab_Request,
    getSnippet: Olz_Api_OlzTypedEndpoint780b029e7920d1ebbc5afd1dd5c75601_Request,
    editSnippet: Olz_Api_OlzTypedEndpointfc0d01153389cf8665d41bed43b284df_Request,
    updateSnippet: Olz_Api_OlzTypedEndpoint57759ba6ed8864df6ddb35deb6552f39_Request,
    createQuestion: Olz_Api_OlzTypedEndpointbbc644cc4e246ea2a2feb3f1abf7d7af_Request,
    getQuestion: Olz_Api_OlzTypedEndpoint6cabc3aa69c0795ddd97b544305b8f28_Request,
    editQuestion: Olz_Api_OlzTypedEndpoint3c2e860b1e4ab5caff4ecac65d87289e_Request,
    updateQuestion: Olz_Api_OlzTypedEndpoint3f496fad070242433abaec2ffaef1e83_Request,
    deleteQuestion: Olz_Api_OlzTypedEndpointb6d91eff354e824535a5add156018e90_Request,
    createQuestionCategory: Olz_Api_OlzTypedEndpoint0d3074367e001f884d7c5f73435133a5_Request,
    getQuestionCategory: Olz_Api_OlzTypedEndpoint071fc36b0ee24f6749c16f7d4a322965_Request,
    editQuestionCategory: Olz_Api_OlzTypedEndpoint00aeff7ff6865019a41d9bfe3f18743c_Request,
    updateQuestionCategory: Olz_Api_OlzTypedEndpoint36058a8dfa05aa16d09dba72106832ed_Request,
    deleteQuestionCategory: Olz_Api_OlzTypedEndpointf405714c333dde94eda1a92e90d4b01d_Request,
    createWeeklyPicture: Olz_Api_OlzTypedEndpoint2674d12f2366436f95d4daf04411206c_Request,
    getWeeklyPicture: Olz_Api_OlzTypedEndpoint88ccfa34761b0d165792fe27c42044e9_Request,
    editWeeklyPicture: Olz_Api_OlzTypedEndpointe561bab850c7a74c97e08246fdd9b22a_Request,
    updateWeeklyPicture: Olz_Api_OlzTypedEndpoint08b0e35d1bf64f83234f36f093bcd798_Request,
    deleteWeeklyPicture: Olz_Api_OlzTypedEndpoint87b27e0fdd6b77c480d1728ae89e8863_Request,
    createTermin: Olz_Api_OlzTypedEndpoint88ad41d096980d2d11b75722418e39af_Request,
    getTermin: Olz_Api_OlzTypedEndpoint4455200f95effde91a87d232e9d2df9c_Request,
    editTermin: Olz_Api_OlzTypedEndpoint444d1d5a251edf092840b6d4a8042bd5_Request,
    updateTermin: Olz_Api_OlzTypedEndpointde8bfc4d144ea939be23aab4dacd4467_Request,
    deleteTermin: Olz_Api_OlzTypedEndpointb3fe6adefad0c99cd5d2f6d0d13a9852_Request,
    listTerminReactions: Olz_Api_OlzTypedEndpoint73faca57780e0d82c43937d3db325082_Request,
    toggleTerminReaction: Olz_Api_OlzTypedEndpointb2ff38c5eaa06dd6feeb2919a3e369a1_Request,
    createTerminLabel: Olz_Api_OlzTypedEndpointdf7751ad58812e0aea6158f1c6a8eab1_Request,
    listTerminLabels: Olz_Api_OlzTypedEndpointccb48a140bb788e43280a406985861f2_Request,
    getTerminLabel: Olz_Api_OlzTypedEndpoint04827aea927649ecd854fdff9f2dd24b_Request,
    editTerminLabel: Olz_Api_OlzTypedEndpoint0d3536f5ad94d18007202ad91a395315_Request,
    updateTerminLabel: Olz_Api_OlzTypedEndpointcb88a6855844d3e4c9be3d579506f138_Request,
    deleteTerminLabel: Olz_Api_OlzTypedEndpointe0d943d9f6f32a6313e19de5816140e7_Request,
    createTerminLocation: Olz_Api_OlzTypedEndpoint47ec4ae1e51437bfe770286ed788d421_Request,
    getTerminLocation: Olz_Api_OlzTypedEndpoint3df57566388159e570e908eca0bdbe86_Request,
    editTerminLocation: Olz_Api_OlzTypedEndpointd2420c029bcbc765d8b3c78f0047dfba_Request,
    updateTerminLocation: Olz_Api_OlzTypedEndpointe8c3eb41794ce5b782001285a3eb5963_Request,
    deleteTerminLocation: Olz_Api_OlzTypedEndpoint3c354446449a4d6baa829ba754dd14a9_Request,
    createTerminTemplate: Olz_Api_OlzTypedEndpoint0d736205261d0f493a89787987f13559_Request,
    getTerminTemplate: Olz_Api_OlzTypedEndpoint04f5859ab1af98a9d7a9daffe145cb98_Request,
    editTerminTemplate: Olz_Api_OlzTypedEndpointfcbc47c08732202358a60c9ea485ae5c_Request,
    updateTerminTemplate: Olz_Api_OlzTypedEndpoint2a445382fc2735e084e666300e02ea86_Request,
    deleteTerminTemplate: Olz_Api_OlzTypedEndpoint765e16f72db7842d676f93fd840c22b1_Request,
    createUser: Olz_Api_OlzTypedEndpoint5f141593eecf60875007b28b989fbeca_Request,
    getUser: Olz_Api_OlzTypedEndpointa10faf7e5e15659c29bf2dd67639145c_Request,
    editUser: Olz_Api_OlzTypedEndpoint78c85b822da108f4e189423517b8fc0c_Request,
    updateUser: Olz_Api_OlzTypedEndpoint4d89e85018426dc4142157a11ae464f1_Request,
    deleteUser: Olz_Api_OlzTypedEndpoint08e0d27bf8fd7b3910a2fa4f1da158d2_Request,
    getUserInfo: Olz_Api_OlzTypedEndpoint8fae1f69c089585dfb04c3666fe1bb14_Request,
    startCaptcha: Olz_Api_OlzTypedEndpointe42d5e2ac4e36f75c7f8f371041ebfe9_Request,
    createBooking: Olz_Api_OlzTypedEndpoint170a4b1ce0b1702a55ee822927170ee6_Request,
    createRegistration: Olz_Api_OlzTypedEndpointa374395e4b7c60a53889b8edcc160983_Request,
    getManagedUsers: Olz_Api_OlzTypedEndpoint2a56c497e838f118422edaeb0e59f017_Request,
    getPrefillValues: Olz_Api_OlzTypedEndpointe8df9a15bcbe46c6d3bc03a99a7b021a_Request,
    getRegistration: Olz_Api_OlzTypedEndpoint1ff73ac7b8317dd15bcd51870f0d2e80_Request,
    executeCommand: Olz_Api_OlzTypedEndpoint90d5088f199da99e821f76ae4b14830f_Request,
    getWebdavAccessToken: Olz_Api_OlzTypedEndpointd065afa6cbe9fd64716c4f4434d58bae_Request,
    revokeWebdavAccessToken: Olz_Api_OlzTypedEndpoint4ed8fea3d3f2a3eeb335cfe0fe0b7a54_Request,
    getLogs: Olz_Api_OlzTypedEndpoint6347a804325089878aa1b491a312c836_Request,
    importMembers: Olz_Api_OlzTypedEndpoint60cda8840655ab20868437541c47f456_Request,
    exportMembers: Olz_Api_OlzTypedEndpoint50251594237e2dc1fb15a6350baa0238_Request,
    getAppMonitoringCredentials: Olz_Api_OlzTypedEndpoint52d7b1ce86c234a277873630b3ab8b95_Request,
    updateNotificationSubscriptions: Olz_Api_OlzTypedEndpointf4bafa0cce57bbea6b9a48aaa723a361_Request,
    searchTransportConnection: Olz_Api_OlzTypedEndpoint2b88ae7a3b1880cf64104cbba31f615d_Request,
    listPanini2024Pictures: Olz_Api_OlzTypedEndpoint03b59825b9902599a80ee265bb7fd709_Request,
    updateMyPanini2024: Olz_Api_OlzTypedEndpoint0512e306bea6fbc8e315fa6275a636ec_Request,
    getMySkillLevels: Olz_Api_OlzTypedEndpointf6b4da7f60ffa6cde730312876e34828_Request,
    updateMySkillLevels: Olz_Api_OlzTypedEndpoint6635bad660fed6f9571be56c72986b9b_Request,
    registerSkillCategories: Olz_Api_OlzTypedEndpoint9e5cfbe2e06be54829a365c5dcefeb9a_Request,
    registerSkills: Olz_Api_OlzTypedEndpoint713ffb4d8f8bd57ef9bd9b67ea30b2c0_Request,
    updateResults: Olz_Api_OlzTypedEndpoint28723e95d996a77958c1a4183565cabd_Request,
    getAppSearchEnginesCredentials: Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Request,
    getAppStatisticsCredentials: Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Request,
    getAppYoutubeCredentials: Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Request,
}

export interface OlzApiResponses extends OlzApiEndpointMapping {
    onContinuously: Olz_Api_OlzTypedEndpoint4164b4d1d24dc38f611fd8292b3f625e_Response,
    login: Olz_Api_OlzTypedEndpoint7531943200fb44bf407e2f66cfaf55e1_Response,
    resetPassword: Olz_Api_OlzTypedEndpoint7b376aaa84e28c6a90e673850719b9d5_Response,
    switchUser: Olz_Api_OlzTypedEndpoint829e19faa8eaa4462ec42721016d7672_Response,
    logout: Olz_Api_OlzTypedEndpoint2b90bc820bc224346fa4f675cde48ece_Response,
    getAuthenticatedUser: Olz_Api_OlzTypedEndpoint8b12c01034c7f4da8eed9167af229c6f_Response,
    getAuthenticatedRoles: Olz_Api_OlzTypedEndpoint904c19fd8ac53353b810ddcb25f4858f_Response,
    getEntitiesAroundPosition: Olz_Api_OlzTypedEndpointebc70268597b516012e174432a1f406f_Response,
    verifyUserEmail: Olz_Api_OlzTypedEndpoint4ed8fea3d3f2a3eeb335cfe0fe0b7a54_Response,
    updatePassword: Olz_Api_OlzTypedEndpointe0c73364ca4b2e5b51724d981de23a19_Response,
    executeEmailReaction: Olz_Api_OlzTypedEndpoint1fb2df4ee7ff7fba2c6061bd8eb791e3_Response,
    linkStrava: Olz_Api_OlzTypedEndpointae9b80a323459f81cd5c7f02633ca62a_Response,
    linkTelegram: Olz_Api_OlzTypedEndpoint521aed3921a5c624e481534d97de7df2_Response,
    onTelegram: Olz_Api_OlzTypedEndpointdf8fed3e82edd9906220af4abcb7a4c8_Response,
    startUpload: Olz_Api_OlzTypedEndpoint4b9701069d098c637985e79e8768561b_Response,
    updateUpload: Olz_Api_OlzTypedEndpointd117e6872a98ac8700650adae232c1b1_Response,
    finishUpload: Olz_Api_OlzTypedEndpoint9baba960f97e0a6240ed22098e6ff39a_Response,
    searchEntities: Olz_Api_OlzTypedEndpoint6fa66265cdbcade62b0a9b7e88701d82_Response,
    createDownload: Olz_Api_OlzTypedEndpoint684aef211f71db9c37adc9bd332bd07e_Response,
    getDownload: Olz_Api_OlzTypedEndpointe74f002b53b4440c17f4a57531cbf2f1_Response,
    editDownload: Olz_Api_OlzTypedEndpoint3dad7d40795e6e21b3ddda39464756cd_Response,
    updateDownload: Olz_Api_OlzTypedEndpoint9e732a8690606b1f652ba5bb495ac89f_Response,
    deleteDownload: Olz_Api_OlzTypedEndpoint7973150998eccd6a727ac06752497c82_Response,
    createKarte: Olz_Api_OlzTypedEndpointecb37fb77f8223d6eebe57ccfb56db6f_Response,
    getKarte: Olz_Api_OlzTypedEndpoint5d02f180fe5876e5d2e56cba74dfbb25_Response,
    editKarte: Olz_Api_OlzTypedEndpointad0ac204338a78ae67916f14b92190ac_Response,
    updateKarte: Olz_Api_OlzTypedEndpoint5ce6cd7a8fcb262a1776aa3f3da7e372_Response,
    deleteKarte: Olz_Api_OlzTypedEndpointf6e1d7620721d58d30a5bf6e3a5e27dd_Response,
    createLink: Olz_Api_OlzTypedEndpointc411b1a560c070b45bac8006c3bfde54_Response,
    getLink: Olz_Api_OlzTypedEndpoint5b0bc669977e4cabe5618a03d09c140a_Response,
    editLink: Olz_Api_OlzTypedEndpointd86df622e688004c841884bde3783efb_Response,
    updateLink: Olz_Api_OlzTypedEndpointcad61eb502060c5f4138ce6971273211_Response,
    deleteLink: Olz_Api_OlzTypedEndpointbd84c56358ff8d45b0b2695bbf8f2690_Response,
    createNews: Olz_Api_OlzTypedEndpoint4c20f9ad209af44598bc580ebef999b8_Response,
    getNews: Olz_Api_OlzTypedEndpoint6624b36fc79007ae66efd04a258afe95_Response,
    editNews: Olz_Api_OlzTypedEndpoint6f69dc824851471c31dfb3e6b49cbbba_Response,
    updateNews: Olz_Api_OlzTypedEndpoint9cc8cb238c7b7382e41e76c82f736c96_Response,
    deleteNews: Olz_Api_OlzTypedEndpoint94a3d0f53a145aff5f346769797884f7_Response,
    getAuthorInfo: Olz_Api_OlzTypedEndpoint6664ec7267d06a2c192baa1ac488f2dc_Response,
    listNewsReactions: Olz_Api_OlzTypedEndpointf091e93370e42c7ba62ac4a32e1c40f5_Response,
    toggleNewsReaction: Olz_Api_OlzTypedEndpointb86ece73e26e81be28d034731631534a_Response,
    createRole: Olz_Api_OlzTypedEndpoint576e136131c17cd3ce98f45eb4f09b42_Response,
    getRole: Olz_Api_OlzTypedEndpoint1f2a0489dfbd35ff09e89fbbd44ac432_Response,
    editRole: Olz_Api_OlzTypedEndpoint373ffe0fa05d1cd7a0158f3643700420_Response,
    updateRole: Olz_Api_OlzTypedEndpoint539ece533e346efe6244a7c25d0cff48_Response,
    deleteRole: Olz_Api_OlzTypedEndpoint3cb1738439d90e249623ede72a6ddf99_Response,
    addUserRoleMembership: Olz_Api_OlzTypedEndpoint8545c2a902fcf0fefcefe74d4640e541_Response,
    removeUserRoleMembership: Olz_Api_OlzTypedEndpointd48b6e3a2b3398eea742833d09dbe22f_Response,
    getRoleInfo: Olz_Api_OlzTypedEndpointd0f24d2860294d00c9f3671fd1b09be2_Response,
    createRun: Olz_Api_OlzTypedEndpointe17d3e151e9de1f6183752624a658c0d_Response,
    getRun: Olz_Api_OlzTypedEndpoint68daac45b341eb65e90f62617fb47770_Response,
    editRun: Olz_Api_OlzTypedEndpoint93292215f74430f493f350e58919db96_Response,
    updateRun: Olz_Api_OlzTypedEndpoint6a479260b2220873fed7e7be7ea4b10e_Response,
    deleteRun: Olz_Api_OlzTypedEndpoint9e5c64328cde85f3c6b5977670c5bbab_Response,
    getSnippet: Olz_Api_OlzTypedEndpoint780b029e7920d1ebbc5afd1dd5c75601_Response,
    editSnippet: Olz_Api_OlzTypedEndpointfc0d01153389cf8665d41bed43b284df_Response,
    updateSnippet: Olz_Api_OlzTypedEndpoint57759ba6ed8864df6ddb35deb6552f39_Response,
    createQuestion: Olz_Api_OlzTypedEndpointbbc644cc4e246ea2a2feb3f1abf7d7af_Response,
    getQuestion: Olz_Api_OlzTypedEndpoint6cabc3aa69c0795ddd97b544305b8f28_Response,
    editQuestion: Olz_Api_OlzTypedEndpoint3c2e860b1e4ab5caff4ecac65d87289e_Response,
    updateQuestion: Olz_Api_OlzTypedEndpoint3f496fad070242433abaec2ffaef1e83_Response,
    deleteQuestion: Olz_Api_OlzTypedEndpointb6d91eff354e824535a5add156018e90_Response,
    createQuestionCategory: Olz_Api_OlzTypedEndpoint0d3074367e001f884d7c5f73435133a5_Response,
    getQuestionCategory: Olz_Api_OlzTypedEndpoint071fc36b0ee24f6749c16f7d4a322965_Response,
    editQuestionCategory: Olz_Api_OlzTypedEndpoint00aeff7ff6865019a41d9bfe3f18743c_Response,
    updateQuestionCategory: Olz_Api_OlzTypedEndpoint36058a8dfa05aa16d09dba72106832ed_Response,
    deleteQuestionCategory: Olz_Api_OlzTypedEndpointf405714c333dde94eda1a92e90d4b01d_Response,
    createWeeklyPicture: Olz_Api_OlzTypedEndpoint2674d12f2366436f95d4daf04411206c_Response,
    getWeeklyPicture: Olz_Api_OlzTypedEndpoint88ccfa34761b0d165792fe27c42044e9_Response,
    editWeeklyPicture: Olz_Api_OlzTypedEndpointe561bab850c7a74c97e08246fdd9b22a_Response,
    updateWeeklyPicture: Olz_Api_OlzTypedEndpoint08b0e35d1bf64f83234f36f093bcd798_Response,
    deleteWeeklyPicture: Olz_Api_OlzTypedEndpoint87b27e0fdd6b77c480d1728ae89e8863_Response,
    createTermin: Olz_Api_OlzTypedEndpoint88ad41d096980d2d11b75722418e39af_Response,
    getTermin: Olz_Api_OlzTypedEndpoint4455200f95effde91a87d232e9d2df9c_Response,
    editTermin: Olz_Api_OlzTypedEndpoint444d1d5a251edf092840b6d4a8042bd5_Response,
    updateTermin: Olz_Api_OlzTypedEndpointde8bfc4d144ea939be23aab4dacd4467_Response,
    deleteTermin: Olz_Api_OlzTypedEndpointb3fe6adefad0c99cd5d2f6d0d13a9852_Response,
    listTerminReactions: Olz_Api_OlzTypedEndpoint73faca57780e0d82c43937d3db325082_Response,
    toggleTerminReaction: Olz_Api_OlzTypedEndpointb2ff38c5eaa06dd6feeb2919a3e369a1_Response,
    createTerminLabel: Olz_Api_OlzTypedEndpointdf7751ad58812e0aea6158f1c6a8eab1_Response,
    listTerminLabels: Olz_Api_OlzTypedEndpointccb48a140bb788e43280a406985861f2_Response,
    getTerminLabel: Olz_Api_OlzTypedEndpoint04827aea927649ecd854fdff9f2dd24b_Response,
    editTerminLabel: Olz_Api_OlzTypedEndpoint0d3536f5ad94d18007202ad91a395315_Response,
    updateTerminLabel: Olz_Api_OlzTypedEndpointcb88a6855844d3e4c9be3d579506f138_Response,
    deleteTerminLabel: Olz_Api_OlzTypedEndpointe0d943d9f6f32a6313e19de5816140e7_Response,
    createTerminLocation: Olz_Api_OlzTypedEndpoint47ec4ae1e51437bfe770286ed788d421_Response,
    getTerminLocation: Olz_Api_OlzTypedEndpoint3df57566388159e570e908eca0bdbe86_Response,
    editTerminLocation: Olz_Api_OlzTypedEndpointd2420c029bcbc765d8b3c78f0047dfba_Response,
    updateTerminLocation: Olz_Api_OlzTypedEndpointe8c3eb41794ce5b782001285a3eb5963_Response,
    deleteTerminLocation: Olz_Api_OlzTypedEndpoint3c354446449a4d6baa829ba754dd14a9_Response,
    createTerminTemplate: Olz_Api_OlzTypedEndpoint0d736205261d0f493a89787987f13559_Response,
    getTerminTemplate: Olz_Api_OlzTypedEndpoint04f5859ab1af98a9d7a9daffe145cb98_Response,
    editTerminTemplate: Olz_Api_OlzTypedEndpointfcbc47c08732202358a60c9ea485ae5c_Response,
    updateTerminTemplate: Olz_Api_OlzTypedEndpoint2a445382fc2735e084e666300e02ea86_Response,
    deleteTerminTemplate: Olz_Api_OlzTypedEndpoint765e16f72db7842d676f93fd840c22b1_Response,
    createUser: Olz_Api_OlzTypedEndpoint5f141593eecf60875007b28b989fbeca_Response,
    getUser: Olz_Api_OlzTypedEndpointa10faf7e5e15659c29bf2dd67639145c_Response,
    editUser: Olz_Api_OlzTypedEndpoint78c85b822da108f4e189423517b8fc0c_Response,
    updateUser: Olz_Api_OlzTypedEndpoint4d89e85018426dc4142157a11ae464f1_Response,
    deleteUser: Olz_Api_OlzTypedEndpoint08e0d27bf8fd7b3910a2fa4f1da158d2_Response,
    getUserInfo: Olz_Api_OlzTypedEndpoint8fae1f69c089585dfb04c3666fe1bb14_Response,
    startCaptcha: Olz_Api_OlzTypedEndpointe42d5e2ac4e36f75c7f8f371041ebfe9_Response,
    createBooking: Olz_Api_OlzTypedEndpoint170a4b1ce0b1702a55ee822927170ee6_Response,
    createRegistration: Olz_Api_OlzTypedEndpointa374395e4b7c60a53889b8edcc160983_Response,
    getManagedUsers: Olz_Api_OlzTypedEndpoint2a56c497e838f118422edaeb0e59f017_Response,
    getPrefillValues: Olz_Api_OlzTypedEndpointe8df9a15bcbe46c6d3bc03a99a7b021a_Response,
    getRegistration: Olz_Api_OlzTypedEndpoint1ff73ac7b8317dd15bcd51870f0d2e80_Response,
    executeCommand: Olz_Api_OlzTypedEndpoint90d5088f199da99e821f76ae4b14830f_Response,
    getWebdavAccessToken: Olz_Api_OlzTypedEndpointd065afa6cbe9fd64716c4f4434d58bae_Response,
    revokeWebdavAccessToken: Olz_Api_OlzTypedEndpoint4ed8fea3d3f2a3eeb335cfe0fe0b7a54_Response,
    getLogs: Olz_Api_OlzTypedEndpoint6347a804325089878aa1b491a312c836_Response,
    importMembers: Olz_Api_OlzTypedEndpoint60cda8840655ab20868437541c47f456_Response,
    exportMembers: Olz_Api_OlzTypedEndpoint50251594237e2dc1fb15a6350baa0238_Response,
    getAppMonitoringCredentials: Olz_Api_OlzTypedEndpoint52d7b1ce86c234a277873630b3ab8b95_Response,
    updateNotificationSubscriptions: Olz_Api_OlzTypedEndpointf4bafa0cce57bbea6b9a48aaa723a361_Response,
    searchTransportConnection: Olz_Api_OlzTypedEndpoint2b88ae7a3b1880cf64104cbba31f615d_Response,
    listPanini2024Pictures: Olz_Api_OlzTypedEndpoint03b59825b9902599a80ee265bb7fd709_Response,
    updateMyPanini2024: Olz_Api_OlzTypedEndpoint0512e306bea6fbc8e315fa6275a636ec_Response,
    getMySkillLevels: Olz_Api_OlzTypedEndpointf6b4da7f60ffa6cde730312876e34828_Response,
    updateMySkillLevels: Olz_Api_OlzTypedEndpoint6635bad660fed6f9571be56c72986b9b_Response,
    registerSkillCategories: Olz_Api_OlzTypedEndpoint9e5cfbe2e06be54829a365c5dcefeb9a_Response,
    registerSkills: Olz_Api_OlzTypedEndpoint713ffb4d8f8bd57ef9bd9b67ea30b2c0_Response,
    updateResults: Olz_Api_OlzTypedEndpoint28723e95d996a77958c1a4183565cabd_Response,
    getAppSearchEnginesCredentials: Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Response,
    getAppStatisticsCredentials: Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Response,
    getAppYoutubeCredentials: Olz_Api_OlzTypedEndpoint266c1545d5f199b63a15cfba0e515549_Response,
}

