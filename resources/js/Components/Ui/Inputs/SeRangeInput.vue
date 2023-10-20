<script setup>
import { onMounted, ref, computed, toRefs } from 'vue';
import { useEventListener } from '@vueuse/core';

const props = defineProps({
    waypoints: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: Number,
        default: 0,
    },
    snap: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: null,
    },
    labelPosition: {
        type: String,
        default: 'right'
    },
    labelAlwaysVisible: {
        type: Boolean,
        default: false
    }
});

const { waypoints, modelValue, snap } = toRefs(props);

const emit = defineEmits(['update:modelValue']);

const track = ref(null);

const trackHeight = ref(0);
const dragging = ref(false);
const trackRect = ref(false);

const isSnapped = ref(false);

const processedPosition = computed(() => {
    if (snap.value) {
        // eslint-disable-next-line no-restricted-syntax
        for (const waypoint of waypoints.value) {
            if (Math.abs(modelValue.value - waypoint) < 5) {
                // eslint-disable-next-line vue/no-side-effects-in-computed-properties
                isSnapped.value = true;
                emit('update:modelValue', waypoint);
                return waypoint;
            }
        }
    }
    // eslint-disable-next-line vue/no-side-effects-in-computed-properties
    isSnapped.value = false;
    return modelValue.value;
});

onMounted(() => {
    trackHeight.value = track.value.clientHeight;
    trackRect.value = track.value.getBoundingClientRect();
});

function onMouseDown(e) {
    dragging.value = true;
}

function onMouseUp(e) {
    dragging.value = false;
}

function onMouseMove(e) {
    if (dragging.value) {
        emit(
            'update:modelValue',
            Math.max(
                0,
                Math.min(
                    100,
                    ((e.pageY - trackRect.value.top) / trackHeight.value) * 100
                )
            )
        );
    }
}

function onTrackClick(e) {
    if (!dragging.value) {
        emit(
            'update:modelValue',
            Math.max(
                0,
                Math.min(
                    100,
                    ((e.pageY - trackRect.value.top) / trackHeight.value) * 100
                )
            )
        );
    }
}

const mouseInsideHandle = ref(false);

function onMouseEnter() {
    mouseInsideHandle.value = true;
}

function onMouseLeave() {
    mouseInsideHandle.value = false;
}

useEventListener(document, 'mouseup', onMouseUp);
useEventListener(document, 'mousemove', onMouseMove);
</script>

<template>
    <div class="relative w-3">
        <div
            ref="handle"
            class="absolute left-1/2 z-10 h-5 w-5 -translate-x-1/2 -translate-y-1/2 cursor-ns-resize rounded-full bg-primary"
            :class="{
                'transition-all duration-75': isSnapped || !dragging,
            }"
            :style="{
                top: `${processedPosition}%`,
            }"
            @mouseenter="onMouseEnter"
            @mouseleave="onMouseLeave"
            @drag="onHandleDrag"
            @mousedown="onMouseDown"
        >
            <transition
                enter-active-class="transition ease-out duration-100"
                enter-from-class="transform opacity-0 scale-50"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-50"
            >
                <div
                    v-if="(label && (mouseInsideHandle || dragging)) || labelAlwaysVisible"
                    class="absolute top-1/2 -translate-y-1/2 select-none rounded bg-zinc-900 p-1 text-white"
                    :class="{
                        'left-full ml-3 origin-left': labelPosition === 'right',
                        'right-full mr-3 origin-right': labelPosition === 'left'
                    }"
                >
                    {{ label }}
                </div>
            </transition>
        </div>
        <div
            ref="track"
            class="h-full w-3 rounded-full bg-zinc-300"
            @click="onTrackClick"
        ></div>
        <div
            v-for="waypoint in waypoints"
            :key="`waypoint${waypoint}`"
            class="pointer-events-none absolute h-px w-full bg-zinc-600"
            :style="{
                top: `${waypoint}%`,
            }"
        ></div>
    </div>
</template>
