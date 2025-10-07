import axios from "axios";
import laravelEcho from "./echo.js";

<<<<<<< Updated upstream
import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */


// import Echo from 'laravel-echo';

// import Pusher from 'pusher-js';
// window.Pusher = Pusher;

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: import.meta.env.VITE_PUSHER_APP_KEY,
//     cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt1',
//     wsHost: import.meta.env.VITE_PUSHER_HOST ? import.meta.env.VITE_PUSHER_HOST : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
//     wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
//     wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
//     forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'https') === 'https',
//     enabledTransports: ['ws', 'wss'],
// });
<


// other bootstrapping you have...
import Pusher from "pusher-js";
import Echo from "laravel-echo";


window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
<<<<<<< Updated upstream
    key: "9fb9967f0fe9d1b937af",
    cluster: "ap1",
    forceTLS: true,
    encrypted: true,
});

import Pusher from "pusher-js";
import Echo from "laravel-echo";
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "https") === "https",
});


    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    
    encrypted: true,
    authEndpoint: "/broadcasting/auth",
=======
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
    disableStats: true,
>>>>>>> Stashed changes
=======
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    forceTLS: false,
    enabledTransports: ["ws", "wss"],
>>>>>>> Stashed changes
    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    },
<<<<<<< Updated upstream
<<<<<<< Updated upstream
});
>>>>>>> Stashed changes
=======
    activityTimeout: 30000,
    auth: {
        headers: {
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content")
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
    },
});
>>>>>>> Stashed changes
=======
});
>>>>>>> Stashed changes
=======
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error(
        "CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token"
    );
}
window.Echo = laravelEcho;
>>>>>>> Stashed changes
