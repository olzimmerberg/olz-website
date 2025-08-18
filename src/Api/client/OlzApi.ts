import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {Api} from 'php-typescript-api';
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
}

export const olzApi = new OlzApi();
