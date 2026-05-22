import "./bootstrap";

import "flowbite";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import Alpine from "alpinejs";

window.Alpine = Alpine;
window.flatpickr = flatpickr;

Alpine.start();

// Initialize flatpickr for flash sale datetime inputs
document.addEventListener('DOMContentLoaded', function() {
    const startAtInput = document.getElementById('start_at');
    const endAtInput = document.getElementById('end_at');

    if (startAtInput) {
        flatpickr('#start_at', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
            locale: {
                firstDayOfWeek: 1,
            }
        });
    }

    if (endAtInput) {
        flatpickr('#end_at', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            minDate: 'today',
            locale: {
                firstDayOfWeek: 1,
            }
        });
    }
});
