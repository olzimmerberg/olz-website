/* global OLZ_BASE_HREF, OLZ_TOKEN, location, document, fetch, alert */

function getData() {
    const urlMatch = /strava\.com\/activities\/([0-9]+)/.exec(location.href);
    if (!urlMatch) {
        alert('🚫 OLZ-Strava-Skript: Du musst auf der Seite einer Strava-Aktivität sein. Folge der Anleitung auf olzimmerberg.ch/2026'); // eslint-disable-line no-alert
        return;
    }
    fetch(`${OLZ_BASE_HREF}api-cors/registerStravaRun`, {
        body: JSON.stringify({
            token: OLZ_TOKEN,
            activityId: urlMatch?.[1],
            html: document.querySelector('#heading').innerHTML,
        }),
        method: 'POST',
        mode: 'cors',
    }).then(
        (resp) => {
            resp.json().then(
                (json) => {
                    alert(json?.msg ?? '🚫 OLZ-Strava-Skript: Ungültige Antwort'); // eslint-disable-line no-alert
                },
                (reason) => {
                    try {
                        alert(`🚫 OLZ-Strava-Skript: Antwort-Fehler "${reason}"`); // eslint-disable-line no-alert
                    } catch (error) { // eslint-disable-line no-unused-vars,@typescript-eslint/no-unused-vars
                        alert('🚫 OLZ-Strava-Skript: Unbekannter Antwort-Fehler'); // eslint-disable-line no-alert
                    }
                },
            );
        },
        (reason) => {
            try {
                alert(`🚫 OLZ-Strava-Skript: Anfrage-Fehler "${reason}"`); // eslint-disable-line no-alert
            } catch (error) { // eslint-disable-line no-unused-vars,@typescript-eslint/no-unused-vars
                alert('🚫 OLZ-Strava-Skript: Unbekannter Anfrage-Fehler'); // eslint-disable-line no-alert
            }
        },
    );
}

getData();
