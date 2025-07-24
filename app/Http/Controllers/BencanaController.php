<?php

namespace App\Http\Controllers;

use App\Models\Bencana;
use App\Models\JenisBencana;
use App\Models\Provinsi;
use App\Models\Kota;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BencanaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua bencana dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data bencana dengan eager loading relasi
        $bencanaCollection = Bencana::with(
            'jenisBencana',
            'provinsi',
            'kota',
            'kecamatan',
            'kelurahan',
            'creator',
            'changer'
        )->get();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $bencanaCollection = $bencanaCollection->filter(function ($bencana) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($bencana->nama_bencana), $searchLower) ||
                       Str::contains(Str::lower($bencana->kronologis), $searchLower) ||
                       Str::contains(Str::lower($bencana->deskripsi), $searchLower) ||
                       Str::contains(Str::lower($bencana->jenisBencana->nama_jenis ?? ''), $searchLower) ||
                       Str::contains(Str::lower($bencana->provinsi->nama_provinsi ?? ''), $searchLower) ||
                       Str::contains(Str::lower($bencana->kota->nama_kota ?? ''), $searchLower) ||
                       Str::contains(Str::lower($bencana->kecamatan->nama_kecamatan ?? ''), $searchLower) ||
                       Str::contains(Str::lower($bencana->kelurahan->nama_kelurahan ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $bencanaCollection = $bencanaCollection->sortByDesc('tanggal_kejadian');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $bencanaCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $bencana = new LengthAwarePaginator($currentItems, $bencanaCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        // Hanya passing $bencana ke view index
        return view('admin.bencana.index', compact('bencana'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat data bencana baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $jenisBencanaList = JenisBencana::all();
        $provinsiList = Provinsi::all();
        return view('admin.bencana.create', compact('jenisBencanaList', 'provinsiList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan data bencana baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'jenis_bencana_id' => 'required|exists:m_jenis_bencana,jenis_bencana_id',
            'nama_bencana' => 'required|string|max:255',
            'kronologis' => 'required|string',
            'deskripsi' => 'required|string',
            'tanggal_kejadian' => 'required|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'provinsi_id' => 'required|exists:m_dis_provinsi,provinsi_id',
            'kota_id' => 'required|exists:m_dis_kota,kota_id',
            'kecamatan_id' => 'required|exists:m_dis_kecamatan,kecamatan_id',
            'kelurahan_id' => 'required|exists:m_dis_kelurahan,kelurahan_id',
        ], [
            'jenis_bencana_id.required' => 'Jenis Bencana wajib dipilih.',
            'jenis_bencana_id.exists' => 'Jenis Bencana tidak valid.',
            'nama_bencana.required' => 'Nama Bencana wajib diisi.',
            'kronologis.required' => 'Kronologis wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'tanggal_kejadian.required' => 'Tanggal Kejadian wajib diisi.',
            'tanggal_kejadian.date' => 'Format Tanggal Kejadian tidak valid.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'provinsi_id.exists' => 'Provinsi tidak valid.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kota_id.exists' => 'Kota/Kabupaten tidak valid.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.exists' => 'Kelurahan tidak valid.',
        ]);

        try {
            $bencana = new Bencana();
            $bencana->jenis_bencana_id = $request->jenis_bencana_id;
            $bencana->nama_bencana = $request->nama_bencana;
            $bencana->kronologis = $request->kronologis;
            $bencana->deskripsi = $request->deskripsi;
            $bencana->tanggal_kejadian = Carbon::parse($request->tanggal_kejadian);
            $bencana->latitude = $request->latitude;
            $bencana->longitude = $request->longitude;
            $bencana->provinsi_id = $request->provinsi_id;
            $bencana->kota_id = $request->kota_id;
            $bencana->kecamatan_id = $request->kecamatan_id;
            $bencana->kelurahan_id = $request->kelurahan_id;
            $bencana->save();

            // Setelah berhasil menyimpan, redirect ke halaman index dengan flash message
            return redirect()->route('bencana.index')->with('success', 'Data Bencana berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding bencana: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Data Bencana: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit data bencana.
     *
     * @param  \App\Models\Bencana  $bencana
     * @return \Illuminate\View\View
     */
    public function edit(Bencana $bencana)
    {
        // Eager load relasi yang dibutuhkan untuk mengisi form edit
        $bencana->load('jenisBencana', 'provinsi', 'kota', 'kecamatan', 'kelurahan');

        $jenisBencanaList = JenisBencana::all();
        $provinsiList = Provinsi::all();

        // Ambil data cascading untuk mengisi dropdown saat edit
        $kotaList = $bencana->provinsi_id ? Kota::where('provinsi_id', $bencana->provinsi_id)->get() : collect();
        $kecamatanList = $bencana->kota_id ? Kecamatan::where('kota_id', $bencana->kota_id)->get() : collect();
        $kelurahanList = $bencana->kecamatan_id ? Kelurahan::where('kecamatan_id', $bencana->kecamatan_id)->get() : collect();

        return view('admin.bencana.edit', compact('bencana', 'jenisBencanaList', 'provinsiList', 'kotaList', 'kecamatanList', 'kelurahanList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate data bencana yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bencana  $bencana
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Bencana $bencana)
    {
        // Validasi input
        $request->validate([
            'jenis_bencana_id' => 'required|exists:m_jenis_bencana,jenis_bencana_id',
            'nama_bencana' => 'required|string|max:255',
            'kronologis' => 'required|string',
            'deskripsi' => 'required|string',
            'tanggal_kejadian' => 'required|date',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'provinsi_id' => 'required|exists:m_dis_provinsi,provinsi_id',
            'kota_id' => 'required|exists:m_dis_kota,kota_id',
            'kecamatan_id' => 'required|exists:m_dis_kecamatan,kecamatan_id',
            'kelurahan_id' => 'required|exists:m_dis_kelurahan,kelurahan_id',
        ], [
            'jenis_bencana_id.required' => 'Jenis Bencana wajib dipilih.',
            'jenis_bencana_id.exists' => 'Jenis Bencana tidak valid.',
            'nama_bencana.required' => 'Nama Bencana wajib diisi.',
            'kronologis.required' => 'Kronologis wajib diisi.',
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'tanggal_kejadian.required' => 'Tanggal Kejadian wajib diisi.',
            'tanggal_kejadian.date' => 'Format Tanggal Kejadian tidak valid.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'provinsi_id.required' => 'Provinsi wajib dipilih.',
            'provinsi_id.exists' => 'Provinsi tidak valid.',
            'kota_id.required' => 'Kota/Kabupaten wajib dipilih.',
            'kota_id.exists' => 'Kota/Kabupaten tidak valid.',
            'kecamatan_id.required' => 'Kecamatan wajib dipilih.',
            'kecamatan_id.exists' => 'Kecamatan tidak valid.',
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.exists' => 'Kelurahan tidak valid.',
        ]);

        try {
            $bencana->jenis_bencana_id = $request->jenis_bencana_id;
            $bencana->nama_bencana = $request->nama_bencana;
            $bencana->kronologis = $request->kronologis;
            $bencana->deskripsi = $request->deskripsi;
            $bencana->tanggal_kejadian = Carbon::parse($request->tanggal_kejadian);
            $bencana->latitude = $request->latitude;
            $bencana->longitude = $request->longitude;
            $bencana->provinsi_id = $request->provinsi_id;
            $bencana->kota_id = $request->kota_id;
            $bencana->kecamatan_id = $request->kecamatan_id;
            $bencana->kelurahan_id = $request->kelurahan_id;
            $bencana->save();

            // Setelah berhasil menyimpan, redirect ke halaman index dengan flash message
            return redirect()->route('bencana.index')->with('success', 'Data Bencana berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating bencana: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Data Bencana: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus data bencana dari database.
     *
     * @param  \App\Models\Bencana  $bencana
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Bencana $bencana)
    {
        try {
            $bencana->delete();

            return response()->json(['success' => true, 'message' => 'Data Bencana berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting bencana: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Data Bencana: ' . $e->getMessage()], 500);
        }
    }
}
