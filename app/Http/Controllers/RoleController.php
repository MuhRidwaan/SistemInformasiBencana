<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission; // Import model Permission
use App\Models\PermissionModule; // Import model PermissionModule
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Illuminate\Support\Facades\Session; // Untuk flash messages
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Support\Str; // Import Str facade untuk Str::startsWith

class RoleController extends Controller
{
    /**
     * Menampilkan daftar semua role.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil semua role dengan eager loading relasi 'permissions'
        $roles = Role::with('permissions')->get();
        // Ambil semua permissions untuk checkbox di modal
        $permissions = Permission::all();
        // Ambil semua modul izin untuk pengelompokan di view
        $permissionModules = PermissionModule::all();

        return view('admin.roles.index', compact('roles', 'permissions', 'permissionModules'));
    }

    /**
     * Menyimpan role baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input dari form
        $request->validate([
            'nama_role' => 'required|string|max:100|unique:m_roles,nama_role',
            'deskripsi_role' => 'nullable|string',
            'permissions' => 'array', // Validasi bahwa permissions adalah array
            'permissions.*' => 'exists:m_permissions,permission_id', // Validasi setiap permission_id ada di tabel
        ], [
            'nama_role.required' => 'Nama Role wajib diisi.',
            'nama_role.unique' => 'Nama Role ini sudah ada.',
            'nama_role.max' => 'Nama Role tidak boleh lebih dari 100 karakter.',
            'permissions.*.exists' => 'Salah satu izin yang dipilih tidak valid.',
        ]);

        try {
            $role = new Role();
            $role->nama_role = $request->nama_role;
            $role->deskripsi_role = $request->deskripsi_role;
            // create_who dan create_date akan diisi otomatis oleh boot method di model Role
            $role->save(); // Simpan role ke database

            // Sinkronkan izin yang dipilih
            $role->permissions()->sync($request->input('permissions', []));

            return response()->json(['success' => true, 'message' => 'Role berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan role: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil data role untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Role $role)
    {
        // Eager load permissions yang dimiliki role ini
        $role->load('permissions');
        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $role]);
    }

    /**
     * Mengupdate role yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Role $role)
    {
        // Validasi input dari form
        $request->validate([
            'nama_role' => 'required|string|max:100|unique:m_roles,nama_role,' . $role->role_id . ',role_id', // Abaikan nama_role saat ini
            'deskripsi_role' => 'nullable|string',
            'permissions' => 'array', // Validasi bahwa permissions adalah array
            'permissions.*' => 'exists:m_permissions,permission_id', // Validasi setiap permission_id ada di tabel
        ], [
            'nama_role.required' => 'Nama Role wajib diisi.',
            'nama_role.unique' => 'Nama Role ini sudah ada.',
            'nama_role.max' => 'Nama Role tidak boleh lebih dari 100 karakter.',
            'permissions.*.exists' => 'Salah satu izin yang dipilih tidak valid.',
        ]);

        try {
            $role->nama_role = $request->nama_role;
            $role->deskripsi_role = $request->deskripsi_role;
            // change_who dan change_date akan diisi otomatis oleh boot method di model Role
            $role->save();

            // Sinkronkan izin yang dipilih
            $role->permissions()->sync($request->input('permissions', []));

            return response()->json(['success' => true, 'message' => 'Role berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui role: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus role dari database.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role)
    {
        try {
            // Hapus relasi di tabel pivot terlebih dahulu
            $role->permissions()->detach();
            $role->delete();

            return response()->json(['success' => true, 'message' => 'Role berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting role: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus role: ' . $e->getMessage()], 500);
        }
    }
}
