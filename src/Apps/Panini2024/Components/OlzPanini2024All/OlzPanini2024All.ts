import './OlzPanini2024All.scss';

export async function showPaniniPictures(ids: string[]): Promise<void> {
    for (const id of ids) {
        // eslint-disable-next-line no-await-in-loop
        await showPaniniPicture(id) // try
            .catch(() => showPaniniPicture(id)) // retry
            .catch(() => showPaniniPicture(id)) // retry once more
            .catch(() => undefined); // give up
    }
}

export function showPaniniPicture(id: string): Promise<void> {
    return new Promise((resolve, reject) => {
        const elem = document.getElementById(`panini-picture-${id}`);
        if (!elem) {
            return;
        }
        elem.innerHTML = `<img
        src='/apps/panini24/single/${id}.jpg'
        alt='${id}'
        style='max-width: 300px; max-height: 300px;'
        id='panini-picture-img-${id}'
    />`;
        const imgElem = document.getElementById(`panini-picture-img-${id}`);
        if (imgElem) {
            imgElem.onload = () => resolve();
            imgElem.onerror = () => reject(new Error());
        }
    });
}
