import { computed } from 'vue';

export default function useCurrentSchedulerTemplate(setting) {
    return computed({
        get() {
            if (setting.value) {
                return setting.value.templates[setting.value.currentTemplateName];
            }
            return {};
        },
        set(value) {
            if (setting.value) {
                setting.value.templates[setting.value.currentTemplateName] = value;
            }
        }
    });
}