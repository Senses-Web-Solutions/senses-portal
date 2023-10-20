import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import PusherBatchAuthorizer from 'pusher-js-auth';

const echo = new Echo({
    broadcaster: 'pusher',
    client: new Pusher(import.meta.env.VITE_PUSHER_APP_KEY, {
        authEndpoint: '/broadcasting/auth/batch',
        authorizer: PusherBatchAuthorizer,
        authDelay: 500,
        wsHost: import.meta.env.VITE_PUSHER_HOST,
        wsPort: import.meta.env.VITE_PUSHER_PORT,
        wssPort: import.meta.env.VITE_PUSHER_PORT,
        forceTLS: true,
        encrypted: true,
        disableStats: true,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        enabledTransports: ['ws', 'wss'],
    }),
});

export default function useEcho() {
    return echo;
}
