require('./bootstrap');

import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

// Toastr configuration (optional)
toastr.options = {
    "positionClass": "toast-top-right",
    "timeOut": "5000",
    "progressBar": true
};

console.log('Hello World');

// Listen for the event when the queue job completes
Echo.private(`import-status.1`) // Replace `userId` with the actual user ID variable
    .listen('ImportCompleted', (e) => {
        toastr.success(e.message, 'Import Completed');
        console.log(e.message);
    });
