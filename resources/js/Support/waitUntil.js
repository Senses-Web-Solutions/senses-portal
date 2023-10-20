import { watch } from "vue";

/**
 * Asynchronously waits for a vue ref to evaluate to truthy.
 * @param {ref} vueRef - The ref to monitor.
 */
export default function waitUntil(vueRef) {
    return new Promise((resolve) => {
        if(vueRef.value) {
            resolve(vueRef.value);
            return;
        }
        const stopWatching = watch(vueRef, () => {
            if(vueRef.value) {
                stopWatching();
                resolve(vueRef.value);
            }
        });
    });
}