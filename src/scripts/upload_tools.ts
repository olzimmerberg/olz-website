export function obfuscaseForUpload(str: string): string {
    const iv = Math.floor(Math.random() * 0xFFFF);
    let uploadStr = '';
    let current = iv;
    for (let i = 0; i < str.length; i++) {
        const chr = str.charCodeAt(i);
        uploadStr += String.fromCharCode(chr ^ ((current >> 8) & 0xFF));
        current = ((current << 5) - current) & 0xFFFF;
    }
    return `${iv};${window.btoa(uploadStr)}`;
}
