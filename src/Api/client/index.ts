import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {OlzApi, olzApi} from './OlzApi';
import {ValidationError} from 'php-typescript-api';

export {OlzApi, olzApi, OlzApiEndpoint, OlzApiRequests, OlzApiResponses, ValidationError};

/**
 * @deprecated Use class `olzApi` instead. Can be mocked for tests!
 *
 * Call the OLZ API.
 */
export function callOlzApi<T extends OlzApiEndpoint>(
    endpoint: T,
    request: OlzApiRequests[T],
): Promise<OlzApiResponses[T]> {
    const api = new OlzApi();
    return api.call(endpoint, request);
}
