<x-layout-admin>
    <!--
  This example requires updating your template:

  ```
  <html class="h-full bg-white">
  <body class="h-full">
  ```
-->


    @if (session('success'))
        <div class="bg-green-500 text-white px-4 py-2 rounded-md mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="px-4 py-10 sm:px-6 lg:px-8 lg:py-6">
        @include('admin.product.index')
    </div>


</x-layout-admin>
