import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
export default laravelEcho;

export default echo;

window.Pusher = Pusher;

const laravelEcho = new Echo({
    broadcaster: 'pusher',
    key: '6dd9d74c5d4722ff19eb',
    cluster: 'ap1',
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        }
    }
});


