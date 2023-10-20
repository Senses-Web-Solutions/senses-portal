import axios from 'axios';
import { ref } from 'vue';
import user from './user';

// The idea behind this is to update the user setting on the backend when the ref value is mutated
export default function useUserSetting(key, loadFunction = null) {
    const loaded = ref(false);
    const value = ref(null);

    // eslint-disable-next-line no-use-before-define
    // const { pause: pauseWatch, resume: resumeWatch } = pausableWatch(value, (val) => set(val), { deep: true });

    const get = async () => {
        const { data } = await axios.get(`/api/v2/users/${user().id}/user-settings/${key}`);

        // pauseWatch();
        value.value = data.data;

        if(loadFunction) {
            loadFunction(data.data);
        }
        // nextTick(() => resumeWatch());
        // loaded.value = true;
    }

    get();

    // EventHub.on('user-setting-updated', (setting) => {
    //     console.log('---emitted');
    //     if (setting === key) {
    //         get();
    //     }
    // })

    // const set = async (val) => {
    //     // console.log('useUserSetting set', val);
    //     // const { data } = await axios.patch(`/api/v2/users/${user().id}/user-settings/${key}`, val);
    //     // pauseWatch();
    //     // value.value = data.data;
    //     // console.log('---set');
    //     // EventHub.emit('user-setting-updated', key);
    //     nextTick(() => resumeWatch());
    // }

    return value;
}