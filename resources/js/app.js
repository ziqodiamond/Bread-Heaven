import "./bootstrap";

import "flowbite";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import Alpine from "alpinejs";

window.Alpine = Alpine;
window.flatpickr = flatpickr;

// Modal function - define BEFORE Alpine.start()
window.modalData = function() {
    return {
        openShipment: false,
        method: 'delivery',
        selectedCourier: null,
        loading: false,
        couriersData: window.COURIERS_DATA || [],
        
        getServices() {
            if (!this.selectedCourier) return [];
            const courier = this.couriersData.find(c => c.code === this.selectedCourier);
            return courier ? courier.services : [];
        },
        
        resetForm() {
            this.method = 'delivery';
            this.selectedCourier = null;
            this.loading = false;
            const tracking = document.getElementById('tracking_number');
            const notes = document.getElementById('notes');
            const notesPick = document.getElementById('notes_pickup');
            const courier = document.getElementById('courier_name');
            const services = document.getElementById('services');
            
            if (tracking) tracking.value = '';
            if (notes) notes.value = '';
            if (notesPick) notesPick.value = '';
            if (courier) courier.value = '';
            if (services) services.value = '';
        },
        
        openModal() {
            this.openShipment = true;
            this.resetForm();
        },
        
        closeModal() {
            this.openShipment = false;
            this.resetForm();
        }
    }
};

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
