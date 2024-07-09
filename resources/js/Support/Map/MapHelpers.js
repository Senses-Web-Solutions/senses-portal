import axios from "axios";
import Utils from "./Utils";

import { getClient, getClientConfig, onClientConfigLoad } from "../client";
import { HexColourMap } from "../../Enums/Colour";

export default {
    mixins: [Utils],
    data() {
        return {
            ukBounds: [
                [-8.65, 49.86],
                [1.78, 59.5],
            ],
            clusterCache: new Map(),
        };
    },

    methods: {
        setupMarkerDataFromCoordinates(coordinates, colour = "purple") {
            const markerData = { ...this.defaultMarkerData };

            markerData.type = "coordinates";
            markerData.label = "Chat Location";
            markerData.id = "coordinates";
            markerData.coordinates = coordinates;
            markerData.leaflet_layer_id = "coordinates";
            markerData.default_bg_colour = HexColourMap[`${colour}-600`];
            markerData.default_border_colour = HexColourMap[`${colour}-700`];
            markerData.default_text_colour = HexColourMap.black;

            markerData.active_bg_colour = HexColourMap["yellow-500"];
            markerData.active_border_colour = HexColourMap["yellow-700"];
            markerData.active_text_colour = HexColourMap.black;
            markerData.pulse = true;

            if (
                markerData.coordinates &&
                markerData.coordinates.length === 2 &&
                markerData.coordinates[0] !== null &&
                markerData.coordinates[1] !== null
            ) {
                return markerData;
            }

            return null;
        },

        /**
         * Calculates the furthest distance between any two points in a given set.
         * @param {Array} points - An array of points.
         * @returns {number} The furthest distance.
         * @remarks This method has a performance of approximately 2.75ms for 2600 points.
         */
        calculateFurthestDistance(points) {
            const start = performance.now();
            const pointsCopy = [...points];

            // Calculate the convex hull of the points
            const hull = this.calculateConvexHull(pointsCopy);

            let furthestDistance = 0;

            // Calculate the distance between each pair of points on the hull
            hull.forEach((point1, index1) => {
                hull.forEach((point2, index2) => {
                    if (index1 !== index2) {
                        const distance = this.calculateDistance(point1, point2);

                        if (distance > furthestDistance) {
                            furthestDistance = distance;
                        }
                    }
                });
            });

            const end = performance.now();
            this.logPerformance(
                "calculateFurthestDistance",
                start,
                end,
                points.length
            );
            return furthestDistance;
        },

        calculateCenter(points) {
            const lngs = points
                .map((point) => Number(point[0]))
                .filter(Number.isFinite);
            const lats = points
                .map((point) => Number(point[1]))
                .filter(Number.isFinite);

            if (lngs.length > 0 && lats.length > 0) {
                const lngMin = Math.min(...lngs);
                const lngMax = Math.max(...lngs);
                const latMin = Math.min(...lats);
                const latMax = Math.max(...lats);

                return [(lngMin + lngMax) / 2, (latMin + latMax) / 2];
            }

            return [-3, 55];
        },

        /**
         * Calculates the convex hull of a set of points using the Graham's scan algorithm.
         * @param {Array<Array<number>>} points - An array of points.
         * @returns {Array} An array of points that form the convex hull of the input.
         * @remarks This method has a performance of approximately 1ms for 2600 points.
         */
        calculateConvexHull(points) {
            const start = performance.now();
            points.sort((a, b) => a[1] - b[1] || a[0] - b[0]);

            const n = points.length;
            const hull = new Array(n * 2);

            let k = 0;

            // Build lower hull
            for (let i = 0; i < n; i += 1) {
                while (
                    k >= 2 &&
                    this.crossProduct(hull[k - 2], hull[k - 1], points[i]) <= 0
                ) {
                    k -= 1;
                }
                hull[k++] = points[i]; // Use post-increment here
            }

            // Build upper hull
            for (let i = n - 2, t = k + 1; i >= 0; i -= 1) {
                while (
                    k >= t &&
                    this.crossProduct(hull[k - 2], hull[k - 1], points[i]) <= 0
                ) {
                    k -= 1;
                }
                hull[k++] = points[i]; // Use post-increment here
            }

            hull.length = k;

            const end = performance.now();
            this.logPerformance(
                "calculateConvexHull",
                start,
                end,
                points.length
            );
            return hull;
        },

        /**
         * Calculates the cross product of three points.
         * @param {Array<number>} O - The first point.
         * @param {Array<number>} A - The second point.
         * @param {Array<number>} B - The third point.
         * @returns {number} The cross product.
         */
        crossProduct(O, A, B) {
            return (
                (A[0] - O[0]) * (B[1] - O[1]) - (A[1] - O[1]) * (B[0] - O[0])
            );
        },

        /**
         * Calculate the padding based on the given distance.
         *
         * @param {number} distance - The distance in metres to calculate padding for.
         * @returns {number} The calculated padding.
         *
         * @example
         * calculatePadding(150000) = 75
         */
        calculatePadding(distance) {
            if (distance > 100000) {
                return 50;
            }
            if (distance > 10000) {
                return 30;
            }

            return 5;
        },

        /**
         * Calculate the distance between two points on the Earth's surface.
         *
         * @param {Array<number>} point1 - The [longitude, latitude] pair of the first point.
         * @param {Array<number>} point2 - The [longitude, latitude] pair of the second point.
         * @param {string} measurement - The unit of measurement to return the distance in. Default is metres.
         * @returns {number} The distance between the two points in metres, rounded to the nearest metre.
         *
         * @example
         * calculateDistance([-2.2597, 51.4071], [-0.02728,52.5531]) = 174449
         */
        calculateDistance(point1, point2, measurement = "metres") {
            const measurementTypeMap = {
                metres: (distance) => Math.round(distance),
                kilometres: (distance) => Math.round(distance / 1000),
                miles: (distance) => Math.round(distance / 1609.34),
                yards: (distance) => Math.round(distance * 1.09361),
                feet: (distance) => Math.round(distance * 3.28084),
                nautical_miles: (distance) => Math.round(distance / 1852),
            };

            const earthRadius = 6371e3; // metres
            const latitude1 = (point1[1] * Math.PI) / 180;
            const latitude2 = (point2[1] * Math.PI) / 180;
            const deltaLatitude = ((point2[1] - point1[1]) * Math.PI) / 180;
            const deltaLongitude = ((point2[0] - point1[0]) * Math.PI) / 180;

            const a =
                Math.sin(deltaLatitude / 2) * Math.sin(deltaLatitude / 2) +
                Math.cos(latitude1) *
                    Math.cos(latitude2) *
                    Math.sin(deltaLongitude / 2) *
                    Math.sin(deltaLongitude / 2);

            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

            const distance = earthRadius * c; // in metres

            const convertDistance = measurementTypeMap[measurement];
            if (!convertDistance) {
                throw new Error(`Invalid measurement type: ${measurement}`);
            }

            return convertDistance(distance);
        },

        /**
         * @param {Array<number>} points - Array of latlngs
         * @param {number} boundsBuffer - The buffer to add to the bounds
         * @returns {Array<number>} Returns botttom left and top right coordinates.
         * @example
         * [ [-2.2597, 51.4071], [-0.02728,52.5531] ]
         * @remarks Performance: 0.2ms for 2600 points
         */
        calculateBounds(points, boundsBuffer = 0.1) {
            const start = performance.now();
            let lngMin;
            let lngMax;
            let latMin;
            let latMax;

            points.forEach((point) => {
                const lng = Number(point[0]);
                const lat = Number(point[1]);

                if (Number.isFinite(lng)) {
                    lngMin = lngMin !== undefined ? Math.min(lngMin, lng) : lng;
                    lngMax = lngMax !== undefined ? Math.max(lngMax, lng) : lng;
                }

                if (Number.isFinite(lat)) {
                    latMin = latMin !== undefined ? Math.min(latMin, lat) : lat;
                    latMax = latMax !== undefined ? Math.max(latMax, lat) : lat;
                }
            });

            let bounds;

            if (lngMin !== undefined && latMin !== undefined) {
                bounds = [
                    [lngMin - boundsBuffer, latMin - boundsBuffer],
                    [lngMax + boundsBuffer, latMax + boundsBuffer],
                ];
            } else {
                bounds = this.map.getBounds();
                bounds = bounds.toArray();
            }

            const end = performance.now();
            this.logPerformance("calculateBounds", start, end, points.length);
            return bounds;
        },

        calculateAndFitBounds(points, boundsBuffer = 0.1) {
            const bounds = this.calculateBounds(points, boundsBuffer);
            const distance = this.calculateDistance(bounds[0], bounds[1]);
            const padding = this.calculatePadding(distance);
            const options = { padding };

            this.map.fitBounds(bounds, options);
        },

        /**
         * Fetches the features of a cluster on a map.
         *
         * @param {Object} e - The event object, which contains the cluster ID and point count.
         * @param {Object} map - The map object on which the clusters are rendered.
         * @returns {Array<Object>} - Returns an array of features for the given cluster. If the map object is not provided, it returns an empty array.
         * @async
         * @remarks Latest performance update 35ms -> 3.5ms
         * @remarks Performance: 3.5ms first click, 0.3ms subsequent clicks
         */
        async getClusterFeatures(e, map) {
            const start = performance.now();
            let end = null;
            if (!this.map) {
                end = performance.now();
                this.logPerformance("getClusterFeatures", start, end);
                return [];
            }

            const clusterId = e.id;
            const pointCount = e.properties.point_count;
            const clusterFeatures = await this.getClusterLeavesWithCaching(
                map,
                clusterId,
                pointCount
            );

            end = performance.now();
            this.logPerformance("getClusterFeatures", start, end);
            return clusterFeatures;
        },

        /**
         * Fetches the leaves of a cluster with caching. If the cluster is already cached, fetching is approximately 10x quicker.
         *
         * @param {number} clusterId - The ID of the cluster.
         * @param {number} pointCount - The number of points in the cluster.
         * @returns {Array<Object>} - Returns an array of leaves for the given cluster. If the cluster is cached, it returns the cached leaves.
         * @async
         */
        async getClusterLeavesWithCaching(map, clusterId, pointCount) {
            const cacheKey = `${clusterId}-${pointCount}`;
            if (this.clusterCache.has(cacheKey)) {
                return this.clusterCache.get(cacheKey);
            }

            const clusterLeaves = await map
                .getSource("markers")
                .getClusterLeaves(clusterId, pointCount);
            this.clusterCache.set(cacheKey, clusterLeaves);
            return clusterLeaves;
        },

        initClientTiles() {
            onClientConfigLoad(() => {
                const start = performance.now();
                const tiles = getClientConfig("tiles");
                const layers = getClientConfig("layers");
                const source = getClient();

                if (tiles?.length > 0) {
                    this.map.on("load", () => {
                        layers.forEach((layer) => {
                            layer.source = source;
                            layer.type = "line";
                            layer.paint = {
                                "line-color": [
                                    "interpolate",
                                    ["linear"],
                                    ["zoom"],
                                    0,
                                    "rgba(85, 42, 178, 0)",
                                    16,
                                    "rgba(85, 42, 178, 0)",
                                    20,
                                    "rgba(85, 42, 178, 0.5)",
                                ],
                                "line-width": 1,
                            };
                            layer.minzoom = 16;
                        });

                        tiles.forEach((tile) => {
                            this.map.addSource(source, tile);
                        });

                        layers.forEach((layer) => {
                            this.map.addLayer(layer, "clusters");
                        });
                    });
                }

                const end = performance.now();
                this.logPerformance("initClientTiles", start, end);
            });
        },

        async initInfrastructureAssetTiles() {
            const start = performance.now();
            const tiles = {
                type: "vector",
                tiles: [
                    `${document.location.origin}/api/v2/map-tiles/{z}/{x}/{y}`,
                ],
                maxzoom: 16,
                promoteId: "id",
            };

            this.map.addSource("infrastructure-assets", tiles);
            const layers = await this.buildInfrastructureAssetLayers();

            console.log(layers);

            layers.forEach((layer) => {
                layer.source = "infrastructure-assets";
                this.map.addLayer(layer);
            });

            // const end = performance.now();
            // this.logPerformance('initInfrastructureAssetTiles', start, end);
        },

        async buildInfrastructureAssetLayers() {
            const start = performance.now();
            const isDark = JSON.parse(localStorage.getItem("darkMode"));

            const response = await axios.get(
                "/api/v2/infrastructure-asset-types?format=all&fields[table]=id&fields[infrastructureAssetCategory.utilityType]=colour"
            );

            const utilityTypes = response.data;
            // Colour
            // {
            //     id: 95,
            //     infrastructureAssetCategory__utilityType__colour: "gray-darker",
            // }

            const layerColour = ["case"];
            layerColour.push(["boolean", ["feature-state", "focused"], false]);
            layerColour.push("#D68724");
            layerColour.push(["boolean", ["feature-state", "hover"], false]);
            layerColour.push("#D68724");
            layerColour.push(["boolean", ["feature-state", "selected"], false]);
            layerColour.push("#3b82f6");
            layerColour.push(["boolean", ["feature-state", "upstream"], false]);
            layerColour.push("#22C55E");
            layerColour.push([
                "boolean",
                ["feature-state", "downstream"],
                false,
            ]);
            layerColour.push("#F4604D");
            utilityTypes.forEach((utilityType) => {
                let colour =
                    HexColourMap[
                        utilityType
                            .infrastructureAssetCategory__utilityType__colour
                    ];
                if (!colour) {
                    colour = "#72569d";
                }
                layerColour.push([
                    "==",
                    ["get", "infrastructure_asset_type_id"],
                    utilityType.id,
                ]);
                layerColour.push(colour);
            });

            layerColour.push("#72569d");

            const lineLayer = {
                id: "infrastructure-asset-lines",
                type: "line",
                "source-layer": "infrastructure_asset_linestrings",
                minzoom: 11,
                layout: {
                    "line-cap": "round",
                    "line-join": "round",
                },
                paint: {
                    "line-color": layerColour,
                    "line-width": [
                        "interpolate",
                        ["linear"],
                        ["zoom"],
                        12,
                        1.75,
                        13,
                        2,
                    ],
                },
            };

            const pointLayer = {
                id: "infrastructure-asset-points",
                type: "circle",
                "source-layer": "infrastructure_asset_points",
                minzoom: 11,
                paint: {
                    "circle-radius": [
                        "interpolate",
                        ["linear"],
                        ["zoom"],
                        6,
                        1.75,
                        16,
                        2,
                        17,
                        7,
                    ],
                    "circle-color": layerColour,
                },
            };

            const labelTextColour = isDark ? "#ffffff" : "#000000";
            const labelTextHaloColour = isDark ? "#000000" : "#ffffff";

            const labelLayers = [
                {
                    id: "infrastructure-asset-lines-labels",
                    type: "symbol",
                    "source-layer": "infrastructure_asset_linestrings",
                    minzoom: 16,
                    layout: {
                        "symbol-placement": "line-center",
                        "text-offset": [0, 1],
                        "text-field": "{reference}",
                        "text-size": 11,
                        "text-rotation-alignment": "map",
                        "text-font": ["Noto Sans Regular"],
                    },
                    paint: {
                        "text-color": labelTextColour,
                        // 'text-halo-color': labelTextHaloColour,
                        "text-halo-width": 2,
                    },
                },
                {
                    id: "infrastructure-asset-points-labels",
                    type: "symbol",
                    "source-layer": "infrastructure_asset_points",
                    minzoom: 16,
                    layout: {
                        "text-field": "{reference}",
                        "text-size": 11,
                        "text-letter-spacing": 0.05,
                        "text-offset": [0, -1],
                        "text-font": ["Noto Sans Regular"],
                    },
                    paint: {
                        "text-color": labelTextColour,
                        "text-halo-color": labelTextHaloColour,
                        "text-halo-width": 2,
                    },
                },
            ];

            const end = performance.now();
            this.logPerformance("buildInfrastructureAssetLayers", start, end);
            return [lineLayer, pointLayer, ...labelLayers];
        },

        isPipe(infrastructureAsset) {
            if (infrastructureAsset?.geom?.numpoints >= 2) {
                return true;
            }

            return false;
        },

        logPerformance(method, start, end, items = 0) {
            const duration = (end - start).toFixed(2);
            const colorizedMethod = `\x1b[35m${method}\x1b[0m`; // Purple
            let colorizedDuration;

            if (duration > 100) {
                colorizedDuration = `\x1b[31m${duration}ms\x1b[0m`; // Red if > 100ms
            } else if (duration > 25) {
                colorizedDuration = `\x1b[33m${duration}ms\x1b[0m`; // Yellow if > 75ms
            } else {
                colorizedDuration = `\x1b[32m${duration}ms\x1b[0m`; // Green if <= 75ms
            }

            const message = `${colorizedMethod} ${colorizedDuration} ${items} items`;
            console.log(message);
        },
    },
};
