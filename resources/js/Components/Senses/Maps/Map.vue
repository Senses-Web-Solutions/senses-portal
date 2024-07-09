<template>
    <div :id="mapId" class="senses-map">
        <div v-if="disabled" class="z-[20] absolute inset-0 pointer-events-none bg-gray-200 bg-opacity-75 transition-opacity dark:bg-zinc-800 dark:bg-opacity-75 grid items-center">
            <EmptyState class="inter">{{disabledMessage}}</EmptyState>
        </div>

        <div v-if="showLoading && loading" class="z-[100] absolute flex items-center justify-center inset-0 pointer-events-none bg-black bg-opacity-25 transition-opacity dark:bg-zinc-800 dark:bg-opacity-75">
            <MapLoadingControl v-if="showLoading" :loading="loading" />
        </div>

        <div class="absolute left-4 top-4 z-20 flex space-x-3">
            <MapRouteTypeControl
                v-if="showControls && routeTypeControl"
                :ants="ants"
                @updateSetting="updateSetting"
            />

            <Map3DControl
                v-if="showControls && buildingControl"
                :three-dimensional="threeDimensional"
                @updateSetting="updateSetting"
            />

            <MapAnimationControl
                v-if="showControls && animationControl"
                :animate="animate"
                @updateSetting="updateSetting"
            />

            <slot name="controls"></slot>
        </div>

        <div class="absolute left-4 bottom-4 z-20">
            <MapDebugControl />
        </div>

        <slot name="modal"></slot>

        <slot></slot>
    </div>
</template>

<script>
import axios from 'axios';
import { computed } from 'vue';
import maplibregl from 'maplibre-gl';
import MapboxDraw from '@mapbox/mapbox-gl-draw';

import MapRouteTypeControl from './Controls/MapRouteTypeControl.vue';
import Map3DControl from './Controls/Map3DControl.vue';
import MapAnimationControl from './Controls/MapAnimationControl.vue';
import MapLoadingControl from './Controls/MapLoadingControl.vue';
import MapDebugControl from './Controls/MapDebugControl.vue';
import mapStyleLight from './Styles/Map/map_light_style.json';
import mapStyleDark from './Styles/Map/map_dark_style.json';

import LoadingIcon from '../../Ui/LoadingIcon.vue';
import EmptyState from '../../Ui/EmptyState.vue';

import user from '../../../Support/user';
import MapHelpers from '../../../Support/Map/MapHelpers';
import EventHub from '../../../Support/EventHub';

