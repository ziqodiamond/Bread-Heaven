<div class="space-y-4">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Delivery Details</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="nama" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                Your name
            </label>
            <input type="text" id="nama"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                placeholder="Bonnie Green" required />
        </div>

        <div>
            <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                Email
            </label>
            <input type="email" id="email"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                placeholder="example@example.com" required />
        </div>

        <div>
            <label for="phone" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                Phone number
            </label>
            <input type="tel" id="phone"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                placeholder="08123456789" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:col-span-2">
            <div>
                <label for="provinsi" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Provinsi
                </label>
                <input type="text" id="provinsi"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    placeholder="Provinsi" required />
            </div>

            <div>
                <label for="kota" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Kota
                </label>
                <input type="text" id="kota"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    placeholder="City Name" required />
            </div>

            <div>
                <label for="kode_pos" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                    Code pos
                </label>
                <input type="text" id="kode_pos"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                    placeholder="12345" required />
            </div>
        </div>

        <div class="md:col-span-2">
            <label for="address" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">
                Full Address
            </label>
            <textarea id="address"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500"
                placeholder="Complete Address" rows="3" required></textarea>
        </div>
    </div>
</div>
