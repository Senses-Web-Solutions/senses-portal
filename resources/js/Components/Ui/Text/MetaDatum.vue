<template>
    <div class="grid grid-cols-3 items-center">
        <div class="col-span-1">
            <SeLabel>
                <slot name="label"></slot>
            </SeLabel>
        </div>
        <div class="col-span-2">
                <slot name="data"></slot>
        </div>
    </div>
</template>
<script>

import SeLabel from '../../Ui/Inputs/SeLabel.vue';

export default {
    components: {
        SeLabel
    }
}

</script>
