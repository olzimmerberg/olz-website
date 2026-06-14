import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {Api, ApiError} from 'php-typescript-api';
import {login} from '../../Components/Auth/OlzLoginModal/OlzLoginModal';
import {codeHref} from '../../Utils/constants';
import {getErrorOrThrow} from '../../Utils/generalUtils';

export class OlzApi extends Api<OlzApiEndpoint, OlzApiRequests, OlzApiResponses> {
    public baseUrl = `${codeHref}api`;

    public async getResult<T extends OlzApiEndpoint>(
        endpoint: T,
        request: OlzApiRequests[T],
    ): Promise<[Error, null] | [null, OlzApiResponses[T]]> {
        try {
            const response = await this.call(endpoint, request);
            return [null, response];
        } catch (unk: unknown) {
            const err = getErrorOrThrow(unk);
            return [err, null];
        }
    }

    public async call<T extends OlzApiEndpoint>(
        endpoint: T,
        request: OlzApiRequests[T],
    ): Promise<OlzApiResponses[T]> {
        try {
            return await super.call(endpoint, request);
        } catch (caught: unknown) {
            if (caught instanceof ApiError) {
                if (caught.status === 401 || caught.status === 403) {
                    await login({});
                    return super.call(endpoint, request);
                }
            }
            throw caught;
        }
    }

}

export const olzApi = new OlzApi();
