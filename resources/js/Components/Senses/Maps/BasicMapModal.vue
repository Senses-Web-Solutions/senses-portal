<template>
    <div class="h-[80vh] w-[80vw] m-3 relative rounded-lg overflow-hidden">
        <BasicMap :markers="data.markers" class="h-[80vh] w-[80vw]" />
    </div>
</template>
<script>
import Modal from '../../../Mixins/Modal';
import BasicMap from './BasicMap.vue';

export default {
    components: {
        BasicMap,
    },
    mixins: [Modal],
}
</script>