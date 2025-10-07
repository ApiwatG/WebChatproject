import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;
Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY || "6dd9d74c5d4722ff19eb",
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || "ap1",
    forceTLS: true,
});
