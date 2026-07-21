{{-- resources/views/admin/management/vouchers/edit.blade.php --}}
<x-layout-admin>
    <!-- Store data in data attributes for JavaScript to read -->
    <div id="voucherData" 
         data-products="{!! htmlspecialchars(json_encode($products->map(fn($p) => ['id' => (string)$p->id, 'name' => $p->name])->values()), ENT_QUOTES, 'UTF-8') !!}"
         data-categories="{!! htmlspecialchars(json_encode($categories->map(fn($c) => ['id' => (string)$c->id, 'name' => $c->name])->values()), ENT_QUOTES, 'UTF-8') !!}"
         data-shipping="{!! htmlspecialchars(json_encode($shippingMethods->map(fn($s) => ['id' => (string)$s->id, 'courier_name' => $s->courier_name])->values()), ENT_QUOTES, 'UTF-8') !!}"
         data-payment="{!! htmlspecialchars(json_encode($paymentMethods->map(fn($p) => ['id' => (string)$p->id, 'name' => $p->name])->values()), ENT_QUOTES, 'UTF-8') !!}"
         data-old-product-ids="{{ old('product_ids', $voucher->products()->wherePivot('is_excluded', false)->pluck('products.id')->implode(',')) }}"
         data-old-category-ids="{{ old('category_ids', $voucher->categories()->wherePivot('is_excluded', false)->pluck('categories.id')->implode(',')) }}"
         data-old-shipping-ids="{{ old('shipping_method_ids', $voucher->shippingMethods()->wherePivot('is_excluded', false)->pluck('shipping_methods.id')->implode(',')) }}"
         data-old-payment-ids="{{ old('payment_method_ids', $voucher->paymentMethods()->wherePivot('is_excluded', false)->pluck('payment_methods.id')->implode(',')) }}"
         style="display:none;"></div>

    <script>
        window.voucherForm = function() {
            const dataEl = document.getElementById('voucherData');
            
            // Parse old values from data attributes
            const oldProductIds = dataEl.dataset.oldProductIds 
                ? dataEl.dataset.oldProductIds.split(',').filter(id => id.trim()) 
                : [];
            const oldCategoryIds = dataEl.dataset.oldCategoryIds 
                ? dataEl.dataset.oldCategoryIds.split(',').filter(id => id.trim()) 
                : [];
            const oldShippingIds = dataEl.dataset.oldShippingIds 
                ? dataEl.dataset.oldShippingIds.split(',').filter(id => id.trim()) 
                : [];
            const oldPaymentIds = dataEl.dataset.oldPaymentIds 
                ? dataEl.dataset.oldPaymentIds.split(',').filter(id => id.trim()) 
                : [];
            
            // Parse data from data attributes
            let products = [];
            let categories = [];
            let shipping = [];
            let payment = [];
            
            try {
                products = JSON.parse(dataEl.dataset.products);
            } catch(e) {
                console.warn('Could not parse products:', e);
                products = [];
            }
            
            try {
                categories = JSON.parse(dataEl.dataset.categories);
            } catch(e) {
                console.warn('Could not parse categories:', e);
                categories = [];
            }
            
            try {
                shipping = JSON.parse(dataEl.dataset.shipping);
            } catch(e) {
                console.warn('Could not parse shipping methods:', e);
                shipping = [];
            }
            
            try {
                payment = JSON.parse(dataEl.dataset.payment);
            } catch(e) {
                console.warn('Could not parse payment methods:', e);
                payment = [];
            }

            return {
                showProductModal: false,
                showCategoryModal: false,
                showShippingModal: false,
                showPaymentModal: false,

                productSearch: '',
                categorySearch: '',
                shippingSearch: '',
                paymentSearch: '',

                selectedProducts: oldProductIds,
                selectedCategories: oldCategoryIds,
                selectedShippingMethods: oldShippingIds,
                selectedPaymentMethods: oldPaymentIds,

                productsList: products,
                categoriesList: categories,
                shippingMethodsList: shipping,
                paymentMethodsList: payment,

                filteredProducts() {
                    if (!this.productSearch) return this.productsList;
                    return this.productsList.filter(prod =>
                        prod.name.toLowerCase().includes(this.productSearch.toLowerCase())
                    );
                },

                filteredCategories() {
                    if (!this.categorySearch) return this.categoriesList;
                    return this.categoriesList.filter(cat =>
                        cat.name.toLowerCase().includes(this.categorySearch.toLowerCase())
                    );
                },

                filteredShippingMethods() {
                    if (!this.shippingSearch) return this.shippingMethodsList;
                    return this.shippingMethodsList.filter(method =>
                        method.courier_name.toLowerCase().includes(this.shippingSearch.toLowerCase())
                    );
                },

                filteredPaymentMethods() {
                    if (!this.paymentSearch) return this.paymentMethodsList;
                    return this.paymentMethodsList.filter(method =>
                        method.name.toLowerCase().includes(this.paymentSearch.toLowerCase())
                    );
                },

                toggleProduct(id) {
                    id = String(id);
                    const index = this.selectedProducts.indexOf(id);
                    if (index > -1) {
                        this.selectedProducts.splice(index, 1);
                    } else {
                        this.selectedProducts.push(id);
                    }
                },

                removeProduct(id) {
                    id = String(id);
                    const index = this.selectedProducts.indexOf(id);
                    if (index > -1) {
                        this.selectedProducts.splice(index, 1);
                    }
                },

                getProductName(id) {
                    id = String(id);
                    const prod = this.productsList.find(p => p.id === id);
                    return prod ? prod.name : 'Unknown';
                },

                toggleCategory(id) {
                    id = String(id);
                    const index = this.selectedCategories.indexOf(id);
                    if (index > -1) {
                        this.selectedCategories.splice(index, 1);
                    } else {
                        this.selectedCategories.push(id);
                    }
                },

                removeCategory(id) {
                    id = String(id);
                    const index = this.selectedCategories.indexOf(id);
                    if (index > -1) {
                        this.selectedCategories.splice(index, 1);
                    }
                },

                getCategoryName(id) {
                    id = String(id);
                    const cat = this.categoriesList.find(c => c.id === id);
                    return cat ? cat.name : 'Unknown';
                },

                toggleShippingMethod(id) {
                    id = String(id);
                    const index = this.selectedShippingMethods.indexOf(id);
                    if (index > -1) {
                        this.selectedShippingMethods.splice(index, 1);
                    } else {
                        this.selectedShippingMethods.push(id);
                    }
                },

                removeShippingMethod(id) {
                    id = String(id);
                    const index = this.selectedShippingMethods.indexOf(id);
                    if (index > -1) {
                        this.selectedShippingMethods.splice(index, 1);
                    }
                },

                getShippingMethodName(id) {
                    id = String(id);
                    const method = this.shippingMethodsList.find(m => m.id === id);
                    return method ? method.courier_name : 'Unknown';
                },

                togglePaymentMethod(id) {
                    id = String(id);
                    const index = this.selectedPaymentMethods.indexOf(id);
                    if (index > -1) {
                        this.selectedPaymentMethods.splice(index, 1);
                    } else {
                        this.selectedPaymentMethods.push(id);
                    }
                },

                removePaymentMethod(id) {
                    id = String(id);
                    const index = this.selectedPaymentMethods.indexOf(id);
                    if (index > -1) {
                        this.selectedPaymentMethods.splice(index, 1);
                    }
                },

                getPaymentMethodName(id) {
                    id = String(id);
                    const method = this.paymentMethodsList.find(m => m.id === id);
                    return method ? method.name : 'Unknown';
                }
            };
        };
    </script>

    <div class="space-y-5">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.management.vouchers.index') }}"
                    class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-base font-medium text-gray-900">Edit Voucher</h2>
                    <p class="text-sm text-gray-400 mt-0.5">{{ $voucher->code }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs font-medium text-gray-600">Status: 
                    @if ($voucher->is_active && (!$voucher->end_at || !$voucher->end_at->isPast()))
                        <span class="text-green-600">Active</span>
                    @elseif ($voucher->end_at && $voucher->end_at->isPast())
                        <span class="text-gray-600">Expired</span>
                    @else
                        <span class="text-yellow-600">Inactive</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <div class="rounded-xl border border-gray-100 bg-white p-6" x-data="voucherForm()">

            <form action="{{ route('admin.management.vouchers.update', $voucher) }}" method="POST" enctype="multipart/form-data" class="space-y-6">

                @csrf
                @method('PUT')

                {{-- ── Informasi Dasar ────────────────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="name" class="block text-xs font-medium text-gray-600">
                                Nama Voucher <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" placeholder="Contoh: Diskon Member"
                                value="{{ old('name', $voucher->name) }}" required
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="code" class="block text-xs font-medium text-gray-600">
                                Kode Voucher
                            </label>
                            <input type="text" value="{{ $voucher->code }}" disabled
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 font-mono uppercase bg-gray-50 cursor-not-allowed">
                            <p class="text-xs text-gray-400">Kode tidak dapat diubah setelah dibuat</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="space-y-1.5">
                            <label for="description" class="block text-xs font-medium text-gray-600">
                                Deskripsi
                            </label>
                            <textarea name="description" id="description" rows="2" placeholder="Deskripsi voucher..."
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 resize-none focus:border-gray-400 focus:outline-none focus:ring-0">{{ old('description', $voucher->description) }}</textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label for="image_path" class="block text-xs font-medium text-gray-600">
                                Gambar Voucher
                            </label>
                            <input type="file" name="image_path" id="image_path" accept="image/*"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            @if($voucher->image_path)
                                <p class="text-xs text-gray-500">Gambar saat ini: {{ basename($voucher->image_path) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Tipe & Nilai Voucher ───────────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Tipe & Nilai Voucher</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="type" class="block text-xs font-medium text-gray-600">
                                Tipe Voucher
                            </label>
                            <input type="text" value="{{ $voucher->type === 'fixed' ? 'Potongan Harga (Rp)' : ($voucher->type === 'percent' ? 'Diskon Persen (%)' : 'Gratis Ongkir') }}" disabled
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-gray-50 cursor-not-allowed">
                            <p class="text-xs text-gray-400">Tipe tidak dapat diubah</p>
                        </div>
                        <div class="space-y-1.5">
                            <label for="value" class="block text-xs font-medium text-gray-600">
                                Nilai <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="value" id="value" min="0" placeholder="0"
                                value="{{ old('value', $voucher->value) }}" required
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            @error('value')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label for="maximum_discount" class="block text-xs font-medium text-gray-600">
                                Diskon Maksimal (Rp)
                            </label>
                            <input type="number" name="maximum_discount" id="maximum_discount" min="0" placeholder="0"
                                value="{{ old('maximum_discount', $voucher->maximum_discount) }}"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        </div>
                    </div>
                </div>

                {{-- ── Syarat Penggunaan ──────────────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Syarat Penggunaan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-1.5">
                            <label for="minimum_purchase" class="block text-xs font-medium text-gray-600">
                                Minimum Pembelian (Rp)
                            </label>
                            <input type="number" name="minimum_purchase" id="minimum_purchase" min="0" placeholder="0"
                                value="{{ old('minimum_purchase', $voucher->minimum_purchase) }}"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        </div>
                        <div class="space-y-1.5">
                            <label for="quota" class="block text-xs font-medium text-gray-600">
                                Kuota Voucher
                            </label>
                            <input type="number" name="quota" id="quota" min="0" placeholder="Kosong = unlimited"
                                value="{{ old('quota', $voucher->quota) }}"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            @if($voucher->quota)
                                <div class="text-xs text-gray-500 mt-1">
                                    <p>Digunakan: <strong>{{ $voucher->usages->count() }}</strong> dari {{ $voucher->quota }}</p>
                                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden mt-1">
                                        <div class="h-full bg-blue-600 rounded-full" style="width: {{ ($voucher->usages->count() / max(1, $voucher->quota)) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="space-y-1.5">
                            <label for="max_usage_per_user" class="block text-xs font-medium text-gray-600">
                                Penggunaan Per User
                            </label>
                            <input type="number" name="max_usage_per_user" id="max_usage_per_user" min="0" placeholder="Kosong = unlimited"
                                value="{{ old('max_usage_per_user', $voucher->max_usage_per_user) }}"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        </div>
                    </div>
                </div>

                {{-- ── Jadwal Voucher ─────────────────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Jadwal Voucher</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="start_at" class="block text-xs font-medium text-gray-600">
                                Mulai
                            </label>
                            <input type="datetime-local" name="start_at" id="start_at"
                                value="{{ old('start_at', $voucher->start_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                        </div>
                        <div class="space-y-1.5">
                            <label for="end_at" class="block text-xs font-medium text-gray-600">
                                Berakhir
                            </label>
                            <input type="datetime-local" name="end_at" id="end_at"
                                value="{{ old('end_at', $voucher->end_at?->format('Y-m-d\TH:i')) }}"
                                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700
                                       placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
                            @if($voucher->end_at && $voucher->end_at->isPast())
                                <p class="text-xs text-red-600 font-medium mt-1">⚠️ Sudah Kadaluarsa</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ── Pengaturan Lanjutan ────────────────────────────────────── --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Pengaturan Lanjutan</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_stackable" value="1" {{ old('is_stackable', $voucher->is_stackable) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                            <span class="text-xs font-medium text-gray-600">Voucher dapat dikombinasikan</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="members_only" value="1" {{ old('members_only', $voucher->members_only) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                            <span class="text-xs font-medium text-gray-600">Hanya untuk member</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="allow_on_flash_sale" value="1" {{ old('allow_on_flash_sale', $voucher->allow_on_flash_sale) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                            <span class="text-xs font-medium text-gray-600">Berlaku pada Flash Sale</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="allow_on_discount" value="1" {{ old('allow_on_discount', $voucher->allow_on_discount) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                            <span class="text-xs font-medium text-gray-600">Berlaku pada Produk Diskon</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                            <span class="text-xs font-medium text-gray-600">Aktif</span>
                        </label>
                    </div>
                </div>

                {{-- ── Rules (Kategori, Ongkir, Payment) ──────────────────────── --}}
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-4">Aturan Voucher</h3>
                    
                    {{-- Produk --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-medium text-gray-600">Produk Berlaku</label>
                            <button type="button" @click="showProductModal = true"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Edit
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 min-h-[40px]">
                            <template x-if="selectedProducts.length === 0">
                                <span class="text-xs text-gray-400">Semua produk</span>
                            </template>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="productId in selectedProducts" :key="productId">
                                    <div class="inline-flex items-center gap-2 bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-lg text-xs">
                                        <span x-text="getProductName(productId)"></span>
                                        <button type="button" @click="removeProduct(productId)" class="text-indigo-700 hover:text-indigo-900">
                                            ✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="product_ids" :value="selectedProducts.join(',')">
                    </div>
                    
                    {{-- Kategori --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-medium text-gray-600">Kategori Berlaku</label>
                            <button type="button" @click="showCategoryModal = true"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Edit
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 min-h-[40px]">
                            <template x-if="selectedCategories.length === 0">
                                <span class="text-xs text-gray-400">Semua kategori</span>
                            </template>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="categoryId in selectedCategories" :key="categoryId">
                                    <div class="inline-flex items-center gap-2 bg-blue-100 text-blue-700 px-2.5 py-1 rounded-lg text-xs">
                                        <span x-text="getCategoryName(categoryId)"></span>
                                        <button type="button" @click="removeCategory(categoryId)" class="text-blue-700 hover:text-blue-900">
                                            ✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="category_ids" :value="selectedCategories.join(',')">
                    </div>

                    {{-- Metode Pengiriman --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-medium text-gray-600">Metode Pengiriman Berlaku</label>
                            <button type="button" @click="showShippingModal = true"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Edit
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 min-h-[40px]">
                            <template x-if="selectedShippingMethods.length === 0">
                                <span class="text-xs text-gray-400">Semua metode pengiriman</span>
                            </template>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="methodId in selectedShippingMethods" :key="methodId">
                                    <div class="inline-flex items-center gap-2 bg-green-100 text-green-700 px-2.5 py-1 rounded-lg text-xs">
                                        <span x-text="getShippingMethodName(methodId)"></span>
                                        <button type="button" @click="removeShippingMethod(methodId)" class="text-green-700 hover:text-green-900">
                                            ✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="shipping_method_ids" :value="selectedShippingMethods.join(',')">
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-medium text-gray-600">Metode Pembayaran Berlaku</label>
                            <button type="button" @click="showPaymentModal = true"
                                class="text-xs text-blue-600 hover:text-blue-700 font-medium">
                                Edit
                            </button>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 min-h-[40px]">
                            <template x-if="selectedPaymentMethods.length === 0">
                                <span class="text-xs text-gray-400">Semua metode pembayaran</span>
                            </template>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="methodId in selectedPaymentMethods" :key="methodId">
                                    <div class="inline-flex items-center gap-2 bg-purple-100 text-purple-700 px-2.5 py-1 rounded-lg text-xs">
                                        <span x-text="getPaymentMethodName(methodId)"></span>
                                        <button type="button" @click="removePaymentMethod(methodId)" class="text-purple-700 hover:text-purple-900">
                                            ✕
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="payment_method_ids" :value="selectedPaymentMethods.join(',')">
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center gap-3 pt-6 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.management.vouchers.index') }}" class="inline-flex items-center rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                </div>

            </form>

            {{-- ── MODAL: PRODUK ──────────────────────────────────────────────────────── --}}
            <div x-show="showProductModal" x-transition.opacity.duration.200 style="display:none"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div @click.outside="showProductModal = false" x-transition.scale.origin.center
            class="w-full max-w-md rounded-2xl bg-white shadow-lg">
            <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">Pilih Produk Berlaku</p>
                <button @click="showProductModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>
            
            {{-- Search Input --}}
            <div class="px-6 py-3 border-b border-gray-100">
                <input type="text" x-model="productSearch" placeholder="Cari produk..."
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700
                           placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>

            {{-- Items List --}}
            <div class="space-y-0 max-h-[50vh] overflow-y-auto">
                <template x-for="product in filteredProducts()" :key="product.id">
                    <label class="flex items-center gap-3 cursor-pointer px-6 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors">
                        <input type="checkbox" :value="String(product.id)" :checked="selectedProducts.includes(String(product.id))"
                            @change="toggleProduct(String(product.id))"
                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                        <span class="text-sm text-gray-700" x-text="product.name"></span>
                    </label>
                </template>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" @click="selectedProducts = []; productSearch = ''"
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Semua
                </button>
                <button type="button" @click="showProductModal = false"
                    class="flex-1 rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    Simpan
                </button>
            </div>
        </div>
    </div>

            {{-- ── MODAL: KATEGORI ────────────────────────────────────────────────────── --}}
            <div x-show="showCategoryModal" x-transition.opacity.duration.200 style="display:none"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div @click.outside="showCategoryModal = false" x-transition.scale.origin.center
            class="w-full max-w-md rounded-2xl bg-white shadow-lg">
            <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">Pilih Kategori Berlaku</p>
                <button @click="showCategoryModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>
            
            {{-- Search Input --}}
            <div class="px-6 py-3 border-b border-gray-100">
                <input type="text" x-model="categorySearch" placeholder="Cari kategori..."
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700
                           placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>

            {{-- Items List --}}
            <div class="space-y-0 max-h-[50vh] overflow-y-auto">
                <template x-for="category in filteredCategories()" :key="category.id">
                    <label class="flex items-center gap-3 cursor-pointer px-6 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors">
                        <input type="checkbox" :value="String(category.id)" :checked="selectedCategories.includes(String(category.id))"
                            @change="toggleCategory(String(category.id))"
                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                        <span class="text-sm text-gray-700" x-text="category.name"></span>
                    </label>
                </template>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" @click="selectedCategories = []; categorySearch = ''"
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Semua
                </button>
                <button type="button" @click="showCategoryModal = false"
                    class="flex-1 rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    {{-- ── MODAL: METODE PENGIRIMAN ───────────────────────────────────────────── --}}
    <div x-show="showShippingModal" x-transition.opacity.duration.200 style="display:none"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div @click.outside="showShippingModal = false" x-transition.scale.origin.center
            class="w-full max-w-md rounded-2xl bg-white shadow-lg">
            <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">Pilih Metode Pengiriman Berlaku</p>
                <button @click="showShippingModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>
            
            {{-- Search Input --}}
            <div class="px-6 py-3 border-b border-gray-100">
                <input type="text" x-model="shippingSearch" placeholder="Cari metode pengiriman..."
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700
                           placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>

            {{-- Items List --}}
            <div class="space-y-0 max-h-[50vh] overflow-y-auto">
                <template x-for="method in filteredShippingMethods()" :key="method.id">
                    <label class="flex items-center gap-3 cursor-pointer px-6 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors">
                        <input type="checkbox" :value="String(method.id)" :checked="selectedShippingMethods.includes(String(method.id))"
                            @change="toggleShippingMethod(String(method.id))"
                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                        <span class="text-sm text-gray-700" x-text="method.courier_name"></span>
                    </label>
                </template>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" @click="selectedShippingMethods = []; shippingSearch = ''"
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Semua
                </button>
                <button type="button" @click="showShippingModal = false"
                    class="flex-1 rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    Simpan
                </button>
            </div>
        </div>
    </div>

    {{-- ── MODAL: METODE PEMBAYARAN ───────────────────────────────────────────── --}}
    <div x-show="showPaymentModal" x-transition.opacity.duration.200 style="display:none"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div @click.outside="showPaymentModal = false" x-transition.scale.origin.center
            class="w-full max-w-md rounded-2xl bg-white shadow-lg">
            <div class="border-b border-gray-100 px-6 py-4 flex items-center justify-between">
                <p class="text-sm font-medium text-gray-900">Pilih Metode Pembayaran Berlaku</p>
                <button @click="showPaymentModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>
            
            {{-- Search Input --}}
            <div class="px-6 py-3 border-b border-gray-100">
                <input type="text" x-model="paymentSearch" placeholder="Cari metode pembayaran..."
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700
                           placeholder:text-gray-400 focus:border-gray-400 focus:outline-none focus:ring-0">
            </div>

            {{-- Items List --}}
            <div class="space-y-0 max-h-[50vh] overflow-y-auto">
                <template x-for="method in filteredPaymentMethods()" :key="method.id">
                    <label class="flex items-center gap-3 cursor-pointer px-6 py-3 hover:bg-gray-50 border-b border-gray-50 transition-colors">
                        <input type="checkbox" :value="String(method.id)" :checked="selectedPaymentMethods.includes(String(method.id))"
                            @change="togglePaymentMethod(String(method.id))"
                            class="rounded border-gray-300 text-gray-900 shadow-sm focus:border-gray-400 focus:ring-0">
                        <span class="text-sm text-gray-700" x-text="method.name"></span>
                    </label>
                </template>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2 px-6 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" @click="selectedPaymentMethods = []; paymentSearch = ''"
                    class="flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Semua
                </button>
                <button type="button" @click="showPaymentModal = false"
                    class="flex-1 rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-700 transition-colors">
                    Simpan
                </button>
            </div>
            </div>
        </div>

        </div>

    </div>

</x-layout-admin>
