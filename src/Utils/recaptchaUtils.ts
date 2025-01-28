import {loadScript, isLocal} from './generalUtils';

declare const grecaptcha: {
    ready: (onReady: () => void) => void,
    execute: (siteKey: string, config: {action: string}) => Promise<string>,
}|undefined;

const fakeGrecaptcha: typeof grecaptcha = {
    ready: (fn) => fn(),
    execute: () => Promise.resolve('fake'),
};

const siteKey = '6LetfAodAAAAALyY2vt84FQ-EI5Sj6HkTbGKWR3U';

/* istanbul ignore next */
export async function loadRecaptchaToken(): Promise<string> {
    await loadRecaptcha();
    const token = await getRecaptchaToken();
    return token;
}

/* istanbul ignore next */
export async function loadRecaptcha(): Promise<void> {
    if (isLocal()) {
        return;
    }
    const scriptUrl = `https://www.google.com/recaptcha/api.js?render=${siteKey}`;
    await loadScript(scriptUrl);
}

/* istanbul ignore next */
function getRecaptchaToken(): Promise<string> {
    return new Promise((resolve, reject) => {
        let gc = (isLocal() ? fakeGrecaptcha : null);
        try {
            gc = grecaptcha;
        } catch (exc: unknown) {
            // ignore
        }
        if (!gc) {
            reject(new Error('grecaptcha is undefined'));
            return;
        }
        gc.ready(() => {
            gc.execute(siteKey, {action: 'submit'})
                .then(resolve, reject);
        });
    });
}
