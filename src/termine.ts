import {CHtoWGSlat, CHtoWGSlng} from './library/wgs84_ch1903/wgs84_ch1903';

export function toggleMap(id: string, xkoord: number, ykoord: number): boolean {
    const mapid = `map${id}`;
    const div = document.getElementById(mapid);
    const buttonid = `map_${id}`;
    const button = document.getElementById(buttonid);
    if (div.style.display === 'none') {
        div.style.display = '';
        const wid = document.getElementById('Spalte1').offsetWidth - 20;
        div.innerHTML = getMapHtml(xkoord, ykoord, wid);
        button.innerHTML = `<a href='' onclick="toggleMap('${id}',${xkoord},${ykoord});return false;" class='linkmap'>Karte ausblenden</a>`;
    } else {
        div.style.display = 'none';
        div.innerHTML = '';
        button.innerHTML = `<a href='' onclick="toggleMap('${id}',${xkoord},${ykoord});return false;" class='linkmap'>Karte zeigen</a>`;
    }
    return false;
}

export function getMapHtml(xkoord: number, ykoord: number, width = 400): string {
    const lat = CHtoWGSlat(xkoord, ykoord);
    const lng = CHtoWGSlng(xkoord, ykoord);

    // Link (im Moment wird noch auf Search.ch verlinkt, denn dort sieht man öV Haltestellen)
    return `<a href='http://map.search.ch/${xkoord},${ykoord}' target='_blank'><img src='https://api.mapbox.com/styles/v1/allestuetsmerweh/ckgf9qdzm1pn319ohqghudvbz/static/pin-l+009000(${lng},${lat})/${lng},${lat},13,0/${width}x300?access_token=pk.eyJ1IjoiYWxsZXN0dWV0c21lcndlaCIsImEiOiJHbG9tTzYwIn0.kaEGNBd9zMvc0XkzP70r8Q' class='noborder test-flaky' style='margin:0px;padding:0px;align:center;border:1px solid #000000;'></a>`;
}

export function mousein(id: string): void {
    document.getElementById(id).style.color = 'rgb(0,0,0)';
}

export function mouseout(id: string): void {
    document.getElementById(id).style.color = 'rgb(0,110,25)';
}