export default {
    components: {
        MapRouteTypeControl,
        MapAnimationControl,
        Map3DControl,
        MapLoadingControl,
        MapDebugControl,
        EmptyState,
        LoadingIcon,
    },

    mixins: [MapHelpers],

    provide() {
        return {
            map: computed(() => this.map),
            zoom: computed(() => this.liveZoom),
            fitToAll: computed(() => this.fitToAll),
            onMapLoad: this.onMapLoad,
            onMapCreated: computed(() => this.onMapCreated),
            onSourceLoad: computed(() => this.onSourceLoad),
            sourceLoaded: computed(() => this.sourceLoaded),

            ants: computed(() => this.ants),
            animate: computed(() => this.animate),
            light: computed(() => this.light),
            threeDimensional: computed(() => this.threeDimensional),
        };
    },

    props: {
        minZoom: {
            type: Number,
            default: 3,
        },

        maxZoom: {
            type: Number,
            default: 21,
        },

        center: {
            type: [Object, Array],
            default: () => [-3, 55],
        },

        zoom: {
            type: Number,
            default: 5,
        },

        zoomControl: {
            type: Boolean,
            default: false,
        },

        boundsBuffer: {
            type: Number,
            default: 0.3,
        },

        pitch: {
            type: Number,
            default: 0,
        },

        draw: {
            type: Boolean,
            default: false,
        },

        showControls: {
            type: Boolean,
            default: true,
        },

        showLoading: {
            type: Boolean,
            default: true,
        },

        routeTypeControl: {
            type: Boolean,
            default: true,
        },

        animationControl: {
            type: Boolean,
            default: true,
        },

        buildingControl: {
            type: Boolean,
            default: true,
        },

        autoPitch: {
            type: Boolean,
            default: true,
        },

        debug: {
            type: Boolean,
            default: false,
        },

        loading: {
            type: Boolean,
            default: false,
        },

        disabled: {
            type: Boolean,
            default: false,
        },

        disabledMessage: {
            type: String,
            default: 'Map Disabled',
        },
    },

    emits: [
        'load',
        'click',
        'mouseover',
        'mousemove',
        'mouseout',
        'movestart',
        'moveend',
        'zoomstart',
        'zoom',
        'zoomend',
        'unload',
        'drawcreate',
        'drawdelete',
        'drawupdate',
    ],

    data() {
        return {
            map: null,
            mapId: this.makeid(6),

            initialized: false,

            onMapLoadCallbacks: [],
            onMapCreatedCallbacks: [],
            onSourceLoadCallbacks: [],

            liveZoom: this.zoom,
            drawControl: null,

            userSetting: null,

            darkMode: (JSON.parse(localStorage.getItem("darkMode")) ?? false),
        };
    },

    computed: {
        building3DLayer() {
            return {
                "id": "building-3d",
                "type": "fill-extrusion",
                "source": "openmaptiles",
                "source-layer": "building",
                "minzoom": 14,
                "filter": ["all", ["!has", "hide_3d"]],
                "paint": {
                    "fill-extrusion-base": {
                        "property": "render_min_height",
                        "type": "identity"
                    },

                    "fill-extrusion-color": this.light ? "#DDDDDD" : "#222222",

                    "fill-extrusion-height": {
                        "property": "render_height",
                        "type": "identity"
                    },

                    "fill-extrusion-opacity": 0.8
                }
            };
        },

        mapStyle() {
            return this.light ? mapStyleLight : mapStyleDark;
        },

        mapOptions() {
            return {
                container: this.mapId,
                style: this.mapStyle,
                center: this.center,
                minZoom: this.minZoom,
                maxZoom: this.maxZoom,
                zoom: this.zoom,
                pitch: this.pitch,
                attributionControl: false,
                renderWorldCopies: false,
                antialias: true,
            };
        },

        userID() {
            return user().id ?? null;
        },

        settingUrl() {
            return `/api/v2/users/${this.userID}/user-settings/map`;
        },

        threeDimensional() {
            return this.userSetting?.data?.three_dimensional ?? true;
        },

        light() {
            return !this.darkMode;
            // return this.userSetting?.data?.light ?? true;
        },

        ants() {
            return this.userSetting?.data?.ants ?? true
        },

        animate() {
            return this.userSetting?.data?.animate ?? true
        },
    },

    watch: {
        threeDimensional(newVal) {
            if (newVal) {
                if (!this.map.getLayer('building-3d')) {
                    this.map.addLayer(this.building3DLayer, 'clusters');
                }
            } else if (this.map.getLayer('building-3d')) {
                this.map.removeLayer('building-3d');
            }
        },

        light(newVal) {
            if (this.map) {
                if (newVal) {
                    this.map.setStyle(mapStyleLight);
                } else {
                    this.map.setStyle(mapStyleDark);
                }
            }
        },

        disabled(newVal) {
            if (this.map) {
                if (newVal) {
                    this.disableMapInteractions();
                    this.disableMapEvents();
                } else {
                    this.setupMapInteractions();
                    this.setupMapEvents();
                }
            }
        },
    },

    created() {
        this.loadUserSetting();
    },

    mounted() {
        this.loadMap();

        EventHub.on('map:draw_mode', (on) => {
            this.toggleDrawMode(on);
        });

        EventHub.on('map:trash_mode', (on) => {
            this.toggleTrashMode(on);
        });

        EventHub.on('map:draw_pop', () => {
            this.removeLastDrawnLayer();
        });

        EventHub.on('map:draw_clear', () => {
            this.removeDrawnLayers();
        });

        EventHub.on('map:three_dimensional', (on) => {
            this.toggle3DMode(on);
        });

        EventHub.on('light-map', () => {
            this.toggleLightStyle();
        });

        EventHub.on('map:debug', (on) => {
            this.map.showTileBoundaries = on;
            this.map.showCollisionBoxes = on;
            // this.map.showPadding = on;
            this.showOverdrawInspector = on;
        });

        EventHub.on('darkMode', (on) => {
            this.darkMode = on;
        });
    },

    beforeUnmount() {
        EventHub.off('map:draw_mode');
        EventHub.off('map:trash_mode');
        EventHub.off('map:draw_clear');
        EventHub.off('map:draw_pop');
        EventHub.off('three-dimensional-map');
        EventHub.off('light-map');
        EventHub.off('map:debug');
        EventHub.off('darkMode');

        this.disableMapEvents();

        this.$emit('unload', this.map);
        this.map.remove();
        this.map = null;
    },

    methods: {
        loadUserSetting() {
            axios.get(this.settingUrl).then(response => {
                this.userSetting = response.data;
            });
        },

        updateSetting({ key, value }) {
            this.userSetting.data[key] = value;
            axios.put(this.settingUrl, this.userSetting.data);
        },

        makeid(length) {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            for (let i = 0; i < length; i += 1) {
                result += characters.charAt(Math.floor(Math.random() * characters.length));
            }

            return result;
        },

        loadMap() {
            this.initMap();
            this.initClientTiles();
            this.initDraw();
            this.setupMapEvents();
        },

        initMap() {
            this.map = new maplibregl.Map(this.mapOptions);

            if (this.disabled) {
                this.disableMapInteractions();
                this.disableMapEvents();
            }

            if (this.debug) {
                this.map.showTileBoundaries = true;
                this.map.showCollisionBoxes = true;
                // this.map.showPadding = true;
                this.showOverdrawInspector = true;
            }
        },

        initDraw() {
            if (this.draw) {
                this.drawControl = new MapboxDraw({
                    displayControlsDefault: false,
                });

                this.map.addControl(this.drawControl, 'top-left');
            }
        },

        setupMapEvents() {
            this.map.on('load', (e) => this.mapLoad(e));

            if (this.disabled) {
                return;
            }

            this.map.on('click', (e) => this.mapClick(e));
            this.map.on('movestart', (e) => this.mapMoveStart(e));
            this.map.on('moveend', (e) => this.mapMoveEnd(e));
            this.map.on('mouseover', (e) => this.mapMouseOver(e));
            this.map.on('mousemove', (e) => this.mapMouseMove(e));
            this.map.on('mouseout', (e) => this.mapMouseOut(e));
            this.map.on('zoomstart', (e) => this.mapZoomStart(e));
            this.map.on('zoomend', (e) => this.mapZoomEnd(e));
            this.map.on('draw.create', (e) => this.mapDrawCreate(e));
            this.map.on('draw.delete', (e) => this.mapDrawDelete(e));
            this.map.on('draw.update', (e) => this.mapDrawUpdate(e));
            this.map.on('draw.modechange', (e) => this.mapDrawModeChange(e));
        },

        disableMapEvents() {
            this.map.off('click');
            this.map.off('movestart');
            this.map.off('moveend');
            this.map.off('mouseover');
            this.map.off('mousemove');
            this.map.off('mouseout');
            this.map.off('zoomstart');
            this.map.off('zoomend');
            this.map.off('draw.create');
            this.map.off('draw.delete');
            this.map.off('draw.update');
            this.map.off('draw.modechange');
        },

        setupMapInteractions() {
            this.map.dragPan.enable();
            this.map.scrollZoom.enable();
            this.map.boxZoom.enable();
            this.map.keyboard.enable();
            this.map.doubleClickZoom.enable();
            this.map.touchZoomRotate.enable();
        },

        disableMapInteractions() {
            this.map.dragPan.disable();
            this.map.scrollZoom.disable();
            this.map.boxZoom.disable();
            this.map.keyboard.disable();
            this.map.doubleClickZoom.disable();
            this.map.touchZoomRotate.disable();
        },

        // Callbacks
        onMapLoad(callback) {
            if (this.map && this.initialized) {
                callback();
                return;
            }

            this.onMapLoadCallbacks.push(callback);
        },

        onMapCreated(callback) {
            if (this.map) {
                callback();
                return;
            }

            this.onMapCreatedCallbacks.push(callback);
        },

        onSourceLoad(id, callback) {
            if (this.map) {
                callback();
                return;
            }

            const found = this.onSourceLoadCallbacks.find(
                (source) => source.id === id
            );

            if (found) {
                found.callbacks.push(callback);
            } else {
                this.onSourceLoadCallbacks.push({
                    id,
                    callbacks: [callback],
                });
            }
        },

        sourceLoaded(id) {
            const found = this.onSourceLoadCallbacks.find(
                (source) => source.id === id
            );

            if (found) {
                found.callbacks.forEach((callback) => callback());
            }
        },

        // Helpers
        toggleDrawMode(on) {
            if (this.drawControl) {
                if (on) {
                    this.drawControl.changeMode('simple_select');
                    this.map.getCanvas().style.cursor = '';
                } else {
                    this.drawControl.changeMode('draw_polygon');
                    this.map.getCanvas().style.cursor = 'crosshair';
                }
            }
        },

        removeDrawnLayers() {
            if (this.drawControl) {
                this.drawControl.deleteAll();
            }
        },

        removeLastDrawnLayer() {
            if (this.drawControl !== null) {
                const data = this.drawControl.getAll();

                if (data.features.length) {
                    const layer = data.features[data.features.length - 1];
                    this.drawControl.delete(layer.id);
                    this.$emit('drawdelete', {e: layer, draw: this.drawControl});
                }

                this.drawControl.changeMode('simple_select');
                this.map.getCanvas().style.cursor = '';
            }
        },

        toggle3DMode(on) {
            if (this.map) {
                if (on) {
                    if (!this.map.getLayer('building-3d')) {
                        this.map.addLayer(this.building3DLayer, 'clusters');
                    }
                } else if (this.map.getLayer('building-3d')) {
                    this.map.removeLayer('building-3d');
                }
            }
        },

        toggleLightStyle() {
            const on = window.localStorage.getItem('light-map') === 'true';
            this.light = on;

            if (this.map) {
                if (on) {
                    this.map.setStyle(mapStyleLight);
                } else {
                    this.map.setStyle(mapStyleDark);
                }

                if (this.threeDimensional) {
                    if (this.map.getLayer('building-3d')) {
                        this.map.removeLayer('building-3d');
                    }

                    this.map.addLayer(this.building3DLayer, 'clusters');
                } else if (this.map.getLayer('building-3d')) {
                    this.map.removeLayer('building-3d');
                }
            }
        },

        // EVENTS
        mapLoad(e) {
            if (this.threeDimensional) {
                if (!this.map.getLayer('building-3d')) {
                    this.map.addLayer(this.building3DLayer, 'clusters');
                }
            }

            this.initialized = true;
            this.onMapLoadCallbacks.forEach((callback) => callback());
            this.onMapLoadCallbacks = [];
            this.$emit('load', this.map);
        },

        mapClick(e) {
            this.$emit('click', e, this.map);

            // console log map features that you have clicked
            const features = this.map.queryRenderedFeatures(e.point);
            console.log('Features Clicked', features);
        },

        mapMoveStart(e) {
            this.$emit('movestart', e, this.map);
        },

        mapMoveEnd(e) {
            this.$emit('moveend', e, this.map);
        },

        mapMouseOver(e) {
            this.$emit('mouseover', e, this.map);
        },

        mapMouseMove(e) {
            this.$emit('mousemove', e, this.map);
        },

        mapMouseOut(e) {
            this.$emit('mouseout', e, this.map);
        },

        mapZoomStart(e) {
            this.$emit('zoomstart', e, this.map);
        },

        mapZoomEnd(e) {
            if (this.map) {
                const zoom = this.map.getZoom();

                if (this.autoPitch) {
                    const currentPitch = this.map.getPitch();
                    const targetPitch = zoom > 13 ? 40 : 0;

                    const shouldPitch = (currentPitch !== targetPitch && currentPitch < 5) || (zoom < 12 && currentPitch !== 0);

                    if (shouldPitch) {
                        this.map.easeTo({ pitch: targetPitch, duration: 750 });
                    }
                }

                this.liveZoom = zoom;
            }

            this.$emit('zoomend', e, this.map);
        },

        mapDrawCreate(e) {
            EventHub.emit('map:draw_off');
            this.$emit('drawcreate', {e, draw: this.drawControl});
        },

        mapDrawDelete(e) {
            this.$emit('drawdelete', {e, draw: this.drawControl});
        },

        mapDrawUpdate(e) {
            this.$emit('drawupdate', {e, draw: this.drawControl});
        },

        mapDrawModeChange(e) {
            if (e.mode === 'draw_polygon' || e.mode === 'draw_line_string') {
                this.map.getCanvas().style.cursor = 'crosshair';
            } else {
                this.map.getCanvas().style.cursor = '';
            }
        },
    },
};

</script>

<style>
.senses-map {
    /* position: absolute;
    top: 0;
    bottom: 0;
    width: 100%; */
}

.inter {
    font-family: 'Inter', sans-serif;
}

</style>
