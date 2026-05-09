<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    /**
     * Halaman daftar toko
     */
    public function index()
    {
        $stores = Store::latest()->paginate(10);

        return view(
            'admin.management.store.index',
            compact('stores')
        );
    }

    /**
     * Halaman tambah toko
     */
    public function create()
    {
        return view('admin.management.store.create');
    }

    /**
     * Simpan toko baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Informasi toko
            |--------------------------------------------------------------------------
            */

            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Kontak
            |--------------------------------------------------------------------------
            */

            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],

            /*
            |--------------------------------------------------------------------------
            | Branding
            |--------------------------------------------------------------------------
            */

            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],

            /*
            |--------------------------------------------------------------------------
            | Alamat
            |--------------------------------------------------------------------------
            */

            'province' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'district' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string'],
            'full_address' => ['nullable', 'string'],
            'address_notes' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | Lokasi GPS
            |--------------------------------------------------------------------------
            */

            'latitude' => ['nullable'],
            'longitude' => ['nullable'],

            'google_maps_embed' => ['nullable'],
            'google_maps_url' => ['nullable'],

            /*
            |--------------------------------------------------------------------------
            | Shipping
            |--------------------------------------------------------------------------
            */

            'allow_pickup' => ['nullable'],
            'is_shipping_origin' => ['nullable'],

            /*
            |--------------------------------------------------------------------------
            | Sosial media
            |--------------------------------------------------------------------------
            */

            'instagram' => ['nullable'],
            'tiktok' => ['nullable'],
            'facebook' => ['nullable'],
            'youtube' => ['nullable'],

            /*
            |--------------------------------------------------------------------------
            | Jam operasional
            |--------------------------------------------------------------------------
            */

            'open_time' => ['nullable'],
            'close_time' => ['nullable'],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'meta_title' => ['nullable'],
            'meta_description' => ['nullable'],

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_active' => ['nullable'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload logo
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('logo')) {

            $validated['logo'] = $request
                ->file('logo')
                ->store('stores/logos', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Upload banner
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('banner')) {

            $validated['banner'] = $request
                ->file('banner')
                ->store('stores/banners', 'public');
        }

        /*
        |--------------------------------------------------------------------------
        | Generate slug
        |--------------------------------------------------------------------------
        */

        $validated['slug'] = Str::slug($validated['name']);

        /*
        |--------------------------------------------------------------------------
        | Boolean checkbox
        |--------------------------------------------------------------------------
        */

        $validated['allow_pickup'] =
            $request->boolean('allow_pickup');

        $validated['is_shipping_origin'] =
            $request->boolean('is_shipping_origin');

        $validated['is_active'] =
            $request->boolean('is_active');

        /*
        |--------------------------------------------------------------------------
        | Simpan toko
        |--------------------------------------------------------------------------
        */

        Store::create($validated);

        return redirect()
            ->route('admin.management.stores.index')
            ->with(
                'success',
                'Toko berhasil ditambahkan'
            );
    }

    /**
     * Halaman edit toko
     */
    public function edit(Store $store)
    {
        return view(
            'admin.management.store.edit',
            compact('store')
        );
    }

    /**
     * Update toko
     */
    public function update(
        Request $request,
        Store $store
    ) {

        $validated = $request->validate([

            /*
        |--------------------------------------------------------------------------
        | Informasi toko
        |--------------------------------------------------------------------------
        */

            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            /*
        |--------------------------------------------------------------------------
        | Kontak
        |--------------------------------------------------------------------------
        */

            'email' => ['nullable', 'email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],

            /*
        |--------------------------------------------------------------------------
        | Branding
        |--------------------------------------------------------------------------
        */

            'logo' => ['nullable', 'image', 'max:2048'],
            'banner' => ['nullable', 'image', 'max:4096'],

            /*
        |--------------------------------------------------------------------------
        | Alamat
        |--------------------------------------------------------------------------
        */

            'province' => ['nullable'],
            'city' => ['nullable'],
            'district' => ['nullable'],
            'postal_code' => ['nullable'],
            'full_address' => ['nullable'],
            'address_notes' => ['nullable'],

            /*
        |--------------------------------------------------------------------------
        | Lokasi GPS
        |--------------------------------------------------------------------------
        */

            'latitude' => ['nullable'],
            'longitude' => ['nullable'],

            'google_maps_embed' => ['nullable'],
            'google_maps_url' => ['nullable'],

            /*
        |--------------------------------------------------------------------------
        | Sosial media
        |--------------------------------------------------------------------------
        */

            'instagram' => ['nullable'],
            'tiktok' => ['nullable'],
            'facebook' => ['nullable'],
            'youtube' => ['nullable'],

            /*
        |--------------------------------------------------------------------------
        | Jam operasional
        |--------------------------------------------------------------------------
        */

            'open_time' => ['nullable'],
            'close_time' => ['nullable'],

            /*
        |--------------------------------------------------------------------------
        | SEO
        |--------------------------------------------------------------------------
        */

            'meta_title' => ['nullable'],
            'meta_description' => ['nullable'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Upload logo baru
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('logo')) {

            // Hapus logo lama
            if ($store->logo) {

                Storage::disk('public')
                    ->delete($store->logo);
            }

            $validated['logo'] = $request
                ->file('logo')
                ->store('stores/logos', 'public');
        }

        /*
    |--------------------------------------------------------------------------
    | Upload banner baru
    |--------------------------------------------------------------------------
    */

        if ($request->hasFile('banner')) {

            // Hapus banner lama
            if ($store->banner) {

                Storage::disk('public')
                    ->delete($store->banner);
            }

            $validated['banner'] = $request
                ->file('banner')
                ->store('stores/banners', 'public');
        }
        /*
|--------------------------------------------------------------------------
| Hapus logo jika diminta (tombol "Hapus logo" di view)
|--------------------------------------------------------------------------
*/

        if ($request->boolean('remove_logo') && !$request->hasFile('logo')) {

            if ($store->logo) {
                Storage::disk('public')->delete($store->logo);
            }

            $validated['logo'] = null;
        }
        /*
|--------------------------------------------------------------------------
| Hapus banner jika diminta
|--------------------------------------------------------------------------
*/

        if ($request->boolean('remove_banner') && !$request->hasFile('banner')) {

            if ($store->banner) {
                Storage::disk('public')->delete($store->banner);
            }

            $validated['banner'] = null;
        }
        /*
    |--------------------------------------------------------------------------
    | Update slug
    |--------------------------------------------------------------------------
    */

        $validated['slug'] =
            \Illuminate\Support\Str::slug(
                $validated['name']
            );

        /*
    |--------------------------------------------------------------------------
    | Boolean
    |--------------------------------------------------------------------------
    */

        $validated['allow_pickup'] =
            $request->boolean('allow_pickup');

        $validated['is_shipping_origin'] =
            $request->boolean('is_shipping_origin');

        $validated['is_active'] =
            $request->boolean('is_active');

        /*
    |--------------------------------------------------------------------------
    | Update data
    |--------------------------------------------------------------------------
    */

        $store->update($validated);

        return redirect()
            ->route('admin.management.stores.index')
            ->with(
                'success',
                'Toko berhasil diperbarui'
            );
    }
}
