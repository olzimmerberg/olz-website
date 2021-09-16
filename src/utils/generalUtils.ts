export function obfuscateForUpload(content: string): string {
    const urlEncodedContent = encodeURIComponent(content);
    const iv = Math.floor(Math.random() * 0xFFFF);
    let uploadStr = '';
    let current = iv;
    for (let i = 0; i < urlEncodedContent.length; i++) {
        const chr = urlEncodedContent.charCodeAt(i);
        uploadStr += String.fromCharCode(chr ^ ((current >> 8) & 0xFF));
        current = ((current << 5) - current) & 0xFFFF;
    }
    const base64 = window.btoa(uploadStr);
    return `${iv};${base64}`;
}

export function deobfuscateUpload(obfuscated: string): string {
    const semipos = obfuscated.indexOf(';');
    const iv = Number(obfuscated.substr(0, semipos));
    const obfusbase64 = obfuscated.substr(semipos + 1);
    const obfuscontent = window.atob(obfusbase64);
    let urlEncodedContent = '';
    let current = iv;
    for (let i = 0; i < obfuscontent.length; i++) {
        urlEncodedContent += String.fromCharCode(obfuscontent.charCodeAt(i) ^ ((current >> 8) & 0xFF));
        current = ((current << 5) - current) & 0xFFFF;
    }
    const content = decodeURIComponent(urlEncodedContent);
    return content;
}

export function assertUnreachable(value: never): never {
    throw new Error(`Unexpectedly reachable using value: ${value}`);
}

export function getErrorOrThrow(err: unknown): Error {
    if (!(err instanceof Error)) {
        throw new Error('Thrown thing is not an error ¯\\_ (ツ)_/¯');
    }
    return err;
}
