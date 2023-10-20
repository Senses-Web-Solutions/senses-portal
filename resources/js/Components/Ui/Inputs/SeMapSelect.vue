<template>
    <InputGroup :id="id" :label="label" :is-valid="isValid" :error="error" :name="name" :help-text="helpText" inset-validation-icon>
        <MapboxMap
        :access-token="mapboxToken"
        :zoom="zoom"
        :center="center"
        :fit-to-all="false"
        class="h-[35vh] select-none rounded-lg"
        @click="mapClick"
        @firstidle="firstMapIdle"
        @load="mapLoad">

            <MapboxSource id="tiles" :options="tileSource" />
            <MapboxSource id="work-package-polygons" :options="workPackagePolygonSource" />

            <MapboxGeojsonLayer source-id="work-package-polygons" :layer="workPackagePolygonsLayer" layer-id="work-package-polygons" :fit-on-update="false" />

            <MapboxGeojsonLayer source-id="tiles" :layer="infrastructureAssetLinesLayer" layer-id="infrastructure-asset-lines" :fit-on-update="false" />

            <MapboxGeojsonLayer source-id="tiles" :layer="infrastructureAssetPointsLayer" layer-id="infrastructure-asset-points" :fit-on-update="false" />

            <MapboxGeojsonLayer source-id="tiles" :layer="infrastructureAssetLinesLabelsLayer" layer-id="infrastructure-asset-lines-labels" :fit-on-update="false" />

            <MapboxGeojsonLayer source-id="tiles" :layer="infrastructureAssetPointsLabelsLayer" layer-id="infrastructure-asset-points-labels" :fit-on-update="false" />

            <MapboxGeojsonLayer source-id="work-package-polygons" :layer="workPackagePolygonLabelsLayer" layer-id="work-package-polygons-labels" :fit-on-update="false" />
        </MapboxMap>
        <div class="mt-4">
            <div v-if="!idleMap" class="flex items-center">
                <LoadingIcon class="mr-2 h-6 w-6" />
                <p>Initialising Map</p>
            </div>
            <div v-if="loadingInfrastructureAsset" class="flex items-center">
                <LoadingIcon class="mr-2 h-6 w-6" />
                <p>Loading Infrastructure Asset Information</p>
            </div>
            <div
                v-if="selectedInfrastructureAsset"
                class="flex items-center justify-between"
            >
                <div class="space-y-2">
                    <p class="block text-zinc-800">
                        <span>Infrastructure Asset ID: </span>
                        <span>{{ selectedInfrastructureAsset.id }}</span>
                    </p>
                    <p class="block text-zinc-800">
                        <span>Infrastructure Asset Type: </span>
                        <span>{{
                            selectedInfrastructureAsset.infrastructure_asset_type_id
                        }}</span>
                    </p>
                </div>
                <SecondaryButton
                    v-if="!confirmationOnly"
                    class="ml-4"
                    @click="resetSeMapSelect()"
                    >Change</SecondaryButton
                >
            </div>

            <!-- Upstream -->
            <div v-if="showUpstreamSelect" class="mt-4 flex items-center justify-between">
                <div class="space-y-2">
                    <SeLabel>Select the Upstream Node</SeLabel>
                    <p v-if="
                        (selectedUpstream || skippedUpstream) &&
                        !loadingUpstream
                    " class="block text-zinc-800">
                        <span>Upstream ID: </span>
                        <span>{{ selectedUpstream?.id || 'Skipped' }}</span>
                    </p>
                    <p v-if="
                        (selectedUpstream || skippedUpstream) &&
                        !loadingUpstream
                    " class="block text-zinc-800">
                        <span>Upstream Type: </span>
                        <span>{{
                            selectedUpstream?.infrastructure_asset_type_id ||
                            'Skipped'
                        }}</span>
                    </p>
                </div>

                <SecondaryButton v-if="
                    !selectedUpstream &&
                    !skippedUpstream &&
                    !loadingUpstream
                " @click="skipUpstream()">I Don't Know</SecondaryButton>
                <SecondaryButton v-if="selectedUpstream || skippedUpstream" class="ml-4" @click="resetUpstream()">Change</SecondaryButton>
            </div>
            <div v-if="loadingUpstream" class="mt-2 flex items-center">
                <LoadingIcon class="mr-2 h-6 w-6" />
                <p>Loading Upstream Information</p>
            </div>

            <!-- Downstream -->
            <div v-if="showDownstreamSelect" class="mt-4 flex items-center justify-between">
                <div class="space-y-2">
                    <SeLabel>Select the Downstream Node</SeLabel>
                    <p v-if="
                        (selectedDownstream || skippedDownstream) &&
                        !loadingDownstream
                    " class="block text-zinc-800">
                        <span>Downstream ID: </span>
                        <span>{{ selectedDownstream?.id || 'Skipped' }}</span>
                    </p>
                    <p v-if="
                        (selectedDownstream || skippedDownstream) &&
                        !loadingDownstream
                    " class="block text-zinc-800">
                        <span>Downstream Type: </span>
                        <span>{{
                            selectedDownstream?.infrastructure_asset_type_id ||
                            'Skipped'
                        }}</span>
                    </p>
                </div>

                <SecondaryButton v-if="
                    !selectedDownstream &&
                    !skippedDownstream &&
                    !loadingDownstream
                " @click="skipDownstream()">I Don't Know</SecondaryButton>
                <SecondaryButton v-if="selectedDownstream || skippedDownstream" class="ml-4" @click="resetDownstream()">Change</SecondaryButton>
            </div>
            <div v-if="loadingDownstream" class="mt-2 flex items-center">
                <LoadingIcon class="mr-2 h-6 w-6" />
                <p>Loading Downstream Information</p>
            </div>
        </div>
        <div v-if="mapSelectError" class="mt-4 text-red-500">
            <ul v-if="allowedInfrastructureAssetTypes.length > 0" class="list-disc pl-5">
                <span class="mb-3 block font-bold">Invalid Infrastructure Asset Type</span>
                <span class="mb-1 block">Allowed Types are:</span>
                <li v-for="(type, index) in allowedInfrastructureAssetTypes" :key="index" class="mb-1">
                    Title: {{ type.title }}, ID: {{ type.id }}
                </li>
                <span class="mt-3 block">If selecting upstream/downstream, please select a
                    Node</span>
            </ul>
            <span v-else>Please select a Node</span>
        </div>
    </InputGroup>
