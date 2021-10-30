import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {OlzApi} from './OlzApi';
import {ValidationError} from 'php-typescript-api';

export {OlzApi, OlzApiEndpoint, OlzApiRequests, OlzApiResponses, ValidationError};

/**
 * @deprecated Use class `OlzApi` instead. Can be mocked for tests!
 *
 * Call the OLZ API.
 */
export function callOlzApi<T extends OlzApiEndpoint>(
    endpoint: T,
    request: OlzApiRequests[T],
): Promise<OlzApiResponses[T]> {
    const olzApi = new OlzApi();
    return olzApi.call(endpoint, request);
}
