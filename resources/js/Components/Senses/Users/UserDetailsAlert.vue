<template>
    <InfoAlert>
        <template #title>You have selected <a :href="'/users/' + user.id"><strong>{{ user.full_name }}</strong></a></template>
        <div class="space-y-1">
            <a :href="'tel:' + user.telephone"><p v-if="user.telephone">{{ user.telephone }}</p></a>
            <a :href="'tel:' + user.mobile"><p v-if="user.mobile">{{ user.mobile }}</p></a>
            <a :href="'mailto:' + user.email"><p v-if="user.email">{{ user.email }}</p></a>
        </div>
    </InfoAlert>
</template>
<script>
import InfoAlert from '../../Ui/Alerts/InfoAlert.vue';

export default {
    components: {InfoAlert},
    props: {
        user: {
            type: Object,
            required: true
        }
    }
}
</script>
