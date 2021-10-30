import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {Api} from 'php-typescript-api';

export class OlzApi extends Api<OlzApiEndpoint, OlzApiRequests, OlzApiResponses> {
    public baseUrl = '/_/api/index.php';
}
