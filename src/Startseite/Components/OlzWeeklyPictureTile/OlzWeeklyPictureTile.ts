import './OlzWeeklyPictureTile.scss';

export function olzWeeklyPictureTileSwap(): boolean {
    const weeklyImage = document.getElementById('olz-weekly-image');
    const weeklyAlternativeImage =
        document.getElementById('olz-weekly-alternative-image');
    if (weeklyImage) {
        weeklyImage.style.display = 'none';
    }
    if (weeklyAlternativeImage) {
        weeklyAlternativeImage.style.display = 'inline';
    }
    return false;
}

export function olzWeeklyPictureTileUnswap(): boolean {
    const weeklyImage = document.getElementById('olz-weekly-image');
    const weeklyAlternativeImage =
        document.getElementById('olz-weekly-alternative-image');
    if (weeklyImage) {
        weeklyImage.style.display = 'inline';
    }
    if (weeklyAlternativeImage) {
        weeklyAlternativeImage.style.display = 'none';
    }
    return false;
}
