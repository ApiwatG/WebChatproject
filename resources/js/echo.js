import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const csrfToken = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");

const laravelEcho = new Echo({
    broadcaster: "pusher",
    key: "6dd9d74c5d4722ff19eb",
    cluster: "ap1",
    wsHost: window.location.hostname,
    wsPort: 6001,
    wssPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ["ws", "wss"],
    auth: {
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
    },
});

export default laravelEcho;