</template>
<script>
import axios from 'axios';
import InputGroup from './InputGroup.vue';

import MapboxMap from '../../Senses/Mapbox/MapboxMap.ts';
import MapboxSource from '../../Senses/Mapbox/MapboxSource.vue';
import MapboxGeojsonLayer from '../../Senses/Mapbox/MapboxGeojsonLayer.ts';
import LoadingIcon from '../LoadingIcon.vue';
import SeLabel from './SeLabel.vue';
import SecondaryButton from '../Buttons/SecondaryButton.vue';

const mapboxToken = import.meta.env.VITE_MAPBOX_TOKEN;

export default {
    components: {
        InputGroup,
        MapboxMap,
        MapboxSource,
        MapboxGeojsonLayer,
        LoadingIcon,
        SeLabel,
        SecondaryButton,
    },
    props: {
        rounded: {
            type: Boolean,
            default: true,
        },
        modelValue: {
            required: true,
        },
        label: {
            type: String,
            required: false,
            default: 'Map Select',
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        trackBy: {
            type: String,
            default: 'id',
        },
        options: {
            type: Array,
            default: () => [],
        },
        error: {
            required: false,
            default: null,
        },
        searchable: {
            type: Boolean,
            default: true,
        },
        field: {
            type: String,
            default: null,
        },
        loading: {
            type: Boolean,
            deafult: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        helpText: {
            type: [String, null],
            default: null,
        },
        data: {
            type: Object,
            required: true,
        },
        confirmationOnly: {
            type: Boolean,
            required: true,
            default:false,
        },
        allowedInfrastructureAssetTypes: {
            type: Array,
            default() {
                return [];
            },
        },
    },
    emits: ['update:modelValue'],

    data() {
        return {
            showBounds:false,
            map: null,
            idleMap: false,
            boundsBuffer: 0.00025,
            nextStep: null,
            featureClicked: null,
            selectedInfrastructureAsset: null,
            selectedUpstream: null,
            selectedDownstream: null,
            selectedInfrastructureAssetID: null,
            selectedUpstreamID: null,
            selectedDownstreamID: null,
            loadingInfrastructureAsset: false,
            loadingUpstream: false,
            loadingDownstream: false,
            upstreamError: false,
            downstreamError: false,
            wrongTypeError: false,
            skippedUpstream: false,
            skippedDownstream: false,
            mapSelectError:false,
            mapboxToken,
            tileSource: {
                type: 'vector',
                tiles: [
                    `${document.location.origin}/api/v2/map-tiles/{z}/{x}/{y}`,
                ],
                maxzoom: 16,
                promoteId: 'id',
            },

            workPackagePolygonSource: {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: [],
                },
            },

            infrastructureAssetPointsLayer: {
                type: 'circle',
                'source-layer': 'infrastructure_asset_points',
                minzoom: 11,
                paint: {
                    'circle-radius': [
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        6,
                        1.75,
                        16,
                        2,
                        17,
                        10,
                    ],
                    'circle-color': [
                        'case',
                        ['boolean', ['feature-state', 'upstream'], false],
                        '#00ff00',
                        ['boolean', ['feature-state', 'downstream'], false],
                        '#ff0000',
                        ['boolean', ['feature-state', 'selected'], false],
                        '#4683E7',
                        '#552ab2', // else
                    ],
                },
            },

            infrastructureAssetLinesLayer: {
                type: 'line',
                'source-layer': 'infrastructure_asset_linestrings',
                minzoom: 11,
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round',
                },
                paint: {
                    'line-color': [
                        'case',
                        ['boolean', ['feature-state', 'selected'], false],
                        '#4683E7',
                        '#552ab2',
                    ],
                    'line-width': [
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        12,
                        1.75, // zoom is 12 (or less)
                        13,
                        2, // zoom is 13 (or greater)
                    ],
                },
            },

            workPackagePolygonsLayer: {
                type: 'fill',
                paint: {
                    'fill-color': '#552ab2',
                    'fill-opacity': [
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        6,
                        0.5,
                        8,
                        0.25,
                        11,
                        0.1,
                    ],
                },
            },

            infrastructureAssetPointsLabelsLayer: {
                type: 'symbol',
                'source-layer': 'infrastructure_asset_points',
                minzoom: 16,
                layout: {
                    'text-offset': [0, -1],
                    'text-field': '{reference}',
                    'text-size': 12,
                },
                paint: {
                    'text-halo-color': 'rgba(255,255,255,1)',
                    'text-halo-width': 2,
                },
            },

            infrastructureAssetLinesLabelsLayer: {
                type: 'symbol',
                'source-layer': 'infrastructure_asset_linestrings',
                minzoom: 16,
                layout: {
                    // "symbol-placement": "line",
                    'symbol-placement': 'line-center',
                    'text-offset': [0, 1],
                    'text-field': '{reference}',
                    'text-size': 12,
                    'text-rotation-alignment': 'map',
                    // "text-rotation-alignment":"viewport"
                },
                paint: {
                    'text-halo-color': 'rgba(255,255,255,1)',
                    'text-halo-width': 2,
                },
            },

            workPackagePolygonLabelsLayer: {
                type: 'symbol',
                maxzoom: 11,
                layout: {
                    'text-field': '{title}',
                    'text-size': 16,
                },
                paint: {
                    'text-halo-color': 'rgba(255,255,255,1)',
                    'text-halo-width': 2,
                },
            },
        };
    },
    computed: {
        proxyModel:{
            get() {
                return this.modelValue;
            },

            set(val) {
                // this.$emit('update:modelValue', val); //normally we would reflect all changes immediatly, however there are watchers calling load
                // and I want to keep for now the same pattern where we call emitModelUpdate to reflect changes back up
            },
        },
        infrastructureAssetUrl() {
            if(!this.selectedInfrastructureAssetID || this.selectedInfrastructureAssetID == 'Unknown Asset') {
                return null;
            }
            return `/api/v2/infrastructure-assets/${this.selectedInfrastructureAssetID}`;
        },

        upstreamInfrastructureAssetUrl() {
            if(!this.selectedUpstreamID) {
                return null;
            }
            return `/api/v2/infrastructure-assets/${this.selectedUpstreamID}`;
        },

        downstreamInfrastructureAssetUrl() {
            if(!this.selectedDownstreamID) {
                return null;
            }

            return `/api/v2/infrastructure-assets/${this.selectedDownstreamID}`;
        },

        zoom() {
            if(this.proxyModel?.map_zoom) {
                return this.proxyModel?.map_zoom;
            }

            if(this.proxyModel?.selected_infrastructure_asset_id) {
                return 18;
            }

            return 5;
        },

        center() {
            if(this.proxyModel?.lng && this.proxyModel?.lat) {
                return [this.proxyModel.lng, this.proxyModel.lat];
            }
            return this.proxyModel?.map_center ?? [-2.7735199, 54.3555337];
        },

        allowedInfrastructureAssetTypeIds() {
            const mapArr = this.allowedInfrastructureAssetTypes.map(
                (type) => type.id
            );
            return mapArr;
        },

        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },

        showDownstreamSelect() {
            return this.selectedInfrastructureAsset && this.selectedInfrastructureAsset.geom.numpoints > 1 && (this.selectedUpstream || this.skippedUpstream || this.selectedDownstream || this.skippedDownstream);
        },

        showUpstreamSelect() {
            return this.selectedInfrastructureAsset && this.selectedInfrastructureAsset.geom.numpoints > 1;
        }
    },
    watch: {
        infrastructureAssetUrl(newUrl) {
            if (!newUrl) {
                return;
            }

            this.selectedInfrastructureAsset = null;
            this.loadingInfrastructureAsset = true;
            this.mapSelectError = false;
            this.loadInfrastructureAsset();
        },

        upstreamInfrastructureAssetUrl(newUrl) {
            if (!newUrl) {
                return;
            }

            this.selectedUpstream = null;
            this.loadingUpstream = true;
            this.upstreamError = false;
            this.mapSelectError = false;

            this.loadUpstreamInfrastructureAsset();
        },

        downstreamInfrastructureAssetUrl(newUrl) {
            if (!newUrl) {
                return;
            }

            this.selectedDownstream = null;
            this.loadingDownstream = true;
            this.downstreamError = false;

            this.loadDownstreamInfrastructureAsset();
        },
    },
    created() {
        this.loadWorkPackageGeoJson();
    },
    methods: {
        loadWorkPackageGeoJson() {
            axios.get('/api/v2/work-packages?format=geojson').then((response) => {
                this.workPackagePolygonSource.data = response.data;
            });
        },

        loadInfrastructureAsset() {
            axios.get(this.infrastructureAssetUrl).then((response) => {
                // If the asset they have selected is in the array then continue
                if (this.allowedInfrastructureAssetTypeIds.includes(response.data.infrastructure_asset_type_id)) {
                    this.selectInfrastructureAsset(response.data);
                }
                else if (this.allowedInfrastructureAssetTypes.length === 0)
                {
                    this.selectInfrastructureAsset(response.data);
                }
                else
                {
                    this.mapSelectError = true;
                    this.map.removeFeatureState({
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_linestrings',
                        id: this.selectedInfrastructureAssetID,
                    });

                    this.map.removeFeatureState({
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_points',
                        id: this.selectedInfrastructureAssetID,
                    });

                    this.selectedInfrastructureAssetID = null;
                }

                this.emitModelUpdate();

                this.loadingInfrastructureAsset = false;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        loadUpstreamInfrastructureAsset() {
            axios.get(this.upstreamInfrastructureAssetUrl).then((response) => {

                // If it's not a pipe continue
                if (!response.data.geom.hasOwnProperty('numpoints')) {
                    // If there are allowedTypes set and the selectedIAsset has an allowedType, continue
                    if (this.allowedInfrastructureAssetTypes.length > 0 && this.allowedInfrastructureAssetTypeIds.includes(response.data.infrastructure_asset_type_id)) {
                        this.selectUpstream(response.data);
                    } else if (this.allowedInfrastructureAssetTypes.length === 0) {
                        setNextStep(response.data);
                        // Else, throw a wrong type error
                    } else {
                        this.handleUpstreamError();
                    }
                } else {
                    this.handleUpstreamError();
                }

                this.emitModelUpdate();
                this.loadingUpstream = false;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        loadDownstreamInfrastructureAsset() {
            axios.get(this.downstreamInfrastructureAssetUrl).then((response) => {

                // If it's not a pipe continue
                if (!response.data.geom.hasOwnProperty('numpoints')) {
                    // If there are allowedTypes set and the selectedIAsset has an allowedType, continue
                    if (this.allowedInfrastructureAssetTypes.length > 0 && this.allowedInfrastructureAssetTypeIds.includes(response.data.infrastructure_asset_type_id)) {
                        this.selectDownstream(response.data);
                    }
                    else if ( this.allowedInfrastructureAssetTypes.length === 0) {
                        this.selectDownstream(response.data);
                        // Else, throw a wrong type error
                    } else {
                        this.handleDownstreamError();
                    }
                } else {
                    this.handleDownstreamError();
                }

                this.emitModelUpdate();
                this.loadingDownstream = false;
            })
            .catch((error) => {
                console.log(error);
            });
        },

        handleUpstreamError() {
            this.mapSelectError = true;
            // Add an upstream error if they've selected something with more than 1 point
            this.map.removeFeatureState({
                source: 'tiles',
                sourceLayer: 'infrastructure_asset_points',
                id: this.selectedUpstreamID,
            });

            this.selectedUpstreamID = null;
        },

        handleDownstreamError() {
            this.mapSelectError = true;
            this.map.removeFeatureState({
                source: 'tiles',
                sourceLayer: 'infrastructure_asset_points',
                id: this.selectedDownstreamID,
            });

            this.selectedDownstreamID = null;
        },

        selectInfrastructureAsset(infrastructureAsset) {
            if (infrastructureAsset.geom.numpoints > 1) {
                this.nextStep = 'upstream';
            }

            this.selectedInfrastructureAsset = infrastructureAsset;
        },

        selectUpstream(upstream) {
            if (this.selectedDownstream) {
                this.nextStep = null;
            } else {
                this.nextStep = 'downstream';
            }
            this.selectedUpstream = upstream;
        },

        selectDownstream() {
            this.nextStep = null;
            this.selectedDownstream = downstream;
        },

        mapClick(e, map) {

            if (!this.idleMap) {
                return;
            }

            if (
                this.loadingInfrastructureAsset ||
                this.loadingUpstream ||
                this.loadingDownstream
            ) {
                return;
            }

            const bbox = this.getBufferedBoundingBoxFromPoint(e.point);
            this.map = map;

            const features = map.queryRenderedFeatures(bbox, {
                layers: [
                    'infrastructure-asset-lines',
                    'infrastructure-asset-points',
                ],
            });

            // TODO: If in confirmation mode && features[0].properties.id !== assignmentgroup.infrastructureAsset.id
            // return

            if (
                this.data?.infrastructureAsset &&
                !this.selectedInfrastructureAssetID &&
                this.confirmationOnly &&
                features[0].properties.id !== this.data?.infrastructureAsset?.id
            ) {
                return;
            }

            if (features[0]) {
                const infrastructureAssetID = features[0].properties.id;

                if (features[0].sourceLayer) {
                    // Remove the feature state from the previously selected feature

                    // if (this.featureClicked) {
                    //     map.removeFeatureState({
                    //         source: this.featureClicked.source,
                    //         sourceLayer: this.featureClicked.sourceLayer,
                    //         id: this.featureClicked.id,
                    //     })
                    // }

                    // Set selected infrastructure asset feature state
                    if (!this.selectedInfrastructureAsset && !this.nextStep) {
                        map.setFeatureState(
                            {
                                source: features[0].source,
                                sourceLayer: features[0].sourceLayer,
                                id: infrastructureAssetID,
                            },
                            {
                                selected: true,
                            }
                        );

                        this.selectedInfrastructureAssetID =
                            infrastructureAssetID;
                        this.featureClicked = features[0];
                    } else if (this.nextStep === 'upstream') {
                        // Set upstream feature state

                        map.setFeatureState(
                            {
                                source: features[0].source,
                                sourceLayer: features[0].sourceLayer,
                                id: infrastructureAssetID,
                            },
                            {
                                upstream: true,
                            }
                        );

                        this.selectedUpstreamID = infrastructureAssetID;
                        // Set downstream feature state
                    } else if (this.nextStep === 'downstream') {
                        map.setFeatureState(
                            {
                                source: features[0].source,
                                sourceLayer: features[0].sourceLayer,
                                id: infrastructureAssetID,
                            },
                            {
                                downstream: true,
                            }
                        );

                        this.selectedDownstreamID = infrastructureAssetID;
                    }
                }
            }
        },

        getBufferedBoundingBoxFromPoint(point, buffer = 10) {
            return [
                [point.x - buffer, point.y - buffer],
                [point.x + buffer, point.y + buffer],
            ];
        },

        resetSeMapSelect() {
            this.map.removeFeatureState({
                source: 'tiles',
                sourceLayer: 'infrastructure_asset_linestrings',
                id: this.selectedInfrastructureAssetID,
            });

            this.map.removeFeatureState({
                source: 'tiles',
                sourceLayer: 'infrastructure_asset_points',
                id: this.selectedInfrastructureAssetID,
            });

            this.resetUpstream();
            this.resetDownstream();

            this.nextStep = null;
            this.featureClicked = null;
            this.selectedInfrastructureAssetID = null;
            this.selectedInfrastructureAsset = null;
            this.loadingInfrastructureAsset = false;

            this.emitModelUpdate();
        },

        skipUpstream() {
            this.selectedUpstreamID = null;
            this.upstreamError = false;
            this.mapSelectError = false;
            this.skippedUpstream = true;
            this.nextStep = 'downstream';

            this.emitModelUpdate();
        },

        skipDownstream() {
            this.selectedDownstreamID = null;
            this.upstreamError = false;
            this.mapSelectError = false;
            this.skippedDownstream = true;
            this.nextStep = null;

            this.emitModelUpdate();
        },

        resetUpstream() {
            if (this.selectedUpstreamID) {
                this.map.removeFeatureState({
                    source: 'tiles',
                    sourceLayer: 'infrastructure_asset_points',
                    id: this.selectedUpstreamID,
                });
            }

            this.mapSelectError = false;
            this.upstreamError = false;
            this.nextStep = 'upstream';
            this.selectedUpstream = null;
            this.selectedUpstreamID = null;
            this.loadingUpstream = false;
            this.skippedUpstream = false;

            this.emitModelUpdate();
        },

        resetDownstream() {
            if (this.selectedDownstreamID) {
                this.map.removeFeatureState({
                    source: 'tiles',
                    sourceLayer: 'infrastructure_asset_points',
                    id: this.selectedDownstreamID,
                });
            }

            this.downstreamError = false;
            this.nextStep = 'downstream';
            this.selectedDownstream = null;
            this.selectedDownstreamID = null;
            this.loadingDownstream = false;
            this.skippedDownstream = false;

            this.emitModelUpdate();
        },

        mapLoad(e) {
            this.map = e;
            this.setMapBounds();
        },

        firstMapIdle() {
            this.idleMap = true;
            this.setFeatureStates();
        },

        setFeatureStates() {
            if (this.confirmationOnly && !this.proxyModel?.selected_infrastructure_asset_id && this.data?.infrastructureAsset?.id) {
                this.map.setFeatureState(
                    {
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_linestrings',
                        id: this.data?.infrastructureAsset?.id,
                    },
                    {
                        selected: true,
                    }
                );

                this.map.setFeatureState(
                    {
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_points',
                        id: this.data?.infrastructureAsset?.id,
                    },
                    {
                        selected: true,
                    }
                );
            }

            if (this.proxyModel?.selected_infrastructure_asset_id) {
                this.selectedInfrastructureAssetID = this.proxyModel.selected_infrastructure_asset_id;

                // TODO: How do I know if the selected infrastructure asset is a pipe or node?
                // I would need to wait for the infrastructure asset info to load in
                // If Pipe
                // source = 'tiles
                // sourcelayer = infrastructure_asset_line_strings

                // If Node
                // source = 'tiles'
                // sourceLayer = infrastructure_asset_points

                // Just do both cuz why not??
                this.map.setFeatureState(
                    {
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_linestrings',
                        id: this.proxyModel.selected_infrastructure_asset_id,
                    },
                    {
                        selected: true,
                    }
                );

                this.map.setFeatureState(
                    {
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_points',
                        id: this.proxyModel.selected_infrastructure_asset_id,
                    },
                    {
                        selected: true,
                    }
                );
            }

            if (this.proxyModel?.selected_upstream_id) {
                this.selectedUpstreamID = this.proxyModel.selected_upstream_id;

                this.map.setFeatureState(
                    {
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_points',
                        id: this.proxyModel.selected_upstream_id,
                    },
                    {
                        upstream: true,
                    }
                );
            }

            if (this.proxyModel?.selected_downstream_id) {
                this.selectedDownstreamID =
                    this.proxyModel.selected_downstream_id;

                this.map.setFeatureState(
                    {
                        source: 'tiles',
                        sourceLayer: 'infrastructure_asset_points',
                        id: this.proxyModel.selected_downstream_id,
                    },
                    {
                        downstream: true,
                    }
                );
            }

            if (this.proxyModel?.selected_infrastructure_asset_id) {
                if (!this.proxyModel.selected_upstream_id) {
                    this.skippedUpstream = true;
                }

                if (!this.proxyModel.selected_downstreamstream_id) {
                    this.skippedDownstream = true;
                }
            }
        },

        emitModelUpdate() {

            let geom = null;
            if (this.selectedInfrastructureAsset?.geom?.points) {
                geom = this.selectedInfrastructureAsset.geom.points[0];
            } else {
                geom = this.selectedInfrastructureAsset?.geom;
            }

            if(!geom) {
                return;
            }

            this.$emit('update:modelValue', {
                lat: geom.y,
                lng: geom.x,
                coordinates:this.modelValue?.coordinates, //not sure on format, so use original format for now.
                map_zoom: this.map.getZoom(),
                selected_infrastructure_asset_id: this.selectedInfrastructureAssetID,
                selected_upstream_id: this.selectedUpstreamID,
                selected_downstream_id: this.selectedDownstreamID,
                selected_infrastructure_asset_uuid: this.selectedInfrastructureAsset.uuid,
                selected_infrastructure_asset_reference: this.selectedInfrastructureAsset.reference,
            });
        },

        setMapBounds() {
            if (
                this.proxyModel?.selected_infrastructure_asset_id ||
                !this.data.infrastructureAsset
            ) {
                return;
            }
            let geomArr = [];
            if (this.data.infrastructureAsset.geom?.points) {
                geomArr = [this.data.infrastructureAsset.geom.points[0]];
            } else {
                geomArr = [this.data.infrastructureAsset.geom];
            }

            const lngs = [];
            const lats = [];

            geomArr.forEach((geom) => {
                lngs.push(geom.x);
                lats.push(geom.y);
            });

            const lngMin = Math.min(...lngs);
            const lngMax = Math.max(...lngs);
            const latMin = Math.min(...lats);
            const latMax = Math.max(...lats);

            const bounds = [
                [latMin - this.boundsBuffer, lngMin - this.boundsBuffer],
                [latMax + this.boundsBuffer, lngMax + this.boundsBuffer],
            ];

            this.map.fitBounds(bounds);
        },
    },
};
</script>
