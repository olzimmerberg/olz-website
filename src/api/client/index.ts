import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './OlzApi';
import {getValidationErrorFromResponseText} from './ValidationError';

export {OlzApiEndpoint};

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
                }
            )
            .fail(
                (
                    jqXHR: any,
                    textStatus: string,
                    errorThrown: string,
                ) => {
                    const httpStatusCode = jqXHR.status;
                    console.warn(`[OLZAPI] ${endpoint}: ${httpStatusCode} ${textStatus}`);
                    const error = getValidationErrorFromResponseText(jqXHR.responseText);
                    if (!error) {
                        reject(new Error("Ein Fehler ist aufgetreten. Bitte später nochmals versuchen."));
                    }
                    reject(error);
                }
            );
    });
}
