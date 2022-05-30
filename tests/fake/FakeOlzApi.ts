import {OlzApi} from '../../_/api/client';
import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from '../../_/api/client/generated_olz_api_types';

type MockFunctionForEndpoint<T extends OlzApiEndpoint> =
    <U extends T>(request: OlzApiRequests[U]) => Promise<OlzApiResponses[U]>;

export class FakeOlzApi extends OlzApi {
    private mockedEndpoints: {[T in OlzApiEndpoint]?: MockFunctionForEndpoint<T>} = {};

    public call<T extends OlzApiEndpoint>(
        endpoint: T,
        request: OlzApiRequests[T],
    ): Promise<OlzApiResponses[T]> {
        const mockFunction: MockFunctionForEndpoint<T> = this.mockedEndpoints[endpoint];
        if (mockFunction === undefined) {
            throw new Error(`Endpoint ${endpoint} has not been mocked`);
        }
        return mockFunction(request);
    }

    public mock<T extends OlzApiEndpoint>(
        endpoint: T,
        getResponseForRequest: MockFunctionForEndpoint<T>,
    ): void {
        this.mockedEndpoints[endpoint] = getResponseForRequest;
    }
}
