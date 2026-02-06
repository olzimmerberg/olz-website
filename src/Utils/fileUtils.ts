
export function getFileWarning(uploadId: string): string | null {
    if (/\.docx?$/.test(uploadId)) {
        return "Wenn möglich PDF statt Word-Datei verwenden";
    }
    if (/\.xlsx?$/.test(uploadId)) {
        return "Wenn möglich PDF statt Excel-Datei verwenden";
    }
    if (/\.pptx?$/.test(uploadId)) {
        return "Wenn möglich PDF statt PowerPoint-Datei verwenden";
    }
    if (/\.(odt|ods|odp)$/.test(uploadId)) {
        return "Wenn möglich PDF statt OpenDocument-Datei verwenden";
    }
    return null;
}

export function getCompactUploadId(uploadId: string): string {
    const match = /^([a-zA-Z0-9_-]{24})(\.[a-zA-Z0-9]+)$/.exec(uploadId);
    if (!match) {
        return uploadId.length > 10 ? `${uploadId.substring(0, 9)}…` : uploadId;
    }
    return `${match[1].substring(0, 4)}…${match[1].substring(20, 24)}${match[2]}`;
}

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
