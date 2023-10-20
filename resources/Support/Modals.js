import { reactive } from 'vue';
import Modal from '../Models/Modal';
import genKey from './genKey';
import { onKeyStroke } from '@vueuse/core';
// import setupDevtools from '../Devtools/_modalsDevtools';

// Because this is a module, it should
// act as a singleton.
// using modals instead of this doesn't work in tests
// for some reason.
const modals = reactive({
    // implements ./Interfaces/StackedInterface.js
    modals: reactive([]),
    push(name, data = {}) {
        if (!data.key) {
            data.key = genKey();
        }
        modals.modals.push(reactive(new Modal(name, data)));
        document.body.classList.add('overflow-y-hidden');

        return data.key;
    },
    pop(amount = 1) {
        for (let i = 0; i < amount; i += 1) {
            modals.modals.pop();
        }
        if (modals.modals.length === 0) {
            document.body.classList.remove('overflow-y-hidden');
        }
    },
    clear() {
        modals.modals = reactive([]);
    },
    findByKey(key) {
        return modals.modals.find((item) => item.data.key === key);
    },
    get current() {
        return modals.modals[modals.modals.length - 1];
    },
    get all() {
        return modals.modals;
    },
});

onKeyStroke('Escape', () => {
    if (modals.modals.length > 0) {
        modals.pop();
    }
});

export default modals;

export const ModalsPlugin = {
    install: (app) => {
        app.config.globalProperties.$modals = modals;
        // setupDevtools(app, modals);
    },
};

// If the naming is an issue:
// import { all as allAsides } from './Asides';
export const { push, pop, clear } = modals;
export const current = () => modals.current;
export const all = () => modals.all;
