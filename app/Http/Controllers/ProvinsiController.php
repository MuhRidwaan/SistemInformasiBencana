<?php

namespace App\Http\Controllers;

use App\Models\Provinsi; // Import model Provinsi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade (PERBAIKAN INI)

class ProvinsiController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua provinsi dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data provinsi dengan eager loading creator dan changer
        // Penting: Ambil semua data terlebih dahulu karena pagination manual akan dilakukan di PHP
        $provinsiCollection = Provinsi::with('creator', 'changer')->get();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            // Menggunakan Str::contains dan Str::lower dari Illuminate\Support\Str
            $provinsiCollection = $provinsiCollection->filter(function ($provinsi) use ($search) {
                return Str::contains(Str::lower($provinsi->nama_provinsi), Str::lower($search)) ||
                       Str::contains(Str::lower($provinsi->kode_wilayah), Str::lower($search));
            });
        }

        // Urutkan data (opsional, jika tidak ingin mengandalkan default database)
        // $provinsiCollection = $provinsiCollection->sortBy('nama_provinsi');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $provinsiCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $provinsi = new LengthAwarePaginator($currentItems, $provinsiCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.provinsi.index', compact('provinsi'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan provinsi baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan provinsi baru
        $request->validate([
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_provinsi,kode_wilayah',
            'nama_provinsi' => 'required|string|max:255',
        ], [
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_provinsi.required' => 'Nama Provinsi wajib diisi.',
        ]);

        try {
            $provinsi = new Provinsi();
            $provinsi->kode_wilayah = $request->kode_wilayah;
            $provinsi->nama_provinsi = $request->nama_provinsi;
            // create_who dan create_date akan diisi otomatis oleh boot method di model Provinsi
            $provinsi->save();

            return response()->json(['success' => true, 'message' => 'Provinsi berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding provinsi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan provinsi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data provinsi untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Provinsi $provinsi)
    {
        // $provinsi sudah otomatis ditemukan oleh Route Model Binding
        if (!$provinsi) {
            return response()->json(['success' => false, 'message' => 'Provinsi tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $provinsi]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate provinsi yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Provinsi $provinsi)
    {
        // Validasi input untuk update provinsi
        $request->validate([
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_provinsi,kode_wilayah,' . $provinsi->provinsi_id . ',provinsi_id', // Abaikan kode_wilayah saat ini
            'nama_provinsi' => 'required|string|max:255',
        ], [
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_provinsi.required' => 'Nama Provinsi wajib diisi.',
        ]);

        try {
            $provinsi->kode_wilayah = $request->kode_wilayah;
            $provinsi->nama_provinsi = $request->nama_provinsi;
            // change_who dan change_date akan diisi otomatis oleh boot method di model Provinsi
            $provinsi->save();

            return response()->json(['success' => true, 'message' => 'Provinsi berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating provinsi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui provinsi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus provinsi dari database.
     *
     * @param  \App\Models\Provinsi  $provinsi
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Provinsi $provinsi)
    {
        try {
            // Anda mungkin ingin menambahkan validasi di sini jika provinsi terhubung ke data kota/kabupaten
            $provinsi->delete();

            return response()->json(['success' => true, 'message' => 'Provinsi berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting provinsi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus provinsi: ' . $e->getMessage()], 500);
        }
    }
}
