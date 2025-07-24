<?php

namespace App\Http\Controllers\Api; // Namespace diubah ke App\Http\Controllers\Api

use App\Http\Controllers\Controller; // Pastikan ini mengacu ke base Controller yang benar
use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ApiLaporanMasyarakatController extends Controller
{
    /**
     * Store a newly created resource in storage via API.
     * Menyimpan laporan masyarakat baru ke database melalui API.
     * Ini bisa diakses publik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'jenis_laporan' => 'required|string|max:50',
            'judul_laporan' => 'required|string|max:255',
            'deskripsi_laporan' => 'required|string',
            'tanggal_laporan' => 'nullable|date',
            'nama_pelapor' => 'nullable|string|max:255',
            'kontak_pelapor' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'path_foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bencana_id' => 'nullable|exists:m_bencana,bencana_id',
        ], [
            'jenis_laporan.required' => 'Jenis Laporan wajib diisi.',
            'judul_laporan.required' => 'Judul Laporan wajib diisi.',
            'deskripsi_laporan.required' => 'Deskripsi Laporan wajib diisi.',
            'tanggal_laporan.date' => 'Format Tanggal Laporan tidak valid.',
            'path_foto.image' => 'File harus berupa gambar.',
            'path_foto.mimes' => 'Format gambar yang diizinkan: jpeg, png, jpg, gif.',
            'path_foto.max' => 'Ukuran gambar maksimal 2MB.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric' => 'Longitude harus antara -180 dan 180.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $laporan = new LaporanMasyarakat();
            $laporan->jenis_laporan = $request->jenis_laporan;
            $laporan->judul_laporan = $request->judul_laporan;
            $laporan->deskripsi_laporan = $request->deskripsi_laporan;
            $laporan->tanggal_laporan = $request->tanggal_laporan ? Carbon::parse($request->tanggal_laporan) : Carbon::now();
            $laporan->nama_pelapor = $request->nama_pelapor;
            $laporan->kontak_pelapor = $request->kontak_pelapor;
            $laporan->latitude = $request->latitude;
            $laporan->longitude = $request->longitude;
            $laporan->status_laporan = 'Pending'; // Default status for public API submission
            $laporan->bencana_id = $request->bencana_id;

            if ($request->hasFile('path_foto')) {
                $path = $request->file('path_foto')->store('public/laporan_masyarakat_fotos');
                $laporan->path_foto = Storage::url($path);
            }

            // create_who akan diisi oleh boot method model, yang akan menjadi Auth::id()
            // Jika API ini publik tanpa autentikasi, Auth::id() akan null.
            // Pastikan kolom create_who di DB Anda nullable jika memungkinkan null.
            // Atau, Anda bisa set default user ID jika ada 'guest' user di sistem Anda:
            // $laporan->create_who = 1; // Contoh: user ID 1 adalah guest/system user
            $laporan->save();

            return response()->json([
                'success' => true,
                'message' => 'Laporan Masyarakat berhasil dikirim.',
                'data' => $laporan
            ], 201);

        } catch (\Exception $e) {
            \Log::error('Error submitting API laporan masyarakat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim laporan: ' . $e->getMessage()
            ], 500);
        }
    }
}
