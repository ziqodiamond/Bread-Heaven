<x-layout>
    @include('components.hero')

    <!-- Flash Sales Section -->
    <div class="px-4 py-8 md:px-6 md:py-12">
        <x-flash-sale-carousel :flashSales="$flashSales" :itemsPerRow="3" />
    </div>

    <!-- Latest Products Section -->
    <div class="px-4 py-8 md:px-6 md:py-12">
        @include('components.product')
    </div>
</x-layout>
