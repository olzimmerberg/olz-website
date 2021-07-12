import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './OlzApi';
import {getValidationErrorFromResponseText, mergeValidationErrors, ValidationError, ErrorsByField, RequestFieldId} from './ValidationError';

export {OlzApiEndpoint, OlzApiRequests, OlzApiResponses, ValidationError, RequestFieldId, mergeValidationErrors};

/**
 * @deprecated Use class `OlzApi` instead. Can be mocked for tests!
 *
 * Call the OLZ API.
 */
export function callOlzApi<T extends OlzApiEndpoint>(
    endpoint: T,
    request: OlzApiRequests[T],
): Promise<OlzApiResponses[T]> {
    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'POST',
            url: `/_/api/index.php/${endpoint}`,
            data: JSON.stringify(request),
            contentType: 'application/json; charset=utf-8',
            dataType: 'json',
        })
            .done(
                (
                    response: OlzApiResponses[T],
                    textStatus: string,
                    jqXHR: any,
                ) => {
                    const httpStatusCode = jqXHR.status;
                    console.log(`[OLZAPI] ${endpoint}: ${httpStatusCode} ${textStatus}`);
                    resolve(response);
                },
            )
            .fail(
                (
                    jqXHR: any,
                    textStatus: string,
                    _errorThrown: string,
                ) => {
                    const httpStatusCode = jqXHR.status;
                    console.warn(`[OLZAPI] ${endpoint}: ${httpStatusCode} ${textStatus}`);
                    const error = getValidationErrorFromResponseText(jqXHR.responseText);
                    if (!error) {
                        reject(new Error('Ein Fehler ist aufgetreten. Bitte später nochmals versuchen.'));
                    }
                    reject(error);
                },
            );
    });
}

export class OlzApi {
    public call<T extends OlzApiEndpoint>(
        endpoint: T,
        request: OlzApiRequests[T],
    ): Promise<OlzApiResponses[T]> {
        return callOlzApi(endpoint, request);
    }
}

export interface OlzApiError<T extends OlzApiEndpoint> {
    message: string;
    validationErrors?: ErrorsByField<T>;
}
