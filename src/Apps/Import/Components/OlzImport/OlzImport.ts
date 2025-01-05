import * as csv from 'csv-parse/browser/esm/sync';

import './OlzImport.scss';

window.addEventListener('load', () => {
    const pasteboxElem = document.getElementById('pastebox');
    pasteboxElem?.addEventListener('paste', (e) => {
        e.preventDefault();

        const transfer = e.clipboardData;
        if (!transfer) {
            return;
        }

        const csvData = getCsvData(transfer);

        console.log(csvData);
    });
});

function getCsvData(transfer: DataTransfer) {
    const csvText = transfer?.getData('application/csv')
        || transfer?.getData('text/csv')
        || transfer?.getData('text/plain')
        || transfer?.getData('text');
    const options = [
        csv.parse(csvText, {
            relax_column_count: true,
            delimiter: [','],
        }),
        csv.parse(csvText, {
            relax_column_count: true,
            delimiter: [';'],
        }),
        csv.parse(csvText, {
            relax_column_count: true,
            delimiter: ['\t'],
        }),
        csv.parse(csvText, {
            relax_column_count: true,
            delimiter: ['|'],
        }),
    ];
    const ratings = options.map(rateCsvOutput);
    const maxRating = Math.max(...ratings);
    return options.find((_, index) => ratings[index] === maxRating);
}

function rateCsvOutput(data: string[][]): number {
    const lengths = data.map((columns) => columns.length);
    const maxLength = Math.max(...lengths);
    const areLengthsSame = lengths.every((length) => length === maxLength);
    return areLengthsSame ? maxLength * 2 : maxLength;
}
