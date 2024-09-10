<!-- Modal Cart -->
<div id="cart-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50"
    aria-modal="true" role="dialog" tabindex="-1">
    <div
        class="relative w-screen max-w-sm mx-auto border border-gray-600 bg-gray-100 px-4 py-8 sm:px-6 lg:px-8 text-center">
        <button id="close-cart-modal" class="absolute end-4 top-4 text-gray-600 transition hover:scale-110">
            <span class="sr-only">Close cart</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="mt-4 space-y-6">
            <ul class="space-y-4">
                @if ($cartItems->count())
                    @foreach ($cartItems as $item)
                        <li class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('storage/' . $item->product->image_url) }}"
                                    alt="{{ $item->product->name }}" class="size-16 rounded object-cover" />
                                <div>
                                    <h3 class="text-sm text-gray-900">{{ $item->product->name }}</h3>
                                    <dl class="mt-0.5 space-y-px text-[10px] text-gray-600">
                                        <div>
                                            <dt class="inline">Quantity:</dt>
                                            <dd class="inline">{{ $item->quantity }}</dd>
                                        </div>
                                        <div>
                                            <dt class="inline">Price:</dt>
                                            <dd class="inline">${{ $item->product->price }}</dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-800 dark:text-white transition hover:scale-110">
                                    <svg class="w-[18px] h-[18px]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M5 7h14m-9 3v8m4-8v8M10 3h4a1 1 0 0 1 1 1v3H9V4a1 1 0 0 1 1-1ZM6 7h12v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7Z" />
                                    </svg>
                                </button>
                            </form>
                        </li>
                    @endforeach
                @else
                    <p>Your cart is empty.</p>
                @endif
            </ul>

            <div class="space-y-4 text-center">
                <a href="/cart"
                    class="block rounded border border-gray-600 px-5 py-3 text-sm text-gray-600 transition hover:ring-1 hover:ring-gray-400">
                    View my cart ({{ $cartItems->count() }})
                </a>

                <a href="#"
                    class="block rounded bg-gray-700 px-5 py-3 text-sm text-gray-100 transition hover:bg-gray-600">
                    Checkout
                </a>

                <a href="#" id="continue-shopping"
                    class="inline-block text-sm text-gray-500 underline underline-offset-4 transition hover:text-gray-600">
                    Continue shopping
                </a>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-item');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-item-id');
                const url = `/cart/remove/${itemId}`;

                fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Refresh modal content or remove the item from the DOM
                            this.closest('li').remove();
                        } else {
                            alert('Failed to remove the item.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
