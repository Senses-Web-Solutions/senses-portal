<template>
    <div>
        <component
            :is="DialogComponent"
            :open="open"
            :data="currentDialog()?.data"
            :resolve="resolve"
            @vue:mounted="dialogMounted"
            @after-leave="afterLeave"
        ></component>
    </div>
</template>
<script>
import { shallowRef, defineAsyncComponent, nextTick, ref, watch } from 'vue';
import { current as currentDialog, reset as resetDialog } from '../../Support/Dialogs';

export default {
    setup(props, { emit }) {
        // Shallow ref because we don't want to convert a
        // component to be reactive
        const DialogComponent = shallowRef(null);
        const open = ref(false);

        // Watch the current dialog
        watch(currentDialog, () => {
            // If it is set and contains a type
            if(currentDialog() && currentDialog().type) {
                // Uppercase the first character
                const type = currentDialog().type.charAt(0).toUpperCase() + currentDialog().type.slice(1);
                // Dynamically load in the component
                DialogComponent.value = defineAsyncComponent(() => import(`./Dialogs/${type}Dialog.vue`));
            } else {
                // If the dialog is null: get rid of the component
                DialogComponent.value = null;
            }
        })

        // This allows the transition to finish before
        // removing the component from the DOM
        const afterLeave = () => {
            resetDialog();
        }

        const dialogMounted = (e) => {
            // Two events bubble up, one is the AsyncComponentWrapper which
            // we DONT want. We want OUR component to be the one that
            // triggers the dialog to open otherwise the transitions
            // won't play.
            if(e.type.name !== 'AsyncComponentWrapper') {
                nextTick(() => open.value = true)
            }
        };

        // Wrap the dialog's resolve function so we can
        // close the modal.
        const resolve = (...data) => {
            open.value = false;
            currentDialog().resolve(...data);
        }

        return { DialogComponent, open, afterLeave, resolve, dialogMounted, currentDialog }
    }
}
</script>
