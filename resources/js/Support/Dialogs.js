import { reactive } from 'vue';
import Dialog from '../Models/Dialog';
// import setupDevtools from '../Devtools/_dialogsDevtools';

const dialogs = reactive({
    dialog: null,
    info(title, subtitle) {
        return dialogs.create('info', { title, subtitle });
    },
    confirm(title, subtitle) {
        return dialogs.create('confirm', { title, subtitle });
    },
    danger(title, subtitle) {
        return dialogs.create('danger', { title, subtitle });
    },
    primary(title, subtitle) {
        return dialogs.create('primary', { title, subtitle });
    },
    secondary(title, subtitle) {
        return dialogs.create('secondary', { title, subtitle });
    },
    success(title, subtitle) {
        return dialogs.create('success', { title, subtitle });
    },
    warning(title, subtitle) {
        return dialogs.create('warning', { title, subtitle });
    },
    reset() {
        dialogs.dialog = null;
    },
    create(type, data) {
        return new Promise((resolve) => {
            dialogs.dialog = new Dialog(type, data, resolve);
        });
    },
});

export default dialogs;

export const DialogsPlugin = {
    install: (app) => {
        app.config.globalProperties.$dialogs = dialogs;
        // setupDevtools(app, dialogs);
    },
};

// If the naming is an issue:
// import { info as infoDialog } from './Dialogs';
export const { info, confirm, reset } = dialogs;
export const current = () => dialogs.dialog;
