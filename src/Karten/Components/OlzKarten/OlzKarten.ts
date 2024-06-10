import Feature, { FeatureLike } from 'ol/Feature';
import Map from 'ol/Map';
import View from 'ol/View';
import {getCenter} from 'ol/extent';
import Point from 'ol/geom/Point';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import {transform} from 'ol/proj';
import XYZ from 'ol/source/XYZ';
import VectorSource from 'ol/source/Vector';
import Fill from 'ol/style/Fill';
import Style from 'ol/style/Style';
import CircleStyle from 'ol/style/Circle';
import Stroke from 'ol/style/Stroke';
import TextStyle from 'ol/style/Text';

import 'ol/ol.css';
import './OlzKarten.scss';

interface KartenList {
    id: number
    url: string;
    name: string;
    lat: number;
    lng: number;
}

let features: Feature[] = [];
const highlightOverlay = new VectorLayer({
    source: new VectorSource(),
    style: (feature, scale) => new Style({
        image: new CircleStyle({
            radius: 20 - Math.log(scale),
            stroke: new Stroke({
                color: [200, 0, 50, 0.75],
                width: 5 - Math.log(scale) / 5,
            }),
            fill: new Fill({color: [0, 0, 0, 0.0]}),
        }),
        text: new TextStyle({
            textAlign: 'center',
            textBaseline: 'middle',
            font: 'Open Sans, sans-serif',
            text: feature.get('label'),
            fill: new Fill({color: [200, 0, 50, 1]}),
            stroke: new Stroke({color: [255, 255, 255, 1], width: 2.5}),
            offsetX: 0,
            offsetY: 32 - Math.log(scale),
            scale: 1.75 - Math.log(scale) / 10,
        }),
    }),
});
let map: Map|null = null;

export function olzKartenMapRender(
    olzKartenList: KartenList[],
): void {
    features = olzKartenList.map((location) => new Feature({
        geometry: new Point(transform([location.lng, location.lat], 'EPSG:4326', 'EPSG:3857')),
        label: location.name,
        url: location.url,
        id: location.id,
    }));
    map = new Map({
        target: 'olz-karten-map',
        layers: [
            new TileLayer({
                source: new XYZ({
                    url: 'https://tile.osm.ch/switzerland/{z}/{x}/{y}.png',
                    // url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                }),
            }),
            new VectorLayer({
                source: new VectorSource({features}),
                style: (feature, scale) => new Style({
                    image: new CircleStyle({
                        radius: 20 - Math.log(scale),
                        stroke: new Stroke({
                            color: [200, 0, 50, 0.75],
                            width: 4 - Math.log(scale) / 5,
                        }),
                        fill: new Fill({color: [0, 0, 0, 0.0]}),
                    }),
                    text: new TextStyle({
                        textAlign: 'center',
                        textBaseline: 'middle',
                        font: 'Open Sans, sans-serif',
                        text: feature.get('label'),
                        fill: new Fill({color: [200, 0, 50, 0.75]}),
                        stroke: new Stroke({color: [255, 255, 255, 0.75], width: 1.5}),
                        offsetX: 0,
                        offsetY: 32 - Math.log(scale),
                        scale: 1.75 - Math.log(scale) / 10,
                    }),
                }),
            }),
            highlightOverlay,
        ],
        view: new View({
            projection: 'EPSG:3857',
            center: transform([8.57, 47.27], 'EPSG:4326', 'EPSG:3857'),
            zoom: 11,
        }),
    });
    let highlightedFeature: FeatureLike|null = null;
    map.on('pointermove', (evt) => {
        if (!map || evt.dragging) {
            return;
        }
        let nearestDistance: number = 20 - Math.log(map?.getView().getResolution() ?? 1);
        let nearestFeature: FeatureLike|null = null;
        map.forEachFeatureAtPixel(evt.pixel, (thisFeature) => {
            const extent = thisFeature.getGeometry()?.getExtent();
            if (!map || !extent) {
                return;
            }
            const centerPixel = map.getPixelFromCoordinate(getCenter(extent));
            const xDiff = centerPixel[0] - evt.pixel[0];
            const yDiff = centerPixel[1] - evt.pixel[1];
            const thisDistance = Math.sqrt(xDiff * xDiff + yDiff * yDiff);
            if (thisDistance > nearestDistance) {
                return;
            }
            nearestFeature = thisFeature;
            nearestDistance = thisDistance;
        });
        if (highlightedFeature === nearestFeature) {
            return;
        }
        highlightOverlay.getSource()?.getFeatures().map((feature) => {
            highlightOverlay.getSource()?.removeFeature(feature);
        });
        if (nearestFeature !== null) {
            highlightOverlay.getSource()?.addFeature(nearestFeature);
        }
        highlightedFeature = nearestFeature;
    });
    map.on('click', () => {
        const url = highlightedFeature?.get('url');
        if (url) {
            window.location.href = url;
        }
    });
}

export function kartenLinkEnter(
    karteId: number,
): boolean {
    console.log('kartenLinkEnter', karteId);
    const newFeature = features.find((item) => item.get('id') === karteId);
    highlightOverlay.getSource()?.getFeatures().map((feature) => {
        highlightOverlay.getSource()?.removeFeature(feature);
    });
    if (newFeature) {
        highlightOverlay.getSource()?.addFeature(newFeature);
    }
    return false;
}

export function kartenLinkLeave(
    karteId: number,
): boolean {
    console.log('kartenLinkLeave', karteId);
    highlightOverlay.getSource()?.getFeatures().map((feature) => {
        highlightOverlay.getSource()?.removeFeature(feature);
    });
    return false;
}
