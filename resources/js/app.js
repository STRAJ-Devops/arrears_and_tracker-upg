require('./bootstrap');

import Echo from 'laravel-echo';
import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Pusher setup
window.Pusher = require('pusher-js');

// Laravel Echo setup
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// Toastr configuration (optional)
toastr.options = {
    "positionClass": "toast-top-right",
    "timeOut": "5000",
    "progressBar": true
};

// Listen for the event when the queue job completes
Echo.private(`import-status.${userId}`) // Replace `userId` with the actual user ID variable
    .listen('ImportCompleted', (e) => {
        toastr.success(e.message, 'Import Completed');
    });
