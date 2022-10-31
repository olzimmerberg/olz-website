import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {Api} from 'php-typescript-api';
import {codeHref} from '../../Utils/constants';

export class OlzApi extends Api<OlzApiEndpoint, OlzApiRequests, OlzApiResponses> {
    public baseUrl = `${codeHref}api`;
}

export const olzApi = new OlzApi();
