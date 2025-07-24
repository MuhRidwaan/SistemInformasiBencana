<?php

namespace App\Http\Controllers;

use App\Models\Kecamatan; // Import model Kecamatan
use App\Models\Kota; // Import model Kota untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class KecamatanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua kecamatan dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data kecamatan dengan eager loading kota, creator, dan changer
        $kecamatanCollection = Kecamatan::with('kota.provinsi', 'creator', 'changer')->get(); // Load provinsi juga

        // Ambil semua kota untuk dropdown di modal
        $kotaList = Kota::all();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $kecamatanCollection = $kecamatanCollection->filter(function ($kecamatan) use ($search) {
                return Str::contains(Str::lower($kecamatan->nama_kecamatan), Str::lower($search)) ||
                       Str::contains(Str::lower($kecamatan->kode_wilayah), Str::lower($search)) ||
                       Str::contains(Str::lower($kecamatan->kota->nama_kota ?? ''), Str::lower($search)) || // Cari di nama kota
                       Str::contains(Str::lower($kecamatan->kota->provinsi->nama_provinsi ?? ''), Str::lower($search)); // Cari di nama provinsi
            });
        }

        // Urutkan data (opsional)
        // $kecamatanCollection = $kecamatanCollection->sortBy('nama_kecamatan');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $kecamatanCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $kecamatan = new LengthAwarePaginator($currentItems, $kecamatanCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.kecamatan.index', compact('kecamatan', 'kotaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan kecamatan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan kecamatan baru
        $request->validate([
            'kota_id' => 'required|exists:m_dis_kota,kota_id',
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_kecamatan,kode_wilayah',
            'nama_kecamatan' => 'required|string|max:255',
        ], [
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kota_id.exists' => 'Kota/Kabupaten tidak valid.',
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_kecamatan.required' => 'Nama Kecamatan wajib diisi.',
        ]);

        try {
            $kecamatan = new Kecamatan();
            $kecamatan->kota_id = $request->kota_id;
            $kecamatan->kode_wilayah = $request->kode_wilayah;
            $kecamatan->nama_kecamatan = $request->nama_kecamatan;
            // create_who dan create_date akan diisi otomatis oleh boot method di model Kecamatan
            $kecamatan->save();

            return response()->json(['success' => true, 'message' => 'Kecamatan berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding kecamatan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan Kecamatan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data kecamatan untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Kecamatan $kecamatan)
    {
        // $kecamatan sudah otomatis ditemukan oleh Route Model Binding
        if (!$kecamatan) {
            return response()->json(['success' => false, 'message' => 'Kecamatan tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $kecamatan]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate kecamatan yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Kecamatan $kecamatan)
    {
        // Validasi input untuk update kecamatan
        $request->validate([
            'kota_id' => 'required|exists:m_dis_kota,kota_id',
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_kecamatan,kode_wilayah,' . $kecamatan->kecamatan_id . ',kecamatan_id', // Abaikan kode_wilayah saat ini
            'nama_kecamatan' => 'required|string|max:255',
        ], [
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kota_id.exists' => 'Kota/Kabupaten tidak valid.',
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_kecamatan.required' => 'Nama Kecamatan wajib diisi.',
        ]);

        try {
            $kecamatan->kota_id = $request->kota_id;
            $kecamatan->kode_wilayah = $request->kode_wilayah;
            $kecamatan->nama_kecamatan = $request->nama_kecamatan;
            // change_who dan change_date akan diisi otomatis oleh boot method di model Kecamatan
            $kecamatan->save();

            return response()->json(['success' => true, 'message' => 'Kecamatan berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating kecamatan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui Kecamatan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus kecamatan dari database.
     *
     * @param  \App\Models\Kecamatan  $kecamatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Kecamatan $kecamatan)
    {
        try {
            // Anda mungkin ingin menambahkan validasi di sini jika kecamatan terhubung ke data lain (misal: kelurahan)
            $kecamatan->delete();

            return response()->json(['success' => true, 'message' => 'Kecamatan berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting kecamatan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Kecamatan: ' . $e->getMessage()], 500);
        }
    }

      public function getKecamatanByKota($kota_id)
    {
        $kecamatan = Kecamatan::where('kota_id', $kota_id)->get(['kecamatan_id', 'nama_kecamatan']);
        return response()->json($kecamatan);
    }
}
