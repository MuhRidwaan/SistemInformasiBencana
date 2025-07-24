<?php

namespace App\Http\Controllers;

use App\Models\Permission; // Import model Permission
use App\Models\PermissionModule; // Import model PermissionModule
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Support\Str; // Import Str facade untuk Str::startsWith

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua izin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data izin dengan eager loading relasi 'module'
        $permissions = Permission::with('module')->get();
        // Mengambil semua data modul izin untuk dropdown dan pengelompokan di view
        $permissionModules = PermissionModule::all();

        // Melewatkan kedua koleksi data ke view
        return view('admin.permissions.index', compact('permissions', 'permissionModules'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan izin baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan izin baru
        $request->validate([
            'name' => 'required|string|max:255|unique:m_permissions,name',
            'description' => 'nullable|string|max:255',
            'module_id' => 'nullable|exists:m_permission_modules,module_id', // Validasi module_id
        ], [
            'name.required' => 'Nama Izin wajib diisi.',
            'name.unique' => 'Nama Izin ini sudah ada.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
            'module_id.exists' => 'Modul izin yang dipilih tidak valid.',
        ]);

        try {
            $permission = new Permission();
            $permission->name = $request->name;
            $permission->description = $request->description;
            $permission->module_id = $request->module_id; // Simpan module_id
            // created_at dan updated_at akan diisi otomatis oleh Laravel
            $permission->save();

            return response()->json(['success' => true, 'message' => 'Izin berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding permission: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan izin: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data izin untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Permission $permission)
    {
        // $permission sudah otomatis ditemukan oleh Route Model Binding
        // Eager load relasi 'module' untuk memastikan data modul tersedia di frontend
        $permission->load('module');
        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Izin tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate izin yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Permission $permission)
    {
        // Validasi input untuk update izin
        $request->validate([
            'name' => 'required|string|max:255|unique:m_permissions,name,' . $permission->permission_id . ',permission_id', // Abaikan nama izin saat ini
            'description' => 'nullable|string|max:255',
            'module_id' => 'nullable|exists:m_permission_modules,module_id', // Validasi module_id
        ], [
            'name.required' => 'Nama Izin wajib diisi.',
            'name.unique' => 'Nama Izin ini sudah ada.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
            'module_id.exists' => 'Modul izin yang dipilih tidak valid.',
        ]);

        try {
            $permission->name = $request->name;
            $permission->description = $request->description;
            $permission->module_id = $request->module_id; // Update module_id
            // updated_at akan diisi otomatis oleh Laravel
            $permission->save();

            return response()->json(['success' => true, 'message' => 'Izin berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating permission: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui izin: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus izin dari database.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission)
    {
        try {
            // Anda mungkin ingin menambahkan validasi di sini jika izin terhubung ke role
            // Misalnya: if ($permission->roles()->count() > 0) { ... }
            $permission->delete();

            return response()->json(['success' => true, 'message' => 'Izin berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting permission: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus izin: ' . $e->getMessage()], 500);
        }
    }
}
