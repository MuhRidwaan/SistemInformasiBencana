<?php

namespace App\Http\Controllers;

use App\Models\PermissionModule; // Import model PermissionModule
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu

class PermissionModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua modul izin.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data modul izin untuk ditampilkan di tabel
        $permissionModules = PermissionModule::all();
        return view('admin.permission_modules.index', compact('permissionModules'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan modul izin baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan modul izin baru
        $request->validate([
            'name' => 'required|string|max:255|unique:m_permission_modules,name',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama Modul wajib diisi.',
            'name.unique' => 'Nama Modul ini sudah ada.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            $module = new PermissionModule();
            $module->name = $request->name;
            $module->description = $request->description;
            // created_at dan updated_at akan diisi otomatis oleh Laravel
            $module->save();

            return response()->json(['success' => true, 'message' => 'Modul Izin berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding permission module: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan modul izin: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data modul izin untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\PermissionModule  $permissionModule
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(PermissionModule $permissionModule)
    {
        // $permissionModule sudah otomatis ditemukan oleh Route Model Binding
        if (!$permissionModule) {
            return response()->json(['success' => false, 'message' => 'Modul Izin tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $permissionModule]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate modul izin yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PermissionModule  $permissionModule
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, PermissionModule $permissionModule)
    {
        // Validasi input untuk update modul izin
        $request->validate([
            'name' => 'required|string|max:255|unique:m_permission_modules,name,' . $permissionModule->module_id . ',module_id', // Abaikan nama modul saat ini
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama Modul wajib diisi.',
            'name.unique' => 'Nama Modul ini sudah ada.',
            'description.max' => 'Deskripsi tidak boleh lebih dari 255 karakter.',
        ]);

        try {
            $permissionModule->name = $request->name;
            $permissionModule->description = $request->description;
            // updated_at akan diisi otomatis oleh Laravel
            $permissionModule->save();

            return response()->json(['success' => true, 'message' => 'Modul Izin berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating permission module: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui modul izin: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus modul izin dari database.
     *
     * @param  \App\Models\PermissionModule  $permissionModule
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(PermissionModule $permissionModule)
    {
        try {
            // Penting: Sebelum menghapus modul, pastikan tidak ada izin yang masih terhubung dengannya.
            // Anda bisa menambahkan validasi di sini atau mengubah foreign key di m_permissions menjadi CASCADE ON DELETE
            // Jika Anda ingin menghapus semua izin yang terkait saat modul dihapus, ubah onDelete('set null') menjadi onDelete('cascade')
            // di migrasi add_module_id_to_m_permissions_table.php
            if ($permissionModule->permissions()->count() > 0) {
                return response()->json(['success' => false, 'message' => 'Tidak dapat menghapus modul karena masih ada izin yang terhubung.'], 400);
            }

            $permissionModule->delete();

            return response()->json(['success' => true, 'message' => 'Modul Izin berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting permission module: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus modul izin: ' . $e->getMessage()], 500);
        }
    }
}
