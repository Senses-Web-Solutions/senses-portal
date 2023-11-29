import { createApp } from 'vue';
import axios from 'axios';
import { onKeyDown } from '@vueuse/core';
import { debounce, throttle } from 'lodash-es';
import { createPinia } from 'pinia';

import Flipper from './Components/Ui/Flip/Flipper.vue';
import Flipped from './Components/Ui/Flip/Flipped.vue';
import EventHub from './Support/EventHub';
import user from './Support/user';

import * as Layout from './Components/Layout';
import * as Components from './Components';

import Notifications from './Components/Ui/Notifications.vue';
import { NotificationsPlugin } from './Support/Notifications';

import Snackbars from './Components/Ui/Snackbars.vue';
import { push as pushSnackbar, SnackbarsPlugin } from './Support/Snackbars';

import Dialogs from './Components/Ui/Dialogs.vue';
import { DialogsPlugin } from './Support/Dialogs';
import { AsidesPlugin } from './Support/Asides';
import { ModalsPlugin } from './Support/Modals';
import highlight from './Support/highlight';

import { loadClientConfig } from './Support/client';

const client = import.meta.env.VITE_SENSES_CLIENT;

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const url = new URL(window.location.href);
window.showCacheLogs = url.searchParams.get('cache') !== null;

if (window.showCacheLogs) {
    axios.interceptors.response.use(
        (response) => {
            if (response.headers['senses-cached-at'] !== undefined) {
                console.log(
                    'cache hit',
                    response.request.responseURL,
                    response.headers['senses-cached-at']
                );
            } else {
                console.warn('cache miss', response.request.responseURL, null);
            }
            return response;
        },
        // Any status codes that falls outside the range of 2xx cause this function to trigger
        // Do something with response error
        (error) => Promise.reject(error)
    );
}

window.onerror = throttle((msg, errUrl, lineNo, columnNo, error) => {
    axios.post('/api/v2/errors', {
        message: msg,
        from: 'frontend',
        status: 1,
        data: JSON.stringify({
            url: errUrl,
            lineNo,
            columnNo,
            error: JSON.stringify(error, Object.getOwnPropertyNames(error))
        }),
        url: window.location.href
    }).then(() => {
        pushSnackbar({
            title: 'Something went wrong',
            description: 'We\'ve reported the issue. If it persists, hop on livechat.',
            type: 'danger'
        });
        // console.log('We found a bug and stored it in our collection! 🐛');
    }).catch(() => {
        pushSnackbar({
            title: 'Error reporting failed.',
            description: 'We tried to catch a bug, but it got away. Can you let us know via livechat.',
            type: 'danger'
        });
        console.log('We tried to catch a bug, but there was a hole in our net! 🐛');
    });
    return false;
}, 500);  // Prevent the server being spammed by requests

window.user = user;
window.SENSES_CLIENT = client; //so we can check what js is using.


/* ##################################################### Cookies #################################################### */

function setCookie(name, value) {
    document.cookie = name + "=" + value + ";" + "expires=Fri, 31 Dec 9999 23:59:59 UTC";
}

function removeCookie(name, value) {
    document.cookie = name + "=" + value + ";" + "expires=Thu, 01 Jan 1970 00:00:00 UTC";
}

function getCookie(name) {
    return Object.fromEntries(document.cookie.split('; ').map(cookie => { return cookie.split('=') }))[name] ?? '';
}

window.setCookie = setCookie;
window.removeCookie = removeCookie;
window.getCookie = getCookie;

/* ################################################################################################################## */


const app = createApp({
    name: 'CommunityInformation',
    components: {
        ...Components,
        ...Layout,
        Notifications,
        Snackbars,
        Dialogs,
        Flipper,
        Flipped,
    },
    data() {
        return {
            EventHub,
            user,
            coordinatesTest: [0, 0],
            sidebar: []
        };
    },
    mounted() {
        let count = 0;

        // const check = debounce((num) => {
        //     if (num >= 3) {
        //         // console.log('we got there');
        //         this.$modals.push('GlobalSearch', { hideCloseButton: true, flush: true });
        //     }
        //     count = 0;
        // }, 250);

        // // triple shift
        // onKeyDown('Shift', (e) => {
        //     if (!e.repeat) {
        //         count += 1;
        //     }
        //     check(count);
        // });

        // slash key opens search
        onKeyDown('/', (e) => {
            if (!this.$modals.current && !this.$asides.current && !this.$dialogs.dialog && !document.activeElement) {
                // No modals, asides or dialogs open, no inputs focused.
                this.$modals.push('GlobalSearch', { hideCloseButton: true, flush: true });
            }
        });
    },
    methods: {
        randomCoords() {
            this.coordinatesTest = [Math.random() * 10, Math.random() * 10];
        },
    },
});

// remove when vue is updated to the next minor release (3.3)
app.config.performance = true;

if (window.$clientInstaller) {
    app.use(window.$clientInstaller);
}

const pinia = createPinia();

app.use(pinia);
app.use(NotificationsPlugin);
app.use(SnackbarsPlugin);
app.use(DialogsPlugin);
app.use(AsidesPlugin);
app.use(ModalsPlugin);

// Import and install client JS
loadClientConfig(client, function(clientData) {
    app.use({
        install: (a) => {
            a.config.globalProperties.$client = clientData;

            Object.keys(clientData.components ?? {}).forEach((component) => {
                a.component(component, clientData.components[component]);
            });
        },
    })
});

if (process.env.NODE_ENV !== 'production') {
    app.mixin({
        mounted() {
            if (this.$el) {
                if (this.$el instanceof Element) {
                    // this.$el.setAttribute('data-component', this?.$options?.name);
                    // eslint-disable-next-line no-underscore-dangle
                    this.$el.setAttribute(
                        'data-component',
                        // eslint-disable-next-line no-underscore-dangle
                        this?.$options?.__file
                            ?.split('/')
                            .pop()
                            .replace('.vue', '')
                    );
                }
            }
        },
    });
}

app.mount('#app');

window.app = app;
window.highlight = highlight;
