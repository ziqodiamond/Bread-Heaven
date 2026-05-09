<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class Usercontroller extends Controller
{
    // Menampilkan semua pengguna
    public function index()
    {
        $users = User::all();
        return view('admin.management.users.index', compact('users'));
    }

    // Menampilkan halaman edit pengguna
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.management.users.partials.edit', compact('user'));
    }

    // Memperbarui data pengguna
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:15',
        ]);

        $user->update($request->only('name', 'email', 'phone'));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    // Menghapus pengguna
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully');
    }

    // Promosi pengguna menjadi admin
    public function promote($id)
    {
        $user = User::findOrFail($id);
        $user->role = 'admin';
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User promoted to admin');
    }
}
