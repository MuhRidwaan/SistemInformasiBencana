<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role; // Import model Role
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan ID user yang login
use Carbon\Carbon; // Untuk tanggal dan waktu

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil semua data user dengan eager loading relasi 'role' untuk ditampilkan di tabel
        $users = User::with('role')->get();
        $roles = Role::all(); // Ambil semua roles untuk dropdown di modal
        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan user baru
        $request->validate([
            'username' => 'required|string|max:255|unique:m_users,username',
            'password' => 'required|string|min:6', // Password untuk create
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:m_users,email',
            'kontak' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'role_id' => 'required|exists:m_roles,role_id', // Validasi role_id
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role_id.required' => 'Peran wajib dipilih.',
            'role_id.exists' => 'Peran tidak valid.',
        ]);

        try {
            $user = new User();
            $user->username = $request->username;
            $user->password_hash = $request->password; // Mutator akan otomatis hash
            $user->nama_lengkap = $request->nama_lengkap;
            $user->email = $request->email;
            $user->kontak = $request->kontak;
            $user->is_active = $request->has('is_active') ? true : false;
            $user->role_id = $request->role_id; // Simpan role_id
            $user->create_who = Auth::id(); // Ambil ID user yang sedang login
            $user->create_date = Carbon::now();
            $user->save();

            return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * (This method will be used by AJAX to fetch user data for the modal)
     */
    public function edit($id)
    {
        $user = User::with('role')->find($id); // Eager load role
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi input untuk update user
        $request->validate([
            'username' => 'required|string|max:255|unique:m_users,username,' . $id . ',user_id',
            'password' => 'nullable|string|min:6', // Password opsional untuk update
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:m_users,email,' . $id . ',user_id',
            'kontak' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'role_id' => 'required|exists:m_roles,role_id', // Validasi role_id
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah terdaftar.',
            'password.min' => 'Password minimal 6 karakter.',
            'nama_lengkap.required' => 'Nama Lengkap wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'role_id.required' => 'Peran wajib dipilih.',
            'role_id.exists' => 'Peran tidak valid.',
        ]);

        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
            }

            $user->username = $request->username;
            if ($request->filled('password')) { // Hanya update password jika diisi
                $user->password_hash = $request->password; // Mutator akan otomatis hash
            }
            $user->nama_lengkap = $request->nama_lengkap;
            $user->email = $request->email;
            $user->kontak = $request->kontak;
            $user->is_active = $request->has('is_active') ? true : false;
            $user->role_id = $request->role_id; // Update role_id
            $user->change_who = Auth::id(); // Ambil ID user yang sedang login
            $user->change_date = Carbon::now();
            $user->save();

            return response()->json(['success' => true, 'message' => 'User berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui user: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User tidak ditemukan.'], 404);
            }

            $user->delete();
            return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);

        } catch (\Exception $e) {
            \Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus user: ' . $e->getMessage()], 500);
        }
    }
}
