import "./bootstrap";

import "flowbite";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

import Alpine from "alpinejs";
import Swal from "sweetalert2";

window.Alpine = Alpine;
window.flatpickr = flatpickr;
window.Swal = Swal;

// Function untuk confirm receive order
window.confirmReceiveOrder = function(url, invoiceNumber) {
    Swal.fire({
        title: 'Konfirmasi Penerimaan Barang',
        html: `<p class="text-gray-600">Apakah barang benar-benar telah diterima dengan baik untuk pesanan <strong>${invoiceNumber}</strong>?</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#16a34a',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Terima Barang',
        cancelButtonText: 'Batalkan',
        buttonsStyling: true,
    }).then((result) => {
        if (result.isConfirmed) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">';
            document.body.appendChild(form);
            form.submit();
        }
    });
};

// Function untuk confirm dengan custom message
window.confirmAction = function(message, url, method = 'POST') {
    Swal.fire({
        title: 'Konfirmasi',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batalkan',
        buttonsStyling: true,
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">';
            
            if (method !== 'POST') {
                form.innerHTML += '<input type="hidden" name="_method" value="' + method + '">';
            }
            
            document.body.appendChild(form);
            form.submit();
        }
    });
};

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
