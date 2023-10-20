import { reactive } from 'vue';
import Aside from '../Models/Aside';
import genKey from './genKey';
import setupDevtools from '../Devtools/_asidesDevtools';
import { confirm } from './Dialogs';

// Because this is a module, it should
// act as a singleton.
// using asides instead of this doesn't work in tests
// for some reason.
const asides = reactive({ // implements ./Interfaces/StackedInterface.js
    asides: reactive([]),
    push(name, data = {})  {
        if(!data.key) {
            data.key = genKey();
        }
        asides.asides.push(
            reactive(new Aside(name, data))
        );
        document.body.classList.add('overflow-y-hidden');
        document.activeElement?.blur();
    },
    get(index) {
        return asides.asides[index];
    },
    async pop(amount = 1)  {
        for(let i = 0; i < amount; i += 1) {
            if (asides.current?.confirmClose) {
                // eslint-disable-next-line no-await-in-loop
                const confirmed = await confirm('Are you sure?', 'Any unsaved changes may be lost!');

                if (!confirmed) {
                    return;
                }
            }
            asides.asides.pop();
        }
        if(asides.asides.length === 0) {
            document.body.classList.remove('overflow-y-hidden');
        }
    },
    async replaceCurrent(name, data = {}) {
        await asides.pop();
        setTimeout(() => {
            asides.push(name, data);
        }, 400) // must be more than the transition out time. 300ms + 50ms buffer
    },
    clear() {
        asides.asides = reactive([])
    },
    findByKey(key) {
        return asides.asides.find(item => item.data.key === key);
    },
    get current() {
        return asides.asides[asides.asides.length - 1];
    },
    get all() {
        return asides.asides;
    }
})

export default asides;

export const AsidesPlugin = {
    install: (app) => {
        app.config.globalProperties.$asides = asides
        setupDevtools(app, asides);
    }
}

// If the naming is an issue:
// import { all as allAsides } from './Asides';
export const { clear, push, pop, get, replaceCurrent } = asides;
export const current = () => asides.current;
export const all = () => asides.all;
