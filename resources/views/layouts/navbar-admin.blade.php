<div class="flex h-screen flex-col justify-between border-e bg-white">
    <div class="px-4 py-6">
        <span class="grid h-10 w-32 place-content-center rounded-lg bg-gray-100 text-xs text-gray-600">
            <img src="{{ asset('storage/general_images/logo.png') }}" alt="logo bread heaven">
        </span>

        <ul class="mt-12 space-y-1">
            <li>
                <a href="{{ route('admin.dashboard') }}"
                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700"s>
                    Dashboard
                </a>
            </li>


            <li>
                <a href="{{ route('admin.users.index') }} "
                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    Manage User
                </a>
            </li>

            <li>
                <a href="/admin/payment-methods"
                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    Manage Payment
                </a>
            </li>

            <li>
                <a href="/admin/delivery-methods"
                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    Delivery Method
                </a>
            </li>

            <li>
                <a href="/admin/products"
                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    Product
                </a>
            </li>

            <li>
                <a href="{{ route('transaction.index') }}"
                    class="block rounded-lg px-4 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                    Transaction
                </a>
            </li>

        </ul>
    </div>

    <div class="sticky inset-x-0 bottom-0 border-t border-gray-100">
        <a href="{{ route('home') }}" class="flex items-center gap-2 bg-white p-4 hover:bg-gray-50">

            <div>
                <p class="text-xs">
                    <strong class="block font-medium">Back to home</strong>
                </p>
            </div>
        </a>
    </div>
</div>
