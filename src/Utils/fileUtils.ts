export interface ReadBase64Options {
    fileReader?: FileReader;
}

export function readBase64(file: File, options?: ReadBase64Options): Promise<string> {
    return new Promise((resolve, reject) => {
        const reader = options?.fileReader || new FileReader();
        reader.onabort = () => {
            reject(new Error(`${file.name}: Reading base64 content was aborted!`));
        };
        reader.onerror = (e: ProgressEvent<FileReader>) => {
            const error = e.target?.error;
            reject(error);
        };
        reader.onload = (e: ProgressEvent<FileReader>) => {
            const base64Content = e.target?.result;
            if (typeof base64Content !== 'string') {
                reject(new Error(`${file.name}: Base64 content is not a string!`));
                return;
            }
            resolve(base64Content);
        };
        reader.readAsDataURL(file);
    });
}
