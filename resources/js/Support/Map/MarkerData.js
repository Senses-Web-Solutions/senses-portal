import RBush from 'rbush';
import EventHub from '../EventHub';

export default {
    data() {
        return {
            defaultMarkerData: {
                id: '',
                model_id: null,
                model: null,
                type: '',
                title: '',
                coordinates: [],
                lines: [],
                schedule_id: null,
                schedule_type: null,
                site_end_date: null,
                site_start_date: null,
                travel_end_date: null,
                travel_start_date: null,
                scheduler_summary_ids: [],
                schedule_items: [],
                render_lines: [],
                order: 0,
                active: false,
                label: '',
                label_position: 'center',
                show_label: false,
                asset_key: null,

                // Used to generate the div icon
                size: 32,
                shape: 'circle',
                icon: '',
                show_icon: true,
                bg_colour: '#ffffff',
                border_colour: '',
                text_colour: '',

                active_bg_colour: '#ff0000',
                active_border_colour: '#ff0000',
                active_text_colour: '#ffffff',
                default_bg_colour: '#ffffff',
                default_border_colour: '#ffffff',
                default_text_colour: '#000000',

                leaflet_layer_id: '',
                popup_html: '',
            },

            invalidMarkers: {},
            tree: new RBush(5),
        };
    },
    methods: {
        /**
         * @param {object} markers - Object of marker data objects (Not a leaflet marker layer object)
         * @param {array} points - Array of points
         * @returns {array} Returns an array of markers that are inside the polygon.
         */
        findMarkersInLatLngs(markers, points) {
            const start = performance.now();
            const foundMarkers = [];

            const bounds = this.calculateBounds(points, 0);

            Object.keys(markers).forEach((key) => {
                const marker = markers[key];

                if (this.isMarkerInsideBounds(marker, bounds)) {
                    if (this.isMarkerInsidePoints(marker, points)) {
                        foundMarkers.push(marker);
                    }
                }
            });

            const end = performance.now();
            this.logPerformance(
                'findMarkersInLatLngs',
                start,
                end,
                foundMarkers.length
            );
            return foundMarkers;
        },

        setupMarkerTree(markers) {
            const start = performance.now();

            const items = markers.map((marker) => ({
                minX: marker.coordinates[0],
                minY: marker.coordinates[1],
                maxX: marker.coordinates[0],
                maxY: marker.coordinates[1],
                marker,
            }));

            this.tree.load(items);

            EventHub.emit('map:marker-tree', this.tree);

            const end = performance.now();
            this.logPerformance('setupMarkerTree', start, end, markers.length);
        },

        findMarkersInPoints(tree, points) {
            const start = performance.now();
            const foundMarkers = [];
            const bounds = this.calculateBounds(points, 0);

            const candidates = tree.search({
                minX: bounds[0][0],
                minY: bounds[0][1],
                maxX: bounds[1][0],
                maxY: bounds[1][1],
            });

            candidates.forEach((item) => {
                if (this.isMarkerInsidePoints(item.marker, points)) {
                    foundMarkers.push(item.marker);
                }
            });

            const end = performance.now();
            this.logPerformance(
                'findMarkersInPoints',
                start,
                end,
                foundMarkers.length
            );

            return foundMarkers;
        },

        isMarkerInsideBounds(marker, bounds) {
            let isInside = false;

            const lat = marker.coordinates[1];
            const lng = marker.coordinates[0];

            if (
                lat >= bounds[0][1] &&
                lat <= bounds[1][1] &&
                lng >= bounds[0][0] &&
                lng <= bounds[1][0]
            ) {
                isInside = true;
            }

            return isInside;
        },

        /**
         * Checks if a marker is inside a polygon using the Ray Casting algorithm.
         * @param {Object} marker - The marker data object (Not a leaflet marker layer object)
         * @param {array} points - Array of points
         * @returns {boolean} Returns true if the marker is inside the polygon, false otherwise.
         */
        isMarkerInsidePoints(marker, points) {
            let isInside = false;

            const lng = marker.coordinates[0];
            const lat = marker.coordinates[1];

            for (let i = 0, j = points.length - 1; i < points.length; j = i++) {
                const latLng1 = points[i];
                const latLng2 = points[j];

                const lng1 = latLng1[0];
                const lat1 = latLng1[1];
                const lng2 = latLng2[0];
                const lat2 = latLng2[1];

                if (
                    lat1 > lat !== lat2 > lat &&
                    lng < ((lng2 - lng1) * (lat - lat1)) / (lat2 - lat1) + lng1
                ) {
                    isInside = !isInside;
                }
            }

            return isInside;
        },

        /**
         * @param {object} markers - Object of marker data objects
         * @returns {array} - Returns an array of leaflet markers
         */
        removeInvalidMarkers(markers) {
            if (Object.keys(markers).length === 0) return {};

            const start = performance.now();
            const validMarkers = {};
            const invalidMarkers = {};
            let hasInvalidMarkers = false;

            for (const key in markers) {
                const marker = markers[key];

                if (
                    marker.coordinates &&
                    marker.coordinates.length === 2 &&
                    marker.coordinates[0] !== null &&
                    marker.coordinates[1] !== null
                ) {
                    validMarkers[key] = marker;
                } else {
                    invalidMarkers[key] = marker;
                    hasInvalidMarkers = true;
                }
            }

            if (hasInvalidMarkers) {
                EventHub.emit('map:invalid-markers', invalidMarkers);
                this.invalidMarkers = invalidMarkers;
            }

            const end = performance.now();
            this.logPerformance(
                'removeInvalidMarkers',
                start,
                end,
                Object.keys(markers).length
            );

            return validMarkers;
        },

        activateMarkers(markers) {
            markers.forEach((marker) => this.activateMarker(marker));
        },

        activateMarker(marker) {
            marker.active = true;
        },

        deactivateMarkers(markers) {
            markers.forEach((marker) => this.deactivateMarker(marker));
        },

        deactivateMarker(marker) {
            marker.active = false;
        },
    },
};
