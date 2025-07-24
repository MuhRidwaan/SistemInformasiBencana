<?php

namespace App\Http\Controllers;

use App\Models\Kota; // Import model Kota
use App\Models\Provinsi; // Import model Provinsi untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class KotaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua kota/kabupaten dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data kota dengan eager loading provinsi, creator, dan changer
        $kotaCollection = Kota::with('provinsi', 'creator', 'changer')->get();

        // Ambil semua provinsi untuk dropdown di modal
        $provinsiList = Provinsi::all();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $kotaCollection = $kotaCollection->filter(function ($kota) use ($search) {
                return Str::contains(Str::lower($kota->nama_kota), Str::lower($search)) ||
                       Str::contains(Str::lower($kota->kode_wilayah), Str::lower($search)) ||
                       Str::contains(Str::lower($kota->provinsi->nama_provinsi ?? ''), Str::lower($search)); // Cari juga di nama provinsi
            });
        }

        // Urutkan data (opsional)
        // $kotaCollection = $kotaCollection->sortBy('nama_kota');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $kotaCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $kota = new LengthAwarePaginator($currentItems, $kotaCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.kota.index', compact('kota', 'provinsiList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan kota/kabupaten baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan kota/kabupaten baru
        $request->validate([
            'provinsi_id' => 'required|exists:m_dis_provinsi,provinsi_id',
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_kota,kode_wilayah',
            'nama_kota' => 'required|string|max:255',
        ], [
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'provinsi_id.exists' => 'Provinsi tidak valid.',
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_kota.required' => 'Nama Kota/Kabupaten wajib diisi.',
        ]);

        try {
            $kota = new Kota();
            $kota->provinsi_id = $request->provinsi_id;
            $kota->kode_wilayah = $request->kode_wilayah;
            $kota->nama_kota = $request->nama_kota;
            // create_who dan create_date akan diisi otomatis oleh boot method di model Kota
            $kota->save();

            return response()->json(['success' => true, 'message' => 'Kota/Kabupaten berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding kota: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan Kota/Kabupaten: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data kota/kabupaten untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\Kota  $kota
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Kota $kota)
    {
        // $kota sudah otomatis ditemukan oleh Route Model Binding
        if (!$kota) {
            return response()->json(['success' => false, 'message' => 'Kota/Kabupaten tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $kota]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate kota/kabupaten yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kota  $kota
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Kota $kota)
    {
        // Validasi input untuk update kota/kabupaten
        $request->validate([
            'provinsi_id' => 'required|exists:m_dis_provinsi,provinsi_id',
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_kota,kode_wilayah,' . $kota->kota_id . ',kota_id', // Abaikan kode_wilayah saat ini
            'nama_kota' => 'required|string|max:255',
        ], [
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'provinsi_id.exists' => 'Provinsi tidak valid.',
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_kota.required' => 'Nama Kota/Kabupaten wajib diisi.',
        ]);

        try {
            $kota->provinsi_id = $request->provinsi_id;
            $kota->kode_wilayah = $request->kode_wilayah;
            $kota->nama_kota = $request->nama_kota;
            // change_who dan change_date akan diisi otomatis oleh boot method di model Kota
            $kota->save();

            return response()->json(['success' => true, 'message' => 'Kota/Kabupaten berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating kota: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui Kota/Kabupaten: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus kota/kabupaten dari database.
     *
     * @param  \App\Models\Kota  $kota
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Kota $kota)
    {
        try {
            // Anda mungkin ingin menambahkan validasi di sini jika kota terhubung ke data lain (misal: kecamatan)
            $kota->delete();

            return response()->json(['success' => true, 'message' => 'Kota/Kabupaten berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting kota: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Kota/Kabupaten: ' . $e->getMessage()], 500);
        }
    }

     public function getKotaByProvinsi($provinsi_id)
    {
        $kota = Kota::where('provinsi_id', $provinsi_id)->get(['kota_id', 'nama_kota']);
        return response()->json($kota);
    }
}
