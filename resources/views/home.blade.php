<x-layout>
    @include('components.hero')

    <!-- Flash Sales Section -->
    <div class="px-4 py-8 md:px-6 md:py-12">
        @include('components.flash-sales', ['flashSales' => $flashSales])
    </div>

    @include('components.product')
</x-layout>
