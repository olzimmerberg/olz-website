/* global location, document, console, fetch */

function getData() {
    const urlMatch = /strava\.com\/activities\/([0-9]+)/.exec(location.href);
    if (!urlMatch) {
        console.error('Du musst auf der Seite einer Aktivität sein, z.B. https://www.strava.com/activities/1234');
        console.info('Deine Aktivitäten-Übersicht: https://www.strava.com/athlete/training');
        console.info('Wähle dort die entsprechende Aktivität und führe das Skript erneut aus.');
        return;
    }
    fetch('https://olzimmerberg.ch/api-cors/registerStravaRun', {
        body: JSON.stringify({
            token: '%%%TOKEN%%%',
            activityId: urlMatch?.[1],
            html: document.querySelector('#heading').innerHTML,
        }),
        method: 'POST',
        mode: 'cors',
    }).then((resp) => {
        const fn = resp.ok ? console.info : console.error;
        resp.json().then((json) => fn(json?.msg ?? json));
    });
}

getData();
