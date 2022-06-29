import './OlzResults.scss';

let filePath: string|undefined = undefined;
let xmlDoc: XMLDocument|undefined = undefined;

if (window.location.search) {
    const match = /\?file=([^&#]+)/.exec(window.location.search);
    if (match) {
        const filename = match[1];
        if (window.location.hostname === 'localhost') { // Lokale version
            filePath = `./${filename}`;
        } else {
            filePath = `/results/${filename}`;
        }
    }
}

interface AnalysisResult {
    distance: number;
    sampleMapping: number[];
    correctMapping: number[];
}

export function allInRightOrder(sample: string[], correct: string[]): AnalysisResult {
    const distList: number[] = [];
    for (let j = 0; j < correct.length + 1; j++) {
        distList.push(j);
    }
    const matrix: number[][] = [];
    matrix.push(distList.map((_, ind) => (ind === 0 ? 0 : 1)));
    for (let i = 1; i < sample.length + 1; i++) {
        const lastDistList = distList;
        const matrixRow: number[] = [];
        for (let j = 0; j < correct.length + 1; j++) {
            const opt0 = (j === 0 ? i : lastDistList[j - 1] + (sample[i - 1] === correct[j - 1] ? 0 : 2));
            const opt1 = lastDistList[j];
            const opt2 = (j === 0 ? i : distList[j - 1] + 1);
            let min = opt0;
            let argmin = 0;
            if (opt1 < min) {
                min = opt1;
                argmin = 1;
            }
            if (opt2 < min) {
                min = opt2;
                argmin = 2;
            }
            distList[j] = min;
            matrixRow.push(argmin);
        }
        matrix.push(matrixRow);
    }
    const sampleMapping: number[] = [];
    for (let i = 0; i < sample.length; i++) {
        sampleMapping.push(NaN);
    }
    const correctMapping: number[] = [];
    for (let j = 0; j < correct.length; j++) {
        correctMapping.push(NaN);
    }
    let i = matrix.length - 1;
    let j = matrix[0].length - 1;
    while (i > 0 && j > 0) {
        sampleMapping[i - 1] = j - 1;
        correctMapping[j - 1] = i - 1;
        if (matrix[i][j] === 0) {
            i--;
            j--;
        } else if (matrix[i][j] === 1) {
            sampleMapping[i - 1] = NaN;
            i--;
        } else if (matrix[i][j] === 2) {
            correctMapping[j - 1] = NaN;
            j--;
        }
    }
    return {
        distance: distList[distList.length - 1],
        sampleMapping: sampleMapping,
        correctMapping: correctMapping,
    };
}

export function formatTime(numSeconds: number): string {
    const hours = Math.floor(numSeconds / 3600);
    const mins = Math.floor((numSeconds / 60) % 60);
    const secs = Math.floor(numSeconds % 60);
    const msecs = Math.floor((numSeconds * 1000) % 1000);
    return `${(0 < hours ? `${hours}:` : '') + (`00${mins}`).slice(-2)}:${(`00${secs}`).slice(-2)}${0 < msecs ? `.${(`000${msecs}`).slice(-3)}` : ''}`;
}
export function hashPath(): string[] {
    return location.hash.substr(1).split('/');
}
export function popHash(): void {
    location.hash = location.hash.substr(0, location.hash.lastIndexOf('/'));
}
export function pushHash(newComponent: string): void {
    location.hash += `/${newComponent}`;
}
export function setHash(newComponent: string, ind: number): void {
    location.hash = `${hashPath().slice(0, ind).join('/')}/${newComponent}`;
}

type IndexSelectionMap = {[index: number]: boolean};
const selectedIndexesByClass: {[classIndex: number]: IndexSelectionMap} = {};
function showChart(classInd: number) {
    if (classInd < 0) { return; }
    if (!(classInd in selectedIndexesByClass)) {
        selectedIndexesByClass[classInd] = {};
    }
    const classResults = xmlDoc.querySelectorAll('ResultList > ClassResult');
    const ranking = classResults[classInd].querySelectorAll('PersonResult');
    interface CorrectControl {
        controlCode: string;
        globalFirstThree: number[];
        localFirstThree: number[];
    }
    const correctControls: CorrectControl[] = [];

    let cont = true;
    for (let i = 0; i < ranking.length && cont; i++) {
        const splitTimes = ranking[i].querySelectorAll('Result > SplitTime');
        for (let j = 0; j < splitTimes.length; j++) {
            const controlCode = splitTimes[j].querySelector('ControlCode');
            const splitStatus = splitTimes[j].getAttribute('status');
            if ((splitStatus === null || splitStatus === 'OK') && controlCode !== null) {
                correctControls.push({'controlCode': controlCode.textContent, 'globalFirstThree': [], 'localFirstThree': []});
            }
            cont = false;
        }
        if (!cont) { correctControls.push({'controlCode': 'F', 'globalFirstThree': [], 'localFirstThree': []}); }
    }
    // Get times of all runners
    const times: number[][] = [];
    for (let i = 0; i < ranking.length; i++) {
        const splitTimes = ranking[i].querySelectorAll('Result > SplitTime');
        // const ind = 0;
        // const lastTime = 0;
        const sampleCodes: string[] = [];
        for (let j = 0; j < splitTimes.length; j++) {
            const controlCode = splitTimes[j].querySelector('ControlCode');
            sampleCodes.push(controlCode.textContent);
        }
        sampleCodes.push('F');
        const correctCodes: string[] = correctControls.map((e) => e.controlCode);
        const res = allInRightOrder(sampleCodes, correctCodes);
        const timesTmp = res.correctMapping.map((e) => {
            if (isNaN(e)) { return NaN; }
            if (sampleCodes[e] === 'F') {
                const resultTime = ranking[i].querySelector('Result > Time');
                if (resultTime) {
                    return parseFloat(resultTime.textContent);
                }
                return NaN;
            }
            const splitStatus = splitTimes[e].getAttribute('status');
            if (splitStatus === null || splitStatus === 'OK') {
                return parseFloat(splitTimes[e].querySelector('Time').textContent);
            }
            return NaN;

        });
        times.push(timesTmp);
    }
    console.log('Times:', times);

    // Get splits of global first three runners
    for (let i = 0; i < times.length && i < 3; i++) {
        for (let j = 0; j < times[i].length; j++) {
            const splitTime = (j === 0 ? times[i][j] : times[i][j] - times[i][j - 1]);
            correctControls[j].globalFirstThree.push(splitTime);
        }
    }
    // Get splits of local first three runners
    for (let i = 0; i < times.length; i++) {
        for (let j = 0; j < times[i].length; j++) {
            if (isNaN(times[i][j])) {
                // eslint-disable-next-line no-continue
                continue;
            }
            if (j !== 0 && isNaN(times[i][j - 1])) {
                // eslint-disable-next-line no-continue
                continue;
            }
            const splitTime = (j === 0 ? times[i][j] : times[i][j] - times[i][j - 1]);
            let k;
            for (k = 0; k < correctControls[j].localFirstThree.length && k < 3; k++) {
                if (splitTime < correctControls[j].localFirstThree[k]) { break; }
            }
            if (k < 3) { correctControls[j].localFirstThree.splice(k, 0, splitTime); }
            correctControls[j].localFirstThree.splice(3);
        }
    }

    const svg: SVGSVGElement = document.querySelector('#grafik-svg');
    const wid: number = svg.width.animVal.value - 100;
    const hei: number = svg.height.animVal.value;

    // Colors
    const colors = ['rgb(133,202,93)', 'rgb(145,210,144)', 'rgb(72,181,163)', 'rgb(111,183,214)', 'rgb(117,137,191)', 'rgb(165,137,193)', 'rgb(249,140,182)', 'rgb(252,169,133)'];

    // Control Spacing: First Local
    const referenceTimes: number[] = [];
    let sumReferenceTime = 0;
    for (let j = 0; j < correctControls.length; j++) {
        sumReferenceTime += correctControls[j].localFirstThree[0];
        referenceTimes.push(sumReferenceTime);
    }
    let minVsReference = 0;
    let maxVsReference = 0;
    for (const i in selectedIndexesByClass[classInd]) {
        if (Object.prototype.hasOwnProperty.call(selectedIndexesByClass[classInd], i)) {
            for (let j = 0; j < times[i].length; j++) {
                if (isNaN(times[i][j])) {
                // eslint-disable-next-line no-continue
                    continue;
                }
                const vsReference = times[i][j] - referenceTimes[j];
                if (vsReference < minVsReference) { minVsReference = vsReference; }
                if (maxVsReference < vsReference) { maxVsReference = vsReference; }
            }
        }
    }

    // Draw
    let svgout = '';
    svgout += `<rect x='0' y='0' width='${wid}' height='${hei}' fill='rgba(250,250,250,0.5)' />`;
    for (let j = 0; j < referenceTimes.length; j++) {
        svgout += `<rect x='${Math.floor(referenceTimes[j] * wid / sumReferenceTime)}' y='0' width='1' height='${hei}' fill='rgb(200,200,200)' />`;
        svgout += `<text x='${Math.floor(referenceTimes[j] * wid / sumReferenceTime - 1)}' y='${hei - 2}' text-anchor='end' font-size='10px' fill='rgb(200,200,200)'>${j + 1 === referenceTimes.length ? 'Z' : j + 1}</text>`;
    }
    let checkedInd = 0;
    for (const i in selectedIndexesByClass[classInd]) {
        if (Object.prototype.hasOwnProperty.call(selectedIndexesByClass[classInd], i)) {

            const color = colors[checkedInd % colors.length];
            // const lastTime = 0;
            let secsBehind = 0;
            let lastSecsBehind = 0;
            let xPart = 0;
            let lastXPart = 0;
            let everSkipped = false;
            const resultStatus = ranking[i].querySelector('Result > Status');
            const isValid = (resultStatus === null || resultStatus.textContent === 'OK');
            let skippedLast = false;
            for (let j = 0; j < times[i].length; j++) {
                if (isNaN(times[i][j])) {
                    skippedLast = true;
                    everSkipped = true;
                    // eslint-disable-next-line no-continue
                    continue;
                }
                lastXPart = xPart;
                xPart = referenceTimes[j] / sumReferenceTime;
                lastSecsBehind = secsBehind;
                secsBehind = times[i][j] - referenceTimes[j];
                svgout += `<line x1='${lastXPart * wid}' y1='${(lastSecsBehind - minVsReference) * (hei - 20) / (maxVsReference - minVsReference) + 10}' x2='${xPart * wid}' y2='${(secsBehind - minVsReference) * (hei - 20) / (maxVsReference - minVsReference) + 10}' stroke-width='1' stroke='${color}'${skippedLast ? ` stroke-dasharray='${isValid ? '5, 5' : '1, 5'}'` : ''}${everSkipped && !isValid ? ' stroke-opacity=\'0.3\'' : ''} />`;
                svgout += `<circle cx='${xPart * wid}' cy='${(secsBehind - minVsReference) * (hei - 20) / (maxVsReference - minVsReference) + 10}' r='2' fill='${color}'${everSkipped && !isValid ? ' fill-opacity=\'0.3\'' : ''} />`;
                // lastTime = times[i][j];
                skippedLast = false;
            }
            const firstName = ranking[i].querySelector('Person > Name > Given');
            const lastName = ranking[i].querySelector('Person > Name > Family');
            svgout += `<text x='${xPart * wid + 2}' y='${(secsBehind - minVsReference) * (hei - 20) / (maxVsReference - minVsReference) + 14}' font-size='14px' fill='${color}'${everSkipped && !isValid ? ' fill-opacity=\'0.3\'' : ''}>${firstName ? firstName.textContent : ''} ${lastName ? lastName.textContent : ''}</text>`;
            checkedInd++;
        }
    }
    svg.innerHTML = svgout;
}
function showRanking(classInd: number) {
    if (classInd < 0) { return; }
    if (!(classInd in selectedIndexesByClass)) { selectedIndexesByClass[classInd] = {}; }
    const classResults = xmlDoc.querySelectorAll('ResultList > ClassResult');
    const ranking = classResults[classInd].querySelectorAll('PersonResult');
    let htmlout = '';
    const shortName = classResults[classInd].querySelector('Class > ShortName');
    const name = classResults[classInd].querySelector('Class > Name');
    const length = classResults[classInd].querySelector('Course > Length');
    const climb = classResults[classInd].querySelector('Course > Climb');
    const numberOfControls = classResults[classInd].querySelector('Course > NumberOfControls');
    htmlout += `<h2 class='mobileonly'>${shortName ? shortName.textContent : name.textContent}</h2>`;
    htmlout += '<div>(';
    htmlout += (length ? (Number(length.textContent) / 1000).toFixed(1) : '?');
    htmlout += ' km, ';
    htmlout += (climb ? climb.textContent : '?');
    htmlout += ' m, ';
    htmlout += (numberOfControls ? numberOfControls.textContent : '?');
    htmlout += ' Posten)</div><div class=\'mobileonly\'><br /><a href=\'javascript:olz.setHash(&quot;grafik&quot;, 2)\' id=\'grafiklink\'>Grafik</a></div><br /><table>';
    for (let i = 0; i < ranking.length; i++) {
        const position = ranking[i].querySelector('Result > Position');
        const firstName = ranking[i].querySelector('Person > Name > Given');
        const lastName = ranking[i].querySelector('Person > Name > Family');
        const birthDate = ranking[i].querySelector('Person > BirthDate');
        const addressCity = ranking[i].querySelector('Person > Address > City');
        const addressZipCode = ranking[i].querySelector('Person > Address > ZipCode');
        const clubName = ranking[i].querySelector('Organisation > ShortName');
        const runTime = ranking[i].querySelector('Result > Time');
        htmlout += `<tr><td style='text-align:right;'><input type='checkbox' class='chart-chk' id='chk-${i}' /></td><td style='text-align:right;'>${position ? `${position.textContent}.` : ''}</td><td>${firstName ? firstName.textContent : ''} ${lastName ? lastName.textContent : ''}</td><td style='text-align:right;'>${birthDate ? birthDate.textContent.substring(0, 4) : ''}</td><td>${addressCity ? addressCity.textContent : (addressZipCode ? addressZipCode.textContent : '')}</td><td>${clubName ? clubName.textContent : ''}</td><td style='text-align:right;'>${runTime ? formatTime(Number(runTime.textContent)) : '--:--'}</td></tr>`;
    }
    htmlout += '</table>';
    document.getElementById('content-box').innerHTML = htmlout;
    for (const i in selectedIndexesByClass[classInd]) {
        if (Object.prototype.hasOwnProperty.call(selectedIndexesByClass[classInd], i)) {
            const checkbox = document.getElementById(`chk-${i}`) as HTMLInputElement;
            checkbox.checked = true;
        }
    }
    for (let i = 0; i < ranking.length; i++) {
        const checkbox = document.getElementById(`chk-${i}`) as HTMLInputElement;
        console.log(checkbox);
        checkbox.onclick = () => {
            if (checkbox.checked) {
                selectedIndexesByClass[classInd][i] = true;
            } else {
                delete selectedIndexesByClass[classInd][i];
            }
            showChart(classInd);
        };
    }
    showChart(classInd);
}
function showClasses(res: number) {
    const classes = xmlDoc.querySelectorAll('ResultList > ClassResult > Class');
    console.log('Classes:', classes);
    console.log(res);
    let htmlout = '';
    for (let i = 0; i < classes.length; i++) {
        const shortName = classes[i].querySelector('ShortName');
        const name = classes[i].querySelector('Name');
        htmlout += `<a class='classlink${i === res ? ' selected' : ''}' href='javascript:olz.setHash(&quot;class${i}&quot;, 1)'>${shortName ? shortName.textContent : name.textContent}</a>`;
    }
    document.getElementById('classes-box').innerHTML = htmlout;
}
function updateContent() {
    const path = hashPath();
    let classInd = -1;
    let grafik = false;
    for (let i = 0; i < path.length; i++) {
        const resClass = /^class(\d+)$/.exec(path[i]);
        if (resClass) { classInd = Number(resClass[1]); }
        if (path[i] === 'grafik' && classInd >= 0) { grafik = true; }
    }
    showClasses(classInd);
    showRanking(classInd);
    showChart(classInd);
    document.getElementById('classes-box').className = (classInd === -1 ? 'active' : 'inactive');
    document.getElementById('content-box').className = (classInd >= 0 && !grafik ? 'active' : 'inactive');
    document.getElementById('grafik-box').className = (grafik ? 'active' : 'inactive');
}

interface LastUpdateInfo {
    lastModified?: string;
    etag?: string;
}

const lastUpdate: LastUpdateInfo = {};
function checkUpdate(): void {
    const xhr = new XMLHttpRequest();
    xhr.open('HEAD', filePath, true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState !== 4) { return; }
        const etag = xhr.getResponseHeader('ETag');
        const lastModified = xhr.getResponseHeader('Last-modified');
        if (etag !== lastUpdate.etag || lastModified !== lastUpdate.lastModified) {
            loadUpdate();
        }
        lastUpdate.etag = etag;
        lastUpdate.lastModified = lastModified;
    };
    xhr.send();
}
function loadUpdate(): void {
    const xhr = new XMLHttpRequest();
    xhr.open('GET', filePath, true);
    xhr.onreadystatechange = () => {
        if (xhr.readyState !== 4) { return; }
        const parser = new DOMParser();
        const etag = xhr.getResponseHeader('ETag');
        const lastModified = xhr.getResponseHeader('Last-modified');
        lastUpdate.etag = etag;
        lastUpdate.lastModified = lastModified;
        xmlDoc = parser.parseFromString(xhr.responseText, 'text/xml');
        console.log('XML', xmlDoc);
        const eventName = xmlDoc.querySelector('ResultList > Event > Name').textContent;
        document.getElementById('title').innerHTML = eventName;
        updateContent();
    };
    xhr.send();
}
window.addEventListener('hashchange', updateContent);
export function loaded(): void {
    window.setInterval(checkUpdate, 15000);
    loadUpdate();
}
