{{-- resources/views/admin/management/vouchers/edit.blade.php --}}
<x-layout-admin>
    <div class="max-w-3xl mx-auto space-y-5">

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
                    @if ($voucher->is_active && !$voucher->expired_at?->isPast())
                        <span class="text-green-600">Active</span>
                    @elseif ($voucher->expired_at?->isPast())
                        <span class="text-gray-600">Expired</span>
                    @else
                        <span class="text-yellow-600">Inactive</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- ── Form ───────────────────────────────────────────────────── --}}
        <form action="{{ route('admin.management.vouchers.update', $voucher) }}" method="POST"
            class="rounded-xl border border-gray-100 bg-white p-5 space-y-5">

            @csrf
            @method('PUT')

            {{-- Kode & Deskripsi --}}
            <div class="space-y-4">
                <h3 class="text-sm font-medium text-gray-900">Informasi Dasar</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="name" class="block text-xs font-medium text-gray-600">Nama Voucher</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $voucher->name) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                        @error('name')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="code" class="block text-xs font-medium text-gray-600">Kode Voucher</label>
                        <input type="text" value="{{ $voucher->code }}" disabled class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 font-mono uppercase bg-gray-50">
                        <p class="text-xs text-gray-400">Kode tidak dapat diubah setelah dibuat</p>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-medium text-gray-600">Deskripsi (Opsional)</label>
                    <input type="text" name="description" id="description" value="{{ old('description', $voucher->description) }}" placeholder="Contoh: Flash Sale Akhir Bulan" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                </div>
            </div>

            {{-- Tipe & Nilai --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Tipe & Nilai Diskon</h3>
                
                <div class="space-y-1.5">
                    <label for="type" class="block text-xs font-medium text-gray-600">Tipe</label>
                    <input type="text" value="{{ ucfirst($voucher->type) }}" disabled class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-gray-50">
                    <p class="text-xs text-gray-400">Tipe tidak dapat diubah</p>
                </div>

                @if ($voucher->type !== 'free_shipping')
                    <div class="space-y-1.5">
                        <label for="value" class="block text-xs font-medium text-gray-600">@if ($voucher->type === 'percent') Diskon (%) @else Diskon (Rp) @endif</label>
                        <div class="relative">
                            <input type="number" name="value" id="value" value="{{ old('value', $voucher->value) }}" placeholder="0" min="0" step="1" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">@if ($voucher->type === 'percent') % @endif</span>
                        </div>
                    </div>
                @else
                    <div class="px-4 py-3 bg-blue-50 rounded-xl border border-blue-100"><p class="text-xs text-blue-900">✓ Voucher ini memberikan gratis ongkir</p></div>
                @endif

                <div class="space-y-1.5">
                    <label for="minimum_purchase" class="block text-xs font-medium text-gray-600">Pembelian Minimum (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="minimum_purchase" id="minimum_purchase" value="{{ old('minimum_purchase', $voucher->minimum_purchase) }}" placeholder="0" min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pl-8 text-sm text-gray-700">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="maximum_discount" class="block text-xs font-medium text-gray-600">Maksimal Potongan (Opsional)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">Rp</span>
                        <input type="number" name="maximum_discount" id="maximum_discount" value="{{ old('maximum_discount', $voucher->maximum_discount) }}" placeholder="0" min="0" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 pl-8 text-sm text-gray-700">
                    </div>
                </div>
            </div>

            {{-- Kuota & Periode --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Kuota & Periode Voucher</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="quota" class="block text-xs font-medium text-gray-600">Kuota</label>
                        <div class="space-y-2">
                            <input type="number" name="quota" id="quota" value="{{ old('quota', $voucher->quota) }}" min="1" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                            <div class="text-xs text-gray-500">
                                <p>Digunakan: <strong>{{ $voucher->usages->count() }}</strong> dari {{ $voucher->quota }}</p>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden mt-1"><div class="h-full bg-gray-700 rounded-full" style="width: {{ ($voucher->usages->count() / max(1, $voucher->quota)) * 100 }}%"></div></div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="start_at" class="block text-xs font-medium text-gray-600">Mulai Pada (Opsional)</label>
                        <input type="text" name="start_at" id="start_at" value="{{ old('start_at', optional($voucher->start_at)?->format('Y-m-d H:i')) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 datetimepicker">
                        @error('start_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                        <label for="end_at" class="block text-xs font-medium text-gray-600 mt-2">Selesai Pada (Opsional)</label>
                        <input type="text" name="end_at" id="end_at" value="{{ old('end_at', optional($voucher->end_at)?->format('Y-m-d H:i')) }}" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 datetimepicker">
                        @error('end_at')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                        @if(optional($voucher->end_at)->isPast())
                            <p class="text-xs text-red-600 font-medium">Sudah Kadaluarsa</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Relasi Voucher --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Relasi Voucher</h3>
                <p class="text-xs text-gray-500">Kelola produk, kategori, metode pengiriman & pembayaran untuk voucher ini.</p>

                @php
                    // Safe filtering dengan null check
                    $includedProductIds = $voucher->products ? $voucher->products->filter(function($p){ return !($p->pivot->is_excluded ?? false); })->pluck('id')->toArray() : [];
                    $excludedProductIds = $voucher->products ? $voucher->products->filter(function($p){ return $p->pivot->is_excluded ?? false; })->pluck('id')->toArray() : [];

                    $includedCategoryIds = $voucher->categories ? $voucher->categories->filter(function($c){ return !($c->pivot->is_excluded ?? false); })->pluck('id')->toArray() : [];
                    $excludedCategoryIds = $voucher->categories ? $voucher->categories->filter(function($c){ return $c->pivot->is_excluded ?? false; })->pluck('id')->toArray() : [];

                    $includedShippingIds = $voucher->shippingMethods ? $voucher->shippingMethods->filter(function($s){ return !($s->pivot->is_excluded ?? false); })->pluck('id')->toArray() : [];
                    $excludedShippingIds = $voucher->shippingMethods ? $voucher->shippingMethods->filter(function($s){ return $s->pivot->is_excluded ?? false; })->pluck('id')->toArray() : [];

                    $includedPaymentIds = $voucher->paymentMethods ? $voucher->paymentMethods->filter(function($p){ return !($p->pivot->is_excluded ?? false); })->pluck('id')->toArray() : [];
                    $excludedPaymentIds = $voucher->paymentMethods ? $voucher->paymentMethods->filter(function($p){ return $p->pivot->is_excluded ?? false; })->pluck('id')->toArray() : [];
                @endphp

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600">Produk (Include)</label>
                        <div x-data="multiSelectModal({ items: @json($products->map(fn($p)=>['id'=>$p->id,'label'=>$p->name])), selected: @json(old('included_products', $includedProductIds)), name: 'included_products[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih produk &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' produk dipilih'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari produk..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Produk (Exclude)</label>
                        <div x-data="multiSelectModal({ items: @json($products->map(fn($p)=>['id'=>$p->id,'label'=>$p->name])), selected: @json(old('excluded_products', $excludedProductIds)), name: 'excluded_products[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih produk dikecualikan &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' produk dikecualikan'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari produk..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>
                    </div>
                        <div x-data="multiSelectModal({ items: @json($categories->map(fn($c)=>['id'=>$c->id,'label'=>$c->name])), selected: @json(old('included_categories', $includedCategoryIds)), name: 'included_categories[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih kategori &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' kategori dipilih'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari kategori..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Kategori (Exclude)</label>
                        <div x-data="multiSelectModal({ items: @json($categories->map(fn($c)=>['id'=>$c->id,'label'=>$c->name])), selected: @json(old('excluded_categories', $excludedCategoryIds)), name: 'excluded_categories[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih kategori dikecualikan &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' kategori dikecualikan'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari kategori..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Brand (Include)</label>
                        <div x-data="multiSelectModal({ items: @json($brands->map(fn($b)=>['id'=>$b->id,'label'=>$b->name])), selected: @json(old('included_brands', $includedBrandIds)), name: 'included_brands[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih brand &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' brand dipilih'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari brand..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Brand (Exclude)</label>
                        <div x-data="multiSelectModal({ items: @json($brands->map(fn($b)=>['id'=>$b->id,'label'=>$b->name])), selected: @json(old('excluded_brands', $excludedBrandIds)), name: 'excluded_brands[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih brand dikecualikan &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' brand dikecualikan'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari brand..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Shipping Methods (Include)</label>
                        <div x-data="multiSelectModal({ items: @json($shippingMethods->map(fn($s)=>['id'=>$s->id,'label'=>$s->courier_name.' - '.$s->service_name])), selected: @json(old('included_shipping_methods', $includedShippingIds)), name: 'included_shipping_methods[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih shipping method &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' method dipilih'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari shipping..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Shipping Methods (Exclude)</label>
                        <div x-data="multiSelectModal({ items: @json($shippingMethods->map(fn($s)=>['id'=>$s->id,'label'=>$s->courier_name.' - '.$s->service_name])), selected: @json(old('excluded_shipping_methods', $excludedShippingIds)), name: 'excluded_shipping_methods[]' })" class="relative">
                            <button type="button" @click="open = true" class="w-full text-left rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 bg-white">
                                <template x-if="selected.length === 0"><span class="text-gray-400">Pilih shipping dikecualikan &hellip;</span></template>
                                <template x-if="selected.length &gt; 0"><span x-text="selected.length + ' shipping dikecualikan'"></span></template>
                            </button>
                            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click="if ($event.target === $el) open = false">
                                <div @click.stop class="w-full max-w-2xl bg-white rounded-xl p-4">
                                    <div class="flex items-center gap-2 mb-3">
                                        <input type="search" x-model="q" placeholder="Cari shipping..." class="flex-1 rounded-md border px-3 py-2">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input type="checkbox" @click="toggleSelectAllFiltered()" :checked="allFilteredSelected">
                                            <span>Pilih semua (filter)</span>
                                        </label>
                                    </div>
                                    <div class="max-h-64 overflow-auto border rounded-md p-2 space-y-1">
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <label class="flex items-center gap-2 p-1 rounded hover:bg-gray-50">
                                                <input type="checkbox" x-model="selected" :value="item.id">
                                                <span x-text="item.label" class="text-sm"></span>
                                            </label>
                                        </template>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="clearSelection()" class="text-sm text-gray-600">Bersihkan</button>
                                            <button type="button" @click="selectAll()" class="text-sm text-gray-600">Pilih Semua</button>
                                        </div>
                                        <div>
                                            <button type="button" @click="open=false" class="px-3 py-2 rounded bg-gray-100">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template x-for="id in selected" :key="id"><input type="hidden" :name="name" :value="id"></template>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <template x-for="id in selected.slice(0,5)" :key="id"><span class="px-2 py-1 bg-gray-100 rounded text-xs" x-text="getLabel(id)"></span></template>
                                <template x-if="selected.length &gt; 5"><span class="px-2 py-1 bg-gray-100 rounded text-xs">+<span x-text="selected.length - 5"></span> lainnya</span></template>
                            </div>
                        </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Payment Methods (Include)</label>
                        <select name="included_payment_methods[]" multiple class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                            @foreach($paymentMethods as $p)
                                <option value="{{ $p->id }}" {{ $voucher->paymentMethods->contains('id', $p->id) && !$voucher->paymentMethods->firstWhere('id', $p->id)->pivot->is_excluded ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600">Payment Methods (Exclude)</label>
                        <select name="excluded_payment_methods[]" multiple class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                            @foreach($paymentMethods as $p)
                                <option value="{{ $p->id }}" {{ $voucher->paymentMethods->contains('id', $p->id) && $voucher->paymentMethods->firstWhere('id', $p->id)->pivot->is_excluded ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Media / Gambar --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Media & Gambar</h3>
                <p class="text-xs text-gray-500">Upload gambar untuk menampilkan voucher secara visual</p>

                <div class="space-y-1.5">
                    <label for="image_path" class="block text-xs font-medium text-gray-600">Gambar Voucher (Opsional)</label>
                    <div class="relative">
                        <input type="file" name="image_path" id="image_path" accept="image/*"
                            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700 file:bg-gray-100 file:border-0 file:rounded-lg file:px-3 file:py-1 file:text-xs file:font-medium file:text-gray-700">
                        <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, GIF, WEBP (Max: 2MB)</p>
                    </div>
                    @error('image_path')<p class="text-xs text-red-600">{{ $message }}</p>@enderror

                    @if ($voucher->image_path)
                    <div class="mt-3">
                        <p class="text-xs font-medium text-gray-600 mb-2">Gambar Saat Ini:</p>
                        <img src="{{ asset('storage/' . $voucher->image_path) }}" alt="{{ $voucher->name }}" class="h-32 w-32 object-cover rounded-lg border border-gray-200">
                    </div>
                    @endif

                    <div id="imagePreview" class="mt-3 hidden">
                        <p class="text-xs font-medium text-gray-600 mb-2">Preview Gambar Baru:</p>
                        <img id="previewImg" src="" alt="Preview" class="h-32 w-32 object-cover rounded-lg border border-gray-200">
                    </div>
                </div>
            </div>

            {{-- Pengaturan --}}
            <div class="space-y-4 border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900">Pengaturan</h3>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $voucher->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Aktifkan voucher</span>
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label for="max_usage_per_user" class="block text-xs font-medium text-gray-600">Limit per User</label>
                            <input type="number" name="max_usage_per_user" id="max_usage_per_user" value="{{ old('max_usage_per_user', $voucher->max_usage_per_user) }}" min="1" class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm text-gray-700">
                            @error('max_usage_per_user')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="is_stackable" value="1" {{ old('is_stackable', $voucher->is_stackable) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Boleh digabung dengan voucher lain (stackable)</span>
                            </label>

                            <label class="flex items-center gap-3 cursor-pointer mt-2">
                                <input type="checkbox" name="members_only" value="1" {{ old('members_only', $voucher->members_only) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300">
                                <span class="text-sm text-gray-600">Hanya untuk pengguna terdaftar (members only)</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistik Penggunaan --}}
            <div class="border-t pt-4">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Statistik Penggunaan</h3>
                <div class="grid grid-cols-3 gap-3">
                    <div class="px-3 py-2.5 bg-gray-50 rounded-lg"><p class="text-xs text-gray-500">Total Digunakan</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $voucher->usages->count() }}</p></div>
                    <div class="px-3 py-2.5 bg-gray-50 rounded-lg"><p class="text-xs text-gray-500">Sisa Kuota</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ max(0, $voucher->quota - $voucher->usages->count()) }}</p></div>
                    <div class="px-3 py-2.5 bg-gray-50 rounded-lg"><p class="text-xs text-gray-500">Persentase Penggunaan</p><p class="text-lg font-bold text-gray-900 mt-0.5">{{ $voucher->quota ? round(($voucher->usages->count() / $voucher->quota) * 100) : 0 }}%</p></div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <form action="{{ route('admin.management.vouchers.destroy', $voucher) }}" method="POST" class="inline"
                    onsubmit="return confirm('Hapus voucher ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700 transition-colors">
                        Hapus Voucher
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.management.vouchers.index') }}"
                        class="rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600
                               hover:bg-gray-50 transition-colors">
                        Kembali
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gray-900 px-4 py-2 text-sm
                               font-medium text-white hover:bg-gray-700 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </div>

        </form>

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Define multiSelectModal factory BEFORE Alpine loads
        window.multiSelectModalFactory = (cfg) => ({
            items: cfg.items || [],
            name: cfg.name || 'items[]',
            open: false,
            q: '',
            selected: cfg.selected ? Array.from(cfg.selected) : [],
            get filteredItems() {
                if (!this.q) return this.items;
                return this.items.filter(i => String(i.label).toLowerCase().includes(this.q.toLowerCase()));
            },
            get allFilteredSelected() {
                const ids = this.filteredItems.map(i => i.id);
                return ids.length > 0 && ids.every(id => this.selected.includes(id));
            },
            toggleSelectAllFiltered() {
                const ids = this.filteredItems.map(i => i.id);
                if (ids.every(id => this.selected.includes(id))) {
                    this.selected = this.selected.filter(s => !ids.includes(s));
                } else {
                    const merged = new Set([...this.selected, ...ids]);
                    this.selected = Array.from(merged);
                }
            },
            selectAll() { this.selected = this.items.map(i => i.id); },
            clearSelection() { this.selected = []; },
            getLabel(id) { const it = this.items.find(i => i.id == id); return it ? it.label : id; }
        });
    </script>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Register multiSelectModal with Alpine after it loads
        document.addEventListener('alpine:init', () => {
            Alpine.data('multiSelectModal', window.multiSelectModalFactory);
        });

        // Image preview handler
        document.getElementById('image_path')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImg.src = event.target.result;
                    preview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                preview.classList.add('hidden');
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            try {
                flatpickr("#start_at", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    allowInput: true,
                    defaultDate: document.getElementById('start_at')?.value || null
                });

                flatpickr("#end_at", {
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                    time_24hr: true,
                    allowInput: true,
                    defaultDate: document.getElementById('end_at')?.value || null
                });
            } catch (e) {
                console.warn('flatpickr init failed', e);
            }
        });
    </script>
</x-layout-admin>
