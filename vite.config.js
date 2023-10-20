import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({command, mode}) => {
    const env = loadEnv(mode, process.cwd());

    return {
        resolve: {
            alias: {
                vue: 'vue/dist/vue.esm-bundler.js',
            },
        },

        server:{
            host: '127.0.0.1'
        },

        build: {
            rollupOptions: {
                output:{
                    manualChunks(id) {
                        if (id.includes('node_modules')) {
                            return id.toString().split('node_modules/')[1].split('/')[0].toString();
                        }
                    }
                }
            }
        },

        plugins: [
            laravel({
                input: [
                    'resources/js/senses.js',
                    'resources/css/senses.css',

                    // `clients/${env.VITE_SENSES_CLIENT}/Resources/css/client.css`,
                ],

                refresh: true,
            }),

            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
        ],
    }
});
