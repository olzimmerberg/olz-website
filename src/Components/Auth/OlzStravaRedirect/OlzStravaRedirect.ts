import {olzApi} from '../../../Api/client';

import './OlzStravaRedirect.scss';

export async function olzLinkStravaWithCode(code: string): Promise<Record<string, never>> {
    return olzApi.call(
        'linkStrava',
        {code},
    );
}
