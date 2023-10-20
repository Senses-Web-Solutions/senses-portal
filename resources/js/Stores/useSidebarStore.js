// The sidebar is in a central pinia store (https://pinia.vuejs.org/)
// so that the GlobalSearch can access items on the sidebar.

import { defineStore } from 'pinia';

export default defineStore('sidebar', {
    state: () => ({
        sidebar: [],
    }),
});
