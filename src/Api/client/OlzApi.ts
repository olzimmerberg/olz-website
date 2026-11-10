import {OlzApiEndpoint, OlzApiRequests, OlzApiResponses} from './generated_olz_api_types';
import {Api, ApiError} from 'php-typescript-api';
import {login} from '../../Common/Components/OlzLoginModal/OlzLoginModal';
import {codeHref} from '../../Common/Utils/constants';
import {getErrorOrThrow} from '../../Common/Utils/generalUtils';

export class OlzApi extends Api<OlzApiEndpoint, OlzApiRequests, OlzApiResponses> {
    public baseUrl = `${codeHref}api`;
    private refreshing: Promise<OlzApiResponses['refresh']> | null = null;

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
                    if (await this.isSessionValid()) {
                        return super.call(endpoint, request);
                    }
                    await login({});
                    return super.call(endpoint, request);
                }
            }
            throw caught;
        }
    }

    /**
     * Refreshes the session from the refresh token cookie. Concurrent callers
     * share the same request, so that the refresh token is never rotated twice.
     *
     * Resolves with the endpoint's status: `OK` if a new refresh token was
     * issued (i.e. the session was restored), `KEEP` if the cookie is unchanged
     * (i.e. the session was already valid), or `NO` if there is no valid
     * refresh token.
     */
    public refreshSession(): Promise<OlzApiResponses['refresh']> {
        if (this.refreshing) {
            return this.refreshing;
        }
        const promise = super.call('refresh', null)
            .finally(() => { this.refreshing = null; });
        this.refreshing = promise;
        return promise;
    }

    /** Whether the request can be retried, i.e. a valid session is available. */
    private async isSessionValid(): Promise<boolean> {
        try {
            const response = await this.refreshSession();
            return response.status !== 'NO';
        } catch {
            return false;
        }
    }
}

export const olzApi = new OlzApi();
