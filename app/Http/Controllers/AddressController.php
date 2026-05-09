<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Daftar alamat user
     */
    public function index()
    {
        $addresses = auth()->user()
            ->addresses()
            ->latest()
            ->get();

        return view(
            'address.index',
            compact('addresses')
        );
    }

    /**
     * Form tambah alamat
     */
    public function create()
    {
        return view('address.create');
    }

    /**
     * Simpan alamat baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Informasi penerima
            |--------------------------------------------------------------------------
            */

            'receiver_name' => [
                'required',
                'string',
                'max:255',
            ],

            'receiver_phone' => [
                'required',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Informasi wilayah
            |--------------------------------------------------------------------------
            */

            'province' => [
                'required',
                'string',
            ],

            'city' => [
                'required',
                'string',
            ],

            'district' => [
                'required',
                'string',
            ],

            'postal_code' => [
                'required',
                'string',
                'max:10',
            ],

            /*
            |--------------------------------------------------------------------------
            | Detail alamat
            |--------------------------------------------------------------------------
            */

            'full_address' => [
                'required',
                'string',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | Lokasi GPS
            |--------------------------------------------------------------------------
            */

            'latitude' => [
                'nullable',
            ],

            'longitude' => [
                'nullable',
            ],

            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'is_default' => [
                'nullable',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Relasi user
        |--------------------------------------------------------------------------
        */

        $validated['user_id'] = auth()->id();

        /*
        |--------------------------------------------------------------------------
        | Boolean
        |--------------------------------------------------------------------------
        */

        $validated['is_default'] =
            $request->boolean('is_default');

        $validated['is_active'] = true;

        /*
        |--------------------------------------------------------------------------
        | Simpan alamat
        |--------------------------------------------------------------------------
        */

        UserAddress::create($validated);

        return redirect()
            ->route('address.index')
            ->with(
                'success',
                'Alamat berhasil ditambahkan'
            );
    }

    /**
     * Form edit alamat
     */
    public function edit(UserAddress $address)
    {
        abort_if(
            $address->user_id !== auth()->id(),
            403
        );

        return view(
            'address.edit',
            compact('address')
        );
    }

    /**
     * Update alamat
     */
    public function update(
        Request $request,
        UserAddress $address
    ) {

        abort_if(
            $address->user_id !== auth()->id(),
            403
        );

        $validated = $request->validate([

            'receiver_name' => [
                'required',
                'string',
                'max:255',
            ],

            'receiver_phone' => [
                'required',
                'string',
                'max:20',
            ],

            'province' => [
                'required',
                'string',
            ],

            'city' => [
                'required',
                'string',
            ],

            'district' => [
                'required',
                'string',
            ],

            'postal_code' => [
                'required',
                'string',
                'max:10',
            ],

            'full_address' => [
                'required',
                'string',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'latitude' => [
                'nullable',
            ],

            'longitude' => [
                'nullable',
            ],

            'is_default' => [
                'nullable',
            ],

        ]);

        /*
        |--------------------------------------------------------------------------
        | Boolean
        |--------------------------------------------------------------------------
        */

        $validated['is_default'] =
            $request->boolean('is_default');

        /*
        |--------------------------------------------------------------------------
        | Update alamat
        |--------------------------------------------------------------------------
        */

        $address->update($validated);

        return redirect()
            ->route('address.index')
            ->with(
                'success',
                'Alamat berhasil diperbarui'
            );
    }

    /**
     * Hapus alamat
     */
    public function destroy(
        UserAddress $address
    ) {

        abort_if(
            $address->user_id !== auth()->id(),
            403
        );

        $address->delete();

        return redirect()
            ->route('address.index')
            ->with(
                'success',
                'Alamat berhasil dihapus'
            );
    }

    /**
     * Set default address
     */
    public function setDefault(
        UserAddress $address
    ) {

        abort_if(
            $address->user_id !== auth()->id(),
            403
        );

        $address->setAsDefault();

        return back()->with(
            'success',
            'Alamat utama berhasil diubah'
        );
    }
}
