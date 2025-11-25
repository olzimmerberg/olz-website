import {botRegexes} from './constants';

export function isBot(userAgent: string): boolean {
    for (const regex of botRegexes) {
        const parsedRegex = /\/(.+)\/([a-z]*)/.exec(regex);
        if (parsedRegex && new RegExp(parsedRegex[1], parsedRegex[2]).test(userAgent)) {
            return true;
        }
    }
    return false;
}
