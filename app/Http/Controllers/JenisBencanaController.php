<?php

namespace App\Http\Controllers;

use App\Models\JenisBencana; // Import model JenisBencana
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu

class JenisBencanaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua jenis bencana.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Mengambil semua data jenis bencana dengan eager loading relasi creator dan changer
        $jenisBencana = JenisBencana::with('creator', 'changer')->get();
        return view('admin.jenis_bencana.index', compact('jenisBencana'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan jenis bencana baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input untuk penambahan jenis bencana baru
        $request->validate([
            'nama_jenis' => 'required|string|max:255|unique:m_jenis_bencana,nama_jenis',
            'deskripsi_jenis' => 'nullable|string',
        ], [
            'nama_jenis.required' => 'Nama Jenis Bencana wajib diisi.',
            'nama_jenis.unique' => 'Nama Jenis Bencana ini sudah ada.',
        ]);

        try {
            $jenisBencana = new JenisBencana();
            $jenisBencana->nama_jenis = $request->nama_jenis;
            $jenisBencana->deskripsi_jenis = $request->deskripsi_jenis;
            // create_who dan create_date akan diisi otomatis oleh boot method di model JenisBencana
            $jenisBencana->save();

            return response()->json(['success' => true, 'message' => 'Jenis Bencana berhasil ditambahkan.']);

        } catch (\Exception $e) {
            \Log::error('Error adding jenis bencana: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menambahkan jenis bencana: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Mengambil data jenis bencana untuk ditampilkan di form edit (via AJAX).
     *
     * @param  \App\Models\JenisBencana  $jenisBencana
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit(JenisBencana $jenisBencana)
    {
        // $jenisBencana sudah otomatis ditemukan oleh Route Model Binding
        if (!$jenisBencana) {
            return response()->json(['success' => false, 'message' => 'Jenis Bencana tidak ditemukan.'], 404);
        }
        return response()->json(['success' => true, 'data' => $jenisBencana]);
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate jenis bencana yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JenisBencana  $jenisBencana
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, JenisBencana $jenisBencana)
    {
        // Validasi input untuk update jenis bencana
        $request->validate([
            'nama_jenis' => 'required|string|max:255|unique:m_jenis_bencana,nama_jenis,' . $jenisBencana->jenis_bencana_id . ',jenis_bencana_id', // Abaikan nama_jenis saat ini
            'deskripsi_jenis' => 'nullable|string',
        ], [
            'nama_jenis.required' => 'Nama Jenis Bencana wajib diisi.',
            'nama_jenis.unique' => 'Nama Jenis Bencana ini sudah ada.',
        ]);

        try {
            $jenisBencana->nama_jenis = $request->nama_jenis;
            $jenisBencana->deskripsi_jenis = $request->deskripsi_jenis;
            // change_who dan change_date akan diisi otomatis oleh boot method di model JenisBencana
            $jenisBencana->save();

            return response()->json(['success' => true, 'message' => 'Jenis Bencana berhasil diperbarui.']);

        } catch (\Exception $e) {
            \Log::error('Error updating jenis bencana: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat memperbarui jenis bencana: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus jenis bencana dari database.
     *
     * @param  \App\Models\JenisBencana  $jenisBencana
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(JenisBencana $jenisBencana)
    {
        try {
            // Anda mungkin ingin menambahkan validasi di sini jika jenis bencana terhubung ke data bencana lain
            $jenisBencana->delete();

            return response()->json(['success' => true, 'message' => 'Jenis Bencana berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting jenis bencana: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus jenis bencana: ' . $e->getMessage()], 500);
        }
    }
}
