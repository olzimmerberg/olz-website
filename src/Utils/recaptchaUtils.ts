import {loadScript} from './generalUtils';

let grecaptcha: {
    ready: (onReady: () => void) => void,
    execute: (siteKey: string, config: {action: string}) => Promise<string>,
}|undefined;

const siteKey = '6LetfAodAAAAALyY2vt84FQ-EI5Sj6HkTbGKWR3U';

/* istanbul ignore next */
export async function loadRecaptchaToken(): Promise<string> {
    await loadRecaptcha();
    const token = await getRecaptchaToken();
    return token;
}

/* istanbul ignore next */
export async function loadRecaptcha(): Promise<void> {
    const isLocal = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    if (isLocal) {
        grecaptcha = {
            ready: (fn) => fn(),
            execute: () => Promise.resolve('fake'),
        };
        return;
    }
    const scriptUrl = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
    await loadScript(scriptUrl);
}

/* istanbul ignore next */
function getRecaptchaToken(): Promise<string> {
    return new Promise((resolve, reject) => {
        if (!grecaptcha) {
            reject(new Error('grecaptcha is undefined'));
            return;
        }
        grecaptcha.ready(() => {
            grecaptcha?.execute(siteKey, {action: 'submit'})
                .then(resolve, reject);
        });
    });
}
