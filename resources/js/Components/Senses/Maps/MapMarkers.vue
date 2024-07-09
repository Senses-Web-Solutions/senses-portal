<template></template>

<script>
import maplibregl from 'maplibre-gl';
import {debounce} from 'lodash-es';

import MapHelpers from '../../../Support/Map/MapHelpers';
import EventHub from '../../../Support/EventHub';

export default {
    mixins: [MapHelpers],
    inject: ['zoom', 'animate'],
    props: {
        map: {
            type: Object,
            required: true,
        },
        markers: {
            type: Array,
            required: true,
        },
        fit: {
            type: Boolean,
            default: false,
        },
        fitOnUpdate: {
            type: Boolean,
            default: false,
        },
        fitBounds: {
            type: Number,
            default: 0.01,
        },
        pulseRadius: {
            type: Number,
            default: 15,
        },
        disableInteractions: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            source: this.map.getSource('markers'),

            timer: Date.now(),

            lastRadius: 4,
        };
    },

    computed: {
        points() {
            const points = this.markers.map((marker) => marker.coordinates);
            return points;
        },
    },

    watch: {
        markers: {
            handler(newVal, oldVal) {
                this.renderMarkers();

                if (this.fitOnUpdate) {
                    this.fitToBounds();
                }

                // Doesn't take into account if the length is the same but markers are different
                // But provides enough of a performance boost to be worth it
                if (newVal.length !== oldVal.length) {
                    this.setupMarkerTree(this.markers);
                }
            },
            deep: true,
        },
    },

    created() {
        this.renderMarkers();
        this.setupMarkerEvents();

        if (this.fit || this.fitOnUpdate) {
            this.fitToBounds();
        }

        this.setupMarkerTree(this.markers);
    },

    beforeUnmount() {
        this.source.setData({
            type: 'FeatureCollection',
            features: [],
        });

        this.clearMarkerEvents();
    },

    methods: {
        renderMarkers() {
            const features = this.createFeatures();

            this.source.setData({
                type: 'FeatureCollection',
                features,
            });
        },

        setupMarkerEvents() {
            // Remove previous event listeners
            this.clearMarkerEvents();

            const popup = new maplibregl.Popup({
                closeButton: false,
                closeOnClick: false,
                className: 'w-64 rounded-lg inter text-sm text-gray-800'
            });

            this.map.on('mouseover', 'senses-markers', (e) => {
                const feature = e.features[0];

                if (feature.properties.popup_html) {
                    // Populate the popup and set its coordinates based on the feature.
                    popup.setLngLat(feature.geometry.coordinates)
                        .setHTML(feature.properties.popup_html)
                        .addTo(this.map);
                }
            });

            this.map.on('mouseleave', 'senses-markers', () => {
                popup.remove();
            });

            this.map.on('click', this.mapClickHandler);
        },

        clearMarkerEvents() {
            this.map.off('mouseover', 'senses-markers');
            this.map.off('mouseleave', 'senses-markers');
            this.map.off('click', this.mapClickHandler);
        },

        createFeatures() {
            return this.markers.map((marker) => marker ? this.createFeature(marker) : null).filter((marker) => marker !== null);
        },

        createFeature(marker) {
            return {
                type: 'Feature',
                geometry: {
                    type: 'Point',
                    coordinates: marker.coordinates,
                },
                properties: {
                    label: marker.label,
                    active: marker.active,
                    colour: marker.active
                        ? marker.active_bg_colour
                        : marker.default_bg_colour,
                    border_colour: marker.active
                        ? marker.active_border_colour
                        : marker.default_border_colour,
                    pulse: marker.pulse,
                    popup_html: marker.popup_html,
                    id: marker.leaflet_layer_id,
                },
            };
        },

        fitToBounds: debounce(function() {
            this.disableMapInteractions();
            this.calculateAndFitBounds(this.points, this.fitBounds);

            this.map.once('moveend', () => {
                this.enableMapInteractions();
            });
        }, 200),

        disableMapInteractions() {
            this.map.boxZoom.disable();
            this.map.dragPan.disable();
            this.map.dragRotate.disable();
            this.map.scrollZoom.disable();
            this.map.doubleClickZoom.disable();
            this.map.touchZoomRotate.disable();
        },

        enableMapInteractions() {
            this.map.boxZoom.enable();
            this.map.dragPan.enable();
            this.map.dragRotate.enable();
            this.map.scrollZoom.enable();
            this.map.doubleClickZoom.enable();
            this.map.touchZoomRotate.enable();
        },

        mapClickHandler: debounce(function(e) {
            if (this.zoom > 9 && !this.disableInteractions) {
                // Define the area around the click (Padding is added on all sides of the click eg. padding = 8 means a 16 x 16 square)
                const padding = 8;
                const boundingBox = [[e.point.x - padding, e.point.y - padding], [e.point.x + padding, e.point.y + padding]];

                // Get the features within the defined area
                const markers = this.map.queryRenderedFeatures(boundingBox, { layers: ['senses-markers'] });
                const clusters = this.map.queryRenderedFeatures(boundingBox, { layers: ['clusters'] });

                // If there's at least one feature, emit the 'map:click_marker' event with the first feature
                if (markers.length > 0) {
                    EventHub.emit('map:click_marker', markers[0]);
                }

                // If there's at least one cluster, emit the 'map:click_cluster' event with the first cluster
                if (clusters.length > 0) {
                    EventHub.emit('map:click_cluster', clusters[0]);
                }
            }
        }, 0),
    },
};

</script>

<style>
.inter {
    font-family: 'Inter', sans-serif;
}
</style>
