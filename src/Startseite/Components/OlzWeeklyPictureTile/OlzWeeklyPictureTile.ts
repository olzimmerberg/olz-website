import './OlzWeeklyPictureTile.scss';

export function olzWeeklyPictureTileSwap(): boolean {
    document.getElementById('olz-weekly-image').style.display = 'none';
    document.getElementById('olz-weekly-alternative-image').style.display = 'inline';
    return false;
}

export function olzWeeklyPictureTileUnswap(): boolean {
    document.getElementById('olz-weekly-image').style.display = 'inline';
    document.getElementById('olz-weekly-alternative-image').style.display = 'none';
    return false;
}
