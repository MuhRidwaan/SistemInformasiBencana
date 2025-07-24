<?php

namespace App\Http\Controllers;

use App\Models\Kelurahan; // Import model Kelurahan
use App\Models\Kecamatan; // Import model Kecamatan untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class KelurahanController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua kelurahan dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
   public function index(Request $request)
{
    $perPage = 10;
    $search = $request->query('search');

    $query = Kelurahan::with('kecamatan.kota.provinsi', 'creator', 'changer');

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('nama_kelurahan', 'like', '%' . $search . '%')
              ->orWhere('kode_wilayah', 'like', '%' . $search . '%')
              ->orWhereHas('kecamatan', function ($q_kecamatan) use ($search) {
                  $q_kecamatan->where('nama_kecamatan', 'like', '%' . $search . '%');
              })
              ->orWhereHas('kecamatan.kota', function ($q_kota) use ($search) {
                  $q_kota->where('nama_kota', 'like', '%' . $search . '%');
              })
              ->orWhereHas('kecamatan.kota.provinsi', function ($q_provinsi) use ($search) {
                  $q_provinsi->where('nama_provinsi', 'like', '%' . $search . '%');
              });
        });
    }

    $kelurahan = $query->orderBy('nama_kelurahan')->paginate($perPage); // Langsung paginate dari query

    // Ambil semua kecamatan untuk dropdown di modal (ini juga bisa dipaginasi atau difilter jika terlalu banyak)
    $kecamatanList = Kecamatan::with('kota.provinsi')->get();

    return view('admin.kelurahan.index', compact('kelurahan', 'kecamatanList'));
}

    /**
     * Store a newly created resource in storage.
     * Menyimpan kelurahan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan kelurahan baru
        $request->validate([
            'kecamatan_id' => 'required|exists:m_dis_kecamatan,kecamatan_id',
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_kelurahan,kode_wilayah',
            'nama_kelurahan' => 'required|string|max:255',
        ], [
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_kelurahan.required' => 'Nama Kelurahan wajib diisi.',
        ]);

        try {
            $kelurahan = new Kelurahan();
            $kelurahan->kecamatan_id = $request->kecamatan_id;
            $kelurahan->kode_wilayah = $request->kode_wilayah;
            $kelurahan->nama_kelurahan = $request->nama_kelurahan;
            // create_who dan create_date akan diisi otomatis oleh boot method di model Kelurahan
            $kelurahan->save();

            return response()->json(['success' => true, 'message' => 'Kelurahan berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding kelurahan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan Kelurahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data kelurahan untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\Kelurahan  $kelurahan
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(Kelurahan $kelurahan)
    {
        // $kelurahan sudah otomatis ditemukan oleh Route Model Binding
        if (!$kelurahan) {
            return response()->json(['success' => false, 'message' => 'Kelurahan tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $kelurahan]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate kelurahan yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kelurahan  $kelurahan
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Kelurahan $kelurahan)
    {
        // Validasi input untuk update kelurahan
        $request->validate([
            'kecamatan_id' => 'required|exists:m_dis_kecamatan,kecamatan_id',
            'kode_wilayah' => 'required|string|max:255|unique:m_dis_kelurahan,kode_wilayah,' . $kelurahan->kelurahan_id . ',kelurahan_id', // Abaikan kode_wilayah saat ini
            'nama_kelurahan' => 'required|string|max:255',
        ], [
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'kode_wilayah.required' => 'Kode Wilayah wajib diisi.',
            'kode_wilayah.unique' => 'Kode Wilayah ini sudah ada.',
            'nama_kelurahan.required' => 'Nama Kelurahan wajib diisi.',
        ]);

        try {
            $kelurahan->kecamatan_id = $request->kecamatan_id;
            $kelurahan->kode_wilayah = $request->kode_wilayah;
            $kelurahan->nama_kelurahan = $request->nama_kelurahan;
            // change_who dan change_date akan diisi otomatis oleh boot method di model Kelurahan
            $kelurahan->save();

            return response()->json(['success' => true, 'message' => 'Kelurahan berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating kelurahan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui Kelurahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus kelurahan dari database.
     *
     * @param  \App\Models\Kelurahan  $kelurahan
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Kelurahan $kelurahan)
    {
        try {
            // Anda mungkin ingin menambahkan validasi di sini jika kelurahan terhubung ke data lain
            $kelurahan->delete();

            return response()->json(['success' => true, 'message' => 'Kelurahan berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting kelurahan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Kelurahan: ' . $e->getMessage()], 500);
        }
    }

     public function getKelurahanByKecamatan($kecamatan_id)
    {
        $kelurahan = Kelurahan::where('kecamatan_id', $kecamatan_id)->get(['kelurahan_id', 'nama_kelurahan']);
        return response()->json($kelurahan);
    }
}
