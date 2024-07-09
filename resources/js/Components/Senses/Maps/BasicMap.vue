<template>
    <Map
        :show-controls="showControls"
        :center="center"
        :zoom="zoom"
        :pitch="pitch"
        @load="map = $event"
    >
        <MapMarkers 
            v-if="showMarkers"
            :map="map"
            :markers="markers"
        />
    </Map>
</template>
<script>
import Map from './Map.vue';
import MapMarkers from './MapMarkers.vue';
import MapHelpers from '../../../Support/Map/MapHelpers';

export default {
    components: {
        Map,
        MapMarkers,
    },
    mixins: [MapHelpers],
    props: {
        id: {
            type: String,
            default: 'basic-map-lines' 
        },
        markers: {
            type: Array,
            required: true,
        },
        routes: {
            type: Array,
            default: () => [],
        },
        boundsBuffer: {
            type: Number,
            default: 0.001,
        },
        showControls: {
            type: Boolean,
            default: true,
        },
        center: {
            type: Array,
            default: () => [-3, 55],
        },
        zoom: {
            type: Number,
            default: 5,
        },
        pitch: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            map: null,
            pathLayerOptions: {
                getColor: (d) => d.colour,
                getPath: d => d.path,
                getWidth: 3,
                widthMinPixels: 3,
                capRounded: true,
                jointRounded: true,
            },
            textLayerOptions: {
                getText: d => d.name,
                getPosition: d => this.calculateCenter(d.path), // or calculate the center of the path
                getColor: [0, 0, 0, 200], // RGBA
                getSize: 10,
                getAngle: 0,
                getTextAnchor: 'middle',
                getAlignmentBaseline: 'center',
                getPixelOffset: [0, 10],
                getBackgroundColor: [255, 255, 255, 255],
                getBackgroundPadding: 2,
            },
        };
    },
    computed: {
        showMarkers() {
            return this.markers.length > 0 && this.map !== null;
        },
        showRoutes() {
            return this.routes.length > 0 && this.map != null;
        },
        points() {
            const points = [];
            this.markers.forEach((marker) =>  {
                if(marker.coordinates.length === 0) {
                    return;
                }
                points.push(marker.coordinates);
            });
            this.routes.forEach((route)  => {
                route.path.forEach((path)  => {
                    points.push(path);
                })
            });

            return points;
        },
    },

    watch: {
        map() {
            if(this.map) {
                this.calculateAndFitBounds(this.points, this.boundsBuffer);
            }
        },
        center() {
            if(this.map) {
                this.map.setCenter(this.center);
            }
        },
    }
};
</script>