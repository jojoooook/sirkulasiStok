<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('pages.setting.index', compact('users'));
    }

    public function create()
    {
        return view('pages.setting.create');
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:admin,karyawan',
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'name.required' => 'Nama wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
        ]);

        try {
            User::create([
                'username' => $request->username,
                'name' => $request->name,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            return redirect()->route('setting.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Terjadi kesalahan saat menyimpan data pengguna: ' . $e->getMessage());
            return redirect()->route('setting.create')->with('error', 'Gagal menambahkan pengguna.');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('pages.setting.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi update data
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'name' => 'required|string|max:255',
            'role' => 'required|in:admin,karyawan',
            'current_password' => 'nullable|string', // Validasi password lama (optional)
            'password' => 'nullable|string|min:6|confirmed', // Validasi password baru (optional)
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'name.required' => 'Nama wajib diisi.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        // Update data pengguna
        $user->update([
            'username' => $request->username,
            'name' => $request->name,
            'role' => $request->role,
        ]);

        // Verifikasi password lama dan update password jika valid
        if ($request->filled('current_password')) {
            // Pastikan password lama benar
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->route('setting.edit', $user->id)->with('error', 'Password lama salah!');
            }

            // Update password
            $user->password = bcrypt($request->password);
            $user->save();
        }

        return redirect()->route('setting.index')->with('success', 'Pengguna berhasil diperbarui.');
    }


    public function toggleActive($id)
    {
        $user = User::findOrFail($id);
        $user->active = !$user->active;
        $user->save();

        $status = $user->active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('setting.index')->with('success', "Pengguna berhasil $status.");
    }

    // Method untuk reset password
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        // Set password default '123456'
        $user->password = bcrypt('123456');
        $user->save();

        return redirect()->route('setting.edit', $user->id)->with('success', 'Password berhasil direset menjadi 123456.');
    }
}
