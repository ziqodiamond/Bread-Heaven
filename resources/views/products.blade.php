<x-layout>
    @if (session('success'))
        <div id="success-alert"
            class="alert alert-success fixed top-4 right-4 z-50 px-4 py-2 bg-green-500 text-white rounded shadow-lg">
            {{ session('success') }}
        </div>
    @endif


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var alert = document.getElementById('success-alert');
            if (alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500); // Hapus elemen setelah transisi selesai
                }, 5000); // 5 detik sebelum mulai menghilang
            }
        });
    </script>



    @include('components.product')

</x-layout>
