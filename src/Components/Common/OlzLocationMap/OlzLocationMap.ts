import Feature from 'ol/Feature';
import Map from 'ol/Map';
import View from 'ol/View';
import Point from 'ol/geom/Point';
import TileLayer from 'ol/layer/Tile';
import VectorLayer from 'ol/layer/Vector';
import XYZ from 'ol/source/XYZ';
import VectorSource from 'ol/source/Vector';
import Fill from 'ol/style/Fill';
import Style from 'ol/style/Style';
import CircleStyle from 'ol/style/Circle';
import Stroke from 'ol/style/Stroke';
import TextStyle from 'ol/style/Text';
import {transform} from 'ol/proj';

import 'ol/ol.css';
import './OlzLocationMap.scss';

export function olzLocationMapRender(
    hash: string,
    name: string,
    lat: number,
    lng: number,
    zoom: number,
): void {
    new Map({
        target: `olz-location-map-render-${hash}`,
        layers: [
            new TileLayer({
                source: new XYZ({
                    url: 'https://tile.osm.ch/switzerland/{z}/{x}/{y}.png',
                    // url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                }),
            }),
            new VectorLayer({
                source: new VectorSource({
                    features: [
                        new Feature({
                            geometry: new Point(transform([lng, lat], 'EPSG:4326', 'EPSG:3857')),
                            label: name,
                        }),
                    ],
                }),
                style: (feature, scale) => new Style({
                    image: new CircleStyle({
                        radius: 20 - Math.log(scale),
                        stroke: new Stroke({
                            color: [200, 0, 50, 0.75],
                            width: 4 - Math.log(scale) / 5,
                        }),
                    }),
                    text: new TextStyle({
                        textAlign: 'center',
                        textBaseline: 'top',
                        font: 'Open Sans, sans-serif',
                        text: feature.get('label'),
                        fill: new Fill({color: [200, 0, 50, 0.75]}),
                        stroke: new Stroke({color: [255, 255, 255, 0.75], width: 1.5}),
                        offsetX: 0,
                        offsetY: 20 - Math.log(scale),
                        scale: 1.75 - Math.log(scale) / 10,
                    }),
                }),
            }),
        ],
        view: new View({
            projection: 'EPSG:3857',
            center: transform([lng, lat], 'EPSG:4326', 'EPSG:3857'),
            zoom,
        }),
        interactions: [],
    });
}
