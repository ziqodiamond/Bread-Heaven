{{-- resources/views/components/hero.blade.php atau langsung di halaman --}}

<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#0a1628]">

    {{-- Background image --}}
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('storage/general_images/banner.jpg') }}')">
    </div>

    {{-- Overlay gelap transparan --}}
    <div class="absolute inset-0 bg-black/55"></div>

    {{-- Gradient blur halus --}}
    <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/20 to-black/60"></div>

    {{-- Content --}}
    <div class="relative z-10 text-center px-6 py-24 max-w-2xl mx-auto">

        {{-- Badge --}}
        {{-- <div
            class="inline-flex items-center gap-1.5 mb-6
                    bg-white/10 border border-white/15 text-white/80
                    text-[11px] font-medium tracking-[1.5px] uppercase
                    px-3.5 py-1.5 rounded-full backdrop-blur-sm">
            <span class="w-1.5 h-1.5 rounded-full bg-white/70"></span>
            Toko Roti Terpercaya
        </div> --}}

        {{-- Heading --}}
        <h1
            class="text-[clamp(32px,5vw,52px)] font-bold text-white leading-[1.15]
                   tracking-tight mb-5">
            Roti Premium, <br>
            Harga <span class="text-white/75">Terjangkau</span>
        </h1>

        {{-- Subtext --}}
        <p class="text-base text-white/70 leading-relaxed max-w-md mx-auto mb-9">
            Bread Heaven menghadirkan roti artisan berkualitas tinggi yang dibuat setiap hari —
            segar, lezat, dan langsung ke tanganmu.
        </p>

        {{-- CTA Buttons --}}
        <div class="flex items-center justify-center gap-3 flex-wrap">

            {{-- Tombol Belanja --}}
            <a href="/products"
                class="inline-flex items-center gap-2
                      bg-white text-gray-900 hover:bg-gray-100
                      text-sm font-semibold px-6 py-3 rounded-[9px]
                      transition-all duration-200 hover:-translate-y-0.5 shadow-lg shadow-black/20">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z" />
                    <line x1="3" y1="6" x2="21" y2="6" />
                    <path d="M16 10a4 4 0 01-8 0" />
                </svg>

                Belanja Sekarang
            </a>

            {{-- Tombol daftar hanya jika belum login --}}
            @guest
                <a href="{{ route('register') }}"
                    class="inline-flex items-center gap-2
                          bg-white/10 hover:bg-white/15
                          border border-white/20 hover:border-white/35
                          text-white text-sm font-medium px-6 py-3 rounded-[9px]
                          backdrop-blur-sm
                          transition-all duration-200">

                    Daftar Sekarang

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" viewBox="0 0 24 24">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12,5 19,12 12,19" />
                    </svg>
                </a>
            @endguest
        </div>

        {{-- Stats --}}
        <div class="flex items-center justify-center gap-8 mt-14 pt-8
                    border-t border-white/10">
            <div class="text-center">
                <div class="text-[22px] font-bold text-white leading-none">50+</div>
                <div class="text-[12px] text-white/50 mt-1 tracking-wide">Varian Roti</div>
            </div>

            <div class="w-px h-9 bg-white/10"></div>

            <div class="text-center">
                <div class="text-[22px] font-bold text-white leading-none">10rb+</div>
                <div class="text-[12px] text-white/50 mt-1 tracking-wide">Pelanggan</div>
            </div>

            <div class="w-px h-9 bg-white/10"></div>

            <div class="text-center">
                <div class="text-[22px] font-bold text-white leading-none">4.9★</div>
                <div class="text-[12px] text-white/50 mt-1 tracking-wide">Rating</div>
            </div>
        </div>

    </div>
</section>
