<template>
    <InputGroup
        :id="id"
        :label="label"
        :is-valid="isValid"
        :error="error"
        :name="name"
        inset-validation-icon
    >
        <MapboxMap
            :access-token="mapboxToken"
            :zoom="zoom"
            map-style="mapbox://styles/mapbox/light-v10"
            :center="center"
            :fit-to-all="false"
            class="h-[35vh] select-none rounded-lg"
            @firstidle="firstMapIdle"
            @load="mapLoad"
        >
        <MapboxDraw @load="mapDrawLoad" @area-updated="onDrawAreaUpdated" v-if="!editForm"></MapboxDraw>
        </MapboxMap>
        <div
            v-if="!idleMap"
            class="flex items-center mt-4"
        >
            <LoadingIcon class="mr-2 h-6 w-6" />
            <p>Initialising Map</p>
        </div>
        <div v-else class="mt-4">
            <!-- <p v-if="editForm">Click the polygon you wish to edit.</p> -->
        </div>
    </InputGroup>
</template>
<script>
import InputGroup from './InputGroup.vue';

import MapboxMap from '../../Senses/Mapbox/MapboxMap.ts';
import MapboxDraw from '../../Senses/Mapbox/MapboxDraw.ts';
import LoadingIcon from '../LoadingIcon.vue';

const mapboxToken = import.meta.env.VITE_MAPBOX_TOKEN;

export default {
    components: {
        InputGroup,
        MapboxMap,
        MapboxDraw,
        LoadingIcon,
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
            default: 'Map Draw',
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
        data: {
            type: Object,
            required: true,
        },

    },
    emits: ['update:modelValue'],

    data() {
        return {
            draw: null,
            coordinates: null,
            map: null,
            idleMap: false,
            boundsBuffer: 0.001,
            mapboxToken,
            zoom: 5,
            center: [-2.7735199, 54.3555337]
        };
    },
    computed: {
        editForm() {
            return !!this.data?.formId;
        },
        drawPreviousModelValue() {
            return this.draw !== null && this.map !== null && this.parsedModelValueCoordinates && this.editForm;
        },
        parsedModelValueCoordinates() {
            if (this.modelValue?.coordinates) {
                return JSON.parse(JSON.stringify(this.modelValue?.coordinates));
            }

            return null;
        },
        isValid() {
            if (this.error && this.error.errors) {
                return !this.error.errors[this.name];
            }
            return true;
        },
    },
    watch: {
        drawPreviousModelValue(newVal) {
            if (newVal && this.modelValue?.coordinates) {
                this.draw.add({
                    type: 'Feature',
                    geometry: { type: 'Polygon', coordinates: [this.parsedModelValueCoordinates] }
                })
            }
        }
    },
    mounted() {
        if (this.modelValue?.map_center) {
            this.center = this.modelValue.map_center;
        }
        if (this.modelValue?.map_zoom) {
            this.zoom = this.modelValue.map_zoom;
        }
    },
    methods: {
        getBufferedBoundingBoxFromPoint(point, buffer = 10) {
            return [
                [point.x - buffer, point.y - buffer],
                [point.x + buffer, point.y + buffer],
            ];
        },
        // Map Events
        firstMapIdle() {
            this.idleMap = true;
        },
        mapLoad(e) {
            this.map = e;
            const map = e;

            let geomArr = [];
            if (this.parsedModelValueCoordinates) {
                geomArr = this.parsedModelValueCoordinates;
                const lngs = [];
                const lats = [];

                geomArr.forEach(geom => {
                    lats.push(geom[0]);
                    lngs.push(geom[1]);
                })

                const lngMin = Math.min(...lngs);
                const lngMax = Math.max(...lngs);
                const latMin = Math.min(...lats);
                const latMax = Math.max(...lats);

                const bounds = [
                    [
                        latMin - this.boundsBuffer,
                        lngMin - this.boundsBuffer,
                    ],
                    [
                        latMax + this.boundsBuffer,
                        lngMax + this.boundsBuffer,
                    ],
                ];

                map.fitBounds(bounds);
            }

        },
        // Map Draw Events
        mapDrawLoad({draw}) {
            this.draw = draw;
        },
        onDrawAreaUpdated ({ draw }) {
            const allPoints = draw.getAll();
            if (allPoints.features[0]) {
                this.coordinates = allPoints.features[0].geometry.coordinates[0];
            } else {
                this.coordinates = [];
            }
            this.emitModelUpdate();
        },
        // ModelValue Binding
        emitModelUpdate() {
            if (this.coordinates.length === 0) {
                this.$emit('update:modelValue', null);
            } else {
                this.$emit('update:modelValue', {
                    coordinates: this.coordinates,
                    map_zoom: this.map.getZoom(),
                    map_center: this.map.getCenter(),
                });
            }
        },
    },
};
</script>
