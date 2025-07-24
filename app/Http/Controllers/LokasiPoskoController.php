<?php

namespace App\Http\Controllers;

use App\Models\LokasiPosko; // Import model LokasiPosko
use App\Models\Bencana; // Import model Bencana untuk dropdown
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Untuk Auth::id()
use Carbon\Carbon; // Untuk tanggal dan waktu
use Illuminate\Pagination\LengthAwarePaginator; // Untuk pagination manual
use Illuminate\Support\Collection; // Untuk Collection
use Illuminate\Support\Str; // Import Str facade

class LokasiPoskoController extends Controller
{
    /**
     * Display a listing of the resource.
     * Menampilkan daftar semua lokasi posko dengan pagination manual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Ambil semua data lokasi posko dengan eager loading bencana, creator, dan changer
        $lokasiPoskoCollection = LokasiPosko::with('bencana', 'creator', 'changer')->get();

        // Filter data jika ada pencarian
        $search = $request->query('search');
        if ($search) {
            $lokasiPoskoCollection = $lokasiPoskoCollection->filter(function ($posko) use ($search) {
                $searchLower = Str::lower($search);
                return Str::contains(Str::lower($posko->nama_posko), $searchLower) ||
                       Str::contains(Str::lower($posko->alamat_posko), $searchLower) ||
                       Str::contains(Str::lower($posko->kontak_person ?? ''), $searchLower) ||
                       Str::contains(Str::lower($posko->bencana->nama_bencana ?? ''), $searchLower);
            });
        }

        // Urutkan data (opsional)
        $lokasiPoskoCollection = $lokasiPoskoCollection->sortByDesc('create_date');

        // Pagination manual
        $perPage = 10; // Jumlah item per halaman
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $lokasiPoskoCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $lokasiPosko = new LengthAwarePaginator($currentItems, $lokasiPoskoCollection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
        ]);

        return view('admin.lokasi_posko.index', compact('lokasiPosko'));
    }

    /**
     * Show the form for creating a new resource.
     * Menampilkan form untuk membuat lokasi posko baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.lokasi_posko.create', compact('bencanaList'));
    }

    /**
     * Store a newly created resource in storage.
     * Menyimpan lokasi posko baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'nama_posko' => 'required|string|max:255',
            'alamat_posko' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'kapasitas' => 'nullable|integer|min:0',
            'kontak_person' => 'nullable|string|max:255',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'nama_posko.required' => 'Nama Posko wajib diisi.',
            'alamat_posko.required' => 'Alamat Posko wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas tidak boleh negatif.',
        ]);

        try {
            $posko = new LokasiPosko();
            $posko->bencana_id = $request->bencana_id;
            $posko->nama_posko = $request->nama_posko;
            $posko->alamat_posko = $request->alamat_posko;
            $posko->latitude = $request->latitude;
            $posko->longitude = $request->longitude;
            $posko->kapasitas = $request->kapasitas;
            $posko->kontak_person = $request->kontak_person;
            // create_who dan create_date akan diisi otomatis oleh boot method
            $posko->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('lokasi-posko.index')->with('success', 'Lokasi Posko berhasil ditambahkan.');

        } catch (\Exception $e) {
            \Log::error('Error adding lokasi posko: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat menambahkan Lokasi Posko: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     * Menampilkan form untuk mengedit lokasi posko.
     *
     * @param  \App\Models\LokasiPosko  $lokasiPosko
     * @return \Illuminate\View\View
     */
    public function edit(LokasiPosko $lokasiPosko)
    {
        $bencanaList = Bencana::all(); // Ambil semua data bencana untuk dropdown
        return view('admin.lokasi_posko.edit', compact('lokasiPosko', 'bencanaList'));
    }

    /**
     * Update the specified resource in storage.
     * Mengupdate lokasi posko yang ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LokasiPosko  $lokasiPosko
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, LokasiPosko $lokasiPosko)
    {
        // Validasi input
        $request->validate([
            'bencana_id' => 'required|exists:m_bencana,bencana_id',
            'nama_posko' => 'required|string|max:255',
            'alamat_posko' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'kapasitas' => 'nullable|integer|min:0',
            'kontak_person' => 'nullable|string|max:255',
        ], [
            'bencana_id.required' => 'Data Bencana wajib dipilih.',
            'bencana_id.exists' => 'Data Bencana tidak valid.',
            'nama_posko.required' => 'Nama Posko wajib diisi.',
            'alamat_posko.required' => 'Alamat Posko wajib diisi.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus antara -90 dan 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus antara -180 dan 180.',
            'kapasitas.integer' => 'Kapasitas harus berupa angka.',
            'kapasitas.min' => 'Kapasitas tidak boleh negatif.',
        ]);

        try {
            $lokasiPosko->bencana_id = $request->bencana_id;
            $lokasiPosko->nama_posko = $request->nama_posko;
            $lokasiPosko->alamat_posko = $request->alamat_posko;
            $lokasiPosko->latitude = $request->latitude;
            $lokasiPosko->longitude = $request->longitude;
            $lokasiPosko->kapasitas = $request->kapasitas;
            $lokasiPosko->kontak_person = $request->kontak_person;
            // change_who dan change_date akan diisi otomatis oleh boot method
            $lokasiPosko->save();

            // Redirect ke halaman index dengan flash message
            return redirect()->route('lokasi-posko.index')->with('success', 'Lokasi Posko berhasil diperbarui.');

        } catch (\Exception $e) {
            \Log::error('Error updating lokasi posko: ' . $e->getMessage());
            // Jika ada error, kembali ke form dengan input sebelumnya dan error
            return redirect()->back()->withInput()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui Lokasi Posko: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Menghapus lokasi posko dari database.
     *
     * @param  \App\Models\LokasiPosko  $lokasiPosko
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(LokasiPosko $lokasiPosko)
    {
        try {
            $lokasiPosko->delete();

            return response()->json(['success' => true, 'message' => 'Lokasi Posko berhasil dihapus.']);
        } catch (\Exception $e) {
            \Log::error('Error deleting lokasi posko: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus Lokasi Posko: ' . $e->getMessage()], 500);
        }
    }
}
