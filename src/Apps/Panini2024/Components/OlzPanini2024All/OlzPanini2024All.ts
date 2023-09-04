import './OlzPanini2024All.scss';

export function showPaniniPicture(id: string): void {
    const elem = document.getElementById(`panini-picture-${id}`);
    if (!elem) {
        return;
    }
    elem.innerHTML = `<img
        src='/apps/panini24/single/${id}.jpg'
        alt='${id}'
        style='max-width: 100px; max-height: 100px;'
    />`;
}
