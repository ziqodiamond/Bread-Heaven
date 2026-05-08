<footer class="bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-screen-xl space-y-8 px-4 py-16 sm:px-6 lg:space-y-16 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            <div>
                <div>
                    <img src="{{ asset('storage/general_images/logo.png') }}" alt="Logo" class="h-48">
                </div>

                {{-- <p class="mt-4 max-w-xs text-gray-500 dark:text-gray-400">
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit. Esse non cupiditate quae nam
                    molestias.
                </p> --}}

                <ul class="mt-8 flex gap-6">

                </ul>
            </div>


        </div>
        <label class="inline-flex items-center cursor-pointer">
            <input type="checkbox" id="theme-toggle" class="sr-only peer">
            <div
                class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
            </div>
            <span id="theme-toggle-text" class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Light</span>
        </label>

        <script>
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleText = document.getElementById('theme-toggle-text');
            const currentTheme = localStorage.getItem('theme') || 'light';

            if (currentTheme === 'dark') {
                document.documentElement.classList.add('dark');
                themeToggleBtn.checked = true;
                themeToggleText.textContent = 'Dark';
            } else {
                themeToggleText.textContent = 'Light';
            }

            themeToggleBtn.addEventListener('change', () => {
                document.documentElement.classList.toggle('dark');
                const newTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
                localStorage.setItem('theme', newTheme);
                themeToggleText.textContent = newTheme.charAt(0).toUpperCase() + newTheme.slice(1);
            });
        </script>


        <p class="text-xs text-gray-500">&copy; 2024. Welvarend Store. All rights reserved.</p>
    </div>
</footer>
